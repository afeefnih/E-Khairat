<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DependentResource\Pages;
use App\Models\Dependent;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
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

    protected static ?string $navigationGroup = 'Ahli';

    protected static ?string $navigationLabel = 'Dependents';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')
                ->label('Member')
                ->relationship(
                    name: 'user',
                    titleAttribute: 'name',
                    modifyQueryUsing: fn($query) => $query->whereDoesntHave('roles', function ($q) {
                        $q->where('name', 'admin');
                    }),
                )
                ->searchable()
                ->preload()
                ->required()
                ->default(fn() => request()->input('data.user_id'))
                ->disabled(fn() => request()->has('data.user_id'))
                ->validationMessages([
                    'required' => 'Ahli diperlukan.',
                ]),

            Forms\Components\TextInput::make('full_name')
                ->required()
                ->maxLength(255)
                ->validationMessages([
                    'required' => 'Nama penuh diperlukan.',
                    'max' => 'Nama penuh tidak boleh melebihi 255 aksara.',
                ]),

            Forms\Components\Select::make('relationship')
                ->required()
                ->options([
                    'Pasangan' => 'Pasangan',
                    'Anak' => 'Anak',
                    'Ibu/Bapa' => 'Ibu/Bapa',
                    'Adik-Beradik' => 'Adik-beradik',
                ])
                ->validationMessages([
                    'required' => 'Hubungan diperlukan.',
                ]),

            Forms\Components\TextInput::make('age')
                ->required()
                ->numeric()
                ->validationMessages([
                    'required' => 'Umur diperlukan.',
                    'numeric' => 'Umur mesti berupa angka.',
                ]),

            Forms\Components\TextInput::make('ic_number')
                ->required()
                ->label('Nombor IC')
                ->unique(Dependent::class, 'ic_number', fn($record) => $record)
                ->length(12)
                ->numeric()
                ->validationMessages([
                    'required' => 'Nombor IC diperlukan.',
                    'digits' => 'Nombor IC mesti 12 digit.',
                    'unique' => 'Nombor IC telah digunakan.',
                    'numeric' => 'Nombor IC mesti berupa angka.',
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Add a column to indicate if the dependent is deceased
                Tables\Columns\IconColumn::make('isDeceased')
                    ->label('Deceased')
                    ->boolean()
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->getStateUsing(fn (Dependent $record) => $record->isDeceased())
                    ->tooltip('Indicates if the dependent is deceased'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Member')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('full_name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('relationship')
                    ->sortable(),

                Tables\Columns\TextColumn::make('age')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('ic_number')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
            ])
            // Rest of your table configuration...
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')->label('Member')->relationship('user', 'name')->searchable()->preload(),

                Tables\Filters\SelectFilter::make('relationship')->options([
                    'Bapa' => 'Bapa',
                    'Ibu' => 'Ibu',
                    'Pasangan' => 'Pasangan',
                    'Anak' => 'Anak',
                ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                // Add View Member Action
                Action::make('view_member')
                    ->label('View Member')
                    ->icon('heroicon-o-user')
                    ->color('success')
                    ->url(fn (Dependent $record) => UserResource::getUrl('edit', ['record' => $record->user_id]))

                    ->visible(fn (Dependent $record) => $record->user_id !== null),
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
                            // Check if any dependents are selected
                            if ($records->isEmpty()) {
                                Notification::make()->title('No dependents to export')->danger()->send();
                                return;
                            }

                            // Generate CSV file
                            $csvFileName = 'dependents-' . date('Y-m-d') . '.csv';
                            $headers = [
                                'Content-Type' => 'text/csv',
                                'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
                            ];

                            $callback = function () use ($records) {
                                $file = fopen('php://output', 'w');

                                // Add headers
                                fputcsv($file, ['Nama Ahli', 'Nama Penuh', 'Hubungan', 'Umur', 'Nombor KP', 'tarikh daftar']);

                                // Add rows
                                foreach ($records as $dependent) {
                                    fputcsv($file, [$dependent->user ? $dependent->user->name : 'N/A', $dependent->full_name, $dependent->relationship, $dependent->age, $dependent->ic_number, $dependent->created_at]);
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
                            // Check if any dependents are selected
                            if ($records->isEmpty()) {
                                Notification::make()->title('No dependents to export')->danger()->send();
                                return;
                            }

                            // Generate the PDF
                            $pdf = Pdf::loadView('pdf.dependents', [
                                'dependents' => $records,
                            ]);

                            return response()->streamDownload(function () use ($pdf) {
                                echo $pdf->output();
                            }, 'dependents-' . date('Y-m-d') . '.pdf');
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
