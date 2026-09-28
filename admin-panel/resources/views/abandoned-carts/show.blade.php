@extends('layouts.app')

@section('title', 'Abandoned Cart Details')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-0">Abandoned Cart Details</h2>
                        <p class="text-muted mb-0">Cart ID: #{{ $abandonedCart->id }}</p>
                    </div>
                    <a href="{{ route('abandoned-carts.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Student Information -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-user"></i> Student Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Name:</strong><br>{{ $abandonedCart->student->first_name }} {{ $abandonedCart->student->last_name }}</p>
                        <p><strong>Email:</strong><br>{{ $abandonedCart->student->email }}</p>
                        <p><strong>Phone:</strong><br>{{ $abandonedCart->student->phone ?? 'N/A' }}</p>
                    </div>
                </div>

                <!-- Status Card -->
                <div class="card mt-3">
                    <div class="card-header bg-{{ $abandonedCart->converted ? 'success' : 'warning' }} text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle"></i> Status</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Status:</strong><br>
                            @if($abandonedCart->converted)
                                <span class="badge bg-success">Converted</span>
                            @else
                                <span class="badge bg-warning">Not Converted</span>
                            @endif
                        </p>
                        <p><strong>Abandoned At:</strong><br>{{ $abandonedCart->abandoned_at->format('Y-m-d H:i:s') }}</p>
                        @if($abandonedCart->converted)
                            <p><strong>Converted At:</strong><br>{{ $abandonedCart->converted_at->format('Y-m-d H:i:s') }}</p>
                        @endif
                    </div>
                </div>

                <!-- Discount Coupon -->
                @if($abandonedCart->discount_coupon)
                <div class="card mt-3">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-tag"></i> Discount Coupon</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <div class="alert alert-warning mb-0">
                                <h4 class="mb-0">{{ $abandonedCart->discount_coupon }}</h4>
                            </div>
                            <small class="text-muted">Sent with 3rd reminder</small>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Cart Items & Timeline -->
            <div class="col-md-8">
                <!-- Cart Items -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-shopping-cart"></i> Cart Items ({{ $abandonedCart->items_count }})</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Course/Product</th>
                                        <th>Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($abandonedCart->items as $item)
                                        <tr>
                                            <td>
                                                <strong>{{ $item->item_name }}</strong>
                                                @if($item->product && $item->product->type_course)
                                                    <br><small class="text-muted">{{ $item->product->getCourseTypeLabelAttribute() }}</small>
                                                @endif
                                            </td>
                                            <td><strong>${{ number_format($item->price, 2) }}</strong></td>
                                        </tr>
                                    @endforeach
                                    <tr class="table-active">
                                        <td class="text-end"><strong>Total:</strong></td>
                                        <td><strong class="text-primary">${{ number_format($abandonedCart->total_amount, 2) }}</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Email Reminders Timeline -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-clock"></i> Reminder Timeline</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <!-- First Reminder -->
                            <div class="timeline-item {{ $abandonedCart->first_reminder_sent_at ? 'completed' : 'pending' }}">
                                <div class="timeline-marker">
                                    @if($abandonedCart->first_reminder_sent_at)
                                        <i class="fas fa-check-circle text-success"></i>
                                    @else
                                        <i class="fas fa-clock text-muted"></i>
                                    @endif
                                </div>
                                <div class="timeline-content">
                                    <h6>First Reminder (1 hour)</h6>
                                    @if($abandonedCart->first_reminder_sent_at)
                                        <p class="text-success mb-0">
                                            <i class="fas fa-check"></i> Sent on {{ $abandonedCart->first_reminder_sent_at->format('Y-m-d H:i:s') }}
                                        </p>
                                    @else
                                        <p class="text-muted mb-0">
                                            @if($abandonedCart->shouldSendFirstReminder())
                                                <i class="fas fa-hourglass-half"></i> Ready to send
                                            @else
                                                <i class="fas fa-clock"></i> Will be sent 1 hour after abandonment
                                            @endif
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <!-- Second Reminder -->
                            <div class="timeline-item {{ $abandonedCart->second_reminder_sent_at ? 'completed' : 'pending' }}">
                                <div class="timeline-marker">
                                    @if($abandonedCart->second_reminder_sent_at)
                                        <i class="fas fa-check-circle text-success"></i>
                                    @else
                                        <i class="fas fa-clock text-muted"></i>
                                    @endif
                                </div>
                                <div class="timeline-content">
                                    <h6>Second Reminder (24 hours)</h6>
                                    @if($abandonedCart->second_reminder_sent_at)
                                        <p class="text-success mb-0">
                                            <i class="fas fa-check"></i> Sent on {{ $abandonedCart->second_reminder_sent_at->format('Y-m-d H:i:s') }}
                                        </p>
                                    @else
                                        <p class="text-muted mb-0">
                                            @if($abandonedCart->shouldSendSecondReminder())
                                                <i class="fas fa-hourglass-half"></i> Ready to send
                                            @else
                                                <i class="fas fa-clock"></i> Will be sent 24 hours after abandonment
                                            @endif
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <!-- Third Reminder -->
                            <div class="timeline-item {{ $abandonedCart->third_reminder_sent_at ? 'completed' : 'pending' }}">
                                <div class="timeline-marker">
                                    @if($abandonedCart->third_reminder_sent_at)
                                        <i class="fas fa-check-circle text-success"></i>
                                    @else
                                        <i class="fas fa-clock text-muted"></i>
                                    @endif
                                </div>
                                <div class="timeline-content">
                                    <h6>Third Reminder (3 days) with Discount</h6>
                                    @if($abandonedCart->third_reminder_sent_at)
                                        <p class="text-success mb-0">
                                            <i class="fas fa-check"></i> Sent on {{ $abandonedCart->third_reminder_sent_at->format('Y-m-d H:i:s') }}
                                        </p>
                                        @if($abandonedCart->discount_coupon)
                                            <p class="text-info mb-0">
                                                <i class="fas fa-tag"></i> Coupon: {{ $abandonedCart->discount_coupon }}
                                            </p>
                                        @endif
                                    @else
                                        <p class="text-muted mb-0">
                                            @if($abandonedCart->shouldSendThirdReminder())
                                                <i class="fas fa-hourglass-half"></i> Ready to send with discount
                                            @else
                                                <i class="fas fa-clock"></i> Will be sent 3 days after abandonment
                                            @endif
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 40px;
}

.timeline-item {
    position: relative;
    padding-bottom: 30px;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -28px;
    top: 30px;
    width: 2px;
    height: calc(100% - 20px);
    background: #e0e0e0;
}

.timeline-item:last-child::before {
    display: none;
}

.timeline-item.completed::before {
    background: #28a745;
}

.timeline-marker {
    position: absolute;
    left: -35px;
    top: 0;
    font-size: 24px;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #e0e0e0;
}

.timeline-item.completed .timeline-content {
    border-left-color: #28a745;
}

.timeline-content h6 {
    margin-bottom: 5px;
    font-weight: bold;
}
</style>
@endsection
