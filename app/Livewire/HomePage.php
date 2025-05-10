<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class HomePage extends Component
{
    public function render()
    {
        $totalMembers = User::whereHas('roles', function ($query) {
            $query->where('name', 'user');
        })->whereDoesntHave('deathRecord')->count();

        return view('livewire.home-page', [
            'totalMembers' => $totalMembers,
        ])->layout('layouts.guest');
    }
}
