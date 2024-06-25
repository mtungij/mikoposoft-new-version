<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guarantor extends Model
{
    use HasFactory;

    protected $fillable = ['branch_id'];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function guarantors(): HasMany
    {
        return $this->hasMany(Guarantor::class);
    }
}
