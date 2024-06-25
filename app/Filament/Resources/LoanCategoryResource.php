<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanCategoryResource\Pages;
use App\Filament\Resources\LoanCategoryResource\RelationManagers;
use App\Models\Branch;
use App\Models\LoanCategory;
use Filament\Forms;
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LoanCategoryResource extends Resource
{
    protected static ?string $model = LoanCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\Select::make('select_by')
                //     ->options([
                //         'all' => 'All',
                //         'branch' => 'Branch(es)'
                //     ])
                //     ->required()
                //     ->live()
                //     ->native(false),
                // MultiSelect::make('branches')
                //     ->label(__('Branches'))
                //     ->options(function () {
                //         $userBranches = auth()->user()->branches()->get()->pluck('id')->toArray();
                //         return Branch::whereIn('id', $userBranches)->get()->pluck('name', 'id');
                //     })
                //     ->visible(fn (Get $get) => $get('select_by') == 'branch')
                //     ->searchable()
                //     ->required(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->unique('loan_categories'),
                Forms\Components\TextInput::make('from')
                    ->required()
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->live(true),
                Forms\Components\TextInput::make('to')
                    ->required()
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->live(true)
                    ->afterStateUpdated(function (Get $get, ?string $state) {
                        $fromAmount = (int) str_replace(',', '', $get('from'));
                        $toAmount = (int) str_replace(',', '', $state);

                        if( $fromAmount > $toAmount && $toAmount > 0  ) {
                            Notification::make()
                                ->title(__('To amount should be greater than from amount'))
                                ->danger()
                                ->persistent()
                                ->send();
                        }
                    }),
                Forms\Components\TextInput::make('interest')
                    ->label(__('Interest(%)'))
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('from')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('to')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('interest')
                    ->numeric()
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ManageLoanCategories::route('/'),
        ];
    }
}
