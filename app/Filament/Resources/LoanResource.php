<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanResource\Pages;
use App\Filament\Resources\LoanResource\RelationManagers;
use App\Models\Loan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LoanResource extends Resource
{
    protected static ?string $model = Loan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('branch_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('guarantor_id')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('loan_detail_id')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('collateral')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('local_goverment_detail_id')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('customer_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('loan_type')
                    ->required()
                    ->maxLength(255)
                    ->default('individual'),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255)
                    ->default('pending'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\TextColumn::make('branch.name')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('guarantor.name')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('loanDetail.')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('collateral')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('local_goverment_detail_id')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('customer.first_name')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('loan_type')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('status')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('created_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('updated_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLoans::route('/'),
            'create' => Pages\CreateLoan::route('/create'),
            'edit' => Pages\EditLoan::route('/{record}/edit'),
        ];
    }
}
