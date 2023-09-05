<?php

namespace App\Exports;

use Carbon\CarbonPeriod;
use App\Models\ProjectTimeData;
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

class FinalCalculationExport implements
    FromCollection,
    WithEvents,
    ShouldAutoSize,
    WithTitle,
    WithCustomStartCell,
    WithHeadings
{

    public int $totalItems = 0;


    public function __construct(public string $approach, public string $intersection, public int $startRow, public ProjectTimeData  $startData, public ProjectTimeData  $endData, public int $totalRows)
    {
        $this->totalItems = count($startData->data);
    }

    use Exportable, RegistersEventListeners;

    public function title(): string
    {
        return 'calculation';
    }



    public function startCell(): string
    {
        return 'A' . $this->startRow;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $data = [];

        // set PCU row.
        $pcuRow = ['PCU'];
        foreach ($this->startData->data as $item) {
            $pcuRow[] = 1;
        }
        $pcuRow[] = "";

        $data[] = $pcuRow;

        // get the left data
        $leftRow = ['Left'];
        $sumRow = $this->startRow + $this->totalRows + 1;

        for ($i = 2; $i < $this->totalItems + 2; $i++) {
            $leftRow[] = '=split!' . $this->getCharacterAt($i) . $sumRow;
        }

        $leftRow[] = '=split!' . $this->getCharacterAt($this->totalItems + 2) . $sumRow;
        $data[] = $leftRow;


        // get the through data
        $throughRow = ['Through'];

        for ($i = $this->totalItems + 6; $i < $this->totalItems * 2 + 6; $i++) {
            $throughRow[] = '=split!' . $this->getCharacterAt($i) . $sumRow;
        }

        $throughRow[] = '=split!' . $this->getCharacterAt($this->totalItems * 2 + 6) . $sumRow;
        $data[] = $throughRow;

        // get the right data
        $rightRow = ['Right'];

        for ($i = $this->totalItems * 2 + 10; $i < $this->totalItems * 3  + 10; $i++) {
            $rightRow[] = '=split!' . $this->getCharacterAt($i) . $sumRow;
        }

        $rightRow[] = '=split!' . $this->getCharacterAt($this->totalItems * 3 + 10) . $sumRow;
        $data[] = $rightRow;

        // total row
        $totalRow = ['Total'];

        for ($i = 2; $i < $this->totalItems + 2; $i++) {
            $column =  $this->getCharacterAt($i);
            $totalRow[] = '=SUM(' . $column . '6:' . $column . '8)';
        }

        $totalRow[] = '=SUM(' . $this->getCharacterAt($this->totalItems + 2) . '6:' . $this->getCharacterAt($this->totalItems + 2) . '8)';

        $data[] = $totalRow;

        // Left (%)
        $leftPercent = ['Left(%)'];
        $lastColumn = $this->totalItems + 2;

        for ($i = 2; $i < $this->totalItems + 2; $i++) {
            $column =  $this->getCharacterAt($i);
            $leftPercent[] = '=' . $column . '6/' . $this->getCharacterAt($lastColumn) . '6*100';
        }

        $leftPercent[] = '=' . $this->getCharacterAt($this->totalItems + 2) . '6/' . $this->getCharacterAt($lastColumn) . '6*100';

        $data[] = $leftPercent;

        // Through (%)
        $throughPercent = ['Through(%)'];
        $lastColumn = $this->totalItems + 2;

        for ($i = 2; $i < $this->totalItems + 2; $i++) {
            $column =  $this->getCharacterAt($i);
            $throughPercent[] = '=' . $column . '7/' . $this->getCharacterAt($lastColumn) . '7*100';
        }

        $throughPercent[] = '=' . $this->getCharacterAt($this->totalItems + 2) . '7/' . $this->getCharacterAt($lastColumn) . '7*100';

        $data[] = $throughPercent;

        // Right (%)
        $rightPercent = ['Right(%)'];
        $lastColumn = $this->totalItems + 2;

        for ($i = 2; $i < $this->totalItems + 2; $i++) {
            $column =  $this->getCharacterAt($i);
            $rightPercent[] = '=' . $column . '8/' . $this->getCharacterAt($lastColumn) . '8*100';
        }

        $rightPercent[] = '=' . $this->getCharacterAt($this->totalItems + 2) . '8/' . $this->getCharacterAt($lastColumn) . '8*100';

        $data[] = $rightPercent;

        // Total (%)
        $totalPercent = ['Total(%)'];
        $lastColumn = $this->totalItems + 2;

        for ($i = 2; $i < $this->totalItems + 2; $i++) {
            $column =  $this->getCharacterAt($i);
            $totalPercent[] = '=' . $column . '9/' . $this->getCharacterAt($lastColumn) . '9*100';
        }

        $totalPercent[] = '=' . $this->getCharacterAt($this->totalItems + 2) . '9/' . $this->getCharacterAt($lastColumn) . '9*100';

        $data[] = $totalPercent;

        $data[] = [''];


        // PCU calculation

        $leftPCU = ['Left (PCU)'];
        $lastColumn = $this->totalItems + 2;

        for ($i = 2; $i < $this->totalItems + 2; $i++) {
            $column =  $this->getCharacterAt($i);
            $leftPCU[] = '=' . $column . '6*' . $column . '5';
        }

        $leftPCU[] = '';
        $data[] = $leftPCU;

        // through PCU
        $throughPCU = ['Through (PCU)'];
        $lastColumn = $this->totalItems + 2;

        for ($i = 2; $i < $this->totalItems + 2; $i++) {
            $column =  $this->getCharacterAt($i);
            $throughPCU[] = '=' . $column . '7*' . $column . '5';
        }

        $throughPCU[] = '';
        $data[] = $throughPCU;

        // right PCU
        $rightPCU = ['Right (PCU)'];
        $lastColumn = $this->totalItems + 2;

        for ($i = 2; $i < $this->totalItems + 2; $i++) {
            $column =  $this->getCharacterAt($i);
            $rightPCU[] = '=' . $column . '8*' . $column . '5';
        }

        $rightPCU[] = '';
        $data[] = $rightPCU;

        // directional distribution

        $data[] = [''];
        $data[] = ['Directional distribution'];

        $data[] = ['Direction', '%'];

        $lastColumn =  $this->getCharacterAt($this->totalItems + 2);

        $data[] = ['Left', '=' . $lastColumn . '6/' . $lastColumn . '9*100'];
        $data[] = ['Through', '=' . $lastColumn . '7/' . $lastColumn . '9*100'];
        $data[] = ['Right', '=' . $lastColumn . '8/' . $lastColumn . '9*100'];


        // Hourly volume

        $data[] = [''];
        $data[] = ['Hourly volume'];

        $c = CarbonPeriod::since($this->startData->start_time)->hours(1)->until($this->endData->end_time)->toArray();


        $times = [];

        foreach ($c as $a) {
            $times[] = $a->format('H:i A');
        }

        if ($this->endData->end_time == "24:00") {
            $times[] = "24:00 AM";
        }

        $times = array_values(array_unique($times));

        $timeSlotData = [];

        for ($i = 0; $i < count($times); $i++) {
            if ($i > 0) {
                $timeSlotData[] = $times[$i - 1] . '-' . $times[$i];
            }
        }

        $data[] = [
            'TimeSlot',
            'Left',
            'Through',
            'Right',
            'Total',
            'PHF',
        ];

        $leftTotalColumn = $this->totalItems + 4;
        $throughTotalColumn = $leftTotalColumn + $this->totalItems + 3;
        $rightTotalColumn = $throughTotalColumn + $this->totalItems + 5;


        foreach ($timeSlotData as $index => $timeSlot) {
            $data[] = [
                $timeSlot,
                '=split!' . $this->getCharacterAt($leftTotalColumn) . $this->startRow + 1 + (12 * $index),
                '=split!' . $this->getCharacterAt($throughTotalColumn) . $this->startRow + 1 + (12 * $index),
                '=split!' . $this->getCharacterAt($rightTotalColumn) . $this->startRow + 1 + (12 * $index),
                '=sum(B' . (27 + $index) . ':D' . (27 + $index) . ')',
                '=split!' . $this->getCharacterAt($leftTotalColumn + 1) . $this->startRow + 1 + (12 * $index),
            ];
        }





        // dd($hourlyRow, $rightTotalColumn, $this->getCharacterAt($rightTotalColumn));






        // dd($this->startData->data, count($this->startData->data), $this->endData);
        return  new Collection($data);
    }

    public function headings(): array
    {
        $header = ['Direction'];

        foreach ($this->startData->data as $item) {
            $header[] = $item['title'];
        }

        $header[] = "Total";

        return $header;
    }


    public function registerEvents(): array
    {
        $cellsToMerge = [];

        return [
            AfterSheet::class => function (AfterSheet $event) use ($cellsToMerge) {

                /** @var Sheet $sheet */
                $sheet = $event->sheet;

                $sheet->mergeCells('A1:' . $this->getCharacterAt(count($this->startData->data) + 3) . "1");
                $sheet->setCellValue('A1', "Calculation :: Approach: " . $this->approach . " To Intersection: " . $this->intersection);

                $sheet->mergeCells('A3:' . $this->getCharacterAt(count($this->startData->data) + 3) . "3");
                $sheet->setCellValue('A3', "Vehicle composition (Approach: " . $this->approach . " To Intersection: " . $this->intersection . ")");
                // $sheet->setCellValue('A2', "Date");
                // $sheet->setCellValue('A3', "Day");
                // $sheet->setCellValue('A4', "From");


                return [];
            },
        ];
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
}