<?php

use App\Models\Deposit;
use App\Models\Customer;
use App\Models\LoanRecovery;
use App\Models\Loan;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::call(function () {
    $loan = new Loan();
    $customers = Customer::with(['deposits' => function (Builder $query) {
                            return $query->where('checked', 'no')->whereDate('end_date', '<=', today());
                        }])->where('status', 'withdrawal')->get();                        
                            
    if($customers) {
        foreach ($customers as $customer) {
            $lastDeposit = $customer?->deposits()?->latest()->first();

            if($lastDeposit && $lastDeposit->next_return_date == date('Y-m-d')) {
                $today_deposit_data = [
                    'loan_id' => $lastDeposit->loan_id,
                    'customer_id' => $lastDeposit->customer_id,
                    'transaction_account_id' => $lastDeposit->transaction_account_id,
                    'user_id' => $lastDeposit->user_id,
                    'desc' => "system/ loan return/ {$loan->getLoanDetail($lastDeposit->loan_id)->duration} ({$loan->getLoanDetail($lastDeposit->loan_id)->repayments})",
                    'amount' => 0,
                    'withdraw' => $lastDeposit->balance > $lastDeposit->collection ? $lastDeposit->collection: $lastDeposit->balance,
                    'balance' => $lastDeposit->balance > $lastDeposit->collection ? $lastDeposit->balance - $lastDeposit->collection: 0,
                    'collection' => $lastDeposit->collection,
                    'loan_amount' => $lastDeposit->loan_amount,
                    'checked_by' => 'system',
                    'checked' => 'yes',
                    'duration' => $lastDeposit->duration,
                    'repayments' => $lastDeposit->repayments,
                    'end_date' => $lastDeposit->end_date,
                    'next_return_date' => $loan->getNextLoanreturnDate($lastDeposit->loan_id),
                    'receipt_date' => $lastDeposit->receipt_date,
                    'payer_name' => $lastDeposit->payer_name,
                ];
    
    
                $loanRecovery = LoanRecovery::where('loan_id', $lastDeposit->loan_id)->first();
    
                $amount = $lastDeposit->collection - $lastDeposit->balance;
    
                if($lastDeposit->balance < $lastDeposit->collection) {
                    if($loanRecovery) {
                        $loanRecovery->increment('amount', $amount);
                    }
                    LoanRecovery::create(['loan_id' => $lastDeposit->loan_id, 'amount' => $amount]);
                }
                $lastDeposit->update(['checked' => 'yes']);
    
                $systemDeposit = Deposit::create($today_deposit_data);
    
                // sub the balance of todays deposit
                // $lastDeposit->decrement('balance', $lastDeposit->balance);
            }
        }
    }
})->everyMinute();
