<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ApprovedLoans extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(
                \App\Models\Loan::query()
                    ->where('status', 'approved')
                    ->where('company_id', auth()->user()->company_id)
            )
            ->columns([
                Tables\Columns\TextColumn::make('customer.full_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer.phone')
                    ->label(__('Phone Number')),
                Tables\Columns\TextColumn::make('customer.branch.name')
                    ->label(__('Branch')),
                Tables\Columns\TextColumn::make('loanDetails.amount')
                    ->label('Loan Amount')
                    ->alignRight()
                    ->numeric(),
                Tables\Columns\TextColumn::make('loanDetails.duration')
                    ->label('Duration'),
                Tables\Columns\TextColumn::make('loanDetails.repayments')
                    ->label('Repayments'),
                Tables\Columns\TextColumn::make('loanDetails.interest')
                    ->label('Interest')
            ]);
    }
}
