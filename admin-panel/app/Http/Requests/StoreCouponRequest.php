<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class StoreCouponRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert checkbox "1" or "on" values to boolean
        $mergeData = [
            'is_active' => $this->has('is_active') && ($this->input('is_active') === '1' || $this->input('is_active') === 'on') ? true : false,
            'is_stackable' => $this->has('is_stackable') && ($this->input('is_stackable') === '1' || $this->input('is_stackable') === 'on') ? true : false,
            'auto_apply' => $this->has('auto_apply') && ($this->input('auto_apply') === '1' || $this->input('auto_apply') === 'on') ? true : false,
            'is_public' => $this->has('is_public') && ($this->input('is_public') === '1' || $this->input('is_public') === 'on') ? true : false,
            'first_purchase_only' => $this->has('first_purchase_only') && ($this->input('first_purchase_only') === '1' || $this->input('first_purchase_only') === 'on') ? true : false,
            'cumulative_enabled' => $this->has('cumulative_enabled') && ($this->input('cumulative_enabled') === '1' || $this->input('cumulative_enabled') === 'on') ? true : false,
            'allow_multiple_uses' => $this->has('allow_multiple_uses') && ($this->input('allow_multiple_uses') === '1' || $this->input('allow_multiple_uses') === 'on') ? true : false,
        ];

        // Map custom_message to description field since that's what exists in database
        if ($this->has('custom_message') && !empty($this->input('custom_message'))) {
            $mergeData['description'] = $this->input('custom_message');
        }

        $this->merge($mergeData);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'nom' => 'required|string|max:255|unique:coupons,nom',
            'code' => 'nullable|string|max:50|unique:coupons,code|regex:/^[A-Z0-9]+$/',
            'description' => 'nullable|string|max:1000',
            'valeur' => 'required|numeric|min:0',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'type' => 'required|in:percentage,fixed',
            'limit_utilise' => 'nullable|integer|min:1',
            'customer_type' => 'required|in:all,new,returning,vip',
            'product_application' => 'required|in:all,selected,category',
            'min_purchase_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'affiliate_partner_id' => 'nullable|exists:affiliate_partners,id',
            'commission_rate' => 'nullable|required_with:affiliate_partner_id|numeric|min:0|max:100',

            // Advanced settings
            'is_active' => 'nullable|boolean',
            'is_stackable' => 'nullable|boolean',
            'stack_priority' => 'nullable|integer|min:1|max:10',
            'auto_apply' => 'nullable|boolean',
            'auto_apply_min_cart_amount' => 'nullable|numeric|min:0',
            'auto_apply_min_items' => 'nullable|integer|min:1',
            'auto_apply_specific_products' => 'nullable|array',
            'auto_apply_specific_products.*' => 'exists:products,id',
            'auto_apply_course_types' => 'nullable|array',
            'auto_apply_course_types.*' => 'string',
            'min_items' => 'nullable|integer|min:1',
            'min_cart_items' => 'nullable|integer|min:0',
            'course_types' => 'nullable|string',
            'max_uses_per_user' => 'nullable|integer|min:1',
            'is_public' => 'nullable|boolean',
            'custom_message' => 'nullable|string|max:1000',
            'usage_limit' => 'nullable|integer|min:1',
            'first_purchase_only' => 'nullable|boolean',
            'cumulative_enabled' => 'nullable|boolean',
            'allow_multiple_uses' => 'nullable|boolean',
        ];

        // Conditional validation based on product application type
        $productApplication = $this->input('product_application');

        if ($productApplication === 'selected') {
            $rules['products'] = 'required|array|min:1';
            $rules['products.*'] = 'exists:products,id';
        } elseif ($productApplication === 'category') {
            $rules['categories'] = 'required|array|min:1';
            $rules['categories.*'] = 'exists:categories,id';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nom.required' => 'The coupon name is required.',
            'nom.max' => 'The coupon name may not be greater than 255 characters.',
            'nom.unique' => 'A coupon with this name already exists. Please choose a different name.',
            'code.unique' => 'This coupon code is already taken.',
            'code.max' => 'The coupon code may not be greater than 50 characters.',
            'code.regex' => 'The coupon code may only contain uppercase letters and numbers.',
            'valeur.required' => 'The coupon value is required.',
            'valeur.numeric' => 'The coupon value must be a number.',
            'valeur.min' => 'The coupon value must be positive.',
            'date_debut.required' => 'The start date is required.',
            'date_fin.required' => 'The end date is required.',
            'date_fin.after' => 'The end date must be after the start date.',
            'type.required' => 'The coupon type is required.',
            'type.in' => 'The coupon type must be either "percentage" or "fixed".',
            'customer_type.required' => 'The customer type is required.',
            'customer_type.in' => 'The customer type must be: all, new, returning, or vip.',
            'product_application.required' => 'The product application is required.',
            'product_application.in' => 'The product application must be: all, selected, or category.',
            'products.required' => 'You must select at least one product.',
            'products.min' => 'You must select at least one product.',
            'products.*.exists' => 'One of the selected products does not exist.',
            'categories.required' => 'You must select at least one category.',
            'categories.min' => 'You must select at least one category.',
            'categories.*.exists' => 'One of the selected categories does not exist.',
            'commission_rate.required_with' => 'The commission rate is required when an affiliate partner is selected.',
            'commission_rate.max' => 'The commission rate may not be greater than 100%.',
            'affiliate_partner_id.exists' => 'The selected affiliate partner does not exist.',

            // Advanced settings messages
            'is_active.boolean' => 'The active status must be true or false.',
            'is_stackable.boolean' => 'The stackable option must be true or false.',
            'stack_priority.integer' => 'The stack priority must be a valid number.',
            'stack_priority.min' => 'The stack priority must be at least 1.',
            'stack_priority.max' => 'The stack priority cannot exceed 10.',
            'auto_apply.boolean' => 'The auto-apply option must be true or false.',
            'auto_apply_min_cart_amount.numeric' => 'The minimum cart amount for auto-apply must be a valid number.',
            'auto_apply_min_cart_amount.min' => 'The minimum cart amount for auto-apply must be at least 0.',
            'auto_apply_min_items.integer' => 'The minimum items for auto-apply must be a valid number.',
            'auto_apply_min_items.min' => 'The minimum items for auto-apply must be at least 1.',
            'auto_apply_specific_products.array' => 'The specific products must be a valid list.',
            'auto_apply_specific_products.*.exists' => 'One or more selected products do not exist.',
            'auto_apply_course_types.array' => 'The course types for auto-apply must be a valid list.',
            'auto_apply_course_types.*.string' => 'Each course type must be valid text.',
            'min_items.integer' => 'The minimum items must be a valid number.',
            'min_items.min' => 'The minimum items must be at least 1.',
            'min_cart_items.integer' => 'The minimum cart items must be a valid number.',
            'min_cart_items.min' => 'The minimum cart items must be at least 0.',
            'course_types.string' => 'The course types must be valid text.',
            'max_uses_per_user.integer' => 'The maximum uses per user must be a valid number.',
            'max_uses_per_user.min' => 'The maximum uses per user must be at least 1.',
            'is_public.boolean' => 'The public visibility must be true or false.',
            'custom_message.string' => 'The custom message must be valid text.',
            'custom_message.max' => 'The custom message cannot exceed 1000 characters.',
            'usage_limit.integer' => 'The usage limit must be a valid number.',
            'usage_limit.min' => 'The usage limit must be at least 1.',
            'first_purchase_only.boolean' => 'The first purchase only option must be true or false.',
            'cumulative_enabled.boolean' => 'The cumulative discount option must be true or false.',
            'allow_multiple_uses.boolean' => 'The allow multiple uses option must be true or false.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'nom' => 'coupon name',
            'code' => 'coupon code',
            'valeur' => 'value',
            'date_debut' => 'start date',
            'date_fin' => 'end date',
            'type' => 'coupon type',
            'limit_utilise' => 'usage limit',
            'customer_type' => 'customer type',
            'product_application' => 'product application',
            'products' => 'products',
            'categories' => 'categories',
            'min_purchase_amount' => 'minimum purchase amount',
            'max_discount_amount' => 'maximum discount amount',
            'affiliate_partner_id' => 'affiliate partner',
            'commission_rate' => 'commission rate',

            // Advanced settings attributes
            'is_active' => 'active status',
            'is_stackable' => 'stackable',
            'stack_priority' => 'stack priority',
            'auto_apply' => 'auto-apply',
            'auto_apply_min_cart_amount' => 'minimum cart amount for auto-apply',
            'auto_apply_min_items' => 'minimum items for auto-apply',
            'auto_apply_specific_products' => 'specific products for auto-apply',
            'auto_apply_course_types' => 'course types for auto-apply',
            'min_items' => 'minimum items',
            'course_types' => 'course types',
            'max_uses_per_user' => 'maximum uses per user',
            'is_public' => 'public visibility',
            'custom_message' => 'custom message',
            'usage_limit' => 'usage limit',
            'first_purchase_only' => 'first purchase only',
            'cumulative_enabled' => 'cumulative discount',
            'allow_multiple_uses' => 'allow multiple uses',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        Log::error('Coupon validation failed', [
            'errors' => $validator->errors()->toArray(),
            'input' => $this->all()
        ]);

        parent::failedValidation($validator);
    }
}
