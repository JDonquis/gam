<?php

namespace App\Services;

use Exception;
use App\Models\User;
use App\Models\Incidence;
use Illuminate\Support\Str;
use App\Enums\TypeActivityEnum;
use App\Events\ActivityCreated;
use App\Jobs\CheckStatusDoctor;
use App\Jobs\RetryRegisterDoctorFromCensusData;
use App\Models\CensusData;
use App\Models\DoctorIncidence;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class IncidenceService
{
    public function getIncidences()
    {
        $incidences = Incidence::query()
            ->where('status_resolve', false)
            ->with('doctor', 'censusData.census')
            ->when(request('reason'), function ($query) {
                $query->where('reason', 'like', '%' . request('reason') . '%');
            })
            ->when(request('start_date'), function ($query) {
                $query->whereDate('created_at', '>=', request('start_date'));
            })
            ->when(request('end_date'), function ($query) {
                $query->whereDate('created_at', '<=', request('end_date'));
            })
            ->when(request('census_id'), function ($query) {
                $query->whereHas('censusData', function ($q) {
                    $q->where('census_id', request('census_id'));
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return $incidences;
    }

    public function store($data)
    {

        try {

            return DB::transaction(function () use ($data) {

                if (request()->hasFile('photo')) {

                    $extension = request()->file('photo')->getClientOriginalExtension();

                    $fileName = Str::slug($data['ci']) . '_' . time() . '.' . $extension;

                    $data['photo'] = request()->file('photo')->storeAs(
                        'users',
                        $fileName,
                        'public'
                    );
                }

                $data['password'] = Hash::make($data['ci']);
                $user = User::create($data);

                /** @var User $accountableUser */
                $accountableUser = Auth::user();


                $url = route('users.show', ['user' => $user->id]);
                event(new ActivityCreated($accountableUser, TypeActivityEnum::CREATE_USER->value, $url));

                return 0;
            });
        } catch (Exception $e) {

            Log::info('Error - UserService - Creando usuario: ' . $e->getMessage(), [$data]);

            throw new Exception('Hubo un problema creando el usuario');
        }
    }

    public function update($data, $incidence)
    {

        try {

            return DB::transaction(function () use ($data, $incidence) {

                $isforeign = isset($data['data']['is_foreign']);

                $censusData = CensusData::where('id', $incidence->census_data_id)->first();
                $censusData->update(['data' => $data['data'], 'is_foreign' => $isforeign]);


                $incidence->load('doctor');

                $hasToCheckStatus = (bool) $incidence->doctor;

                if ($hasToCheckStatus) {

                    DoctorIncidence::where('incidence_id', $incidence->id)->delete();
                    $incidence->delete();
                    CheckStatusDoctor::dispatch();
                } else {
                    $incidence->delete();
                }

                RetryRegisterDoctorFromCensusData::dispatch($censusData);


                /** @var User $accountableUser */
                $accountableUser = Auth::user();

                $url = "#";
                event(new ActivityCreated($accountableUser, TypeActivityEnum::UPDATE_INCIDENCE->value, $url));

                return 0;
            });
        } catch (Exception $e) {

            Log::info('Error - IncidenceService - Actualizando incidencia: ' . $e->getMessage(), [$data, $incidence]);

            throw new Exception('Hubo un problema actualizando el incidencia');
        }
    }

    public function destroy($incidence)
    {
        try {

            return DB::transaction(function () use ($incidence) {


                $incidence->load('doctor');

                $hasToCheckStatus = (bool) $incidence->doctor;

                if ($hasToCheckStatus) {

                    DoctorIncidence::where('incidence_id', $incidence->id)->delete();
                    $incidence->delete();
                    CheckStatusDoctor::dispatch();
                } else {
                    $incidence->delete();
                }


                /** @var User $accountableUser */
                $accountableUser = Auth::user();

                $url = '#';
                event(new ActivityCreated($accountableUser, TypeActivityEnum::DELETE_INCIDENCE->value, $url));


                return 0;
            });
        } catch (Exception $e) {

            Log::info('Error - IncidenceService - Eliminando incidencia: ' . $e->getMessage(), [$incidence]);

            throw $e;
        }
    }
    public function destroyAll($census)
    {
        try {

            return DB::transaction(function () use ($census) {

                if (is_null($census)) {
                    $incidences = Incidence::with('doctor')->get();

                    foreach ($incidences as $incidence) {

                        $hasToCheckStatus = (bool) $incidence->doctor;

                        if ($hasToCheckStatus) {

                            DoctorIncidence::where('incidence_id', $incidence->id)->delete();
                            $incidence->delete();
                            CheckStatusDoctor::dispatch();
                        } else {
                            $incidence->delete();
                        }
                    }
                } else {
                    $censuDatas = CensusData::where('census_id', $census)->get();

                    foreach ($censuDatas as $censusData) {

                        $incidence = Incidence::where('census_data_id', $censusData->id)->with('doctor')->first();

                        if (is_null($incidence))
                            continue;

                        $hasToCheckStatus = (bool) $incidence->doctor;

                        if ($hasToCheckStatus) {

                            DoctorIncidence::where('incidence_id', $incidence->id)->delete();
                            $incidence->delete();
                            CheckStatusDoctor::dispatch();
                        } else {
                            $incidence->delete();
                        }
                    }
                }




                /** @var User $accountableUser */
                $accountableUser = Auth::user();

                $url = '#';
                event(new ActivityCreated($accountableUser, TypeActivityEnum::DELETE_MULTIPLE_INCIDENCES->value, $url));


                return 0;
            });
        } catch (Exception $e) {

            Log::info('Error - IncidenceService - Eliminando incidencia: ' . $e->getMessage(), [$census]);

            throw $e;
        }
    }
}
