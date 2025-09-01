<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Census;
use App\Models\Doctor;
use App\Models\Incidence;
use App\Models\CensusData;
use App\Models\Configuration;
use Illuminate\Bus\Batchable;
use App\Enums\DoctorStatusEnum;
use App\Models\DoctorIncidence;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CensusCreatedNotification;
use App\Notifications\ResignationsCreatedNotification;

class CheckResignations implements ShouldQueue
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

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->totalRows = CensusData::where('census_id', $this->census->id)->count();

        if ($this->totalRows === 0) {
            $this->completeCensus();
            return;
        }

        // Obtener la estructura de configuración
        $structure = $this->configuration->structure;

        // Encontrar el campo que representa la cédula (ci: true)
        $ciField = $this->findCiField($structure);

        $censusData = CensusData::where('census_id', $this->census->id)->get();

        foreach ($censusData as $data) {
            $ciToInsert = $data->data[$ciField['name']];

            if (is_null($ciToInsert)) {
                continue;
            }

            $doctor = $this->searchRepeatCI($ciToInsert);

            if (isset($doctor->id)) {
                $this->createIncidence($doctor->id, $data->id, 'La cedula se encuentra repetida');
                continue;
            }

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

        $message = "Renuncia {$this->census->id} procesado completamente. Total registros: {$this->processedRows}/{$this->totalRows}";

        Log::info($message);

        $users = User::all();

        Notification::send($users, new ResignationsCreatedNotification($this->census, $message));
    }
}
