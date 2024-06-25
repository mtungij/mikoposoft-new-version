<?php

namespace App\Filament\Resources\LoanCategoryResource\Pages;

use App\Filament\Resources\LoanCategoryResource;
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
                    if( $fromAmount > $toAmount ) {
                        Notification::make()
                            ->title(__('To amount should be greater than from amount.'))
                            ->danger()
                            ->persistent()
                            ->send();
                        
                        $action->halt();
                    }
                    return $data;
                })
                // ->using(function (array $data, string $model): Model {
                //     $data['branch_id'] = Filament::getTenant()->id;
                //     // if($data['select_by'] == 'all') {
                //     //     $data['branches'] = auth()->user()->branches()->get()->pluck('id')->toArray();
                //     // }
                //     $loanCategory = $model::create($data);
                //     // $loanCategory->branches()->attach($data['branches']);
                //     return $loanCategory;
                // })
        ];
    }
}
