<?php
namespace App\Filament\Pages\Tenancy;

use App\Models\Branch;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\RegisterTenant;
 
class RegisterBranch extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Register Branch';
    }
 
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Branch Name'),
                TextInput::make('phone')
                    ->label('Branch Phone Number')
                    ->required(),
                TextInput::make('email')
                    ->label('Branch Email'),
                Select::make('region_id')
                    ->relationship('region', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }
 
    protected function handleRegistration(array $data): Branch
    {
        $branch = Branch::create($data);
 
        $branch->users()->attach(auth()->user());
 
        return $branch;
    }
}