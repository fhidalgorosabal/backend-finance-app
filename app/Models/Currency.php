<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Receipt;
use App\Models\Company;

class Currency extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'initials',
        'description',
        'exchange_rate',
        'is_default',      
        'company_id',
        'active'
    ];

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
