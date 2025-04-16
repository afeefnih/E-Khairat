<?php

namespace App\Filament\Resources\DeathRecordResource\Pages;

use App\Filament\Resources\DeathRecordResource;
use App\Filament\Resources\UserResource;
use App\Models\Dependent;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;

class EditDeathRecord extends EditRecord
{
    protected static string $resource = DeathRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // View Member Action
            Action::make('viewMember')
                ->label('View Member')
                ->icon('heroicon-o-users')
                ->color('success')
                ->url(function () {
                    try {
                        // For dependents, link to their member
                        if ($this->record->deceased_type === 'App\\Models\\Dependent' ||
                            $this->record->deceased_type === Dependent::class) {

                            // Check for deceased relationship
                            if ($this->record->deceased && $this->record->deceased->user_id) {
                                return UserResource::getUrl('edit', ['record' => $this->record->deceased->user_id]);
                            }

                            // For legacy records
                            if ($this->record->dependent_id) {
                                $dependent = Dependent::find($this->record->dependent_id);
                                if ($dependent && $dependent->user_id) {
                                    return UserResource::getUrl('edit', ['record' => $dependent->user_id]);
                                }
                            }
                        }

                        return null;
                    } catch (\Exception $e) {
                        // Log the error but don't crash
                        \Illuminate\Support\Facades\Log::error("Error in viewMember button: " . $e->getMessage());
                        return null;
                    }
                })
                ->openUrlInNewTab()
                ->visible(function () {
                    try {
                        $isDependent = $this->record->deceased_type === 'App\\Models\\Dependent' ||
                                      $this->record->deceased_type === Dependent::class ||
                                      $this->record->dependent_id;

                        return $isDependent;
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error("Error in viewMember visibility: " . $e->getMessage());
                        return false;
                    }
                }),

            Actions\DeleteAction::make(),
        ];
    }
}
