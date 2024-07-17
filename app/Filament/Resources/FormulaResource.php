<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FormulaResource\Pages;
use App\Filament\Resources\FormulaResource\RelationManagers;
use App\Models\Formula;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FormulaResource extends Resource
{
    protected static ?string $model = Formula::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('name')
                    ->required()
                    ->options([
                        'straight' => 'Straight Formula',
                        'fraterate' => 'Frate Rate Formula',
                        'reducing' => "Reducing Formula"
                    ])
                    ->native(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Formula::query())
            ->modifyQueryUsing(function (Builder $query) {
                return $query->where('company_id', auth()->user()->company_id);
            })
            ->columns([
                
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->state(fn (Formula $record) => match ($record->name) {
                        "straight" => "Straight Formula",
                        "fraterate" => "Frate Rate Formula",
                        "reducing" => "Reducing Formula",
                        default => "Unknown Formula"
                    }),
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
            'index' => Pages\ManageFormulas::route('/'),
        ];
    }
}
