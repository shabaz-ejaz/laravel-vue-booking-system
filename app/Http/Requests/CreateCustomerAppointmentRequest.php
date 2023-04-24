<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateCustomerAppointmentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['string', 'max:255'],
            'email' => ['email', 'max:255'],
            'phone' => ['numeric'],
            'vehicle_make' => ['string', 'max:255'],
            'vehicle_model' => ['string', 'max:255'],
            'date' => ['date', 'required'],
            'start_at' => ['date_format:H:i:s', 'required'],
        ];
    }
}
