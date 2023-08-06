<?php

namespace App\Http\Livewire;

use App\Models\Project;
use Livewire\Component;
use Carbon\CarbonPeriod;

class ProjectComponent extends Component
{
    public $project;
    public $projectData = [];
    public $hours = [];

    public $startTime = null;
    public $itemToCount = null;

    public $newCardData = [
        'start_time' => null,
        'end_time' => null,
        'data' => [],
    ];

    public function addItem()
    {
        if ($this->itemToCount == null) {
            return;
        }
        $this->newCardData['data'][] = [
            'title' => $this->itemToCount,
            'left' => 0,
            'through' => 0,
            'right' => 0,
        ];

        $this->itemToCount = null;
    }

    public function save()
    {
        array_push($this->projectData, $this->newCardData);

        $this->project->update(['data' => $this->projectData]);

        $this->newCardData =
            [
                'start_time' => null,
                'end_time' => null,
                'data' => [],
            ];
    }

    public function increment(int $parentIndex, int $childIndex, string $key)
    {
        // dd($parentIndex,$childIndex,$key);
        $this->projectData[$parentIndex]['data'][$childIndex][$key]++;
    }


    public function decrement(int $parentIndex, int $childIndex, string $key)
    {
        // dd($parentIndex,$childIndex,$key);
        $this->projectData[$parentIndex]['data'][$childIndex][$key]--;
    }

    public function update()
    {
        $this->project->update(['data' => $this->projectData]);
        session()->flash('message', 'Record Updated.');
    }

    public function duplicate()
    {
        $duplicate = $this->projectData[0];
        $items = [];
        // $this->newCardData['data'][] = [
        //     'title' => $this->itemToCount,
        //     'left' => 0,
        //     'through' => 0,
        //     'right' => 0,
        // ];

        foreach ($duplicate['data'] as $item) {
            $items[] = [
                'title' => $item['title'],
                'left' => 0,
                'through' => 0,
                'right' => 0,
            ];
        }

        $duplicate['data'] = $items;

        array_push($this->projectData, $duplicate);

        // $this->project->update(['data' => $this->projectData]);
    }


    public function mount(Project $project)
    {
        $this->project = $project;
        $this->projectData = $project->data ?: [];

        $c = CarbonPeriod::since('00:00')->minutes(5)->until('23:59')->toArray();

        $d = [];
        foreach ($c as $a) {
            $d[] = $a->format('H:i');
        }

        $this->hours = $d;
    }
    public function render()
    {
        return view('livewire.project-component');
    }
}
