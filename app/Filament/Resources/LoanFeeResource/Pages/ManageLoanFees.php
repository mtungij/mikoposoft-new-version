<?php

namespace App\Filament\Resources\LoanFeeResource\Pages;

use App\Filament\Resources\LoanFeeResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Model;

class ManageLoanFees extends ManageRecords
{
    protected static string $resource = LoanFeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->using(function (array $data, string $model): Model {
                $data['company_id'] = auth()->user()->company_id;
                $data['branch_id'] = Filament::getTenant()->id;
                
                $loanFee = $model::create($data);
                return $loanFee;
            }),
        ];
    }
}
