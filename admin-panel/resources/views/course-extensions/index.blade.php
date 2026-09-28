@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i data-feather="refresh-cw" class="me-2"></i>Course Extension Orders</h2>
                    <p class="text-muted mb-0">Manage course extension payment requests from students</p>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h4 class="mb-0">{{ $stats['total'] }}</h4>
                                    <small>Total Orders</small>
                                </div>
                                <i data-feather="shopping-cart" style="width:32px;height:32px;opacity:0.6"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card bg-warning text-dark">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h4 class="mb-0">{{ $stats['pending'] }}</h4>
                                    <small>Pending Approval</small>
                                </div>
                                <i data-feather="clock" style="width:32px;height:32px;opacity:0.6"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h4 class="mb-0">{{ $stats['approved'] }}</h4>
                                    <small>Approved</small>
                                </div>
                                <i data-feather="check-circle" style="width:32px;height:32px;opacity:0.6"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h4 class="mb-0">${{ number_format($stats['revenue'], 2) }}</h4>
                                    <small>Total Revenue</small>
                                </div>
                                <i data-feather="dollar-sign" style="width:32px;height:32px;opacity:0.6"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('course-extensions.index') }}" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" class="form-control" placeholder="Student name or email..."
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">From</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">To</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary me-2">
                                <i data-feather="search" style="width:14px;height:14px"></i> Filter
                            </button>
                            <a href="{{ route('course-extensions.index') }}" class="btn btn-outline-secondary">Clear</a>
                        </div>
                    </form>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Orders Table -->
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Student</th>
                                <th>Course</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Extension</th>
                                <th>Status</th>
                                <th>Receipt</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($extensionOrders as $order)
                            <tr>
                                <td>{{ $order->id }}</td>
                                <td>
                                    @if($order->student)
                                        <div class="fw-bold">{{ $order->student->prenom ?? '' }} {{ $order->student->nom ?? '' }}</div>
                                        <small class="text-muted">{{ $order->student->email ?? '' }}</small>
                                    @else
                                        <span class="text-muted">Deleted</span>
                                    @endif
                                </td>
                                <td>
                                    @if($order->product)
                                        {{ $order->product->titre ?? 'N/A' }}
                                    @else
                                        <span class="text-muted">Deleted</span>
                                    @endif
                                </td>
                                <td>${{ number_format($order->price, 2) }}</td>
                                <td>
                                    @if($order->payment_method === 'credit_card')
                                        <span class="badge bg-info">Credit Card</span>
                                    @else
                                        <span class="badge bg-secondary">Bank Transfer</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $order->extension_months }} months
                                    @if($order->new_expiration_date)
                                        <br><small class="text-muted">New: {{ $order->new_expiration_date->format('M d, Y') }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $order->status_badge }}">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                    @if($order->is_processed)
                                        <br><small class="text-success"><i data-feather="check" style="width:12px;height:12px"></i> Applied</small>
                                    @endif
                                </td>
                                <td>
                                    @if($order->payment_receipt)
                                        <a href="{{ route('course-extensions.receipt', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i data-feather="download" style="width:14px;height:14px"></i> Download
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                <td>
                                    @if($order->payment_status === 'pending')
                                        <div class="btn-group btn-group-sm">
                                            <form action="{{ route('course-extensions.approve', $order->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm" title="Approve"
                                                        onclick="return confirm('Approve this extension and grant access?')">
                                                    <i data-feather="check" style="width:14px;height:14px"></i> Approve
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-danger btn-sm" title="Disapprove"
                                                    data-bs-toggle="modal" data-bs-target="#rejectModal{{ $order->id }}">
                                                <i data-feather="x" style="width:14px;height:14px"></i> Disapprove
                                            </button>
                                        </div>

                                        <!-- Reject Modal -->
                                        <div class="modal fade" id="rejectModal{{ $order->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="{{ route('course-extensions.reject', $order->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Disapprove Extension Order #{{ $order->id }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label">Rejection Reason</label>
                                                                <textarea name="rejection_reason" class="form-control" rows="3"
                                                                          placeholder="Enter the reason for rejection..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-danger">Disapprove</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif($order->payment_status === 'approved')
                                        <div class="d-flex flex-column gap-1">
                                            @if($order->payment_receipt)
                                                <a href="{{ route('course-extensions.receipt', $order->id) }}" class="btn btn-outline-primary btn-sm">
                                                    <i data-feather="download" style="width:14px;height:14px"></i> Receipt
                                                </a>
                                            @endif
                                            <span class="badge bg-success">
                                                <i data-feather="check-circle" style="width:12px;height:12px"></i> Completed
                                            </span>
                                        </div>
                                    @elseif($order->payment_status === 'rejected')
                                        <div class="d-flex flex-column gap-1">
                                            @if($order->payment_receipt)
                                                <a href="{{ route('course-extensions.receipt', $order->id) }}" class="btn btn-outline-primary btn-sm">
                                                    <i data-feather="download" style="width:14px;height:14px"></i> Receipt
                                                </a>
                                            @endif
                                            <span class="badge bg-danger">Rejected</span>
                                        </div>
                                    @else
                                        <span class="text-muted">No actions</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <i data-feather="inbox" style="width:48px;height:48px" class="text-muted mb-2 d-block mx-auto"></i>
                                    <p class="text-muted mb-0">No extension orders found.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($extensionOrders->hasPages())
                <div class="card-footer">
                    {{ $extensionOrders->withQueryString()->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
