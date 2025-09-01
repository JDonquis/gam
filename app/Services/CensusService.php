<?php

namespace App\Services;

use Exception;
use App\Models\CensusData;
use Illuminate\Support\Str;
use App\Enums\TypeActivityEnum;
use App\Enums\TypeDocumentEnum;
use App\Events\ActivityCreated;
use App\Jobs\CheckResignations;
use App\Jobs\CheckStatusDoctor;
use App\Jobs\RegisterDoctorsFromCensus;
use App\Models\Census;
use App\Models\Configuration;
use App\Models\DoctorIncidence;
use App\Models\Incidence;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CensusService
{
    public function getCensus()
    {
        $census = Census::query()
            ->with('user')
            ->when(request('search'), function ($query) {
                $query->where(function ($q) {
                    $q->whereAny(['title'], 'like', '%' . request('search') . '%');
                });
            })
            ->when(request('title'), function ($query) {
                $query->where('title', 'like', '%' . request('title') . '%');
            })
            ->when(request('type'), function ($query) {
                $query->where('type_document_id', request('type'));
            })
            ->when(request('start_date'), function ($query) {
                $query->whereDate('created_at', '>=', request('start_date'));
            })
            ->when(request('end_date'), function ($query) {
                $query->whereDate('created_at', '<=', request('end_date'));
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return $census;
    }

    public function getPreview()
    {
        try {
            $spreadsheet = IOFactory::load(request()->file('file')->getRealPath());

            $sheetName = request()->input('sheet_name');

            if (!$spreadsheet->sheetNameExists($sheetName)) {
                throw new \InvalidArgumentException(
                    "El archivo no contiene la hoja requerida: {$sheetName}"
                );
            }

            $registers = $spreadsheet
                ->getSheetByName($sheetName)
                ->rangeToArray('A1:AH2000', null, true, true, true);


            return [$registers];
        } catch (Exception $e) {
            Log::error('Error al leer excel: ' . $e->getMessage());
            throw $e;
        }
    }



    public function store($data)
    {
        try {
            return DB::transaction(function () use ($data) {

                $accountableUser = Auth::user();

                $lastID = Census::orderBy('id', 'desc')->value('id') ?? 0;

                $lastID += 1;

                $uploadedFile = request()->file('file');
                $extension = $uploadedFile->getClientOriginalExtension();
                $fileSizeBytes = $uploadedFile->getSize();
                $fileSizeMB = round($fileSizeBytes / (1024 * 1024), 2);
                $file = null;

                if ($data['type_document_id'] == TypeDocumentEnum::CENSO_RESIDENTES->value)
                    $file = 'CENSO_RESIDENTE_#' . $lastID;
                else
                    $file = 'RENUNCIAS_#' . $lastID;


                $census = Census::create([
                    'file' => $file,
                    'title' => $file,
                    'type' => $extension,
                    'size' => $fileSizeMB,
                    'user_id' => $accountableUser->id,
                    'configuration_id' => $data['configuration_id'],
                    'type_document_id' => $data['type_document_id'],
                ]);

                $configuration = Configuration::where('id', $data['configuration_id'])->first();
                $this->handleRegisters($data, $census);

                if ($census->type_document_id == TypeDocumentEnum::CENSO_RESIDENTES->value) {
                    RegisterDoctorsFromCensus::dispatch(
                        $census,
                        $configuration
                    );
                } else {
                    CheckResignations::dispatch(
                        $census,
                        $configuration
                    );
                }


                /** @var User $accountableUser */
                $accountableUser = Auth::user();


                $url = route('census.show', ['census' => $census->id]);
                event(new ActivityCreated($accountableUser, TypeActivityEnum::INSERT_CENSUS->value, $url));

                return $census;
            });
        } catch (Exception $e) {
            Log::error('Error - CensusService - Registrando censo: ' . $e->getMessage(), [$data]);
            throw new Exception('Hubo un problema registrando el censo: ' . $e->getMessage());
        }
    }

    public function showCensus($census, $request = null)
    {
        $census->load(['configuration', 'user']);

        $query = CensusData::where('census_id', $census->id);

        // Filtro por observaciones
        if ($request && $request->has('has_observation')) {
            if ($request->has_observation == 'with') {
                $query->whereNotNull('observation');
            } elseif ($request->has_observation == 'without') {
                $query->whereNull('observation');
            }
        }

        // Búsqueda en el JSON data
        if ($request && $request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('data', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('observation', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        $data = $query->paginate(5);

        return [$census, $data];
    }

    private function handleRegisters($data, $census)
    {
        $registers = $data['registros'];
        $configuration = Configuration::where('id', $data['configuration_id'])->first();

        if (!isset($configuration->id)) {
            throw new Exception("La configuración seleccionada no fue encontrada, intente nuevamente", 404);
        }

        // Filtrar los registros nulos
        $filteredRegisters = array_filter($registers, function ($register) {
            foreach ($register as $value) {
                if ($value !== null) {
                    return true;
                }
            }
            return false;
        });

        $structure = $configuration->structure;

        $cellToNameMap = [];

        // Solo mapear las celdas a nombres, sin verificar required
        foreach ($structure as $field) {
            $cellToNameMap[$field['excel_cell']] = $field['name'];
        }

        $createdRecords = [];

        foreach ($filteredRegisters as $register) {
            $mappedData = [];

            // Solo mapear los datos sin validaciones
            foreach ($register as $cell => $value) {

                if (isset($cellToNameMap[$cell])) {
                    $fieldName = $cellToNameMap[$cell];
                    $mappedData[$fieldName] = $value;
                }
            }

            $censusData = CensusData::create([
                'census_id' => $census->id,
                'data' => $mappedData,
                'observation' => null, // Sin observaciones de validación
                'is_foreign' => false,
            ]);

            $createdRecords[] = $censusData;
        }

        return $createdRecords;
    }




    public function destroy($census)
    {
        try {

            return DB::transaction(function () use ($census) {


                $censusData = CensusData::where('census_id', $census->id)->get();

                $censusDataIDs = $censusData->pluck('id')->toArray();


                $incidences = Incidence::whereIn('census_data_id', $censusDataIDs)->get();

                $incidencesIds = $incidences->pluck('id')->toArray();



                DoctorIncidence::whereIn('incidence_id', $incidencesIds)->delete();
                Incidence::whereIn('census_data_id', $censusDataIDs)->delete();
                CensusData::where('census_id', $census->id)->delete();
                $census->delete();

                CheckStatusDoctor::dispatch();


                /** @var User $accountableUser */
                $accountableUser = Auth::user();

                $url = '#';
                event(new ActivityCreated($accountableUser, TypeActivityEnum::DELETE_CENSUS->value, $url));

                return 0;
            });
        } catch (Exception $e) {

            Log::info('Error - CensusService - Eliminando registro de censo: ' . $e->getMessage(), [$census]);

            throw $e;
        }
    }
}
