<?php

namespace App\Filament\Resources\LoanCategoryResource\Pages;

use App\Filament\Resources\LoanCategoryResource;
use App\Models\LoanCategory;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateLoanCategory extends CreateRecord
{
    protected static string $resource = LoanCategoryResource::class;


    protected function mutateFormDataBeforeCreate(array $data): array
    {
           $fromAmount = (int) str_replace(',', '', $data['from']);
            $toAmount = (int) str_replace(',', '', $data['to']);
            $penaltAmount = (int) str_replace(',', '', $data['penalt_amount']);

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
                
                $this->halt();
            }
            if($loanCategory) {
                Notification::make()
                    ->title(__('This loan product already exist.'))
                    ->danger()
                    ->persistent()
                    ->send();
                
                $this->halt();
            }

            if( $data['penalt_type'] == 'percentage' && $penaltAmount > 100 ) {
                Notification::make()
                    ->title(__('Penalty amount should be less than 100%'))
                    ->danger()
                    ->persistent()
                    ->send();
                $this->halt();
            }
            if( $data['penalt_type'] =='money' && $penaltAmount < 0 ) {
                Notification::make()
                    ->title(__('Penalty amount should be greater than 0'))
                    ->danger()
                    ->persistent()
                    ->send();
                $this->halt();
            }

        // $data['branch_id'] = auth()->user()->branch_id;
        $data['company_id'] = auth()->user()->company_id;

        return $data;
    }
}
