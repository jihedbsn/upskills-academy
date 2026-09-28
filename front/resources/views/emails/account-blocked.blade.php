@extends('emails.base-template')

@section('title', 'Account Access Notification')

@section('header-type', 'error')

@section('email-title', 'Account Access Notification')

@section('content')
    <p class="greeting">Hello {{ $studentName }},</p>

    <p class="content-text">
        We are writing to inform you that your Upskills Academy account has been temporarily restricted. This action has been taken in accordance with our platform policies and terms of service.
    </p>

    <div class="info-card error">
        <h3 class="info-card-title">⚠️ Account Status: Blocked</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Account Email:</strong> {{ $student->email }}</li>
            <li class="info-list-item"><strong>Action Date:</strong> {{ $blockedAt ? $blockedAt->format('F d, Y \a\t H:i') : now()->format('F d, Y \a\t H:i') }}</li>
            @if($blockReason)
            <li class="info-list-item"><strong>Reason:</strong> {{ $blockReason }}</li>
            @endif
        </ul>
    </div>

    @if($blockReason)
    <div class="info-card warning">
        <h3 class="info-card-title">Details</h3>
        <p class="content-text" style="margin: 0;">
            {{ $blockReason }}
        </p>
    </div>
    @endif

    <p class="content-text">
        <strong>What This Means:</strong>
    </p>
    <ul class="info-list">
        <li class="info-list-item">You will not be able to log in to your account</li>
        <li class="info-list-item">Access to courses and learning materials is temporarily suspended</li>
        <li class="info-list-item">Your account data remains secure and preserved</li>
    </ul>

    <p class="content-text">
        <strong>Next Steps:</strong>
    </p>
    <p class="content-text">
        If you believe this action was taken in error or would like to discuss the status of your account, please contact our support team. Our team is available to review your case and provide assistance.
    </p>

    <div class="info-card info">
        <h3 class="info-card-title">📞 Contact Support</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Email:</strong> support@up-skills.academy</li>
            <li class="info-list-item"><strong>Phone:</strong> +46 (0) 123 456 789</li>
            <li class="info-list-item"><strong>Business Hours:</strong> Monday - Friday, 9:00 AM - 5:00 PM CET</li>
        </ul>
    </div>

    <p class="content-text">
        We appreciate your understanding and cooperation in this matter.
    </p>

    <p class="signature">
        Best regards,<br>
        <strong>Upskills Academy Support Team</strong><br>
        Upskills Academy
    </p>
@endsection

@section('footer-note')
    <p class="footer-note">
        This is an automated notification regarding your account status. Please do not reply directly to this email.
        For inquiries, please use the contact information provided above.
    </p>
@endsection
