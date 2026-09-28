@extends('layouts.app')

@section('title', 'Edit Coupon - Marketing')

@section('content')
<div class="container-fluid">
    <!-- Validation Errors Summary -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" role="alert">
            <div class="d-flex align-items-start">
                <i class="fa fa-exclamation-circle me-3 mt-1" style="font-size: 1.5rem;"></i>
                <div class="flex-grow-1">
                    <h5 class="alert-heading mb-2">Please fix the following errors:</h5>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">
                        <i class="fa fa-edit me-2"></i>
                        Edit Coupon: {{ $coupon->nom }}
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('coupons.update', $coupon) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- General Information -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">General Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="nom" class="form-label">
                                                <i class="fa fa-tag me-2"></i>Coupon Name <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control @error('nom') is-invalid @enderror"
                                                   id="nom" name="nom" value="{{ old('nom', $coupon->nom) }}" required>
                                            @error('nom')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="code" class="form-label">
                                                <i class="fa fa-ticket-alt me-2"></i>Coupon Code <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control text-uppercase @error('code') is-invalid @enderror"
                                                   id="code" name="code" value="{{ old('code', $coupon->code) }}"
                                                   required maxlength="50" style="text-transform: uppercase;">
                                            @error('code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Use uppercase letters and numbers only (e.g., SAVE10, WINTER2025)</div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="type" class="form-label">
                                                <i class="fa fa-percentage me-2"></i>Discount Type <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                                <option value="">Select type</option>
                                                <option value="percentage" {{ old('type', $coupon->type) == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                                <option value="fixed" {{ old('type', $coupon->type) == 'fixed' ? 'selected' : '' }}>Fixed Amount ($)</option>
                                            </select>
                                            @error('type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="valeur" class="form-label">
                                                <i class="fa fa-euro-sign me-2"></i>Value <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number" class="form-control @error('valeur') is-invalid @enderror"
                                                       id="valeur" name="valeur" value="{{ old('valeur', $coupon->valeur) }}"
                                                       step="0.01" min="0" required>
                                                <span class="input-group-text" id="valeur-unit">{{ $coupon->type === 'percentage' ? '%' : '$' }}</span>
                                            </div>
                                            @error('valeur')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="limit_utilise" class="form-label">
                                                <i class="fa fa-user-check me-2"></i>Usage Limit
                                            </label>
                                            <input type="number" class="form-control @error('limit_utilise') is-invalid @enderror"
                                                   id="limit_utilise" name="limit_utilise" value="{{ old('limit_utilise', $coupon->limit_utilise) }}"
                                                   min="1" placeholder="Leave empty for unlimited usage">
                                            @error('limit_utilise')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Optional. Define the maximum number of times this coupon can be used.</div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="customer_type" class="form-label">
                                                <i class="fa fa-users me-2"></i>Customer Type <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select @error('customer_type') is-invalid @enderror" id="customer_type" name="customer_type" required>
                                                <option value="all" {{ old('customer_type', $coupon->customer_type) == 'all' ? 'selected' : '' }}>All Customers</option>
                                                <option value="new" {{ old('customer_type', $coupon->customer_type) == 'new' ? 'selected' : '' }}>New Customers Only</option>
                                                <option value="returning" {{ old('customer_type', $coupon->customer_type) == 'returning' ? 'selected' : '' }}>Returning Customers</option>
                                                <option value="vip" {{ old('customer_type', $coupon->customer_type) == 'vip' ? 'selected' : '' }}>VIP Customers</option>
                                            </select>
                                            @error('customer_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="custom_message" class="form-label">
                                                <i class="fa fa-comment me-2"></i>Custom Message
                                            </label>
                                            <textarea class="form-control @error('custom_message') is-invalid @enderror"
                                                      id="custom_message" name="custom_message" rows="3"
                                                      placeholder="Optional message to display with this coupon">{{ old('custom_message', $coupon->description) }}</textarea>
                                            @error('custom_message')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">This message will be displayed to customers when they apply the coupon.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Period -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Validity Period</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="date_debut" class="form-label">
                                                <i class="fa fa-calendar-alt me-2"></i>Start Date <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" class="form-control @error('date_debut') is-invalid @enderror"
                                                   id="date_debut" name="date_debut" value="{{ old('date_debut', $coupon->date_debut->format('Y-m-d')) }}" required>
                                            @error('date_debut')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="date_fin" class="form-label">
                                                <i class="fa fa-calendar-alt me-2"></i>End Date <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" class="form-control @error('date_fin') is-invalid @enderror"
                                                   id="date_fin" name="date_fin" value="{{ old('date_fin', $coupon->date_fin->format('Y-m-d')) }}" required>
                                            @error('date_fin')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                                   {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                <i class="fa fa-check-circle me-2"></i>Active Coupon
                                            </label>
                                        </div>

                                        <div class="alert alert-info mt-3">
                                            <i class="fa fa-info-circle me-2"></i>
                                            <strong>Coupon Code:</strong> <code>{{ $coupon->code }}</code>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

        <!-- Advanced Settings -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fa fa-cog me-2"></i>Advanced Settings
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="min_purchase_amount" class="form-label">
                                <i class="fa fa-shopping-cart me-2"></i>Minimum Purchase Amount ($)
                            </label>
                            <input type="number" class="form-control @error('min_purchase_amount') is-invalid @enderror"
                                   id="min_purchase_amount" name="min_purchase_amount" value="{{ old('min_purchase_amount', $coupon->min_purchase_amount) }}"
                                   step="0.01" min="0" placeholder="Leave empty for no minimum">
                            @error('min_purchase_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Minimum cart value required to use this coupon.</div>
                        </div>

                        <div class="mb-3">
                            <label for="min_cart_items" class="form-label">
                                <i class="fa fa-boxes me-2"></i>Minimum Cart Items
                            </label>
                            <input type="number" class="form-control @error('min_cart_items') is-invalid @enderror"
                                   id="min_cart_items" name="min_cart_items" value="{{ old('min_cart_items', $coupon->min_cart_items ?? 1) }}"
                                   min="0" placeholder="Enter minimum number of items">
                            @error('min_cart_items')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Minimum number of items in cart. Default is 1. Set to 0 for no minimum.</div>
                        </div>

                        <div class="mb-3">
                            <label for="max_discount_amount" class="form-label">
                                <i class="fa fa-coins me-2"></i>Maximum Discount Amount ($)
                            </label>
                            <input type="number" class="form-control @error('max_discount_amount') is-invalid @enderror"
                                   id="max_discount_amount" name="max_discount_amount" value="{{ old('max_discount_amount', $coupon->max_discount_amount) }}"
                                   step="0.01" min="0" placeholder="Leave empty for no limit">
                            @error('max_discount_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Cap the maximum discount (useful for percentage coupons).</div>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox"
                                   id="is_stackable" name="is_stackable" value="1"
                                   {{ old('is_stackable', $coupon->is_stackable) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_stackable">
                                <i class="fa fa-layer-group me-2"></i><strong>Allow Stacking</strong>
                                <div class="form-text">Allow this coupon to be combined with other coupons.</div>
                            </label>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox"
                                   id="auto_apply" name="auto_apply" value="1"
                                   {{ old('auto_apply', $coupon->auto_apply) ? 'checked' : '' }}>
                            <label class="form-check-label" for="auto_apply">
                                <i class="fa fa-magic me-2"></i><strong>Auto-Apply</strong>
                                <div class="form-text">Automatically apply this coupon when conditions are met.</div>
                            </label>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox"
                                   id="first_purchase_only" name="first_purchase_only" value="1"
                                   {{ old('first_purchase_only', $coupon->first_purchase_only) ? 'checked' : '' }}>
                            <label class="form-check-label" for="first_purchase_only">
                                <i class="fa fa-star me-2"></i><strong>First Purchase Only</strong>
                                <div class="form-text">This coupon can only be used on the customer's first order.</div>
                            </label>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox"
                                   id="cumulative_enabled" name="cumulative_enabled" value="1"
                                   {{ old('cumulative_enabled', $coupon->cumulative_enabled) ? 'checked' : '' }}>
                            <label class="form-check-label" for="cumulative_enabled">
                                <i class="fa fa-plus-circle me-2"></i><strong>Cumulative Discount</strong>
                                <div class="form-text">Allow discount to accumulate with other promotions.</div>
                            </label>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox"
                                   id="is_public" name="is_public" value="1"
                                   {{ old('is_public', $coupon->is_public) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_public">
                                <i class="fa fa-globe me-2"></i><strong>Public Coupon</strong>
                                <div class="form-text">Make this coupon publicly visible to all users.</div>
                            </label>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox"
                                   id="allow_multiple_uses" name="allow_multiple_uses" value="1"
                                   {{ old('allow_multiple_uses', $coupon->allow_multiple_uses) ? 'checked' : '' }}>
                            <label class="form-check-label" for="allow_multiple_uses">
                                <i class="fa fa-repeat me-2"></i><strong>Allow Multiple Uses</strong>
                                <div class="form-text">Allow users to use this coupon multiple times, but not on the same order.</div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fa fa-handshake me-2"></i>Affiliate & Targeting
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="affiliate_partner_id" class="form-label">
                                <i class="fa fa-user-tie me-2"></i>Affiliate Partner
                            </label>
                            <select class="form-select @error('affiliate_partner_id') is-invalid @enderror"
                                    id="affiliate_partner_id" name="affiliate_partner_id">
                                <option value="">No Affiliate</option>
                                @foreach($affiliatePartners ?? [] as $affiliate)
                                    <option value="{{ $affiliate->id }}" {{ old('affiliate_partner_id', $coupon->affiliate_partner_id) == $affiliate->id ? 'selected' : '' }}>
                                        {{ $affiliate->name }} ({{ $affiliate->commission_rate }}%)
                                    </option>
                                @endforeach
                            </select>
                            @error('affiliate_partner_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Link this coupon to an affiliate partner for commission tracking.</div>
                        </div>

                        <div class="mb-3" id="commission_rate_field" @if(!old('affiliate_partner_id', $coupon->affiliate_partner_id)) style="display: none;" @endif>
                            <label for="commission_rate" class="form-label">
                                <i class="fa fa-percentage me-2"></i>Commission Rate (%)
                            </label>
                            <input type="number" class="form-control @error('commission_rate') is-invalid @enderror"
                                   id="commission_rate" name="commission_rate" value="{{ old('commission_rate', $coupon->commission_rate) }}"
                                   step="0.01" min="0" max="100" placeholder="Enter commission rate">
                            @error('commission_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Commission rate for the selected affiliate partner.</div>
                        </div>


                    </div>
                </div>
            </div>
        </div>                        <!-- Product Selection -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">
                                            <i class="fa fa-box me-2"></i>Product Selection <span class="text-danger">*</span>
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        @error('products')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror

                                        @php
                                            $currentApplication = old('product_application', $applicationType ?? 'selected');
                                            $currentCategories = old('categories', $selectedCategories ?? []);
                                        @endphp

                                        <!-- Product Application Type -->
                                        <div class="mb-4">
                                            <label class="form-label">
                                                <i class="fa fa-cogs me-2"></i>Apply coupon to:
                                            </label>
                                            <div class="d-flex gap-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="product_application" id="all_products" value="all" {{ $currentApplication == 'all' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="all_products">
                                                        <i class="fa fa-globe me-1"></i>All Products
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="product_application" id="selected_products" value="selected" {{ $currentApplication == 'selected' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="selected_products">
                                                        <i class="fa fa-list me-1"></i>Selected Products Only
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="product_application" id="by_category" value="category" {{ $currentApplication == 'category' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="by_category">
                                                        <i class="fa fa-tags me-1"></i>By Category
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Categories Selection -->
                                        <div class="mb-4" id="category_selection_container" style="display: {{ $currentApplication == 'category' ? 'block' : 'none' }};">
                                            <label for="categories" class="form-label">
                                                <i class="fa fa-tags me-2"></i>Select Categories
                                            </label>
                                            <select class="form-select @error('categories') is-invalid @enderror" id="categories" name="categories[]" multiple>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" {{ in_array($category->id, $currentCategories) ? 'selected' : '' }}>
                                                        {{ $category->titre_en ?: $category->titre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('categories')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">
                                                <i class="fa fa-info-circle me-1"></i>
                                                Hold Ctrl/Cmd to select multiple categories. All products in selected categories will be eligible for the coupon.
                                            </div>
                                        </div>

                                        <div id="products_selection_container">
                                            <div class="row">
                                            @foreach($products as $product)
                                            <div class="col-md-4 mb-3">
                                                <div class="card product-card">
                                                    <div class="card-body">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                   name="products[]" value="{{ $product->id }}"
                                                                   id="product_{{ $product->id }}"
                                                                   {{ in_array($product->id, old('products', $selectedProducts)) ? 'checked' : '' }}>
                                                            <label class="form-check-label w-100" for="product_{{ $product->id }}">
                                                                <div class="d-flex align-items-center">
                                                                    @if($product->image)
                                                                        <img src="{{ asset('/uploads/products/images/' . $product->image) }}"
                                                                             alt="{{ $product->titre }}"
                                                                             class="rounded me-3"
                                                                             style="width: 60px; height: 60px; object-fit: cover;">
                                                                    @else
                                                                        <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                                                             style="width: 60px; height: 60px;">
                                                                            <i class="fa fa-image text-muted fa-2x"></i>
                                                                        </div>
                                                                    @endif
                                                                    <div class="flex-grow-1">
                                                                        <h6 class="mb-1">{{ $product->titre }}</h6>
                                                                        <div class="mt-1">
                                                                            <span class="badge bg-primary">{{ number_format($product->prix, 2) }} $</span>
                                                                            <span class="badge bg-secondary">{{ $product->type_course }}</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>

                                            @if($products->count() === 0)
                                                <div class="text-center py-4">
                                                    <i class="fa fa-box fa-3x text-muted mb-3"></i>
                                                    <h5 class="text-muted">No products available</h5>
                                                    <p class="text-muted">You must first create products to create coupons.</p>
                                                </div>
                                            @endif
                                        </div> <!-- End products_selection_container -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('coupons.index') }}" class="btn btn-secondary">
                                        <i class="fa fa-arrow-left me-2"></i>Back
                                    </a>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fa fa-save me-2"></i>Update Coupon
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.product-card {
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.product-card:hover {
    border-color: #ffc107;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.form-check-input:checked + .form-check-label .product-card {
    border-color: #ffc107;
    background-color: #fffbf0;
}

.form-check-input:checked + .form-check-label {
    color: #856404;
    font-weight: bold;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Scroll to first error if validation failed
    @if ($errors->any())
        const firstError = document.querySelector('.is-invalid, .alert-danger');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            // Focus on the first invalid input
            const firstInvalidInput = document.querySelector('.is-invalid');
            if (firstInvalidInput && firstInvalidInput.tagName !== 'SELECT') {
                setTimeout(() => firstInvalidInput.focus(), 500);
            }
        }
    @endif

    // Add form validation on submit
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const requiredFields = [
            { id: 'nom', name: 'Coupon Name' },
            { id: 'code', name: 'Coupon Code' },
            { id: 'type', name: 'Discount Type' },
            { id: 'valeur', name: 'Value' },
            { id: 'date_debut', name: 'Start Date' },
            { id: 'date_fin', name: 'End Date' },
            { id: 'customer_type', name: 'Customer Type' },
            { id: 'product_application', name: 'Product Application' }
        ];

        let hasError = false;
        let errorMessages = [];

        requiredFields.forEach(field => {
            const input = document.getElementById(field.id);
            if (input && !input.value) {
                hasError = true;
                errorMessages.push(field.name + ' is required');
                input.classList.add('is-invalid');
            }
        });

        // Check product selection
        const productApplication = document.querySelector('input[name="product_application"]:checked');
        if (productApplication) {
            if (productApplication.value === 'selected') {
                const selectedProducts = document.querySelectorAll('input[name="products[]"]:checked');
                if (selectedProducts.length === 0) {
                    hasError = true;
                    errorMessages.push('Please select at least one product');
                }
            } else if (productApplication.value === 'category') {
                const selectedCategories = document.getElementById('categories');
                if (selectedCategories && selectedCategories.selectedOptions.length === 0) {
                    hasError = true;
                    errorMessages.push('Please select at least one category');
                }
            }
        }

        if (hasError) {
            e.preventDefault();
            alert('Please fill in all required fields:\\n\\n' + errorMessages.join('\\n'));
            const firstInvalid = document.querySelector('.is-invalid');
            if (firstInvalid) {
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                setTimeout(() => firstInvalid.focus(), 300);
            }
            return false;
        }
    });

    const typeSelect = document.getElementById('type');
    const valeurUnit = document.getElementById('valeur-unit');
    const valeurInput = document.getElementById('valeur');

    typeSelect.addEventListener('change', function() {
        if (this.value === 'percentage') {
            valeurUnit.textContent = '%';
            valeurInput.setAttribute('max', '100');
            valeurInput.setAttribute('step', '0.01');
        } else if (this.value === 'fixed') {
            valeurUnit.textContent = '$';
            valeurInput.removeAttribute('max');
            valeurInput.setAttribute('step', '0.01');
        }
    });

    // Date validation
    const dateDebut = document.getElementById('date_debut');
    const dateFin = document.getElementById('date_fin');

    dateDebut.addEventListener('change', function() {
        dateFin.setAttribute('min', this.value);
    });

    dateFin.addEventListener('change', function() {
        if (this.value <= dateDebut.value) {
            this.setCustomValidity('End date must be after start date');
        } else {
            this.setCustomValidity('');
        }
    });

    // Handle affiliate partner selection
    document.getElementById('affiliate_partner_id').addEventListener('change', function() {
        const commissionField = document.getElementById('commission_rate_field');
        const commissionInput = document.getElementById('commission_rate');

        if (this.value) {
            // Show commission rate field
            commissionField.style.display = 'block';
            commissionInput.required = true;

            // Auto-populate with affiliate's commission rate
            const selectedOption = this.options[this.selectedIndex];
            const text = selectedOption.text;
            const match = text.match(/\((\d+(?:\.\d+)?)%\)/);
            if (match) {
                commissionInput.value = match[1];
            }
        } else {
            // Hide commission rate field
            commissionField.style.display = 'none';
            commissionInput.required = false;
            commissionInput.value = '';
        }
    });

    // Handle product application type
    const allProductsRadio = document.getElementById('all_products');
    const selectedProductsRadio = document.getElementById('selected_products');
    const byCategoryRadio = document.getElementById('by_category');
    const productsContainer = document.getElementById('products_selection_container');
    const categoryContainer = document.getElementById('category_selection_container');

    function toggleProductSelection() {
        const productCheckboxes = document.querySelectorAll('input[name="products[]"]');
        const categorySelect = document.getElementById('categories');

        if (allProductsRadio.checked) {
            // Hide both containers
            productsContainer.style.display = 'none';
            categoryContainer.style.display = 'none';
            // Remove validations
            productCheckboxes.forEach(cb => {
                cb.required = false;
                cb.checked = false;
            });
            if (categorySelect) {
                categorySelect.required = false;
                categorySelect.value = '';
            }
        } else if (selectedProductsRadio.checked) {
            // Show products, hide category
            productsContainer.style.display = 'block';
            categoryContainer.style.display = 'none';
            if (categorySelect) {
                categorySelect.required = false;
                categorySelect.value = '';
            }
        } else if (byCategoryRadio.checked) {
            // Show category, hide products
            productsContainer.style.display = 'none';
            categoryContainer.style.display = 'block';
            // Remove product validation, add category validation
            productCheckboxes.forEach(cb => {
                cb.required = false;
                cb.checked = false;
            });
            if (categorySelect) {
                categorySelect.required = true;
            }
        }
    }

    allProductsRadio.addEventListener('change', toggleProductSelection);
    selectedProductsRadio.addEventListener('change', toggleProductSelection);
    byCategoryRadio.addEventListener('change', toggleProductSelection);

    // Initialize on page load
    toggleProductSelection();

    // Trigger change event on page load if affiliate is pre-selected
    const affiliateSelect = document.getElementById('affiliate_partner_id');
    if (affiliateSelect.value) {
        affiliateSelect.dispatchEvent(new Event('change'));
    }

    // Name validation functionality
    const nameInput = document.getElementById('nom');
    const originalName = '{{ $coupon->nom }}';
    const couponId = {{ $coupon->id }};
    let nameTimeout;

    if (nameInput) {
        nameInput.addEventListener('input', function() {
            const name = this.value.trim();

            // Clear previous timeout
            clearTimeout(nameTimeout);

            // Remove any existing validation feedback
            this.classList.remove('is-invalid', 'is-valid');
            const existingFeedback = this.parentNode.querySelector('.name-validation-feedback');
            if (existingFeedback) {
                existingFeedback.remove();
            }

            // Only validate if name is not empty and different from original
            if (name.length > 0 && name !== originalName) {
                // Debounce the validation request
                nameTimeout = setTimeout(() => {
                    fetch('{{ route("coupons.validate-name") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            name: name,
                            except_id: couponId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        const feedback = document.createElement('div');
                        feedback.className = 'name-validation-feedback';

                        if (data.available) {
                            nameInput.classList.add('is-valid');
                            feedback.className += ' valid-feedback';
                            feedback.textContent = 'Coupon name is available';
                        } else {
                            nameInput.classList.add('is-invalid');
                            feedback.className += ' invalid-feedback';
                            feedback.textContent = data.message || 'This coupon name is already taken';
                        }

                        nameInput.parentNode.appendChild(feedback);
                    })
                    .catch(error => {
                        console.error('Name validation error:', error);
                    });
                }, 500); // 500ms delay
            } else if (name === originalName) {
                // If name is same as original, show it's valid
                nameInput.classList.add('is-valid');
                const feedback = document.createElement('div');
                feedback.className = 'name-validation-feedback valid-feedback';
                feedback.textContent = 'Current coupon name';
                nameInput.parentNode.appendChild(feedback);
            }
        });
    }

    // Code validation functionality
    const codeInput = document.getElementById('code');
    const originalCode = '{{ $coupon->code }}';
    let codeTimeout;

    if (codeInput) {
        codeInput.addEventListener('input', function() {
            // Auto-uppercase as user types
            this.value = this.value.toUpperCase();

            const code = this.value.trim();

            // Clear previous timeout
            clearTimeout(codeTimeout);

            // Remove any existing validation feedback
            this.classList.remove('is-invalid', 'is-valid');
            const existingFeedback = this.parentNode.querySelector('.code-validation-feedback');
            if (existingFeedback) {
                existingFeedback.remove();
            }

            // Validate format (alphanumeric only)
            const formatValid = /^[A-Z0-9]*$/.test(code);

            if (code.length > 0) {
                if (!formatValid) {
                    this.classList.add('is-invalid');
                    const feedback = document.createElement('div');
                    feedback.className = 'code-validation-feedback invalid-feedback';
                    feedback.textContent = 'Code must contain only uppercase letters and numbers';
                    this.parentNode.appendChild(feedback);
                    return;
                }

                if (code !== originalCode) {
                    // Debounce the validation request
                    codeTimeout = setTimeout(() => {
                        fetch('{{ route("coupons.validate-code") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                code: code,
                                except_id: couponId
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            const feedback = document.createElement('div');
                            feedback.className = 'code-validation-feedback';

                            if (data.available) {
                                codeInput.classList.add('is-valid');
                                feedback.className += ' valid-feedback';
                                feedback.textContent = 'Coupon code is available';
                            } else {
                                codeInput.classList.add('is-invalid');
                                feedback.className += ' invalid-feedback';
                                feedback.textContent = data.message || 'This coupon code is already taken';
                            }

                            codeInput.parentNode.appendChild(feedback);
                        })
                        .catch(error => {
                            console.error('Code validation error:', error);
                        });
                    }, 500); // 500ms delay
                } else {
                    // If code is same as original, show it's valid
                    this.classList.add('is-valid');
                    const feedback = document.createElement('div');
                    feedback.className = 'code-validation-feedback valid-feedback';
                    feedback.textContent = 'Current coupon code';
                    this.parentNode.appendChild(feedback);
                }
            }
        });
    }
});
</script>
@endpush
