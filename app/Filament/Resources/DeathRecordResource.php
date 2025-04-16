<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeathRecordResource\Pages;
use App\Models\DeathRecord;
use App\Models\User;
use App\Models\Dependent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Filament\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\Action;
use Illuminate\Support\HtmlString;

class DeathRecordResource extends Resource
{
    protected static ?string $model = DeathRecord::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Death Records';

    protected static ?string $navigationGroup = 'Records Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Death Record Type Selector
            Forms\Components\Section::make('Death Record Information')->schema([
                Forms\Components\Select::make('deceased_type')
                    ->label('Record Type')
                    ->options([
                        User::class => 'Primary Member',
                        Dependent::class => 'Dependent',
                    ])
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, $set) {
                        $set('deceased_id', null);
                        // Clear member info fields
                        $set('member_no', null);
                        $set('member_address', null);
                    })
                    ->default(Dependent::class),

                Forms\Components\Select::make('deceased_id')
                    ->label(fn(callable $get) => $get('deceased_type') === User::class ? 'Member' : 'Dependent')
                    ->options(function (callable $get) {
                        $type = $get('deceased_type');

                        if ($type === User::class) {
                            return User::query()->whereDoesntHave('deathRecord')->pluck('name', 'id')->toArray();
                        }

                        if ($type === Dependent::class) {
                            // Use full_name for display but dependent_id as the value
                            $dependents = Dependent::query()
                                ->select(['dependent_id', 'full_name'])
                                ->whereDoesntHave('deathRecord')
                                ->get();

                            // Create an associative array with dependent_id => full_name
                            return $dependents->pluck('full_name', 'dependent_id')->toArray();
                        }

                        return [];
                    })
                    ->getOptionLabelUsing(function ($value, $get) {
                        $type = $get('deceased_type');

                        if ($type === User::class) {
                            $user = User::find($value);
                            return $user ? $user->name : null;
                        }

                        if ($type === Dependent::class) {
                            $dependent = Dependent::find($value);
                            return $dependent ? $dependent->full_name : null;
                        }

                        return null;
                    })
                    ->searchable()
                    ->required()
                    ->reactive(),
            ]),

            // Member Information Section
            Forms\Components\Section::make('Member Information')
                ->schema([
                    Forms\Components\TextInput::make('member_no')
                        ->label('No Ahli')
                        ->formatStateUsing(function ($state, $record, $get) {
                            // For edit form with existing record
                            if ($record) {
                                // If this is a User (Primary Member)
                                if ($record->deceased_type === 'App\\Models\\User') {
                                    $user = User::find($record->deceased_id);
                                    return $user ? $user->No_Ahli : 'N/A';
                                }

                                // If this is a Dependent
                                if ($record->deceased_type === 'App\\Models\\Dependent') {
                                    $dependent = Dependent::find($record->deceased_id);
                                    if ($dependent && $dependent->user) {
                                        return $dependent->user->No_Ahli ?? 'N/A';
                                    }
                                }

                                return 'N/A';
                            }

                            // For create form
                            $deceasedType = $get('deceased_type');
                            $deceasedId = $get('deceased_id');

                            if (!$deceasedType || !$deceasedId) {
                                return 'N/A';
                            }

                            if ($deceasedType === User::class) {
                                $user = User::find($deceasedId);
                                return $user ? $user->No_Ahli : 'N/A';
                            } elseif ($deceasedType === Dependent::class) {
                                $dependent = Dependent::find($deceasedId);
                                return ($dependent && $dependent->user) ? $dependent->user->No_Ahli : 'N/A';
                            }

                            return 'N/A';
                        })
                        ->disabled()
                        ->dehydrated(false),

                    Forms\Components\Textarea::make('member_address')
                        ->label('Address')
                        ->formatStateUsing(function ($state, $record, $get) {
                            // For edit form with existing record
                            if ($record) {
                                // If this is a User (Primary Member)
                                if ($record->deceased_type === 'App\\Models\\User' || $record->deceased_type === User::class) {
                                    $user = User::find($record->deceased_id);
                                    return $user ? $user->address : 'N/A';
                                }

                                // If this is a Dependent
                                if ($record->deceased_type === 'App\\Models\\Dependent' || $record->deceased_type === Dependent::class) {
                                    $dependent = Dependent::find($record->deceased_id);
                                    if ($dependent && $dependent->user) {
                                        return $dependent->user->address ?? 'N/A';
                                    }
                                }

                                return 'N/A';
                            }

                            // For create form
                            $deceasedType = $get('deceased_type');
                            $deceasedId = $get('deceased_id');

                            if (!$deceasedType || !$deceasedId) {
                                return 'N/A';
                            }

                            if ($deceasedType === User::class) {
                                $user = User::find($deceasedId);
                                return $user ? $user->address : 'N/A';
                            } elseif ($deceasedType === Dependent::class) {
                                $dependent = Dependent::find($deceasedId);
                                return ($dependent && $dependent->user) ? $dependent->user->address : 'N/A';
                            }

                            return 'N/A';
                        })
                        ->disabled()
                        ->dehydrated(false)
                        ->rows(2),
                ])
                ->columns(2)
                ->collapsible(),

            // Death Information
            Forms\Components\DatePicker::make('date_of_death')->required()->label('Date of Death'),

            Forms\Components\TimePicker::make('time_of_death')->label('Time of Death')->seconds(false),

            Forms\Components\TextInput::make('place_of_death')->label('Place of Death')->required()->maxLength(255),

            Forms\Components\Textarea::make('cause_of_death')->label('Cause of Death')->rows(3)->maxLength(1000),

            Forms\Components\Textarea::make('death_notes')->label('Notes')->rows(3)->maxLength(1000),

            Forms\Components\Section::make('Death Certificate')->schema([
                Forms\Components\FileUpload::make('death_attachment_path')
                    ->label('Death Certificate')
                    ->directory('death-certificates')
                    ->visibility('private')
                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                    ->downloadable()
                    ->maxSize(5120), // 5MB
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Type indicator
                Tables\Columns\TextColumn::make('deceased_type')
                    ->label('Type')
                    ->formatStateUsing(function ($state) {
                        if ($state === 'App\\Models\\User' || $state === 'AppModelsUser') {
                            return 'Primary Member';
                        }
                        return 'Dependent';
                    })
                    ->badge()
                    ->color(fn($state) => $state === 'App\\Models\\User' || $state === 'AppModelsUser' ? 'danger' : 'warning'),

                // Name column
                Tables\Columns\TextColumn::make('deceased_name')
                    ->searchable()
                    ->sortable()
                    ->label('Name')
                    ->getStateUsing(function ($record) {
                        // Safe access to record relationships
                        try {
                            // Check for string variants of class names
                            $isUser = $record->deceased_type === '\\App\\Models\\User' || $record->deceased_type === 'App\\Models\\User' || strpos($record->deceased_type, 'User') !== false;

                            $isDependent = $record->deceased_type === '\\App\\Models\\Dependent' || $record->deceased_type === 'App\\Models\\Dependent' || strpos($record->deceased_type, 'Dependent') !== false;

                            if ($isUser && $record->deceased) {
                                return $record->deceased->name ?? 'Unknown Member';
                            }

                            if ($isDependent && $record->deceased) {
                                return $record->deceased->full_name ?? 'Unknown Dependent';
                            }
                        } catch (\Exception $e) {
                            // Log error for debugging but don't crash the page
                            \Illuminate\Support\Facades\Log::error("Error getting name for death record {$record->id}: " . $e->getMessage());
                        }

                        return 'Unknown';
                    }),

                // No Ahli column
                Tables\Columns\TextColumn::make('member_no')->label('No Ahli')->searchable(),

                // IC Number column
                Tables\Columns\TextColumn::make('deceased_ic_number')
                    ->searchable()
                    ->label('IC Number')
                    ->getStateUsing(function ($record) {
                        try {
                            // Check for string variants of class names
                            $isUser = $record->deceased_type === '\\App\\Models\\User' || $record->deceased_type === 'App\\Models\\User' || strpos($record->deceased_type, 'User') !== false;

                            $isDependent = $record->deceased_type === '\\App\\Models\\Dependent' || $record->deceased_type === 'App\\Models\\Dependent' || strpos($record->deceased_type, 'Dependent') !== false;

                            if ($isUser && $record->deceased) {
                                return $record->deceased->ic_number ?? 'N/A';
                            }

                            if ($isDependent && $record->deceased) {
                                return $record->deceased->ic_number ?? 'N/A';
                            }
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error("Error getting IC for death record {$record->id}: " . $e->getMessage());
                        }

                        return 'N/A';
                    }),

                Tables\Columns\TextColumn::make('date_of_death')->date()->sortable()->label('Date of Death'),

                Tables\Columns\TextColumn::make('place_of_death')->searchable()->limit(30)->label('Place of Death'),

                Tables\Columns\IconColumn::make('death_attachment_path')->boolean()->label('Certificate')->trueIcon('heroicon-o-document')->falseIcon('heroicon-o-x-mark')->getStateUsing(fn(DeathRecord $record) => $record->death_attachment_path !== null),

                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('deceased_type')
                    ->label('Record Type')
                    ->options([
                        User::class => 'Primary Member',
                        Dependent::class => 'Dependent',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('viewCertificate')->label('View Certificate')->icon('heroicon-o-eye')->color('info')->visible(fn($record) => $record->death_attachment_path)->url(fn($record) => $record->death_attachment_path ? Storage::url($record->death_attachment_path) : null, true),

                // View Member Action for dependents
                Tables\Actions\Action::make('view_member')
                    ->label('View Member')
                    ->icon('heroicon-o-users')
                    ->color('success')
                    ->url(function (DeathRecord $record) {
                        // For dependents, link to their member
                        if ($record->deceased_type === Dependent::class && $record->deceased && $record->deceased->user_id) {
                            return UserResource::getUrl('edit', ['record' => $record->deceased->user_id]);
                        }

                        // For primary members, don't show this button
                        return null;
                    })
                    ->openUrlInNewTab()
                    ->visible(function (DeathRecord $record) {
                        // Only show for dependent records
                        return $record->deceased_type === Dependent::class &&
                               $record->deceased &&
                               $record->deceased->user_id;
                    }),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    // Add CSV Export Bulk Action
                    BulkAction::make('export-csv')
                        ->label('Export CSV')
                        ->color('success')
                        ->icon('heroicon-o-document-arrow-down')
                        ->action(function () {
                            // Get all death records with eager loading for relationships
                            $records = DeathRecord::with(['deceased'])->get();

                            // Check if any records exist
                            if ($records->isEmpty()) {
                                Notification::make()->title('No death records to export')->danger()->send();
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
                                fputcsv($file, ['Record Type', 'Name', 'No Ahli', 'IC Number', 'Date of Death', 'Time of Death', 'Place of Death', 'Cause of Death', 'Death Notes', 'Certificate Available', 'Created At']);

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

                                        if ($dependent) {
                                            $name = $dependent->full_name ?? 'Unknown';
                                            $icNumber = $dependent->ic_number ?? 'N/A';

                                            // Get No_Ahli from the User related to this dependent
                                            if ($dependent->user) {
                                                $noAhli = $dependent->user->No_Ahli ?? 'N/A';
                                            }
                                        }
                                    }

                                    fputcsv($file, [$recordType, $name, $noAhli, $icNumber, $record->date_of_death ? $record->date_of_death->format('Y-m-d') : 'N/A', $record->time_of_death ? $record->time_of_death->format('H:i') : 'N/A', $record->place_of_death ?? 'N/A', $record->cause_of_death ?? 'N/A', $record->death_notes ?? 'N/A', $record->death_attachment_path ? 'Yes' : 'No', $record->created_at->format('Y-m-d')]);
                                }

                                fclose($file);
                            };

                            return response()->stream($callback, 200, $headers);
                        }),
                    // Add PDF Export Bulk Action
                    BulkAction::make('export-pdf')
                        ->label('Export to PDF')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('danger')
                        ->action(function (Collection $records) {
                            // Check if any records are selected
                            if ($records->isEmpty()) {
                                Notification::make()->title('No death records to export')->danger()->send();
                                return;
                            }

                            // Generate the PDF
                            $pdf = Pdf::loadView('pdf.death-records', [
                                'records' => $records,
                            ]);

                            return response()->streamDownload(function () use ($pdf) {
                                echo $pdf->output();
                            }, 'death-records-' . date('Y-m-d') . '.pdf');
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
                //
            ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDeathRecords::route('/'),
            'create' => Pages\CreateDeathRecord::route('/create'),
            'edit' => Pages\EditDeathRecord::route('/{record}/edit'),
        ];
    }
}
