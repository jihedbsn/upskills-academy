@extends('emails.base-template')

@section('title', 'Course Extension Approved')

@section('header-type', 'success')

@section('email-title', 'Course Extension Approved')

@section('content')
    <p class="greeting">Dear {{ $student->prenom ?? '' }} {{ $student->nom ?? '' }},</p>

    <div style="text-align: center; padding: 20px 0;">
        <div style="font-size: 60px; color: #16a34a;">✓</div>
    </div>

    <p class="content-text">
        Great news! Your course extension request has been approved. Your access has been renewed.
    </p>

    <div class="info-card success">
        <h3 class="info-card-title">Extension Details</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Course:</strong> {{ $product->titre ?? 'N/A' }}</li>
            <li class="info-list-item"><strong>Extension Period:</strong> {{ $extensionOrder->extension_months }} months</li>
            <li class="info-list-item"><strong>Amount Paid:</strong> ${{ number_format($extensionOrder->price, 2) }}</li>
            @if($extensionOrder->old_expiration_date)
            <li class="info-list-item"><strong>Previous Expiry:</strong> {{ $extensionOrder->old_expiration_date->format('M d, Y') }}</li>
            @endif
            @if($extensionOrder->new_expiration_date)
            <li class="info-list-item"><strong>New Expiry:</strong> <span style="color: #16a34a; font-weight: bold;">{{ $extensionOrder->new_expiration_date->format('M d, Y') }}</span></li>
            @endif
            <li class="info-list-item"><strong>Status:</strong> <span style="color: #16a34a; font-weight: bold;">✓ Approved</span></li>
        </ul>
    </div>

    <p class="content-text">
        You can now continue your learning. Log in to your student dashboard to access your course.
    </p>

    <div style="text-align: center; padding: 20px 0;">
        <a href="{{ \App\Support\StudentFrontendUrl::localized('en', 'student-dashboard') }}" style="background-color: #16a34a; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-weight: bold;">
            Go to Dashboard
        </a>
    </div>
@endsection
