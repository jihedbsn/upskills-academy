@extends('emails.base-template')

@section('title', 'New Support Ticket')

@section('header-type', 'success')

@section('email-title', 'New Support Ticket Received')

@section('content')
    <p class="greeting">Hello Administrator,</p>
    
    <p class="content-text">
        A new support ticket has been created and requires your attention. Ticket details are provided below.
    </p>
    
    <div class="info-card success">
        <h3 class="info-card-title">Ticket Information</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Ticket ID:</strong> {{ $ticketId }}</li>
            <li class="info-list-item"><strong>Subject:</strong> {{ $subject }}</li>
            <li class="info-list-item"><strong>Priority:</strong> {{ $priority }}</li>
            <li class="info-list-item"><strong>Status:</strong> {{ $status }}</li>
            <li class="info-list-item"><strong>Created:</strong> {{ $createdAt }}</li>
        </ul>
    </div>
    
    <div class="info-card success">
        <h3 class="info-card-title">Student Information</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Name:</strong> {{ $studentName }}</li>
            <li class="info-list-item"><strong>Email:</strong> {{ $studentEmail }}</li>
            @if($studentPhone)
            <li class="info-list-item"><strong>Phone:</strong> {{ $studentPhone }}</li>
            @endif
        </ul>
    </div>
    
    <div class="info-card" style="background-color: #f9fafb; border-left: 4px solid #6b7280;">
        <h3 class="info-card-title">Initial Message</h3>
        <p class="content-text" style="color: #374151;">
            {{ $message }}
        </p>
    </div>
    
    <div class="button-container">
        <a href="{{ $ticketUrl }}" class="button button-success">View Ticket</a>
    </div>
@endsection
