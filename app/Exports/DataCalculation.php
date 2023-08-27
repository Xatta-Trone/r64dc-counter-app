<?php

namespace App\Exports;

use App\Models\Project;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

class DataCalculation implements
    FromCollection,
    WithHeadings,
    WithEvents,
    ShouldAutoSize,
    WithTitle,
    WithCustomStartCell
{
    public Project $project;
    public int $startRow = 4;
    public function __construct(public Project $p)
    {
        $this->project = Project::with('projectData')->find($p->id);
    }

    use Exportable, RegistersEventListeners;

    public function title(): string
    {
        return 'Data-calculation';
    }


    public function collection()
    {
        $data = [];

        if (count($this->project->projectData) == 0) {
            return new Collection($data);
        }



        foreach ($this->project->projectData as $row => $singleItem) {
            $time = $singleItem['start_time'] . '-' . $singleItem['end_time'];
            $singleRow = [
                $time,
            ];

            $totalItems = count($singleItem['data']);
            $offset =  $this->startRow + 1;

            $firstLetter = 'A';

            // $firstLetter = $firstLetter + $totalItems;


            foreach (['left', 'through', 'right'] as $sideIndex => $side) {
                // dd($side);

                $startLetter = 2 + ($sideIndex * ($totalItems + 4));
                $endLetter = $startLetter + $totalItems - 1;


                foreach ($singleItem['data'] as $item) {
                    $singleRow = [
                        ...$singleRow,
                        (string)$item[$side]
                    ];
                }

                $singleRow = [
                    ...$singleRow,
                    '=SUM(' . $this->getCharacterAt($startLetter) . ($row + $offset) . ':' . $this->getCharacterAt($endLetter) . ($row + $offset) . ')',
                    $row % 3 == 0 ?  '=SUM(' . $this->getCharacterAt($endLetter + 1) . ($row + $offset) . ':' . $this->getCharacterAt($endLetter + 1) . ($row + $offset + 2) . ')' : "",
                    $row % 12 == 0 ?  '=SUM(' . $this->getCharacterAt($endLetter + 2) . ($row + $offset) . ':' . $this->getCharacterAt($endLetter + 2) . ($row + $offset + 11) . ')' : "",
                    $row % 12 == 0 ?  '=' . $this->getCharacterAt($endLetter + 3) . ($row + $offset) . '/(4*Max(' . $this->getCharacterAt($endLetter + 2) . ($row + $offset) . ':' . $this->getCharacterAt($endLetter + 2) . ($row + $offset + 11) . '))' : "",
                ];
            }


            $data[] = $singleRow;

            // dd($singleRow);
            // dd($singleItem, $time);
        }


        // dd($data);

        return new Collection($data);
    }

    private function getCharacterAt(int $index): string
    {
        $letters = range('A', 'Z');

        if ($index > 26) {
            $reminder = $index % 26;
            $quotient = (int)floor($index / 26);

            return $letters[$quotient - 1] . $letters[$reminder - 1];
        } else {
            return $letters[$index - 1];
        }
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
        $header = ['Time'];

        if ($this->project->projectData) {
            foreach (['left', 'through', 'right'] as $side) {
                foreach ($this->project->projectData[0]['data'] as $item) {
                    $header[] = $item['title'];
                }

                $header[] = "Total (5min)";
                $header[] = "Total (15min)";
                $header[] = "Total (1hour)";
                $header[] = "PCF";
            }
        }


        return $header;
    }

    public function startCell(): string
    {
        return 'A' . $this->startRow;
    }


    public function registerEvents(): array
    {
        // $cellsToMerge = [];
        // $startRow = 7;

        // if ($this->project->projectData) {
        //     for ($i = 0; $i < count($this->project->projectData); $i++) {
        //         $cellsToMerge[] = 'A' . $startRow . ':' . 'A' . $startRow + 2;
        //         $startRow = $startRow + 3;
        //     }
        // }

        // return [
        //     AfterSheet::class => function (AfterSheet $event) use ($cellsToMerge) {

        //         /** @var Sheet $sheet */
        //         $sheet = $event->sheet;

        //         // $sheet->mergeCells('A1:B1');
        //         $sheet->setCellValue('A1', "Project Name");
        //         $sheet->setCellValue('A2', "Date");
        //         $sheet->setCellValue('A3', "Day");
        //         $sheet->setCellValue('A4', "From");

        //         $sheet->mergeCells('B1:F1');
        //         $sheet->setCellValue('B1', $this->project->title);
        //         $sheet->setCellValue('B3', $this->project->day);
        //         $sheet->setCellValue('B4', $this->project->projectData[0]['start_time']);

        //         $sheet->setCellValue('C2', "Intersection");
        //         $sheet->setCellValue('C3', "Approach Name");
        //         $sheet->setCellValue('C4', "To");

        //         $sheet->setCellValue('D2', $this->project->intersection);
        //         $sheet->setCellValue('D3', $this->project->approach_name);
        //         $sheet->setCellValue('D4', $this->project->projectData[count($this->project->projectData) - 1]['end_time']);

        //         $sheet->setCellValue('E2', "Surveyor Name");
        //         $sheet->setCellValue('E3', "Surveyor Id");
        //         $sheet->setCellValue('E4', "Weather Condition");

        //         $sheet->setCellValue('F2', $this->project->user->name);
        //         $sheet->setCellValue('F4', $this->project->weather_condition);



        //         foreach ($cellsToMerge as $cellRange) {
        //             $event->sheet->getDelegate()->mergeCells($cellRange);
        //         }
        //     },
        // ];
        return [];
    }
}
