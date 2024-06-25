<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenaltResource\Pages;
use App\Filament\Resources\PenaltResource\RelationManagers;
use App\Models\Penalt;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PenaltResource extends Resource
{
    protected static ?string $model = Penalt::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                
                Forms\Components\Select::make('type')
                    ->required()
                    ->unique('penalts')
                    ->options([
                        'money'=> 'Money Value',
                        'percentage'=> 'Percentage Value',
                    ]),

                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(','),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->searchable()
                    ->state(fn ($record): string => match ($record->type) {
                        "money" => "Money Value",
                        "percentage" => "Percentage Value",
                    }),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ManagePenalts::route('/'),
        ];
    }
}
