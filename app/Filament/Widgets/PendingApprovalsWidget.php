<?php

namespace App\Filament\Widgets;

use App\Models\DependentEditRequest;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;
use App\Models\Dependent;
use App\Notifications\DependentRequestProcessed;


class PendingApprovalsWidget extends BaseWidget
{
    protected static ?string $heading = 'Permohonan Kelulusan';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 80;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                DependentEditRequest::query()
                    ->where('status', 'pending')
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('user.name')
                    ->label('Ahli')
                    ->searchable(),
                TextColumn::make('full_name')
                    ->label('Nama Tanggungan')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('request_type')
                    ->label('Jenis Permohonan')
                    ->colors([
                        'danger' => fn ($state) => $state === 'delete',
                        'success' => fn ($state) => $state === 'add',
                        'info' => fn ($state) => $state === 'edit',
                    ]),
                TextColumn::make('relationship')
                    ->label('Hubungan'),
                TextColumn::make('created_at')
                    ->label('Tarikh Permohonan')
                    ->dateTime('d M Y, H:i'),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Luluskan')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->requiresConfirmation()
                    ->modalHeading('Luluskan permohonan ini?')
                    ->modalDescription('Ini akan meluluskan permohonan dan membuat perubahan kepada tanggungan.')
                    ->modalSubmitActionLabel('Ya, luluskan')
                    ->action(function (DependentEditRequest $record) {
                        // Update request status
                        $record->status = 'approved';
                        $record->processed_by = Auth::id();
                        $record->processed_at = now();
                        $record->save();

                        // Process the request based on type
                        if ($record->request_type === 'add') {
                            // Create new dependent
                            Dependent::create([
                                'user_id' => $record->user_id,
                                'full_name' => $record->full_name,
                                'relationship' => $record->relationship,
                                'age' => $record->age,
                                'ic_number' => $record->ic_number,
                            ]);
                        } elseif ($record->request_type === 'edit') {
                            // Update existing dependent
                            $dependent = Dependent::find($record->dependent_id);
                            if ($dependent) {
                                $dependent->update([
                                    'full_name' => $record->full_name,
                                    'relationship' => $record->relationship,
                                    'age' => $record->age,
                                    'ic_number' => $record->ic_number,
                                ]);
                            }
                        } elseif ($record->request_type === 'delete') {
                            // Delete the dependent
                            $dependent = Dependent::find($record->dependent_id);
                            if ($dependent) {
                                $dependent->delete();
                            }
                        }

                        // Send notification to user
                        $record->user->notify(new DependentRequestProcessed($record));

                        // Show success notification
                        \Filament\Notifications\Notification::make()
                            ->title('Permohonan diluluskan')
                            ->success()
                            ->send();
                    }),
                Action::make('reject')
                    ->label('Tolak')
                    ->color('danger')
                    ->icon('heroicon-o-x-mark')
                    ->requiresConfirmation()
                    ->form([
                        Textarea::make('admin_comments')
                            ->label('Sebab Penolakan')
                            ->helperText('Ini akan dapat dilihat oleh ahli')
                            ->required()
                    ])
                    ->modalHeading('Tolak permohonan ini?')
                    ->modalDescription('Sila nyatakan sebab untuk menolak permohonan ini.')
                    ->modalSubmitActionLabel('Ya, tolak')
                    ->action(function (DependentEditRequest $record, array $data) {
                        $record->status = 'rejected';
                        $record->admin_comments = $data['admin_comments'];
                        $record->processed_by = Auth::id();
                        $record->processed_at = now();
                        $record->save();

                        // Send notification to user
                        $record->user->notify(new DependentRequestProcessed($record));

                        // Show success notification
                        \Filament\Notifications\Notification::make()
                            ->title('Permohonan ditolak')
                            ->success()
                            ->send();
                    }),
            ])
            ->emptyStateIcon('heroicon-o-document-check')
            ->emptyStateHeading('Tiada Permohonan Kelulusan Tertunggak');
    }
}
