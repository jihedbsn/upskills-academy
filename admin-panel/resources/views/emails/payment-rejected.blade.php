@extends('emails.base-template')

@section('title', 'Payment Rejected')

@section('header-type', 'error')

@section('email-title', 'Payment Rejected')

@section('content')
    <p class="greeting">Dear {{ $studentName }},</p>

    <div style="text-align: center; padding: 20px 0;">
        <div style="font-size: 60px; color: #dc2626;">✕</div>
    </div>

    <p class="content-text">
        We regret to inform you that your payment has been rejected and could not be processed.
    </p>

    <div class="info-card error">
        <h3 class="info-card-title">Order Details</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Order ID:</strong> #{{ $orderId }}</li>
            <li class="info-list-item"><strong>Order Date:</strong> {{ $orderDate }}</li>
            <li class="info-list-item"><strong>Payment Method:</strong> {{ $paymentMethod }}</li>
            <li class="info-list-item"><strong>Amount:</strong> ${{ $amount }}</li>
            <li class="info-list-item"><strong>Status:</strong> <span style="color: #dc2626; font-weight: bold;">✕ Rejected</span></li>
        </ul>
    </div>

    <div class="info-card warning">
        <h3 class="info-card-title">⚠️ Rejection Reason</h3>
        @if($rejectionComment)
            <p class="content-text">{{ $rejectionComment }}</p>
        @else
            <p class="content-text">Common reasons for payment rejection include:</p>
            <ul class="info-list">
                <li class="info-list-item">Incorrect payment information</li>
                <li class="info-list-item">Insufficient verification documents</li>
                <li class="info-list-item">Payment receipt not matching the order details</li>
                <li class="info-list-item">Invalid or incomplete bank transfer information</li>
            </ul>
        @endif
    </div>

    <div class="info-card primary">
        <h3 class="info-card-title">📧 What should I do next?</h3>
        <p class="content-text">
            Please contact our support team for more information about why your payment was rejected and how to proceed. Our team will be happy to assist you in resolving this issue.
        </p>
        <p class="content-text">
            You can also reply to this email and our support team will get back to you as soon as possible.
        </p>
    </div>

    <div class="button-container">
        <a href="{{ env('USER_URL') }}/support" class="button button-primary">Contact Support</a>
    </div>

    <hr class="divider">

    <p class="content-text">
        We apologize for any inconvenience this may have caused.
    </p>
@endsection

