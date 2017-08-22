<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeviceWriteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (request()->device) {
            return auth()->user()->can('update', request()->device);
        }
        else {
            return auth()->user()->can('create', \App\Device::class);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];

        if (!request()->device || (request()->device->user && !request()->device->user->isSuperadmin())) {
            $rules['group_id'] = 'Required';
        }

        return $rules;
    }
}
