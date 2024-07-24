<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'customer_id',
        'user_id',
        'transaction_account_id',
        'amount',
        'loan_amount',
        'receipt_date',
        'payer_name'
    ];
}
