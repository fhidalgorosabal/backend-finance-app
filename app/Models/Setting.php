<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use App\Models\Company;
use App\Models\ReceiptHistory;
use App\Models\Receipt;

class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'current_month',
        'current_year',
        'company_id'
    ];    

    /**
     * Get current month
     * 
     * @return string
     */
    public function getCurrentMonth()
    {
        return $this->current_month;
    }
 
    /**
     * Get current year
     * 
     * @return string
     */
    public function getCurrentYear()
    {
        return $this->current_year;
    }
     
    /**
     * Get company id
     * 
     * @return string
     */
    public function getCompanyId()
    {
        return $this->company_id;
    }
     
    /**
     * One setting to one company.
     * 
     * @return string
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Close year
     * 
     * @return void
     */
    public function closeYear()
    {
        $currentYear = $this->current_year;
        $companyId = $this->company_id;

        $historyTable = 'receipts_history_' . $currentYear;
        if (!Schema::hasTable($historyTable)) {
            Schema::create($historyTable, function ($table) {
                $table->id();
                $table->bigInteger('receipt_id');
                $table->date('date');
                $table->string('concept_description', 100);
                $table->enum('concept_type', ['Expense', 'Ingress']);
                $table->string('description', 150)->nullable();
                $table->decimal('amount', 10, 2);
                $table->string('amount_currency_initials', 3);
                $table->decimal('actual_amount', 10, 2);
                $table->string('actual_amount_currency_initials', 3);
                $table->unsignedBigInteger('company_id');
                $table->timestamps();

                $table->foreign('company_id')->references('id')->on('companies');
            });

            ReceiptHistory::copyReceiptsToHistory($historyTable, $currentYear, $companyId);
            Receipt::where('company_id', $companyId)->delete();
        }

        $this->current_year = $currentYear + 1;
        $this->current_month = 1;
        $this->save();
    }

}
