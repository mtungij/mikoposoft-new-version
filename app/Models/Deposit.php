<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'customer_id',
        'user_id',
        'desc',
        'status',
        'transaction_account_id',
        'amount',
        'withdraw',
        'balance',
        'checked_by',
        'checked',
        'loan_amount',
        'collection',
        'payer_name',
        'receipt_date',
        'end_date',
        'next_return_date',
        'duration',
        'repayments',
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactionAccount(): BelongsTo
    {
        return $this->belongsTo(TransactionAccount::class);
    }
}
