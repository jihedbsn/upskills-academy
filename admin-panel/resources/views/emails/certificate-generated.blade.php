@extends('emails.base-template')

@section('title', 'Your Certificate is Ready - ' . $courseName)

@section('header-type', 'success')

@section('email-title', 'Your Certificate is Ready! - ' . $courseName)

@section('content')
    <p class="greeting">Dear {{ $studentName }},</p>

    <div style="text-align: center; padding: 20px 0;">
        <div style="font-size: 60px;">�</div>
    </div>

    <p class="content-text">
        We are pleased to inform you that your certificate for <strong>{{ $courseName }}</strong> has been generated and is now available!
    </p>

    <div class="info-card success">
        <h3 class="info-card-title">Certificate Details</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Course:</strong> {{ $courseName }}</li>
            <li class="info-list-item"><strong>Serial Number:</strong> {{ $serialNumber }}</li>
            <li class="info-list-item"><strong>Issue Date:</strong> {{ $certificate->certificate_date ? $certificate->certificate_date->format('F d, Y') : $certificate->created_at->format('F d, Y') }}</li>
        </ul>
    </div>

    <div class="info-card primary">
        <h3 class="info-card-title">📄 Certificate Access</h3>
        <p class="content-text">
            Your certificate is attached to this email and is also available in your student dashboard.
        </p>
    </div>

    <hr class="divider">

    <div class="info-card info">
        <h3 class="info-card-title">What's Next?</h3>
        <ul class="info-list">
            <li class="info-list-item">Download your certificate from your dashboard</li>
            <li class="info-list-item">Share your achievement on social media</li>
            <li class="info-list-item">Add it to your professional portfolio</li>
            <li class="info-list-item">Continue learning with our other courses</li>
        </ul>
    </div>

    <p class="content-text" style="text-align: center; font-style: italic; color: #059669; margin-top: 20px;">
        Congratulations on your achievement! We're proud of your dedication and hard work.
    </p>

    <div class="button-container" style="text-align: center; margin: 40px 0;">
        <a href="{{ config('app.user_url') }}/student-dashboard/certificates" class="button button-success" style="display: inline-block; padding: 16px 36px; font-size: 16px; font-weight: 600; text-decoration: none; border-radius: 8px; background-color: #059669; color: #ffffff; box-shadow: 0 4px 6px rgba(0, 50, 100, 0.1);">View My Certificates</a>
    </div>
@endsection
