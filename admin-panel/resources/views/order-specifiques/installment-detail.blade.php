@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Installment #{{ $installment->installment_number }} — Order #{{ $orderSpecifique->id }}</h3>
                    <a href="{{ route('admin.order-specifiques.show', $orderSpecifique->id) }}" class="btn btn-secondary">
                        <i data-feather="arrow-left" class="me-1"></i> Back to Order
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span>&times;</span></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span>&times;</span></button>
                </div>
            @endif

            <div class="row">
                <!-- Left Column: Installment Info -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Payment Information</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%"><strong>Installment #:</strong></td>
                                    <td>{{ $installment->installment_number }} of {{ $orderSpecifique->total_installments }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Amount:</strong></td>
                                    <td><span class="h5 text-primary">${{ number_format($installment->amount, 2) }}</span></td>
                                </tr>
                                @if($installment->late_fee > 0)
                                <tr>
                                    <td><strong>Late Fee:</strong></td>
                                    <td><span class="text-danger">${{ number_format($installment->late_fee, 2) }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Total Due:</strong></td>
                                    <td><span class="h5 text-danger">${{ number_format($installment->amount + ($installment->late_fee ?? 0), 2) }}</span></td>
                                </tr>
                                @endif
                                <tr>
                                    <td><strong>Due Date:</strong></td>
                                    <td>{{ $installment->formatted_due_date }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Paid Date:</strong></td>
                                    <td>{{ $installment->formatted_paid_date }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @switch($installment->status)
                                            @case('pending')
                                                <span class="badge badge-warning" style="font-size: 14px; padding: 6px 12px;">Pending</span>
                                                @break
                                            @case('paid')
                                                <span class="badge badge-success" style="font-size: 14px; padding: 6px 12px;">Paid</span>
                                                @break
                                            @case('overdue')
                                                <span class="badge badge-danger" style="font-size: 14px; padding: 6px 12px;">Overdue</span>
                                                @break
                                            @case('awaiting_payment')
                                                <span class="badge badge-info" style="font-size: 14px; padding: 6px 12px;">Awaiting Confirmation</span>
                                                @break
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Payment Method:</strong></td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $installment->payment_method ?? 'Not specified')) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Days Until Due:</strong></td>
                                    <td>
                                        @if($installment->status === 'paid')
                                            <span class="text-success">Paid</span>
                                        @elseif($installment->days_until_due < 0)
                                            <span class="text-danger font-weight-bold">{{ abs($installment->days_until_due) }} days overdue</span>
                                        @elseif($installment->days_until_due == 0)
                                            <span class="text-warning font-weight-bold">Due today</span>
                                        @else
                                            <span class="text-info">{{ $installment->days_until_due }} days remaining</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $installment->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Updated:</strong></td>
                                    <td>{{ $installment->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>

                            @if($installment->notes)
                                <div class="mt-2">
                                    <strong>Notes:</strong>
                                    <div class="alert alert-info mt-1">{{ $installment->notes }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column: Student & Order Info -->
                <div class="col-md-6">
                    <!-- Student Info -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Student & Order</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                @if($orderSpecifique->student)
                                <tr>
                                    <td width="40%"><strong>Student:</strong></td>
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
                                @endif
                                <tr>
                                    <td><strong>Course:</strong></td>
                                    <td>{{ $orderSpecifique->product_title }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Order Total:</strong></td>
                                    <td>${{ number_format($orderSpecifique->total_price, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Order Status:</strong></td>
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
                                    <td><strong>Progress:</strong></td>
                                    <td>{{ $orderSpecifique->paid_installments }}/{{ $orderSpecifique->total_installments }} installments paid</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Payment Receipt -->
                    @if($installment->payment_receipt)
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i data-feather="file-text" class="me-1"></i> Payment Receipt</h5>
                        </div>
                        <div class="card-body text-center">
                            @php
                                $receiptPath = $installment->payment_receipt;
                                $extension = pathinfo($receiptPath, PATHINFO_EXTENSION);
                                $downloadUrl = route('admin.order-specifiques.download-installment-receipt', $installment->id);
                            @endphp

                            @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                <img src="{{ $downloadUrl }}" alt="Payment Receipt" class="img-fluid rounded mb-3" style="max-height: 400px;">
                            @else
                                <div class="mb-3">
                                    <i data-feather="file" style="width:64px; height:64px;" class="text-muted"></i>
                                    <p class="mt-2 text-muted">{{ strtoupper($extension) }} Document</p>
                                </div>
                            @endif

                            <div>
                                <a href="{{ $downloadUrl }}" target="_blank" class="btn btn-info">
                                    <i data-feather="download" class="me-1"></i> View / Download Receipt
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Actions</h5>
                        </div>
                        <div class="card-body">
                            @if($installment->status === 'pending' || $installment->status === 'overdue' || $installment->status === 'awaiting_payment')
                                <!-- Mark as Paid Form -->
                                <div class="card border-success mb-3">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0">Mark Installment as Paid</h6>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('admin.order-specifiques.mark-installment-paid', $installment->id) }}" method="POST">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label><strong>Amount:</strong></label>
                                                        <p class="h5 text-primary">${{ number_format($installment->amount + ($installment->late_fee ?? 0), 2) }}</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="payment_method">Payment Method</label>
                                                        <select name="payment_method" id="payment_method" class="form-control">
                                                            <option value="">Select method...</option>
                                                            <option value="cash" {{ $installment->payment_method === 'cash' ? 'selected' : '' }}>Cash</option>
                                                            <option value="bank_transfer" {{ $installment->payment_method === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                                            <option value="credit_card" {{ $installment->payment_method === 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                                            <option value="swish" {{ $installment->payment_method === 'swish' ? 'selected' : '' }}>Swish</option>
                                                            <option value="cash_on_delivery" {{ $installment->payment_method === 'cash_on_delivery' ? 'selected' : '' }}>Cash on Delivery</option>
                                                            <option value="other" {{ $installment->payment_method === 'other' ? 'selected' : '' }}>Other</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="paid_date">Payment Date</label>
                                                        <input type="date"
                                                               name="paid_date"
                                                               id="paid_date"
                                                               class="form-control"
                                                               value="{{ optional($installment->paid_date)->format('Y-m-d') ?? now()->format('Y-m-d') }}"
                                                               required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="notes">Notes</label>
                                                        <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="Optional notes...">{{ $installment->notes }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-success mt-2" onclick="return confirm('Confirm marking this installment as paid?')">
                                                <i data-feather="check" class="me-1"></i> Confirm Payment — Mark as Paid
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endif

                            @if($installment->status === 'paid')
                                <form action="{{ route('admin.order-specifiques.update-installment-paid-date', $installment->id) }}"
                                      method="POST"
                                      class="d-inline-flex align-items-end me-2"
                                      style="gap: 8px;">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group mb-0">
                                        <label for="edit_paid_date" class="mb-1"><strong>Payment Date</strong></label>
                                        <input type="date"
                                               id="edit_paid_date"
                                               name="paid_date"
                                               class="form-control"
                                               value="{{ optional($installment->paid_date)->format('Y-m-d') }}"
                                               required>
                                    </div>
                                    <button type="submit" class="btn btn-primary" onclick="return confirm('Update payment date for this installment?')">
                                        <i data-feather="calendar" class="me-1"></i> Update Payment Date
                                    </button>
                                </form>

                                <!-- Revert to Pending -->
                                <form action="{{ route('admin.order-specifiques.mark-installment-pending', $installment->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-warning" onclick="return confirm('Revert this installment to pending? Payment info will be removed.')">
                                        <i data-feather="rotate-ccw" class="me-1"></i> Revert to Pending
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('admin.order-specifiques.show', $orderSpecifique->id) }}" class="btn btn-secondary ml-2">
                                <i data-feather="arrow-left" class="me-1"></i> Back to Order
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>
@endsection
