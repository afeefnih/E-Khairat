<?php

namespace App\Livewire\Registration;

use Livewire\Component;
use App\Models\User;
use App\Models\Dependent;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class Register extends Component
{
    public $currentStep = 1;
    public $userData = [];
    public $dependentData = [];
    public $paymentData = [];
    public $dependents = [];

    protected $listeners = ['step1Complete', 'step2Complete', 'step3Complete'];

    public function step1Listener()
    {
        $this->currentStep = 2;
    }

    public function step1Complete($data)
    {
        $this->userData = $data;
        $this->currentStep = 2;
    }

    public function step2Complete($data)
    {
        $this->dependentData = $data;
        $this->currentStep = 3;
    }

    public function step3Complete($data)
    {
        $this->paymentData = $data;
        $this->submitForm();
    }

    public function decreaseStep()
    {
        $this->currentStep--;
    }

    public function submitForm() {}

    public function render()
    {
        return view('livewire.registration.register')->layout('layouts.guest');
    }
}
