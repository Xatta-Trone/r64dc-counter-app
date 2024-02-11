<?php

namespace App\Exports;

use Carbon\CarbonPeriod;
use App\Models\ProjectTimeData;
use App\Traits\ExcelHelperTrait;
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

class FinalCalculationExport implements
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
    use ExcelHelperTrait;


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



        $leftTotalColumn = $this->totalItems + 4;
        $throughTotalColumn = $leftTotalColumn + $this->totalItems + 3;
        $rightTotalColumn = $throughTotalColumn + $this->totalItems + 5;

        // time row
        $timeRow = ['TimeSlot'];
        $leftRow = ['Left'];
        $throughRow = ['Through'];
        $rightRow = ['Right'];
        $totalRow = ['Total'];
        $phfRow = ['PHF'];

        foreach ($timeSlotData as $index => $timeSlot) {
            $timeRow[] = $timeSlot;
            $leftRow[] = '=split!' . $this->getCharacterAt($leftTotalColumn) . $this->startRow + 1 + (12 * $index);
            $throughRow[] = '=split!' . $this->getCharacterAt($throughTotalColumn) . $this->startRow + 1 + (12 * $index);
            $rightRow[] = '=split!' . $this->getCharacterAt($rightTotalColumn) . $this->startRow + 1 + (12 * $index);
            $totalRow[] = '=sum(' . $this->getCharacterAt($index + 2) . '27:' . $this->getCharacterAt($index + 2) . '29)';
            $phfRow[] = '=split!' . $this->getCharacterAt($leftTotalColumn + 1) . $this->startRow + 1 + (12 * $index);
        }

        $data[] = $timeRow;
        $data[] = $leftRow;
        $data[] = $throughRow;
        $data[] = $rightRow;
        $data[] = $totalRow;
        $data[] = $phfRow;

        $data[] = [''];
        $data[] = ["Capacity Information"];
        $data[] = ['', 'Number of Lanes', '15'];
        $data[] = ['', 'Capacity Per Lane', '1700'];
        $data[] = ["LOS Table"];
        $data[] = ['Type', 'LOS A', 'LOS B', 'LOS C', 'LOS D', 'LOS E', 'LOS F'];
        $data[] = ['Co-efficient', '0.35', '0.55', '0.77', '0.92', '1', '2'];
        $data[] = ["Hourly Volume-PCU (Approach: " . $this->approach . " To Intersection: " . $this->intersection . ")", 'LOS A', 'LOS B', 'LOS C', 'LOS D', 'LOS E', 'LOS F'];
        $data[] = [''];


        // rows count
        $totalFixedRows = 26;
        $rowNumOfLOSTable = $totalFixedRows + count($timeSlotData) + 7;
        $firstRowNumber = $rowNumOfLOSTable + 3;


        // rows
        $timeRow2 = ['TimeSlot'];
        $losA = ['LOSA'];
        $losB = ['LOSB'];
        $losC = ['LOSC'];
        $losD = ['LOSD'];
        $losE = ['LOSE'];
        $losF = ['LOSF'];
        $leftCol2 = ['Left'];
        $throughCol2 = ['Through'];
        $rightCol2 = ['Right'];
        $totalCol2 = ['Total'];
        $capacityCol = ['Capacity'];
        $wcCol = ['v/c'];
        $losCol = ['LOS'];

        foreach ($timeSlotData as $index => $timeSlot) {
            // current row number
            $timeRow2[] = $timeSlot;
            $losA[] = '=$B$38';
            $losB[] = '=$C$38';
            $losC[] = '=$D$38';
            $losD[] = '=$E$38';
            $losE[] = '=$F$38';
            $losF[] = '=$G$38';
            $leftCol2[] = '=pcu!' . $this->getCharacterAt($leftTotalColumn) . $this->startRow + 1 + (12 * $index);
            $throughCol2[] = '=pcu!' . $this->getCharacterAt($throughTotalColumn) . $this->startRow + 1 + (12 * $index);
            $rightCol2[] = '=pcu!' . $this->getCharacterAt($rightTotalColumn) . $this->startRow + 1 + (12 * $index);
            $totalCol2[] = '=sum(' . $this->getCharacterAt($index + 2) . '48:' . $this->getCharacterAt($index + 2) . '50)';
            $capacityCol[] = '=$C$34*$C$35';
            $wcCol[] =  '=' . $this->getCharacterAt($index + 2) . '51/' . $this->getCharacterAt($index + 2) . '52';
            $losCol[] = '=HLOOKUP(' . $this->getCharacterAt($index + 2) . '53,$B$38:$G$39,2,TRUE)';
        }

        $data[] = $timeRow2;
        $data[] = $losA;
        $data[] = $losB;
        $data[] = $losC;
        $data[] = $losD;
        $data[] = $losE;
        $data[] = $losF;
        $data[] = $leftCol2;
        $data[] = $throughCol2;
        $data[] = $rightCol2;
        $data[] = $totalCol2;
        $data[] = $capacityCol;
        $data[] = $wcCol;
        $data[] = $losCol;


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
                $event->sheet->getDelegate()->getStyle('A33')->getFont()->setBold(true)->setSize(16);
                $event->sheet->getDelegate()->getStyle('A36')->getFont()->setBold(true)->setSize(16);
                $event->sheet->getDelegate()->getStyle('A39')->getFont()->setBold(true)->setSize(16);

                $sheet->getStyle('B38:G38')->getFill()->applyFromArray(['fillType' => 'solid', 'rotation' => 0, 'color' => ['rgb' => 'f1c40f'],]);
                $sheet->getStyle('C34:C35')->getFill()->applyFromArray(['fillType' => 'solid', 'rotation' => 0, 'color' => ['rgb' => 'f1c40f'],]);


                return [];
            },
        ];
    }

}
