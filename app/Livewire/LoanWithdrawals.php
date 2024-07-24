<?php

namespace App\Livewire;

use App\Filament\Resources\LoanResource;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\LoanFeeRecord;
use App\Models\TransactionAccount;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Support\RawJs;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Attributes\Url;
use App\Models\LoanFee;
use App\Models\LoanCategoryFee;
use App\Models\Withdrawal;
use App\Models\Flot;
use App\Models\Deposit;


class LoanWithdrawals extends Component implements HasForms, HasTable, HasActions
{
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;

    protected static string $view = 'filament.pages.loan-withdrawal-details';

    public ?Loan $loan = null;

    #[Url]
    public ?string $customer_id = ''; 

    public ?int $collection = 0;

    public ?int $withdrawal_amount = 0; 

    public ?string $vourcher_status = '';

    public ?string $loan_end_date = null;

    public ?int $loanAmount = 0;

    public function mount() 
    {
        if($this->customer_id)
            $this->loan = Loan::with(['customer', 'loanDetails.formula', 'localGovermentDetails', 'guarantors', 'collaterals'])
                                                ->where('customer_id', $this->customer_id)->latest()->first();
            $this->getCollection();
            $this->getBalanceAndStatus();
            $this->getVoucherStatus();
            $this->getLoanEndDate();
            $this->getLoanAmount();
    }


    #[On('customer-changed')]
    public function getVoucherStatus()
    {
        $withdrawalExists = Withdrawal::where('customer_id', $this->customer_id)->first();
        $this->vourcher_status = $withdrawalExists ? 'Old' : 'New';
    }

