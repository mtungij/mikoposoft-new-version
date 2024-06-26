<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'phone', 'email', 'region_id', 'status'];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function LoanCategories(): HasMany
    {
        return $this->hasMany(LoanCategory::class);
    }

    public function LoanFees(): HasMany
    {
        return $this->hasMany(LoanFee::class);
    }

    public function manyLoanCategories(): BelongsToMany{
        return $this->belongsToMany(LoanCategory::class);
    }

    public function penalts(): HasMany
    {
        return $this->hasMany(Penalt::class);
    }


    public function transactionAccounts(): HasMany
    {
        return $this->hasMany(TransactionAccount::class);
    }

    public function formulas(): HasMany
    {
        return $this->hasMany(Formula::class);
    }

    public function capitals(): HasMany
    {
        return $this->hasMany(Capital::class);
    }

    public function flots(): HasMany
    {
        return $this->hasMany(Flot::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
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

    public function loanDetails(): HasMany
    {
        return $this->hasMany(LoanDetail::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function company(): HasOne
    {
        return $this->hasOne(Company::class);
    }
}
