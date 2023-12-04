<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Receipt;
use App\Models\Concept;
use App\Models\Currency;
use App\Models\Account;
use App\Models\User;
use App\Models\Setting;

class Company extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_code',
        'company_name'
    ];

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }

    public function concepts()
    {
        return $this->hasMany(Concept::class);
    }

    public function currencies()
    {
        return $this->hasMany(Currency::class);
    }

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function setting()
    {
        return $this->belongsTo(Setting::class);
    }
}
