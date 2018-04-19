<?php

namespace App\Http\Requests;

use App\Reply;
use App\Rules\SpamFree;
use App\Exceptions\ThrottleException;
use Illuminate\Foundation\Http\FormRequest;

class CreatePostRequest extends FormRequest
{
    // Gate allows that we can create a new reply
    public function authorize()
    {
        return \Gate::allows('create', Reply::class);
    }

    protected function failedAuthorization()
    {
        throw new ThrottleException('Your are posting too frequently. Please take a break.');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'body' => ['required', new SpamFree]
        ];
    }
}
