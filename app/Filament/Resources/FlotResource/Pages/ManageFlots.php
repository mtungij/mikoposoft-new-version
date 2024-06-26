<?php

namespace App\Filament\Resources\FlotResource\Pages;

use App\Filament\Resources\FlotResource;
use App\Models\Capital;
use App\Models\Flot;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Actions\CreateAction;
use Illuminate\Database\Eloquent\Model;

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
                })
                ->using(function (array $data, string $model): ?Model {
                    $data['company_id'] = auth()->user()->company_id;
                    $data['branch_id'] = Filament::getTenant()->id;

                    $floatExist = Flot::where([
                        ['transaction_account_id', '=', $data['transaction_account_id']],
                        ['to_branch_id','=', $data['branch_id']],
                        ['company_id','=', auth()->user()->company_id],
                    ])->first();

                    if($floatExist) {
                        $floatExist->increment('amount', $data['amount']);
                        return null;
                    }
                    
                    $float = $model::create($data);
                    return $float;
                }),
        ];
    }
}
