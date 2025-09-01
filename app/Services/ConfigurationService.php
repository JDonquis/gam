<?php

namespace App\Services;

use Exception;
use App\Models\Configuration;
use App\Enums\TypeActivityEnum;
use App\Events\ActivityCreated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ConfigurationService
{


    public function getConfigurations()
    {

        $configurations = Configuration::query()
            ->when(request('search'), function ($query) {
                $query->where('name', 'like', '%' . request('search') . '%');
            })
            ->when(request('start_date'), function ($query) {
                $query->whereDate('created_at', '>=', request('start_date'));
            })
            ->when(request('end_date'), function ($query) {
                $query->whereDate('created_at', '<=', request('end_date'));
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return $configurations;
    }



    public function store($data)
    {
        try {
            return DB::transaction(function () use ($data) {


                $structure = [];
                foreach ($data['fields'] as $field) {
                    $structure[] = [
                        'name' => $field['name'],
                        'excel_cell' => $field['excel_cell'],
                        'required' => $field['required'],
                        'unique' => $field['unique'],
                        'filterable' => $field['filterable'],
                        'searchable' => $field['searchable'],
                        'start_date' => $field['start_date'],
                        'end_date' => $field['end_date'],
                        'ci' => $field['ci'],

                    ];
                }

                $configuration = Configuration::create([
                    'name' => $data['name'],
                    'structure' => $structure,
                ]);


                /** @var User $accountableUser */
                $accountableUser = Auth::user();


                $url = route('configuration.edit', ['config' => $configuration->id]);
                event(new ActivityCreated($accountableUser, TypeActivityEnum::INSERT_CONFIGURATION->value, $url));

                return $configuration;
            });
        } catch (Exception $e) {
            Log::error('Error - ConfigurationService - Registrando configuracion: ' . $e->getMessage(), [$data]);
            throw new Exception('Hubo un problema registrando el configuracion: ' . $e->getMessage());
        }
    }

    public function update($data, $config)
    {
        try {
            return DB::transaction(function () use ($data, $config) {


                $structure = [];
                foreach ($data['fields'] as $field) {
                    $structure[] = [
                        'name' => $field['name'],
                        'excel_cell' => $field['excel_cell'],
                        'required' => $field['required'],
                        'unique' => $field['unique'],
                        'filterable' => $field['filterable'],
                        'searchable' => $field['searchable'],
                        'start_date' => $field['start_date'],
                        'end_date' => $field['end_date'],
                        'ci' => $field['ci'],

                    ];
                }

                $config->update([
                    'name' => $data['name'],
                    'structure' => $structure,
                ]);


                /** @var User $accountableUser */
                $accountableUser = Auth::user();


                $url = route('configuration.edit', ['config' => $config->id]);
                event(new ActivityCreated($accountableUser, TypeActivityEnum::UPDATE_CONFIGURATION->value, $url));

                return $config;
            });
        } catch (Exception $e) {
            Log::error('Error - ConfigurationService - Actualizando configuracion: ' . $e->getMessage(), [$data]);
            throw new Exception('Hubo un problema actualizando la configuracion: ' . $e->getMessage());
        }
    }




    public function destroy($config)
    {
        try {

            return DB::transaction(function () use ($config) {


                $config->delete();

                /** @var User $accountableUser */
                $accountableUser = Auth::user();

                $url = '#';
                event(new ActivityCreated($accountableUser, TypeActivityEnum::DELETE_CONFIGURATION->value, $url));

                return 0;
            });
        } catch (Exception $e) {

            Log::info('Error - CensusService - Eliminando registro de censo: ' . $e->getMessage(), [$config]);

            throw $e;
        }
    }
}
