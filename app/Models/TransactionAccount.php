<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TransactionAccount extends Model
{
    use HasFactory;

    protected $fillable = ['branch_id', 'company_id', 'name'];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function capitals(): HasMany
    {
        return $this->hasMany(Capital::class);
    }

    public function flots(): HasOne
    {
        return $this->hasOne(Flot::class);
    }
}
