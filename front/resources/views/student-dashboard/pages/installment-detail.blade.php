@extends('student-dashboard.layouts.app')

@section('title', __('messages.installment_details', ['default' => 'Installment Details']))

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('student-dashboard.index') }}">{{ __('messages.dashboard', ['default' => 'Dashboard']) }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('student-dashboard.installments.index') }}">{{ __('messages.installments', ['default' => 'My Installments']) }}</a></li>
            <li class="breadcrumb-item active">{{ __('messages.details', ['default' => 'Details']) }}</li>
        </ol>
    </nav>

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

    @php
        $product = $order->product;
        $totalInstallments = $order->installments->count();
        $paidInstallments = $order->installments->where('status', 'paid')->count();
        $progressPercent = $totalInstallments > 0 ? ($paidInstallments / $totalInstallments) * 100 : 0;
    @endphp

    <!-- Course & Order Info Header -->
    <div class="card shadow-sm border-0 mb-4 {{ $order->is_suspended ? 'border-danger border-2' : '' }}">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3 class="fw-bold mb-1">
                        <i class="bi bi-mortarboard" style="color: #0071ae;"></i>
                        {{ $product->titre ?? $product->title ?? 'Course #' . $product->id }}
                    </h3>
                    <p class="text-muted mb-2">
                        <i class="bi bi-calendar3"></i> {{ __('messages.started', ['default' => 'Started']) }}: {{ $order->created_at->format('M d, Y') }}
                        <span class="mx-2">|</span>
                        <i class="bi bi-clock-history"></i> {{ __('messages.duration', ['default' => 'Duration']) }}: {{ $product->validity_months ?? '—' }} {{ __('messages.months', ['default' => 'months']) }}
                    </p>
                    @if($order->is_suspended)
                        <span class="badge bg-danger px-3 py-2 fs-6">
                            <i class="bi bi-exclamation-triangle-fill"></i> {{ __('messages.suspended', ['default' => 'SUSPENDED']) }}
                        </span>
                    @elseif($order->isAllPaid())
                        <span class="badge bg-success px-3 py-2 fs-6">
                            <i class="bi bi-check-circle-fill"></i> {{ __('messages.fully_paid', ['default' => 'Fully Paid']) }}
                        </span>
                    @else
                        <span class="badge bg-primary px-3 py-2 fs-6">
                            <i class="bi bi-hourglass-split"></i> {{ __('messages.in_progress', ['default' => 'In Progress']) }}
                        </span>
                    @endif
                </div>
                <div class="col-md-4 text-end">
                    <div class="mb-2">
                        <small class="text-muted">{{ __('messages.total_amount', ['default' => 'Total Amount']) }}</small>
                        <h2 class="fw-bold mb-0">@price($order->total_price)</h2>
                    </div>
                    <div>
                        <small class="text-muted">{{ __('messages.remaining_balance', ['default' => 'Remaining Balance']) }}</small>
                        <h4 class="fw-bold text-danger mb-0">@price($order->total_due)</h4>
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="mt-3">
                <div class="d-flex justify-content-between mb-1">
                    <small class="text-muted">{{ __('messages.payment_progress', ['default' => 'Payment Progress']) }}</small>
                    <small class="fw-bold">{{ round($progressPercent) }}%</small>
                </div>
                <div class="progress" style="height: 10px;">
                    <div class="progress-bar {{ $order->is_suspended ? 'bg-danger' : 'bg-success' }}"
                         role="progressbar"
                         style="width: {{ $progressPercent }}%">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Suspended Warning -->
    @if($order->is_suspended)
        <div class="alert alert-danger border-danger mb-4">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill fs-3 me-3"></i>
                <div>
                    <strong>{{ __('messages.account_suspended_title', ['default' => 'Your access has been suspended']) }}</strong><br>
                    {{ __('messages.pay_overdue_installment', ['default' => 'Please pay your overdue installment to restore access.']) }}
                    @if($order->suspended_at)
                        <br><small class="text-muted">{{ __('messages.suspended_since', ['default' => 'Suspended since']) }}: {{ $order->suspended_at->format('M d, Y') }}</small>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Late Fees Summary -->
    @if($order->late_fee_total > 0)
        <div class="alert alert-warning mb-4">
            <i class="bi bi-exclamation-circle me-2"></i>
            <strong>{{ __('messages.late_fees_accumulated', ['default' => 'Late Fees Accumulated']) }}:</strong>
            @price($order->late_fee_total)
            <small class="text-muted d-block mt-1">{{ __('messages.late_fee_notice', ['default' => 'A $5.00/day late fee is applied for each overdue installment.']) }}</small>
        </div>
    @endif

    <!-- Installments Table -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 fw-bold"><i class="bi bi-list-check"></i> {{ __('messages.installment_schedule', ['default' => 'Installment Schedule']) }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="installment-table-wrap">
                <table class="table table-hover mb-0 installment-schedule-table">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('messages.amount', ['default' => 'Amount']) }}</th>
                            <th>{{ __('messages.late_fee', ['default' => 'Late Fee']) }}</th>
                            <th>{{ __('messages.total_due_col', ['default' => 'Total Due']) }}</th>
                            <th>{{ __('messages.due_date', ['default' => 'Due Date']) }}</th>
                            <th>{{ __('messages.status', ['default' => 'Status']) }}</th>
                            <th>{{ __('messages.paid_date', ['default' => 'Paid Date']) }}</th>
                            <th>{{ __('messages.action', ['default' => 'Action']) }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->installments->sortBy('installment_number') as $installment)
                            @php
                                $isOverdue = !in_array($installment->status, ['paid', 'awaiting_payment']) && $installment->due_date < now();
                                // "Pay Now" only for pending/overdue, and only if all previous ones are paid or awaiting
                                $isNext = in_array($installment->status, ['pending', 'overdue'])
                                    && !$order->installments
                                        ->where('installment_number', '<', $installment->installment_number)
                                        ->whereNotIn('status', ['paid', 'awaiting_payment'])
                                        ->count();
                            @endphp
                            <tr class="{{ $isOverdue ? 'table-danger' : ($installment->status === 'paid' ? 'table-success' : ($isNext ? 'table-warning' : '')) }}">
                                <td class="fw-bold">{{ $installment->installment_number }}</td>
                                <td>@price($installment->amount)</td>
                                <td class="{{ $installment->late_fee > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                                    @price($installment->late_fee ?? 0)
                                </td>
                                <td class="fw-bold">@price($installment->total_due)</td>
                                <td>
                                    {{ $installment->due_date->format('M d, Y') }}
                                    @if($isOverdue)
                                        <br><small class="text-danger fw-bold">{{ $installment->overdue_days }} {{ __('messages.days_overdue', ['default' => 'days overdue']) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $installment->status_color }} px-2 py-1">
                                        {{ $installment->status_label }}
                                    </span>
                                </td>
                                <td>
                                    {{ $installment->paid_date ? $installment->paid_date->format('M d, Y') : '—' }}
                                </td>
                                <td>
                                    @if($installment->status !== 'paid' && $isNext)
                                        <a href="{{ route('student-dashboard.installments.payment-options', ['locale' => app()->getLocale(), 'orderSpecifique' => $order->id]) }}"
                                           class="btn btn-success btn-xs py-1 px-2" style="font-size: 0.75rem;">
                                            <i class="bi bi-credit-card"></i> {{ __('messages.pay_now', ['default' => 'Pay Now']) }}
                                        </a>
                                    @elseif($installment->status === 'paid')
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                    @elseif($installment->status === 'awaiting_payment')
                                        <span class="badge bg-info">{{ __('messages.awaiting_payment', ['default' => 'Awaiting']) }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Content Access / Unlocked Levels -->
    @if(isset($unlockedStudies) && $unlockedStudies->isNotEmpty())
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-unlock"></i> {{ __('messages.unlocked_content', ['default' => 'Unlocked Content']) }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($unlockedStudies as $study)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card border h-100">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success bg-opacity-10 p-2 rounded me-3">
                                            <i class="bi bi-play-circle text-success fs-5"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $study->titre ?? $study->title ?? 'Level ' . $loop->iteration }}</h6>
                                            <small class="text-success"><i class="bi bi-unlock-fill"></i> {{ __('messages.unlocked', ['default' => 'Unlocked']) }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Exam & Certificate Status -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center py-4">
                    @if($order->canAccessFinalExam())
                        <i class="bi bi-patch-check-fill text-success fs-1"></i>
                        <h5 class="mt-2 mb-1">{{ __('messages.final_exam_available', ['default' => 'Final Exam Available']) }}</h5>
                        <p class="text-muted small">{{ __('messages.all_installments_paid_exam', ['default' => 'All installments are paid. You can now take the final exam.']) }}</p>
                    @else
                        <i class="bi bi-lock-fill text-danger fs-1"></i>
                        <h5 class="mt-2 mb-1">{{ __('messages.final_exam_locked', ['default' => 'Final Exam Locked']) }}</h5>
                        <p class="text-muted small">{{ __('messages.pay_all_installments_exam', ['default' => 'Pay all installments to unlock the final exam.']) }}</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center py-4">
                    @if($order->canReceiveCertificate())
                        <i class="bi bi-award-fill text-success fs-1"></i>
                        <h5 class="mt-2 mb-1">{{ __('messages.certificate_eligible', ['default' => 'Certificate Eligible']) }}</h5>
                        <p class="text-muted small">{{ __('messages.certificate_eligible_desc', ['default' => 'You are eligible to receive your certificate.']) }}</p>
                    @else
                        <i class="bi bi-award text-muted fs-1"></i>
                        <h5 class="mt-2 mb-1">{{ __('messages.certificate_not_eligible', ['default' => 'Certificate Not Yet Available']) }}</h5>
                        <p class="text-muted small">{{ __('messages.certificate_requirements', ['default' => 'Complete all payments and pass the final exam to receive your certificate.']) }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="text-center mb-4">
        <a href="{{ route('student-dashboard.installments.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> {{ __('messages.back_to_installments', ['default' => 'Back to Installments']) }}
        </a>
    </div>
</div>

@push('styles')
<style>
    .installment-table-wrap {
        overflow: visible;
    }

    .installment-schedule-table {
        width: 100%;
        table-layout: fixed;
    }

    .installment-schedule-table th,
    .installment-schedule-table td {
        white-space: normal;
        word-break: break-word;
        vertical-align: middle;
        padding: 0.65rem 0.5rem;
        font-size: 0.88rem;
    }
</style>
@endpush
@endsection
