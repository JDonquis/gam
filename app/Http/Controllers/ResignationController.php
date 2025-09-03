<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Doctor;
use App\Models\Resignation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\ResignationService;
use App\Http\Requests\StoreResignationRequest;
use App\Http\Requests\UpdateResignationRequest;

class ResignationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $resignationService;

    public function __construct()
    {
        $this->resignationService = new ResignationService;
    }

    public function index()
    {
        $resignations = $this->resignationService->getResignations();
        return view('dashboard.resignations')->with(compact('resignations'));
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreResignationRequest $request)
    {
        try {

            $this->resignationService->store($request->validated());

            return redirect()->route('resignations.index')->with('success', 'Renuncia generada exitosamente');
        } catch (Exception $e) {

            return back()
                ->withErrors([
                    'status' => $e->getMessage()
                ]);
        }
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Resignation $resignation)
    {
        $resignation->load('doctor.course');

        return view('dashboard.crud_resignations.edit_resignation')->with(compact('resignation'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateResignationRequest $request, Resignation $resignation)
    {
        try {

            $this->resignationService->update($request->validated(), $resignation);

            return redirect()->route('resignations.index')->with('success', 'Renuncia actualizada exitosamente');
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
    public function destroy(Resignation $resignation)
    {
        try {

            $this->resignationService->destroy($resignation);

            return redirect()->route('resignations.index')->with('success', 'Renuncia eliminada con exito');
        } catch (Exception $e) {

            return back()
                ->with('error', $e->getMessage())
                ->withErrors([
                    'status' => $e->getMessage()
                ]);
        }
    }
}
