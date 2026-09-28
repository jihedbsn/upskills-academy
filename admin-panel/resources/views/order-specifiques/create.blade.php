@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Create New Installment Order</h3>
                    <a href="{{ route('admin.order-specifiques.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Orders
                    </a>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('admin.order-specifiques.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="student_id">Student <span class="text-danger">*</span></label>
                                    <select name="student_id" id="student_id" class="form-control @error('student_id') is-invalid @enderror" required>
                                        <option value="">Select a student...</option>
                                        @foreach($students as $student)
                                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                                {{ $student->first_name }} {{ $student->last_name }} - {{ $student->email }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('student_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="product_id">Product <span class="text-danger">*</span></label>
                                    <select name="product_id" id="product_id" class="form-control @error('product_id') is-invalid @enderror" required>
                                        <option value="">Select a product...</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" 
                                                    data-price="{{ $product->prix }}" 
                                                    {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                                Product #{{ $product->id }} - {{ $product->period }} - ${{ number_format($product->prix, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('product_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="product_variation_id">Product Variation (Optional)</label>
                                    <select name="product_variation_id" id="product_variation_id" class="form-control @error('product_variation_id') is-invalid @enderror">
                                        <option value="">Select a variation...</option>
                                        @if(old('product_id'))
                                            @php
                                                $selectedProduct = $products->firstWhere('id', old('product_id'));
                                            @endphp
                                            @if($selectedProduct)
                                                @foreach($selectedProduct->variations as $variation)
                                                    <option value="{{ $variation->id }}" {{ old('product_variation_id') == $variation->id ? 'selected' : '' }}>
                                                        {{ $variation->name }} ({{ strtoupper($variation->langue) }})
                                                    </option>
                                                @endforeach
                                            @endif
                                        @endif
                                    </select>
                                    @error('product_variation_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="total_price">Total Price ($) <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           name="total_price" 
                                           id="total_price" 
                                           class="form-control @error('total_price') is-invalid @enderror" 
                                           value="{{ old('total_price') }}" 
                                           step="0.01" 
                                           min="0" 
                                           required>
                                    @error('total_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="total_installments">Number of Installments <span class="text-danger">*</span></label>
                                    <select name="total_installments" id="total_installments" class="form-control @error('total_installments') is-invalid @enderror" required>
                                        <option value="">Select number of installments...</option>
                                        @for($i = 2; $i <= 12; $i++)
                                            <option value="{{ $i }}" {{ old('total_installments') == $i ? 'selected' : '' }}>
                                                {{ $i }} installments
                                            </option>
                                        @endfor
                                    </select>
                                    @error('total_installments')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Installment Amount Preview</label>
                                    <div class="form-control-plaintext" id="installment_preview">
                                        <span class="text-muted">Select total price and installments to see preview</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="4">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Installment Order
                            </button>
                            <a href="{{ route('admin.order-specifiques.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Auto-fill price when product is selected
        $('#product_id').change(function() {
            const selectedOption = $(this).find('option:selected');
            const price = selectedOption.data('price');
            
            if (price && !$('#total_price').val()) {
                $('#total_price').val(price);
            }
            
            // Load product variations
            loadProductVariations($(this).val());
            updateInstallmentPreview();
        });
        
        // Update installment preview when price or installments change
        $('#total_price, #total_installments').on('input change', function() {
            updateInstallmentPreview();
        });
        
        function loadProductVariations(productId) {
            if (!productId) {
                $('#product_variation_id').html('<option value="">Select a variation...</option>');
                return;
            }
            
            $.ajax({
                url: '{{ route("admin.order-specifiques.product-variations") }}',
                method: 'GET',
                data: { product_id: productId },
                success: function(variations) {
                    let options = '<option value="">Select a variation...</option>';
                    variations.forEach(function(variation) {
                        options += `<option value="${variation.id}">${variation.name} (${variation.langue.toUpperCase()})</option>`;
                    });
                    $('#product_variation_id').html(options);
                },
                error: function() {
                    $('#product_variation_id').html('<option value="">Error loading variations</option>');
                }
            });
        }
        
        function updateInstallmentPreview() {
            const totalPrice = parseFloat($('#total_price').val()) || 0;
            const installments = parseInt($('#total_installments').val()) || 0;
            
            if (totalPrice > 0 && installments > 0) {
                const installmentAmount = totalPrice / installments;
                $('#installment_preview').html(`
                    <strong>$${installmentAmount.toFixed(2)}</strong> per installment
                    <br><small class="text-muted">Total: $${totalPrice.toFixed(2)} ÷ ${installments} installments</small>
                `);
            } else {
                $('#installment_preview').html('<span class="text-muted">Select total price and installments to see preview</span>');
            }
        }
    });
</script>
@endsection
