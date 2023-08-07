<?php

namespace App\Http\Livewire;

use App\Models\Project;
use Livewire\Component;
use Carbon\CarbonPeriod;

class ProjectItemsCounter extends Component
{

    public $project;
    public $currentTimeIndex = null;
    public $currentSide = 'left';
    public $projectData = [];

    public function mount(Project $project)
    {
        $this->project = $project;

        if ($project->data) {
            $this->projectData = $project->data;
            $this->currentTimeIndex = 0;
        }
    }

    protected $listeners = ['clickKey' => 'handleClickKey', 'saveData' => 'update'];


    public function handleClickKey($data)
    {
        if ($this->currentTimeIndex !== null) {
            foreach ($this->projectData[$this->currentTimeIndex]['data'] as $index => $item) {
                if ($item['key'] == $data) {
                    // dd($this->projectData[$this->currentTimeIndex]['data'][$index]);
                    $this->increment($this->currentTimeIndex, $index, $this->currentSide);
                }
            }
        }
    }



    public function increment(int $parentIndex, int $childIndex, string $key)
    {
        // dd($parentIndex, $childIndex, $key);
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

    public function test()
    {
        dd('test');
    }


    public function render()
    {
        return view('livewire.project-items-counter');
    }
}
