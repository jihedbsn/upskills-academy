@extends('emails.base-template')

@section('title', 'Password Changed Successfully')

@section('header-type', 'success')

@section('email-title', 'Password Changed Successfully')

@section('content')
    <p class="greeting">Hello{{ $studentName ? ' ' . $studentName : '' }},</p>

    <p class="content-text">
        We are writing to confirm that your password has been successfully changed for your Upskills Academy account. This change was processed on {{ now()->format('F d, Y \a\t g:i A') }}.
    </p>

    <div class="info-card success">
        <h3 class="info-card-title">Password Change Confirmation</h3>
        <div style="text-align: center; padding: 20px;">
            <div style="display: inline-block; background: linear-gradient(135deg, #059669 0%, #10b981 100%); color: white; padding: 15px 25px; border-radius: 50px; font-size: 18px; font-weight: bold;">
                ✓ Password Changed Successfully
            </div>
        </div>
        <ul class="info-list">
            <li class="info-list-item"><strong>Account Email:</strong> {{ $studentEmail }}</li>
            <li class="info-list-item"><strong>Change Date:</strong> {{ now()->format('F d, Y') }}</li>
            <li class="info-list-item"><strong>Change Time:</strong> {{ now()->format('g:i A T') }}</li>
            <li class="info-list-item"><strong>Status:</strong> Password updated successfully</li>
        </ul>
    </div>

    <p class="content-text">
        Your account security has been enhanced with your new password. You can now use your new password to sign in to your Upskills Academy account.
    </p>

    <div class="info-card primary">
        <h3 class="info-card-title">What's Next?</h3>
        <ul class="info-list">
            <li class="info-list-item">You can now log in with your new password</li>
            <li class="info-list-item">Make sure to keep your password secure and confidential</li>
            <li class="info-list-item">Consider updating any saved passwords in your browser or password manager</li>
            <li class="info-list-item">Your account access remains the same with all your courses and progress intact</li>
        </ul>
    </div>

    <div class="button-container">
        <a href="{{ config('app.url') }}/login" class="button button-primary">Sign In to Your Account</a>
    </div>

    <div class="info-card warning">
        <h3 class="info-card-title">Security Notice</h3>
        <p class="content-text">
            <strong>Did you make this change?</strong> If you did not request this password change, please contact our support team immediately. This could indicate unauthorized access to your account.
        </p>
        <ul class="info-list">
            <li class="info-list-item">Contact our support if this wasn't you</li>
            <li class="info-list-item">Review your account activity regularly</li>
            <li class="info-list-item">Never share your password with anyone</li>
        </ul>
    </div>

    <hr class="divider">

    <p class="content-text">
        If you have any questions or concerns about this password change, please don't hesitate to contact our support team. We're here to help ensure your account remains secure.
    </p>
@endsection

@section('footer-additional')
    <p style="color: #6b7280; font-size: 12px; text-align: center; margin-top: 20px;">
        This is an automated security notification. This email was sent to confirm the password change for your account.
    </p>
@endsection
