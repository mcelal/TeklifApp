<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProposalRequest extends FormRequest
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
            'customer.id'     => 'required|exists:App\Models\Customer,id',
            'customer.title'  => 'required',
            'customer.email'  => 'required',
            'cars.*.id'       => 'required|exists:App\Models\Car,id',
            'cars.*.title'    => 'required',
            'cars.*.quantity' => 'required|numeric',
            'cars.*.price'    => 'required'
        ];
    }
}
