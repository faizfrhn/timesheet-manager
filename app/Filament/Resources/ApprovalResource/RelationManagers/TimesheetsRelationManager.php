<?php

namespace App\Filament\Resources\ApprovalResource\RelationManagers;

use App\Mail\Invoicing;
use App\Models\Timesheet;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Mail;

class TimesheetsRelationManager extends RelationManager
{
    protected static string $relationship = 'timesheets';

    protected static ?string $recordTitleAttribute = 'user_id';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->maxLength(255),
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
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('date_worked', 'desc');;
    }

    protected function getTableHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->action(fn () => $this->approveTimesheet())
                ->icon('heroicon-o-check')
                ->color('success')
                ->requiresConfirmation()
                ->hidden($this->getTableQuery()->count() == 0)
        ];
    }

    protected function approveTimesheet()
    {
        $this->getTableQuery()->update(['status' => 'Approved']);

        Notification::make() 
            ->title('Timesheet approved successfully')
            ->success()
            ->send();

        $timesheets = $this->ownerRecord->timesheets()->where('status', 'Approved')->get()->sortBy('date_worked');


        Mail::to('accounts@mail.net')->send(new Invoicing($timesheets, $this->ownerRecord));
    }

    public function getRelationship(): Relation | Builder
    {
        return $this->getOwnerRecord()->{static::getRelationshipName()}()->where('status', 'Submitted');
    }
}
