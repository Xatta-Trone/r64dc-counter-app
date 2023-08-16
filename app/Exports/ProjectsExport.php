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
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

class ProjectsExport implements FromCollection, WithHeadings, WithEvents, WithCustomStartCell, ShouldAutoSize
{
    public Project $project;
    public function __construct(public Project $p)
    {
        $this->project = $p;
    }

    use Exportable, RegistersEventListeners;


    public function collection()
    {
        $data = [];

        if (count($this->project->projectData) == 0) {
            return new Collection($data);
        }

        foreach ($this->project->projectData as $singleItem) {
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
    public function startCell(): string
    {
        return 'A6';
    }

    public function headings(): array
    {
        $header = ['Time', 'Movement'];

        if ($this->project->projectData()) {
            foreach ($this->project->projectData[0]['data'] as $item) {
                $header[] = $item['title'];
            }
        }

        return $header;
    }

    public function registerEvents(): array
    {
        $cellsToMerge = [];
        $startRow = 7;

        if ($this->project->projectData) {
            for ($i = 0; $i < count($this->project->projectData); $i++) {
                $cellsToMerge[] = 'A' . $startRow . ':' . 'A' . $startRow + 2;
                $startRow = $startRow + 3;
            }
        }

        return [
            AfterSheet::class => function (AfterSheet $event) use ($cellsToMerge) {

                /** @var Sheet $sheet */
                $sheet = $event->sheet;

                // $sheet->mergeCells('A1:B1');
                $sheet->setCellValue('A1', "Project Name");
                $sheet->setCellValue('A2', "Date");
                $sheet->setCellValue('A3', "Day");
                $sheet->setCellValue('A4', "From");

                $sheet->mergeCells('B1:F1');
                $sheet->setCellValue('B1', $this->project->title);
                $sheet->setCellValue('B3', $this->project->day);
                $sheet->setCellValue('B4', $this->project->projectData[0]['start_time']);

                $sheet->setCellValue('C2', "Intersection");
                $sheet->setCellValue('C3', "Approach Name");
                $sheet->setCellValue('C4', "To");

                $sheet->setCellValue('D2', $this->project->intersection);
                $sheet->setCellValue('D3', $this->project->approach_name);
                $sheet->setCellValue('D4', $this->project->projectData[count($this->project->projectData) - 1]['end_time']);

                $sheet->setCellValue('E2', "Surveyor Name");
                $sheet->setCellValue('E3', "Surveyor Id");
                $sheet->setCellValue('E4', "Weather Condition");

                $sheet->setCellValue('F2', $this->project->user->name);
                $sheet->setCellValue('F4', $this->project->weather_condition);



                foreach ($cellsToMerge as $cellRange) {
                    $event->sheet->getDelegate()->mergeCells($cellRange);
                }
            },
        ];
    }
}
