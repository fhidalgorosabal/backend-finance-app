<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Account;

class Bank extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'swift',
        'bank_name',
        'cis',
        'branch_name',
        'address',
        'phone_number',
        'email',
        'active'
    ];

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }
}
