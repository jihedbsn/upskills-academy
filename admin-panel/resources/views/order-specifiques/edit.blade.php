@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Edit Installment Order #{{ $orderSpecifique->id }}</h3>
                    <div class="btn-group">
                        <a href="{{ route('admin.order-specifiques.show', $orderSpecifique->id) }}" class="btn btn-info">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                        <a href="{{ route('admin.order-specifiques.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Orders
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('admin.order-specifiques.update', $orderSpecifique->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="student_id">Student <span class="text-danger">*</span></label>
                                    <select name="student_id" id="student_id" class="form-control @error('student_id') is-invalid @enderror" required>
                                        <option value="">Select a student...</option>
                                        @foreach($students as $student)
                                            <option value="{{ $student->id }}" {{ old('student_id', $orderSpecifique->student_id) == $student->id ? 'selected' : '' }}>
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
                                                    {{ old('product_id', $orderSpecifique->product_id) == $product->id ? 'selected' : '' }}>
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
                                        @if($orderSpecifique->product)
                                            @foreach($orderSpecifique->product->variations as $variation)
                                                <option value="{{ $variation->id }}" {{ old('product_variation_id', $orderSpecifique->product_variation_id) == $variation->id ? 'selected' : '' }}>
                                                    {{ $variation->name }} ({{ strtoupper($variation->langue) }})
                                                </option>
                                            @endforeach
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
                                           value="{{ old('total_price', $orderSpecifique->total_price) }}" 
                                           step="0.01" 
                                           min="0" 
                                           required>
                                    @error('total_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="4">{{ old('notes', $orderSpecifique->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Current Status Display -->
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Current Order Status</h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Status:</strong>
                                    @switch($orderSpecifique->status)
                                        @case('pending')
                                            <span class="badge badge-warning">Pending</span>
                                            @break
                                        @case('partial')
                                            <span class="badge badge-info">Partial</span>
                                            @break
                                        @case('paid')
                                            <span class="badge badge-success">Paid</span>
                                            @break
                                        @case('cancelled')
                                            <span class="badge badge-danger">Cancelled</span>
                                            @break
                                    @endswitch
                                </div>
                                <div class="col-md-3">
                                    <strong>Paid Amount:</strong> ${{ number_format($orderSpecifique->paid_amount, 2) }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Remaining:</strong> ${{ number_format($orderSpecifique->remaining_amount, 2) }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Progress:</strong> {{ $orderSpecifique->payment_progress }}%
                                </div>
                            </div>
                        </div>
                        
                        @if($orderSpecifique->paid_amount > 0)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Warning:</strong> This order has payments recorded. Only basic information can be modified.
                            </div>
                        @endif
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Order
                            </button>
                            <a href="{{ route('admin.order-specifiques.show', $orderSpecifique->id) }}" class="btn btn-secondary">
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
    });
</script>
@endsection
