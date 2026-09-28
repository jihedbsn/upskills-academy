@extends('emails.base-template')

@php
    $isApproved = ($status ?? 'approved') === 'approved';
    $titleText = $isApproved ? 'Course Extension Approved' : 'Course Extension Disapproved';
    $headerType = $isApproved ? 'success' : 'danger';
@endphp

@section('title', $titleText)

@section('header-type', $headerType)

@section('email-title', $titleText)

@section('content')
    <p class="greeting">Dear {{ $student->prenom ?? '' }} {{ $student->nom ?? '' }},</p>

    <div style="text-align: center; padding: 20px 0;">
        @if($isApproved)
            <div style="font-size: 60px; color: #16a34a;">✓</div>
        @else
            <div style="font-size: 60px; color: #dc2626;">✕</div>
        @endif
    </div>

    <p class="content-text">
        @if($isApproved)
            Great news! Your course extension request has been approved. Your access has been renewed.
        @else
            Your course extension request has been disapproved by our team.
        @endif
    </p>

    <div class="info-card {{ $isApproved ? 'success' : 'warning' }}">
        <h3 class="info-card-title">Extension Details</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Course:</strong> {{ $product->titre ?? 'N/A' }}</li>
            <li class="info-list-item"><strong>Extension Period:</strong> {{ $extensionOrder->extension_months }} months</li>
            <li class="info-list-item"><strong>Amount:</strong> ${{ number_format($extensionOrder->price, 2) }}</li>
            @if($extensionOrder->old_expiration_date)
            <li class="info-list-item"><strong>Previous Expiry:</strong> {{ $extensionOrder->old_expiration_date->format('M d, Y') }}</li>
            @endif
            @if($extensionOrder->new_expiration_date)
            <li class="info-list-item"><strong>New Expiry:</strong>
                <span style="color: #16a34a; font-weight: bold;">{{ $extensionOrder->new_expiration_date->format('M d, Y') }}</span>
            </li>
            @endif
            <li class="info-list-item"><strong>Status:</strong>
                @if($isApproved)
                    <span style="color: #16a34a; font-weight: bold;">✓ Approved</span>
                @else
                    <span style="color: #dc2626; font-weight: bold;">✕ Disapproved</span>
                @endif
            </li>
            @if(!$isApproved && !empty($reason))
            <li class="info-list-item"><strong>Reason:</strong> {{ $reason }}</li>
            @endif
        </ul>
    </div>

    <p class="content-text">
        @if($isApproved)
            You can now continue your learning. Log in to your student dashboard to access your course.
        @else
            If you need more details, please contact support or submit a new request with valid payment proof.
        @endif
    </p>

    <div style="text-align: center; padding: 20px 0;">
        <a href="{{ \App\Support\StudentFrontendUrl::localized('en', 'student-dashboard') }}" style="background-color: #2563eb; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-weight: bold;">
            Go to Dashboard
        </a>
    </div>
@endsection
