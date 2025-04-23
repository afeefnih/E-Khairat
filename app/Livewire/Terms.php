<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PaymentCategory;
;

class Terms extends Component
{
    public $currentSection = 'all';
    public $language = 'ms'; // Default to Malay
    public $searchQuery = '';
    public $showMobileMenu = false;

    public $amount;

    public $sections = [
        'tanggungan' => [
            'id' => 1,
            'title' => 'Tanggungan Ahli',
            'icon' => 'users'
        ],
        'sumbangan' => [
            'id' => 2,
            'title' => 'Sumbangan Keahlian',
            'icon' => 'cash'
        ],
        'nota' => [
            'id' => 3,
            'title' => 'Nota Penting',
            'icon' => 'information-circle'
        ],
        'hak' => [
            'id' => 4,
            'title' => 'Hak dan Tanggungjawab Ahli',
            'icon' => 'shield-check'
        ],
        'perubahan' => [
            'id' => 5,
            'title' => 'Perubahan Syarat',
            'icon' => 'refresh'
        ],
    ];

    public function setSection($section)
    {
        $this->currentSection = $section;
        $this->showMobileMenu = false;
    }

    public function toggleLanguage()
    {
        $this->language = $this->language === 'ms' ? 'en' : 'ms';
    }

    public function toggleMobileMenu()
    {
        $this->showMobileMenu = !$this->showMobileMenu;
    }

    public function mount(){

        $category = PaymentCategory::find(1);

        if ($category) {
            $this->amount = $category->amount;
        } else {
            $this->amount = 0; // Default if not found
        }
    }

    public function render()
    {
        return view('livewire.terms');
    }
}
