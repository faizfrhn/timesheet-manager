<?php

namespace App\Filament\Resources\TimesheetResource\Pages;

use App\Filament\Resources\TimesheetResource;
use App\Models\Timesheet;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTimesheet extends EditRecord
{
    protected static string $resource = TimesheetResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->hidden(fn (Timesheet $record) => $record->where(['status', '!=', 'Draft'])),
        ];
    }

    protected function getFormActions(): array
    {

        return [
            $this->getSaveFormAction()->disabled(fn() => $this->getRecord()->where(['status', '!=', 'Draft'])),
            $this->getCancelFormAction(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
