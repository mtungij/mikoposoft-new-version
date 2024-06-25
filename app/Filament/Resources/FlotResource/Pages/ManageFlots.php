<?php

namespace App\Filament\Resources\FlotResource\Pages;

use App\Filament\Resources\FlotResource;
use App\Models\Capital;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Actions\CreateAction;

class ManageFlots extends ManageRecords
{
    protected static string $resource = FlotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->mutateFormDataUsing(function (CreateAction $action,array $data): array {
                    $capital = Capital::where('id', $data['capital_id'])->with('transactionAccount')->first();

                    $amount = (int) str_replace(',', '', $data['amount']) ?? 0;
                    $withdrawCharges = (int) str_replace(',', '', $data['withdrawal_charges']) ?? 0;
                    if ($capital->amount < $amount || $capital->amount < $withdrawCharges) {
                        Notification::make()
                            ->title(__('Capital is not enough to transfer to branch account.'))
                            ->danger()
                            ->persistent()
                            ->send();
                        $action->halt();
                    }

                    $capital->decrement('amount', $amount + $withdrawCharges);
                    return $data;
                }),
        ];
    }
}
