<?php

namespace App\Filament\Resources\LoanResource\RelationManagers;

use App\Models\Loan;
use App\Models\LoanDetail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LoanDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'LoanDetails';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['loanCategory', 'formula']))
            ->recordTitleAttribute('amount')
            ->columns([
                Tables\Columns\TextColumn::make('loanCategory.name')
                    ->label(__('Loan Product')),
                Tables\Columns\TextColumn::make('formula.name'),
                Tables\Columns\TextColumn::make('reason'),
                Tables\Columns\TextColumn::make('duration')
                    ->alignRight(),
                    Tables\Columns\TextColumn::make('repayments')
                    ->label('Number of repaynment')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('amount')
                    ->label(__('Principal'))
                    ->numeric()
                    ->alignRight(),
                Tables\Columns\TextColumn::make('interest')
                    ->label('Interest')
                    ->state(function (LoanDetail $record) {
                        if($record->formula->name == 'straight'){
                            $answer = $record->amount * $record->loanCategory->interest / 100 ;
                            return $answer;
                        }
                    })
                    ->numeric()
                    ->alignRight(),
                Tables\Columns\TextColumn::make('principle')
                    ->label('Collection')
                    ->state(function (LoanDetail $record) {
                        if($record->formula->name == 'straight'){
                            $answer = ($record->amount * $record->loanCategory->interest / 100 + $record->amount) / $record->repayments;
                            return $answer;
                        } elseif($record->formula->name == 'flatrate'){
                            $answer = ($record->amount * $record->loanCategory->interest / 100 + $record->amount) / $record->repayments;
                            return $answer;
                        }
                    })
                    ->numeric()
                    ->alignRight(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Application Date'))
                    ->dateTime('d/m/Y'),
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
