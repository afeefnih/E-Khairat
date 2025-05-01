<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Barryvdh\DomPDF\Facade\Pdf;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = 'Pengurusan Keahlian';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Ahli';

    protected static ?string $pluralModelLabel = 'Ahli Khairat Masjid Taman Sutera';

    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Maklumat Asas')
                ->schema([
                    Forms\Components\Select::make('roles')
                        ->relationship('roles', 'name')
                        ->preload()
                        ->label('Peranan')
                        ->required()
                        ->live() // Make this field live to trigger other field requirements
                        ->afterStateUpdated(fn(Forms\Set $set) => $set('password', null)), // Reset password when role changes

                    Forms\Components\TextInput::make('name')
                        ->label('Nama')
                        ->required()
                        ->maxLength(255)
                        ->validationMessages([
                            'required' => 'Nama diperlukan.',
                            'max' => 'Nama tidak boleh melebihi 255 aksara.',
                        ]),

                    Forms\Components\TextInput::make('email')
                        ->label('Emel')
                        ->email()
                        ->maxLength(255)
                        ->unique(User::class, 'email', ignoreRecord: true)
                        ->validationMessages([
                            'email' => 'Emel mesti alamat emel yang sah.',
                            'unique' => 'Emel telah digunakan.',
                        ])
                        ->required(fn(Forms\Get $get) => $get('roles') === '1'), // Required for admin

                    Forms\Components\TextInput::make('ic_number')
                        ->required()
                        ->label('Nombor IC')
                        ->unique(User::class, 'ic_number', ignoreRecord: true)
                        ->minLength(12)
                        ->maxLength(12)
                        ->rule('digits:12')
                        ->numeric()
                        ->mask('999999999999') // Added mask to restrict input length
                        ->helperText('Nombor IC mesti 12 digit. Contoh: 92010112345678
')
                        ->live() // Keep live() for immediate age calculation
                        ->validationMessages([
                            'required' => 'Nombor IC diperlukan.',
                            'digits' => 'Nombor IC mestilah 12 digit.',
                            'min' => 'Nombor IC mestilah 12 digit.',
                            'max' => 'Nombor IC mestilah 12 digit.',
                            'numeric' => 'Nombor IC mesti berupa angka.',
                            'unique' => 'Nombor IC telah digunakan.',
                        ])
                        ->afterStateUpdated(function ($state, callable $set) {
                            if (preg_match('/^\\d{12}$/', $state)) {
                                $year = substr($state, 0, 2);
                                $currentYear = (int) date('y');
                                $birthYear = (int) $year + ((int) $year > $currentYear ? 1900 : 2000);
                                $age = (int) date('Y') - $birthYear;
                                $set('age', $age);
                            } else {
                                $set('age', null);
                            }
                        }),
                ])
                ->columns(2),

            Forms\Components\Section::make('Maklumat Keahlian')
                ->schema([
                    Forms\Components\TextInput::make('phone_number')
                        ->label('Nombor Telefon')
                        ->tel()
                        ->required()
                        ->numeric()
                        ->minLength(10)
                        ->maxLength(15)
                        ->validationMessages([
                            'required' => 'Nombor telefon diperlukan.',
                            'numeric' => 'Nombor telefon mesti berupa angka.',
                            'min_digits' => 'Nombor telefon mesti sekurang-kurangnya 10 digit.',
                            'max_digits' => 'Nombor telefon tidak boleh melebihi 15 digit.',
                        ]),
                    Forms\Components\TextInput::make('home_phone')
                        ->label('Telefon Rumah')
                        ->tel()
                        ->numeric()
                        ->minLength(10)
                        ->maxLength(15)
                        ->required(function (Forms\Get $get, $record) {
                            // Check the selected role in the form OR the existing role for the record
                            return $get('roles') !== '1' && !($record && $record->hasRole('admin'));
                        })
                        ->hidden(function (Forms\Get $get, $record) {
                            // Check the selected role in the form OR the existing role for the record
                            return $get('roles') === '1' || ($record && $record->hasRole('admin'));
                        })
                        ->validationMessages([
                            'required' => 'Nombor telefon rumah diperlukan.',
                            'numeric' => 'Nombor telefon rumah mesti berupa angka.',
                            'min_digits' => 'Nombor telefon rumah mesti sekurang-kurangnya 10 digit.',
                            'max_digits' => 'Nombor telefon rumah tidak boleh melebihi 15 digit.',
                        ]),

                    Forms\Components\Textarea::make('address')
                        ->label('Alamat')
                        ->required(function (Forms\Get $get, $record) {
                            // Check the selected role in the form OR the existing role for the record
                            return $get('roles') !== '1' && !($record && $record->hasRole('admin'));
                        })
                        ->hidden(function (Forms\Get $get, $record) {
                            // Check the selected role in the form OR the existing role for the record
                            return $get('roles') === '1' || ($record && $record->hasRole('admin'));
                        })
                        ->string()
                        ->maxLength(255)
                        ->validationMessages([
                            'required' => 'Alamat diperlukan.',
                            'string' => 'Alamat mesti berupa teks.',
                            'max' => 'Alamat tidak boleh melebihi 255 aksara.',
                        ]),

                    Forms\Components\TextInput::make('age')
                        ->label('Umur')
                        ->disabled()
                        ->live() // Keep live() for immediate display update
                        ->dehydrated(true)
                        ->required(function (Forms\Get $get, $record) {
                            // Check the selected role in the form OR the existing role for the record
                            return $get('roles') !== '1' && !($record && $record->hasRole('admin'));
                        })
                        ->hidden(function (Forms\Get $get, $record) {
                            // Check the selected role in the form OR the existing role for the record
                            return $get('roles') === '1' || ($record && $record->hasRole('admin'));
                        })
                        ->numeric()
                        ->minValue(18)
                        ->validationMessages([
                            'required' => 'Umur diperlukan.',
                            'integer' => 'Umur mesti berupa angka.',
                            'min' => 'Umur mesti sekurang-kurangnya 18 tahun.',
                            'numeric' => 'Umur mesti berupa angka.',
                        ]),

                    Forms\Components\Select::make('residence_status')
                        ->label('Status Kediaman')
                        ->required(function (Forms\Get $get, $record) {
                            // Check the selected role in the form OR the existing role for the record
                            return $get('roles') !== '1' && !($record && $record->hasRole('admin'));
                        })
                        ->hidden(function (Forms\Get $get, $record) {
                            // Check the selected role in the form OR the existing role for the record
                            return $get('roles') === '1' || ($record && $record->hasRole('admin'));
                        })
                        ->options([
                            'kekal' => 'Kekal',
                            'sewa' => 'Sewa',
                        ])
                        ->validationMessages([
                            'required' => 'Status kediaman diperlukan.',
                            'in' => 'Status kediaman mesti salah satu daripada: kekal, sewa.',
                        ]),
                ])
                ->columns(2),

            Forms\Components\Section::make('Kata Laluan')
                ->schema([
                    Forms\Components\TextInput::make('password')
                        ->label('Kata Laluan')
                        ->password()
                        ->required(false)
                        ->minLength(8)

                        ->helperText('jika kata laluan tidak diisi, nombor IC akan digunakan sebagai kata laluan.')
                        ->revealable()
                        ->dehydrateStateUsing(function ($state) {
                            // If password is provided, hash it
                            if (filled($state)) {
                                return Hash::make($state);
                            }

                            // Return null if no password is provided
                            return null;
                        })
                        ->dehydrated(fn(?string $state): bool => filled($state))
                        ->validationMessages([
                            'min' => 'Kata laluan mesti sekurang-kurangnya 8 aksara.',
                        ]),

                    Forms\Components\TextInput::make('password_confirmation')
                        ->label('Pengesahan Kata Laluan')
                        ->password()
                        ->required(fn(Forms\Get $get) => filled($get('password'))) // Ensure confirmation is required only if password is filled
                        ->minLength(8)
                        ->revealable()
                        ->dehydrated(false)
                        ->same('password') // Ensures password confirmation matches the password
                        ->validationMessages([
                            'min' => 'Pengesahan kata laluan mesti sekurang-kurangnya 8 aksara.',
                            'same' => 'Pengesahan kata laluan tidak sepadan.',
                        ]),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('isDeceased')->label('Status Kematian')->boolean()->trueIcon('heroicon-s-x-circle')->falseIcon('heroicon-s-check-circle')->trueColor('danger')->falseColor('success')->getStateUsing(fn(User $record) => $record->isDeceased())->tooltip(fn(User $record) => $record->isDeceased() ? 'Ahli ini telah meninggal' : 'Ahli ini masih hidup'),

                Tables\Columns\TextColumn::make('No_Ahli')->label('No. Ahli')->searchable()->sortable(),

                Tables\Columns\TextColumn::make('name')->label('Nama')->searchable(),

                Tables\Columns\TextColumn::make('email')->label('Emel')->searchable(),

                Tables\Columns\TextColumn::make('ic_number')->label('Nombor IC')->searchable(),

                Tables\Columns\TextColumn::make('phone_number')->label('Nombor Telefon')->searchable(),

                Tables\Columns\TextColumn::make('residence_status')->label('Status Kediaman'),

                Tables\Columns\TextColumn::make('roles.name')->badge()->label('Peranan'),

                Tables\Columns\TextColumn::make('dependents_count')->label('Tanggungan')->counts('dependents')->badge(),

                Tables\Columns\TextColumn::make('created_at')->label('Tarikh Cipta')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([Tables\Filters\SelectFilter::make('roles')->label('Peranan')->relationship('roles', 'name')->preload()->multiple()])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->hidden(fn($record) => $record->No_Ahli === 'ADM-0001'), // Hide delete for ADM-0001
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Add CSV Export Bulk Action
                    BulkAction::make('export-csv')
                        ->label('Eksport ke CSV')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->action(function (Collection $records) {
                            // Exclude users with the 'admin' role
                            $filteredUsers = $records->filter(function ($user) {
                                return !$user->hasRole('admin');
                            });

                            // Check if any users remain after filtering
                            if ($filteredUsers->isEmpty()) {
                                Notification::make()->title('Tiada ahli untuk dieksport')->danger()->send();
                                return;
                            }

                            // Generate CSV file
                            $csvFileName = 'ahli-' . date('Y-m-d') . '.csv';
                            $headers = [
                                'Content-Type' => 'text/csv',
                                'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
                            ];

                            $callback = function () use ($filteredUsers) {
                                $file = fopen('php://output', 'w');

                                // Add headers
                                fputcsv($file, ['No. Ahli', 'Nama', 'Emel', 'Nombor IC', 'Umur', 'Telefon Rumah', 'Nombor Telefon', 'Alamat', 'Status Kediaman', 'Jumlah Tanggungan', 'Tarikh Cipta']);

                                // Add rows
                                foreach ($filteredUsers as $user) {
                                    fputcsv($file, [$user->No_Ahli ?? 'Tiada', $user->name, $user->email ?? 'Tiada', $user->ic_number, $user->age, $user->home_phone, $user->phone_number, $user->address, $user->residence_status, $user->dependents_count, $user->created_at]);
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
                            // Exclude users with the 'admin' role
                            $filteredUsers = $records->filter(function ($user) {
                                return !$user->hasRole('admin');
                            });

                            // Check if any users remain after filtering
                            if ($filteredUsers->isEmpty()) {
                                Notification::make()->title('Tiada ahli untuk dieksport')->danger()->send();
                                return;
                            }

                            try {
                                $pdf = Pdf::loadView('pdf.users', [
                                    'users' => $filteredUsers,
                                ])->setPaper('A4', 'landscape'); // Landscape paper orientation

                                // Stream the PDF download response
                                return response()->streamDownload(function () use ($pdf) {
                                    echo $pdf->output();
                                }, 'ahli-' . date('Y-m-d') . '.pdf');
                            } catch (\Exception $e) {
                                // Handle errors and show a notification if PDF generation fails
                                Notification::make()->title('Ralat menjana PDF')->danger()->body($e->getMessage())->send();
                            }
                        }),

                    BulkAction::make('delete')
                        ->label('Padam')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            // Separate admin users and other users
                            $adminUsers = $records->filter(fn($user) => $user->hasRole('admin'));
                            $nonAdminUsers = $records->reject(fn($user) => $user->hasRole('admin'));

                            // If any admin users are selected, show a notification and do not proceed
                            if ($adminUsers->isNotEmpty()) {
                                Notification::make()
                                    ->title('Ahli dengan peranan admin tidak boleh dipadam')
                                    ->warning()
                                    ->send();
                                return;
                            }

                            // Delete the non-admin users
                            $nonAdminUsers->each->delete();

                            // Notify the user of successful deletion
                            Notification::make()
                                ->title('Ahli berjaya dipadam')
                                ->success()
                                ->send();
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [RelationManagers\DependentsRelationManager::class, RelationManagers\PaymentRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
