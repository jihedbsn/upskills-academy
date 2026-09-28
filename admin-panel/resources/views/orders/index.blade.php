@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Orders Management</h3>
                    <a href="{{ route('admin.orders.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create New Order
                    </a>
                </div>

                <!-- Statistics Cards -->
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-lg-3 col-md-6">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $stats['total'] }}</h4>
                                            <p class="mb-0">Total Orders</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-shopping-cart fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $stats['paid'] }}</h4>
                                            <p class="mb-0">Approved Orders</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-check-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $stats['pending'] }}</h4>
                                            <p class="mb-0">Pending Orders</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-clock fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">${{ number_format($stats['revenue'], 2) }}</h4>
                                            <p class="mb-0">Total Revenue</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-chart-line fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <form method="GET" class="row g-3">
                                <div class="col-md-2">
                                    <select name="status" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Approved</option>
                                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Pending</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="type" class="form-control">
                                        <option value="">All Types</option>
                                        <option value="course" {{ request('type') == 'course' ? 'selected' : '' }}>Course</option>
                                        <option value="book" {{ request('type') == 'book' ? 'selected' : '' }}>Book</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                                </div>
                                <div class="col-md-2">
                                    <input type="text" name="student" class="form-control" placeholder="Student name" value="{{ request('student') }}">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Clear</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Orders Table -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Student</th>
                                    <th>Product/Book</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Payment Method</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                <tr>
                                    <td>{{ $order->id }}</td>
                                    <td>
                                        @if($order->student)
                                            <strong>{{ $order->student->first_name }} {{ $order->student->last_name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $order->student->email }}</small>
                                        @else
                                            <span class="text-danger">Student not found</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($order->product)
                                            <strong>{{ $order->product->titre ?? 'Product #' . $order->product->id }}</strong>
                                            @if($order->product->period)
                                                <br><small class="text-muted">Duration: {{ $order->product->period }}</small>
                                            @endif
                                        @elseif($order->book)
                                            <strong>{{ $order->book->title ?? 'Book #' . $order->book->id }}</strong>
                                        @else
                                            <span class="text-muted">No product/book</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>${{ number_format($order->price, 2) }}</strong>
                                    </td>
                                    <td>
                                        @if($order->payment_method === 'bank_transfer' || $order->payment_method === 'cash_on_delivery')
                                            @if($order->payment_status === 'pending')
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-clock"></i> Pending
                                                </span>
                                            @elseif($order->payment_status === 'approved')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle"></i> Approved
                                                </span>
                                            @elseif($order->payment_status === 'rejected')
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times-circle"></i> Rejected
                                                </span>
                                            @endif
                                        @elseif($order->price == 0 || $order->payment_method === 'free')
                                            {{-- Free course: show Approved if activated --}}
                                            @if($order->payment_success)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle"></i> Approved
                                                </span>
                                            @else
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-clock"></i> Pending
                                                </span>
                                            @endif
                                        @else
                                            {{-- Paid course with other payment methods --}}
                                            @if($order->payment_success)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle"></i> Approved
                                                </span>
                                            @else
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-clock"></i> Pending
                                                </span>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if($order->price == 0 || $order->payment_method === 'free')
                                            <span class="text-muted">free</span>
                                        @elseif($order->payment_method === 'bank_transfer')
                                            <i class="fas fa-university text-primary"></i> Bank Transfer
                                        @elseif($order->payment_method === 'credit_card')
                                            <i class="fas fa-credit-card text-info"></i> Credit Card
                                        @elseif($order->payment_method === 'paypal')
                                            <i class="fab fa-paypal text-primary"></i> PayPal
                                        @else
                                            {{ $order->payment_method ?? 'Not specified' }}
                                        @endif
                                    </td>
                                    <td>
                                        {{ $order->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.orders.show', $order->id) }}"
                                               class="btn btn-sm btn-info" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            @if(($order->payment_method === 'bank_transfer' || $order->payment_method === 'cash_on_delivery') && $order->payment_status === 'pending')
                                                <form action="{{ route('admin.orders.approve-payment', $order->id) }}"
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" title="Approve Payment">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.orders.reject-payment', $order->id) }}"
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="button" class="btn btn-sm btn-danger reject-btn"
                                                            data-order-id="{{ $order->id }}"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#rejectModal"
                                                            title="Reject Payment">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <a href="{{ route('admin.orders.edit', $order->id) }}"
                                                   class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif

                                            <form action="{{ route('admin.orders.destroy', $order->id) }}"
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this order?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">No orders found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $orders->links() }}
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
                <h5 class="modal-title" id="rejectModalLabel">Reject Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectForm" method="POST">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const rejectButtons = document.querySelectorAll('.reject-btn');
    const rejectForm = document.getElementById('rejectForm');

    rejectButtons.forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            const actionUrl = '{{ route("admin.orders.reject-payment", ":id") }}'.replace(':id', orderId);
            rejectForm.action = actionUrl;

            // Update modal title
            document.getElementById('rejectModalLabel').textContent = `Reject Payment - Order #${orderId}`;
        });
    });
});
</script>
@endsection
