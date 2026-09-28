@extends('student-dashboard.layouts.app')

@section('title', __('student_dashboard.ext_extend_course'))

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mt-3 mt-md-0">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('student-dashboard.index') }}">{{ __('student_dashboard.dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('student-dashboard.course-extensions.index') }}">{{ __('student_dashboard.ext_course_extensions') }}</a></li>
            <li class="breadcrumb-item active">{{ __('student_dashboard.ext_extend_course') }}</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Course Info Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-arrow-clockwise me-2"></i>{{ __('student_dashboard.ext_extend_course') }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-start mb-4">
                        <img src="{{ $product->image ? config('app.file_url') . 'uploads/products/images/' . $product->image : asset('assets/img/placeholders/course-not-found.png') }}"
                             class="rounded me-4" width="120" height="80" style="object-fit: cover;"
                             onerror="this.src='{{ asset('assets/img/placeholders/course-not-found.png') }}'">
                        <div>
                            <h4 class="mb-1">{{ $product->titre }}</h4>
                            @if($product->teacher)
                                <p class="text-muted mb-1"><i class="bi bi-person me-1"></i>{{ $product->teacher->nom ?? '' }}</p>
                            @endif
                            @if($product->category)
                                <span class="badge bg-light text-dark"><i class="bi bi-tag me-1"></i>{{ $product->category->nom ?? '' }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Current Access Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="border rounded p-3 text-center">
                                <small class="text-muted d-block mb-1">{{ __('student_dashboard.ext_current_expiry') }}</small>
                                @if($productStudent->expiration_date)
                                    <h5 class="mb-0 {{ $productStudent->expiration_date->isPast() ? 'text-danger' : 'text-warning' }}">
                                        {{ $productStudent->expiration_date->format('M d, Y') }}
                                    </h5>
                                    @if($productStudent->expiration_date->isPast())
                                        <span class="badge bg-danger mt-1">{{ __('student_dashboard.ext_expired') }}</span>
                                    @else
                                        <span class="badge bg-warning text-dark mt-1">{{ $productStudent->daysRemaining() }} {{ __('student_dashboard.ext_days_left') }}</span>
                                    @endif
                                @else
                                    <h5 class="mb-0 text-success">{{ __('student_dashboard.ext_lifetime') }}</h5>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 text-center">
                                <small class="text-muted d-block mb-1">{{ __('student_dashboard.ext_after_extension') }}</small>
                                @php
                                    $baseDate = $productStudent->expiration_date && $productStudent->expiration_date->isFuture()
                                        ? $productStudent->expiration_date
                                        : now();
                                    $newDate = $baseDate->copy()->addMonths($extensionMonths);
                                @endphp
                                <h5 class="mb-0 text-success">{{ $newDate->format('M d, Y') }}</h5>
                                <span class="badge bg-success mt-1">+{{ $extensionMonths }} {{ __('student_dashboard.ext_months') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="bg-light rounded p-3 mb-4">
                        <h6 class="mb-3">{{ __('student_dashboard.ext_order_summary') }}</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('student_dashboard.ext_extension_period') }}:</span>
                            <span class="fw-bold">{{ $extensionMonths }} {{ __('student_dashboard.ext_months') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('student_dashboard.ext_renewal_price') }}:</span>
                            <span class="fw-bold">@price($extensionPrice)</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="h5 mb-0">{{ __('student_dashboard.ext_total') }}:</span>
                            <span class="h5 mb-0 text-primary">@price($extensionPrice)</span>
                        </div>
                    </div>

                    <!-- Payment Form -->
                    <form action="{{ route('student-dashboard.course-extensions.process', ['product' => $product->id]) }}"
                          method="POST" enctype="multipart/form-data" id="extensionPaymentForm">
                        @csrf

                        <h6 class="mb-3">{{ __('student_dashboard.ext_payment_method') }}</h6>

                        <!-- Payment Method Selection -->
                        <div class="row mb-3 g-2">
                            <div class="col-md-4">
                                <div class="form-check card p-3 border-2 h-100" id="card-credit_card">
                                    <input class="form-check-input" type="radio" name="payment_method" value="credit_card"
                                           id="pm_credit_card" checked>
                                    <label class="form-check-label w-100" for="pm_credit_card">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-credit-card fs-4 me-2 text-primary"></i>
                                            <div>
                                                <strong>{{ __('student_dashboard.ext_credit_card') }}</strong>
                                                <small class="d-block text-muted">{{ __('student_dashboard.ext_pay_with_stripe') }}</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check card p-3 border-2 h-100" id="card-bank_transfer">
                                    <input class="form-check-input" type="radio" name="payment_method" value="bank_transfer"
                                           id="pm_bank_transfer">
                                    <label class="form-check-label w-100" for="pm_bank_transfer">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-bank fs-4 me-2 text-secondary"></i>
                                            <div>
                                                <strong>{{ __('student_dashboard.ext_bank_transfer') }}</strong>
                                                <small class="d-block text-muted">{{ __('student_dashboard.ext_manual_transfer') }}</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check card p-3 border-2 h-100" id="card-cash_on_delivery">
                                    <input class="form-check-input" type="radio" name="payment_method" value="cash_on_delivery"
                                           id="pm_cash_on_delivery">
                                    <label class="form-check-label w-100" for="pm_cash_on_delivery">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-cash-coin fs-4 me-2 text-success"></i>
                                            <div>
                                                <strong>{{ __('student_dashboard.ext_cash_on_delivery') }}</strong>
                                                <small class="d-block text-muted">{{ __('student_dashboard.ext_cash_on_delivery_desc') }}</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Bank Transfer Details (hidden by default) -->
                        <div id="bankTransferFields" style="display: none;">
                            <div class="mb-3">
                                <div class="bg-light rounded p-3" dir="ltr">
                                    <h6 class="mb-3">
                                        <i class="bi bi-bank me-2"></i>Bank Transfer Instructions
                                    </h6>
                                    <table class="table table-sm mb-0">
                                        <tbody>
                                            <tr>
                                                <td class="text-muted" style="width: 180px;">Company Name:</td>
                                                <td class="fw-bold">{{ env('BANK_COMPANY_NAME') ?: env('BANK_NAME') }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Bank Name:</td>
                                                <td class="fw-bold">{{ env('BANK_NAME') }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Account Number RIB:</td>
                                                <td class="fw-bold">{{ env('BANK_ACCOUNT_NUMBER') }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">IBAN:</td>
                                                <td class="fw-bold">{{ env('BANK_IBAN') }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">BIC:</td>
                                                <td class="fw-bold">{{ env('BANK_BIC') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="payment_description" class="form-label">{{ __('student_dashboard.ext_transfer_details') }}</label>
                                <textarea class="form-control" id="payment_description" name="payment_description" rows="3"
                                          placeholder="{{ __('student_dashboard.ext_transfer_placeholder') }}"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="payment_receipt" class="form-label">{{ __('student_dashboard.ext_upload_receipt') }} <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="payment_receipt" name="payment_receipt"
                                       accept=".jpg,.jpeg,.png,.pdf">
                                <small class="text-muted">{{ __('student_dashboard.ext_accepted_formats') }}</small>
                            </div>
                        </div>

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="d-flex gap-2 mt-4">
                            <a href="{{ route('student-dashboard.course-extensions.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>{{ __('student_dashboard.ext_back') }}
                            </a>
                            <button type="submit" class="btn btn-primary flex-grow-1" id="payButton">
                                <i class="bi bi-lock me-1"></i>{{ __('student_dashboard.ext_pay_now') }} - @price($extensionPrice)
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const creditCardRadio = document.getElementById('pm_credit_card');
    const bankTransferRadio = document.getElementById('pm_bank_transfer');
    const codRadio = document.getElementById('pm_cash_on_delivery');
    const bankTransferFields = document.getElementById('bankTransferFields');
    const cardCredit = document.getElementById('card-credit_card');
    const cardBank = document.getElementById('card-bank_transfer');
    const cardCod = document.getElementById('card-cash_on_delivery');

    function togglePaymentMethod() {
        bankTransferFields.style.display = bankTransferRadio.checked ? 'block' : 'none';
        cardCredit.classList.toggle('border-primary', creditCardRadio.checked);
        cardBank.classList.toggle('border-primary', bankTransferRadio.checked);
        cardCod.classList.toggle('border-primary', codRadio.checked);
    }

    creditCardRadio.addEventListener('change', togglePaymentMethod);
    bankTransferRadio.addEventListener('change', togglePaymentMethod);
    codRadio.addEventListener('change', togglePaymentMethod);

    // Initial state
    togglePaymentMethod();

    // Prevent double submission
    document.getElementById('extensionPaymentForm').addEventListener('submit', function(e) {
        const btn = document.getElementById('payButton');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>{{ __("student_dashboard.processing") }}';
    });
});
</script>
@endpush
@endsection
