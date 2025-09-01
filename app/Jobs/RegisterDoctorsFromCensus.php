<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Configuration;
use Illuminate\Bus\Batchable;
use App\Enums\DoctorStatusEnum;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CensusCreatedNotification;
use App\Models\{Census, Doctor, CensusData, Incidence, Courses, DoctorIncidence, UniqueException};

class RegisterDoctorsFromCensus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    protected Census $census;
    protected Configuration $configuration;
    protected int $totalRows = 0;
    protected int $processedRows = 0;

    public function __construct(Census $census, Configuration $configuration)
    {
        $this->census = $census;
        $this->configuration = $configuration;
    }

    public function handle()
    {


        // Contar total de registros a procesar
        $this->totalRows = CensusData::where('census_id', $this->census->id)->count();


        if ($this->totalRows === 0) {
            $this->completeCensus();
            return;
        }

        // Obtener la estructura de configuración
        $structure = $this->configuration->structure;

        // Encontrar el campo que representa la cédula (ci: true)
        $ciField = $this->findCiField($structure);

        // Obtener campos únicos para validación
        $uniqueFields = $this->getUniqueFields($structure);

        $requiredFields = $this->getRequiredFields($structure);


        // Obtener campos de fecha
        $dateFields = $this->getDateFields($structure);




        $uniqueExceptions = UniqueException::get();

        $censusData = CensusData::where('census_id', $this->census->id)->get();


        foreach ($censusData as $data) {

            $ciToInsert = $data->data[$ciField['name']];

            if (is_null($ciToInsert)) {
                $this->createIncidence(null, $data->id, 'La cedula de indentidad es nula');
                continue;
            }

            $doctor = $this->searchRepeatCI($ciToInsert);

            if (isset($doctor->id)) {
                $this->createIncidence($doctor->id, $data->id, 'La cedula se encuentra repetida');
                continue;
            }

            // Si la cedula esta bien, proseguimos validar los campos necesarios para el curso:

            $starDateWithoutFormat = $data->data[$dateFields['start_date']];
            $endDateWithoutFormat = $data->data[$dateFields['end_date']];

            if (is_null($starDateWithoutFormat)) {
                $this->createIncidence(null, $data->id, 'Fecha de inicio de curso no encontrada');
                continue;
            }

            if (is_null($endDateWithoutFormat)) {
                $this->createIncidence(null, $data->id, 'Fecha de culminacion de curso no encontrada');
                continue;
            }

            $startDateFormat = Carbon::createFromFormat('d/m/Y', $starDateWithoutFormat);
            $endDateFormat = Carbon::createFromFormat('d/m/Y', $endDateWithoutFormat);


            if (!$startDateFormat) {
                $this->createIncidence(null, $data->id, 'Formato de fecha de inicio de curso no valida');
                continue;
            }

            if (!$endDateFormat) {
                $this->createIncidence(null, $data->id, 'Formato de fecha de culminacion de curso no valida');
                continue;
            }

            // Si todo esta bien esta listo para crearse el doctor, pero vamos a validar los campos unicos que haya configurado el usuario:

            foreach ($uniqueFields as $cell => $name) {
                $existingDoctor = Doctor::whereJsonContains('data->' . $name, $data->data[$name])->first();

                if (isset($existingDoctor->id)) {

                    // Antes de crear una incidencia veremos si contiene una de las Excepciones:
                    $exists = $uniqueExceptions->contains('name', $existingDoctor->data[$name]);
                    if (!$exists) {
                        $this->createIncidence($existingDoctor->id, $data->id, 'El campo: ' . $name . ' se encuentra repetido');
                        continue 2;
                    }
                }
            }

            // Ahora valido los campos requeridos tambien:

            foreach ($requiredFields as $cell => $name) {
                $value =  $data->data[$name];

                if (is_null($value)) {
                    $this->createIncidence(null, $data->id, 'El campo requerido: ' . $name . ' no se ha encontrado o no tiene valor');
                    continue 2;
                }
            }

            // Ahora el ultimo paso antes de insertar, es detectar el estado del doctor:

            $now = Carbon::now();
            $newStatus = $now->gt($endDateFormat)
                ? DoctorStatusEnum::AVAILABLE->value
                : DoctorStatusEnum::IN_COURSE->value;

            // Finalmente creo el doctor con su curso:

            $doctor = Doctor::create([
                'data' => $data->data,
                'ci' => str_replace(['.', ','], '', $ciToInsert),
                'status' => $newStatus,
                'is_foreign' => false,
            ]);

            Courses::create([
                'doctor_id' => $doctor->id,
                'start_date' => $startDateFormat,
                'end_date' => $endDateFormat,
            ]);

            $this->processedRows += 1;

            if (($this->processedRows % 10) === 0) {
                $this->updateProgress();
            }
        }

        $this->completeCensus();
    }

    protected function searchRepeatCI($ci)
    {
        $cleanCi = str_replace(['.', ','], '', $ci);

        return Doctor::where('ci', $cleanCi)->first();
    }

    protected function findCiField(array $structure): ?array
    {
        foreach ($structure as $field) {
            if ($field['ci'] === true) {
                return $field;
            }
        }
        return null;
    }

    protected function getUniqueFields(array $structure): array
    {
        $uniqueFields = [];
        foreach ($structure as $field) {
            if ($field['unique'] === true) {
                $uniqueFields[$field['excel_cell']] = $field['name'];
            }
        }
        return $uniqueFields;
    }

    protected function getRequiredFields(array $structure): array
    {
        $requiredFields = [];
        foreach ($structure as $field) {
            if ($field['required'] === true) {
                $requiredFields[$field['excel_cell']] = $field['name'];
            }
        }
        return $requiredFields;
    }

    protected function getDateFields(array $structure): array
    {
        $dateFields = [];
        foreach ($structure as $field) {
            if ($field['start_date'] === true) {
                $dateFields['start_date'] = $field['name'];
            }
            if ($field['end_date'] === true) {
                $dateFields['end_date'] = $field['name'];
            }
        }
        return $dateFields;
    }


    protected function createIncidence($doctorId, $censusDataId, $reason)
    {
        CensusData::where('id', $censusDataId)->update(['observation' => $reason]);

        $incidence = Incidence::create([
            'reason' => $reason,
            'doctor_id' => $doctorId,
            'census_data_id' => $censusDataId,
            'status_resolve' => false,
        ]);

        // Si hay un doctor asociado, actualizar su estado a WITH_INCIDENCE
        if (!is_null($doctorId)) {

            DoctorIncidence::create([
                'doctor_id' => $doctorId,
                'incidence_id' => $incidence->id,
            ]);

            Doctor::where('id', $doctorId)->update([
                'status' => DoctorStatusEnum::WITH_INCIDENCE->value
            ]);
        }
    }


    protected function updateProgress(): void
    {
        if ($this->totalRows > 0) {
            $percentage = min(100, round(($this->processedRows / $this->totalRows) * 100));

            $this->census->update([
                'percentage' => $percentage,
                'is_completed' => $percentage === 100
            ]);
        }
    }

    protected function completeCensus(): void
    {
        $this->census->update([
            'percentage' => 100,
            'is_completed' => true
        ]);

        $message = "Census {$this->census->id} procesado completamente. Total registros: {$this->processedRows}/{$this->totalRows}";

        Log::info($message);

        $users = User::all();

        Notification::send($users, new CensusCreatedNotification($this->census, $message));
    }
}
