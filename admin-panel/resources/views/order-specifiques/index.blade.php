@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Installment Orders Management</h3>
                    <a href="{{ route('admin.order-specifiques.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create New Installment Order
                    </a>
                </div>

                <!-- Statistics Cards -->
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-lg-2 col-md-4 col-sm-6">
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

                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $stats['pending'] }}</h4>
                                            <p class="mb-0">Pending</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-clock fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $stats['partial'] }}</h4>
                                            <p class="mb-0">Partial</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-percentage fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $stats['paid'] }}</h4>
                                            <p class="mb-0">Paid</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-check-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $stats['cancelled'] }}</h4>
                                            <p class="mb-0">Cancelled</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-times-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="card bg-dark text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">${{ number_format($stats['total_revenue'], 2) }}</h4>
                                            <p class="mb-0">Revenue</p>
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
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="search" class="form-control" placeholder="Search by student name or email" value="{{ request('search') }}">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="{{ route('admin.order-specifiques.index') }}" class="btn btn-secondary">Clear</a>
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
                                    <th>Product</th>
                                    <th>Total Price</th>
                                    <th>Paid Amount</th>
                                    <th>Remaining</th>
                                    <th>Progress</th>
                                    <th>Status</th>
                                    <th>Installments</th>
                                    <th>Created</th>
                                                    <th>Installment Status</th>
                                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orderSpecifiques as $order)
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
                                        <strong>{{ $order->product_title }}</strong>
                                        @if($order->productVariation)
                                            <br><small class="text-muted">{{ $order->productVariation->name }} ({{ strtoupper($order->productVariation->langue) }})</small>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>${{ number_format($order->total_price, 2) }}</strong>
                                    </td>
                                    <td>
                                        <span class="text-success">${{ number_format($order->paid_amount, 2) }}</span>
                                    </td>
                                    <td>
                                        <span class="text-warning">${{ number_format($order->remaining_amount, 2) }}</span>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar {{ $order->payment_progress >= 100 ? 'bg-success' : ($order->payment_progress > 0 ? 'bg-warning' : 'bg-secondary') }}"
                                                 role="progressbar"
                                                 style="width: {{ $order->payment_progress }}%">
                                                {{ $order->payment_progress }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @switch($order->status)
                                            @case('pending')
                                                <span class="badge bg-warning text-dark">Pending</span>
                                                @break
                                            @case('partial')
                                                <span class="badge bg-info text-white">Partial</span>
                                                @break
                                            @case('paid')
                                                <span class="badge bg-success text-white">Paid</span>
                                                @break
                                            @case('cancelled')
                                                <span class="badge bg-danger text-white">Cancelled</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        <span class="badge bg-primary text-white">{{ $order->paid_installments }}/{{ $order->total_installments }}</span>
                                    </td>
                                    <td>
                                        {{ $order->created_at->format('d/m/Y') }}
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @php
                                                $paidCount = $order->installments->where('status', 'paid')->count();
                                                $pendingCount = $order->installments->where('status', 'pending')->count();
                                                $overdueCount = $order->installments->where('status', 'overdue')->count();
                                                $awaitingCount = $order->installments->where('status', 'awaiting_payment')->count();
                                            @endphp

                                            @if($paidCount > 0)
                                                <span class="badge bg-success text-white">{{ $paidCount }} Paid</span>
                                            @endif
                                            @if($awaitingCount > 0)
                                                <span class="badge bg-info text-white">{{ $awaitingCount }} Awaiting</span>
                                            @endif
                                            @if($pendingCount > 0)
                                                <span class="badge bg-warning text-dark">{{ $pendingCount }} Pending</span>
                                            @endif
                                            @if($overdueCount > 0)
                                                <span class="badge bg-danger text-white">{{ $overdueCount }} Overdue</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.order-specifiques.show', $order->id) }}"
                                               class="btn btn-sm btn-info" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($order->paid_amount == 0)
                                                <a href="{{ route('admin.order-specifiques.edit', $order->id) }}"
                                                   class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                            @if($order->status !== 'paid' && $order->status !== 'cancelled')
                                                <button type="button"
                                                        class="btn btn-sm btn-success"
                                                        title="Add Payment"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#addPaymentModal{{ $order->id }}">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            @endif
                                            @if($order->paid_amount == 0)
                                                <form action="{{ route('admin.order-specifiques.destroy', $order->id) }}"
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Are you sure you want to delete this order?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                <!-- Add Payment Modal -->
                                <div class="modal fade" id="addPaymentModal{{ $order->id }}" tabindex="-1" role="dialog">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Add Payment - Order #{{ $order->id }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('admin.order-specifiques.add-payment', $order->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Remaining Amount: <strong>${{ number_format($order->remaining_amount, 2) }}</strong></label>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="amount{{ $order->id }}">Payment Amount ($)</label>
                                                        <input type="number"
                                                               name="amount"
                                                               id="amount{{ $order->id }}"
                                                               class="form-control"
                                                               step="0.01"
                                                               min="0.01"
                                                               max="{{ $order->remaining_amount }}"
                                                               required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="payment_method{{ $order->id }}">Payment Method</label>
                                                        <select name="payment_method" id="payment_method{{ $order->id }}" class="form-control">
                                                            <option value="">Select method...</option>
                                                            <option value="cash">Cash</option>
                                                            <option value="bank_transfer">Bank Transfer</option>
                                                            <option value="credit_card">Credit Card</option>
                                                            <option value="swish">Swish</option>
                                                            <option value="other">Other</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="notes{{ $order->id }}">Notes</label>
                                                        <textarea name="notes" id="notes{{ $order->id }}" class="form-control" rows="3"></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-success">Add Payment</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <tr>
                                    <td colspan="12" class="text-center">No installment orders found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $orderSpecifiques->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
