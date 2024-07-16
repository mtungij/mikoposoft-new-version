<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\Branch;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('Full Name'))
                    ->required()
                    ->maxLength(100),
                TextInput::make('email')
                    ->label(__('Email Address'))
                    ->required()
                    ->unique('users')
                    ->visible(fn (?string $operation) => $operation !== 'edit'),
                TextInput::make('phone')
                    ->label(__('Phone Number'))
                    ->required()
                    ->maxLength(12)
                    ->minLength(11)
                    ->default('255'),
                Select::make('gender')
                    ->label(__('Gender'))
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                    ])
                    ->native(false)
                    ->required(),
                Select::make('branch_id')
                    ->label(__('Branch'))
                    ->options(function () {
                        return Branch::where('company_id', auth()->user()->company_id)->get()->pluck('name', 'id');
                    })
                    ->searchable()
                    ->required(),
                Select::make('position')
                    ->label(__('Position'))
                    ->options([
                        'loan_officer' => 'Loan Officer',
                        'branch_manager' => 'Branch Manager',
                        'general_manager'=> 'General Manager',
                        'admin' => 'Admin' 
                    ])
                    ->native(false)
                    ->required(),
                
                Select::make('account')
                    ->label(__('Bank Account Name'))
                    ->required()
                    ->options([
                        'cash' => 'CASH',
                        'nmb' => 'NMB',
                        'crdb' => 'CRDB',
                        'nbc' => 'NBC',
                    ])
                    ->native(false)
                    ->live(),
                 TextInput::make('account_number')
                    ->label(__('Account Number'))
                    ->visible(fn (Get $get) => $get('account') !== 'cash')
                    ->maxLength(16),

                TextInput::make('salary')
                    ->label(__('Salary Amount'))
                    ->required()
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->maxLength(fn (?string $operation): int => $operation === 'create'? 8: 10),

                Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'blocked' => 'Blocked'
                    ])
                    ->default('active')
                    ->required()
                    ->visible(fn (?string $operation) => $operation !== 'create'),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->revealable()
                    ->required()
                    ->visible(fn (?string $operation) => $operation !== 'edit'),
                Forms\Components\TextInput::make('passwordConfirmation')
                    ->password()
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->revealable()
                    ->same('password')
                    ->required()
                    ->visible(fn (?string $operation) => $operation !== 'edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(User::query())
            ->modifyQueryUsing(function (Builder $query) {
                return $query->where('company_id', auth()->user()->company_id)->where('position', '!=', 'admin');
            })
            ->columns([
                TextColumn::make('name')
                
                 ->searchable(),
                TextColumn::make('email'),
                TextColumn::make('gender')
                    ->default('-'),
                TextColumn::make('phone')
                    ->default('-'),
                TextColumn::make('position')
                    ->default('-')
                    ->badge()
                    ->color('success'),
                SelectColumn::make('status')
                    ->options([
                        'active' => 'Active',
                        'blocked' => 'Blocked'
                    ]),
                TextColumn::make('branch.name'),
                TextColumn::make('account')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->default('-'),
                TextColumn::make('account_number')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->default('-'),
                TextColumn::make('salary')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->default('-')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUsers::route('/'),
        ];
    }
}
