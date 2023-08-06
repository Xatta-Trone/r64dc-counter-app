<?php

namespace App\Http\Livewire;

use App\Models\Project;
use Livewire\Component;

class CreateProject extends Component
{
    public $title;

    protected $rules = [
        'title' => 'required',
    ];



    public function submit()

    {

        $this->validate();


        Project::create([
            'title' => $this->title,

        ]);

        return redirect()->route('home');
    }
    public function render()
    {
        return view('livewire.create-project');
    }
}
