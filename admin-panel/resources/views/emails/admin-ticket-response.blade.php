@extends('emails.base-template')

@section('title', 'Support Ticket Response')

@section('header-type', 'primary')

@section('email-title', 'New Response to Your Support Ticket')

@section('content')
    <p class="greeting">Hello {{ $studentName }},</p>
    
    <p class="content-text">
        Our support team has responded to your ticket. Please find the details below.
    </p>
    
    <div class="info-card primary">
        <h3 class="info-card-title">Ticket Information</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Ticket ID:</strong> #TKT-{{ str_pad($ticketId, 3, '0', STR_PAD_LEFT) }}</li>
            <li class="info-list-item"><strong>Subject:</strong> {{ $subject }}</li>
            <li class="info-list-item"><strong>Status:</strong> {{ $status }}</li>
            <li class="info-list-item"><strong>Created:</strong> {{ $createdAt }}</li>
        </ul>
    </div>
    
    <div class="info-card primary">
        <h3 class="info-card-title">Admin Response</h3>
        <p class="content-text" style="color: #374151; white-space: pre-wrap;">{{ $adminMessage }}</p>
    </div>
    
    <p class="content-text">
        You can view the full conversation and reply to this ticket by clicking the button below.
    </p>
    
    <div class="button-container">
        <a href="{{ $ticketUrl }}" class="button button-primary">View Ticket</a>
    </div>
    
    <p class="content-text" style="margin-top: 30px; font-size: 14px; color: #6b7280;">
        If you have any questions or need further assistance, please don't hesitate to reply to this ticket.
    </p>
@endsection
