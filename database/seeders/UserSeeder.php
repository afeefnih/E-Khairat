<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Dependent;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Array of Malay names separated by gender
        $maleFirstNames = ['Ahmad', 'Mohd', 'Muhammad', 'Hafiz', 'Azman', 'Roslan', 'Rahim', 'Farid', 'Syafiq', 'Aiman', 'Sulaiman'];
        $femaleFirstNames = ['Nur', 'Siti', 'Aisyah', 'Fatimah', 'Aminah', 'Zainab', 'Nadia', 'Fatin', 'Hafizah'];

        $lastNames = ['Abdullah', 'Ahmad', 'Ismail', 'Ali', 'Hassan', 'Ibrahim', 'Othman', 'Rahman', 'Mahmud'];

        $relationships = ['Bapa', 'Ibu', 'Pasangan', 'Anak'];
        $usedIcNumbers = [];
        $usedEmails = [];
        $currentYear = (int)date('Y');
        $noAhliCounter = 1;

        // First truncate dependents table, then users table to avoid foreign key issues
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table('dependents')->truncate();
        \DB::table('users')->truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Get regular user role (assume role_id 2 is regular user)
        $userRole = \App\Models\Role::find(2);
        if (!$userRole) {
            // Create user role if it doesn't exist
            $userRole = \App\Models\Role::create([
                'name' => 'user',
                'description' => 'Regular User'
            ]);
        }

        // Create 50 users
        for ($i = 0; $i < 50; $i++) {
            // Generate gender and appropriate first name
            $isUserMale = fake()->boolean(60); // 60% chance of male user
            $userFirstName = $isUserMale
                ? fake()->randomElement($maleFirstNames)
                : fake()->randomElement($femaleFirstNames);

            $lastName = fake()->randomElement($lastNames);

            // Properly formatted full name based on gender
            $fullName = $userFirstName . ' ' . ($isUserMale ? 'bin ' : 'binti ') . $lastName;

            // Generate No_Ahli as 4-digit incrementing string
            $noAhli = str_pad($noAhliCounter++, 4, '0', STR_PAD_LEFT);

            // Generate unique IC number with proper century
            do {
                $birthDate = fake()->dateTimeBetween('-80 years', '-18 years');
                $year = (int)$birthDate->format('y');
                $century = $year > ((int)date('y')) ? 1900 : 2000;
                $fullYear = $century + $year;
                $icPrefix = $birthDate->format('ymd');
                $icSuffix = str_pad(fake()->numberBetween(0, 999999), 6, '0', STR_PAD_LEFT);
                $icNumber = $icPrefix . $icSuffix;
            } while (in_array($icNumber, $usedIcNumbers));

            $usedIcNumbers[] = $icNumber;
            $age = $currentYear - $fullYear;

            // Generate unique email
            do {
                $emailCounter = count($usedEmails) + 1;
                $email = strtolower($userFirstName) . '.' . strtolower($lastName) . $emailCounter . '@example.com';
            } while (in_array($email, $usedEmails));

            $usedEmails[] = $email;

            // Create user
            $user = \App\Models\User::create([
                'No_Ahli' => $noAhli,
                'ic_number' => $icNumber,
                'name' => $fullName,
                'email' => $email,
                'password' => bcrypt('password'),
                'phone_number' => '01' . fake()->numberBetween(1, 9) . fake()->numberBetween(1000000, 9999999),
                'address' => "No " . fake()->numberBetween(1, 99) . " Jalan Sutera " . fake()->numberBetween(1, 10) . "/" . fake()->numberBetween(1, 9) . " taman sutera",
                'age' => $age,
                'home_phone' => '03' . fake()->numberBetween(10000000, 99999999),
                'residence_status' => fake()->randomElement(['kekal', 'sewa']),
                'registration_date' => fake()->dateTimeBetween('-30 days', 'now'),
                'remember_token' => null,
            ]);

            // Assign user role
            $user->roles()->attach($userRole->id);

            // Generate 0-3 dependents for each user
            $numDependents = rand(0, 3);

            for ($j = 0; $j < $numDependents; $j++) {
                // Generate dependent's gender and relationship
                $relationship = fake()->randomElement($relationships);
                $isDependentMale = true; // default

                // Set gender based on relationship type
                if ($relationship === 'Bapa') {
                    $isDependentMale = true;
                } elseif ($relationship === 'Ibu') {
                    $isDependentMale = false;
                } else {
                    // For spouse or child, randomly determine gender
                    $isDependentMale = fake()->boolean();
                }

                // Get appropriate first name based on gender
                $dependentFirstName = $isDependentMale
                    ? fake()->randomElement($maleFirstNames)
                    : fake()->randomElement($femaleFirstNames);

                // Generate unique IC number for dependent
                do {
                    $birthDate = fake()->dateTimeBetween('-120 years', '-1 years');
                    $year = (int)$birthDate->format('y');
                    $century = $year > ((int)date('y')) ? 1900 : 2000;
                    $fullYear = $century + $year;
                    $icPrefix = $birthDate->format('ymd');
                    $icSuffix = str_pad(fake()->numberBetween(0, 999999), 6, '0', STR_PAD_LEFT);
                    $icNumber = $icPrefix . $icSuffix;
                } while (in_array($icNumber, $usedIcNumbers));

                $usedIcNumbers[] = $icNumber;
                $age = $currentYear - $fullYear;

                // Extract just the first name of the user for the dependent's name
                $userNameParts = explode(' ', $user->name);
                $userBaseName = $userNameParts[0];

                // Format dependent's name correctly
                if ($relationship === 'Bapa' || $relationship === 'Ibu') {
                    // Use user's first name as the last name for parent dependents
                    $dependentFullName = $dependentFirstName . ' ' .
                        ($isDependentMale ? 'bin ' : 'binti ') .
                        $userBaseName;
                } else {
                    // For spouse or children, create independent name
                    $dependentLastName = fake()->randomElement($lastNames);
                    $dependentFullName = $dependentFirstName . ' ' .
                        ($isDependentMale ? 'bin ' : 'binti ') .
                        $dependentLastName;
                }

                // Create dependent
                $user->dependents()->create([
                    'full_name' => $dependentFullName,
                    'ic_number' => $icNumber,
                    'age' => $age,
                    'relationship' => $relationship,
                ]);
            }
        }
    }
}
