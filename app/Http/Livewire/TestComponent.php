<?php

namespace App\Http\Livewire;

use Livewire\Component;

class TestComponent extends Component
{
    public function render()
    {
        return view('livewire.test-component');
    }

    public function changeFocus()
    {
        $this->emit('change-focus-other-field');
    }

    public $postCount;

    public $components = [
        'item1' => [
            'name' => 'Item 1',
            'key' => 'asdf',
        ]
    ];

    public function add()
    {
        $key = "item" . time();
        $this->components[$key] = [
            'name' => $key,
            'key' => 'key' . $key
        ];

        $this->emit('focus', $key);
    }



    protected $listeners = ['postAdded' => 'incrementPostCount'];



    public function incrementPostCount($data)

    {

        $this->postCount = $data;
    }
}
