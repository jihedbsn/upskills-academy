@extends('emails.base-template')

@section('title', 'Certificate Ready')

@section('header-type', 'primary')

@section('email-title', 'Your Certificate is Ready')

@section('content')
    <p class="greeting">Hello {{ $student_name }},</p>

    <p class="content-text">
        Congratulations on successfully completing your course! Your certificate of completion is now available for download.
    </p>

    <div class="info-card success">
        <h3 class="info-card-title">Course Completion Details</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Course:</strong> {{ $course_name }}</li>
            <li class="info-list-item"><strong>Completion Date:</strong> {{ $completion_date }}</li>
            <li class="info-list-item"><strong>Certificate Number:</strong> {{ $certificate_number }}</li>
            @if(isset($final_score))
            <li class="info-list-item"><strong>Final Score:</strong> {{ $final_score }}%</li>
            @endif
        </ul>
    </div>

    <div class="info-card primary">
        <h3 class="info-card-title">Your Certificate</h3>
        <p class="content-text">
            Your professionally designed certificate is ready to download. You can add it to your professional profiles and share your achievement with your network.
        </p>
    </div>

    <div class="button-container">
        <a href="{{ $certificate_url }}" class="button button-success">Download Certificate</a>
    </div>

    <hr class="divider">

    <p class="content-text">
        <strong>Continue Your Learning Journey</strong>
    </p>
    <p class="content-text">
        Explore our catalog for more advanced courses to further enhance your skills and knowledge.
    </p>

    <div class="button-container">
        <a href="{{ $catalog_url ?? '#' }}" class="button button-outline">Browse Courses</a>
    </div>
@endsection
