<?php

namespace App\Filament\Resources\LoanCategoryResource\Pages;

use App\Filament\Resources\LoanCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLoanCategories extends ListRecords
{
    protected static string $resource = LoanCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
