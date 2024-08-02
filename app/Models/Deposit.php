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
        'transaction_account_id',
        'amount',
        'withdraw',
        'balance',
        'checked_by',
        'loan_amount',
        'collection',
        'receipt_date',
        'payer_name'
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
