<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Doctor;
use Illuminate\Http\Request;
use App\Models\Configuration;
use Illuminate\Support\Facades\Log;
use App\Services\ConfigurationService;
use App\Http\Requests\StoreConfiguration;
use App\Http\Requests\UpdateConfigurationRequest;

class ConfigurationController extends Controller
{
    protected $configurationService;

    public function __construct()
    {
        $this->configurationService = new ConfigurationService;
    }

    public function index(Request $request)
    {
        $configurations = $this->configurationService->getConfigurations();
        return view('dashboard.configurations')->with(compact('configurations'));
    }

    public function create()
    {
        $doctor = new Doctor;
        $doctorFieldsRequired = $doctor->fieldsRequired;
        return view('dashboard.crud_configurations.create_configuration')->with(compact('doctorFieldsRequired'));
    }

    public function store(StoreConfiguration $request)
    {

        try {

            Log::info('creando configuration', $request->validated());

            $this->configurationService->store($request->validated());

            return redirect()->route('configuration.index')->with('success', 'Configuracion creada exitosamente');
        } catch (Exception $e) {

            return back()
                ->withInput()
                ->withErrors([
                    'status' => $e->getMessage()
                ]);
        }
    }

    public function edit(Configuration $config)
    {
        $doctor = new Doctor;
        $doctorFieldsRequired = $doctor->fieldsRequired;
        return view('dashboard.crud_configurations.edit_configuration')->with(compact('config', 'doctorFieldsRequired'));
    }

    public function update(UpdateConfigurationRequest $request, Configuration $config)
    {
        try {
            $this->configurationService->update($request->validated(), $config);

            return redirect()->route('configuration.index')->with('success', 'Configuracion actualizada exitosamente');
        } catch (Exception $e) {

            return back()
                ->withInput()
                ->withErrors([
                    'status' => $e->getMessage()
                ]);
        }
    }

    public function destroy(Configuration $config)
    {
        try {

            $this->configurationService->destroy($config);

            return redirect()->route('configuration.index')->with('success', 'Configuracion eliminada con exito');
        } catch (Exception $e) {

            return back()
                ->with('error', $e->getMessage())
                ->withErrors([
                    'status' => $e->getMessage()
                ]);
        }
    }
}
