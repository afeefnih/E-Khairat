<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\User;
use Filament\Notifications\Notification;
use App\Filament\Widgets\UserStatsWidget;
class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            UserStatsWidget::class,
        ];
    }


    protected function getHeaderActions(): array
    {
        return [


            Actions\CreateAction::make(),

            Actions\Action::make('exportPDF')
                ->label('Export PDF')
                ->color('danger')
                ->icon('heroicon-o-document-arrow-down')
                ->action(function () {
                    // Retrieve all users excluding admins
                    $users = User::whereDoesntHave('roles', function ($query) {
                        $query->where('name', 'admin');
                    })->get(); // Get users excluding 'admin' role

                    // Generate the PDF with the filtered users
                    $pdf = Pdf::loadView('pdf.users', [
                        'users' => $users,
                    ])
                    ->setPaper('A4', 'landscape'); // Set paper to landscape

                    // Return the PDF download response
                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->output();
                    }, 'users-' . date('Y-m-d') . '.pdf');
                }),

            Actions\Action::make('exportCSV')
                ->label('Export CSV')
                ->color('success')
                ->icon('heroicon-o-document-arrow-down')
                ->action(function () {
                    // Get all users
                    $users = User::whereDoesntHave('roles', function ($query) {
                        $query->where('name', 'admin');
                    })->get();

                    // Check if any users remain after filtering
                    if ($users->isEmpty()) {
                        Notification::make()
                            ->title('No users to export')
                            ->danger()
                            ->send();
                        return;
                    }

                    // Generate CSV file
                    $csvFileName = 'users-' . date('Y-m-d') . '.csv';
                    $headers = [
                        'Content-Type' => 'text/csv',
                        'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
                    ];

                    $callback = function () use ($users) {
                        $file = fopen('php://output', 'w');

                        // Add headers
                        fputcsv($file, [
                            'No_Ahli',
                            'Name',
                            'Email',
                            'IC Number',
                            'Age',
                            'Home Phone',
                            'Phone Number',
                            'Address',
                            'Residence Status',
                            'Dependents Count',
                            'Created At',
                        ]);

                        // Add rows
                        foreach ($users as $user) {
                            fputcsv($file, [
                                $user->No_Ahli ?? 'N/A',
                                $user->name,
                                $user->email ?? 'N/A',
                                $user->ic_number,
                                $user->age,
                                $user->home_phone,
                                $user->phone_number,
                                $user->address,
                                $user->residence_status,
                                $user->dependents->count(), // Using relationship count
                                $user->created_at,
                            ]);
                        }

                        fclose($file);
                    };

                    return response()->stream($callback, 200, $headers);
                }),



        ];
    }
}
