<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Doctor;
use App\Models\Sanction;
use Illuminate\Http\Request;
use App\Services\SanctionService;
use App\Http\Requests\StoreSanctionRequest;
use App\Http\Requests\UpdateSanctionRequest;

class SanctionController extends Controller
{
    protected $sanctionService;

    public function __construct()
    {
        $this->sanctionService = new SanctionService;
    }

    public function index()
    {
        $sanctions = $this->sanctionService->getSanctions();
        return view('dashboard.sanctions')->with(compact('sanctions'));
    }

    public function store(StoreSanctionRequest $request)
    {
        try {

            $this->sanctionService->store($request->validated());

            return redirect()->route('sanctions.index')->with('success', 'Sancion generada exitosamente');
        } catch (Exception $e) {

            return back()
                ->withInput()
                ->withErrors([
                    'status' => $e->getMessage()
                ]);
        }
    }

    public function edit(Sanction $sanction)
    {
        $sanction->load(['doctor.course']);
        return view('dashboard.crud_sanctions.edit_sanction')->with(compact('sanction'));
    }

    public function update(UpdateSanctionRequest $request, Sanction $sanction)
    {
        try {

            $this->sanctionService->update($request->validated(), $sanction);

            return redirect()->route('sanctions.index')->with('success', 'Sancion actualizada exitosamente');
        } catch (Exception $e) {

            return back()
                ->withInput()
                ->withErrors([
                    'status' => $e->getMessage()
                ]);
        }
    }

    public function destroy($sanction)
    {
        try {

            $this->sanctionService->destroy($sanction);

            return redirect()->route('sanctions.index')->with('success', 'Sanción eliminada con exito');
        } catch (Exception $e) {

            return back()
                ->with('error', $e->getMessage())
                ->withErrors([
                    'status' => $e->getMessage()
                ]);
        }
    }
    // hacer el eliminar, donde debo eliminar la sancion, poner la incidencia status_resolve en false, y reestrablecer el status del doctor
    // hacer el comando que se ejecute para actualizar los estados de los doctores cada dia
}
