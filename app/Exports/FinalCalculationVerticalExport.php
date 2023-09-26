<?php

namespace App\Exports;

use Carbon\CarbonPeriod;
use App\Models\ProjectTimeData;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

class FinalCalculationVerticalExport implements
    FromCollection,
    WithEvents,
    ShouldAutoSize,
    WithTitle,
    WithCustomStartCell,
    WithHeadings
{

    public int $totalItems = 0;
    public array $timeSlotData = [];


    public function __construct(public string $approach, public string $intersection, public int $startRow, public ProjectTimeData  $startData, public ProjectTimeData  $endData, public int $totalRows)
    {
        $this->totalItems = count($startData->data);
    }

    use Exportable, RegistersEventListeners;

    public function title(): string
    {
        return 'calculation-vertical-table';
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
        foreach ($this->startData->data as $index => $item) {
            $pcuRow[] =  '=pcu!' . $this->getCharacterAt($index + 2) . '2';
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

        $this->timeSlotData =  $timeSlotData;

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

        $data[] = [''];
        $data[] = ["Capacity Information"];
        $data[] = ['', 'Number of Lanes', '=calculation!C34'];
        $data[] = ['', 'Capacity Per Lane', '=calculation!C35'];
        $data[] = ["LOS Table"];
        $data[] = ['Type', 'LOS A', 'LOS B', 'LOS C', 'LOS D', 'LOS E', 'LOS F'];
        $data[] = ['Co-efficient', '=calculation!B38', '=calculation!C38', '=calculation!D38', '=calculation!E38', '=calculation!F38', '=calculation!G38',];
        $data[] = ["Hourly Volume-PCU (Approach: " . $this->approach . " To Intersection: " . $this->intersection . ")", 'LOS A', 'LOS B', 'LOS C', 'LOS D', 'LOS E', 'LOS F'];
        $data[] = [''];
        $data[] = [
            'TimeSlot',
            'LOSA', 'LOSB', 'LOSC', 'LOSD', 'LOSE', 'LOSF',
            'Left',
            'Through',
            'Right',
            'Total',
            'Capacity',
            'v/c',
            'LOS'
        ];

        // rows count
        $totalFixedRows = 26;
        $rowNumOfLOSTable = $totalFixedRows + count($timeSlotData) + 7;
        $firstRowNumber = $rowNumOfLOSTable + 3;




        foreach ($timeSlotData as $index => $timeSlot) {
            // current row number
            $data[] = [
                $timeSlot,
                '=$B$' . $rowNumOfLOSTable,
                '=$C$' . $rowNumOfLOSTable,
                '=$D$' . $rowNumOfLOSTable,
                '=$E$' . $rowNumOfLOSTable,
                '=$F$' . $rowNumOfLOSTable,
                '=$G$' . $rowNumOfLOSTable,
                '=pcu!' . $this->getCharacterAt($leftTotalColumn) . $this->startRow + 1 + (12 * $index),
                '=pcu!' . $this->getCharacterAt($throughTotalColumn) . $this->startRow + 1 + (12 * $index),
                '=pcu!' . $this->getCharacterAt($rightTotalColumn) . $this->startRow + 1 + (12 * $index),
                '=sum(H' . ($firstRowNumber + $index + 1) . ':J' . ($firstRowNumber + $index + 1) . ')',
                '=$C$' . $rowNumOfLOSTable - 4 . '*$C$' . $rowNumOfLOSTable - 3,
                '=K' . ($firstRowNumber + $index + 1) . '/L' . ($firstRowNumber + $index + 1),
                '=HLOOKUP(M' . ($firstRowNumber + $index + 1) . ',$B$' . ($rowNumOfLOSTable) . ':$G$' . ($rowNumOfLOSTable + 1) . ',2,TRUE)'
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

                $event->sheet->getDelegate()->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $event->sheet->getDelegate()->getStyle('A3')->getFont()->setBold(true)->setSize(16);
                $event->sheet->getDelegate()->getStyle('A25')->getFont()->setBold(true)->setSize(16);
                $event->sheet->getDelegate()->getStyle('A' . (count($this->timeSlotData) + 3 + 25))->getFont()->setBold(true)->setSize(16);
                $event->sheet->getDelegate()->getStyle('A' . (count($this->timeSlotData) + 6 + 25))->getFont()->setBold(true)->setSize(16);
                $event->sheet->getDelegate()->getStyle('A' . (count($this->timeSlotData) + 9 + 25))->getFont()->setBold(true)->setSize(16);

                $sheet->getStyle('C' . (25 + count($this->timeSlotData) + 4) . ':C' . (25 + count($this->timeSlotData) + 5))->getFill()->applyFromArray(['fillType' => 'solid', 'rotation' => 0, 'color' => ['rgb' => 'f1c40f'],]);
                $sheet->getStyle('B' . (25 + count($this->timeSlotData) + 8) . ':G' . (25 + count($this->timeSlotData) + 8))->getFill()->applyFromArray(['fillType' => 'solid', 'rotation' => 0, 'color' => ['rgb' => 'f1c40f'],]);


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
