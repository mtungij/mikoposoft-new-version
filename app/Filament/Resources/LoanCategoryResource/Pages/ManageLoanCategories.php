<?php

namespace App\Filament\Resources\LoanCategoryResource\Pages;

use App\Filament\Resources\LoanCategoryResource;
use App\Models\LoanCategory;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Actions\CreateAction;
use Illuminate\Database\Eloquent\Model;

class ManageLoanCategories extends ManageRecords
{
    protected static string $resource = LoanCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->mutateFormDataUsing(function (CreateAction $action,array $data): array {
                    $fromAmount = (int) str_replace(',', '', $data['from']);
                    $toAmount = (int) str_replace(',', '', $data['to']);

                    $loanCategory = LoanCategory::where([
                        'name' => $data['name'],
                        'company_id'=> auth()->user()->company_id,
                    ])->first();

                    if( $fromAmount > $toAmount ) {
                        Notification::make()
                            ->title(__('To amount should be greater than from amount.'))
                            ->danger()
                            ->persistent()
                            ->send();
                        
                        $action->halt();
                    }
                    if($loanCategory) {
                        Notification::make()
                            ->title(__('This loan product already exist.'))
                            ->danger()
                            ->persistent()
                            ->send();
                        
                        $action->halt();
                    }
                    
                    return $data;
                })
                ->using(function (array $data, string $model): Model {
                    $data['company_id'] = auth()->user()->company_id;
                    $data['branch_id'] = Filament::getTenant()->id;
                    
                    $loanCategories = $model::create($data);
                    return $loanCategories;
                }),
        ];
    }
}
