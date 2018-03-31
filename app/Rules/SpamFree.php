<?php

namespace App\Rules;

use App\Inspections\Spam;
use Illuminate\Contracts\Validation\Rule;

class SpamFree implements Rule
{
    public function __construct()
    {
        //
    }

    public function passes($attribute, $value)
    {
        try {
            // We're going to return what it passes
            // As long as it doesn't detect any spam
            return ! app(Spam::class)->detect($value);
            //  == return !resolve(Spam::class)->detect($value);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function message()
    {
        return 'The :attribute contains spam.';
    }
}
