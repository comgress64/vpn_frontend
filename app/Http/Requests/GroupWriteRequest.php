<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GroupWriteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (request()->group) {
            return auth()->user()->can('update', request()->group);
        }
        else {
            return auth()->user()->can('create', \App\Group::class);
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
            'name' => ['Required'],
        ];
    }
}
