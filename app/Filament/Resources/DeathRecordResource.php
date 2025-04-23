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

    protected static ?string $navigationLabel = 'Rekod Kematian';

    protected static ?string $navigationGroup = 'Pengurusan Rekod';

    protected static ?string $label = 'Rekod Kematian';
    protected static ?string $pluralLabel = 'Rekod Kematian';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Death Record Type Selector
            Forms\Components\Section::make('Maklumat Rekod Kematian')->schema([
                Forms\Components\Select::make('deceased_type')
                    ->label('Jenis Rekod')
                    ->options([
                        User::class => 'Ahli Utama',
                        Dependent::class => 'Tanggungan',
                    ])
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('deceased_id', null);
                        // Clear member info fields
                        $set('member_no', 'Tiada');
                        $set('member_address', 'Tiada');
                    })
                    ->default(Dependent::class),

                Forms\Components\Select::make('deceased_id')
                    ->label(fn(callable $get) => $get('deceased_type') === User::class ? 'Ahli' : 'Tanggungan')
                    ->options(function (callable $get) {
                        $type = $get('deceased_type');

                        if ($type === User::class) {
                            // Filter out admin users from the list
                            return User::query()
                                ->whereDoesntHave('deathRecord')
                                ->whereDoesntHave('roles', function ($query) {
                                    $query->where('name', 'admin');
                                })
                                ->pluck('name', 'id')
                                ->toArray();
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
                    ->getOptionLabelUsing(function ($value, callable $get) {
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
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $deceasedType = $get('deceased_type');

                        // Reset to default values for all user fields
                        $set('member_no', 'Tiada');
                        $set('member_ic', 'Tiada');
                        $set('member_name', 'Tiada');
                        $set('member_email', 'Tiada');
                        $set('member_phone', 'Tiada');
                        $set('member_home_phone', 'Tiada');
                        $set('member_age', 'Tiada');
                        $set('member_residence_status', 'Tiada');
                        $set('member_address', 'Tiada');

                        if ($state && $deceasedType) {
                            if ($deceasedType === User::class) {
                                $user = User::find($state);
                                if ($user) {
                                    $set('member_no', $user->No_Ahli ?? 'Tiada');
                                    $set('member_ic', $user->ic_number ?? 'Tiada');
                                    $set('member_name', $user->name ?? 'Tiada');
                                    $set('member_email', $user->email ?? 'Tiada');
                                    $set('member_phone', $user->phone_number ?? 'Tiada');
                                    $set('member_home_phone', $user->home_phone ?? 'Tiada');
                                    $set('member_age', $user->age ?? 'Tiada');
                                    $set('member_residence_status', $user->residence_status ?? 'Tiada');
                                    $set('member_address', $user->address ?? 'Tiada');
                                }
                            } elseif ($deceasedType === Dependent::class) {
                                $dependent = Dependent::find($state);
                                if ($dependent) {
                                    // Set dependent's own information
                                    $set('member_ic', $dependent->ic_number ?? 'Tiada');
                                    $set('member_name', $dependent->full_name ?? 'Tiada');
                                    $set('member_age', $dependent->age ?? 'Tiada');

                                    // Set information from parent user
                                    if ($dependent->user) {
                                        $set('member_no', $dependent->user->No_Ahli ?? 'Tiada');
                                        $set('member_email', $dependent->user->email ?? 'Tiada');
                                        $set('member_phone', $dependent->user->phone_number ?? 'Tiada');
                                        $set('member_home_phone', $dependent->user->home_phone ?? 'Tiada');
                                        $set('member_residence_status', $dependent->user->residence_status ?? 'Tiada');
                                        $set('member_address', $dependent->user->address ?? 'Tiada');
                                    }
                                }
                            }
                        }
                    }),
            ]),

            // Member Information Section
            Forms\Components\Section::make('Maklumat Ahli')
                ->schema([
                    // No Ahli
                    Forms\Components\TextInput::make('member_no')
                        ->label('No Ahli')
                        ->formatStateUsing(function ($state, $record, callable $get) {
                            // For edit form with existing record
                            if ($record) {
                                if ($record->deceased_type === 'App\\Models\\User' && $record->deceased) {
                                    return $record->deceased->No_Ahli ?? 'Tiada';
                                }

                                if ($record->deceased_type === 'App\\Models\\Dependent' && $record->deceased) {
                                    return $record->deceased->user->No_Ahli ?? 'Tiada';
                                }
                                return 'Tiada';
                            }

                            // For create form
                            $deceasedType = $get('deceased_type');
                            $deceasedId = $get('deceased_id');

                            if (!$deceasedId) {
                                return 'Tiada';
                            }

                            if ($deceasedType === User::class) {
                                $user = User::find($deceasedId);
                                return $user ? ($user->No_Ahli ?? 'Tiada') : 'Tiada';
                            }

                            if ($deceasedType === Dependent::class) {
                                $dependent = Dependent::find($deceasedId);
                                return ($dependent && $dependent->user) ? ($dependent->user->No_Ahli ?? 'Tiada') : 'Tiada';
                            }

                            return $state ?? 'Tiada';
                        })
                        ->disabled()
                        ->dehydrated(false),

                    // IC Number
                    Forms\Components\TextInput::make('member_ic')
                        ->label('No KP')
                        ->formatStateUsing(function ($state, $record, callable $get) {
                            // For edit form with existing record
                            if ($record) {
                                if ($record->deceased_type === 'App\\Models\\User' && $record->deceased) {
                                    return $record->deceased->ic_number ?? 'Tiada';
                                }

                                if ($record->deceased_type === 'App\\Models\\Dependent' && $record->deceased) {
                                    return $record->deceased->ic_number ?? 'Tiada';
                                }
                                return 'Tiada';
                            }

                            // For create form
                            $deceasedType = $get('deceased_type');
                            $deceasedId = $get('deceased_id');

                            if (!$deceasedId) {
                                return 'Tiada';
                            }

                            if ($deceasedType === User::class) {
                                $user = User::find($deceasedId);
                                return $user ? ($user->ic_number ?? 'Tiada') : 'Tiada';
                            }

                            if ($deceasedType === Dependent::class) {
                                $dependent = Dependent::find($deceasedId);
                                return $dependent ? ($dependent->ic_number ?? 'Tiada') : 'Tiada';
                            }

                            return 'Tiada';
                        })
                        ->disabled()
                        ->dehydrated(false),

                    // Name
                    Forms\Components\TextInput::make('member_name')
                        ->label('Nama')
                        ->formatStateUsing(function ($state, $record, callable $get) {
                            // For edit form with existing record
                            if ($record) {
                                if ($record->deceased_type === 'App\\Models\\User' && $record->deceased) {
                                    return $record->deceased->name ?? 'Tiada';
                                }

                                if ($record->deceased_type === 'App\\Models\\Dependent' && $record->deceased) {
                                    return $record->deceased->full_name ?? 'Tiada';
                                }
                                return 'Tiada';
                            }

                            // For create form
                            $deceasedType = $get('deceased_type');
                            $deceasedId = $get('deceased_id');

                            if (!$deceasedId) {
                                return 'Tiada';
                            }

                            if ($deceasedType === User::class) {
                                $user = User::find($deceasedId);
                                return $user ? ($user->name ?? 'Tiada') : 'Tiada';
                            }

                            if ($deceasedType === Dependent::class) {
                                $dependent = Dependent::find($deceasedId);
                                return $dependent ? ($dependent->full_name ?? 'Tiada') : 'Tiada';
                            }

                            return 'Tiada';
                        })
                        ->disabled()
                        ->dehydrated(false),

                    // Email
                    Forms\Components\TextInput::make('member_email')
                        ->label('Email')
                        ->formatStateUsing(function ($state, $record, callable $get) {
                            // For edit form with existing record
                            if ($record) {
                                if ($record->deceased_type === 'App\\Models\\User' && $record->deceased) {
                                    return $record->deceased->email ?? 'Tiada';
                                }

                                if ($record->deceased_type === 'App\\Models\\Dependent' && $record->deceased) {
                                    return $record->deceased->user->email ?? 'Tiada';
                                }
                                return 'Tiada';
                            }

                            // For create form
                            $deceasedType = $get('deceased_type');
                            $deceasedId = $get('deceased_id');

                            if (!$deceasedId) {
                                return 'Tiada';
                            }

                            if ($deceasedType === User::class) {
                                $user = User::find($deceasedId);
                                return $user ? ($user->email ?? 'Tiada') : 'Tiada';
                            }

                            if ($deceasedType === Dependent::class) {
                                $dependent = Dependent::find($deceasedId);
                                return ($dependent && $dependent->user) ? ($dependent->user->email ?? 'Tiada') : 'Tiada';
                            }

                            return 'Tiada';
                        })
                        ->disabled()
                        ->dehydrated(false),

                    // Phone Number
                    Forms\Components\TextInput::make('member_phone')
                        ->label('No Telefon')
                        ->formatStateUsing(function ($state, $record, callable $get) {
                            // For edit form with existing record
                            if ($record) {
                                if ($record->deceased_type === 'App\\Models\\User' && $record->deceased) {
                                    return $record->deceased->phone_number ?? 'Tiada';
                                }

                                if ($record->deceased_type === 'App\\Models\\Dependent' && $record->deceased) {
                                    return $record->deceased->user->phone_number ?? 'Tiada';
                                }
                                return 'Tiada';
                            }

                            // For create form
                            $deceasedType = $get('deceased_type');
                            $deceasedId = $get('deceased_id');

                            if (!$deceasedId) {
                                return 'Tiada';
                            }

                            if ($deceasedType === User::class) {
                                $user = User::find($deceasedId);
                                return $user ? ($user->phone_number ?? 'Tiada') : 'Tiada';
                            }

                            if ($deceasedType === Dependent::class) {
                                $dependent = Dependent::find($deceasedId);
                                return ($dependent && $dependent->user) ? ($dependent->user->phone_number ?? 'Tiada') : 'Tiada';
                            }

                            return 'Tiada';
                        })
                        ->disabled()
                        ->dehydrated(false),

                    // Home Phone
                    Forms\Components\TextInput::make('member_home_phone')
                        ->label('No Telefon Rumah')
                        ->formatStateUsing(function ($state, $record, callable $get) {
                            // For edit form with existing record
                            if ($record) {
                                if ($record->deceased_type === 'App\\Models\\User' && $record->deceased) {
                                    return $record->deceased->home_phone ?? 'Tiada';
                                }

                                if ($record->deceased_type === 'App\\Models\\Dependent' && $record->deceased) {
                                    return $record->deceased->user->home_phone ?? 'Tiada';
                                }
                                return 'Tiada';
                            }

                            // For create form
                            $deceasedType = $get('deceased_type');
                            $deceasedId = $get('deceased_id');

                            if (!$deceasedId) {
                                return 'Tiada';
                            }

                            if ($deceasedType === User::class) {
                                $user = User::find($deceasedId);
                                return $user ? ($user->home_phone ?? 'Tiada') : 'Tiada';
                            }

                            if ($deceasedType === Dependent::class) {
                                $dependent = Dependent::find($deceasedId);
                                return ($dependent && $dependent->user) ? ($dependent->user->home_phone ?? 'Tiada') : 'Tiada';
                            }

                            return 'Tiada';
                        })
                        ->disabled()
                        ->dehydrated(false),

                    // Age
                    Forms\Components\TextInput::make('member_age')
                        ->label('Umur')
                        ->formatStateUsing(function ($state, $record, callable $get) {
                            // For edit form with existing record
                            if ($record) {
                                if ($record->deceased_type === 'App\\Models\\User' && $record->deceased) {
                                    return $record->deceased->age ?? 'Tiada';
                                }

                                if ($record->deceased_type === 'App\\Models\\Dependent' && $record->deceased) {
                                    return $record->deceased->age ?? 'Tiada';
                                }
                                return 'Tiada';
                            }

                            // For create form
                            $deceasedType = $get('deceased_type');
                            $deceasedId = $get('deceased_id');

                            if (!$deceasedId) {
                                return 'Tiada';
                            }

                            if ($deceasedType === User::class) {
                                $user = User::find($deceasedId);
                                return $user ? ($user->age ?? 'Tiada') : 'Tiada';
                            }

                            if ($deceasedType === Dependent::class) {
                                $dependent = Dependent::find($deceasedId);
                                return $dependent ? ($dependent->age ?? 'Tiada') : 'Tiada';
                            }

                            return 'Tiada';
                        })
                        ->disabled()
                        ->dehydrated(false),

                    // Residence Status
                    Forms\Components\TextInput::make('member_residence_status')
                        ->label('Status Kediaman')
                        ->formatStateUsing(function ($state, $record, callable $get) {
                            // For edit form with existing record
                            if ($record) {
                                if ($record->deceased_type === 'App\\Models\\User' && $record->deceased) {
                                    return $record->deceased->residence_status ?? 'Tiada';
                                }

                                if ($record->deceased_type === 'App\\Models\\Dependent' && $record->deceased) {
                                    return $record->deceased->user->residence_status ?? 'Tiada';
                                }
                                return 'Tiada';
                            }

                            // For create form
                            $deceasedType = $get('deceased_type');
                            $deceasedId = $get('deceased_id');

                            if (!$deceasedId) {
                                return 'Tiada';
                            }

                            if ($deceasedType === User::class) {
                                $user = User::find($deceasedId);
                                return $user ? ($user->residence_status ?? 'Tiada') : 'Tiada';
                            }

                            if ($deceasedType === Dependent::class) {
                                $dependent = Dependent::find($deceasedId);
                                return ($dependent && $dependent->user) ? ($dependent->user->residence_status ?? 'Tiada') : 'Tiada';
                            }

                            return 'Tiada';
                        })
                        ->disabled()
                        ->dehydrated(false),

                    // Address
                    Forms\Components\Textarea::make('member_address')
                        ->label('Alamat')
                        ->formatStateUsing(function ($state, $record, callable $get) {
                            // For edit form with existing record
                            if ($record) {
                                // If this is a User (Primary Member)
                                if ($record->deceased_type === 'App\\Models\\User' || $record->deceased_type === User::class) {
                                    $user = User::find($record->deceased_id);
                                    return $user ? $user->address : 'Tiada';
                                }

                                // If this is a Dependent
                                if ($record->deceased_type === 'App\\Models\\Dependent' || $record->deceased_type === Dependent::class) {
                                    $dependent = Dependent::find($record->deceased_id);
                                    if ($dependent && $dependent->user) {
                                        return $dependent->user->address ?? 'Tiada';
                                    }
                                }

                                return 'Tiada';
                            }

                            // For create form
                            $deceasedType = $get('deceased_type');
                            $deceasedId = $get('deceased_id');

                            if (!$deceasedId) {
                                return 'Tiada';
                            }

                            if ($deceasedType === User::class) {
                                $user = User::find($deceasedId);
                                return $user ? ($user->address ?? 'Tiada') : 'Tiada';
                            }

                            if ($deceasedType === Dependent::class) {
                                $dependent = Dependent::find($deceasedId);
                                return ($dependent && $dependent->user) ? ($dependent->user->address ?? 'Tiada') : 'Tiada';
                            }

                            return $state ?? 'Tiada';
                        })
                        ->disabled()
                        ->dehydrated(false)
                        ->rows(3)
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->collapsible(),

            // Death Information
            Forms\Components\DatePicker::make('date_of_death')->required()->label('Tarikh Kematian'),

            Forms\Components\TimePicker::make('time_of_death')->label('Masa Kematian')->seconds(false),

            Forms\Components\TextInput::make('place_of_death')->label('Tempat Kematian')->required()->maxLength(255),

            Forms\Components\Textarea::make('cause_of_death')->label('Sebab Kematian')->rows(3)->maxLength(1000),

            Forms\Components\Textarea::make('death_notes')->label('Catatan')->rows(3)->maxLength(1000),

            Forms\Components\Section::make('Sijil Kematian')->schema([
                Forms\Components\FileUpload::make('death_attachment_path')
                    ->label('Sijil Kematian')
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
                    ->label('Jenis')
                    ->formatStateUsing(function ($state) {
                        if ($state === 'App\\Models\\User' || $state === 'AppModelsUser') {
                            return 'Ahli Utama';
                        }
                        return 'Tanggungan';
                    })
                    ->badge()
                    ->color(fn($state) => $state === 'App\\Models\\User' || $state === 'AppModelsUser' ? 'danger' : 'warning'),

                // Name column
                Tables\Columns\TextColumn::make('deceased_name')
                    ->searchable()
                    ->sortable()
                    ->label('Nama')
                    ->getStateUsing(function ($record) {
                        // Safe access to record relationships
                        try {
                            // Check for string variants of class names
                            $isUser = $record->deceased_type === '\\App\\Models\\User' || $record->deceased_type === 'App\\Models\\User' || strpos($record->deceased_type, 'User') !== false;

                            $isDependent = $record->deceased_type === '\\App\\Models\\Dependent' || $record->deceased_type === 'App\\Models\\Dependent' || strpos($record->deceased_type, 'Dependent') !== false;

                            if ($isUser && $record->deceased) {
                                return $record->deceased->name ?? 'Ahli Tidak Diketahui';
                            }

                            if ($isDependent && $record->deceased) {
                                return $record->deceased->full_name ?? 'Tanggungan Tidak Diketahui';
                            }
                        } catch (\Exception $e) {
                            // Log error for debugging but don't crash the page
                            \Illuminate\Support\Facades\Log::error("Error getting name for death record {$record->id}: " . $e->getMessage());
                        }

                        return 'Tidak Diketahui';
                    }),

                // No Ahli column
                Tables\Columns\TextColumn::make('member_no')
                ->label('No Ahli')
                ->searchable()
                ->sortable(),
                // IC Number column
                Tables\Columns\TextColumn::make('deceased_ic_number')
                    ->searchable()
                    ->label('No KP')
                    ->getStateUsing(function ($record) {
                        try {
                            // Check for string variants of class names
                            $isUser = $record->deceased_type === '\\App\\Models\\User' || $record->deceased_type === 'App\\Models\\User' || strpos($record->deceased_type, 'User') !== false;

                            $isDependent = $record->deceased_type === '\\App\\Models\\Dependent' || $record->deceased_type === 'App\\Models\\Dependent' || strpos($record->deceased_type, 'Dependent') !== false;

                            if ($isUser && $record->deceased) {
                                return $record->deceased->ic_number ?? 'Tiada';
                            }

                            if ($isDependent && $record->deceased) {
                                return $record->deceased->ic_number ?? 'Tiada';
                            }
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error("Error getting IC for death record {$record->id}: " . $e->getMessage());
                        }

                        return 'Tiada';
                    }),

                Tables\Columns\TextColumn::make('date_of_death')->date()->sortable()->label('Tarikh Kematian'),

                Tables\Columns\TextColumn::make('place_of_death')->searchable()->limit(30)->label('Tempat Kematian'),

                Tables\Columns\IconColumn::make('death_attachment_path')->boolean()->label('Sijil')->trueIcon('heroicon-o-document')->falseIcon('heroicon-o-x-mark')->getStateUsing(fn(DeathRecord $record) => $record->death_attachment_path !== null),

                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->label('Tarikh Cipta')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('deceased_type')
                    ->label('Jenis Rekod')
                    ->options([
                        User::class => 'Ahli Utama',
                        Dependent::class => 'Tanggungan',
                    ]),
            ])
            ->actions([

                Tables\Actions\Action::make('viewCertificate')->label('Lihat Sijil')->icon('heroicon-o-eye')->color('info')->visible(fn($record) => $record->death_attachment_path)->url(fn($record) => $record->death_attachment_path ? Storage::url($record->death_attachment_path) : null, true),

                Tables\Actions\EditAction::make()->label('edit'),

                Tables\Actions\DeleteAction::make()->label('Padam'),


            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Padam Terpilih'),

                    // Add CSV Export Bulk Action
                    BulkAction::make('export-csv')
                        ->label('Eksport CSV')
                        ->color('success')
                        ->icon('heroicon-o-document-arrow-down')
                        ->action(function () {
                            // Get all death records with eager loading for relationships
                            $records = DeathRecord::with(['deceased'])->get();

                            // Check if any records exist
                            if ($records->isEmpty()) {
                                Notification::make()->title('Tiada rekod kematian untuk dieksport')->danger()->send();
                                return;
                            }

                            // Generate CSV file
                            $csvFileName = 'rekod-kematian-' . date('Y-m-d') . '.csv';
                            $headers = [
                                'Content-Type' => 'text/csv',
                                'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
                            ];

                            $callback = function () use ($records) {
                                $file = fopen('php://output', 'w');

                                // Add headers
                                fputcsv($file, ['Jenis Rekod', 'Nama', 'No Ahli', 'No KP', 'Tarikh Kematian', 'Masa Kematian', 'Tempat Kematian', 'Sebab Kematian', 'Catatan', 'Sijil Ada', 'Tarikh Cipta']);

                                // Add rows
                                foreach ($records as $record) {
                                    // Determine record type
                                    $recordType = 'Tanggungan';
                                    $name = 'Tiada';
                                    $noAhli = 'Tiada';
                                    $icNumber = 'Tiada';

                                    // Handle Primary Member (User)
                                    if ($record->deceased_type === 'App\\Models\\User' || strpos($record->deceased_type, 'User') !== false) {
                                        $recordType = 'Ahli Utama';

                                        // Try to get user directly if relationship isn't loaded
                                        $user = $record->deceased;
                                        if (!$user && $record->deceased_id) {
                                            $user = User::find($record->deceased_id);
                                        }

                                        if ($user) {
                                            $name = $user->name ?? 'Tidak Diketahui';
                                            $noAhli = $user->No_Ahli ?? 'Tiada';
                                            $icNumber = $user->ic_number ?? 'Tiada';
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
                                            $name = $dependent->full_name ?? 'Tidak Diketahui';
                                            $icNumber = $dependent->ic_number ?? 'Tiada';

                                            // Get No_Ahli from the User related to this dependent
                                            if ($dependent->user) {
                                                $noAhli = $dependent->user->No_Ahli ?? 'Tiada';
                                            }
                                        }
                                    }

                                    fputcsv($file, [$recordType, $name, $noAhli, $icNumber, $record->date_of_death ? $record->date_of_death->format('Y-m-d') : 'Tiada', $record->time_of_death ? $record->time_of_death->format('H:i') : 'Tiada', $record->place_of_death ?? 'Tiada', $record->cause_of_death ?? 'Tiada', $record->death_notes ?? 'Tiada', $record->death_attachment_path ? 'Ya' : 'Tidak', $record->created_at->format('Y-m-d')]);
                                }

                                fclose($file);
                            };

                            return response()->stream($callback, 200, $headers);
                        }),
                    // Add PDF Export Bulk Action
                    BulkAction::make('export-pdf')
                        ->label('Eksport ke PDF')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('danger')
                        ->action(function (Collection $records) {
                            // Check if any records are selected
                            if ($records->isEmpty()) {
                                Notification::make()->title('Tiada rekod kematian untuk dieksport')->danger()->send();
                                return;
                            }

                            // Generate the PDF
                            $pdf = Pdf::loadView('pdf.death-records', [
                                'records' => $records,
                            ]);

                            return response()->streamDownload(function () use ($pdf) {
                                echo $pdf->output();
                            }, 'rekod-kematian-' . date('Y-m-d') . '.pdf');
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
