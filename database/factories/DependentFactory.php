<?php

namespace Database\Factories;

use App\Models\Dependent;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class DependentFactory extends Factory
{
    protected $model = Dependent::class;

    public function definition()
    {
        static $usedIcNumbers = [];

        // Will only use first name here, full name will be set in seeder
        // based on the relationship and parent's name
        $malayFirstNames = ['Ahmad', 'Mohd', 'Muhammad', 'Nur', 'Siti', 'Aisyah', 'Fatimah', 'Hafiz',
                          'Aminah', 'Azman', 'Roslan', 'Zainab', 'Rahim', 'Farid', 'Syafiq', 'Nadia',
                          'Fatin', 'Aiman', 'Hafizah', 'Sulaiman'];

        // Generate unique IC number with correct century calculation
        $currentYear = (int)date('y');
        do {
            $birthDate = fake()->dateTimeBetween('-80 years', '-1 years');
            $birthYear = (int)$birthDate->format('y');
            $century = ($birthYear > $currentYear) ? 1900 : 2000; // If year > current year, it's 19xx, else 20xx
            $fullYear = $century + $birthYear;
            $icPrefix = $birthDate->format('ymd');
            $icSuffix = str_pad(fake()->numberBetween(0, 999999), 6, '0', STR_PAD_LEFT);
            $icNumber = $icPrefix . $icSuffix;
        } while (in_array($icNumber, $usedIcNumbers));
        $usedIcNumbers[] = $icNumber;

        // Calculate age correctly based on century calculation
        $age = Carbon::now()->year - $fullYear;

        return [
            'full_name' => fake()->randomElement($malayFirstNames), // Will be overridden in seeder
            'ic_number' => $icNumber,
            'age' => $age,
            'relationship' => fake()->randomElement(['Bapa', 'Ibu', 'Pasangan', 'Anak']),
        ];
    }
}
