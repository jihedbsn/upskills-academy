@extends('emails.base-template')

@section('title', 'Course Access Expired')

@section('header-type', 'primary')

@section('email-title', 'Course Access Has Expired')

@section('content')
    <p class="greeting">Hello {{ $studentName }},</p>
    
    <p class="content-text">
        Your access to the course listed below has expired. If you would like to continue your studies, you may purchase an extension to regain access to all course materials.
    </p>
    
    <div class="info-card error">
        <h3 class="info-card-title">Course Information</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Course:</strong> {{ $courseName }}</li>
            <li class="info-list-item"><strong>Enrollment Date:</strong> {{ $enrollmentDate }}</li>
            <li class="info-list-item"><strong>Expired On:</strong> {{ $expirationDate }}</li>
            @if($progress)
            <li class="info-list-item"><strong>Progress Achieved:</strong> {{ $progress }}%</li>
            @endif
        </ul>
    </div>
    
    @if($extensionAvailable)
    <div class="info-card success">
        <h3 class="info-card-title">Extension Available</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Extension Period:</strong> {{ $extensionPeriod }}</li>
            <li class="info-list-item"><strong>Price:</strong> ${{ number_format($extensionPrice, 2) }}</li>
        </ul>
    </div>
    
    <div class="button-container">
        <a href="{{ $extensionUrl }}" class="button button-primary">Purchase Extension</a>
    </div>
    @endif
    
    <hr class="divider">
    
    <p class="content-text text-center">
        For questions about course access or extensions, please contact our support team.
    </p>
@endsection
