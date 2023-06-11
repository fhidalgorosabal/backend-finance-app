<?php

namespace App\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class ValidateSameMonth implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $currentMonth = Carbon::now()->format('Y-m');
        $selectedMonth = Carbon::parse($value)->format('Y-m');
        return $currentMonth === $selectedMonth;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'La :attribute debe estar en el mes en curso.'; //TODO: Language change options 'The :attribute must be in the current month.'
    }
}
