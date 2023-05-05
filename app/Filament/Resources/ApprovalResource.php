<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApprovalResource\Pages;
use App\Filament\Resources\ApprovalResource\RelationManagers;
use App\Models\Approval;
use App\Models\Timesheet;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ApprovalResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $modelLabel = 'Approval';

    protected static ?string $navigationLabel = 'Approval';

    protected static ?string $slug = 'approval';

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->disabled(),
                Forms\Components\TextInput::make('email')->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\BadgeColumn::make('status')
                    ->getStateUsing(fn (User $record): string => self::getApprovalStatus($record))
                    ->colors([
                        'success' => 'Approved',
                    ]),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    
    public static function getApprovalStatus(User $record)
    {
        $status = 'No submissions';
        $approved = $record->timesheets()->where('status', 'Approved')->count();
        $submitted = $record->timesheets()->where('status', 'Submitted')->count();

        if($submitted) {
            $status = 'Pending';
        } else if ($submitted == 0 && $approved > 0) {
            $status = 'Approved';
        }    
        return $status;
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TimesheetsRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApprovals::route('/'),
            'create' => Pages\CreateApproval::route('/create'),
            'edit' => Pages\EditApproval::route('/{record}/edit'),
        ];
    }
}
