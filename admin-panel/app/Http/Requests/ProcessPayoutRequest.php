<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class ProcessPayoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:bank_transfer,paypal,stripe,check',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'amount.required' => 'The payment amount is required.',
            'amount.numeric' => 'The payment amount must be a number.',
            'amount.min' => 'The payment amount must be at least 0.01.',

            'payment_method.required' => 'The payment method is required.',
            'payment_method.in' => 'The payment method must be: bank transfer, PayPal, Stripe or check.',

            'reference_number.string' => 'The reference number must be a string.',
            'reference_number.max' => 'The reference number may not be greater than 255 characters.',

            'notes.string' => 'The notes must be a string.',
        ];
    }    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'amount' => 'payment amount',
            'payment_method' => 'payment method',
            'reference_number' => 'reference number',
            'notes' => 'notes',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        Log::error('Payout processing validation failed', [
            'errors' => $validator->errors()->toArray(),
            'input' => $this->all()
        ]);

        parent::failedValidation($validator);
    }
}
