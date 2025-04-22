<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DependentEditRequestResource\Pages;
use App\Models\Dependent;
use App\Models\DependentEditRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use App\Notifications\DependentRequestProcessed;

class DependentEditRequestResource extends Resource
{
    protected static ?string $model = DependentEditRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Permohonan Kelulusan';

    protected static ?string $navigationGroup = 'Pengurusan Ahli';

     // Add the pending request count to the navigation
     public static function getNavigationBadge(): string
     {
         return (string) DependentEditRequest::where('status', 'pending')->count();
     }
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Permohonan Kelulusan';
    protected static ?string $pluralModelLabel = 'Permohonan Kelulusan';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Butiran Permohonan')->schema([
                Forms\Components\Select::make('user_id')->relationship('user', 'name')->label('Ahli')->required()->disabled(),

                Forms\Components\Select::make('dependent_id')->relationship('dependent', 'full_name')->label('Nama Tanggungan')->disabled(),

                Forms\Components\Select::make('request_type')
                    ->options([
                        'add' => 'Tambah Tanggungan Baru',
                        'edit' => 'Kemaskini Tanggungan Sedia Ada',
                        'delete' => 'Padam Tanggungan',
                    ])
                    ->label('Jenis Permohonan')
                    ->required()
                    ->disabled(),

                Forms\Components\TextInput::make('full_name')->label('Nama Penuh')->required()->maxLength(255)->disabled(),

                Forms\Components\TextInput::make('relationship')->label('Hubungan')->required()->maxLength(255)->disabled(),

                Forms\Components\TextInput::make('age')->label('Umur')->required()->numeric()->disabled(),

