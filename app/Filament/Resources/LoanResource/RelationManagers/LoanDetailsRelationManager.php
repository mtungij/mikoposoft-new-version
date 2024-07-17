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
                        $number_of_months = match ($record->duration) {
                            'monthly' => $record->repayments,
                            'weekly' => $record->repayments < 5 ? 1 : ceil($record->repayments / 4),
                            'daily' => $record->repayments < 30 ? 1 : ceil($record->repayments / 30),
                            default => $record->repayments,
                        };

                        $answer = $record->amount * $record->loanCategory->interest / 100 * $number_of_months;
                        return round($answer);
                    })
                    ->numeric()
                    ->alignRight(),
                Tables\Columns\TextColumn::make('colletion')
                    ->label('Collection')
                    ->state(function (LoanDetail $record) {
                        $days = $record->repayments;

                        if($record->formula->name == 'straight'){
                            switch ($record->duration) {
                                case "daily":
                                    $answer = ($record->amount * $record->loanCategory->interest / 100 + $record->amount) / $record->repayments;
                                    return round($answer);
                                case "weekly":
                                    $answer = ($record->amount * $record->loanCategory->interest / 100 + $record->amount) / ($record->repayments);
                                    return round($answer);
                                case "monthly":
                                    $answer = ($record->amount * $record->loanCategory->interest / 100 + $record->amount) / ($record->repayments);
                                    return round($answer);
                            }
                        } elseif($record->formula->name == 'fraterate' || $record->formula->name == 'reducing'){
                            if ($record->duration == "daily") {
                                $number_of_months = ceil($record->repayments / 30);
                                    $answer = (($record->amount * $record->loanCategory->interest / 100 ) * $number_of_months + $record->amount) / $record->repayments;
                                    return round($answer);
                                } elseif($record->duration == "weekly") {
                                    $number_of_months = $record->repayments < 5 ? 1 : ceil($record->repayments / 4);
                                    
                                    $answer = (($record->amount * $record->loanCategory->interest / 100 ) * $number_of_months + $record->amount) / $record->repayments;
                                    return round($answer);
                                } elseif("monthly") {
                                    $answer = (($record->amount * $record->loanCategory->interest / 100 ) * $days + $record->amount) / $record->repayments;
                                    return round($answer);
                                }
                            } else {
                                if ($record->duration == "daily") {
                                $number_of_months = ceil($record->repayments / 30);
                                    $answer = ($record->amount * $record->loanCategory->interest / 100 + $record->amount) / $record->repayments;
                                    return round($answer);
                                } elseif($record->duration == "weekly") {
                                    $number_of_months = $record->repayments < 5 ? 1 : ceil($record->repayments / 4);
                                    
                                    $answer = ($record->amount * $record->loanCategory->interest / 100 + $record->amount) / $record->repayments;
                                    return round($answer);
                                } elseif("monthly") {
                                    $answer = ($record->amount * $record->loanCategory->interest / 100 + $record->amount) / $record->repayments;
                                    return round($answer);
                                }
                            }
                        }
                    )
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
