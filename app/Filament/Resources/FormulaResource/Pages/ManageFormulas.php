<?php

namespace App\Filament\Resources\FormulaResource\Pages;

use App\Filament\Resources\FormulaResource;
use App\Models\Formula;
use Filament\Actions;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Model;

class ManageFormulas extends ManageRecords
{
    protected static string $resource = FormulaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data, CreateAction $action): array {
                    $formulaExist = Formula::where([
                        'name' => $data['name'],
                        'company_id'=> auth()->user()->company_id,
                    ])->first();

                    if ($formulaExist) {
                        Notification::make()
                            ->title(__('Formula already exist'))
                            ->danger()
                            ->send();

                        $action->halt();
                    }
                    return $data;
                })
                ->using(function (array $data, string $model): Model {
                    $data['company_id'] = auth()->user()->company_id;
                    // $data['branch_id'] = Filament::getTenant()->id;
                    
                    $formula = $model::create($data);
                    return $formula;
                }),
        ];
    }
}
