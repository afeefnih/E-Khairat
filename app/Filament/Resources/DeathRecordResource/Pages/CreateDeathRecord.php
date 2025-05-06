<?php

namespace App\Filament\Resources\DeathRecordResource\Pages;

use App\Filament\Resources\DeathRecordResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Dependent;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class CreateDeathRecord extends CreateRecord
{
    protected static string $resource = DeathRecordResource::class;

    /**
     * Override mount to handle URL parameters for pre-filling form data
     */
    public function mount(): void
    {
        // Get data from URL parameters
        $deceasedType = request()->query('deceased_type');
        $deceasedId = request()->query('deceased_id');



        // Call parent mount first to initialize the form
        parent::mount();

        // If we have valid parameters, pre-fill the form with deceased info
        if ($deceasedType && $deceasedId) {
            $formData = [
                'deceased_type' => $deceasedType,
                'deceased_id' => $deceasedId,
            ];

            // Fill member details based on deceased type
            if ($deceasedType === 'App\\Models\\User') {
                $user = User::find($deceasedId);
                if ($user) {
                    Log::info("Found User for death record:", ['user_id' => $user->id, 'name' => $user->name]);

                    // Calculate death cost based on age
                    $ageCost = 1050; // Default for adults
                    $ageCategory = 'Dewasa';

                    if (is_numeric($user->age)) {
                        $age = (int) $user->age;
                        if ($age <= 3) {
                            $ageCost = 450;
                            $ageCategory = 'Janin - 3 tahun';
                        } elseif ($age >= 4 && $age <= 6) {
                            $ageCost = 650;
                            $ageCategory = 'Kanak-kanak (4-6 tahun)';
                        }
                    }

                    // Additional member data to fill
                    $formData = array_merge($formData, [
                        'age_category' => $ageCategory,
                        'calculated_amount' => (string)$ageCost,
                        'final_amount' => (string)$ageCost,
                        'custom_amount' => '0',
                    ]);
                }
            } elseif ($deceasedType === 'App\\Models\\Dependent') {
                $dependent = Dependent::find($deceasedId);
                if ($dependent) {
                    Log::info("Found Dependent for death record:", ['dependent_id' => $dependent->dependent_id, 'name' => $dependent->full_name]);

                    // Calculate death cost based on age
                    $ageCost = 1050; // Default for adults
                    $ageCategory = 'Dewasa';

                    if (is_numeric($dependent->age)) {
                        $age = (int) $dependent->age;
                        if ($age <= 3) {
                            $ageCost = 450;
                            $ageCategory = 'Janin - 3 tahun';
                        } elseif ($age >= 4 && $age <= 6) {
                            $ageCost = 650;
                            $ageCategory = 'Kanak-kanak (4-6 tahun)';
                        }
                    }

                    // Additional member data to fill
                    $formData = array_merge($formData, [
                        'age_category' => $ageCategory,
                        'calculated_amount' => (string)$ageCost,
                        'final_amount' => (string)$ageCost,
                        'custom_amount' => '0',
                    ]);
                }
            }

            // Fill the form with all the collected data
            $this->form->fill($formData);

            // Log the data being filled
            Log::info("Pre-filling death record form with data:", $formData);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        // Get the created record
        $record = $this->record;

        // Handle status updates based on the death record type
        if ($record->deceased_type === User::class && $record->deceased_id) {
            // Logic for when a primary member has died
            $user = User::find($record->deceased_id);
            if ($user) {
                // Notify admins about orphaned dependents
                $dependentsCount = $user->dependents()->count();
                if ($dependentsCount > 0) {
                    Notification::make()
                        ->title("Ahli {$user->name} mempunyai {$dependentsCount} tanggungan")
                        ->warning()
                        ->persistent()
                        ->send();
                }
            }
        } elseif ($record->deceased_type === Dependent::class && $record->deceased_id) {
            // Logic for when a dependent has died
            $dependent = Dependent::find($record->deceased_id);
            // You can add additional logic here if needed
        }
    }

    protected function handleRecordCreation(array $data): Model
    {
        // Log the data being submitted
        Log::info('Creating death record with data:', $data);

        return static::getModel()::create($data);
    }
}
