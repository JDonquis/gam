<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Sanction;
use App\Models\Incidence;
use App\Enums\DoctorStatusEnum;
use App\Enums\TypeActivityEnum;
use App\Events\ActivityCreated;
use App\Jobs\CheckStatusDoctor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class SanctionService
{
    public function getSanctions()
    {
        $sanctions = Sanction::query()
            ->with('doctor', 'incidence')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return $sanctions;
    }

    public function store($data)
    {

        try {

            return DB::transaction(function () use ($data) {


                $this->validateIfExistsSanction($data['doctor_id']);

                $incidence = Incidence::where('id', $data['incidence_id'])->first();

                if (!isset($incidence->id))
                    throw new Exception('Incidencia no encontrada');

                $current = Carbon::now();
                $twoYearsAfter = Carbon::now()->addYears(2);

                $sanction = Sanction::create([
                    'doctor_id' => $data['doctor_id'],
                    'incidence_id' => $data['incidence_id'],
                    'reason' => $incidence->reason,
                    'start_date' => $current,
                    'end_date' => $twoYearsAfter,
                ]);

                $incidence->update(['status_resolve' => true]);

                Doctor::where('id', $data['doctor_id'])->update(['status' => DoctorStatusEnum::SANCTIONED->value]);


                /** @var User $accountableUser */
                $accountableUser = Auth::user();
                $url = route('sanctions.edit', ['sanction' => $sanction->id]);
                event(new ActivityCreated($accountableUser, TypeActivityEnum::CREATE_DOCTOR->value, $url));

                return 0;
            });
        } catch (Exception $e) {

            Log::info('Error - SanctionService - Creando sancion: ' . $e->getMessage(), [$data]);

            throw $e;
        }
    }

    public function update($data, $sanction)
    {

        try {

            return DB::transaction(function () use ($data, $sanction) {


                $sanction->update([
                    'reason' => $data['reason']
                ]);


                /** @var User $accountableUser */
                $accountableUser = Auth::user();
                $url = route('sanctions.edit', ['sanction' => $sanction->id]);
                event(new ActivityCreated($accountableUser, TypeActivityEnum::UPDATE_SANCTION->value, $url));

                return 0;
            });
        } catch (Exception $e) {

            Log::info('Error - SanctionService - Actualizando sancion: ' . $e->getMessage(), [$data, $sanction]);

            throw $e;
        }
    }

    public function validateIfExistsSanction($doctorID)
    {
        $sanction = Sanction::where('doctor_id', $doctorID)->first();

        if (isset($sanction->id)) {
            throw new Exception("Este doctor ya tiene un sancion, ID: " . $sanction->id, 400);
        }
    }

    public function destroy($sanctionID)
    {
        try {

            return DB::transaction(function () use ($sanctionID) {

                $sanction = Sanction::where('id', $sanctionID)->first();
                Incidence::where('id', $sanction->incidence_id)->update(['status_resolve' => false]);
                $sanction->delete();
                CheckStatusDoctor::dispatch();


                /** @var User $accountableUser */
                $accountableUser = Auth::user();

                $url = '#';
                event(new ActivityCreated($accountableUser, TypeActivityEnum::DELETE_SANCTION->value, $url));

                return 0;
            });
        } catch (Exception $e) {

            Log::info('Error - SanctionService - Eliminando sancion: ' . $e->getMessage(), [$sanction]);

            throw $e;
        }
    }
}
