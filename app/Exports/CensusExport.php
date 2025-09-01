<?php

namespace App\Exports;

use App\Models\Census;
use App\Models\CensusData;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CensusExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $census;
    protected $headers;

    public function __construct(Census $census)
    {
        $this->census = $census;
        $this->headers = $this->getHeaders();
    }

    public function collection()
    {
        return CensusData::where('census_id', $this->census->id)
            ->with('census.configuration')
            ->get();
    }

    protected function getHeaders()
    {
        $headers = [];

        foreach ($this->census->configuration->structure as $field) {
            $headers[] = $field['name'];
        }

        return $headers;
    }

    public function headings(): array
    {
        $headings = ['ID'];

        foreach ($this->census->configuration->structure as $field) {
            $headings[] = $field['name'];
        }

        return $headings;
    }

    public function map($censusData): array
    {
        // Datos básicos
        $row = [$censusData->id];

        // Agregar datos del JSON
        foreach ($this->census->configuration->structure as $field) {
            $fieldName = $field['name'];
            $row[] = $censusData->data[$fieldName] ?? 'N/A';
        }

        return $row;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo para los encabezados
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '3490DC']]
            ],

            // Autoajustar columnas
            'A:Z' => [
                'alignment' => ['vertical' => 'center']
            ]
        ];
    }
}
