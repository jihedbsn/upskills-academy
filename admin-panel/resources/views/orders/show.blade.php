@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Order Details #{{ $order->id }}</h3>
                    <div class="btn-group">
                        <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit Order
                        </a>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Orders
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Order Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Order Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Order ID:</strong></td>
                                    <td>{{ $order->id }}</td>
                                </tr>
                                @php
                                    $originalPrice = $order->product ? $order->product->prix : ($order->book ? $order->book->prix : 0);
                                    $finalPrice = $order->price;
                                    $totalDiscount = $originalPrice - $finalPrice;
                                    $pointsDiscount = $order->points_discount ?? 0;
                                    $referralPctDiscount = $referralInfo['referredDiscount'] ?? 0;
                                    $referralCreditApplied = $referralInfo['creditApplied'] ?? 0;
                                    $otherDiscount = $totalDiscount - $pointsDiscount - $referralPctDiscount - $referralCreditApplied;
                                    if ($otherDiscount < 0) { $otherDiscount = 0; }
                                @endphp
                                <tr>
                                    <td><strong>Original Price:</strong></td>
                                    <td><span class="text-muted">${{ number_format($originalPrice, 2) }}</span></td>
                                </tr>
                                @if($otherDiscount > 0)
                                <tr>
                                    <td><strong>Coupon/Package Discount:</strong></td>
                                    <td><span class="text-success">-${{ number_format($otherDiscount, 2) }}</span></td>
                                </tr>
                                @endif
                                @if($referralPctDiscount > 0)
                                <tr>
                                    <td><strong>Discount Type:</strong></td>
                                    <td>
                                        <span class="badge" style="background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;">
                                            <i class="fas fa-gift me-1"></i> Referral Discount (5%)
                                        </span>
                                        <span class="text-success ms-2">-${{ number_format($referralPctDiscount, 2) }}</span>
                                        @if(!empty($referralInfo['referrerName']))
                                            <div class="small text-muted mt-1">
                                                Referred by: <strong>{{ $referralInfo['referrerName'] }}</strong>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                                @if($referralCreditApplied > 0)
                                <tr>
                                    <td><strong>Discount Type:</strong></td>
                                    <td>
                                        <span class="badge" style="background:linear-gradient(135deg,#28a745,#198754);color:#fff;">
                                            <i class="fas fa-coins me-1"></i> Referral Credit Applied
                                        </span>
                                        <span class="text-success ms-2">-${{ number_format($referralCreditApplied, 2) }}</span>
                                        <div class="small text-muted mt-1">
                                            Credit earned from previous referrals, auto-applied at checkout.
                                        </div>
                                    </td>
                                </tr>
                                @endif
                                @if($pointsDiscount > 0)
                                <tr>
                                    <td><strong>Points Discount:</strong></td>
                                    <td>
                                        <span class="text-success">-${{ number_format($pointsDiscount, 2) }}</span>
                                        <small class="text-muted">({{ $order->points_used ?? 0 }} points)</small>
                                    </td>
                                </tr>
                                @endif
                                @if($totalDiscount > 0)
                                <tr>
                                    <td><strong>Total Discount:</strong></td>
                                    <td><span class="text-success fw-bold">-${{ number_format($totalDiscount, 2) }}</span></td>
                                </tr>
                                @endif
                                <tr>
                                    <td><strong>Final Price:</strong></td>
                                    <td><span class="h5 text-primary mb-0">${{ number_format($finalPrice, 2) }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Payment Status:</strong></td>
                                    <td>
                                        @if($order->payment_success)
                                            <span class="badge badge-success">Paid</span>
                                        @else
                                            <span class="badge badge-warning">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Payment Method:</strong></td>
                                    <td>{{ $order->payment_method ?? 'Not specified' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Updated:</strong></td>
                                    <td>{{ $order->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                @if($order->payment_method === 'bank_transfer' || $order->payment_method === 'cash_on_delivery')
                                    <tr>
                                        <td><strong>Payment Status:</strong></td>
                                        <td>
                                            @if($order->payment_status === 'pending')
                                                <span class="badge badge-warning">Pending Approval</span>
                                            @elseif($order->payment_status === 'approved')
                                                <span class="badge badge-success">Approved</span>
                                            @elseif($order->payment_status === 'rejected')
                                                <span class="badge badge-danger">Rejected</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @if($order->payment_status === 'rejected' && $order->rejection_comment)
                                        <tr>
                                            <td><strong>Rejection Reason:</strong></td>
                                            <td>
                                                <div class="alert alert-danger" role="alert">
                                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                                    {{ $order->rejection_comment }}
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                    @if($order->payment_description)
                                        <tr>
                                            <td><strong>Payment Description:</strong></td>
                                            <td>{{ $order->payment_description }}</td>
                                        </tr>
                                    @endif
                                    @if($order->payment_receipt)
                                        <tr>
                                            <td><strong>Payment Receipt:</strong></td>
                                            <td>
                                                @php
                                                    // Get base URL without locale suffix (e.g., /en, /ar)
                                                    $userUrl = env('USER_URL', 'http://localhost:8000');
                                                    // Remove trailing slash and locale suffix if present
                                                    $userUrl = preg_replace('#/(en|ar)/?$#', '', rtrim($userUrl, '/'));
                                                    $receiptUrl = $userUrl . '/storage/' . $order->payment_receipt;
                                                @endphp
                                                <a href="{{ $receiptUrl }}" target="_blank" class="btn btn-sm btn-info me-2">
                                                    <i class="fas fa-eye"></i> View Receipt
                                                </a>
                                                <a href="{{ route('admin.orders.download-receipt', $order->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-download"></i> Download Receipt
                                                </a>
                                            </td>
                                        </tr>
                                    @endif
                                @endif
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h5>Student Information</h5>
                            <table class="table table-borderless">
                                @if($order->student)
                                    <tr>
                                        <td><strong>Name:</strong></td>
                                        <td>{{ $order->student->first_name }} {{ $order->student->last_name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email:</strong></td>
                                        <td>{{ $order->student->email }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Phone:</strong></td>
                                        <td>{{ $order->student->phone ?? 'Not provided' }}</td>
                                    </tr>
                                @else
                                    <tr>
                                        <td colspan="2" class="text-danger">Student not found</td>
                                    </tr>
                                @endif
                            </table>

                            <h5>Product/Book Information</h5>
                            <table class="table table-borderless">
                                @if($order->product)
                                    <tr>
                                        <td><strong>Type:</strong></td>
                                        <td>Course</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Product:</strong></td>
                                        <td>{{ $order->product->titre ?? $order->product->variation_title ?? 'Product #' . $order->product->id }}@if($order->product->period) - {{ $order->product->period }}@endif</td>
                                    </tr>
                                @elseif($order->book)
                                    <tr>
                                        <td><strong>Type:</strong></td>
                                        <td>Book</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Book:</strong></td>
                                        <td>{{ $order->book->titre ?? $order->book->title ?? 'Book #' . $order->book->id }}</td>
                                    </tr>
                                @else
                                    <tr>
                                        <td colspan="2" class="text-danger">No product or book found</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Order Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="btn-group" role="group">
                                        @if(($order->payment_method === 'bank_transfer' || $order->payment_method === 'cash_on_delivery') && $order->payment_status === 'pending')
                                            <form action="{{ route('admin.orders.approve-payment', $order->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fas fa-check"></i> Approve Payment
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                                <i class="fas fa-times"></i> Reject Payment
                                            </button>
                                        @else
                                            <form action="{{ route('admin.orders.toggle-payment', $order->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit"
                                                        class="btn {{ $order->payment_success ? 'btn-warning' : 'btn-success' }}">
                                                    <i class="fas {{ $order->payment_success ? 'fa-times' : 'fa-check' }}"></i>
                                                    {{ $order->payment_success ? 'Mark as Pending' : 'Mark as Paid' }}
                                                </button>
                                            </form>
                                        @endif

                                        <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-primary">
                                            <i class="fas fa-edit"></i> Edit Order
                                        </a>

                                        <form action="{{ route('admin.orders.destroy', $order->id) }}"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this order?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-trash"></i> Delete Order
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">Reject Payment - Order #{{ $order->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.orders.reject-payment', $order->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection_comment" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejection_comment" name="rejection_comment" rows="4"
                                  placeholder="Please provide a reason for rejecting this payment..." required></textarea>
                        <div class="form-text">This reason will be sent to the student via email and notification.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> Reject Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
