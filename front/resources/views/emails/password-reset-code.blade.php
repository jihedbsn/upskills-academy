@extends('emails.base-template')

@section('title', 'Password Reset Code')

@section('header-type', 'primary')

@section('email-title', 'Password Reset Request')

@section('content')
    <p class="greeting">Hello{{ $studentName ? ' ' . $studentName : '' }},</p>

    <p class="content-text">
        We have received a request to reset the password for your Upskills Academy account. Please use the verification code below to proceed with resetting your password.
    </p>

    <div class="info-card primary">
        <h3 class="info-card-title" style="text-align: center;">Your Verification Code</h3>
        <div style="background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); color: white; text-align: center; font-size: 42px; font-weight: bold; padding: 30px 20px; border-radius: 8px; letter-spacing: 8px; margin: 20px 0; font-family: 'Courier New', monospace;">
            {{ $code }}
        </div>
    </div>

    @if (!empty($verifyUrl))
        <div class="button-container">
            <a href="{{ $verifyUrl }}" class="button button-primary">Enter Code &amp; Reset Password</a>
        </div>

        <p class="content-text" style="text-align: center; font-size: 14px;">
            If the button does not open, copy and paste this link into your browser:<br>
            <a href="{{ $verifyUrl }}" style="color: #093162; word-break: break-all;">{{ $verifyUrl }}</a>
        </p>
    @endif

    <div class="info-card success">
        <h3 class="info-card-title">How to use this code</h3>
        <ul class="info-list">
            <li class="info-list-item">1) Click the button above to open the verification page</li>
            <li class="info-list-item">2) Your email{{ $studentEmail ? ' (' . $studentEmail . ')' : '' }} will be pre-filled—enter the code shown above</li>
            <li class="info-list-item">3) Create your new password and confirm it</li>
        </ul>
    </div>

    <div class="info-card warning">
        <h3 class="info-card-title">Important Security Information</h3>
        <ul class="info-list">
            <li class="info-list-item">This code expires in 15 minutes</li>
            <li class="info-list-item">This code can only be used once</li>
            <li class="info-list-item">If you did not request this reset, please ignore this email</li>
            <li class="info-list-item">Never share this code with anyone, including Upskills Academy staff</li>
        </ul>
    </div>

    <hr class="divider">
@endsection
