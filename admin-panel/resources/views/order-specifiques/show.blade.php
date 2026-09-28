@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Installment Order Details #{{ $orderSpecifique->id }}</h3>
                    <div class="btn-group">
                        @if($orderSpecifique->paid_amount == 0)
                            <a href="{{ route('admin.order-specifiques.edit', $orderSpecifique->id) }}" class="btn btn-warning">
                                <i data-feather="edit" class="me-2"></i>Edit Order
                            </a>
                        @endif
                        <a href="{{ route('admin.order-specifiques.index') }}" class="btn btn-secondary">
                            <i data-feather="arrow-left" class="me-2"></i>Back to Orders
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
                                    <td>{{ $orderSpecifique->id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Price:</strong></td>
                                    <td><span class="h5 text-primary">${{ number_format($orderSpecifique->total_price, 2) }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Paid Amount:</strong></td>
                                    <td><span class="h5 text-success">${{ number_format($orderSpecifique->paid_amount, 2) }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Remaining Amount:</strong></td>
                                    <td><span class="h5 text-warning">${{ number_format($orderSpecifique->remaining_amount, 2) }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Payment Progress:</strong></td>
                                    <td>
                                        <div class="progress" style="height: 25px;">
                                            <div class="progress-bar {{ $orderSpecifique->payment_progress >= 100 ? 'bg-success' : ($orderSpecifique->payment_progress > 0 ? 'bg-warning' : 'bg-secondary') }}"
                                                 role="progressbar"
                                                 style="width: {{ $orderSpecifique->payment_progress }}%">
                                                {{ $orderSpecifique->payment_progress }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
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
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Installments:</strong></td>
                                    <td><span class="badge badge-primary">{{ $orderSpecifique->paid_installments }}/{{ $orderSpecifique->total_installments }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $orderSpecifique->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Updated:</strong></td>
                                    <td>{{ $orderSpecifique->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h5>Student Information</h5>
                            <table class="table table-borderless">
                                @if($orderSpecifique->student)
                                    <tr>
                                        <td><strong>Name:</strong></td>
                                        <td>{{ $orderSpecifique->student->first_name }} {{ $orderSpecifique->student->last_name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email:</strong></td>
                                        <td>{{ $orderSpecifique->student->email }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Phone:</strong></td>
                                        <td>{{ $orderSpecifique->student->phone ?? 'Not provided' }}</td>
                                    </tr>
                                @else
                                    <tr>
                                        <td colspan="2" class="text-danger">Student not found</td>
                                    </tr>
                                @endif
                            </table>

                            <h5>Product Information</h5>
                            <table class="table table-borderless">
                                @if($orderSpecifique->product)
                                    <tr>
                                        <td><strong>Product:</strong></td>
                                        <td>{{ $orderSpecifique->product_title }}</td>
                                    </tr>
                                    @if($orderSpecifique->productVariation)
                                        <tr>
                                            <td><strong>Variation:</strong></td>
                                            <td>{{ $orderSpecifique->productVariation->name }} ({{ strtoupper($orderSpecifique->productVariation->langue) }})</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td><strong>Original Price:</strong></td>
                                        <td>${{ number_format($orderSpecifique->product->prix, 2) }}</td>
                                    </tr>
                                @else
                                    <tr>
                                        <td colspan="2" class="text-danger">Product not found</td>
                                    </tr>
                                @endif
                            </table>

                            @if($orderSpecifique->notes)
                                <h5>Notes</h5>
                                <div class="alert alert-info">
                                    {{ $orderSpecifique->notes }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Installments Table -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Installments</h5>
                                    @if($orderSpecifique->status !== 'paid' && $orderSpecifique->status !== 'cancelled')
                                        <button type="button"
                                                class="btn btn-success btn-sm"
                                                onclick="document.getElementById('add-payment-section').style.display='block'; document.getElementById('add-payment-section').scrollIntoView({behavior:'smooth'});">
                                            <i data-feather="plus" class="me-1"></i>Add Payment
                                        </button>
                                    @endif
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th width="5%">#</th>
                                                    <th width="15%">Amount</th>
                                                    <th width="15%">Due Date</th>
                                                    <th width="15%">Paid Date</th>
                                                    <th width="10%">Status</th>
                                                    <th width="15%">Payment Method</th>
                                                    <th width="15%">Notes</th>
                                                    <th width="10%">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($orderSpecifique->installments as $installment)
                                                <tr class="{{ $installment->isOverdue() ? 'table-danger' : '' }}">
                                                    <td class="text-center">{{ $loop->iteration }}</td>
                                                    <td class="text-right"><strong>${{ number_format($installment->amount, 2) }}</strong></td>
                                                    <td class="text-center">
                                                        <div>{{ $installment->formatted_due_date }}</div>
                                                        @if($installment->status !== 'paid')
                                                            <form action="{{ route('admin.order-specifiques.update-installment-due-date', $installment->id) }}"
                                                                  method="POST"
                                                                  class="mt-1 d-flex justify-content-center align-items-center"
                                                                  style="gap: 6px;">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="date"
                                                                       name="due_date"
                                                                       value="{{ optional($installment->due_date)->format('Y-m-d') }}"
                                                                       class="form-control form-control-sm"
                                                                       style="max-width: 165px;"
                                                                       required>
                                                                <button type="submit" class="btn btn-sm btn-outline-primary" title="Update due date">
                                                                    <i data-feather="calendar"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <div>{{ $installment->formatted_paid_date }}</div>
                                                        @if($installment->status === 'paid')
                                                            <form action="{{ route('admin.order-specifiques.update-installment-paid-date', $installment->id) }}"
                                                                  method="POST"
                                                                  class="mt-1 d-flex justify-content-center align-items-center"
                                                                  style="gap: 6px;">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="date"
                                                                       name="paid_date"
                                                                       value="{{ optional($installment->paid_date)->format('Y-m-d') }}"
                                                                       class="form-control form-control-sm"
                                                                       style="max-width: 165px;"
                                                                       required>
                                                                <button type="submit" class="btn btn-sm btn-outline-primary" title="Update payment date">
                                                                    <i data-feather="calendar"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @switch($installment->status)
                                                            @case('pending')
                                                                <span class="badge badge-warning">Pending</span>
                                                                @break
                                                            @case('paid')
                                                                <span class="badge badge-success">Paid</span>
                                                                @break
                                                            @case('overdue')
                                                                <span class="badge badge-danger">Overdue</span>
                                                                @break
                                                            @case('awaiting_payment')
                                                                <span class="badge badge-info">Awaiting Confirmation</span>
                                                                @break
                                                        @endswitch
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $installment->payment_method ?? 'Not specified' }}
                                                        @if($installment->payment_receipt)
                                                            <br><a href="{{ route('admin.order-specifiques.download-installment-receipt', $installment->id) }}" target="_blank" class="btn btn-xs btn-info mt-1" title="View Receipt">
                                                                <i data-feather="file-text"></i> Receipt
                                                            </a>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">{{ $installment->notes ?? '-' }}</td>
                                                    <td class="text-center">
                                                        <div class="btn-group" role="group">
                                                            @if($installment->status === 'pending' || $installment->status === 'overdue' || $installment->status === 'awaiting_payment')
                                                                <a href="{{ route('admin.order-specifiques.installment-detail', $installment->id) }}"
                                                                   class="btn btn-sm btn-success"
                                                                   title="Mark as Paid">
                                                                    <i data-feather="check"></i>
                                                                </a>
                                                            @endif

                                                            @if($installment->status === 'paid')
                                                                <a href="{{ route('admin.order-specifiques.installment-detail', $installment->id) }}"
                                                                   class="btn btn-sm btn-warning"
                                                                   title="Manage">
                                                                    <i data-feather="settings"></i>
                                                                </a>
                                                            @endif

                                                            <a href="{{ route('admin.order-specifiques.installment-detail', $installment->id) }}"
                                                               class="btn btn-sm btn-info"
                                                               title="View Details">
                                                                <i data-feather="eye"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="8" class="text-center">No installments found.</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Order Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="btn-group" role="group">
                                        @if($orderSpecifique->paid_amount == 0)
                                            <a href="{{ route('admin.order-specifiques.edit', $orderSpecifique->id) }}" class="btn btn-primary">
                                                <i data-feather="edit" class="me-2"></i>Edit Order
                                            </a>
                                        @endif

                                        @if($orderSpecifique->status !== 'paid' && $orderSpecifique->status !== 'cancelled')
                                            <a href="{{ route('admin.order-specifiques.show', $orderSpecifique->id) }}#add-payment-section"
                                               class="btn btn-success"
                                               onclick="document.getElementById('add-payment-section').style.display='block'; return true;">
                                                <i data-feather="plus" class="me-2"></i>Add Payment
                                            </a>
                                        @endif

                                        @if($orderSpecifique->paid_amount == 0)
                                            <form action="{{ route('admin.order-specifiques.destroy', $orderSpecifique->id) }}"
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this order?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">
                                                    <i data-feather="trash-2" class="me-2"></i>Delete Order
                                                </button>
                                            </form>
                                        @endif
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

<!-- Add Payment Section (inline form) -->
@if($orderSpecifique->status !== 'paid' && $orderSpecifique->status !== 'cancelled')
<div class="row mt-3" id="add-payment-section" style="display: none;">
    <div class="col-12">
        <div class="card border-success">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Add Payment - Order #{{ $orderSpecifique->id }}</h5>
                <button type="button" class="btn btn-sm btn-light" onclick="document.getElementById('add-payment-section').style.display='none'">✕</button>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.order-specifiques.add-payment', $orderSpecifique->id) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Remaining Amount</label>
                                <p class="h5 text-warning">${{ number_format($orderSpecifique->remaining_amount, 2) }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="amount">Payment Amount ($)</label>
                                <input type="number" name="amount" id="amount" class="form-control"
                                       step="0.01" min="0.01" max="{{ $orderSpecifique->remaining_amount }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="payment_method">Payment Method</label>
                                <select name="payment_method" id="payment_method" class="form-control">
                                    <option value="">Select method...</option>
                                    <option value="cash">Cash</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="credit_card">Credit Card</option>
                                    <option value="swish">Swish</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="payment_notes">Notes</label>
                                <textarea name="notes" id="payment_notes" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success mt-2">
                        <i data-feather="check" class="me-1"></i> Add Payment
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof feather !== 'undefined') {
        feather.replace();
    }

    // Show add-payment-section if URL has #add-payment-section
    if (window.location.hash === '#add-payment-section') {
        var section = document.getElementById('add-payment-section');
        if (section) section.style.display = 'block';
    }
});
</script>
@endsection
