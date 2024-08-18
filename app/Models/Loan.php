<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        "branch_id",
        "company_id",
        "user_id",
        "customer_id",
        "approved_by",
        "loan_type",
        "status",
        "health",
    ];

    public function isWithdrown(): bool
    {
        return Withdrawal::where('loan_id', $this->id)->exists();
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function loanDetails(): HasMany
    {
        return $this->hasMany(LoanDetail::class);
    }

    public function guarantors(): HasMany
    {
        return $this->hasMany(Guarantor::class);
    }

    public function localGovermentDetails(): HasMany
    {
        return $this->hasMany(LocalGovermentDetail::class);
    }

    public function collaterals(): HasMany
    {
        return $this->hasMany(Collateral::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function loanFeeRecords(): HasMany
    {
        return $this->hasMany(LoanFeeRecord::class);
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class);
    }

    public function loanRecovery(): HasOne
    {
        return $this->hasOne(LoanRecovery::class);
    }

    public function getLoanDetail(int $loanId)
    {
        return LoanDetail::where('loan_id', $loanId)->first();
    }

    public function getNextLoanreturnDate(int $loanId)
    {
        $loan = LoanDetail::where('loan_id', $loanId)->first();

        $next_loan_return_date = match ($loan->duration) {
            "daily" => now()->addDay()->format('Y-m-d'),
            'weekly' => now()->addWeek()->format('Y-m-d'),
            'monthly' => now()->addMonth()->format('Y-m-d'),
        };

        return $next_loan_return_date;
    }

}
