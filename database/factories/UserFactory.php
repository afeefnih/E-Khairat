<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Jetstream\Features;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $ahliCounter = 1;
        static $usedIcNumbers = [];

        // Malay names samples
        $malayFirstNames = ['Ahmad', 'Mohd', 'Muhammad', 'Nur', 'Siti', 'Aisyah', 'Fatimah', 'Hafiz', 'Aminah', 'Azman', 'Roslan', 'Zainab', 'Rahim', 'Farid', 'Syafiq', 'Nadia', 'Fatin', 'Aiman', 'Hafizah', 'Sulaiman'];
        $malayLastNames = ['Bin Abdullah', 'Binti Abdullah', 'Bin Ahmad', 'Binti Ahmad', 'Bin Ismail', 'Binti Ismail', 'Bin Ali', 'Binti Ali', 'Bin Hassan', 'Binti Hassan'];
        $firstName = fake()->randomElement($malayFirstNames);
        $lastName = fake()->randomElement($malayLastNames);
        $name = $firstName . ' ' . $lastName;

        // Generate unique IC number with correct century calculation
        $currentYear = (int)date('y');
        do {
            $birthDate = fake()->dateTimeBetween('-80 years', '-18 years');
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

        // Address in format: No XX Jalan Sutera Y/Z taman sutera
        $houseNo = fake()->numberBetween(1, 99);
        $jalanNo = fake()->numberBetween(1, 10);
        $subSectionNo = fake()->numberBetween(1, 9);
        $address = "No $houseNo Jalan Sutera $jalanNo/$subSectionNo taman sutera";

        // Random registration date within last 5 years
        $registrationDate = fake()->dateTimeBetween('-5 years', 'now');

        return [
            'No_Ahli' => str_pad($ahliCounter++, 4, '0', STR_PAD_LEFT),
            'ic_number' => $icNumber,
            'name' => $name,
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => null,
            'phone_number' => '01' . fake()->numberBetween(1, 9) . fake()->numberBetween(1000000, 9999999),
            'address' => $address,
            'age' => $age,
            'home_phone' => '03' . fake()->numberBetween(10000000, 99999999),
            'residence_status' => fake()->randomElement(['kekal', 'sewa']),
            'registration_date' => $registrationDate,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user should have a personal team.
     */
    public function withPersonalTeam(?callable $callback = null): static
    {
        if (! Features::hasTeamFeatures()) {
            return $this->state([]);
        }

        return $this->has(
            Team::factory()
                ->state(fn (array $attributes, User $user) => [
                    'name' => $user->name.'\'s Team',
                    'user_id' => $user->id,
                    'personal_team' => true,
                ])
                ->when(is_callable($callback), $callback),
            'ownedTeams'
        );
    }
}
