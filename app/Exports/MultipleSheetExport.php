<?php

namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultipleSheetExport implements WithMultipleSheets
{
    use Exportable;

    public function __construct(public Project $p)
    {
    }


    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new ProjectsExport($this->p);
        $sheets[] = new DataCalculation(p: $this->p, startRow: 4);
        $sheets[] = new PCUCalculation(p: $this->p, startRow: 4);
        $sheets[] = new FinalCalculationExport(
            approach: $this->p->approach_name ?? "",
            intersection: $this->p->intersection ?? "",
            startRow: 4,
            startData: $this->p->projectData->first(),
            endData: $this->p->projectData->last(),
            totalRows: count($this->p->projectData)
        );

        return $sheets;
    }
}
