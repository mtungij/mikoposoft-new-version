<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanResource\Pages;
use App\Filament\Resources\LoanResource\RelationManagers;
use App\Models\Customer;
use App\Models\Formula;
use App\Models\Loan;
use App\Models\LoanCategory;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class LoanResource extends Resource
{
    protected static ?string $model = Loan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('user_id')
                    ->default(auth()->user()->id),
                Wizard::make([
                    Wizard\Step::make('initialDetails')
                        ->label('Search Customer')
                        ->schema([
                            Select::make('customer_id')
                                ->label('Customer')
                                ->options(
                                    fn () => Customer::get()->pluck('full_name', 'id')
                                )
                                ->preload()
                                ->searchable()
                                ->required(),
                        ]),
                    Wizard\Step::make('guarantors')
                        ->label('Guarantors Details')
                        ->schema([
                            Repeater::make('guarantors')
                                ->relationship()
                                ->columns(2)
                                ->schema([
                                    TextInput::make('name')
                                        ->label('Guarantor Name')
                                        ->maxLength(100),
                                    TextInput::make('phone')
                                    ->label('Phone Number')
                                        ->tel(),
                                    TextInput::make('relationship')
                                       ->label('Relationship With customer:')
                                        ->maxLength(30),
                                    TextInput::make('street')
                                        ->maxLength(100),
                                    TextInput::make('business_name')
                                        ->maxLength(255)
                                ])
                        ]),
                    Wizard\Step::make('loan Detail')
                        ->label('Loan Details')
                        ->columnSpanFull()
                        ->schema([
                            Repeater::make('loanDetails')
                                ->relationship()
                                ->columns(2)
                                ->maxItems(1)
                                ->minItems(1)
                                ->schema([
                                    Select::make('loan_category_id')
                                        ->label('Loan Product')
                                        ->options(
                                            fn () => LoanCategory::where('company_id', auth()->user()->company_id)->get()->pluck('name', 'id')
                                        )
                                        ->live()
                                        ->searchable()
                                        ->required(),
                                    Select::make('formula_id')
                                        ->label('Interest Formula')
                                        ->options(
                                            fn () => Formula::where('company_id', auth()->user()->company_id)->get()->pluck('name', 'id')
                                        )
                                        ->searchable()
                                        ->required(),
                                    TextInput::make('amount')
                                        ->live(onBlur:true)
                                        ->afterStateUpdated(function (?string $state, Set $set, Get $get) {
                                            $amount = (int) str_replace(',','', $state);

                                            $loanCategory = LoanCategory::where('company_id', auth()->user()->company_id)->where('id', $get('loan_category_id'))->first();
                                            if ($amount && ($amount < $loanCategory->from || $amount > $loanCategory->to)) {
                                                Notification::make()
                                                    ->title('The loan amount applied is invalid.')
                                                    ->danger()
                                                    ->color('danger')
                                                    ->persistent()
                                                    ->send();

                                                $set('amount', '');
                                            }
                                        })
                                        ->label('Loan Amount Applied')
                                        ->mask(RawJs::make('$money($input)'))
                                        ->stripCharacters(',')
                                        ->required(),
                                    Select::make('duration')
                                        ->options([
                                            'daily' => 'Daily',
                                            'weekly'=> 'Weekly',
                                            'monthly'=> 'Monthly',
                                        ])
                                        ->required()
                                        ->native(false),
                                    TextInput::make('repayments')
                                        ->label('Number of repayments')
                                        ->numeric()
                                        ->required(),
                                    Textarea::make('reason')
                                        ->label('Reason of Applying Loan:')
                                        ->required()
                                        ->maxLength(255)
                                ])
                        ]),
                    Wizard\Step::make('collaterals')
                         ->label('Collateral Details')
                        ->schema([
                            Repeater::make('collaterals')
                                ->relationship()
                                ->columns(2)
                                ->schema([
                                    TextInput::make('name')
                                        ->label('Collateral Name')
                                        ->maxLength(100),
                                    TextInput::make('current_condition')
                                        ->label('Collateral Condition')
                                        ->maxLength(255),
                                    TextInput::make('current_value')
                                        ->label('current Collateral value')
                                        ->maxLength(12)
                                        ->mask(RawJs::make('$money($input)'))
                                        ->stripCharacters(','),
                                    FileUpload::make('img_url')
                                        ->label('Collateral attachment')
                                        ->imageEditor()
                                        ->directory('collaterals'),
                                ])
                        ]),
                    Wizard\Step::make('Local Goverment Details')
                        ->label('Authorization Details')
                        ->columnSpanFull()
                        ->schema([
                            Repeater::make('localGovermentDetails')
                                ->relationship()
                                ->columns(2)
                                ->maxItems(1)
                                ->minItems(1)
                                ->schema([
                                    TextInput::make('name')
                                        ->label('Name Of Officer')
                                        ->maxLength(100),
                                    TextInput::make('phone')
                                        ->label('Officer phone Number')
                                        ->tel(),
                                    Select::make('title')
                                        ->options([
                                            'mwenyekiti'=> 'mwenyekiti',
                                            'afisa' => 'Afisa Mtendaji'
                                        ])
                                        ->placeholder('select title')
                                        ->searchable()
                                ])
                            ])
                ])
                ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('branch.name'),
                TextColumn::make('customer.full_name')
                    ->searchable(),
                TextColumn::make('loanDetails.amount')
                    ->label('Loan Amount')
                    ->numeric(),
                TextColumn::make('status')
                    ->badge(),
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
            'view' => Pages\ViewLoan::route('/{record}/view'),
        ];
    }
}
