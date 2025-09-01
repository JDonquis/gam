<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Courses;
use App\Models\Sanction;
use App\Models\Incidence;
use Illuminate\Support\Str;
use App\Enums\DoctorStatusEnum;
use App\Enums\TypeActivityEnum;
use App\Events\ActivityCreated;
use App\Jobs\CheckStatusDoctor;
use App\Models\DoctorIncidence;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DoctorService
{
    public function getDoctors()
    {
        $doctors = Doctor::query()
            ->when(request('search'), function ($query) {
                $searchTerm = request('search');

                $query->where(function ($q) use ($searchTerm) {
                    $q->where('ci', 'like', '%' . $searchTerm . '%');
                    $q->orWhereRaw('JSON_SEARCH(data, "all", ?) IS NOT NULL', [$searchTerm])
                        ->orWhereRaw('JSON_SEARCH(data, "all", ?) IS NOT NULL', ['%' . $searchTerm . '%']);
                });
            })
            ->when(request('status_id'), function ($query) {
                $query->where('status', request('status_id'));
            })
            ->when(!is_null(request('is_foreign')), function ($query) {
                $query->where('is_foreign', request('is_foreign'));
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return $doctors;
    }

    public function store($data)
    {

        try {

            return DB::transaction(function () use ($data) {


                $startDateFormat = Carbon::createFromFormat('d/m/Y', $data['start_date']);
                $endDateFormat = Carbon::createFromFormat('d/m/Y', $data['end_date']);

                $now = Carbon::now();
                $newStatus = $now->gt($endDateFormat)
                    ? DoctorStatusEnum::AVAILABLE->value
                    : DoctorStatusEnum::IN_COURSE->value;

                $doctor = Doctor::create([
                    'data' => $data['data'],
                    'is_foreign' => $data['is_foreign'],
                    'ci' => str_replace(['.', ','], '', $data['ci']),
                    'status' => $newStatus,
                ]);


                Courses::create([
                    'doctor_id' => $doctor->id,
                    'start_date' => $startDateFormat,
                    'end_date' => $endDateFormat,
                ]);

                /** @var User $accountableUser */
                $accountableUser = Auth::user();
                $url = route('doctors.show', ['doctor' => $doctor->id]);
                event(new ActivityCreated($accountableUser, TypeActivityEnum::CREATE_DOCTOR->value, $url));

                return 0;
            });
        } catch (Exception $e) {

            Log::info('Error - DoctorService - Creando doctor: ' . $e->getMessage(), [$data]);

            throw new Exception('Hubo un problema creando el doctor');
        }
    }


    public function update($data, $doctor)
    {

        try {

            return DB::transaction(function () use ($data, $doctor) {

                $doctor->update([
                    'data' => $data['data'],
                    'is_foreign' => $data['is_foreign'],
                    'ci' => str_replace(['.', ','], '', $data['ci']),
                ]);

                $startDateFormat = Carbon::parse($data['start_date']);
                $endDateFormat = Carbon::parse($data['end_date']);


                Courses::where('doctor_id', $doctor->id)->update([
                    'start_date' => $startDateFormat,
                    'end_date' => $endDateFormat,
                ]);

                CheckStatusDoctor::dispatch();


                /** @var User $accountableUser */
                $accountableUser = Auth::user();

                $url = route('doctors.show', ['doctor' => $doctor->id]);
                event(new ActivityCreated($accountableUser, TypeActivityEnum::UPDATE_DOCTOR->value, $url));

                return 0;
            });
        } catch (Exception $e) {

            Log::info('Error - DoctorService - Actualizando doctor: ' . $e->getMessage(), [$data]);

            throw new Exception('Hubo un problema actualizando el doctor');
        }
    }

    public function destroy($doctor)
    {
        try {

            return DB::transaction(function () use ($doctor) {

                Courses::where('doctor_id', $doctor->id)->delete();
                Sanction::where('doctor_id', $doctor->id)->delete();
                DoctorIncidence::where('doctor_id', $doctor->id)->delete();
                Incidence::where('doctor_id', $doctor->id)->delete();
                $doctor->delete();


                /** @var User $accountableUser */
                $accountableUser = Auth::user();

                $url = '#';
                event(new ActivityCreated($accountableUser, TypeActivityEnum::DELETE_DOCTOR->value, $url));

                return 0;
            });
        } catch (Exception $e) {

            Log::info('Error - DoctorService - Eliminando medico: ' . $e->getMessage(), [$doctor]);

            throw $e;
        }
    }

    public function generateSanction($doctor) {}
}
