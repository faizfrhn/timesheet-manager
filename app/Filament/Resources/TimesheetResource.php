<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TimesheetResource\Pages;
use App\Filament\Resources\TimesheetResource\RelationManagers;
use App\Models\Timesheet;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Hidden;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rules\Unique;

class TimesheetResource extends Resource
{   
    protected static ?string $model = Timesheet::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function canViewAny(): bool
    {
        return auth()->user()->role == 'talent';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Hidden::make('user_id'),
                        Forms\Components\DatePicker::make('date_worked')->maxDate(now())
                            ->unique(callback: function (Unique $rule, callable $get) { 
                                return $rule
                                        ->where('date_worked', $get('date_worked'))
                                        ->where('user_id', auth()->id());
                                }, ignoreRecord: true)
                            ->required(),
                        Forms\Components\TextInput::make('hours')->numeric()->minValue(0)->maxValue(24)->required(),
                        Hidden::make('status'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date_worked')->date()->sortable(),
                Tables\Columns\TextColumn::make('hours'),
                Tables\Columns\TextColumn::make('status'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->hidden(fn (Timesheet $record) => $record->status !== 'Draft'),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('date_worked', 'desc');
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereBelongsTo(auth()->user());
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTimesheets::route('/'),
            'create' => Pages\CreateTimesheet::route('/create'),
            'edit' => Pages\EditTimesheet::route('/{record}/edit'),
        ];
    }    
}
