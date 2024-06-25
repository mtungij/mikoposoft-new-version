<?php

namespace App\Filament\Resources\PenaltResource\Pages;

use App\Filament\Resources\PenaltResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePenalts extends ManageRecords
{
    protected static string $resource = PenaltResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
