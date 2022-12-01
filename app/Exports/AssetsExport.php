<?php

namespace App\Exports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AssetsExport implements FromQuery, WithHeadings, WithStyles
{
    use Exportable;

    private $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        return array_keys($this->query()->first()->toArray());
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
        ];
    }

    public function prepareRows($rows)
    {
        return $rows->transform(function ($asset) {
            $asset->notes = preg_replace("/\s+/", " ", $asset->notes);
            $asset->asset_model = $asset->asset_model->name ?? '';
            $asset->current_holder = $asset->current_holder->name ?? '';
            return $asset;
        });
    }
}
