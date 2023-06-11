<?php

namespace App\Http\Utils;

use App\Models\Currency;
use App\Models\Concept;
use App\Models\Receipt;

class Utils {

    public static function getExchangeRate($currencyId)
    {
        $currency = Currency::find($currencyId);

        return $currency ? $currency->exchange_rate : 1;
    }

    public static function conceptType($conceptId)
    {
        $concept = Concept::find($conceptId);

        return $concept ? $concept->type : '';
    }

    public static function totalAmountOfAccount($accountId, $conceptType)
    {
        return Receipt::where('account_id', $accountId)
            ->whereHas('concept', function ($query) use ($conceptType) {
                $query->where('type', $conceptType);
            })
            ->sum('actual_amount');
    }

}
