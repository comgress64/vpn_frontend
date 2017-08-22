<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserWriteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (request()->user) {
            return auth()->user()->can('update', request()->user);
        }
        else {
            return auth()->user()->can('create', \App\User::class);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'fname' => ['Required'],
            'lname' => ['Required'],
            'email' => ['Required', 'Email'],
            'password' => ['RequiredWithout:id', 'Confirmed'],
            'max_devices' => ['Nullable', 'Integer'],
            'group_ids' => ['Array','RequiredUnless:role,superadmin'],
        ];
    }
}
