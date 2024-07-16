<?php

namespace App\Filament\Resources\CapitalResource\Pages;

use App\Filament\Resources\CapitalResource;
use App\Models\Capital;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Model;

class ManageCapitals extends ManageRecords
{
    protected static string $resource = CapitalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->using(function (array $data, string $model): ?Model {
                    $data['company_id'] = auth()->user()->company_id;
                    $data['branch_id'] = auth()->user()->branch_id;

                    $capital = Capital::where([
                        'transaction_account_id' => $data['transaction_account_id'],
                        'company_id'=> auth()->user()->company_id,
                    ])->first();

                    if ($capital) {
                        $capital->increment('amount', $data['amount']);
                        return null;
                    }
                    $capital = $model::create($data);
                    
                    return $capital;
                }),
        ];
    }
}
