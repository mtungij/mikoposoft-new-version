<?php
namespace App\Filament\Pages\Tenancy;

use App\Models\Branch;
use App\Models\Company;
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
                // TextInput::make('company_name')
                //     ->label('Company Name'),
                TextInput::make('name')
                    ->label('First Branch Name'),
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
        // $company = Company::create([
        //     'name'=> $data['company_name'],
        //     'user_id' => auth()->id(),
        // ]);

        $branch = Branch::create($data);

        // auth()->user()->update(['company_id'=> $company->id]);
 
        $branch->users()->attach(auth()->user());
 
        return $branch;
    }
}