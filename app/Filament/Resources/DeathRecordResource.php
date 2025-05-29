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
use Illuminate\Database\Eloquent\Model;
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

    protected static ?string $navigationGroup = 'Pengurusan Kematian';

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
                        'non_member' => 'Bukan Ahli',
                    ])
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('deceased_id', null);
                        $set('member_no', 'Tiada');
                        $set('member_address', 'Tiada');
                        // Clear non-member fields
                        $set('non_member_name', null);
                        $set('non_member_ic_number', null);
                        $set('non_member_age', null);
                        $set('non_member_relationship', null);
                    })
                    ->default(Dependent::class),

                Forms\Components\Select::make('deceased_id')
                    ->label(fn(callable $get) => $get('deceased_type') === User::class ? 'Ahli' : ($get('deceased_type') === Dependent::class ? 'Tanggungan' : ''))
                    ->options(function (callable $get) {
                        $type = $get('deceased_type');
                        if ($type === User::class) {
                            return User::query()
                                ->whereDoesntHave('deathRecord')
                                ->whereDoesntHave('roles', function ($query) {
                                    $query->where('name', 'admin');
                                })
                                ->pluck('name', 'id')
                                ->toArray();
                        }
                        if ($type === Dependent::class) {
                            $dependents = Dependent::query()
                                ->select(['dependent_id', 'full_name'])
                                ->whereDoesntHave('deathRecord')
                                ->get();
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
                    ->required(fn(callable $get) => $get('deceased_type') !== 'non_member')
                    ->visible(fn(callable $get) => $get('deceased_type') !== 'non_member')
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $deceasedType = $get('deceased_type');
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
                            if ($deceasedType === \App\Models\User::class) {
                                $user = \App\Models\User::find($state);
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
                            } elseif ($deceasedType === \App\Models\Dependent::class) {
                                $dependent = \App\Models\Dependent::find($state);
                                if ($dependent) {
                                    $set('member_no', $dependent->user->No_Ahli ?? 'Tiada');
                                    $set('member_ic', $dependent->ic_number ?? 'Tiada');
                                    $set('member_name', $dependent->full_name ?? 'Tiada');
                                    $set('member_email', $dependent->user->email ?? 'Tiada');
                                    $set('member_phone', $dependent->user->phone_number ?? 'Tiada');
                                    $set('member_home_phone', $dependent->user->home_phone ?? 'Tiada');
                                    $set('member_age', $dependent->age ?? 'Tiada');
                                    $set('member_residence_status', $dependent->user->residence_status ?? 'Tiada');
                                    $set('member_address', $dependent->user->address ?? 'Tiada');
                                }
                            }
                            // Calculate death cost based on age
                            if ($deceasedType === \App\Models\User::class && isset($user) && is_numeric($user->age)) {
                                $age = (int) $user->age;
                                if ($age <= 3) {
                                    $set('age_category', 'Janin - 3 tahun');
                                    $set('calculated_amount', '450');
                                    $set('final_amount', '450');
                                } elseif ($age >= 4 && $age <= 6) {
                                    $set('age_category', 'Kanak-kanak (4-6 tahun)');
                                    $set('calculated_amount', '650');
                                    $set('final_amount', '650');
                                } else {
                                    $set('age_category', 'Dewasa');
                                    $set('calculated_amount', '1050');
                                    $set('final_amount', '1050');
                                }
                            } elseif ($deceasedType === \App\Models\Dependent::class && isset($dependent) && is_numeric($dependent->age)) {
                                $age = (int) $dependent->age;
                                if ($age <= 3) {
                                    $set('age_category', 'Janin - 3 tahun');
                                    $set('calculated_amount', '450');
                                    $set('final_amount', '450');
                                } elseif ($age >= 4 && $age <= 6) {
                                    $set('age_category', 'Kanak-kanak (4-6 tahun)');
                                    $set('calculated_amount', '650');
                                    $set('final_amount', '650');
                                } else {
                                    $set('age_category', 'Dewasa');
                                    $set('calculated_amount', '1050');
                                    $set('final_amount', '1050');
                                }
                            }
                        }
                    }),

                // Non-member fields in a card layout
                Forms\Components\Card::make([
                    Forms\Components\TextInput::make('non_member_name')
                        ->label('Nama Si Mati (Bukan Ahli)')
                        ->required(fn(callable $get) => $get('deceased_type') === 'non_member')
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('non_member_ic_number')
                        ->label('No KP Si Mati (Bukan Ahli)')
                        ->live(onBlur: true) // Use onBlur for performance if instant update isn't strictly needed, or keep ->live()
                        ->required(fn(callable $get) => $get('deceased_type') === 'non_member')
                        ->minLength(12)
                        ->maxLength(12)
                        ->rule('digits:12')
                        ->helperText('No KP mesti 12 digit. Contoh: 031114160355')
                        ->validationMessages([
                            'required' => 'Sila masukkan No KP si mati.',
                            'digits' => 'No KP mestilah 12 digit.',
                            'min' => 'No KP mestilah 12 digit.',
                            'max' => 'No KP mestilah 12 digit.'
                        ])
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            if ($get('deceased_type') === 'non_member' && preg_match('/^\d{12}$/', $state)) {
                                $year = substr($state, 0, 2);
                                $currentYear = (int) date('y');
                                $birthYear = (int) $year + ((int) $year > $currentYear ? 1900 : 2000);
                                $age = (int) date('Y') - $birthYear;
                                $set('non_member_age', $age);

                                // --- Start: Calculate and set cost fields directly ---
                                if ($age <= 3) {
                                    $set('age_category', 'Janin - 3 tahun');
                                    $set('calculated_amount', '450');
                                    $calc = 450;
                                } elseif ($age >= 4 && $age <= 6) {
                                    $set('age_category', 'Kanak-kanak (4-6 tahun)');
                                    $set('calculated_amount', '650');
                                    $calc = 650;
                                } else {
                                    $set('age_category', 'Dewasa');
                                    $set('calculated_amount', '1050');
                                    $calc = 1050;
                                }

                                $customAmount = $get('custom_amount') ?? 0; // Get custom amount, default to 0
                                $customValue = is_numeric($customAmount) ? (float) $customAmount : 0;
                                $total = $calc + $customValue;
                                $set('final_amount', (string) $total);
                                // --- End: Calculate and set cost fields directly ---

                            } else {
                                $set('non_member_age', null);
                                // --- Start: Clear cost fields if IC is invalid ---
                                $set('age_category', null); // Use null
                                $set('calculated_amount', null); // Use null
                                $set('final_amount', null); // Use null
                                // --- End: Clear cost fields if IC is invalid ---
                            }
                        }),
                    Forms\Components\TextInput::make('non_member_age')
                        ->label('Umur Si Mati (Bukan Ahli)')
                        ->disabled()
                        ->live() // Keep live to ensure it updates visually
                        ->dehydrated(true) // Keep dehydrated so value is saved
                        ->helperText('Umur akan diisi secara automatik berdasarkan No KP.')
                        ->validationMessages([
                            'required' => 'Umur si mati diperlukan.',
                        ]),
                    Forms\Components\TextInput::make('non_member_relationship')
                        ->label('Hubungan dengan Wakil (Bukan Ahli)')
                        ->required(fn(callable $get) => $get('deceased_type') === 'non_member'),
                ])->visible(fn(callable $get) => $get('deceased_type') === 'non_member')
                ->columns(2),
            ]),

            // Hide Maklumat Ahli section for non-member
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
                                return $user ? $user->No_Ahli ?? 'Tiada' : 'Tiada';
                            }

                            if ($deceasedType === Dependent::class) {
                                $dependent = Dependent::find($deceasedId);
                                return $dependent && $dependent->user ? $dependent->user->No_Ahli ?? 'Tiada' : 'Tiada';
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
                                return $user ? $user->ic_number ?? 'Tiada' : 'Tiada';
                            }

                            if ($deceasedType === Dependent::class) {
                                $dependent = Dependent::find($deceasedId);
                                return $dependent ? $dependent->ic_number ?? 'Tiada' : 'Tiada';
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
                                return $user ? $user->name ?? 'Tiada' : 'Tiada';
                            }

                            if ($deceasedType === Dependent::class) {
                                $dependent = Dependent::find($deceasedId);
                                return $dependent ? $dependent->full_name ?? 'Tiada' : 'Tiada';
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
                                return $user ? $user->email ?? 'Tiada' : 'Tiada';
                            }

                            if ($deceasedType === Dependent::class) {
                                $dependent = Dependent::find($deceasedId);
                                return $dependent && $dependent->user ? $dependent->user->email ?? 'Tiada' : 'Tiada';
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
                                return $user ? $user->phone_number ?? 'Tiada' : 'Tiada';
                            }

                            if ($deceasedType === Dependent::class) {
                                $dependent = Dependent::find($deceasedId);
                                return $dependent && $dependent->user ? $dependent->user->phone_number ?? 'Tiada' : 'Tiada';
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
                                return $user ? $user->home_phone ?? 'Tiada' : 'Tiada';
                            }

                            if ($deceasedType === Dependent::class) {
                                $dependent = Dependent::find($deceasedId);
                                return $dependent && $dependent->user ? $dependent->user->home_phone ?? 'Tiada' : 'Tiada';
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
                                return $user ? $user->age ?? 'Tiada' : 'Tiada';
                            }

                            if ($deceasedType === Dependent::class) {
                                $dependent = Dependent::find($deceasedId);
                                return $dependent ? $dependent->age ?? 'Tiada' : 'Tiada';
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
                                return $user ? $user->residence_status ?? 'Tiada' : 'Tiada';
                            }

                            if ($deceasedType === Dependent::class) {
                                $dependent = Dependent::find($deceasedId);
                                return $dependent && $dependent->user ? $dependent->user->residence_status ?? 'Tiada' : 'Tiada';
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
                                return $user ? $user->address ?? 'Tiada' : 'Tiada';
                            }

                            if ($deceasedType === Dependent::class) {
                                $dependent = Dependent::find($deceasedId);
                                return $dependent && $dependent->user ? $dependent->user->address ?? 'Tiada' : 'Tiada';
                            }

                            return $state ?? 'Tiada';
                        })
                        ->disabled()
                        ->dehydrated(false)
                        ->rows(3)
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->collapsible()
                ->visible(fn(callable $get) => $get('deceased_type') !== 'non_member'),

            // Add this after your death information fields but before the certificate section
            Forms\Components\Section::make('Kos Khairat Kematian')
                ->schema([
                    Forms\Components\Placeholder::make('harga_khairat')
                        ->label('Senarai Kos Khairat Kematian')
                        ->content(function () {
                            return new \Illuminate\Support\HtmlString('
                                <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Janin - 3 tahun</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">RM450</dd>
                                    </div>
                                    <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Kanak-kanak (4-6 tahun)</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">RM650</dd>
                                    </div>
                                    <div class="py-2 sm:grid sm:grid-cols-3 sm:gap-4">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Dewasa</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">RM1050</dd>
                                    </div>
                                </dl>
                            ');
                        }),
                    Forms\Components\TextInput::make('age_category')
                        ->label('Kategori Umur')
                        ->disabled()
                        ->live()
                        ->reactive()
                        ->formatStateUsing(fn($state, $record) => $record ? $record->age_category : $state)
                        ->dehydrated(), // Allow saving

                    Forms\Components\TextInput::make('calculated_amount')
                        ->label('Jumlah Mengikut Kategori')
                        ->disabled()
                        ->prefix('RM')
                        ->live()
                        ->reactive()
                        ->formatStateUsing(fn($state, $record) => $record ? $record->calculated_amount : $state)
                        ->dehydrated(), // Allow saving

                    // Added custom_amount field
                    Forms\Components\TextInput::make('custom_amount')
                       ->label('Jumlah Tambahan')
                       ->numeric()
                       ->prefix('RM')
                       ->live(onBlur: true) // Update on blur is usually sufficient
                       ->reactive()
                       ->afterStateUpdated(function ($state, callable $set, callable $get) {
                           // Recalculate final_amount when custom_amount changes
                           $calculatedAmount = $get('calculated_amount');
                           // Use null coalescing and ensure numeric check
                           $calcValue = ($calculatedAmount !== null && is_numeric($calculatedAmount)) ? (float) $calculatedAmount : 0;
                           $customValue = is_numeric($state) ? (float) $state : 0;
                           $total = $calcValue + $customValue;
                           $set('final_amount', (string) $total);
                       })
                       ->default(0)
                       ->formatStateUsing(fn($state, $record) => $record ? $record->custom_amount : $state)
                       ->dehydrated(), // Ensure it's saved

                    Forms\Components\Textarea::make('custom_amount_notes')
                        ->label('Catatan Jumlah Tambahan')
                        ->rows(3)
                        ->maxLength(1000)
                        ->formatStateUsing(fn($state, $record) => $record ? $record->custom_amount_notes : $state)
                        ->dehydrated() // Ensure it's saved
                        ->visible(fn(callable $get) => !empty($get('custom_amount'))), // Only show if custom_amount has a value

                    Forms\Components\TextInput::make('final_amount')
                        ->label('Jumlah Akhir')
                        ->disabled()
                        ->prefix('RM')
                        ->live()
                        ->reactive()
                        ->formatStateUsing(fn($state, $record) => $record ? $record->final_amount : $state)
                        ->dehydrated(), // Allow saving
                ])
                ->columns(2),

            // Death Information Section
            Forms\Components\Section::make('Maklumat Kematian')->schema([
                Forms\Components\Grid::make()
                    ->schema([Forms\Components\DatePicker::make('date_of_death')->required()->label('Tarikh Kematian'), Forms\Components\TimePicker::make('time_of_death')->label('Masa Kematian')->seconds(false)])
                    ->columns(2),

                Forms\Components\TextInput::make('place_of_death')->label('Tempat Kematian')->required()->maxLength(255),

                Forms\Components\Textarea::make('cause_of_death')->label('Sebab Kematian')->rows(3)->maxLength(1000),

                Forms\Components\Textarea::make('death_notes')->label('Catatan')->rows(3)->maxLength(1000),
            ]),

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
                        if ($state === 'App\\Models\\Dependent' || $state === 'AppModelsDependent') {
                            return 'Tanggungan';
                        }
                        return 'Bukan Ahli';
                    })
                    ->badge()
                    ->color(function($state) {
                        if ($state === 'App\\Models\\User' || $state === 'AppModelsUser') {
                            return 'danger';
                        }
                        if ($state === 'App\\Models\\Dependent' || $state === 'AppModelsDependent') {
                            return 'warning';
                        }
                        return 'success';
                    }),

                // Name column
                Tables\Columns\TextColumn::make('deceased_name')
                    ->label('Nama')
                    ->getStateUsing(function ($record) {
                        if (empty($record->deceased_type) || $record->deceased_type === 'non_member') {
                            return $record->non_member_name ?? 'Tidak Diketahui';
                        }
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
                    ->getStateUsing(function ($record) {
                        if (empty($record->deceased_type) || $record->deceased_type === 'non_member') {
                            return 'Bukan Ahli';
                        }
                        // Safe access to record relationships
                        try {
                            // Check for string variants of class names
                            $isUser = $record->deceased_type === '\\App\\Models\\User' || $record->deceased_type === 'App\\Models\\User' || strpos($record->deceased_type, 'User') !== false;

                            $isDependent = $record->deceased_type === '\\App\\Models\\Dependent' || $record->deceased_type === 'App\\Models\\Dependent' || strpos($record->deceased_type, 'Dependent') !== false;

                            if ($isUser && $record->deceased) {
                                return $record->deceased->No_Ahli ?? 'Tiada';
                            }

                            if ($isDependent && $record->deceased) {
                                return $record->deceased->user->No_Ahli ?? 'Tiada';
                            }
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error("Error getting No Ahli for death record {$record->id}: " . $e->getMessage());
                        }

                        return 'Tiada';
                    }),

                // IC Number column
                Tables\Columns\TextColumn::make('deceased_ic_number')
                    ->label('No KP')
                    ->getStateUsing(function ($record) {
                        if (empty($record->deceased_type) || $record->deceased_type === 'non_member') {
                            return $record->non_member_ic_number ?? 'Tiada';
                        }
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

                Tables\Columns\TextColumn::make('date_of_death')
                    ->date()
                    ->sortable()
                    ->label('Tarikh Kematian'),

                Tables\Columns\TextColumn::make('place_of_death')
                    ->searchable()
                    ->limit(30)
                    ->label('Tempat Kematian'),

                // Add Death Cost column
                Tables\Columns\TextColumn::make('total_cost')
                    ->label('Kos Kematian')
                    ->sortable()
                    ->money('MYR')
                    ->getStateUsing(function ($record) {
                        try {
                            // Use the accessor methods from the model
                            return $record->total_cost;
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error("Error calculating cost for death record {$record->id}: " . $e->getMessage());
                            return 0;
                        }
                    }),

                Tables\Columns\IconColumn::make('death_attachment_path')
                    ->boolean()
                    ->label('Sijil')
                    ->trueIcon('heroicon-o-document')
                    ->falseIcon('heroicon-o-x-mark')
                    ->getStateUsing(fn(DeathRecord $record) => $record->death_attachment_path !== null),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Tarikh Cipta')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('deceased_type')
                    ->label('Jenis Rekod')
                    ->options([
                        User::class => 'Ahli Utama',
                        Dependent::class => 'Tanggungan',
                        'non_member' => 'Bukan Ahli',
                    ]),

                // Add date range filter
                Tables\Filters\Filter::make('date_range')
                    ->label('Julat Tarikh')
                    ->form([
                        Forms\Components\DatePicker::make('from_date')
                            ->label('Dari Tarikh'),
                        Forms\Components\DatePicker::make('to_date')
                            ->label('Hingga Tarikh'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date_of_death', '>=', $date),
                            )
                            ->when(
                                $data['to_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date_of_death', '<=', $date),
                            );
                    }),

                // Add cost range filter
                Tables\Filters\Filter::make('cost_range')
                    ->label('Julat Kos')
                    ->form([
                        Forms\Components\TextInput::make('min_cost')
                            ->label('Kos Minimum (RM)')
                            ->numeric(),
                        Forms\Components\TextInput::make('max_cost')
                            ->label('Kos Maksimum (RM)')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        // Filter by custom amount for now (since total_cost is a calculated attribute)
                        return $query
                            ->when(
                                $data['min_cost'],
                                fn (Builder $query, $min): Builder => $query->where('custom_amount', '>=', $min),
                            )
                            ->when(
                                $data['max_cost'],
                                fn (Builder $query, $max): Builder => $query->where('custom_amount', '<=', $max),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('viewCertificate')
                    ->label('Lihat Sijil')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->visible(fn($record) => $record->death_attachment_path)
                    ->url(fn($record) => $record->death_attachment_path ? Storage::url($record->death_attachment_path) : null, true),

                Tables\Actions\EditAction::make()
                    ->label('edit'),

                Tables\Actions\DeleteAction::make()
                    ->label('Padam'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Padam Terpilih'),

                    // Add CSV Export Bulk Action
                    BulkAction::make('export-csv')
                        ->label('Eksport CSV')
                        ->color('success')
                        ->icon('heroicon-o-document-arrow-down')
                        ->action(function (Collection $records) {
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

                                // Add headers with new cost columns
                                fputcsv($file, [
                                    'Jenis Rekod',
                                    'Nama',
                                    'No Ahli',
                                    'No KP',
                                    'Tarikh Kematian',
                                    'Masa Kematian',
                                    'Tempat Kematian',
                                    'Sebab Kematian',
                                    'Catatan',
                                    'Kos Asas (RM)',
                                    'Jumlah Tambahan (RM)',
                                    'Jumlah Akhir (RM)',
                                    'Catatan Kos',
                                    'Sijil Ada',
                                    'Tarikh Cipta'
                                ]);

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

                                    // Get cost information
                                    $baseCost = $record->base_cost ?? 0;
                                    $customAmount = $record->custom_amount ?? 0;
                                    $totalCost = $record->total_cost ?? 0;
                                    $costNotes = $record->custom_amount_notes ?? 'Tiada';

                                    fputcsv($file, [
                                        $recordType,
                                        $name,
                                        $noAhli,
                                        $icNumber,
                                        $record->date_of_death ? $record->date_of_death->format('Y-m-d') : 'Tiada',
                                        $record->time_of_death ? $record->time_of_death->format('H:i') : 'Tiada',
                                        $record->place_of_death ?? 'Tiada',
                                        $record->cause_of_death ?? 'Tiada',
                                        $record->death_notes ?? 'Tiada',
                                        $baseCost,
                                        $customAmount,
                                        $totalCost,
                                        $costNotes,
                                        $record->death_attachment_path ? 'Ya' : 'Tidak',
                                        $record->created_at->format('Y-m-d')
                                    ]);
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

                            // Add cost information to each record for the PDF view
                            foreach ($records as $record) {
                                // Set cost properties for the PDF view
                                $record->baseCost = $record->base_cost ?? 0;
                                $record->customAmount = $record->custom_amount ?? 0;
                                $record->totalCost = $record->total_cost ?? 0;
                                $record->costNotes = $record->custom_amount_notes ?? 'Tiada';
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
            ])
            ->defaultSort('date_of_death', 'desc'); // Sort by newest deaths first
    }

    public static function getRelations(): array
    {
        return [
                //
            ];
    }

    /**
     * Make sure the form selection reflects the URL parameters when creating a new record
     */
    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return static::getUrl('edit', ['record' => $record]);
    }

    public static function canCreate(): bool
    {
        return true;
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
