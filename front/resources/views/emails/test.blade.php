@extends('emails.base-template')

@section('title', 'Test Email')

@section('email-title', 'Test Email')

@section('content')
    <p class="greeting">Hello {{ $name ?? 'User' }},</p>

    <div class="content-text">
        {!! nl2br(e($content ?? 'This is a test email.')) !!}
    </div>

    <div class="info-card primary">
        <h3 class="info-card-title">Test Information</h3>
        <p class="content-text">
            This email was sent to verify the email system functionality.
        </p>
    </div>
@endsection
