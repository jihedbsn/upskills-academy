@extends('student-dashboard.layouts.app')

@section('title', __('messages.my_orders'))

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('student-dashboard.index') }}">{{ __('messages.dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('messages.my_orders') }}</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">{{ __('messages.my_orders') }}</h1>
            <p class="text-muted">{{ __('messages.view_order_history') }}</p>
        </div>
    </div>

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="bi bi-info-circle me-2"></i>{{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Orders List -->
    <div class="row">
        @forelse($orderGroups as $groupKey => $groupedOrders)
            @php
                $firstOrder = $groupedOrders->first();
                $isBankTransfer = $firstOrder->payment_method === 'bank_transfer';
                $isPending = $firstOrder->payment_status === 'pending';
                $isApproved = $firstOrder->payment_status === 'approved';
                $isRejected = $firstOrder->payment_status === 'rejected';
            @endphp

            <div class="col-12 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                        <div>
                            <h5 class="mb-1 fw-bold">
                                <i class="bi bi-receipt" style="color: #0071ae;"></i> {{ __('messages.order') }} #{{ $firstOrder->id }}
                            </h5>
                            <small class="text-muted">
                                <i class="bi bi-calendar3"></i> {{ $firstOrder->created_at->format('M d, Y') }}
                                <i class="bi bi-clock ms-2"></i> {{ $firstOrder->created_at->format('H:i') }}
                            </small>
                        </div>
                        <div>
                            @if($isRejected)
                                <span class="badge bg-danger px-3 py-2">
                                    <i class="bi bi-x-circle-fill"></i> {{ __('messages.rejected') }}
                                </span>
                            @elseif($isBankTransfer)
                                @if($isPending)
                                    <span class="badge bg-warning text-dark px-3 py-2">
                                        <i class="bi bi-clock-history"></i> {{ __('messages.pending_approval') }}
                                    </span>
                                @elseif($isApproved)
                                    <span class="badge bg-success px-3 py-2">
                                        <i class="bi bi-check-circle-fill"></i> {{ __('messages.approved') }}
                                    </span>
                                @endif
                            @elseif($firstOrder->payment_success)
                                <span class="badge bg-success px-3 py-2">
                                    <i class="bi bi-check-circle-fill"></i> {{ __('messages.paid') }}
                                </span>
                            @else
                                <span class="badge bg-secondary px-3 py-2">
                                    <i class="bi bi-hourglass-split"></i> {{ __('messages.unpaid') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <!-- Payment Method and Total -->
                        <div class="row mb-4 pb-3 border-bottom">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="text-muted small mb-2">{{ __('messages.payment_method') }}</div>
                                <span class="badge bg-info text-dark px-3 py-2">
                                    @if($firstOrder->payment_method === 'credit_card')
                                        <i class="bi bi-credit-card-2-front"></i> {{ __('messages.credit_card') }}
                                    @elseif($firstOrder->payment_method === 'bank_transfer')
                                        <i class="bi bi-bank2"></i> {{ __('messages.online_bank_transfer') }}
                                    @elseif($firstOrder->payment_method === 'paypal')
                                        <i class="bi bi-paypal"></i> PayPal
                                    @elseif($firstOrder->payment_method === 'cash_on_delivery')
                                        <i class="bi bi-cash-coin"></i> {{ __('messages.cash_on_delivery') }}
                                    @else
                                        {{ ucfirst(str_replace('_', ' ', $firstOrder->payment_method)) }}
                                    @endif
                                </span>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <div class="text-muted small mb-2">{{ __('messages.total') }}</div>
                                <div class="h4 fw-bold mb-0" style="color: #0071ae;">@price($groupedOrders->sum('price'))</div>
                            </div>
                        </div>

                        <!-- Bank Transfer Payment Info -->
                        @if($isBankTransfer && $isPending)
                            <div class="alert border-0 mb-4" style="background-color: #e6f4fb; border-left: 4px solid #0071ae !important;">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <h6 class="fw-bold mb-3">
                                            <i class="bi bi-bank2" style="color: #0071ae;"></i> {{ __('messages.bank_transfer_instructions') }}
                                        </h6>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="bg-white p-3 rounded-3 border mb-2">
                                            <div class="small text-muted mb-1">IBAN</div>
                                            <div class="fw-bold text-dark" style="font-family: monospace;" dir="ltr">{{ env('BANK_TRANSFER_IBAN') }}</div>
                                        </div>
                                        <div class="bg-white p-3 rounded-3 border">
                                            <div class="small text-muted mb-1">{{ __('messages.rib') }}</div>
                                            <div class="fw-bold text-dark" style="font-family: monospace;">{{ env('BANK_TRANSFER_RIB') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        @if($firstOrder->payment_description)
                                            <div class="bg-white p-3 rounded-3 border mb-2">
                                                <div class="small text-muted mb-1">
                                                    <i class="bi bi-chat-left-text"></i> {{ __('messages.your_note') }}
                                                </div>
                                                <div class="text-dark">{{ $firstOrder->payment_description }}</div>
                                            </div>
                                        @endif
                                        @if($firstOrder->payment_receipt)
                                            <div class="bg-white p-3 rounded-3 border">
                                                <div class="small text-muted mb-2">
                                                    <i class="bi bi-file-earmark-check"></i> {{ __('messages.receipt_uploaded') }}
                                                </div>
                                                @php
                                                    // Get base URL without locale suffix for storage access
                                                    $baseUrl = preg_replace('#/(en|ar)/?$#', '', rtrim(config('app.url'), '/'));
                                                    $receiptUrl = $baseUrl . '/storage/' . $firstOrder->payment_receipt;
                                                @endphp
                                                <a href="{{ $receiptUrl }}" target="_blank" class="btn btn-sm" style="border: 1px solid #0071ae; color: #0071ae;">
                                                    <i class="bi bi-file-earmark-pdf"></i> {{ __('messages.view_receipt') }}
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-12">
                                        <div class="border-top pt-2 mt-2">
                                            <small class="text-muted">
                                                <i class="bi bi-info-circle"></i> {{ __('messages.pending_payment_verification') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Order Items -->
                        <h6 class="mb-3 fw-bold">
                            <i class="bi bi-list-ul"></i> {{ __('messages.order_items') }}
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0">{{ __('messages.item') }}</th>
                                        <th class="border-0">{{ __('messages.type') }}</th>
                                        <th class="border-0 text-end">{{ __('messages.price') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($groupedOrders as $order)
                                        <tr>
                                            <td>
                                                @if($order->product)
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-book me-2" style="color: #0071ae;"></i>
                                                        <strong>{{ $order->product->titre }}</strong>
                                                    </div>
                                                @elseif($order->book)
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-journal-text text-info me-2"></i>
                                                        <strong>{{ $order->book->titre }}</strong>
                                                    </div>
                                                @else
                                                    <span class="text-muted">{{ __('messages.unknown_item') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($order->product)
                                                    <span class="badge bg-success bg-opacity-10 text-success border border-success">
                                                        {{ __('messages.course') }}
                                                    </span>
                                                @elseif($order->book)
                                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary">
                                                        {{ __('messages.book') }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <span class="fw-bold text-dark">@price($order->price)</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr class="table-light fw-bold">
                                        <td colspan="2" class="text-end">{{ __('messages.total') }}:</td>
                                        <td class="text-end fs-5" style="color: #0071ae;">@price($groupedOrders->sum('price'))</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="bi bi-cart-x text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                        </div>
                        <h5 class="mb-2 fw-bold">{{ __('messages.no_orders_found') }}</h5>
                        <p class="text-muted mb-4">{{ __('messages.no_orders_description') }}</p>
                        <a href="{{ route('courses.index') }}" class="btn btn-lg" style="background-color: #0071ae; color: white;">
                            <i class="bi bi-search"></i> {{ __('messages.browse_courses') }}
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($orders->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
