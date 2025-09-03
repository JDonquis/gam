<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Courses;
use App\Models\Sanction;
use App\Models\Incidence;
use App\Models\Resignation;
use Illuminate\Support\Str;
use App\Enums\DoctorStatusEnum;
use App\Enums\TypeActivityEnum;
use App\Events\ActivityCreated;
use App\Jobs\CheckStatusDoctor;
use App\Models\DoctorIncidence;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ResignationService
{
    public function getResignations()
    {
        $resignations = Resignation::query()
            ->with('doctor')
            ->when(request('search'), function ($query) {
                $searchTerm = request('search');

                $query->where(function ($q) use ($searchTerm) {
                    $q->where('reason', 'like', '%' . $searchTerm . '%');
                })
                    ->orWhereHas('doctor', function ($q) use ($searchTerm) {
                        $q->where('ci', $searchTerm);
                    });
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return $resignations;
    }

    public function store($data)
    {

        try {

            return DB::transaction(function () use ($data) {


                $resignation = Resignation::create([
                    'doctor_id' => $data['doctor_id'],
                ]);

                $sanctionService = new SanctionService;

                $sanctionService->validateIfExistsSanction($resignation->doctor_id);

                $current = Carbon::now();
                $twoYearsAfter = Carbon::now()->addYears(2);

                Sanction::create([
                    'doctor_id' => $resignation->doctor_id,
                    'resignation_id' => $resignation->id,
                    'start_date' => $current,
                    'end_date' => $twoYearsAfter,
                    'reason' => 'Renuncia generada. ID: ' . $resignation->id,
                ]);


                Doctor::where('id', $resignation->doctor_id)->update(['status' => DoctorStatusEnum::SANCTIONED]);

                /** @var User $accountableUser */
                $accountableUser = Auth::user();
                $url = route('resignations.edit', ['resignation' => $resignation->id]);
                event(new ActivityCreated($accountableUser, TypeActivityEnum::GENERATE_RESIGNATION->value, $url));

                return 0;
            });
        } catch (Exception $e) {

            Log::info('Error - ResignationService - Creando renuncia: ' . $e->getMessage(), [$data]);

            throw new Exception('Hubo un problema creando la renuncia');
        }
    }


    public function update($data, $resignation)
    {

        try {

            return DB::transaction(function () use ($data, $resignation) {

                $letterFileName = $resignation->resignation_letter;
                $documentFileName = $resignation->document;


                if (request()->hasFile('resignation_letter')) {

                    if ($resignation->resignation_letter)
                        Storage::disk('public')->delete($resignation->resignation_letter);

                    $extension = request()->file('resignation_letter')->getClientOriginalExtension();

                    $fileName = 'carta_renuncia_' . $resignation->id . '_' . time() . '.' . $extension;

                    $letterFileName = request()->file('resignation_letter')->storeAs(
                        'doctors',
                        $fileName,
                        'public'
                    );
                }

                if (request()->hasFile('document')) {

                    if ($resignation->document)
                        Storage::disk('public')->delete($resignation->document);

                    $extension = request()->file('document')->getClientOriginalExtension();

                    $fileName = 'oficio_' . $resignation->id . '_' . time() . '.' . $extension;

                    $documentFileName = request()->file('document')->storeAs(
                        'doctors',
                        $fileName,
                        'public'
                    );
                }

                $resignation->update([
                    'reason' => $data['reason'],
                    'resignation_letter' => $letterFileName,
                    'document' => $documentFileName,
                ]);

                /** @var User $accountableUser */
                $accountableUser = Auth::user();

                $url = route('resignations.edit', ['resignation' => $resignation->id]);
                event(new ActivityCreated($accountableUser, TypeActivityEnum::UPDATE_RESIGNATION->value, $url));

                return 0;
            });
        } catch (Exception $e) {

            Log::info('Error - ResignationService - Actualizando renuncia: ' . $e->getMessage(), [$data, $resignation]);

            throw new Exception('Hubo un problema actualizando la renuncia');
        }
    }

    public function destroy($resignation)
    {
        try {

            return DB::transaction(function () use ($resignation) {

                if ($resignation->resignation_letter)
                    Storage::disk('public')->delete($resignation->resignation_letter);

                if ($resignation->document)
                    Storage::disk('public')->delete($resignation->document);

                Sanction::where('resignation_id', $resignation->id)->delete();

                $resignation->delete();

                CheckStatusDoctor::dispatch();


                /** @var User $accountableUser */
                $accountableUser = Auth::user();

                $url = '#';
                event(new ActivityCreated($accountableUser, TypeActivityEnum::DELETE_RESIGNATION->value, $url));

                return 0;
            });
        } catch (Exception $e) {

            Log::info('Error - ResignationService - Eliminando renuncia: ' . $e->getMessage(), [$resignation]);

            throw $e;
        }
    }
}
