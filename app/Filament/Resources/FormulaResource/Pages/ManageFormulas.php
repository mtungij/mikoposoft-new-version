<?php

namespace App\Filament\Resources\FormulaResource\Pages;

use App\Filament\Resources\FormulaResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageFormulas extends ManageRecords
{
    protected static string $resource = FormulaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
