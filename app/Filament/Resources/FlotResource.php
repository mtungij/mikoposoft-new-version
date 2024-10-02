<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FlotResource\Pages;
use App\Filament\Resources\FlotResource\RelationManagers;
use App\Models\Branch;
use App\Models\Capital;
use App\Models\Flot;
use App\Models\TransactionAccount;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FlotResource extends Resource
{
    protected static ?string $model = Flot::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('capital_id')
                    ->relationship(
                        name: 'capital',
                        modifyQueryUsing: function (Builder $query) {
                            return $query->where('company_id', auth()->user()->company_id)->with('transactionAccount');
                        })
                    ->label(__('Company Account'))
                    ->getOptionLabelFromRecordUsing(fn (?Model $record): string => "{$record->transactionAccount->name} " ."(" . number_format($record->amount) .")" )
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live(),
                Select::make('to_branch_id')
                    ->options(function () {
                        return Branch::where('company_id', auth()->user()->company_id)->get()->pluck('name', 'id');
                    })
                    ->label(__('To Branch Name'))
                    ->required()
                    ->searchable(),

                TextInput::make('amount')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->live(onBlur:true)
                    ->afterStateUpdated(function (?string $state, Get $get) {
                        if($get('capital_id')) {
                            $capital = Capital::where('id', $get('capital_id'))->with('transactionAccount')->first();

                            $amount = (int) str_replace(',', '', $state) ?? 0;
                            if ($capital->amount < $amount) {
                                Notification::make()
                                    ->title(__('Capital is not enough to transfer to branch account.'))
                                    ->danger()
                                    ->persistent()
                                    ->send();
                            }
                        }
                    })
                    ->required(),

                Select::make('transaction_account_id')
                    ->options(function () {
                        return TransactionAccount::where('company_id', auth()->user()->company_id)->get()->pluck('name', 'id');
                    })
                    ->label(__('To Branch Account'))
                    ->required()
                    ->searchable(),

                TextInput::make('withdrawal_charges')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->default(0)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query->with('transactionAccount')->where('company_id', auth()->user()->company_id);
            })
            ->columns([
                TextColumn::make('capital.transactionAccount.name')
                    ->label(__('Company Account'))
                    ->searchable(),
                TextColumn::make('toBranch.name'),
                TextColumn::make('transactionAccount.name')
                ->label('To Branch Account'),
                TextColumn::make('amount')
                    ->numeric(),
                TextColumn::make('withdrawal_charges')
                    ->numeric(),
                TextColumn::make('created_at')
                    ->label(__('Date'))
                    ->dateTime('d-m-Y')
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
            'index' => Pages\ManageFlots::route('/'),
        ];
    }
}
