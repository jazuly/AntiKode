<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OutletRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "name" => "required|string",
            "picture" => "nullable|image",
            "address" => "nullable|string",
            "longitude" => "required|numeric",
            "latitude" => "required|numeric",
            "brand_id" => "required|numeric|exists:brands,id",
        ];
    }
}
