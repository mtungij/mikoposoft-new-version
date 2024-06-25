<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuarantorItem extends Model
{
    use HasFactory;

    protected $fillable = [
        "guarantor_id",
        "name",
        "phone",
        "relationship",
        "street",
        "business_name",
    ];


    public function guarantor(): BelongsTo
    {
        return $this->belongsTo(Guarantor::class);
    }
}
