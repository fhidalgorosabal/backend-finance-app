<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ReceiptHistory extends Model
{
    use HasFactory;

    /**
     * The table name.
     *
     * @var string
     */
    protected $table;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'receipt_id',
        'date',
        'concept_description',
        'concept_type',
        'description',
        'amount',
        'amount_currency_initials',
        'actual_amount',
        'actual_amount_currency_initials',
        'company_id',
    ];

    /**
     * The constructor.
     *
     * @param array<int, string> $attributes
     * @param string|null $year
     */
    public function __construct(array $attributes = [], $year = null)
    {
        parent::__construct($attributes);
        $this->setTableFromYear($year);
    }

    /**
     * Set the table name based on the provided or fetched year.
     *
     * @param string|null $year
     * @return void
     */
    private function setTableFromYear($year = null)
    {
        $currentYear = $year ?? Setting::value('current_year');
        $this->table = 'receipts_history_' . $currentYear;
    }

    /**
     * Copy receipts to receipts history.
     *
     * @param string $historyTable
     * @param string $currentYear
     * @param string $companyId
     * @return void
     */
    public static function copyReceiptsToHistory($historyTable, $currentYear, $companyId)
    {
        DB::table('receipts')
            ->whereYear('date', $currentYear)
            ->where('receipts.company_id', $companyId)
            ->join('concepts', 'receipts.concept_id', '=', 'concepts.id')
            ->join('currencies', 'receipts.currency_id', '=', 'currencies.id')
            ->select(
                'receipts.id',
                'receipts.date',
                'concepts.description as concept_description',
                'concepts.type as concept_type',
                'receipts.description',
                'receipts.amount',
                'currencies.initials as amount_currency_initials',
                'receipts.actual_amount',
                'currencies_default.initials as actual_amount_currency_initials'
            )
            ->leftJoin('currencies as currencies_default', function ($join) {
                $join->on('currencies_default.company_id', '=', 'receipts.company_id')
                    ->where('currencies_default.is_default', true);
            })
            ->orderBy('receipts.date') 
            ->chunk(200, function ($receipts) use ($historyTable, $companyId) {
                foreach ($receipts as $receipt) {
                    DB::table($historyTable)->insert([
                        'receipt_id' => $receipt->id,
                        'date' => $receipt->date,
                        'concept_description' => $receipt->concept_description,
                        'concept_type' => $receipt->concept_type,
                        'description' => $receipt->description,
                        'amount' => $receipt->amount,
                        'amount_currency_initials' => $receipt->amount_currency_initials,
                        'actual_amount' => $receipt->actual_amount,
                        'actual_amount_currency_initials' => $receipt->actual_amount_currency_initials,
                        'company_id' => $companyId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });
    }
}
