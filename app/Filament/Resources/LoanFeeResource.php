<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanFeeResource\Pages;
use App\Filament\Resources\LoanFeeResource\RelationManagers;
use App\Models\LoanCategory;
use App\Models\LoanFee;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LoanFeeResource extends Resource
{
    protected static ?string $model = LoanFee::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('category')
                    ->required()
                    ->options([
                        'general' => 'General',
                        'loan_product' => 'By Loan Product'
                    ])
                    ->live()
                    ->native(false),
                Forms\Components\TextInput::make('desc')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('fee_type')
                    ->required()
                    ->options([
                        'percentage' => 'Percentage Value(%)',
                        'money' => 'Money Value'
                    ])
                    ->visible(fn (Get $get) => $get('category') == 'general')
                    ->native(false),
                Forms\Components\TextInput::make('fee_amount')
                    ->required()
                    ->numeric()
                    ->visible(fn (Get $get) => $get('category') == 'general')
                    ->maxLength(8),
                // Repeater::make('loanCategoryFees')
                //      ->label('Loan Product Fees')
                //     ->relationship('loanCategoryFees')
                //     ->columnSpanFull()
                //     ->columns(3)
                //     ->visible(fn (Get $get) => $get('category') == 'loan_product')
                //     ->schema([
                //         Select::make('loan_category_id')
                //             ->label('Loan Product')
                //             ->options(function () {
                //                 return LoanCategory::where('branch_id', auth()->user()->branch_id)->get()->pluck('name', 'id');
                //             })
                //             ->searchable()
                //             ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                //             ->required(),
                //         Select::make('fee_type')
                //             ->options([
                //                 'percentage' => 'Percentage Value',
                //                 'money' => 'Money Value',
                //             ])
                //             ->native(false)
                //             ->required(),
                //         TextInput::make('fee_amount')
                //             ->numeric()
                //             ->mask(RawJs::make('$money($input)'))
                //             ->stripCharacters(',')
                //             ->required(),
                //     ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(LoanFee::query())
            ->modifyQueryUsing(function (Builder $query) {
                return $query->where('company_id', auth()->user()->company_id);
            })
            ->columns([
                Tables\Columns\TextColumn::make('branch.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fee_type')
                    ->default('-')
                    ->searchable(),
                Tables\Columns\TextColumn::make('desc')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fee_amount')
                    ->numeric()
                    ->sortable()
                    ->default('-'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ManageLoanFees::route('/'),
        ];
    }
}
