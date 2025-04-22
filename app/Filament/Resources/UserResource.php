<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Filament\Resources\DependentResource;
use App\Filament\Resources\PaymentCategoryResource;
use App\Filament\Resources\PaymentResource;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;
use App\Models\Dependent;
use Filament\Actions;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Filament\Tables\Actions\ExportBulkAction;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = 'Ahli';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Users';

    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255)
                ->validationMessages([
                    'required' => 'Nama diperlukan.',
                    'string' => 'Nama mesti berupa teks.',
                    'max' => 'Nama tidak boleh melebihi 255 aksara.',
                ]),

            Forms\Components\TextInput::make('email')
                ->email()
                ->maxLength(255)
                ->unique(User::class, 'email', ignoreRecord: true)
                ->validationMessages([
                    'required' => 'Emel diperlukan.',
                    'email' => 'Emel mesti alamat emel yang sah.',
                    'unique' => 'Emel telah digunakan.',
                ]),

            Forms\Components\TextInput::make('ic_number')
                ->required()
                ->label('Nombor IC')
                ->unique(User::class, 'ic_number', ignoreRecord: true)
                ->length(12)
                ->numeric()
                ->validationMessages([
                    'required' => 'Nombor IC diperlukan.',
                    'digits' => 'Nombor IC mesti 12 digit.',
                    'unique' => 'Nombor IC telah digunakan.',
                    'numeric' => 'Nombor IC mesti berupa angka.',
                ]),

            Forms\Components\TextInput::make('phone_number')
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
                ->tel()
                ->required()
                ->numeric()
                ->minLength(10)
                ->maxLength(15)
                ->validationMessages([
                    'required' => 'Nombor telefon rumah diperlukan.',
                    'numeric' => 'Nombor telefon rumah mesti berupa angka.',
                    'min_digits' => 'Nombor telefon rumah mesti sekurang-kurangnya 10 digit.',
                    'max_digits' => 'Nombor telefon rumah tidak boleh melebihi 15 digit.',
                ]),

            Forms\Components\Textarea::make('address')
                ->required()
                ->string()
                ->maxLength(255)
                ->validationMessages([
                    'required' => 'Alamat diperlukan.',
                    'string' => 'Alamat mesti berupa teks.',
                    'max' => 'Alamat tidak boleh melebihi 255 aksara.',
                ]),

            Forms\Components\TextInput::make('age')
                ->required()
                ->numeric()
                ->minValue(18)
                ->validationMessages([
                    'required' => 'Umur diperlukan.',
                    'integer' => 'Umur mesti berupa angka.',
                    'min' => 'Umur mesti sekurang-kurangnya 18 tahun.',
                ]),

            Forms\Components\Select::make('residence_status')
                ->required()
                ->options([
                    'kekal' => 'Kekal',
                    'sewa' => 'Sewa',
                ])
                ->validationMessages([
                    'required' => 'Status kediaman diperlukan.',
                    'in' => 'Status kediaman mesti salah satu daripada: kekal, sewa.',
                ]),

            Forms\Components\TextInput::make('password')
                ->password()
                ->required(false) // Make it not required
                ->minLength(8)
                ->revealable()
                ->dehydrateStateUsing(function ($state) {
                    // If password is provided, hash it
                    if (filled($state)) {
                        return Hash::make($state);
                    }

                    // Return null to indicate no change if empty (handled in mutateFormDataBeforeCreate)
                    return null;
                })
                ->dehydrated(fn(?string $state): bool => filled($state))
                ->validationMessages([
                    'min' => 'Kata laluan mesti sekurang-kurangnya 8 aksara.',
                ]),

            // Also make the confirmation optional
            Forms\Components\TextInput::make('password_confirmation')
                ->password()
                ->required(false)
                ->minLength(8)
                ->revealable()
                ->dehydrated(false)
                ->same('password')
                ->visible(fn($get) => filled($get('password'))) // Only show if password is filled
                ->validationMessages([
                    'min' => 'Pengesahan kata laluan mesti sekurang-kurangnya 8 aksara.',
                    'same' => 'Pengesahan kata laluan tidak sepadan.',
                ]),

            // Role selector for admins
            Forms\Components\Select::make('roles')->relationship('roles', 'name')->preload()->label('Peranan'),
        ]);
    }

    public static function table(Table $table): Table
{
    return $table
        ->columns([
            // Add a column to indicate if the user is deceased
            Tables\Columns\IconColumn::make('isDeceased')
            ->label('Kematian')
            ->boolean()
            ->trueIcon('heroicon-s-x-circle')  // Solid X circle for deceased
            ->falseIcon('heroicon-s-check-circle')  // Solid check circle for alive
            ->trueColor('danger')
            ->falseColor('success')
            ->getStateUsing(fn(User $record) => $record->isDeceased())
            ->tooltip(fn(User $record) => $record->isDeceased() ? 'Ahli ini telah meninggal' : 'Ahli ini masih hidup'),

            Tables\Columns\TextColumn::make('No_Ahli')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('name')
                ->label('Nama')
                ->searchable(),

            Tables\Columns\TextColumn::make('email')
                ->searchable(),

            Tables\Columns\TextColumn::make('ic_number')
                ->label('Nombor IC')
                ->searchable(),

            Tables\Columns\TextColumn::make('phone_number')
                ->label('Nombor Telefon')
                ->searchable(),

            Tables\Columns\TextColumn::make('residence_status')
                ->label('Status Kediaman'),

            Tables\Columns\TextColumn::make('roles.name')
                ->badge()
                ->label('Peranan'),

            // Add a column to show number of dependents
            Tables\Columns\TextColumn::make('dependents_count')
                ->label('Tanggungan')
                ->counts('dependents')
                ->badge(),

            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
            ->filters([
                // Add filters if needed
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),



                    // Add CSV Export Bulk Action
                    BulkAction::make('export-csv')
                    ->label('Export to CSV')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function (Collection $records) {
                        // Exclude users with the 'admin' role
                        $filteredUsers = $records->filter(function ($user) {
                            return !$user->hasRole('admin');
                        });

                        // Check if any users remain after filtering
                        if ($filteredUsers->isEmpty()) {
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

                        $callback = function () use ($filteredUsers) {
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
                            foreach ($filteredUsers as $user) {
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
                                    $user->dependents_count,
                                    $user->created_at,
                                ]);
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
                          // Exclude users with the 'admin' role
                          $filteredUsers = $records->filter(function ($user) {
                              return !$user->hasRole('admin');
                          });

                          // Check if any users remain after filtering
                          if ($filteredUsers->isEmpty()) {
                              Notification::make()
                                  ->title('No users to export')
                                  ->danger()
                                  ->send();
                              return;
                          }
                              try {
                                  $pdf = Pdf::loadView('pdf.users', [
                                      'users' => $filteredUsers,
                                  ])
                                  ->setPaper('A4', 'landscape'); // Landscape paper orientation

                                  // Stream the PDF download response
                                  return response()->streamDownload(function () use ($pdf) {
                                      echo $pdf->output();
                                  }, 'users-' . date('Y-m-d') . '.pdf');

                              } catch (\Exception $e) {
                                  // Handle errors and show a notification if PDF generation fails
                                  Notification::make()
                                      ->title('Error generating PDF')
                                      ->danger()
                                      ->body($e->getMessage())
                                      ->send();
                              }
                          })


                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DependentsRelationManager::class,
            RelationManagers\PaymentRelationManager::class, // Add this line
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    // Exclude admin users from the table for non-admin users
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Only filter out user if the current user is not a user
        if (!auth()->user()->hasRole('user')) {
            return $query->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'admin');
            });
        }

        return $query;
    }
}
