<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DependentResource\Pages;
use App\Models\Dependent;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Filament\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Tables\Actions\Action;

class DependentResource extends Resource
{
    protected static ?string $model = Dependent::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Pengurusan Keahlian';

    protected static ?string $navigationLabel = 'Tanggungan';

    protected static ?string $modelLabel = 'Tanggungan';
    protected static ?string $pluralModelLabel = 'Tanggungan';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Maklumat Tanggungan')
                ->schema([
                    Forms\Components\Select::make('user_id')
                        ->label('Ahli')
                        ->relationship(
                            name: 'user',
                            modifyQueryUsing: fn($query) => $query->whereDoesntHave('roles', function ($q) {
                                $q->where('name', 'admin');
                            }),
                        )
                        ->getOptionLabelFromRecordUsing(fn (User $record) => "{$record->name} ({$record->ic_number})")
                        ->searchable(['name', 'ic_number'])
                        ->preload()
                        ->required()
                        ->default(fn() => request()->input('data.user_id'))
                        ->disabled(fn() => request()->has('data.user_id'))
                        ->validationMessages([
                            'required' => 'Ahli diperlukan.',
                        ]),

                    Forms\Components\TextInput::make('full_name')
                        ->label('Nama Penuh')
                        ->required()
                        ->maxLength(255)
                        ->validationMessages([
                            'required' => 'Nama penuh diperlukan.',
                            'max' => 'Nama penuh tidak boleh melebihi 255 aksara.',
                        ]),

                    Forms\Components\Select::make('relationship')
                        ->label('Hubungan')
                        ->required()
                        ->options([
                           'Bapa' => 'Bapa',
                            'Ibu' => 'Ibu',
                            'Pasangan' => 'Pasangan',
                            'Anak' => 'Anak',
                        ])
                        ->validationMessages([
                            'required' => 'Hubungan diperlukan.',
                        ]),

                    Forms\Components\TextInput::make('ic_number')
                        ->required()
                        ->label('Nombor IC')
                        ->unique(Dependent::class, 'ic_number', fn($record) => $record)
                        ->minLength(12)
                        ->maxLength(12)
                        ->rule('digits:12')
                        ->numeric()
                        ->mask('999999999999')
                        ->helperText('Nombor IC mesti 12 digit. Contoh: 031114160355')
                        ->live()
                        ->validationMessages([
                            'required' => 'Nombor IC diperlukan.',
                            'digits' => 'Nombor IC mestilah 12 digit.',
                            'min' => 'Nombor IC mestilah 12 digit.',
                            'max' => 'Nombor IC mestilah 12 digit.',
                            'numeric' => 'Nombor IC mesti berupa angka.',
                            'unique' => 'Nombor IC telah digunakan.',
                        ])
                        ->afterStateUpdated(function ($state, callable $set) {
                            if (preg_match('/^\d{12}$/', $state)) {
                                $year = substr($state, 0, 2);
                                $currentYear = (int) date('y');
                                $birthYear = (int) $year + ((int) $year > $currentYear ? 1900 : 2000);
                                $age = (int) date('Y') - $birthYear;
                                $set('age', $age);
                            } else {
                                $set('age', null);
                            }
                        }),

                    Forms\Components\TextInput::make('age')
                        ->label('Umur')
                        ->disabled()
                        ->live()
                        ->dehydrated(true)
                        ->numeric()
                        ->validationMessages([
                            'required' => 'Umur diperlukan.',
                            'numeric' => 'Umur mesti berupa angka.',
                        ]),
                ])->columns(2)
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('isDeceased')
                    ->label('Status Kematian')
                    ->boolean()
                    ->trueIcon('heroicon-s-x-circle')
                    ->falseIcon('heroicon-s-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->getStateUsing(fn (Dependent $record) => $record->isDeceased())
                    ->tooltip(fn (Dependent $record) => $record->isDeceased() ? 'Tanggungan ini telah meninggal' : 'Tanggungan ini masih hidup'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Ahli (Nama)')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.ic_number')
                    ->label('Ahli (No KP)')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nama Penuh Tanggungan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('relationship')
                    ->label('Hubungan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('age')
                    ->label('Umur')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('ic_number')
                    ->label('No KP Tanggungan')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tarikh Cipta')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Ahli')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('relationship')
                    ->label('Hubungan')
                    ->options([
                        'Bapa' => 'Bapa',
                        'Ibu' => 'Ibu',
                        'Pasangan' => 'Pasangan',
                        'Anak' => 'Anak',
                    ])
                    ->multiple(),

                Tables\Filters\Filter::make('age_range')
                    ->label('Julat Umur')
                    ->form([
                        Forms\Components\TextInput::make('min_age')
                            ->label('Umur Minimum')
                            ->numeric()
                            ->minValue(0),
                        Forms\Components\TextInput::make('max_age')
                            ->label('Umur Maksimum')
                            ->numeric()
                            ->minValue(0),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_age'],
                                fn (Builder $query, $min): Builder => $query->where('age', '>=', $min),
                            )
                            ->when(
                                $data['max_age'],
                                fn (Builder $query, $max): Builder => $query->where('age', '<=', $max),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

                Action::make('view_member')
                    ->label('Lihat Ahli')
                    ->icon('heroicon-o-user')
                    ->color('success')
                    ->url(fn (Dependent $record) => UserResource::getUrl('edit', ['record' => $record->user_id]))
                    ->visible(fn (Dependent $record) => $record->user_id !== null),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Padam Terpilih'),

                    BulkAction::make('export-csv')
                        ->label('Eksport ke CSV')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->action(function (Collection $records) {
                            if ($records->isEmpty()) {
                                Notification::make()->title('Tiada tanggungan untuk dieksport')->danger()->send();
                                return;
                            }
                            $csvFileName = 'tanggungan-' . date('Y-m-d') . '.csv';
                            $headers = [
                                'Content-Type' => 'text/csv',
                                'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
                            ];
                            $callback = function () use ($records) {
                                $file = fopen('php://output', 'w');
                                fputcsv($file, ['Nama Ahli', 'No KP Ahli', 'Nama Penuh Tanggungan', 'No KP Tanggungan', 'Hubungan', 'Umur', 'Tarikh Daftar']);
                                foreach ($records as $dependent) {
                                    fputcsv($file, [
                                        $dependent->user?->name ?? 'Tiada',
                                        $dependent->user?->ic_number ?? 'Tiada',
                                        $dependent->full_name,
                                        $dependent->ic_number,
                                        $dependent->relationship,
                                        $dependent->age,
                                        $dependent->created_at
                                    ]);
                                }
                                fclose($file);
                            };
                            return response()->stream($callback, 200, $headers);
                        }),

                    BulkAction::make('export-pdf')
                        ->label('Eksport ke PDF')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('danger')
                        ->action(function (Collection $records) {
                            if ($records->isEmpty()) {
                                Notification::make()->title('Tiada tanggungan untuk dieksport')->danger()->send();
                                return;
                            }
                            $pdf = Pdf::loadView('pdf.dependents', [
                                'dependents' => $records->load('user'),
                            ]);
                            return response()->streamDownload(function () use ($pdf) {
                                echo $pdf->output();
                            }, 'tanggungan-' . date('Y-m-d') . '.pdf');
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
            'index' => Pages\ListDependents::route('/'),
            'create' => Pages\CreateDependent::route('/create'),
            'edit' => Pages\EditDependent::route('/{record}/edit'),
        ];
    }
}
