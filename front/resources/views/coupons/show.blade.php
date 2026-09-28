@extends('layouts.app')

@section('content')

<!-- Page Header -->
<section class="page-header -type-1">
    <div class="container">
        <div class="page-header__content">
            <div class="row justify-center text-center">
                <div class="col-auto">
                    <div>
                        <h1 class="page-header__title">{{ $coupon->nom }}</h1>
                    </div>
                    <div>
                        <p class="page-header__text">{{ $coupon->custom_message ?? __('Exclusive Discount Offer') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Coupon Details Section -->
<section class="layout-pt-md layout-pb-lg">
    <div class="container">
        <div class="row justify-center">
            <div class="col-lg-8">

                <!-- Main Coupon Card -->
                <div class="card -border-purple-1 rounded-16 overflow-hidden">
                    <!-- Header with Discount -->
                    <div class="card__header bg-purple-1 py-40 text-center">
                        @if($coupon->type === 'percentage')
                        <div class="text-80 text-white fw-700 mb-10">{{ number_format($coupon->valeur, 0) }}%</div>
                        <div class="text-20 text-white">{{ __('DISCOUNT') }}</div>
                        @else
                        <div class="text-70 text-white fw-700 mb-10">${{ number_format($coupon->valeur, 2) }}</div>
                        <div class="text-20 text-white">{{ __('OFF') }}</div>
                        @endif
                    </div>

                    <!-- Coupon Code Section -->
                    <div class="card__body p-40">
                        <div class="text-center mb-40">
                            <h3 class="text-24 fw-600 mb-10">{{ __('Your Coupon Code') }}</h3>
                            <div class="d-flex justify-center items-center bg-light-4 rounded-8 px-40 py-20 mb-15" style="max-width: 400px; margin: 0 auto;">
                                <span class="text-30 fw-600 text-purple-1 code-text mr-20">{{ $coupon->code }}</span>
                                <button type="button" class="button -sm -purple-1 text-white copy-code-btn" data-code="{{ $coupon->code }}">
                                    <i class="icon-copy mr-5"></i> {{ __('Copy') }}
                                </button>
                            </div>
                            <small class="text-14 text-light-1">{{ __('Click to copy and use at checkout') }}</small>
                        </div>

                        <!-- Coupon Details -->
                        <div class="border-top-light pt-30">
                            <h4 class="text-20 fw-600 mb-20">{{ __('Offer Details') }}</h4>

                            <div class="row y-gap-20">
                                <!-- Validity Period -->
                                <div class="col-12">
                                    <div class="d-flex items-start">
                                        <div class="size-50 d-flex justify-center items-center bg-light-purple-1 rounded-8 mr-15">
                                            <i class="icon-clock text-24 text-purple-1"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="text-16 fw-500 mb-5">{{ __('Valid Until') }}</div>
                                            <div class="text-15 text-light-1">{{ $coupon->date_fin->format('l, F d, Y') }}</div>
                                            @if($coupon->date_fin->isFuture())
                                            <div class="text-14 text-green-1 mt-5">
                                                <i class="icon-check mr-5"></i>{{ __('Active') }} ({{ $coupon->date_fin->diffForHumans() }})
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Discount Description -->
                                @if($coupon->discount_description)
                                <div class="col-12">
                                    <div class="d-flex items-start">
                                        <div class="size-50 d-flex justify-center items-center bg-light-purple-1 rounded-8 mr-15">
                                            <i class="icon-info text-24 text-purple-1"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="text-16 fw-500 mb-5">{{ __('Description') }}</div>
                                            <div class="text-15 text-light-1">{{ $coupon->discount_description }}</div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Minimum Purchase -->
                                @if($coupon->min_purchase_amount)
                                <div class="col-12">
                                    <div class="d-flex items-start">
                                        <div class="size-50 d-flex justify-center items-center bg-light-purple-1 rounded-8 mr-15">
                                            <i class="icon-shopping-cart text-24 text-purple-1"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="text-16 fw-500 mb-5">{{ __('Minimum Purchase') }}</div>
                                            <div class="text-15 text-light-1">${{ number_format($coupon->min_purchase_amount, 2) }}</div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Maximum Discount -->
                                @if($coupon->max_discount_amount)
                                <div class="col-12">
                                    <div class="d-flex items-start">
                                        <div class="size-50 d-flex justify-center items-center bg-light-purple-1 rounded-8 mr-15">
                                            <i class="icon-percent text-24 text-purple-1"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="text-16 fw-500 mb-5">{{ __('Maximum Discount') }}</div>
                                            @php
                                                $currencySymbol = session('currency', 'USD') === 'TND' ? 'TND' : '$';
                                            @endphp
                                            <div class="text-15 text-light-1">{{ __('Up to') }} {{ $currencySymbol }} {{ number_format($coupon->max_discount_amount, 2) }}</div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Usage Limit -->
                                @if($coupon->limit_utilise)
                                <div class="col-12">
                                    <div class="d-flex items-start">
                                        <div class="size-50 d-flex justify-center items-center bg-light-purple-1 rounded-8 mr-15">
                                            <i class="icon-user text-24 text-purple-1"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="text-16 fw-500 mb-5">{{ __('Limited Availability') }}</div>
                                            <div class="text-15 text-light-1">
                                                {{ $coupon->limit_utilise - $coupon->usage_count }} {{ __('remaining uses') }}
                                            </div>
                                            <div class="progress mt-10" style="height: 6px;">
                                                @php
                                                $usagePercentage = ($coupon->usage_count / $coupon->limit_utilise) * 100;
                                                @endphp
                                                <div class="progress-bar bg-purple-1" style="width: {{ $usagePercentage }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Special Features -->
                        @if($coupon->auto_apply || $coupon->first_purchase_only || $coupon->is_stackable)
                        <div class="border-top-light pt-30 mt-30">
                            <h4 class="text-20 fw-600 mb-20">{{ __('Special Features') }}</h4>
                            <div class="row y-gap-15">
                                @if($coupon->auto_apply)
                                <div class="col-md-4">
                                    <div class="badge -purple-1 text-purple-1 text-14 px-15 py-10 w-100">
                                        <i class="icon-lightning mr-5"></i>{{ __('Auto-applies') }}
                                    </div>
                                </div>
                                @endif

                                @if($coupon->first_purchase_only)
                                <div class="col-md-4">
                                    <div class="badge -orange-1 text-orange-1 text-14 px-15 py-10 w-100">
                                        <i class="icon-star mr-5"></i>{{ __('First Purchase') }}
                                    </div>
                                </div>
                                @endif

                                @if($coupon->is_stackable)
                                <div class="col-md-4">
                                    <div class="badge -green-1 text-green-1 text-14 px-15 py-10 w-100">
                                        <i class="icon-layers mr-5"></i>{{ __('Stackable') }}
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="border-top-light pt-30 mt-40">
                            <div class="row y-gap-15">
                                <div class="col-md-6">
                                    <a href="{{ route('filter.index') }}" class="button -md -purple-1 text-white w-100">
                                        <i class="icon-book mr-10"></i>{{ __('Browse Courses') }}
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <a href="{{ route('cart.index') }}" class="button -md -outline-purple-1 text-purple-1 w-100">
                                        <i class="icon-shopping-cart mr-10"></i>{{ __('Go to Cart') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Back Button -->
                <div class="text-center mt-30">
                    <a href="{{ route('coupons.index') }}" class="button -md -outline-purple-1 text-purple-1">
                        <i class="icon-arrow-left mr-10"></i>{{ __('View All Coupons') }}
                    </a>
                </div>

            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Copy code functionality
        const copyButton = document.querySelector('.copy-code-btn');

        if (copyButton) {
            copyButton.addEventListener('click', function() {
                const code = this.getAttribute('data-code');

                // Copy to clipboard
                navigator.clipboard.writeText(code).then(() => {
                    // Show success feedback
                    const originalText = this.innerHTML;
                    this.innerHTML = '<i class="icon-check mr-5"></i> {{ __("Copied!") }}';
                    this.classList.remove('-purple-1');
                    this.classList.add('-green-1');

                    // Reset after 2 seconds
                    setTimeout(() => {
                        this.innerHTML = originalText;
                        this.classList.remove('-green-1');
                        this.classList.add('-purple-1');
                    }, 2000);
                }).catch(err => {
                    alert('{{ __("Failed to copy code") }}');
                });
            });
        }
    });
</script>
@endpush

@push('styles')
<style>

</style>
@endpush
