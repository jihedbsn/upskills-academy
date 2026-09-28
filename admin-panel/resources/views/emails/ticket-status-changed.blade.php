@extends('emails.base-template')

@section('title', 'Support Ticket Status Updated')

@section('header-type', 'primary')

@section('email-title', 'Ticket Status Updated')

@section('content')
    <p class="greeting">Hello {{ $studentName }},</p>

    <p class="content-text">
        We wanted to inform you that the status of your support ticket has been updated.
    </p>

    <div class="info-card {{ $isResolved ? 'success' : 'primary' }}">
        <h3 class="info-card-title">Ticket Information</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Ticket ID:</strong> #{{ $ticketId }}</li>
            <li class="info-list-item"><strong>Subject:</strong> {{ $subject }}</li>
            <li class="info-list-item"><strong>Submitted On:</strong> {{ $createdAt }}</li>
            <li class="info-list-item"><strong>Updated:</strong> {{ $updatedAt }}</li>
            <li class="info-list-item">
                <strong>New Status:</strong> 
                <span style="color: {{ $isResolved ? '#059669' : '#d97706' }}; font-weight: bold; text-transform: uppercase;">
                    {{ $isResolved ? '✓ Resolved' : '↻ Reopened' }}
                </span>
            </li>
        </ul>
    </div>

    @if($isResolved)
    <div class="info-card success">
        <h3 class="info-card-title">Ticket Resolved</h3>
        <p class="content-text">
            Great news! Your support ticket has been marked as resolved. We hope your issue has been addressed satisfactorily.
        </p>
        <p class="content-text">
            If you feel the issue is not fully resolved or if you have any follow-up questions, you can reopen this ticket or create a new one.
        </p>
    </div>
    @else
    <div class="info-card warning">
        <h3 class="info-card-title">Ticket Reopened</h3>
        <p class="content-text">
            Your support ticket has been reopened. Our team will continue to work on resolving your issue.
        </p>
        <p class="content-text">
            You can add additional information or comments to your ticket if needed. We'll keep you updated on any progress.
        </p>
    </div>
    @endif

    <div class="button-container">
        <a href="{{ $ticketUrl }}" class="button button-primary">View Ticket Details</a>
    </div>

    @if($isResolved)
    <hr class="divider">
    
    <p class="content-text">
        <strong>Was this helpful?</strong>
    </p>
    <p class="content-text">
        We value your feedback. If you have any comments about your support experience, please let us know.
    </p>
    @endif

    <p class="content-text">
        Thank you for using our support system. We're always here to help!
    </p>
@endsection
