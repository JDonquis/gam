<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\University;
use Illuminate\Http\Request;
use App\Enums\DoctorStatusEnum;
use App\Services\DoctorService;
use App\Http\Requests\StoreDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;
use App\Models\Configuration;
use App\Models\Doctor;
use App\Models\DoctorIncidence;
use App\Models\Sanction;

class DoctorController extends Controller
{

    protected $doctorService;

    public function __construct()
    {
        $this->doctorService = new DoctorService;
    }

    public function index(Request $request)
    {

        $doctors = $this->doctorService->getDoctors();
        $statuses = DoctorStatusEnum::allWithLabels();

        return view('dashboard.doctors')->with(compact('doctors', 'statuses'));
    }

    public function create()
    {
        $configurations = Configuration::get();
        $statuses = DoctorStatusEnum::allWithLabels();

        return view('dashboard.crud_doctors.create_doctor')->with(compact('configurations', 'statuses'));
    }

    public function store(StoreDoctorRequest $request)
    {

        try {

            $this->doctorService->store($request->validated());

            return redirect()->route('doctors.index')->with('success', 'Médico creado exitosamente');
        } catch (Exception $e) {

            return back()
                ->withInput()
                ->withErrors([
                    'status' => $e->getMessage()
                ]);
        }
    }

    public function update(UpdateDoctorRequest $request, Doctor $doctor)
    {
        try {


            $this->doctorService->update($request->validated(), $doctor);

            return redirect()->route('doctors.index')->with('success', 'Médico actualizado exitosamente');
        } catch (Exception $e) {

            return back()
                ->withInput()
                ->withErrors([
                    'status' => $e->getMessage()
                ]);
        }
    }

    public function show(Doctor $doctor)
    {
        $doctor->load('course');
        $incidences = DoctorIncidence::where('doctor_id', $doctor->id)->count();
        $sanctions = Sanction::where('doctor_id', $doctor->id)->count();

        return view('dashboard.crud_doctors.show_doctor')->with(compact('doctor', 'incidences', 'sanctions'));
    }

    public function edit(Doctor $doctor)
    {
        $statuses = DoctorStatusEnum::allWithLabels();
        $doctor->load('course');

        return view('dashboard.crud_doctors.edit_doctor')->with(compact('doctor', 'statuses'));
    }

    public function destroy(Doctor $doctor)
    {
        try {

            $this->doctorService->destroy($doctor);

            return redirect()->route('doctors.index')->with('success', 'Medico eliminado con exito');
        } catch (Exception $e) {

            return back()
                ->with('error', $e->getMessage())
                ->withErrors([
                    'status' => $e->getMessage()
                ]);
        }
    }
}
