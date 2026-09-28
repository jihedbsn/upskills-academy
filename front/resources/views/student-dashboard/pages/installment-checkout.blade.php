@extends('student-dashboard.layouts.app')

@section('title', __('messages.installment_checkout'))

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('student-dashboard.index') }}">{{ __('messages.dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('student-dashboard.installments.index') }}">{{ __('messages.installments') }}</a></li>
            <li class="breadcrumb-item active">{{ __('messages.checkout') }}</li>
        </ol>
    </nav>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom py-3">
                    <h4 class="mb-0 fw-bold">
                        <i class="bi bi-wallet2" style="color: #0071ae;"></i>
                        {{ __('messages.installment_payment_plan') }}
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Course Info -->
                    <div class="d-flex align-items-center mb-4 p-3 bg-light rounded">
                        @if($product->image)
                            <img src="{{ asset('uploads/' . $product->image) }}" alt="{{ $product->titre }}" class="rounded me-3" style="width: 80px; height: 80px; object-fit: cover;">
                        @else
                            <div class="bg-primary bg-opacity-10 rounded me-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="bi bi-mortarboard text-primary fs-2"></i>
                            </div>
                        @endif
                        <div>
                            <h5 class="mb-1 fw-bold">{{ $product->titre ?? $product->title }}</h5>
                            <p class="text-muted mb-0">
                                <i class="bi bi-clock"></i> {{ $product->validity_months ?? '—' }} {{ __('messages.months') }}
                                <span class="mx-2">|</span>
                                <i class="bi bi-tag"></i> @price($product->prix ?? $product->price)
                            </p>
                        </div>
                    </div>

                    <!-- Payment Plan Breakdown -->
                    <h5 class="fw-bold mb-3"><i class="bi bi-list-columns-reverse"></i> {{ __('messages.payment_plan') }}</h5>

                    @php
                        $months = $product->validity_months ?? 1;
                        $totalPrice = $totalPrice ?? ($product->prix ?? $product->price);
                        $monthlyAmount = $months > 0 ? $totalPrice / $months : $totalPrice;
                    @endphp

                    @if(!empty($referralDiscount) && $referralDiscount > 0)
                        <div class="alert alert-success d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <i class="bi bi-gift me-2"></i>
                                <strong>{{ __('messages.referral_discount', ['default' => 'Referral discount']) }}</strong>
                                ({{ rtrim(rtrim(number_format($referralPct, 2), '0'), '.') }}% {{ __('messages.first_order_off', ['default' => 'off your first order']) }})
                                <div class="small text-muted mt-1">
                                    {{ __('messages.was', ['default' => 'Was']) }} <s>@price($originalTotal)</s>
                                </div>
                            </div>
                            <span class="text-success fw-bold">-@price($referralDiscount)</span>
                        </div>
                    @endif

                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('messages.installment_number') }}</th>
                                    <th>{{ __('messages.amount') }}</th>
                                    <th>{{ __('messages.due_date') }}</th>
                                    <th>{{ __('messages.content_unlocked') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for($i = 1; $i <= $months; $i++)
                                    <tr class="{{ $i === 1 ? 'table-success' : '' }}">
                                        <td class="fw-bold">
                                            {{ $i }}
                                            @if($i === 1)
                                                <span class="badge bg-success ms-1">{{ __('messages.pay_now_badge') }}</span>
                                            @endif
                                        </td>
                                        <td>@price($monthlyAmount)</td>
                                        <td>
                                            @if($i === 1)
                                                {{ __('messages.today') }}
                                            @else
                                                {{ now()->addMonths($i - 1)->format('M d, Y') }}
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ __('messages.month_content', ['month' => $i]) }}</small>
                                        </td>
                                    </tr>
                                @endfor
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td class="fw-bold">{{ __('messages.total') }}</td>
                                    <td class="fw-bold">@price($totalPrice)</td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Important Info -->
                    <div class="alert alert-info mb-4">
                        <h6 class="fw-bold mb-2"><i class="bi bi-info-circle"></i> {{ __('messages.important_info') }}</h6>
                        <ul class="mb-0 small">
                            <li>{{ __('messages.installment_info_1') }}</li>
                            <li>{{ __('messages.installment_info_2') }}</li>
                            <li>{{ __('messages.installment_info_3') }}</li>
                            <li>{{ __('messages.installment_info_4') }}</li>
                            <li>{{ __('messages.installment_info_5') }}</li>
                        </ul>
                    </div>

                    <h5 class="fw-bold mb-3"><i class="bi bi-hand-index"></i> {{ __('messages.choose_payment_method') }}</h5>
                    <p class="text-muted small mb-4">{{ __('messages.installment_first_payment_intro') }}</p>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="card h-100 border shadow-sm">
                                <div class="card-body">
                                    <h6 class="fw-bold"><i class="bi bi-credit-card me-2 text-primary"></i>{{ __('messages.credit_card') }}</h6>
                                    <p class="text-muted small mb-1">{{ __('messages.secure_payment') }}</p>
                                    <p class="small text-secondary mb-3"><i class="bi bi-globe2 me-1"></i>{{ __('messages.credit_card_international_only') }}</p>
                                    <form action="{{ route('student-dashboard.installments.checkout-start-payment', ['locale' => app()->getLocale(), 'product' => $product->id]) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="payment_method" value="stripe">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="bi bi-lock me-1"></i>{{ __('messages.pay_with_card') }} — @price($monthlyAmount)
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100 border shadow-sm">
                                <div class="card-body">
                                    <h6 class="fw-bold"><i class="bi bi-cash-coin me-2 text-success"></i>{{ __('messages.cash_on_delivery') }}</h6>
                                    <p class="text-muted small mb-3">{{ __('messages.cash_payment_installment_desc') }}</p>
                                    <form action="{{ route('student-dashboard.installments.checkout-start-payment', ['locale' => app()->getLocale(), 'product' => $product->id]) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="payment_method" value="cash_on_delivery">
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="bi bi-send me-1"></i>{{ __('messages.request_cash_payment') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border shadow-sm">
                        <div class="card-body">
                            <h6 class="fw-bold mb-2"><i class="bi bi-bank me-2 text-secondary"></i>{{ __('messages.bank_transfer') }}</h6>
                            <p class="text-muted small mb-3">{{ __('messages.bank_transfer_installment_checkout_blurb') }}</p>
                            <form action="{{ route('student-dashboard.installments.checkout-start-payment', ['locale' => app()->getLocale(), 'product' => $product->id]) }}" method="POST">
                                @csrf
                                <input type="hidden" name="payment_method" value="bank_transfer">
                                <button type="submit" class="btn btn-outline-secondary w-100">
                                    <i class="bi bi-arrow-right-circle me-1"></i>{{ __('messages.continue_with_bank_transfer') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
