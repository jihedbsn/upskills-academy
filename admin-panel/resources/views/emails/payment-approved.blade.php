@extends('emails.base-template')

@section('title', 'Payment Approved')

@section('header-type', 'success')

@section('email-title', 'Payment Approved')

@section('content')
    <p class="greeting">Dear {{ $studentName }},</p>

    <div style="text-align: center; padding: 20px 0;">
        <div style="font-size: 60px;">✓</div>
    </div>

    <p class="content-text">
        Great news! Your payment has been approved and processed successfully.
    </p>

    <div class="info-card success">
        <h3 class="info-card-title">Order Details</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Order ID:</strong> #{{ $orderId }}</li>
            <li class="info-list-item"><strong>Order Date:</strong> {{ $orderDate }}</li>
            <li class="info-list-item"><strong>Payment Method:</strong> {{ $paymentMethod }}</li>
            <li class="info-list-item"><strong>Amount:</strong> ${{ $amount }}</li>
            <li class="info-list-item"><strong>Status:</strong> <span style="color: #10b981; font-weight: bold;">✓ Approved</span></li>
        </ul>
    </div>

    <div class="info-card primary">
        <h3 class="info-card-title">🎓 Course Access</h3>
        <p class="content-text">
            <strong>You now have full access to your enrolled courses!</strong>
        </p>
        <p class="content-text">
            You can start learning right away by visiting your dashboard.
        </p>
    </div>

    <div class="button-container">
        <a href="{{ \App\Support\StudentFrontendUrl::localized('en', 'student-dashboard/courses') }}" class="button button-primary">Go to My Courses</a>
    </div>

    <hr class="divider">

    <p class="content-text">
        If you have any questions or need assistance, please don't hesitate to contact our support team.
    </p>

    <p class="content-text">
        Happy learning!
    </p>
@endsection

