<?php

namespace App\Exports;

use App\Models\Project;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

class ProjectsExport implements FromCollection, WithHeadings, WithEvents
{
    public Project $project;
    public function __construct(public Project $p)
    {
        $this->project = $p;
    }

    use Exportable, RegistersEventListeners;

    public function view(): View
    {
        return view('table', [
            'project' => $this->project
        ]);
    }

    public function collection()
    {
        $data = [];

        if ($this->project->data == null) {
            return new Collection($data);
        }

        foreach ($this->project->data as $singleItem) {
            $d = [];
            $time = $singleItem['start_time'] . '-' . $singleItem['end_time'];

            // dd($singleItem);

            $outerRow = [];

            foreach (['left', 'through', 'right'] as $key) {
                $tempData = [
                    $time,
                    $this->getRoute($key),
                ];

                foreach ($singleItem['data'] as $item) {
                    $tempData = [
                        ...$tempData,
                        (string)$item[$key]
                    ];
                }

                // dd($tempData);
                $outerRow[] = $tempData;
            }

            // dd($outerRow);




            $data = [...$data, ...$outerRow];
        }


        return new Collection($data);
    }

    public function getRoute($key)
    {
        switch ($key) {
            case 'left':
                return 'L';
            case 'right':
                return 'R';

            default:
                return 'T';
        }
    }

    public function headings(): array
    {
        $header = ['Time', 'Movement'];

        if ($this->project->data) {
            foreach ($this->project->data[0]['data'] as $item) {
                $header[] = $item['title'];
            }
        }

        return $header;
    }

    public function registerEvents(): array
    {
        $cellsToMerge = [];
        $startRow = 2;

        if ($this->project->data) {
            for ($i = 0; $i < count($this->project->data); $i++) {
                $cellsToMerge[] = 'A' . $startRow . ':' . 'A' . $startRow + 2;
                $startRow = $startRow + 3;
            }
        }


        return [
            AfterSheet::class => function (AfterSheet $event) use ($cellsToMerge) {
                foreach ($cellsToMerge as $cellRange) {
                    $event->sheet->getDelegate()->mergeCells($cellRange);
                }
            },
        ];
    }
}
