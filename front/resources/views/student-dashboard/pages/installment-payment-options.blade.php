@extends('student-dashboard.layouts.app')

@section('title', __('messages.choose_payment_method', ['default' => 'Choose Payment Method']))

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('student-dashboard.index') }}">{{ __('messages.dashboard', ['default' => 'Dashboard']) }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('student-dashboard.installments.index') }}">{{ __('messages.installments', ['default' => 'My Installments']) }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('student-dashboard.installments.show', ['locale' => app()->getLocale(), 'orderSpecifique' => $order->id]) }}">{{ __('messages.details', ['default' => 'Details']) }}</a></li>
            <li class="breadcrumb-item active">{{ __('messages.choose_payment_method', ['default' => 'Choose Payment Method']) }}</li>
        </ol>
    </nav>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h4 class="fw-bold mb-2">{{ __('messages.installment', ['default' => 'Installment']) }} #{{ $nextInstallment->installment_number }}</h4>
                    <p class="text-muted mb-0">{{ $order->product->titre ?? $order->product->title ?? 'Course' }}</p>
                    <div class="mt-3 d-flex justify-content-between align-items-center">
                        <span>{{ __('messages.total_due_col', ['default' => 'Total Due']) }}</span>
                        <span class="h4 fw-bold text-primary mb-0">@price($amountDue)</span>
                    </div>
                    @if(($nextInstallment->late_fee ?? 0) > 0)
                        <small class="text-danger d-block mt-2">{{ __('messages.includes_late_fee', ['default' => 'Includes late fee']) }}: @price($nextInstallment->late_fee)</small>
                    @endif
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="fw-bold"><i class="bi bi-credit-card me-2 text-primary"></i>{{ __('messages.credit_card', ['default' => 'Credit Card']) }}</h5>
                            <p class="text-muted small mb-1">{{ __('messages.secure_payment') }}</p>
                            <p class="small text-secondary mb-3"><i class="bi bi-globe2 me-1"></i>{{ __('messages.credit_card_international_only') }}</p>
                            <form action="{{ route('student-dashboard.installments.pay', ['locale' => app()->getLocale(), 'orderSpecifique' => $order->id]) }}" method="POST">
                                @csrf
                                <input type="hidden" name="payment_method" value="stripe">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-lock me-1"></i>{{ __('messages.pay_now', ['default' => 'Pay Now']) }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="fw-bold"><i class="bi bi-cash-coin me-2 text-success"></i>{{ __('messages.cash_on_delivery', ['default' => 'Cash Payment']) }}</h5>
                            <p class="text-muted small mb-3">{{ __('messages.cash_payment_installment_success', ['default' => 'Submit request and complete payment at our office.']) }}</p>
                            <form action="{{ route('student-dashboard.installments.pay', ['locale' => app()->getLocale(), 'orderSpecifique' => $order->id]) }}" method="POST">
                                @csrf
                                <input type="hidden" name="payment_method" value="cash_on_delivery">
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bi bi-send me-1"></i>{{ __('messages.submit_payment', ['default' => 'Submit Payment']) }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div id="installment-bank-transfer" class="card border-0 shadow-sm mt-3">
                <div class="card-body">
                    <h5 class="fw-bold mb-3"><i class="bi bi-bank me-2 text-secondary"></i>{{ __('messages.bank_transfer', ['default' => 'Bank Transfer']) }}</h5>

                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <strong>{{ __('messages.bank_transfer_instructions', ['default' => 'Bank Transfer Instructions']) }}</strong>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm mb-0">
                                <tr><td class="fw-bold" style="width: 170px;">{{ __('messages.bank_name', ['default' => 'Bank Name']) }}</td><td>{{ env('BANK_NAME', 'Upskills Academy Bank') }}</td></tr>
                                <tr><td class="fw-bold">{{ __('messages.account_name', ['default' => 'Account Name']) }}</td><td>{{ env('BANK_ACCOUNT_NAME', 'Upskills Academy') }}</td></tr>
                                <tr><td class="fw-bold">{{ __('messages.account_number', ['default' => 'Account Number']) }}</td><td><code>{{ env('BANK_ACCOUNT_NUMBER', '1234567890') }}</code></td></tr>
                                <tr><td class="fw-bold">{{ __('messages.iban', ['default' => 'IBAN']) }}</td><td><code>{{ env('BANK_IBAN', 'SE00 0000 0000 0000 0000 0000') }}</code></td></tr>
                                <tr><td class="fw-bold">{{ __('messages.swift_code', ['default' => 'SWIFT/BIC']) }}</td><td><code>{{ env('BANK_SWIFT', 'SWEDSESS') }}</code></td></tr>
                                <tr><td class="fw-bold">{{ __('messages.reference', ['default' => 'Reference']) }}</td><td><code>INS-{{ $order->id }}-{{ $nextInstallment->installment_number }}</code></td></tr>
                            </table>
                        </div>
                    </div>

                    <form action="{{ route('student-dashboard.installments.pay', ['locale' => app()->getLocale(), 'orderSpecifique' => $order->id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="payment_method" value="bank_transfer">

                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('messages.payment_description', ['default' => 'Payment Description']) }}</label>
                            <textarea name="payment_description" class="form-control" rows="2" placeholder="{{ __('messages.enter_payment_description', ['default' => 'Enter any additional details about your payment...']) }}"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('messages.upload_receipt', ['default' => 'Upload Payment Receipt']) }} <span class="text-danger">*</span></label>
                            <input type="file" name="payment_receipt" class="form-control" accept="image/*,application/pdf" required>
                            <small class="text-muted">{{ __('messages.receipt_format_hint', ['default' => 'Accepted formats: JPG, PNG, PDF (max 5MB)']) }}</small>
                        </div>

                        <button type="submit" class="btn btn-secondary">
                            <i class="bi bi-upload me-1"></i>{{ __('messages.submit_payment', ['default' => 'Submit Payment']) }}
                        </button>
                    </form>
                </div>
            </div>

            <div class="text-center mt-4 mb-2">
                <a href="{{ route('student-dashboard.installments.show', ['locale' => app()->getLocale(), 'orderSpecifique' => $order->id]) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>{{ __('messages.back_to_installments', ['default' => 'Back to Installments']) }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
