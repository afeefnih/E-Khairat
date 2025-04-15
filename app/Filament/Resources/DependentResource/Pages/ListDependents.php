<?php

namespace App\Filament\Resources\DependentResource\Pages;

use App\Filament\Resources\DependentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Models\Dependent;
use Filament\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;

class ListDependents extends ListRecords
{
    protected static string $resource = DependentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

            Actions\Action::make('exportPDF')
                ->label('Export PDF')
                ->color('danger')
                ->icon('heroicon-o-document-arrow-down')
                ->action(function () {
                    // Get all dependents with their related users
                    $dependents = Dependent::with('user')->get();

                    // Check if any dependents exist
                    if ($dependents->isEmpty()) {
                        Notification::make()
                            ->title('No dependents to export')
                            ->danger()
                            ->send();
                        return;
                    }

                    // Generate the PDF with the dependents
                    $pdf = Pdf::loadView('pdf.dependents', [
                        'dependents' => $dependents,
                    ]); // Set paper to landscape

                    // Return the PDF download response
                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->output();
                    }, 'dependents-' . date('Y-m-d') . '.pdf');
                }),

            Actions\Action::make('exportCSV')
                ->label('Export CSV')
                ->color('success')
                ->icon('heroicon-o-document-arrow-down')
                ->action(function () {
                    // Get all dependents
                    $dependents = Dependent::with('user')->get();

                    // Check if any dependents exist
                    if ($dependents->isEmpty()) {
                        Notification::make()
                            ->title('No dependents to export')
                            ->danger()
                            ->send();
                        return;
                    }

                    // Generate CSV file
                    $csvFileName = 'dependents-' . date('Y-m-d') . '.csv';
                    $headers = [
                        'Content-Type' => 'text/csv',
                        'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
                    ];

                    $callback = function () use ($dependents) {
                        $file = fopen('php://output', 'w');

                        // Add headers
                        fputcsv($file, [
                            'Member Name',
                            'Full Name',
                            'Relationship',
                            'Age',
                            'IC Number',
                            'Created At',
                        ]);

                        // Add rows
                        foreach ($dependents as $dependent) {
                            fputcsv($file, [
                                $dependent->user ? $dependent->user->name : 'N/A',
                                $dependent->full_name,
                                $dependent->relationship,
                                $dependent->age,
                                $dependent->ic_number,
                                $dependent->created_at,
                            ]);
                        }

                        fclose($file);
                    };

                    return response()->stream($callback, 200, $headers);
                }),
        ];
    }
}
