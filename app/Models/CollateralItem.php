<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CollateralItem extends Model
{
    use HasFactory;

    protected $fillable = [
        "collarateral_id",
        "name",
        "current_condition",
        "current_value",
        "img_url",
    ];


    public function collateral(): BelongsTo
    {
        return $this->belongsTo(Collateral::class);
    }
}
