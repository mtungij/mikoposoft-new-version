<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CapitalResource\Pages;
use App\Filament\Resources\CapitalResource\RelationManagers;
use App\Models\Capital;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CapitalResource extends Resource
{
    protected static ?string $model = Capital::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Select::make('user_id')
                    ->label(__('Shareholder Name'))
                    ->default(auth()->id())
                    ->required()
                    ->options([
                        auth()->id() => auth()->user()->name,
                    ])
                    ->disablePlaceholderSelection(),
                Select::make('transaction_account_id')
                    ->relationship(
                        name:'transactionAccount',
                        titleAttribute:'name',
                        modifyQueryUsing: fn (Builder $query) => $query->where('company_id', auth()->user()->company_id)
                    )
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('amount')
                   ->mask(RawJs::make('$money($input)'))
                   ->stripCharacters(',')
                   ->required()
                   ->maxLength(14)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->where('company_id', auth()->user()->company_id))
            ->columns([
                TextColumn::make('transactionAccount.name'),
                TextColumn::make('amount')
                    ->numeric(),
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
            'index' => Pages\ManageCapitals::route('/'),
        ];
    }
}
