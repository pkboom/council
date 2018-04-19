<?php

namespace App\Rules;

use Zttp\Zttp;
use Illuminate\Contracts\Validation\Rule;

class Recaptcha implements Rule
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
        return Zttp::asFormParams()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret'),
            'response' => $value,
            // 'remoteIp' => $_SERVER['REMOTE_ADDR']
            'remoteIp' => request()->ip()
            ])->json()['success'];
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The recaptcha verification failed. Try again.';
    }

    public static function isInTestMode()
    {
        return ! Zttp::asFormParams()->post('https://www.google.com/recaptcha/api/siteverify', [
            // 'secret' => config('services.recaptcha.secret'),
            'secret' => '',
            'response' => 'test',
            'remoteip' => request()->ip()
        ])->json()['success'];
    }
}
