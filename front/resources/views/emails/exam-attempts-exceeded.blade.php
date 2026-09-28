@extends('emails.base-template')

@section('title', 'Exam Attempts Limit Reached')

@section('header-type', 'warning')

@section('email-title', 'Exam Attempts Limit Reached')

@section('content')
    <p class="greeting">Dear {{ $student_name }},</p>

    <div class="info-card warning" style="border-left: 4px solid #ff9800; background-color: #fff3e0; padding: 20px; margin: 20px 0; border-radius: 8px;">
        <h3 style="color: #e65100; margin-top: 0;">
            <span style="font-size: 24px;">⚠️</span> Exam Attempt Limit Reached
        </h3>
        <p class="content-text">
            You have reached the maximum number of attempts for the following exam:
        </p>
    </div>

    <div class="info-card" style="background-color: #f5f5f5; padding: 20px; margin: 20px 0; border-radius: 8px;">
        <h4 style="margin-top: 0; color: #333;">Exam Details</h4>
        <ul class="info-list" style="list-style: none; padding: 0;">
            <li class="info-list-item" style="padding: 8px 0; border-bottom: 1px solid #ddd;">
                <strong>Course:</strong> {{ $product_name }}
            </li>
            <li class="info-list-item" style="padding: 8px 0; border-bottom: 1px solid #ddd;">
                <strong>Exam:</strong> {{ $quiz_name }}
            </li>
            <li class="info-list-item" style="padding: 8px 0;">
                <strong>Attempts Made:</strong> {{ $attempts_made }}
            </li>
        </ul>
    </div>

    <div class="info-card" style="background-color: #e3f2fd; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #2196f3;">
        <h3 style="color: #1565c0; margin-top: 0;">
            <span style="font-size: 20px;">🔄</span> Exam Renewal Available
        </h3>
        <p class="content-text">
            To continue taking this exam, you can renew your exam access for:
        </p>
        <div style="text-align: center; margin: 20px 0;">
            <span style="font-size: 36px; font-weight: bold; color: #1565c0;">${{ $renewal_price }}</span>
        </div>
        <p class="content-text">
            This renewal will give you additional attempts to pass the exam successfully.
        </p>
    </div>

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ config('app.user_url') }}/courses/{{ $product_name }}"
           style="display: inline-block; background-color: #2196f3; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;">
            View Course & Renew Access
        </a>
    </div>

    <div class="info-card" style="background-color: #f9f9f9; padding: 15px; margin: 20px 0; border-radius: 8px;">
        <h4 style="margin-top: 0; color: #555;">💡 Need Help?</h4>
        <p class="content-text">
            If you have any questions about the renewal process or need assistance, please don't hesitate to contact our support team.
        </p>
    </div>

    <p class="content-text" style="margin-top: 30px;">
        We believe in your ability to succeed and look forward to supporting you on your learning journey!
    </p>

    <p class="content-text" style="margin-top: 20px;">
        Best regards,<br>
        <strong>The Upskills Academy Team</strong>
    </p>
@endsection
