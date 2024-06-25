<?php

namespace App\Filament\Resources\LoanFeeResource\Pages;

use App\Filament\Resources\LoanFeeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageLoanFees extends ManageRecords
{
    protected static string $resource = LoanFeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