                Forms\Components\TextInput::make('ic_number')->label('Nombor IC')->required()->maxLength(12)->disabled(),
            ]),

            Forms\Components\Section::make('Pemprosesan')->schema([
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Menunggu',
                        'approved' => 'Diluluskan',
                        'rejected' => 'Ditolak',
                    ])
                    ->label('Status')
                    ->required(),

                Forms\Components\Textarea::make('admin_comments')
                    ->label('Komen Pentadbir')
                    ->placeholder('Tambah komen tentang permohonan ini, terutamanya jika ditolak')
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Ahli')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nama Tanggungan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\IconColumn::make('request_type')
                    ->label('Jenis Permohonan')
                    ->icon(
                        fn(string $state): string => match ($state) {
                            'add' => 'heroicon-o-plus',
                            'edit' => 'heroicon-o-pencil',
                            'delete' => 'heroicon-o-trash',
                            default => 'heroicon-o-question-mark-circle',
                        },
                    )
                    ->color(
                        fn(string $state): string => match ($state) {
                            'add' => 'success',
                            'edit' => 'info',
                            'delete' => 'danger',
                            default => 'gray',
                        },
                    ),

                Tables\Columns\TextColumn::make('request_type')
                    ->label('Jenis Permohonan')
                    ->badge()
                    ->color(
                        fn(string $state): string => match ($state) {
                            'add' => 'success',
                            'edit' => 'info',
                            'delete' => 'danger',
                            default => 'gray',
                        },
                    ),

                Tables\Columns\TextColumn::make('relationship')
                    ->label('Hubungan')
                    ->searchable(),

                Tables\Columns\TextColumn::make('age')
                    ->label('Umur')
                    ->sortable(),

                Tables\Columns\TextColumn::make('ic_number')
                    ->label('Nombor IC')
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(
                        fn(string $state): string => match ($state) {
                            'pending' => 'warning',
                            'approved' => 'success',
                            'rejected' => 'danger',
                            default => 'gray',
                        },
                    )
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tarikh Permohonan')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('processed_at')
                    ->label('Tarikh Diproses')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'pending',
                        'approved' => 'approved',
                        'rejected' => 'rejected',

                    ])
                    ->default('pending'),

                Tables\Filters\SelectFilter::make('request_type')
                    ->label('Jenis Permohonan')
                    ->options([
                        'add' => 'Tambah Baru',
                        'edit' => 'Kemaskini Sedia Ada',
                        'delete' => 'Padam',
                    ]),

                Tables\Filters\Filter::make('created_at')
                    ->label('Tarikh Permohonan')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')->label('Dari'),
                        Forms\Components\DatePicker::make('created_until')->label('Sehingga'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Dari ' . $data['created_from'];
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Sehingga ' . $data['created_until'];
                        }
                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([

                    Tables\Actions\Action::make('approve')
                        ->label('Luluskan')
                        ->color('success')
                        ->icon('heroicon-o-check')
                        ->requiresConfirmation()
                        ->modalHeading('Luluskan permohonan ini?')
                        ->modalDescription('Ini akan meluluskan permohonan dan membuat perubahan kepada tanggungan.')
                        ->modalSubmitActionLabel('Ya, luluskan')
                        ->visible(fn(DependentEditRequest $record) => $record->status === 'pending')
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
                                ->title('Request approved')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\Action::make('reject')
                        ->label('Tolak')
                        ->color('danger')
                        ->icon('heroicon-o-x-mark')
                        ->requiresConfirmation()
                        ->visible(fn(DependentEditRequest $record) => $record->status === 'pending')
                        ->form([
                            Forms\Components\Textarea::make('admin_comments')
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
                                ->title('Request rejected')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\ViewAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approveBulk')
                        ->label('Luluskan Dipilih')
                        ->color('success')
                        ->icon('heroicon-o-check')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(function ($records) {
                            $approvedCount = 0;

                            foreach ($records as $record) {
                                if ($record->status === 'pending') {
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

                                    $approvedCount++;
                                }
                            }

                            // Show success notification
                            \Filament\Notifications\Notification::make()
                                ->title($approvedCount . ' ' . str('request')->plural($approvedCount) . ' approved')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\BulkAction::make('rejectBulk')
                        ->label('Tolak Dipilih')
                        ->color('danger')
                        ->icon('heroicon-o-x-mark')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->form([
                            Forms\Components\Textarea::make('admin_comments')
                                ->label('Sebab Penolakan')
                                ->helperText('Ini akan dapat dilihat oleh semua ahli')
                                ->required()
                        ])
                        ->action(function ($records, array $data) {
                            $rejectedCount = 0;

                            foreach ($records as $record) {
                                if ($record->status === 'pending') {
                                    $record->status = 'rejected';
                                    $record->admin_comments = $data['admin_comments'];
                                    $record->processed_by = Auth::id();
                                    $record->processed_at = now();
                                    $record->save();

                                    // Send notification to user
                                    $record->user->notify(new DependentRequestProcessed($record));

                                    $rejectedCount++;
                                }
                            }

                            // Show success notification
                            \Filament\Notifications\Notification::make()
                                ->title($rejectedCount . ' ' . str('request')->plural($rejectedCount) . ' rejected')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->emptyStateHeading('Tiada Permohonan Kelulusan')
            ->emptyStateDescription('Apabila ahli menambah, mengemaskini, atau memadam tanggungan, permohonan akan muncul di sini untuk kelulusan.')
            ->emptyStateIcon('heroicon-o-clipboard-document-check');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Butiran Permohonan')
                ->schema([
                    Infolists\Components\TextEntry::make('user.name')->label('Ahli'),

                    Infolists\Components\TextEntry::make('request_type')
                        ->label('Jenis Permohonan')
                        ->badge()
                        ->color(
                            fn(string $state): string => match ($state) {
                                'add' => 'success',
                                'edit' => 'info',
                                'delete' => 'danger',
                                default => 'gray',
                            },
                        ),

                    Infolists\Components\TextEntry::make('status')
                        ->label('Status')
                        ->badge()
                        ->color(
                            fn(string $state): string => match ($state) {
                                'pending' => 'warning',
                                'approved' => 'success',
                                'rejected' => 'danger',
                                default => 'gray',
                            },
                        ),

                    Infolists\Components\TextEntry::make('created_at')->label('Tarikh Permohonan')->dateTime(),
                ])
                ->columns(2),

            Infolists\Components\Section::make('Maklumat Tanggungan')
                ->schema([
                    Infolists\Components\TextEntry::make('full_name')->label('Nama'),
                    Infolists\Components\TextEntry::make('relationship')->label('Hubungan'),
                    Infolists\Components\TextEntry::make('age')->label('Umur'),
                    Infolists\Components\TextEntry::make('ic_number')->label('Nombor IC')
                ])
                ->columns(2),

            Infolists\Components\Section::make('Maklumat Pemprosesan')
                ->schema([
                    Infolists\Components\TextEntry::make('processor.name')
                        ->label('Diproses Oleh')
                        ->visible(fn(DependentEditRequest $record): bool => $record->processed_by !== null),

                    Infolists\Components\TextEntry::make('processed_at')
                        ->label('Tarikh Diproses')
                        ->dateTime()
                        ->visible(fn(DependentEditRequest $record): bool => $record->processed_at !== null),

                    Infolists\Components\TextEntry::make('admin_comments')
                        ->label('Komen Pentadbir')
                        ->columnSpanFull()
                        ->visible(fn(DependentEditRequest $record): bool => $record->admin_comments !== null)
                ])
                ->columns(2)
                ->visible(fn(DependentEditRequest $record): bool => $record->status !== 'pending'),
        ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDependentEditRequests::route('/'),
            //'view' => Pages\ViewDependentEditRequest::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['user', 'dependent', 'processor']);
    }
}
