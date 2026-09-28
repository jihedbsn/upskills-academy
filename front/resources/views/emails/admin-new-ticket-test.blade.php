@extends('emails.base-template')

@section('title', 'New Support Ticket')

@section('header-type', 'success')

@section('email-title', 'New Support Ticket Received')

@section('content')
    <p class="greeting">Hello Administrator,</p>
    
    <p class="content-text">
        Test email template - minimal version.
    </p>
    
    <div class="info-card success">
        <h3 class="info-card-title">Ticket Information</h3>
        <p>Ticket ID: {{ $ticketId }}</p>
    </div>
@endsection
