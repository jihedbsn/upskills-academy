@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Edit Order #{{ $order->id }}</h3>
                    <div class="btn-group">
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-info">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Orders
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="student_id">Student <span class="text-danger">*</span></label>
                                    <select name="student_id" id="student_id" class="form-control @error('student_id') is-invalid @enderror" required>
                                        <option value="">Select a student...</option>
                                        @foreach($students as $student)
                                            <option value="{{ $student->id }}" {{ old('student_id', $order->student_id) == $student->id ? 'selected' : '' }}>
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
                                    <label for="price">Price ($) <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           name="price" 
                                           id="price" 
                                           class="form-control @error('price') is-invalid @enderror" 
                                           value="{{ old('price', $order->price) }}" 
                                           step="0.01" 
                                           min="0" 
                                           required>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="product_id">Course (Optional)</label>
                                    <select name="product_id" id="product_id" class="form-control @error('product_id') is-invalid @enderror">
                                        <option value="">Select a course...</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" 
                                                    data-price="{{ $product->prix }}" 
                                                    {{ old('product_id', $order->product_id) == $product->id ? 'selected' : '' }}>
                                                Product #{{ $product->id }} - {{ $product->period }} - ${{ number_format($product->prix, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('product_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="book_id">Book (Optional)</label>
                                    <select name="book_id" id="book_id" class="form-control @error('book_id') is-invalid @enderror">
                                        <option value="">Select a book...</option>
                                        @foreach($books as $book)
                                            <option value="{{ $book->id }}" 
                                                    {{ old('book_id', $order->book_id) == $book->id ? 'selected' : '' }}>
                                                {{ $book->title ?? 'Book #' . $book->id }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('book_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="payment_method">Payment Method</label>
                                    <select name="payment_method" id="payment_method" class="form-control">
                                        <option value="">Select payment method...</option>
                                        <option value="cash" {{ old('payment_method', $order->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="bank_transfer" {{ old('payment_method', $order->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                        <option value="credit_card" {{ old('payment_method', $order->payment_method) == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                        <option value="swish" {{ old('payment_method', $order->payment_method) == 'swish' ? 'selected' : '' }}>Swish</option>
                                        <option value="other" {{ old('payment_method', $order->payment_method) == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" 
                                               name="payment_success" 
                                               id="payment_success" 
                                               class="form-check-input" 
                                               value="1" 
                                               {{ old('payment_success', $order->payment_success) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="payment_success">
                                            Mark as Paid
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Current Status Display -->
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Current Order Status</h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Payment Status:</strong>
                                    @if($order->payment_success)
                                        <span class="badge badge-success">Paid</span>
                                    @else
                                        <span class="badge badge-warning">Pending</span>
                                    @endif
                                </div>
                                <div class="col-md-3">
                                    <strong>Price:</strong> ${{ number_format($order->price, 2) }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Created:</strong> {{ $order->created_at->format('d/m/Y') }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Updated:</strong> {{ $order->updated_at->format('d/m/Y') }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Order
                            </button>
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-secondary">
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
            
            if (price && !$('#price').val()) {
                $('#price').val(price);
            }
        });
        
        // Ensure only one of product or book is selected
        $('#product_id, #book_id').change(function() {
            const productId = $('#product_id').val();
            const bookId = $('#book_id').val();
            
            if (productId && bookId) {
                alert('Please select either a course or a book, not both.');
                $(this).val('');
            }
        });
    });
</script>
@endsection