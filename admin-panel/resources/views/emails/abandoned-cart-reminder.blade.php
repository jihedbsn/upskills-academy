@extends('emails.base-template')

@section('title', $reminderType === 'third' ? 'Special Offer Inside!' : 'Your Cart is Waiting')

@section('header-type', 'primary')

@section('email-title', $reminderType === 'third' ? '🎁 Special Offer Inside!' : '🛒 Your Cart is Waiting')

@section('content')
    <p class="greeting">Hi {{ $student->first_name }},</p>

    @if($reminderType === 'first')
        <p class="content-text">
            We noticed you left some items in your cart. Don't worry, we've saved them for you!
        </p>
        <p class="content-text">
            Your selected courses are waiting for you to continue your learning journey with Upskills Academy.
        </p>
    @elseif($reminderType === 'second')
        <p class="content-text">
            Your courses are still waiting for you! Complete your purchase now and start learning.
        </p>
        <p class="content-text">
            Join thousands of students already learning with us. Your educational journey is just one click away.
        </p>
    @else
        <p class="content-text">
            <strong>This is your last chance!</strong> We're offering you a special discount to help you complete your purchase.
        </p>
        <p class="content-text">
            This offer expires soon. Don't miss out on this opportunity to save!
        </p>
    @endif

    <div class="info-card primary">
        <h3 class="info-card-title">Items in your cart</h3>
        <ul class="info-list">
            @foreach($items as $item)
                <li class="info-list-item">
                    <strong>{{ $item->item_name }}</strong>
                    <span style="float: right; color: #093162; font-weight: 600;">${{ number_format($item->price, 2) }}</span>
                </li>
            @endforeach
        </ul>
        <div style="border-top: 2px solid #e5e7eb; margin-top: 16px; padding-top: 16px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <strong style="font-size: 18px; color: #111827;">Total:</strong>
                <strong style="font-size: 24px; color: #093162;">${{ number_format($totalAmount, 2) }}</strong>
            </div>
        </div>
    </div>

    @if($reminderType === 'third' && $discountCoupon)
        <div class="info-card warning">
            <h3 class="info-card-title" style="text-align: center;">🎉 Exclusive Discount for You! 🎉</h3>
            <p class="content-text" style="text-align: center; margin-bottom: 16px;">
                Use this coupon code at checkout:
            </p>
            <div style="background-color: #ffffff; border: 2px dashed #FCD116; padding: 20px; border-radius: 8px; text-align: center; margin: 20px 0;">
                <div style="font-size: 32px; font-weight: 800; letter-spacing: 3px; color: #093162; font-family: 'Courier New', monospace;">
                    {{ $discountCoupon }}
                </div>
            </div>
            <p class="content-text" style="text-align: center; font-size: 18px; color: #059669; font-weight: 600;">
                Save 10% on your order!
            </p>
        </div>
    @endif

    <div style="text-align: center; margin: 40px 0;">
        <a href="{{ $cartUrl }}" class="btn-primary" style="display: inline-block; padding: 16px 48px; background-color: #093162; color: white; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 6px rgba(0, 87, 166, 0.2);">
            Complete My Purchase →
        </a>
    </div>

    @if($reminderType === 'first')
        <p class="content-text">
            These courses won't reserve themselves! Secure your spot today.
        </p>
    @elseif($reminderType === 'second')
        <p class="content-text">
            Your educational journey is just one click away.
        </p>
    @endif

    <p class="content-text" style="margin-top: 40px; padding-top: 30px; border-top: 1px solid #e5e7eb;">
        Questions? Need help? Reply to this email and we'll be happy to assist you.
    </p>
@endsection

@section('additional-footer')
    <p style="font-size: 13px; color: #9ca3af; margin-top: 12px;">
        You received this email because you have items in your cart.
    </p>
@endsection

