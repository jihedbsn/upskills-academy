<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class StoreAffiliatePartnerRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:affiliate_partners,email',
            'phone' => 'nullable|string|max:50',
            'company' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:500',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'payment_method' => 'nullable|string|max:100',
            'payment_details' => 'nullable|string',
            'payout_threshold' => 'nullable|numeric|min:0',
            'marketing_channels' => 'nullable|array',
            'audience_size' => 'nullable|string|max:100',
            'social_media' => 'nullable|array',
            'social_media.facebook' => 'nullable|url|max:500',
            'social_media.instagram' => 'nullable|url|max:500',
            'social_media.twitter' => 'nullable|url|max:500',
            'social_media.linkedin' => 'nullable|url|max:500',
            'social_media.youtube' => 'nullable|url|max:500',
            'notes' => 'nullable|string'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The partner name is required.',
            'name.string' => 'The partner name must be a string.',
            'name.max' => 'The partner name may not be greater than 255 characters.',

            'email.required' => 'The email address is required.',
            'email.email' => 'The email address must be valid.',
            'email.unique' => 'This email address is already used by another partner.',

            'phone.string' => 'The phone number must be a string.',
            'phone.max' => 'The phone number may not be greater than 50 characters.',

            'company.string' => 'The company name must be a string.',
            'company.max' => 'The company name may not be greater than 255 characters.',

            'website.url' => 'The website URL must be valid.',
            'website.max' => 'The website URL may not be greater than 500 characters.',

            'commission_rate.required' => 'The commission rate is required.',
            'commission_rate.numeric' => 'The commission rate must be a number.',
            'commission_rate.min' => 'The commission rate cannot be negative.',
            'commission_rate.max' => 'The commission rate may not be greater than 100%.',

            'payment_method.string' => 'The payment method must be a string.',
            'payment_method.max' => 'The payment method may not be greater than 100 characters.',

            'payment_details.string' => 'The payment details must be a string.',

            'payout_threshold.numeric' => 'The payout threshold must be a number.',
            'payout_threshold.min' => 'The payout threshold cannot be negative.',

            'marketing_channels.array' => 'The marketing channels must be an array.',

            'audience_size.string' => 'The audience size must be a string.',
            'audience_size.max' => 'The audience size may not be greater than 100 characters.',

            'social_media.array' => 'The social media must be an array.',
            'social_media.facebook.url' => 'The Facebook URL must be valid.',
            'social_media.facebook.max' => 'The Facebook URL may not be greater than 500 characters.',
            'social_media.instagram.url' => 'The Instagram URL must be valid.',
            'social_media.instagram.max' => 'The Instagram URL may not be greater than 500 characters.',
            'social_media.twitter.url' => 'The Twitter URL must be valid.',
            'social_media.twitter.max' => 'The Twitter URL may not be greater than 500 characters.',
            'social_media.linkedin.url' => 'The LinkedIn URL must be valid.',
            'social_media.linkedin.max' => 'The LinkedIn URL may not be greater than 500 characters.',
            'social_media.youtube.url' => 'The YouTube URL must be valid.',
            'social_media.youtube.max' => 'The YouTube URL may not be greater than 500 characters.',

            'notes.string' => 'The notes must be a string.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'partner name',
            'email' => 'email address',
            'phone' => 'phone number',
            'company' => 'company name',
            'website' => 'website',
            'commission_rate' => 'commission rate',
            'payment_method' => 'payment method',
            'payment_details' => 'payment details',
            'payout_threshold' => 'payout threshold',
            'marketing_channels' => 'marketing channels',
            'audience_size' => 'audience size',
            'social_media' => 'social media',
            'social_media.facebook' => 'Facebook',
            'social_media.instagram' => 'Instagram',
            'social_media.twitter' => 'Twitter',
            'social_media.linkedin' => 'LinkedIn',
            'social_media.youtube' => 'YouTube',
            'notes' => 'notes',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        Log::error('Affiliate partner creation validation failed', [
            'errors' => $validator->errors()->toArray(),
            'input' => $this->all()
        ]);

        parent::failedValidation($validator);
    }
}
