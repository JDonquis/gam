<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Census;
use App\Models\Doctor;
use App\Models\Courses;
use App\Models\Incidence;
use App\Models\CensusData;
use Illuminate\Bus\Batchable;
use App\Enums\DoctorStatusEnum;
use App\Models\DoctorIncidence;
use App\Models\UniqueException;
use App\Notifications\ReInsertCensusDataNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Notification;

class RetryRegisterDoctorFromCensusData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;


    /**
     * Create a new job instance.
     */
    protected CensusData $censusData;

    public function __construct(CensusData $censusData)
    {
        $this->censusData = $censusData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Primero debemos conseguir el configuration.
        $censusID = $this->censusData->census_id;
        $document = Census::where('id', $censusID)->with('configuration')->first();
        $configuration = $document->configuration;

        // Obtener la estructura de configuración
        $structure = $configuration->structure;

        // Encontrar el campo que representa la cédula (ci: true)
        $ciField = $this->findCiField($structure);

        // Obtener campos únicos para validación
        $uniqueFields = $this->getUniqueFields($structure);

        $requiredFields = $this->getRequiredFields($structure);


        // Obtener campos de fecha
        $dateFields = $this->getDateFields($structure);

        $uniqueExceptions = UniqueException::get();

        Log::info('que tiene census data aca: ', [$this->censusData->data]);

        $ciToInsert = $this->censusData->data[$ciField['name']];

        if (is_null($ciToInsert)) {
            $incidence = $this->createIncidence(null, $this->censusData->id, 'La cedula de indentidad es nula');
            $this->failedCensus($incidence);
            return;
        }

        $doctor = $this->searchRepeatCI($ciToInsert);

        if (isset($doctor->id)) {
            $incidence = $this->createIncidence($doctor->id, $this->censusData->id, 'La cedula se encuentra repetida');
            $this->failedCensus($incidence);
            return;
        }

        // Si la cedula esta bien, proseguimos validar los campos necesarios para el curso:

        $starDateWithoutFormat = $this->censusData->data[$dateFields['start_date']];
        $endDateWithoutFormat = $this->censusData->data[$dateFields['end_date']];

        if (is_null($starDateWithoutFormat)) {
            $incidence = $this->createIncidence(null, $this->censusData->id, 'Fecha de inicio de curso no encontrada');
            $this->failedCensus($incidence);
            return;
        }

        if (is_null($endDateWithoutFormat)) {
            $incidence = $this->createIncidence(null, $this->censusData->id, 'Fecha de culminacion de curso no encontrada');
            $this->failedCensus($incidence);
            return;
        }

        $startDateFormat = Carbon::createFromFormat('d/m/Y', $starDateWithoutFormat);
        $endDateFormat = Carbon::createFromFormat('d/m/Y', $endDateWithoutFormat);


        if (!$startDateFormat) {
            $incidence = $this->createIncidence(null, $this->censusData->id, 'Formato de fecha de inicio de curso no valida');
            $this->failedCensus($incidence);
            return;
        }

        if (!$endDateFormat) {
            $incidence = $this->createIncidence(null, $this->censusData->id, 'Formato de fecha de culminacion de curso no valida');
            $this->failedCensus($incidence);
            return;
        }

        // Si todo esta bien esta listo para crearse el doctor, pero vamos a validar los campos unicos que haya configurado el usuario:

        foreach ($uniqueFields as $cell => $name) {
            $existingDoctor = Doctor::whereJsonContains('data->' . $name, $this->censusData->data[$name])->first();

            if (isset($existingDoctor->id)) {

                // Antes de crear una incidencia veremos si contiene una de las Excepciones:
                $exists = $uniqueExceptions->contains('name', $existingDoctor->data[$name]);
                if (!$exists) {
                    $incidence = $this->createIncidence($existingDoctor->id, $this->censusData->id, 'El campo: ' . $name . ' se encuentra repetido');
                    $this->failedCensus($incidence);
                    return;
                }
            }
        }

        // Ahora valido los campos requeridos tambien:

        foreach ($requiredFields as $cell => $name) {
            $value =  $this->censusData->data[$name];

            if (is_null($value)) {
                $incidence = $this->createIncidence(null, $this->censusData->id, 'El campo requerido: ' . $name . ' no se ha encontrado o no tiene valor');
                $this->failedCensus($incidence);
                return;
            }
        }

        // Ahora el ultimo paso antes de insertar, es detectar el estado del doctor:

        $now = Carbon::now();
        $newStatus = $now->gt($endDateFormat)
            ? DoctorStatusEnum::AVAILABLE->value
            : DoctorStatusEnum::IN_COURSE->value;

        // Finalmente creo el doctor con su curso:

        $doctor = Doctor::create([
            'data' => $this->censusData->data,
            'ci' => str_replace(['.', ','], '', $ciToInsert),
            'status' => $newStatus,
            'is_foreign' => false,
        ]);

        Courses::create([
            'doctor_id' => $doctor->id,
            'start_date' => $startDateFormat,
            'end_date' => $endDateFormat,
        ]);

        $this->completeCensus($doctor);
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
        Log::info('INCIDENCIA: ', [$censusDataId, $reason]);

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

        return $incidence;
    }

    protected function completeCensus($doctor): void
    {

        $message = "El censo {$this->censusData->id} procesado completamente. el doctor se ha registrado exitosamente";

        Log::info($message);

        $users = User::all();

        Notification::send($users, new ReInsertCensusDataNotification($doctor, $message));
    }

    protected function failedCensus($incidence)
    {
        $message = "El censo {$this->censusData->id} no se podido procesar. incidencia generada";

        Log::info($message);

        $users = User::all();

        Notification::send($users, new ReInsertCensusDataNotification($incidence, $message, false));
    }
}
