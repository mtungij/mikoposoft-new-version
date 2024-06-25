<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        "branch_id",
        "user_id",
        "guarantor_id",
        "loan_detail_id",
        "local_goverment_detail_id",
        "collateral_id",
        "customer_id",
        "loan_type",
        "status",
    ];

    public function branch(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function loanDetail(): BelongsTo
    {
        return $this->belongsTo(LoanDetail::class);
    }

    public function localGovermentDetail(): BelongsTo
    {
        return $this->belongsTo(LocalGovermentDetail::class);
    }

    public function collateral(): BelongsTo
    {
        return $this->belongsTo(Collateral::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
