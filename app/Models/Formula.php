<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Formula extends Model
{
    use HasFactory;


    protected $fillable = ['branch_id', 'name'];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function loanDetails(): HasMany
    {
        return $this->hasMany(LoanDetail::class);
    }
}
