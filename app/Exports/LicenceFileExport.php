<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LicenceFileExport implements FromQuery, WithHeadings, WithStyles
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

    public function prepareRows($licenceFiles)
    {
        return $licenceFiles->transform(function ($licenceFile) {
            $licenceFile->licence = $licenceFile->licence->name ?? '';
            $licenceFile->uploader = $licenceFile->uploader->name ?? '';
            return $licenceFile;
        });
    }
}
