<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        "c_number",
        "branch_id",
        "user_id",
        "first_name",
        "middle_name",
        "last_name",
        "gender",
        "phone",
        "ward",
        "street",
        "id_type",
        "id_number",
        "nick_name",
        "marital_status",
        "working_status",
        "business_type",
        "business_location",
        "monthly_income",
        "account_type",
        "img_url",
        "status",
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
}
