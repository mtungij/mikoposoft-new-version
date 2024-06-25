<?php

namespace App\Filament\Resources\TransactionAccountResource\Pages;

use App\Filament\Resources\TransactionAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTransactionAccounts extends ManageRecords
{
    protected static string $resource = TransactionAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
