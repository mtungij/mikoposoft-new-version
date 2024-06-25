<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionAccountResource\Pages;
use App\Filament\Resources\TransactionAccountResource\RelationManagers;
use App\Models\TransactionAccount;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionAccountResource extends Resource
{
    protected static ?string $model = TransactionAccount::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('name')
                    ->required()
                    ->options([
                        'cash'=> 'Cash',
                        'mpesa' => 'M-Pesa',
                        'tigo'=> 'Tigo Pesa',
                        'airtel'=> 'Airtel Money',
                        'halopesa' => 'Halopesa',
                        'nmb'=> 'NMB',
                        'crdb' => 'CRDB',
                        'nbc'=> 'NBC',
                    ])
                    ->searchable()
                    ->unique('transaction_accounts'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
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
            'index' => Pages\ManageTransactionAccounts::route('/'),
        ];
    }
}
