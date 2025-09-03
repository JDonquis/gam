<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateIncidenceRequest;
use Exception;
use App\Models\Census;
use App\Models\Incidence;
use Illuminate\Http\Request;
use App\Services\IncidenceService;

class IncidenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $incidenceService;

    public function __construct()
    {
        $this->incidenceService = new IncidenceService;
    }

    public function index(Request $request)
    {
        $incidences = $this->incidenceService->getIncidences();
        $censusIds = Incidence::with('censusData')
            ->where('status_resolve', false)
            ->get()
            ->pluck('censusData.census_id')
            ->unique()
            ->filter()
            ->values();

        // Obtenemos solo esos censos
        $censuses = Census::whereIn('id', $censusIds)->get();

        return view('dashboard.incidences')->with(compact('incidences', 'censuses'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Incidence $incidence)
    {
        $incidence->load(['censusData.census.configuration', 'doctor']);
        return view('dashboard.crud_incidences.edit_incidence')->with(compact('incidence'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIncidenceRequest $request, Incidence $incidence)
    {
        try {

            $this->incidenceService->update($request->validated(), $incidence);

            return redirect()->route('incidences.index')->with('success', 'Incidencia actualizada, reintentando insercion... ');
        } catch (Exception $e) {

            return back()
                ->withInput()
                ->withErrors([
                    'status' => $e->getMessage()
                ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Incidence $incidence)
    {
        try {

            $this->incidenceService->destroy($incidence);

            return redirect()->route('incidences.index')->with('success', 'Incidencia eliminada con exito');
        } catch (Exception $e) {

            return back()
                ->with('error', $e->getMessage())
                ->withErrors([
                    'status' => $e->getMessage()
                ]);
        }
    }

    public function destroyAll(Request $request)
    {
        try {

            $this->incidenceService->destroyAll($request->census_id);

            return redirect()->route('incidences.index')->with('success', 'Incidencias eliminadas con exito');
        } catch (Exception $e) {

            return back()
                ->with('error', $e->getMessage())
                ->withErrors([
                    'status' => $e->getMessage()
                ]);
        }
    }
}
