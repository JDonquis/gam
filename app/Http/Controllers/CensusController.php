<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Census;
use App\Models\CensusData;
use Illuminate\Http\Request;
use App\Exports\CensusExport;
use App\Models\Configuration;
use App\Models\DoctorIncidence;
use App\Services\CensusService;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\StoreCensusRequest;

class CensusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $censusService;

    public function __construct()
    {
        $this->censusService = new CensusService;
    }

    public function index(Request $request)
    {
        $censuses = $this->censusService->getCensus();
        $totalCensus = Census::count();
        return view('dashboard.census')->with(compact('censuses', 'totalCensus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $configurations = Configuration::get();
        return view('dashboard.crud_census.create_census')->with(compact('configurations'));
    }

    public function preview(Request $request)
    {


        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
            'sheet_name' => 'required|string',
            'configuration_id' => 'required',
        ]);

        try {

            $registers = $this->censusService->getPreview();

            return response()->json([
                'registers' => $registers,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => 'El archivo no contiene las hojas esperadas',
                'error' => $e->getMessage()
            ], 422);
        } catch (Exception $e) {

            Log::error('Error inesperado al procesar Excel: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al procesar el archivo',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCensusRequest $request)
    {

        try {
            $this->censusService->store($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Registrado exitosamente',
                'redirect' => route('census.index')
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Census $census)
    {
        [$census, $data] = $this->censusService->showCensus($census, $request);

        return view('dashboard.crud_census.show_census')->with(compact('census', 'data'));
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Census $census)
    {
        try {

            $this->censusService->destroy($census);

            return redirect()->route('census.index')->with('success', 'Registro eliminado con exito');
        } catch (Exception $e) {

            return back()
                ->with('error', $e->getMessage())
                ->withErrors([
                    'status' => $e->getMessage()
                ]);
        }
    }

    public function download(Census $census)
    {
        $census->load('configuration');

        $fileName = $census->title . '_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new CensusExport($census), $fileName);
    }

    public function progress(Request $request)
    {
        $censusIds = $request->input('census_ids', []);
        $censuses = Census::whereIn('id', $censusIds)->get();

        return response()->json($censuses->map(function ($census) {
            return [
                'id' => $census->id,
                'percentage' => $census->percentage,
                'is_completed' => $census->is_completed
            ];
        }));
    }
}
