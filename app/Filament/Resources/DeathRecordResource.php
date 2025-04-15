<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeathRecordResource\Pages;
use App\Models\DeathRecord;
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

class DeathRecordResource extends Resource
{
    protected static ?string $model = DeathRecord::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Death Records';

    protected static ?string $navigationGroup = 'Records Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('dependent_id')
                    ->relationship('dependent', 'full_name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\DatePicker::make('date_of_death')
                    ->required()
                    ->label('Date of Death'),

                Forms\Components\TimePicker::make('time_of_death')
                    ->label('Time of Death')
                    ->seconds(false),

                Forms\Components\TextInput::make('place_of_death')
                    ->label('Place of Death')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('cause_of_death')
                    ->label('Cause of Death')
                    ->rows(3)
                    ->maxLength(1000),

                Forms\Components\Textarea::make('death_notes')
                    ->label('Notes')
                    ->rows(3)
                    ->maxLength(1000),

                Forms\Components\Section::make('Death Certificate')
                    ->schema([
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
                Tables\Columns\TextColumn::make('dependent.full_name')
                    ->searchable()
                    ->sortable()
                    ->label('Dependent Name'),

                Tables\Columns\TextColumn::make('dependent.ic_number')
                    ->searchable()
                    ->label('IC Number'),

                Tables\Columns\TextColumn::make('date_of_death')
                    ->date()
                    ->sortable()
                    ->label('Date of Death'),

                Tables\Columns\TextColumn::make('place_of_death')
                    ->searchable()
                    ->limit(30)
                    ->label('Place of Death'),

                Tables\Columns\IconColumn::make('death_attachment_path')
                    ->boolean()
                    ->label('Certificate')
                    ->trueIcon('heroicon-o-document')
                    ->falseIcon('heroicon-o-x-mark')
                    ->getStateUsing(fn (DeathRecord $record) => $record->death_attachment_path !== null),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('viewCertificate')
                    ->label('View Certificate')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->visible(fn ($record) => $record->death_attachment_path)
                    ->url(fn ($record) => $record->death_attachment_path ? Storage::url($record->death_attachment_path) : null, true),
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
                         // Check if any records are selected
                         if ($records->isEmpty()) {
                             Notification::make()
                                 ->title('No death records to export')
                                 ->danger()
                                 ->send();
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
                             fputcsv($file, [
                                 'Dependent Name',
                                 'IC Number',
                                 'Date of Death',
                                 'Time of Death',
                                 'Place of Death',
                                 'Cause of Death',
                                 'Death Notes',
                                 'Certificate Available',
                                 'Created At',
                             ]);

                             // Add rows
                             foreach ($records as $record) {
                                 fputcsv($file, [
                                     $record->dependent ? $record->dependent->full_name : 'N/A',
                                     $record->dependent ? $record->dependent->ic_number : 'N/A',
                                     $record->date_of_death ? $record->date_of_death->format('Y-m-d') : 'N/A',
                                     $record->time_of_death ? $record->time_of_death->format('H:i') : 'N/A',
                                     $record->place_of_death ?? 'N/A',
                                     $record->cause_of_death ?? 'N/A',
                                     $record->death_notes ?? 'N/A',
                                     $record->death_attachment_path ? 'Yes' : 'No',
                                     $record->created_at->format('Y-m-d'),
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
                         // Check if any records are selected
                         if ($records->isEmpty()) {
                             Notification::make()
                                 ->title('No death records to export')
                                 ->danger()
                                 ->send();
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
