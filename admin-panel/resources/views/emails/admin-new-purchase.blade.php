@extends('emails.base-template')

@section('title', 'New Purchase Notification')

@section('header-type', 'primary')

@section('email-title', 'New Purchase Received')

@section('content')
    <p class="greeting">Hello Administrator,</p>
    
    <p class="content-text">
        A new purchase has been completed. Order details are provided below for your review.
    </p>
    
    <div class="info-card primary">
        <h3 class="info-card-title">Order Information</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Order ID:</strong> #{{ $orderDetails['order_id'] }}</li>
            <li class="info-list-item"><strong>Order Date:</strong> {{ $orderDetails['order_date'] }}</li>
            <li class="info-list-item"><strong>Total Amount:</strong> ${{ number_format($orderDetails['total_amount'], 2) }}</li>
            <li class="info-list-item"><strong>Payment Status:</strong> {{ $orderDetails['payment_status'] }}</li>
        </ul>
    </div>
    
    <div class="info-card success">
        <h3 class="info-card-title">Customer Information</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Name:</strong> {{ $orderDetails['student_name'] }}</li>
            <li class="info-list-item"><strong>Email:</strong> {{ $orderDetails['student_email'] }}</li>
            @if(isset($student->phone) && $student->phone)
            <li class="info-list-item"><strong>Phone:</strong> {{ $student->phone }}</li>
            @endif
            @if(isset($student->country) && $student->country)
            <li class="info-list-item"><strong>Country:</strong> {{ $student->country }}</li>
            @endif
        </ul>
    </div>
    
    <div class="button-container">
        <a href="{{ route('admin.orders.show', $order->id) }}" class="button button-primary">View Order Details</a>
    </div>
    
    <hr class="divider">
    
    <p class="content-text">
        <strong>Quick Actions</strong>
    </p>
    <ul class="info-list">
        <li class="info-list-item">Review order details in the admin panel</li>
        <li class="info-list-item">Process payment confirmation if needed</li>
        <li class="info-list-item">Update order status</li>
        <li class="info-list-item">Contact customer if any issues arise</li>
    </ul>
@endsection
