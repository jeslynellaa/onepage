<?php

namespace App\Livewire;

use Livewire\Component;

class ProcedureSteps extends Component
{
    public $steps = [];

    // Step form fields
    public $responsibility = '';
    public $activities = '';
    public $note = '';

    public $interfaces_input = [];
    public $interfaces_output = [];

    public function addInput()
    {
        $this->interfaces_input[] = ['category' => '', 'name' => ''];
    }

    public function removeInput($index)
    {
        unset($this->interfaces_input[$index]);
        $this->interfaces_input = array_values($this->interfaces_input);
    }

    public function addOutput()
    {
        $this->interfaces_output[] = ['category' => '', 'name' => ''];
    }

    public function removeOutput($index)
    {
        unset($this->interfaces_output[$index]);
        $this->interfaces_output = array_values($this->interfaces_output);
    }

    public function addStep()
    {
        $step = [
            'responsibility' => $this->responsibility,
            'activities' => $this->activities,
            'note' => $this->note,
            'interfaces_input' => array_filter($this->interfaces_input, fn($i) => $i['category'] || $i['name']),
            'interfaces_output' => array_filter($this->interfaces_output, fn($i) => $i['category'] || $i['name']),
        ];

        $this->steps[] = $step;

        // Reset form
        $this->reset(['responsibility', 'activities', 'note', 'interfaces_input', 'interfaces_output']);
    }

    public function render()
    {
        return view('livewire.procedure-steps');
    }
}
