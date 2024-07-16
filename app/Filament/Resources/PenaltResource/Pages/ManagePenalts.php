<?php

namespace App\Filament\Resources\PenaltResource\Pages;

use App\Filament\Resources\PenaltResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Model;

class ManagePenalts extends ManageRecords
{
    protected static string $resource = PenaltResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->using(function (array $data, string $model): Model {
                    $data['company_id'] = auth()->user()->company_id;
                    // $data['branch_id'] = Filament::getTenant()->id;
                    
                    $penalt = $model::create($data);
                    return $penalt;
                }),
        ];
    }
}
