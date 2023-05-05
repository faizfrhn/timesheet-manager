<?php

namespace App\Filament\Resources\TimesheetResource\Pages;

use App\Filament\Resources\TimesheetResource;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListTimesheets extends ListRecords
{
    protected static string $resource = TimesheetResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('Submit Weekly Timesheet')
                ->action(fn () => $this->submitWeeklyTimesheet())
                ->requiresConfirmation()
                ->disabled(fn () => !$this->checkIfCanSubmit())
        ];
    }

    protected function checkIfCanSubmit()
    {
        $date = Carbon::now();
        // assuming end of week is Friday
        $endOfWeek = $date->isSameDay($date->copy()->endOfWeek(Carbon::FRIDAY));

        // get total hours for current week
        $startOfWeekDate = $date->startOfWeek()->format('Y-m-d');
        $endOfWeekDate = $date->endOfWeek()->format('Y-m-d');
        $condition = ['user_id' => auth()->id(), ['date_worked', '>=', $startOfWeekDate], ['date_worked', '<=', $endOfWeekDate]];
        $totalHours = static::getModel()::query()->where($condition)->sum('hours');

        $submitCondition = ['user_id' => auth()->id(), ['date_worked', '>=', $startOfWeekDate], ['date_worked', '<=', $endOfWeekDate], ['status', '=', 'Draft']];
        $submitted = static::getModel()::query()->where($submitCondition)->count();

        // can only submit if end of week and total hours for current week is >= 40 hours
        return ($endOfWeek && $totalHours >= 40 && $submitted != 0);
    }

    protected function submitWeeklyTimesheet()
    {
        $date = Carbon::now();

        $startOfWeekDate = $date->startOfWeek()->format('Y-m-d');
        $endOfWeekDate = $date->endOfWeek()->format('Y-m-d');
        $condition = ['user_id' => auth()->id(), ['date_worked', '>=', $startOfWeekDate], ['date_worked', '<=', $endOfWeekDate]];

        static::getModel()::query()->where($condition)->update(['status' => 'Submitted']);

        Notification::make() 
            ->title('Weekly timesheet submitted successfully')
            ->success()
            ->send(); 
    }

}
