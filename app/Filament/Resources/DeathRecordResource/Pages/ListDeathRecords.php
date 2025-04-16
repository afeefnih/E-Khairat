<?php

namespace App\Filament\Resources\DeathRecordResource\Pages;

use App\Filament\Resources\DeathRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Models\DeathRecord;
use App\Models\User;
use App\Models\Dependent;
use Filament\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;

class ListDeathRecords extends ListRecords
{
    protected static string $resource = DeathRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

            Actions\Action::make('exportPDF')
                ->label('Export PDF')
                ->color('danger')
                ->icon('heroicon-o-document-arrow-down')
                ->action(function () {
                    // Get all death records with eager loading for both relationships
                    $records = DeathRecord::with(['dependent', 'deceased'])->get();

                    // Check if any records exist
                    if ($records->isEmpty()) {
                        Notification::make()
                            ->title('No death records to export')
                            ->danger()
                            ->send();
                        return;
                    }

                    // Generate the PDF with the records
                    $pdf = Pdf::loadView('pdf.death-records', [
                        'records' => $records,
                    ]);

                    // Return the PDF download response
                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->output();
                    }, 'death-records-' . date('Y-m-d') . '.pdf');
                }),

            Actions\Action::make('exportCSV')
                ->label('Export CSV')
                ->color('success')
                ->icon('heroicon-o-document-arrow-down')
                ->action(function () {
                    // Get all death records with eager loading for both relationships
                    $records = DeathRecord::with(['dependent', 'deceased'])->get();

                    // Check if any records exist
                    if ($records->isEmpty()) {
                        Notification::make()
                            ->title('No death records to export')
                            ->danger()
                            ->send();
                        return;
                    }

                    // Generate CSV file
                    $csvFileName = 'death-records-' . date('Y-m-d') . '.csv';
                    $headers = [
                        'Content-Type' => 'text/csv',
                        'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
                    ];

                    $callback = function () use ($records) {
                        $file = fopen('php://output', 'w');

                        // Add headers
                        fputcsv($file, [
                            'Record Type',
                            'Name',
                            'No Ahli',
                            'IC Number',
                            'Date of Death',
                            'Time of Death',
                            'Place of Death',
                            'Cause of Death',
                            'Death Notes',
                            'Certificate Available',
                            'Created At',
                        ]);

                        // Add rows
                        foreach ($records as $record) {
                            // Determine record type
                            $recordType = 'Dependent';
                            $name = 'N/A';
                            $noAhli = 'N/A';
                            $icNumber = 'N/A';

                            // Handle Primary Member (User)
                            if ($record->deceased_type === 'App\\Models\\User' || strpos($record->deceased_type, 'User') !== false) {
                                $recordType = 'Primary Member';

                                // Try to get user directly if relationship isn't loaded
                                $user = $record->deceased;
                                if (!$user && $record->deceased_id) {
                                    $user = User::find($record->deceased_id);
                                }

                                if ($user) {
                                    $name = $user->name ?? 'Unknown';
                                    $noAhli = $user->No_Ahli ?? 'N/A';
                                    $icNumber = $user->ic_number ?? 'N/A';
                                }
                            }
                            // Handle Dependent
                            else {
                                // Try to get data from the polymorphic relationship
                                $dependent = $record->deceased;
                                if (!$dependent && $record->deceased_id) {
                                    $dependent = Dependent::find($record->deceased_id);
                                }

                                // If still not found, try legacy relationship
                                if (!$dependent && $record->dependent_id) {
                                    $dependent = $record->dependent ?: Dependent::find($record->dependent_id);
                                }

                                if ($dependent) {
                                    $name = $dependent->full_name ?? 'Unknown';
                                    $icNumber = $dependent->ic_number ?? 'N/A';

                                    // Get No_Ahli from the User related to this dependent
                                    if ($dependent->user) {
                                        $noAhli = $dependent->user->No_Ahli ?? 'N/A';
                                    }
                                }
                            }

                            fputcsv($file, [
                                $recordType,
                                $name,
                                $noAhli,
                                $icNumber,
                                $record->date_of_death ? $record->date_of_death->format('Y-m-d') : 'N/A',
                                $record->time_of_death ? $record->time_of_death->format('H:i') : 'N/A',
                                $record->place_of_death ?? 'N/A',
                                $record->cause_of_death ?? 'N/A',
                                $record->death_notes ?? 'N/A',
                                $record->death_attachment_path ? 'Yes' : 'No',
                                $record->created_at->format('Y-m-d'),
                            ]);
                        }

                        fclose($file);
                    };

                    return response()->stream($callback, 200, $headers);
                }),
        ];
    }
}
