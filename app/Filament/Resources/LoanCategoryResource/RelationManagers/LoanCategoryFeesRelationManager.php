<?php

namespace App\Filament\Resources\LoanCategoryResource\RelationManagers;

use App\Models\LoanCategoryFee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LoanCategoryFeesRelationManager extends RelationManager
{
    protected static string $relationship = 'loanCategoryFees';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                 Forms\Components\TextInput::make('desc')
                    ->label(__('Description'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('fee_type')
                    ->options([
                        'percentage' => __('Percentage Value'),
                        'money' => __('Money Value'),
                    ])
                    ->live()
                    ->required(),
                Forms\Components\TextInput::make('fee_amount')
                    ->label(__('Fee Amount'))
                    ->required()
                    ->numeric()
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->suffix(fn (Get $get) => $get('fee_type') == 'percentage'? '%' : 'Tsh')
            ]);
                
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('fee_amount')
            ->columns([
                Tables\Columns\TextColumn::make('desc')
                    ->label(__('Description')),
                Tables\Columns\TextColumn::make('fee_amount')
                    ->suffix(fn (LoanCategoryFee $record) => $record->fee_type == 'percentage'? '%' : '')
                    ->numeric(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
