@extends('student-dashboard.layouts.app')

@section('title', __('messages.account_suspended', ['default' => 'Account Suspended']))

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center mt-5">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-danger border-2">
                <div class="card-body text-center py-5">
                    <!-- Suspended Icon -->
                    <div class="mb-4">
                        <div class="bg-danger bg-opacity-10 d-inline-flex rounded-circle p-4">
                            <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 3rem;"></i>
                        </div>
                    </div>

                    <!-- Title -->
                    <h2 class="fw-bold text-danger mb-3">
                        {{ __('messages.account_suspended_title', ['default' => 'Account Access Suspended']) }}
                    </h2>

                    <!-- Message -->
                    <p class="text-muted fs-5 mb-4">
                        {{ __('messages.pay_overdue_installment', ['default' => 'Please pay your overdue installment to restore access.']) }}
                    </p>

                    <!-- Suspended Orders Info -->
                    @if(isset($suspendedOrders) && $suspendedOrders->isNotEmpty())
                        <div class="text-start mb-4">
                            @foreach($suspendedOrders as $order)
                                <div class="alert alert-danger d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $order->product->titre ?? $order->product->title ?? 'Course #' . $order->product_id }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            {{ __('messages.overdue_amount', ['default' => 'Overdue Amount']) }}:
                                            <strong class="text-danger">${{ number_format($order->total_due, 2) }}</strong>
                                            @if($order->late_fee_total > 0)
                                                ({{ __('messages.includes_late_fees', ['default' => 'includes']) }} ${{ number_format($order->late_fee_total, 2) }} {{ __('messages.in_late_fees', ['default' => 'in late fees']) }})
                                            @endif
                                        </small>
                                    </div>
                                    <form action="{{ route('student-dashboard.installments.pay', ['locale' => app()->getLocale(), 'orderSpecifique' => $order->id]) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="payment_method" value="stripe">
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="bi bi-credit-card"></i> {{ __('messages.pay_now', ['default' => 'Pay Now']) }}
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Late Fee Notice -->
                    <div class="bg-warning bg-opacity-10 rounded p-3 mb-4">
                        <i class="bi bi-clock text-warning me-2"></i>
                        <small class="text-muted">
                            {{ __('messages.late_fee_daily_notice', ['default' => 'A $5.00 late fee is charged daily for each overdue installment. Pay now to stop accumulating fees.']) }}
                        </small>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('student-dashboard.installments.index') }}" class="btn btn-primary btn-lg">
                            <i class="bi bi-credit-card-2-front"></i> {{ __('messages.view_installments', ['default' => 'View My Installments']) }}
                        </a>
                        <a href="{{ route('student-dashboard.support.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-headset"></i> {{ __('messages.contact_support', ['default' => 'Contact Support']) }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
