<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concept;
use App\Models\Currency;
use App\Models\Account;
use App\Models\Company;

class Receipt extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'date',
        'concept_id',
        'description',
        'amount',
        'currency_id',
        'actual_amount',
        'account_id',
        'company_id'
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function concept()
    {
        return $this->belongsTo(Concept::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
