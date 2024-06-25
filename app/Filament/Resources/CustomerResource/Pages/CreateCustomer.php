<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;


    protected function handleRecordCreation(array $data): Model
    {
        if(auth()->user()->position !== 'admin') {
            $data['branch_id'] = Filament::getTenant()->id;
            $data['user_id'] = auth()->user()->id;
        }
        
        return static::getModel()::create($data);
    }
}
