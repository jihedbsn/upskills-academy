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
            <li class="info-list-item"><strong>Order ID:</strong> #{{ $orderId }}@if(count($items) > 1) <span style="color: #666;">(+ {{ count($items) - 1 }} more)</span>@endif</li>
            <li class="info-list-item"><strong>Customer:</strong> {{ $customerName }}</li>
            <li class="info-list-item"><strong>Email:</strong> {{ $customerEmail }}</li>
            @if(isset($student->phone) && $student->phone)
            <li class="info-list-item"><strong>Phone:</strong> {{ $student->phone }}</li>
            @endif
            @if(isset($student->country) && $student->country)
            <li class="info-list-item"><strong>Country:</strong> {{ $student->country }}</li>
            @endif
            <li class="info-list-item"><strong>Date:</strong> {{ $orderDate }}</li>
            <li class="info-list-item"><strong>Payment Method:</strong> {{ $paymentMethod }}</li>
            <li class="info-list-item"><strong>Payment Status:</strong> <span style="color: {{ $paymentStatus === 'Pending' ? '#ff9800' : ($paymentStatus === 'Approved' ? '#4caf50' : '#666') }};">{{ $paymentStatus }}</span></li>
            <li class="info-list-item"><strong>Total Amount:</strong> <span style="color: #4caf50; font-size: 1.2em; font-weight: bold;">${{ number_format($totalAmount, 2) }}</span></li>
        </ul>
    </div>

    <div class="info-card success">
        <h3 class="info-card-title">Order Items ({{ count($items) }})</h3>
        <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
            <thead>
                <tr style="background-color: #f5f5f5;">
                    <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">Item</th>
                    <th style="padding: 10px; text-align: left; border-bottom: 2px solid #ddd;">Type</th>
                    <th style="padding: 10px; text-align: center; border-bottom: 2px solid #ddd;">Qty</th>
                    <th style="padding: 10px; text-align: right; border-bottom: 2px solid #ddd;">Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;">{{ $item['title'] ?? 'Unknown Item' }}</td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;"><span style="background-color: #e3f2fd; padding: 3px 8px; border-radius: 3px; font-size: 0.85em;">{{ $item['type'] ?? 'Item' }}</span></td>
                    <td style="padding: 10px; text-align: center; border-bottom: 1px solid #eee;">{{ $item['quantity'] ?? 1 }}</td>
                    <td style="padding: 10px; text-align: right; border-bottom: 1px solid #eee; font-weight: bold;">${{ number_format($item['price'] ?? 0, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="button-container">
        <a href="{{ $orderUrl }}" class="button button-primary">View Order Details</a>
    </div>

    <hr class="divider">

    <p class="content-text">
        <strong>Quick Actions</strong>
    </p>
    <ul class="info-list">
        <li class="info-list-item">Review order details in the admin panel</li>
        @if($paymentStatus === 'Pending')
        <li class="info-list-item" style="color: #ff9800;">⚠️ Payment pending approval - review and approve/reject</li>
        @endif
        <li class="info-list-item">Contact customer if any issues arise</li>
    </ul>
@endsection
