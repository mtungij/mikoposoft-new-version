<?php
namespace App\Filament\Pages\Tenancy;
 
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\EditTenantProfile;
 
class EditBranchProfile extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return 'Branch Profile';
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
            ]);
    }
}