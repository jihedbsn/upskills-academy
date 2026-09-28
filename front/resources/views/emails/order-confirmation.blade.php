@extends('emails.base-template')

@section('title', 'Order Confirmation')

@section('header-type')
    @if(in_array($paymentMethod, ['bank_transfer', 'cash_on_delivery']))
        warning
    @else
        primary
    @endif
@endsection

@section('email-title')
    @if(in_array($paymentMethod, ['bank_transfer', 'cash_on_delivery']))
        Order Placed - Awaiting Confirmation
    @else
        Order Confirmed
    @endif
@endsection

@section('content')
    <p class="greeting">Hello {{ $studentName }},</p>

    @if(in_array($paymentMethod, ['bank_transfer', 'cash_on_delivery']))
        <p class="content-text">
            Thank you for your order. We have received your {{ $paymentMethod === 'bank_transfer' ? 'bank transfer' : 'cash on delivery' }} request and it is currently <strong>pending admin approval</strong>.
        </p>

        <div class="info-card warning">
            <h3 class="info-card-title">⏳ Payment Pending Approval</h3>
            <p class="content-text">
                Your order has been placed successfully, but you will not have access to the courses until an administrator reviews and approves your payment.
            </p>
            <p class="content-text" style="margin-bottom: 0;">
                <strong>What happens next?</strong>
            </p>
            <ul class="info-list">
                <li class="info-list-item">Our team will review your payment information</li>
                <li class="info-list-item">You will receive an email notification once approved</li>
                <li class="info-list-item">Course access will be granted immediately after approval</li>
            </ul>
        </div>
    @else
        <p class="content-text">
            Thank you for your purchase. We are pleased to confirm that your order has been successfully processed. You now have immediate access to all purchased items.
        </p>
    @endif

    <div class="info-card primary">
        <h3 class="info-card-title">Customer Information</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Name:</strong> {{ $studentName }}</li>
            <li class="info-list-item"><strong>Email:</strong> {{ $student->email }}</li>
            <li class="info-list-item"><strong>Order Date:</strong> {{ $orderDate }}</li>
        </ul>
    </div>

    <h3 class="info-card-title" style="margin-top: 24px; margin-bottom: 16px;">Order Details</h3>
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 14px;">
            <thead>
                <tr style="background-color: #f9fafb; border-bottom: 2px solid #e5e7eb;">
                    <th style="padding: 12px; text-align: left; font-weight: 600; color: #374151;">Type</th>
                    <th style="padding: 12px; text-align: left; font-weight: 600; color: #374151;">Item</th>
                    <th style="padding: 12px; text-align: center; font-weight: 600; color: #374151;">Qty</th>
                    <th style="padding: 12px; text-align: right; font-weight: 600; color: #374151;">Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <td style="padding: 12px;">
                            @if($item['type'] === 'course')
                                <span style="background: #3b82f6; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">COURSE</span>
                            @elseif($item['type'] === 'package')
                                <span style="background: #e6b800; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">PACKAGE</span>
                            @else
                                <span style="background: #059669; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">BOOK</span>
                            @endif
                        </td>
                        <td style="padding: 12px;">
                            <strong style="color: #1f2937;">{{ $item['title'] }}</strong>
                            @if(isset($item['author']) && $item['author'])
                                <br><small style="color: #6b7280;">by {{ $item['author'] }}</small>
                            @endif
                        </td>
                        <td style="padding: 12px; text-align: center; color: #374151;">{{ $item['quantity'] }}</td>
                        <td style="padding: 12px; text-align: right; font-weight: 600; color: #1f2937;">${{ number_format($item['total_price'] ?? $item['unit_price'] ?? 0, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #f9fafb;">
                    <td colspan="3" style="padding: 12px; text-align: right; font-weight: 600; color: #374151;">Subtotal:</td>
                    <td style="padding: 12px; text-align: right; font-weight: 600; color: #1f2937;">${{ number_format($totalAmount ?? 0, 2) }}</td>
                </tr>
                @if(isset($tax) && $tax > 0)
                <tr style="background-color: #f9fafb;">
                    <td colspan="3" style="padding: 12px; text-align: right; color: #6b7280;">Tax (VAT):</td>
                    <td style="padding: 12px; text-align: right; color: #374151;">${{ number_format($tax, 2) }}</td>
                </tr>
                @endif
                <tr style="background-color: #093162; color: white;">
                    <td colspan="3" style="padding: 15px; text-align: right; font-weight: 700; font-size: 16px;">TOTAL:</td>
                    <td style="padding: 15px; text-align: right; font-weight: 700; font-size: 16px;">${{ number_format($totalAmount ?? 0, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    @if(isset($paymentMethod) && $paymentMethod)
        <div class="info-card {{ in_array($paymentMethod, ['bank_transfer', 'cash_on_delivery']) ? 'warning' : 'success' }}" style="margin-top: 24px;">
            <h3 class="info-card-title">Payment Information</h3>
            <ul class="info-list">
                <li class="info-list-item"><strong>Method:</strong> {{ ucfirst(str_replace('_', ' ', $paymentMethod)) }}</li>
                @if(in_array($paymentMethod, ['bank_transfer', 'cash_on_delivery']))
                    <li class="info-list-item"><strong>Status:</strong> <span style="color: #f59e0b; font-weight: bold;">⏳ Pending Approval</span></li>
                @else
                    <li class="info-list-item"><strong>Status:</strong> <span style="color: #10b981; font-weight: bold;">✓ Confirmed</span></li>
                @endif
                @if(isset($notes) && $notes)
                <li class="info-list-item"><strong>Notes:</strong> {{ $notes }}</li>
                @endif
            </ul>
        </div>
    @endif

    <div class="info-card info" style="margin-top: 24px; background: linear-gradient(135deg, #eff6ff, #dbeafe); border-left: 4px solid #3b82f6;">
        <h3 class="info-card-title" style="color: #1e40af;">
            <span style="font-size: 18px; margin-right: 8px;">📘</span>
            Student Guide Available
        </h3>
        <p class="content-text" style="color: #1e3a8a; margin-bottom: 16px;">
            Need help getting started? Download our comprehensive Student Guide (دليل الطالب) to learn how to navigate the platform and make the most of your courses.
        </p>
        <div style="text-align: center;">
            <a href="{{ config('app.url') }}/pdfs/student-guide.pdf" class="button button-info" style="background: linear-gradient(135deg, #3b82f6, #2563eb) !important; display: inline-block;" target="_blank">
                <span style="font-size: 16px; margin-right: 8px;">📥</span> Download Student Guide
            </a>
        </div>
    </div>

    <div class="button-container" style="margin-top: 24px;">
        <a href="{{ config('app.url') }}/en/student-dashboard/courses" class="button button-primary">Access My Dashboard</a>
    </div>

    <hr class="divider">
@endsection

