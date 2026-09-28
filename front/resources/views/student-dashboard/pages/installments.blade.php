@extends('student-dashboard.layouts.app')

@section('title', __('messages.installments', ['default' => 'My Installments']))

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('student-dashboard.index') }}">{{ __('messages.dashboard', ['default' => 'Dashboard']) }}</a></li>
            <li class="breadcrumb-item active">{{ __('messages.installments', ['default' => 'My Installments']) }}</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">{{ __('messages.installments', ['default' => 'My Installments']) }}</h1>
            <p class="text-muted">{{ __('messages.manage_installment_payments', ['default' => 'Manage your installment payment plans']) }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="bi bi-info-circle me-2"></i>{{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($orders->isEmpty())
        <!-- Empty State -->
        <div class="text-center py-5">
            <i class="bi bi-credit-card-2-front" style="font-size: 4rem; color: #ccc;"></i>
            <h4 class="mt-3 text-muted">{{ __('messages.no_installment_orders', ['default' => 'No Installment Plans']) }}</h4>
            <p class="text-muted">{{ __('messages.no_installment_orders_desc', ['default' => 'You don\'t have any active installment payment plans yet.']) }}</p>
        </div>
    @else
        <!-- Installment Orders -->
        @foreach($orders as $order)
            @php
                $product = $order->product;
                $totalInstallments = $order->installments->count();
                $paidInstallments = $order->installments->where('status', 'paid')->count();
                $awaitingCount = $order->installments->where('status', 'awaiting_payment')->count();
                $nextInstallment = $order->installments->whereIn('status', ['pending', 'overdue'])->sortBy('installment_number')->first();
                $progressPercent = $totalInstallments > 0 ? ($paidInstallments / $totalInstallments) * 100 : 0;
            @endphp
            <div class="card shadow-sm border-0 mb-4 {{ $order->is_suspended ? 'border-danger border-2' : '' }}">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                    <div>
                        <h5 class="mb-1 fw-bold">
                            <i class="bi bi-mortarboard" style="color: #0071ae;"></i>
                            {{ $product->titre ?? $product->title ?? 'Course #' . $product->id }}
                        </h5>
                        <small class="text-muted">
                            <i class="bi bi-calendar3"></i> {{ __('messages.started', ['default' => 'Started']) }}: {{ $order->created_at->format('M d, Y') }}
                            <span class="mx-2">|</span>
                            <i class="bi bi-cash-stack"></i> {{ __('messages.total', ['default' => 'Total']) }}: @price($order->total_price)
                        </small>
                    </div>
                    <div class="text-end">
                        @if($order->is_suspended)
                            <span class="badge bg-danger px-3 py-2">
                                <i class="bi bi-exclamation-triangle-fill"></i> {{ __('messages.suspended', ['default' => 'Suspended']) }}
                            </span>
                        @elseif($order->isAllPaid())
                            <span class="badge bg-success px-3 py-2">
                                <i class="bi bi-check-circle-fill"></i> {{ __('messages.fully_paid', ['default' => 'Fully Paid']) }}
                            </span>
                        @else
                            <span class="badge bg-primary px-3 py-2">
                                <i class="bi bi-hourglass-split"></i> {{ __('messages.in_progress', ['default' => 'In Progress']) }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <!-- Progress Bar -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">{{ __('messages.payment_progress', ['default' => 'Payment Progress']) }}</small>
                            <small class="fw-bold">{{ $paidInstallments }}/{{ $totalInstallments }} {{ __('messages.installments_paid', ['default' => 'installments paid']) }}</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar {{ $order->is_suspended ? 'bg-danger' : 'bg-success' }}"
                                 role="progressbar"
                                 style="width: {{ $progressPercent }}%"
                                 aria-valuenow="{{ $progressPercent }}"
                                 aria-valuemin="0"
                                 aria-valuemax="100">
                            </div>
                        </div>
                    </div>

                    <!-- Installment Summary Row -->
                    <div class="row text-center">
                        <div class="col-md-3 col-6 mb-2">
                            <div class="p-2 rounded bg-light">
                                <small class="text-muted d-block">{{ __('messages.paid_amount', ['default' => 'Paid']) }}</small>
                                <strong class="text-success">@price($order->installments->where('status', 'paid')->sum('amount'))</strong>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <div class="p-2 rounded bg-light">
                                <small class="text-muted d-block">{{ __('messages.remaining', ['default' => 'Remaining']) }}</small>
                                <strong class="text-danger">@price($order->total_due)</strong>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <div class="p-2 rounded bg-light">
                                <small class="text-muted d-block">{{ __('messages.late_fees', ['default' => 'Late Fees']) }}</small>
                                <strong class="{{ $order->late_fee_total > 0 ? 'text-danger' : 'text-muted' }}">@price($order->late_fee_total ?? 0)</strong>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <div class="p-2 rounded bg-light">
                                <small class="text-muted d-block">{{ __('messages.next_due', ['default' => 'Next Due']) }}</small>
                                <strong class="{{ $nextInstallment && $nextInstallment->due_date < now() ? 'text-danger' : '' }}">
                                    {{ $nextInstallment ? $nextInstallment->due_date->format('M d, Y') : '—' }}
                                </strong>
                            </div>
                        </div>
                    </div>

                    <!-- Suspended Warning -->
                    @if($order->is_suspended)
                        <div class="alert alert-danger mt-3 mb-0">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>{{ __('messages.account_suspended_title', ['default' => 'Account Suspended']) }}</strong><br>
                            {{ __('messages.pay_overdue_installment', ['default' => 'Please pay your overdue installment to restore access.']) }}
                        </div>
                    @endif
                </div>

                <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center">
                    <a href="{{ route('student-dashboard.installments.show', ['locale' => app()->getLocale(), 'orderSpecifique' => $order->id]) }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-eye"></i> {{ __('messages.view_details', ['default' => 'View Details']) }}
                    </a>
                    <div>
                        @if($awaitingCount > 0)
                            <span class="badge bg-info px-3 py-2 me-2">
                                <i class="bi bi-hourglass-split"></i> {{ $awaitingCount }} {{ __('messages.awaiting_confirmation', ['default' => 'awaiting confirmation']) }}
                            </span>
                        @endif
                        @if($nextInstallment)
                            <a href="{{ route('student-dashboard.installments.show', ['locale' => app()->getLocale(), 'orderSpecifique' => $order->id]) }}" class="btn btn-success btn-sm">
                                <i class="bi bi-credit-card"></i>
                                {{ __('messages.pay_installment', ['default' => 'Pay Installment']) }}
                                (#{{ $nextInstallment->installment_number }}) — @price($nextInstallment->total_due)
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection
