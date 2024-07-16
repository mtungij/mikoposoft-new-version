<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanCategoryFee extends Model
{
    use HasFactory;

    protected $fillable = ['loan_category_id', 'company_id', 'loan_fee_id', 'fee_amount', 'fee_type', 'desc'];


    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function loanCategory(): BelongsTo
    {
        return $this->belongsTo(LoanCategory::class);
    }

    public function LoanFee(): BelongsTo
    {
        return $this->belongsTo(LoanFee::class);
    }
}
