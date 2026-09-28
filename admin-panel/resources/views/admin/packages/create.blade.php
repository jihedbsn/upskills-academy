@extends('layouts.app')

@section('title', 'Create Package - Marketing')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fa fa-plus me-2"></i>
                        Create New Package
                    </h4>
                    <a href="{{ route('packages.index') }}" class="btn btn-outline-light btn-sm">
                        <i class="fa fa-arrow-left me-1"></i>Back to Packages
                    </a>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('packages.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <!-- Package Information -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Package Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="title" class="form-label">Title (English) *</label>
                                            <input type="text" class="form-control" id="title" name="title"
                                                   value="{{ old('title') }}" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="title_ar" class="form-label">Title (Arabic) *</label>
                                            <input type="text" class="form-control" id="title_ar" name="title_ar"
                                                   value="{{ old('title_ar') }}" required dir="rtl">
                                        </div>

                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea class="form-control" id="description" name="description"
                                                      rows="3">{{ old('description') }}</textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label for="description_ar" class="form-label">Description (Arabic)</label>
                                            <textarea class="form-control" id="description_ar" name="description_ar"
                                                      rows="3" dir="rtl">{{ old('description_ar') }}</textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label for="image" class="form-label">Package Image</label>
                                            <input type="file" class="form-control" id="image" name="image"
                                                   accept="image/*">
                                            <div class="form-text">Recommended size: 400x300px. Max size: 2MB</div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="is_active"
                                                       name="is_active" value="1" {{ old('is_active') ? 'checked' : 'checked' }}>
                                                <label class="form-check-label" for="is_active">
                                                    Active Package
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Products Selection -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Products & Reductions</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Select Products *</label>
                                            <div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                                                @foreach($products as $product)
                                                <div class="form-check mb-3 product-item" data-product-id="{{ $product->id }}">
                                                    <div class="d-flex align-items-center">
                                                        <input class="form-check-input product-checkbox" type="checkbox"
                                                               name="products[]" value="{{ $product->id }}"
                                                               id="product_{{ $product->id }}">
                                                        <div class="d-flex align-items-center flex-grow-1">
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
                                                                <label class="form-check-label fw-bold" for="product_{{ $product->id }}">
                                                                    {{ $product->titre }}
                                                                </label>
                                                                <div class="mt-1">
                                                                    <span class="badge bg-primary">${{ number_format($product->prix, 2) }}</span>
                                                                    <span class="badge bg-secondary">{{ $product->type_course }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="ms-3">
                                                            <div class="input-group" style="width: 200px;">
                                                                <select class="form-select form-select-sm discount-type-select"
                                                                        name="discount_types[]"
                                                                        style="max-width: 80px;"
                                                                        disabled>
                                                                    <option value="percentage" selected>%</option>
                                                                    <option value="fixed">$</option>
                                                                </select>
                                                                <input type="number" class="form-control form-control-sm reduction-input"
                                                                       name="reductions[]" min="0" step="0.1"
                                                                       placeholder="0" disabled>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                            <div class="form-text">Select products and set reduction percentage for each</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('packages.index') }}" class="btn btn-secondary">
                                        <i class="fa fa-times me-1"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-save me-1"></i>Create Package
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.product-checkbox');
    const reductionInputs = document.querySelectorAll('.reduction-input');

    checkboxes.forEach((checkbox, index) => {
        checkbox.addEventListener('change', function() {
            const productItem = this.closest('.product-item');
            const reductionInput = productItem.querySelector('.reduction-input');
            const discountTypeSelect = productItem.querySelector('.discount-type-select');

            if (this.checked) {
                reductionInput.disabled = false;
                reductionInput.required = true;
                if (discountTypeSelect) discountTypeSelect.disabled = false;
                reductionInput.value = reductionInput.value || '0';
            } else {
                reductionInput.disabled = true;
                reductionInput.required = false;
                if (discountTypeSelect) discountTypeSelect.disabled = true;
                reductionInput.value = '';
            }
        });
    });

    // Validation du formulaire
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const checkedBoxes = document.querySelectorAll('.product-checkbox:checked');
        if (checkedBoxes.length === 0) {
            e.preventDefault();
            alert('Please select at least one product for the package.');
            return false;
        }

        // Vérifier que tous les produits sélectionnés ont une réduction
        let allValid = true;
        checkedBoxes.forEach(checkbox => {
            const productItem = checkbox.closest('.product-item');
            const reductionInput = productItem.querySelector('.reduction-input');
            const discountTypeSelect = productItem.querySelector('.discount-type-select');
            const discountType = discountTypeSelect ? discountTypeSelect.value : 'percentage';
            const value = parseFloat(reductionInput.value);

            if (!reductionInput.value || value < 0) {
                allValid = false;
            } else if (discountType === 'percentage' && value > 100) {
                allValid = false;
            }
        });

        if (!allValid) {
            e.preventDefault();
            alert('Please set a valid reduction (0-100% for percentage, ≥0 for fixed amount) for all selected products.');
            return false;
        }

        // Before submit, remove name attributes from unchecked products' inputs
        document.querySelectorAll('.product-checkbox:not(:checked)').forEach(checkbox => {
            const productItem = checkbox.closest('.product-item');
            const reductionInput = productItem.querySelector('.reduction-input');
            const discountTypeSelect = productItem.querySelector('.discount-type-select');

            if (reductionInput) reductionInput.removeAttribute('name');
            if (discountTypeSelect) discountTypeSelect.removeAttribute('name');
        });
    });
});
</script>
@endpush
