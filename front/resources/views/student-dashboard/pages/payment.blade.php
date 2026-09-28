@extends('student-dashboard.layouts.app')

@section('title', __('student_dashboard.payment'))

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('student-dashboard.index') }}">{{ __('student_dashboard.dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('student_dashboard.payment') }}</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">{{ __('student_dashboard.payment') }}</h1>
            <p class="text-muted">{{ __('student_dashboard.payment_management') }}</p>
        </div>
        <div>

        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-credit-card"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-1">${{ number_format($paymentStats['total_spent'], 2) }}</h3>
                            <p class="text-muted mb-0">{{ __('student_dashboard.total_spent') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success bg-opacity-10 text-success">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-1">{{ $paymentStats['successful_orders'] }}</h3>
                            <p class="text-muted mb-0">{{ __('student_dashboard.successful_payments') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-clock"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-1">{{ $paymentStats['total_orders'] - $paymentStats['successful_orders'] }}</h3>
                            <p class="text-muted mb-0">{{ __('student_dashboard.pending_payments') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-info bg-opacity-10 text-info">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-1">${{ number_format($paymentStats['this_month'], 2) }}</h3>
                            <p class="text-muted mb-0">{{ __('student_dashboard.this_month') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Savings Card (conditional) -->
    @if(isset($paymentStats['total_savings']) && $paymentStats['total_savings'] > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-success">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                            <i class="bi bi-piggy-bank"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h3 class="mb-1 text-success">${{ number_format($paymentStats['total_savings'], 2) }}</h3>
                            <p class="text-muted mb-0">{{ __('student_dashboard.total_savings') }} - {{ __('Total saved with coupons') }}</p>
                        </div>
                        <div class="text-end">
                            <small class="text-muted">{{ __('Congratulations! You\'ve saved money with coupons.') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <!-- Payment Methods -->


        <!-- Recent Transactions -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('student_dashboard.recent_transactions') }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('student_dashboard.date') }}</th>
                                    <th>{{ __('student_dashboard.description') }}</th>
                                    <th>{{ __('student_dashboard.amount') }}</th>
                                    <th>{{ __('student_dashboard.status') }}</th>
                                    <th>{{ __('student_dashboard.payment_method') }}</th>
                                    <th>{{ __('student_dashboard.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                    <tr>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-medium">{{ $transaction->created_at->format('d M Y') }}</span>
                                                <small class="text-muted">{{ $transaction->created_at->format('H:i') }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($transaction->isForCourse() && $transaction->product)
                                                    <img src="{{ $transaction->product->image ? config('app.file_url') . 'uploads/products/' . $transaction->product->image : asset('assets/img/placeholders/course-not-found.png') }}"
                                                         alt="{{ $transaction->product->titre }}"
                                                         class="rounded me-2"
                                                         style="width: 40px; height: 40px; object-fit: cover;">
                                                    <div>
                                                        <div class="fw-medium">{{ $transaction->product->titre }}</div>
                                                        <small class="text-muted">{{ __('student_dashboard.course') }}</small>
                                                    </div>
                                                @elseif($transaction->isForBook() && $transaction->book)
                                                    <img src="{{ $transaction->book->image ? config('app.file_url') . 'uploads/books/' . $transaction->book->image : asset('assets/img/placeholders/book-not-found.png') }}"
                                                         alt="{{ $transaction->book->titre }}"
                                                         class="rounded me-2"
                                                         style="width: 40px; height: 40px; object-fit: cover;">
                                                    <div>
                                                        <div class="fw-medium">{{ $transaction->book->titre }}</div>
                                                        <small class="text-muted">{{ __('student_dashboard.book') }}</small>
                                                    </div>
                                                @else
                                                    <div>
                                                        <div class="fw-medium">{{ $transaction->getItemName() }}</div>
                                                        <small class="text-muted">{{ ucfirst($transaction->getItemType()) }}</small>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-{{ $transaction->payment_success ? 'success' : 'warning' }} fw-medium">
                                            @if($transaction->coupon_used)
                                                <div class="d-flex flex-column">
                                                    <small class="text-muted text-decoration-line-through">@price($transaction->original_price)</small>
                                                    <span class="text-success fw-bold">@price($transaction->final_price)</span>
                                                    <small class="text-primary">
                                                        <i class="bi bi-tag me-1"></i>{{ $transaction->coupon_used }}
                                                        (-@price($transaction->discount_amount))
                                                    </small>
                                                </div>
                                            @else
                                                @price($transaction->price)
                                            @endif
                                        </td>
                                        <td>
                                            @if($transaction->payment_success)
                                                <span class="badge bg-success">{{ __('student_dashboard.completed') }}</span>
                                            @else
                                                <span class="badge bg-warning">{{ __('student_dashboard.pending') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-muted">
                                                {{ $transaction->payment_method ? ucfirst(str_replace('_', ' ', $transaction->payment_method)) : 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#transactionModal"
                                                        onclick="showTransactionDetails({{ $transaction->id }})">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                @if($transaction->payment_success)
                                                    <button type="button" class="btn btn-sm btn-outline-success">
                                                        <i class="bi bi-download"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="bi bi-credit-card display-4 text-muted"></i>
                                            <p class="mt-2 text-muted">{{ __('student_dashboard.no_transactions') }}</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Payment Method Modal -->
<div class="modal fade" id="addPaymentMethodModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('student_dashboard.add_payment_method') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label">{{ __('student_dashboard.payment_method') }}</label>
                        <select class="form-select">
                            <option value="">{{ __('student_dashboard.select_method') }}</option>
                            <option value="credit_card">{{ __('student_dashboard.credit_card') }}</option>
                            <option value="paypal">{{ __('student_dashboard.paypal') }}</option>
                            <option value="bank_transfer">{{ __('student_dashboard.bank_transfer') }}</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('student_dashboard.card_number') }}</label>
                        <input type="text" class="form-control" placeholder="1234 5678 9012 3456">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('student_dashboard.expiry_date') }}</label>
                            <input type="text" class="form-control" placeholder="MM/YY">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('student_dashboard.cvv') }}</label>
                            <input type="text" class="form-control" placeholder="123">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('student_dashboard.cardholder_name') }}</label>
                        <input type="text" class="form-control" placeholder="{{ __('student_dashboard.enter_name') }}">
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="defaultPayment">
                        <label class="form-check-label" for="defaultPayment">
                            {{ __('student_dashboard.set_as_default') }}
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('student_dashboard.cancel') }}</button>
                <button type="button" class="btn btn-primary">{{ __('student_dashboard.add_method') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Transaction Details Modal -->
<div class="modal fade" id="transactionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('student_dashboard.transaction_details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="transactionDetailsBody">
                <!-- Content will be loaded dynamically -->
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('student_dashboard.close') }}</button>
                <button class="btn btn-primary" id="downloadReceiptBtn" style="display: none;">
                    <i class="bi bi-download me-2"></i>Download Receipt
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to show transaction details
    window.showTransactionDetails = function(transactionId) {
        const modalBody = document.getElementById('transactionDetailsBody');
        const downloadBtn = document.getElementById('downloadReceiptBtn');

        // Show loading spinner
        modalBody.innerHTML = `
            <div class="text-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `;

        // Fetch transaction details
        fetch(`/student-dashboard/payment/transaction/${transactionId}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const transaction = data.transaction;
                modalBody.innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>{{ __('student_dashboard.transaction_info') }}</h6>
                            <ul class="list-unstyled">
                                <li><strong>{{ __('student_dashboard.transaction_id') }}:</strong> ${transaction.id || 'N/A'}</li>
                                <li><strong>{{ __('student_dashboard.date') }}:</strong> ${new Date(transaction.created_at).toLocaleDateString()}</li>
                                <li><strong>{{ __('student_dashboard.amount') }}:</strong> $${parseFloat(transaction.price).toFixed(2)}</li>
                                <li><strong>{{ __('student_dashboard.status') }}:</strong>
                                    <span class="badge ${transaction.payment_success ? 'bg-success' : 'bg-warning'}">
                                        ${transaction.payment_success ? '{{ __("student_dashboard.completed") }}' : '{{ __("student_dashboard.pending") }}'}
                                    </span>
                                </li>
                                <li><strong>{{ __('student_dashboard.payment_method') }}:</strong> ${transaction.payment_method ? transaction.payment_method.replace('_', ' ') : 'N/A'}</li>
                                ${transaction.transaction_id ? `<li><strong>{{ __('student_dashboard.transaction_reference') }}:</strong> ${transaction.transaction_id}</li>` : ''}
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>{{ __('student_dashboard.item_details') }}</h6>
                            <ul class="list-unstyled">
                                <li><strong>{{ __('student_dashboard.item_name') }}:</strong> ${transaction.item_name || 'N/A'}</li>
                                <li><strong>{{ __('student_dashboard.item_type') }}:</strong> ${transaction.item_type || 'N/A'}</li>
                                ${transaction.item_description ? `<li><strong>{{ __('student_dashboard.description') }}:</strong> ${transaction.item_description}</li>` : ''}
                                ${transaction.item_instructor ? `<li><strong>{{ __('student_dashboard.instructor') }}:</strong> ${transaction.item_instructor}</li>` : ''}
                                ${transaction.item_duration ? `<li><strong>{{ __('student_dashboard.duration') }}:</strong> ${transaction.item_duration}</li>` : ''}
                            </ul>
                        </div>
                    </div>
                `;

                // Show download button if payment was successful
                if (transaction.payment_success) {
                    downloadBtn.style.display = 'inline-block';
                    downloadBtn.onclick = () => downloadReceipt(transaction.id);
                } else {
                    downloadBtn.style.display = 'none';
                }
            } else {
                modalBody.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        {{ __('student_dashboard.error_loading_transaction') }}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading transaction details:', error);
            modalBody.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    {{ __('student_dashboard.error_loading_transaction') }}
                </div>
            `;
        });
    };

    // Function to download receipt
    window.downloadReceipt = function(transactionId) {
        // Create a link to download the receipt
        const link = document.createElement('a');
        link.href = `/student-dashboard/payment/receipt/${transactionId}`;
        link.download = `receipt-${transactionId}.pdf`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    };
});
</script>
