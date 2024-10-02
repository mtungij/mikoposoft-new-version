<?php

namespace App\Filament\Resources\TransactionAccountResource\Pages;

use App\Filament\Resources\TransactionAccountResource;
use App\Models\TransactionAccount;
use Filament\Actions;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Model;

class ManageTransactionAccounts extends ManageRecords
{
    protected static string $resource = TransactionAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->mutateFormDataUsing(function (CreateAction $action,array $data): array {
                    $account = TransactionAccount::where([
                        'company_id' => auth()->user()->company_id,
                        'name'=> $data['name'],
                    ])->first();

                    if($account) {
                        Notification::make()
                            ->title(__('This account already exist.'))
                            ->danger()
                            ->persistent()
                            ->send();
                        
                        $action->halt();
                    }
                    return $data;
                })
                ->using(function (array $data, string $model): Model {
                    $data['company_id'] = auth()->user()->company_id;
                    
                    $account = $model::create($data);
                    return $account;
                }),
        ];
    }
}
