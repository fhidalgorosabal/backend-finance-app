<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Receipt;
use App\Models\Company;

class Concept extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'description',
        'type',        
        'company_id'
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
