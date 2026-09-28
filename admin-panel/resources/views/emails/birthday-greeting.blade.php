@extends('emails.base-template')

@section('title', 'Happy Birthday')

@section('header-type', 'primary')

@section('email-title', 'Happy Birthday!')

@section('content')
    <p class="greeting">Dear {{ $student_name }},</p>

    <div style="text-align: center; padding: 30px 0;">
        <img src="{{ $birthday_image_url }}" alt="Happy Birthday" style="max-width: 400px; width: 100%; height: auto; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" />
    </div>

    <p class="content-text" style="text-align: center; font-size: 18px;">
        <strong>The entire Upskills Academy team wishes you a wonderful birthday!</strong>
    </p>

    <p class="content-text">
        On this special day, we want to take a moment to thank you for being part of our community. Your dedication to learning and personal growth inspires us every day.
    </p>

    <div class="info-card success">
        <h3 class="info-card-title">Your Journey With Us</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Member Since:</strong> {{ $member_since }}</li>
            <li class="info-list-item"><strong>Courses Completed:</strong> {{ $courses_completed }}</li>
            <li class="info-list-item"><strong>Learning Hours:</strong> {{ $learning_hours }}</li>
        </ul>
    </div>

    @if(isset($special_offer))
    <div class="info-card warning">
        <h3 class="info-card-title">Special Birthday Gift</h3>
        <p class="content-text">
            {{ $special_offer }}
        </p>
    </div>
    @endif

    <div style="text-align: center; padding: 15px 0; font-size: 32px;">
        🎉 🎈 🎁 ✨
    </div>

    <p class="content-text" style="text-align: center;">
        May this year bring you success, happiness, and continued growth!
    </p>
@endsection
