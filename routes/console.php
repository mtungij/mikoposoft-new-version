<?php

use App\Models\Deposit;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::call(function () {
    $todayDeposits = DB::table('deposits')->where('checked_by', 'customer')->whereDate('receipt_date', today())->get();
    if($todayDeposits) {
        foreach ($todayDeposits as $deposit) {
            $depositExists = DB::table('deposits')
                    ->where([['checked_by', 'system'], ['customer_id', $deposit->customer_id]])
                    ->whereDate('receipt_date', today())
                    ->first();
            $yesteriday_deposit = DB::table('deposits')
                    ->where([['checked_by', 'system']])
                    ->whereDate('receipt_date', today()->subDay())
                    ->first();

            $today_deposit_data = [
                'loan_id' => $deposit->loan_id,
                'customer_id' => $deposit->customer_id,
                'transaction_account_id' => $deposit->transaction_account_id,
                'user_id' => $deposit->user_id,
                'amount' => 0,
                
                'collection' => $deposit->collection,
                'loan_amount' => $deposit->loan_amount,
                'checked_by' => 'system',
                'receipt_date' => $deposit->receipt_date,
                'payer_name' => $deposit->payer_name,
            ];

            if($yesteriday_deposit) {
                $today_deposit_data['balance'] += $yesteriday_deposit->balance;
            }

            $today_deposit_data['withdraw'] = $deposit->balance > $deposit->collection ? $deposit->collection: $deposit->balance;
            $today_deposit_data['balance'] = $deposit->balance > $deposit->collection ? $deposit->amount - $deposit->collection: 0;

            if($depositExists) {
                continue;
            }

            Deposit::create($today_deposit_data);
        }
    }
})->everyTwoMinutes();