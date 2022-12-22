<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LicenceExport implements FromQuery, WithHeadings, WithStyles
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
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function prepareRows($licences)
    {
        return $licences->transform(function ($licence) {
            $licence->category = $licence->category->name ?? '';
            $licence->manufacturer = $licence->manufacturer->name ?? '';
            return $licence;
        });
    }
}
