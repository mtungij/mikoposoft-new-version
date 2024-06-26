<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Flot extends Model
{
    use HasFactory;

    protected $fillable = ['branch_id', 'company_id', 'capital_id', 'to_branch_id', 'amount', 'transaction_account_id', 'withdrawal_charges'];


    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function capital(): BelongsTo
    {
        return $this->belongsTo(Capital::class);
    }

    public function toBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'to_branch_id');
    }

    public function transactionAccount(): BelongsTo
    {
        return $this->belongsTo(TransactionAccount::class);
    }
}
