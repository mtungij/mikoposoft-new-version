<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Capital extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'branch_id', 'company_id', 'transaction_account_id', 'amount'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function transactionAccount(): BelongsTo
    {
        return $this->belongsTo(TransactionAccount::class);
    }

    public function flots(): HasMany
    {
        return $this->hasMany(Flot::class);
    }
}
