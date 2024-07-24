<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $recordTitleAttribute = 'full_name';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(3)
                    ->schema([
                    Forms\Components\Select::make('branch_id')
                        ->relationship('branch', 'name', function (Builder $query) {
                            return $query->where('company_id',auth()->user()->company_id);
                        })
                        ->afterStateUpdated(function (?string $state, Set $set) {
                            $branchId = (int) $state ?? null;

                            $customersCount = Customer::where('branch_id', $branchId)->count() + 1;

                            $customerNumber = "C-". date("Ymi"). $branchId . "-". $customersCount;

                            $set('c_number', $customerNumber);
                        })
                        ->live()
                        ->searchable()
                        ->preload()
                        ->visible(auth()->user()->position == 'admin')
                        ->required(),
                    Forms\Components\Select::make('user_id')
                        ->relationship('user', 'name', function (Builder $query, Get $get) {
                            return $query->where('branch_id', $get('branch_id'))->where('position', '!=', 'admin');
                        })
                        ->label('Branch Employee')
                        ->searchable()
                        ->default(auth()->user()->position !== 'admin' ? auth()->id() : null)
                        ->preload()
                        ->visible(auth()->user()->position == 'admin')
                        ->required(),
                    Forms\Components\TextInput::make('c_number')
                        ->label('Customer Id')
                        ->required()
                        ->default(function (Get $get) {
                            $branchId = auth()->user()->branch_id;

                            $customersCount = Customer::where('branch_id', $branchId)->count() + 1;
                            $customerNumber = "C-". date("Ym"). $branchId . $customersCount;
                            // dd($customersCount);
                            return $customerNumber;
                        })
                        ->live()
                        ->readOnly()
                        ->hidden(fn (?string $operation) => $operation === 'edit')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('first_name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('middle_name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('last_name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Select::make('gender')
                        ->required()
                        ->options([
                            'male' => 'Male',
                            'female'=> 'Female',
                        ])
                    ->searchable(),
                    Forms\Components\TextInput::make('phone')
                        ->tel()
                        ->required()
                        ->default("255")
                        ->maxLength(12)
                        ->minLength(12),
                    Forms\Components\TextInput::make('ward')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('street')
                        ->maxLength(255),
                    Forms\Components\Select::make('id_type')
                        ->options([
                            'nida'=> 'National Identity Number',
                            'voter'=> 'Voter Number',
                            'driving' => 'Driving License'
                        ])
                        ->native(false),
                    Forms\Components\TextInput::make('id_number')
                        ->maxLength(20)
                        ->numeric(),
                    Forms\Components\TextInput::make('nick_name')
                        ->maxLength(255),
                    Forms\Components\Select::make('marital_status')
                        ->options([
                            'single' => 'Single',
                            'married' => 'Married',
                            'widow' => 'Widow',
                            'separated'=> 'Separated',
                            'divorced'=> 'Divorced',
                        ])
                        ->native(false),
                    Forms\Components\Select::make('working_status')
                        ->options([
                            'Business owner'=> 'Business Owner',
                            'goverment employee' => 'Goverment Employee',
                            'private sector' => 'Private Sector',
                        ])
                        ->native(false),
                    Forms\Components\TextInput::make('business_type')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('business_location')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('monthly_income')
                        ->required()
                        ->mask(RawJs::make('$money($input)'))
                        ->stripCharacters(','),
                    Forms\Components\Select::make('account_type')
                        ->required()
                        ->options([
                            'loan account'=> 'Loan Account',
                            'saving account'=> 'Saving Account',
                        ])
                        ->native(false),
                    Forms\Components\FileUpload::make('img_url')
                        ->directory('profiles')
                        ->avatar()
                        ->minSize(300)
                        ->minSize(300)
                        ->imageEditor(),
                    ])
               
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query->with(['branch', 'user'])->whereRelation('branch', 'company_id', auth()->user()->company_id);
            })
            ->columns([
                Tables\Columns\ImageColumn::make('img_url')
                    ->label('Passport')
                    ->circular(),
                Tables\Columns\TextColumn::make('branch.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Employee')
                    ->sortable(),
                Tables\Columns\TextColumn::make('c_number')
                    ->label('Customer ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->state(fn (?Model $record): ?string => $record->first_name .' '. $record->middle_name . " " . $record->last_name)
                    ->searchable(),
                Tables\Columns\TextColumn::make('gender'),
                Tables\Columns\TextColumn::make('phone'),
                Tables\Columns\TextColumn::make('ward'),
                Tables\Columns\TextColumn::make('street'),
                Tables\Columns\TextColumn::make('id_type'),
                Tables\Columns\TextColumn::make('id_number'),
                Tables\Columns\TextColumn::make('nick_name'),
                Tables\Columns\TextColumn::make('marital_status')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('working_status')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('business_type')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('business_location')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('monthly_income')
                    ->sortable(),
                Tables\Columns\TextColumn::make('account_type')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
