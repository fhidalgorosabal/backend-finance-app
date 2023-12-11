<?php

namespace App\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;
use App\Models\Setting;

class ValidateSameYear implements Rule
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
        $setting = Setting::where('company_id', auth()->user()->company_id)->first();
        $currentYear = (int) $setting->getCurrentYear();
        $selectedYear = (int) Carbon::parse($value)->format('Y');
        return $currentYear === $selectedYear;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'La :attribute debe estar en el año en curso.'; //TODO: Language change options 'The :attribute must be in the current year.'
    }
}
