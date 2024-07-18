<?php

namespace App\Livewire;

use App\Filament\Resources\LoanResource;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\TransactionAccount;
use App\Models\User;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Support\RawJs;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class LoanWithdrawals extends Component implements HasForms, HasTable, HasActions
{
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;

    protected static string $view = 'filament.pages.loan-withdrawal-details';

    public ?Loan $loan;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('customer_id')
                    ->label(__(''))
                    ->options(fn () => Customer::whereRelation('branch', 'company_id', auth()->user()->company_id)->pluck('full_name', 'id'))
                    ->searchable()
                    ->preload()
                    ->prefixIcon('heroicon-o-user')
            ]);
    }


    public function createAction(): CreateAction
    {
        return CreateAction::make()
            ->model(Loan::class)
            ->label(__('Withdrawal'))
            ->form([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make('amount')
                            ->label(__('Withdrawal Amount'))
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->required(),
                        Select::make('transaction_account_id')
                            ->options(fn () => TransactionAccount::where('company_id', auth()->user()->company_id)->pluck('name', 'id'))
                            ->native(false)
                            ->required(),
                        DatePicker::make('withdrawal_date')
                            ->label(__('Withdrawal Date'))
                            ->default(now()->format('Y-m-d'))
                            ->required(),
                        TextInput::make('voucher_status')
                            ->label('Voucher Status')
                            ->required(),
                    ])
            ]);
    }

    
    public function table(Table $table): Table
    {
        return $table
            ->query(Loan::query())
            ->modifyQueryUsing(function (Builder $query) {
                return $query->with(['loanDetails'])->where([
                    ['company_id', auth()->user()->company_id],
                    ['status', 'pending']
                ]);
            })
            ->columns([
                TextColumn::make('customer.full_name')
                    ->searchable(),
                TextColumn::make('customer.phone')
                    ->label(__('Phone Number')),
                TextColumn::make('customer.branch.name')
                    ->label(__('Branch')),
                TextColumn::make('loanDetails.amount')
                    ->label('Loan Amount')
                    ->alignRight()
                    ->numeric(),
                TextColumn::make('loanDetails.duration')
                    ->label('Duration'),
                TextColumn::make('loanDetails.repayments')
                    ->label('Repayments'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (Loan $record) => match ($record->status) {
                        'pending' => 'warning',
                        'approved' => 'success',
                    }),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                ViewAction::make()
                   ->url(fn (Loan $record): string => LoanResource::getUrl('view', ['record' => $record])),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make()
            ]);
    }

    public function render()
    {
        return view('livewire.loan-withdrawals');
    }
}