    public function getLoanAmount()
    {
        $days = $this?->loan?->loanDetails()->first()->repayments;
       if($this->customer_id) {
           if($this->loan->loanDetails()->first()->formula->name == 'straight'){
               $this->loanAmount = match ($this->loan->loanDetails()->first()->duration) {
                   "daily" => ($this->loan->loanDetails()->first()->amount * $this->loan->loanDetails()->first()->loanCategory->interest / 100 + $this->loan->loanDetails()->first()->amount),
                   "weekly" => ($this->loan->loanDetails()->first()->amount * $this->loan->loanDetails()->first()->loanCategory->interest / 100 + $this->loan->loanDetails()->first()->amount),
                   "monthly" => ($this->loan->loanDetails()->first()->amount * $this->loan->loanDetails()->first()->loanCategory->interest / 100 + $this->loan->loanDetails()->first()->amount),
               };
           } elseif($this->loan->loanDetails()->first()->formula->name == 'fraterate' || $this->loan->loanDetails()->first()->formula->name == 'reducing'){
               if ($this->loan->loanDetails()->first()->duration == "daily") {
                   $number_of_months = ceil($this->loan->loanDetails()->first()->repayments / 30);
                   $this->loanAmount = ($this->loan->loanDetails()->first()->amount * $this->loan->loanDetails()->first()->loanCategory->interest / 100) * $number_of_months + $this->loan->loanDetails()->first()->amount;
               } elseif($this->loan->loanDetails()->first()->duration == "weekly") {
                   $number_of_months = $this->loan->loanDetails()->first()->repayments < 5 ? 1 : ceil($this->loan->loanDetails()->first()->repayments / 4);
                   
                   $this->loanAmount = ($this->loan->loanDetails()->first()->amount * $this->loan->loanDetails()->first()->loanCategory->interest / 100) * $number_of_months + $this->loan->loanDetails()->first()->amount;
               } elseif("monthly") {
                   $this->loanAmount = ($this->loan->loanDetails()->first()->amount * $this->loan->loanDetails()->first()->loanCategory->interest / 100) * $days + $this->loan->loanDetails()->first()->amount;
               }
           } else {
               if ($this->loan->loanDetails()->first()->duration == "daily") {
               $number_of_months = ceil($this->loan->loanDetails()->first()->repayments / 30);
                   $this->loanAmount = $this->loan->loanDetails()->first()->amount * $this->loan->loanDetails()->first()->loanCategory->interest / 100 + $this->loan->loanDetails()->first()->amount;
               } elseif($this->loan->loanDetails()->first()->duration == "weekly") {
                   $number_of_months = $this->loan->loanDetails()->first()->repayments < 5 ? 1 : ceil($this->loan->loanDetails()->first()->repayments / 4);
                   
                   $this->loanAmount = $this->loan->loanDetails()->first()->amount * $this->loan->loanDetails()->first()->loanCategory->interest / 100 + $this->loan->loanDetails()->first()->amount;
               } elseif("monthly") {
                   $this->loanAmount = $this->loan->loanDetails()->first()->amount * $this->loan->loanDetails()->first()->loanCategory->interest / 100 + $this->loan->loanDetails()->first()->amount;
               }
           }
       } else {
           $this->loanAmount = 0;
       }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('customer_id')
                    ->label(__(''))
                    ->options(
                        fn () => Customer::whereRelation('branch', 'company_id', auth()->user()->company_id)
                                    ->whereRelation('loans', 'status', '=', 'approved')
                                    ->pluck('full_name', 'id')
                        )
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function (?string $state) {
                        $this->loan = Loan::with(['customer', 'loanDetails', 'localGovermentDetails', 'guarantors', 'collaterals'])
                                ->where('customer_id', $this->customer_id)->latest()->first();

                        $this->dispatch('customer-changed');
                        $this->getBalanceAndStatus();
                        $this->getVoucherStatus();
                        $this->getLoanEndDate();
                        $this->getLoanAmount();
                        
                        Notification::make()
                            ->title("Customer changed")
                            ->success()
                            ->send();
                        
                    })
                    ->prefixIcon('heroicon-o-user')
            ]);
    }

     public function depositAction(): CreateAction
    {
        return CreateAction::make()
            ->model(Deposit::class)
            ->label(__('Deposit'))
            ->mutateFormDataUsing(function ($data, CreateAction $action) {
                $data['user_id'] = auth()->id();
                $data['customer_id'] = $this->customer_id;
                $data['loan_id'] = $this->loan?->id;

                $float = Flot::where([
                    ['transaction_account_id', '=', $data['transaction_account_id']],
                    ['company_id', '=', auth()->user()->company_id],
                    ])->first();

                if($float->amount < $data['amount']) {
                    Notification::make()
                        ->title("Insufficient float balance. Please top up your float account.")
                        ->warning()
                        ->send();
                    $action->halt();
                } else {
                    $float->decrement('amount', $data['amount']);
                }
                
                return $data;
            })
            ->form([
                Section::make()
                    ->columns(3)
                    ->schema([
                        TextInput::make('loan_amount')
                            ->label(__('Loan Amount'))
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->default($this->loanAmount)
                            ->readOnly()
                            ->required(),
                        TextInput::make('collection')
                            ->label(__('Collection'))
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->readOnly()
                            ->required(),
                        TextInput::make('dept')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->readOnly()
                            ->required(),
                        TextInput::make('recovery_amount')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->readOnly()
                            ->required(),
                        TextInput::make('last_payment')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->readOnly()
                            ->required(),
                        TextInput::make('vourcher_status')
                            ->label(__('Voucher Status')),
                        DatePicker::make('receipt_date')
                            ->label(__('Receipt Date'))
                            ->required(),
                        TextInput::make('amount')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->readOnly()
                            ->required(),
                        Select::make('transaction_account_id')
                            ->label('Transaction Account')
                            ->options(fn () => TransactionAccount::where('company_id', auth()->user()->company_id)->pluck('name', 'id'))
                            ->native(false)
                            ->required(),
                        TextInput::make('payer_name'),
                    ])
            ]);
    }

    public function withdrawAction(): CreateAction
    {
        return CreateAction::make()
            ->model(Withdrawal::class)
            ->label(__('Withdrawal'))
            ->mutateFormDataUsing(function ($data, CreateAction $action) {
                $data['user_id'] = auth()->id();
                $data['customer_id'] = $this->customer_id;
                $data['loan_id'] = $this->loan?->id;

                $float = Flot::where([
                    ['transaction_account_id', '=', $data['transaction_account_id']],
                    ['company_id', '=', auth()->user()->company_id],
                    ])->first();

                if($float->amount < $data['amount']) {
                    Notification::make()
                        ->title("Insufficient float balance. Please top up your float account.")
                        ->warning()
                        ->send();
                    $action->halt();
                } else {
                    $float->decrement('amount', $data['amount']);
                }
                
                return $data;
            })
            ->form([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make('amount')
                            ->label(__('Withdrawal Amount'))
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->default($this->withdrawal_amount)
                            ->readOnly()
                            ->live()
                            ->required(),
                        Select::make('transaction_account_id')
                            ->options(fn () => TransactionAccount::where('company_id', auth()->user()->company_id)->pluck('name', 'id'))
                            ->native(false)
                            ->required(),
                        DatePicker::make('date')
                            ->label(__('Withdrawal Date'))
                            ->default(now()->format('Y-m-d'))
                            ->required(),
                        TextInput::make('status')
                            ->label('Voucher Status')
                            ->required()
                            ->default($this->vourcher_status),
                    ])
            ]);
    }


    public function getLoanEndDate()
    {
        $duration = $this->loan?->loanDetails()->first()->duration;
        $repayments = $this->loan?->loanDetails()->first()->repayments;
        $withdrawal = Withdrawal::where('loan_id', $this->loan?->id)->first();

        if($withdrawal) {
            $this->loan_end_date = match ($duration) {
                "daily" => $withdrawal->created_at->addDays($repayments)->format('Y-m-d'),
                'weekly' => $withdrawal->created_at->addWeeks($repayments)->format('Y-m-d'),
                'monthly' => $withdrawal->created_at->addMonths($repayments)->format('Y-m-d'),
            };
        } else {
            $this->loan_end_date = null;
        }
    }

    // #[On('customer-changed')]
    public function getBalanceAndStatus()
    {
        $loanFee = LoanFee::where('company_id', auth()->user()->company_id)->oldest()->first();

        $loan_fees = ($loanFee && $loanFee->category == 'general') ?
                            LoanFee::where('company_id', auth()->user()->company_id)->get() : 
                            LoanCategoryFee::whereRelation('loanCategory', 'company_id', auth()->user()->company_id)->get();

         $balance = $this?->loan?->loanDetails()->first()->amount ?? 0;
         if($this->customer_id) {
            foreach ($loan_fees as $loan_fee) {
                switch ($loan_fee->fee_type) {
                    case 'money':
                        $balance -= $loan_fee->fee_amount;
                        break;
                    case 'percentage':
                        $balance -= $this->loan?->loanDetails()?->first()?->amount * ($loan_fee?->fee_amount / 100);
                        break;
                }
            }

            
        }
        $this->withdrawal_amount = $balance;
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
                    ->color(fn (Loan $record) => match ($this->loan->loanDetails()->first()->status) {
                        'pending' => 'warning',
                        'approved' => 'success',
                    }),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                ViewAction::make()
                   ->url(fn (Loan $loan): string => LoanResource::getUrl('view', ['record' => $this->loan->loanDetails()->first()])),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make()
            ]);
    }

    #[On('customer-changed')]
    public function getCollection()
    {
       $days = $this?->loan?->loanDetails()->first()->repayments;
       if($this->customer_id) {
           if($this->loan->loanDetails()->first()->formula->name == 'straight'){
               $this->loanAmount = match ($this->loan->loanDetails()->first()->duration) {
                   "daily" => ($this->loan->loanDetails()->first()->amount * $this->loan->loanDetails()->first()->loanCategory->interest / 100 + $this->loan->loanDetails()->first()->amount) ,
                   "weekly" => ($this->loan->loanDetails()->first()->amount * $this->loan->loanDetails()->first()->loanCategory->interest / 100 + $this->loan->loanDetails()->first()->amount) ,
                   "monthly" => ($this->loan->loanDetails()->first()->amount * $this->loan->loanDetails()->first()->loanCategory->interest / 100 + $this->loan->loanDetails()->first()->amount),
               };
           } elseif($this->loan->loanDetails()->first()->formula->name == 'fraterate' || $this->loan->loanDetails()->first()->formula->name == 'reducing'){
               if ($this->loan->loanDetails()->first()->duration == "daily") {
                   $number_of_months = ceil($this->loan->loanDetails()->first()->repayments / 30);
                   $this->loanAmount = (($this->loan->loanDetails()->first()->amount * $this->loan->loanDetails()->first()->loanCategory->interest / 100 ) * $number_of_months + $this->loan->loanDetails()->first()->amount) / $this->loan->loanDetails()->first()->repayments;
               } elseif($this->loan->loanDetails()->first()->duration == "weekly") {
                   $number_of_months = $this->loan->loanDetails()->first()->repayments < 5 ? 1 : ceil($this->loan->loanDetails()->first()->repayments / 4);
                   
                   $this->loanAmount = (($this->loan->loanDetails()->first()->amount * $this->loan->loanDetails()->first()->loanCategory->interest / 100 ) * $number_of_months + $this->loan->loanDetails()->first()->amount) / $this->loan->loanDetails()->first()->repayments;
               } elseif("monthly") {
                   $this->loanAmount = (($this->loan->loanDetails()->first()->amount * $this->loan->loanDetails()->first()->loanCategory->interest / 100 ) * $days + $this->loan->loanDetails()->first()->amount) / $this->loan->loanDetails()->first()->repayments;
               }
           } else {
               if ($this->loan->loanDetails()->first()->duration == "daily") {
               $number_of_months = ceil($this->loan->loanDetails()->first()->repayments / 30);
                   $this->loanAmount = ($this->loan->loanDetails()->first()->amount * $this->loan->loanDetails()->first()->loanCategory->interest / 100 + $this->loan->loanDetails()->first()->amount) / $this->loan->loanDetails()->first()->repayments;
               } elseif($this->loan->loanDetails()->first()->duration == "weekly") {
                   $number_of_months = $this->loan->loanDetails()->first()->repayments < 5 ? 1 : ceil($this->loan->loanDetails()->first()->repayments / 4);
                   
                   $this->loanAmount = ($this->loan->loanDetails()->first()->amount * $this->loan->loanDetails()->first()->loanCategory->interest / 100 + $this->loan->loanDetails()->first()->amount);
               } elseif("monthly") {
                   $this->loanAmount = ($this->loan->loanDetails()->first()->amount * $this->loan->loanDetails()->first()->loanCategory->interest / 100 + $this->loan->loanDetails()->first()->amount);
               }
           }
       } else {
           $this->loanAmount = 0;
       }
    }

    public function render()
    {
        $loan_fees = [];
        $withdrawal = Withdrawal::with(['user', 'transactionAccount'])->where('loan_id', $this->loan?->id)->first();

        $loanFee = LoanFee::where('company_id', auth()->user()->company_id)->oldest()->first();
        if($this->customer_id) {
            $loanFeedRecordExists = LoanFeeRecord::where('loan_id', $this->loan->id)->first();
            $loan_fees = ($loanFee && $loanFee->category == 'general') ?
                        LoanFee::where('company_id', auth()->user()->company_id)->get() : 
                        LoanCategoryFee::whereRelation('loanCategory', 'company_id', auth()->user()->company_id)->get();
            if($loanFeedRecordExists) {
                $loan_fees = LoanFeeRecord::where('loan_id', $this->loan->id)->get();
            } else {
                //create loan fee records from loan fees
                $this->loan->loanFeeRecords()->createMany($loan_fees->toArray());
                //refetch the loan fee records
                $loan_fees = LoanFeeRecord::where('loan_id', $this->loan->id)->get();
            }
        }

        return view('livewire.loan-withdrawals', [
            'loan_fees' => $loan_fees,
            'withdrawal' => $withdrawal,
        ]);
    }
}
