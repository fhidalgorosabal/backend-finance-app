<?php

namespace App\Http\Utils;

use App\Models\Currency;

class Utils {

    public static function getExchangeRate($currency_id)
    {
        $currency = Currency::find($currency_id);

        return $currency ? $currency->exchange_rate : 1;
    }

}
