<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
  @include('layouts.head')
  <link rel="stylesheet" href="{{ asset('assets/css/books-custom.css') }}">
  <style>
    /* Custom dropdown styles - always opens downward */
    .custom-select-wrapper {
      position: relative;
      width: 100%;
    }
    .custom-select-trigger {
      width: 100%;
      padding: 15px 20px;
      border: 1px solid #e5e5e5;
      border-radius: 8px;
      font-size: 15px;
      background-color: #fff;
      cursor: pointer;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .custom-select-trigger:hover {
      border-color: #6440FB;
    }
    .custom-select-trigger::after {
      content: '▼';
      font-size: 10px;
      color: #666;
    }
    .custom-select-options {
      position: absolute;
      top: 100%;
      left: 0;
      right: 0;
      background: #fff;
      border: 1px solid #e5e5e5;
      border-radius: 8px;
      margin-top: 5px;
      max-height: 250px;
      overflow-y: auto;
      z-index: 1000;
      display: none;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .custom-select-wrapper.open .custom-select-options {
      display: block;
    }
    .custom-select-option {
      padding: 12px 20px;
      cursor: pointer;
      font-size: 14px;
    }
    .custom-select-option:hover {
      background-color: #f5f5f5;
    }
    .custom-select-option.selected {
      background-color: #6440FB;
      color: #fff;
    }
    .custom-select-search {
      padding: 10px;
      border-bottom: 1px solid #e5e5e5;
      position: sticky;
      top: 0;
      background: #fff;
    }
    .custom-select-search input {
      width: 100%;
      padding: 8px 12px;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 14px;
    }
    /* Make open state work with class toggle */
    .custom-select-options.open {
      display: block !important;
    }

    /* RTL specific styles for Arabic */
    [dir="rtl"] .form-radio h5 {
      margin-left: 0 !important;
      margin-right: 8px !important;
    }

    [dir="rtl"] .form-radio .ml-15 {
      margin-left: 0 !important;
      margin-right: 8px !important;
    }

    [dir="rtl"] .form-radio .ml-25 {
      margin-left: 0 !important;
      margin-right: 25px !important;
    }

    [dir="rtl"] .form-radio .pl-5 {
      padding-left: 0 !important;
      padding-right: 5px !important;
    }

    /* Icon spacing for RTL */
    [dir="rtl"] i.me-2 {
      margin-left: 0.5rem;
      margin-right: 0;
    }

    [dir="rtl"] .icon-tag.me-2 {
      margin-left: 0.5rem;
      margin-right: 0;
    }

    /* RTL fixes for points section */
    [dir="rtl"] i.me-1 {
      margin-left: 0.25rem;
      margin-right: 0;
    }

    [dir="rtl"] .ms-2 {
      margin-right: 0.5rem !important;
      margin-left: 0 !important;
    }

    [dir="rtl"] .button.-sm {
      white-space: nowrap;
      min-width: fit-content;
    }

    /* Points section specific fixes */
    .points-form-row {
      display: flex;
      flex-wrap: nowrap;
      gap: 10px;
      align-items: stretch;
      width: 100%;
    }

    .points-form-row input[type="number"] {
      flex: 0 0 150px;
      width: 150px;
      padding: 12px 15px;
      height: 46px;
      border: 2px solid #ddd;
      border-radius: 8px;
      font-size: 15px;
    }

    .btn-points {
      padding: 12px 20px !important;
      height: 46px !important;
      display: inline-flex !important;
      align-items: center !important;
      justify-content: center !important;
      border-radius: 8px !important;
      font-weight: 500 !important;
      font-size: 15px !important;
      cursor: pointer !important;
      white-space: nowrap !important;
      line-height: 1 !important;
      flex-shrink: 0 !important;
    }

    .btn-points-primary {
      background-color: #f59e0b !important;
      color: white !important;
      border: none !important;
    }

    .btn-points-outline {
      background-color: white !important;
      border: 2px solid #f59e0b !important;
      color: #f59e0b !important;
    }

    .btn-points:hover {
      opacity: 0.9;
    }

    [dir="rtl"] .points-form-row {
      direction: rtl;
    }
  </style>
</head>
<body class="preloader-visible" data-barba="wrapper">
  <!-- preloader start -->
  <div class="preloader js-preloader">
    <div class="preloader__bg"></div>
  </div>
  <!-- preloader end -->

  <main class="main-content">
    @include('layouts.header')

    <div class="content-wrapper js-content-wrapper">
      <!-- Page Header -->
      <section class="page-header -type-1">
        <div class="page-header__bg">
          <div class="bg-image js-lazy" data-bg="{{ asset('assets/img/page-header/bg.jpg') }}"></div>
        </div>

        <div class="container">
          <div class="page-header__content">
            <div class="row justify-center text-center">
              <div class="col-auto">
                <div data-anim="slide-up delay-1">
                  <h1 class="page-header__title">{{ __('messages.checkout') }}</h1>
                </div>

                <div data-anim="slide-up delay-2">
                  <p class="page-header__text">{{ __('messages.checkout_description') }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Checkout Content -->
      <section class="layout-pt-md layout-pb-lg">
        <div class="container">
          <div class="row y-gap-50">
            <!-- Login Form (si non connecté) -->
            @if(!$isAuthenticated)
            <div class="col-lg-8">
              <div class="shopCheckout-form">
                <div class="contact-form row x-gap-30 y-gap-30">
                  <div class="col-12">
                    <h5 class="text-20">{{ __('messages.login_required') }}</h5>
                    <p class="text-14 text-light-1 mt-10">{{ __('messages.login_to_continue_checkout') }}</p>
                  </div>

                  <div class="col-12" id="login-error-message" style="display: none;">
                    <div class="alert alert-danger">
                      <span id="login-error-text"></span>
                    </div>
                  </div>

                  <form id="checkout-login-form" class="contact-form row x-gap-30 y-gap-30 w-1/1">
                    @csrf
                    <div class="col-12">
                      <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.email') }} *</label>
                      <input type="email" name="email" id="login-email" placeholder="{{ __('messages.email') }}" required>
                    </div>

                    <div class="col-12">
                      <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.password') }} *</label>
                      <input type="password" name="password" id="login-password" placeholder="{{ __('messages.password') }}" required>
                    </div>

                    <div class="col-12 text-right">
                      <a href="{{ route('forgot-password') }}" class="text-14 text-purple-1">{{ __('messages.forgot_password') }}?</a>
                    </div>

                    <div class="col-12">
                      <button type="submit" id="checkout-login-btn" class="button -md -purple-1 text-white w-1/1">
                        <span class="button-text">{{ __('messages.login') }}</span>
                        <span class="button-loading" style="display: none;">
                          <i class="fa fa-spinner fa-spin"></i> {{ __('messages.logging_in') }}...
                        </span>
                      </button>
                    </div>
                  </form>

                  <div class="col-12">
                    <div class="text-center mt-20">
                      <p class="text-14 text-light-1">{{ __('messages.dont_have_account') }}
                        <a href="{{ route('signup') }}" class="text-purple-1">{{ __('messages.signup_free') }}</a>
                      </p>
                    </div>
                  </div>

                  <!-- Social Login -->
                  <div class="col-12">
                    <div class="mt-30 mb-20">
                      <div class="text-center">
                        <span class="text-14 text-grey-1">{{ __('messages.or_continue_with') }}</span>
                      </div>
                    </div>
                    <div class="d-flex gap-10 justify-center">
                      {{-- Facebook login disabled for now
                      <a href="{{ route('login.facebook') }}" class="button -md -outline-dark-1 text-dark-1">
                        <i class="fa fa-facebook mr-10"></i> Facebook
                      </a>
                      --}}
                      <a href="{{ route('login.google') }}" class="button -md -outline-dark-1 text-dark-1">
                        <i class="fa fa-google mr-10"></i> Google
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            @else
            <!-- Checkout Form (si connecté) -->
            <div class="col-lg-8">
              <div class="shopCheckout-form">
                <form id="checkout-form" action="{{ route('checkout.process') }}" method="POST" enctype="multipart/form-data" class="contact-form row x-gap-30 y-gap-30">
                  @csrf
                  <div class="col-12">
                    <h5 class="text-20">{{ __('messages.billing_details') }}</h5>
                  </div>

                  <div class="col-sm-6">
                    <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.first_name') }} *</label>
                    <input type="text" name="first_name" value="{{ $user->first_name ?? old('first_name') }}" placeholder="{{ __('messages.first_name') }}" required pattern="[a-zA-Z\s\-']+" title="English letters only">
                    <small class="text-muted">English letters only</small>
                    @error('first_name')
                      <div class="text-red-1 text-14 mt-5">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="col-sm-6">
                    <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.last_name') }} *</label>
                    <input type="text" name="last_name" value="{{ $user->last_name ?? old('last_name') }}" placeholder="{{ __('messages.last_name') }}" required pattern="[a-zA-Z\s\-']+" title="English letters only">
                    <small class="text-muted">English letters only</small>
                    @error('last_name')
                      <div class="text-red-1 text-14 mt-5">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="col-12">
                    <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.email') }} *</label>
                    <input type="email" name="email" value="{{ $user->email ?? old('email') }}" placeholder="{{ __('messages.email') }}" required>
                    @error('email')
                      <div class="text-red-1 text-14 mt-5">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="col-12">
                    <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.phone') }} *</label>
                    <input type="text" name="phone" value="{{ $user->phone ?? old('phone') }}" placeholder="{{ __('messages.phone') }}" required>
                    @error('phone')
                      <div class="text-red-1 text-14 mt-5">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="col-12">
                    <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.country') }} *</label>
                    <div class="custom-select-wrapper" id="country-dropdown">
                      <select name="country" id="country-select" required>
                        <option value="">{{ __('messages.select_country') }}</option>
                        @php
                          $countries = \App\Helpers\CountryHelper::getAllCountries();
                          $countriesArabic = \App\Helpers\CountryHelper::getAllCountriesArabic();
                          $currentCountry = old('country', $user->country ?? '');
                          $isArabic = app()->getLocale() == 'ar';
                        @endphp
                        @foreach($countries as $country)
                          <option value="{{ $country }}" {{ $currentCountry == $country ? 'selected' : '' }}>
                            {{ $isArabic && isset($countriesArabic[$country]) ? $countriesArabic[$country] : $country }}
                          </option>
                        @endforeach
                      </select>
                      <div class="custom-select-trigger" id="country-trigger">
                        <span id="country-selected">{{ old('country') ?: ($user->country ?? __('messages.select_country')) }}</span>
                      </div>
                      <div class="custom-select-options" id="country-options">
                        <div class="custom-select-search">
                          <input type="text" id="country-search" placeholder="Search country...">
                        </div>
                        <div id="country-options-list"></div>
                      </div>
                    </div>
                    @error('country')
                      <div class="text-red-1 text-14 mt-5">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="col-12">
                    <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.address') }} *</label>
                    <input type="text" name="address" value="{{ $user->address ?? old('address') }}" placeholder="{{ __('messages.address') }}" required>
                    @error('address')
                      <div class="text-red-1 text-14 mt-5">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="col-sm-6">
                    <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.city') }} *</label>
                    <input type="text" name="city" value="{{ $user->city ?? old('city') }}" placeholder="{{ __('messages.city') }}" required>
                    @error('city')
                      <div class="text-red-1 text-14 mt-5">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="col-sm-6">
                    <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.zip_code') }} *</label>
                    <input type="text" name="zip_code" value="{{ $user->zip_code ?? old('zip_code') }}" placeholder="{{ __('messages.zip_code') }}" required>
                    @error('zip_code')
                      <div class="text-red-1 text-14 mt-5">{{ $message }}</div>
                    @enderror
                  </div>

                  <!-- Bank Transfer Fields (shown when bank transfer is selected) -->
                  <div id="bank-transfer-fields" style="display: none;" class="col-12">
                    <div class="row x-gap-20 y-gap-15 pt-20">
                      <!-- Bank Details Display -->
                      <div class="col-12">
                        <div class="bg-light-3 rounded-8 px-20 py-15" dir="ltr">
                          <h6 class="text-16 fw-500 text-dark-1 mb-10">
                            <i class="icon-bank text-20 me-2"></i>Bank Transfer Instructions
                          </h6>
                          <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                              <td class="text-14 text-light-1 py-3" style="width: 150px;">Company Name:</td>
                              <td class="text-14 text-dark-1 fw-500 py-3">{{ env('BANK_COMPANY_NAME') ?: env('BANK_NAME') }}</td>
                            </tr>
                            <tr>
                              <td class="text-14 text-light-1 py-3">Bank Name:</td>
                              <td class="text-14 text-dark-1 fw-500 py-3">{{ env('BANK_NAME') }}</td>
                            </tr>
                            <tr>
                              <td class="text-14 text-light-1 py-3">Account Number RIB:</td>
                              <td class="text-14 text-dark-1 fw-500 py-3">{{ env('BANK_ACCOUNT_NUMBER') }}</td>
                            </tr>
                            <tr>
                              <td class="text-14 text-light-1 py-3">IBAN:</td>
                              <td class="text-14 text-dark-1 fw-500 py-3">{{ env('BANK_IBAN') }}</td>
                            </tr>
                            <tr>
                              <td class="text-14 text-light-1 py-3">BIC:</td>
                              <td class="text-14 text-dark-1 fw-500 py-3">{{ env('BANK_BIC') }}</td>
                            </tr>
                          </table>
                        </div>
                      </div>

                      <!-- Payment Description -->
                      <div class="col-12">
                        <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.payment_description') }}</label>
                        <textarea
                          name="payment_description"
                          rows="3"
                          placeholder="{{ __('messages.enter_payment_description') }}"
                          class="form-control"
                        ></textarea>
                        @error('payment_description')
                          <div class="text-red-1 text-14 mt-5">{{ $message }}</div>
                        @enderror
                      </div>

                      <!-- Upload Receipt -->
                      <div class="col-12">
                        <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.upload_receipt') }} *</label>
                        <input
                          type="file"
                          name="payment_receipt"
                          accept="image/*,application/pdf"
                          class="form-control"
                        >
                        <div class="text-light-1 text-13 mt-5">{{ __('messages.receipt_format_hint') }}</div>
                        @error('payment_receipt')
                          <div class="text-red-1 text-14 mt-5">{{ $message }}</div>
                        @enderror
                      </div>
                    </div>
                  </div>

                  <!-- Payment Method Hidden Field -->
                  <div class="col-12">
                    <input type="hidden" name="payment_method" id="payment_method_input" value="credit_card">
                  </div>

                  <!-- Submit Button in Form -->
                  <div class="col-12">
                    <button type="submit" class="button -md -purple-1 text-white w-1/1 mb-10 add-to-cart-course-btn">
                      <span class="button-text">{{ __('messages.place_order') }}</span>
                      <span class="button-loading" style="display: none;">
                        <i class="fa fa-spinner fa-spin"></i> {{ __('messages.processing') }}...
                      </span>
                    </button>
                  </div>
                </form>
              </div>
            </div>
            @endif

            <!-- Order Summary -->
            <div class="col-lg-4">
              <div class="">
                <div class="pt-30 pb-15 bg-white border-light rounded-8 bg-light-4">
                  <h5 class="px-30 text-20 fw-500">
                    {{ __('messages.your_order') }}
                  </h5>

                  <div class="d-flex justify-between px-30 mt-25">
                    <div class="py-15 fw-500 text-dark-1">{{ __('messages.product') }}</div>
                    <div class="py-15 fw-500 text-dark-1">{{ __('messages.subtotal') }}</div>
                  </div>

                  @foreach($cartItems as $item)
                    <div class="d-flex justify-between border-top-dark px-30">
                      <div class="py-15 text-grey">
                        @if($item->book)
                          {{ $item->book->titre }}
                        @elseif($item->product)
                          {{ $item->product->titre }}
                        @elseif($item->package)
                          {{ $item->package->formatted_title }}
                        @endif
                        x{{ $item->quantity }}
                      </div>
                      <div class="py-15 text-grey">@price($item->price)</div>
                    </div>
                  @endforeach

                  <div class="d-flex justify-between border-top-dark px-30">
                    <div class="py-15 fw-500">{{ __('messages.subtotal') }}</div>
                    <div class="py-15 fw-500">@price($total)</div>
                  </div>

                  @if(!empty($referralDiscount) && $referralDiscount > 0)
                    <div class="d-flex justify-between px-30">
                      <div class="py-15 text-green-1">
                        <i class="fas fa-gift me-1"></i>{{ __('messages.referral_discount', ['default' => 'Referral discount']) }} ({{ rtrim(rtrim(number_format($referralPct, 2), '0'), '.') }}% {{ __('messages.first_order_off', ['default' => 'off your first order']) }})
                      </div>
                      <div class="py-15 text-green-1">-@price($referralDiscount)</div>
                    </div>
                  @endif

                  @if(!empty($referralCreditApplied) && $referralCreditApplied > 0)
                    <div class="d-flex justify-between px-30">
                      <div class="py-15 text-green-1">
                        <i class="fas fa-coins me-1"></i>{{ __('messages.referral_credit_applied', ['default' => 'Referral credit applied']) }}
                      </div>
                      <div class="py-15 text-green-1">-@price($referralCreditApplied)</div>
                    </div>
                  @endif

                  @if(!empty($appliedCoupons))
                    @foreach($appliedCoupons as $coupon)
                      <div class="d-flex justify-between px-30">
                        <div class="py-15 text-green-1">
                          {{ __('messages.discount') }} ({{ $coupon['code'] }})
                          @if(isset($coupon['description']))
                            <br><small class="text-12 text-light-1">{{ $coupon['description'] }}</small>
                          @endif
                        </div>
                        <div class="py-15 text-green-1">-@price($coupon['discount_amount'] ?? 0)</div>
                      </div>
                    @endforeach
                  @elseif($appliedCoupon)
                    <div class="d-flex justify-between px-30">
                      <div class="py-15 text-green-1">
                        {{ __('messages.discount') }} ({{ $appliedCoupon['code'] }})
                        @if(isset($appliedCoupon['description']))
                          <br><small class="text-12 text-light-1">{{ $appliedCoupon['description'] }}</small>
                        @endif
                      </div>
                      <div class="py-15 text-green-1">-@price($discountAmount)</div>
                    </div>
                  @endif

                  @if(isset($pointsDiscount) && $pointsDiscount > 0)
                    <div class="d-flex justify-between px-30" id="points-discount-row">
                      <div class="py-15 text-yellow-1">
                        <i class="fas fa-star me-1"></i>{{ __('messages.points_discount') }} ({{ $appliedPoints }} {{ __('messages.points') }})
                      </div>
                      <div class="py-15 text-yellow-1">-@price($pointsDiscount)</div>
                    </div>
                  @endif

                  <div class="d-flex justify-between border-top-dark px-30">
                    <div class="py-15 fw-500 text-dark-1">{{ __('messages.total') }}</div>
                    <div class="py-15 fw-500 text-dark-1">@price($finalTotal)</div>
                  </div>
                </div>

                <!-- Points Section (only if authenticated and has points) -->
                @if($isAuthenticated && isset($pointsBalance) && $pointsBalance['available_points'] > 0)
                  <div class="py-30 px-30 bg-white mt-30 border-light rounded-8" style="background: linear-gradient(135deg, #FFF9E6 0%, #FFF3CD 100%);">
                    <div class="d-flex justify-between items-center mb-15">
                      <h5 class="text-18 fw-500 mb-0">
                        <i class="fas fa-star text-warning me-2"></i>{{ __('messages.your_points') }}
                      </h5>
                      <span class="badge bg-warning text-dark px-3 py-2 text-14">{{ $pointsBalance['available_points'] }} {{ __('messages.points') }}</span>
                    </div>

                    @if($pointsBalance['max_discount_for_order'] > 0)
                      <p class="text-14 text-grey mb-15">
                        {{ __('messages.you_can_use_up_to') }} <strong>{{ $pointsBalance['max_points_for_order'] }}</strong> {{ __('messages.points_for') }}
                        <strong>@price($pointsBalance['max_discount_for_order'])</strong> {{ __('messages.discount') }}
                      </p>

                      @if(isset($appliedPoints) && $appliedPoints > 0)
                        <div class="d-flex items-center justify-between bg-white rounded-8 p-15 border-light">
                          <div>
                            <span class="text-14 text-dark-1">
                              <i class="fas fa-check-circle text-success me-1"></i>
                              {{ $appliedPoints }} {{ __('messages.points_applied') }}
                            </span>
                            <span class="text-success fw-500 ms-2">(-@price($pointsDiscount))</span>
                          </div>
                          <form action="{{ route('checkout.remove-points') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="button -sm -outline-red-1 text-red-1">
                              <i class="fas fa-times me-1"></i>{{ __('messages.remove') }}
                            </button>
                          </form>
                        </div>
                      @else
                        <form action="{{ route('checkout.apply-points') }}" method="POST" class="mt-10">
                          @csrf
                          <div class="points-form-row">
                            <input type="number" name="points" id="points-input"
                              class="form-control"
                              placeholder="{{ __('messages.enter_points') }}"
                              min="20"
                              max="{{ $pointsBalance['max_points_for_order'] }}">
                            <button type="submit" class="btn-points btn-points-primary">
                              <i class="fas fa-star" style="margin-inline-end: 5px;"></i>{{ __('messages.use_points') }}
                            </button>
                          </div>
                          <div style="margin-top: 10px;">
                            <button type="button" class="btn-points btn-points-outline" id="use-all-points-btn">
                              {{ __('messages.use_all') }}
                            </button>
                          </div>
                          <small class="text-12 text-grey mt-10 d-block">
                            {{ __('messages.minimum_20_points') }} (20 {{ __('messages.points') }} = @price(1))
                          </small>
                        </form>
                      @endif
                    @else
                      <p class="text-14 text-grey mb-0">
                        {{ __('messages.points_not_applicable') }}
                      </p>
                    @endif
                  </div>
                @endif

                <!-- Coupon Section (seulement si connecté) -->
                @if($isAuthenticated)
                  <div class="py-30 px-30 bg-white mt-30 border-light rounded-8 bg-light-4 coupon-section-checkout">
                    <div class="d-flex justify-between items-center mb-20">
                      <h5 class="text-20 fw-500 mb-0">{{ __('messages.coupon_code') }}</h5>
                      <a href="{{ route('coupons.index') }}" class="text-14 text-purple-1">
                        <i class="icon-tag me-2"></i>{{ __('Browse Coupons') }}
                      </a>
                    </div>
                    <div class="mt-20">
                      <div class="d-flex gap-10">
                        <input type="text" id="coupon-code-checkout" class="form-control" placeholder="{{ __('messages.enter_coupon_code') }}" maxlength="255">
                        <button type="button" id="apply-coupon-checkout-btn" class="button -md -purple-1 text-white">
                          {{ __('messages.apply_coupon') }}
                        </button>
                      </div>
                      <div id="coupon-message-checkout" class="mt-10 text-14" style="display: none;"></div>
                    </div>
                  </div>
                @endif

                  <!-- Payment Type and Methods (hidden for free orders) -->
                  @if(!isset($isFreeCheckout) || !$isFreeCheckout)

                  <!-- Payment Method Selection -->
                  <div class="py-30 px-30 bg-white mt-30 border-light rounded-8 bg-light-4" id="payment-methods-section">
                    <h5 class="text-20 fw-500">
                      {{ __('messages.payment') }}
                    </h5>

                    <div class="mt-30">
                      <div class="form-radio d-flex items-center">
                        <div class="radio">
                          <input type="radio" name="payment_method" value="credit_card" checked="checked">
                          <div class="radio__mark">
                            <div class="radio__icon"></div>
                          </div>
                        </div>
                        <h5 class="ml-15 text-15 lh-1 fw-500 text-dark-1">
                          <i class="fas fa-credit-card me-2"></i>{{ __('messages.credit_card') }}
                        </h5>
                      </div>
                      <p class="ml-25 pl-5 mt-25">{{ __('messages.pay_securely_with_stripe') }}</p>
                    </div>

                    <div class="mt-30">
                      <div class="form-radio d-flex items-center">
                        <div class="radio">
                          <input type="radio" name="payment_method" value="bank_transfer">
                          <div class="radio__mark">
                            <div class="radio__icon"></div>
                          </div>
                        </div>
                        <h5 class="ml-15 text-15 lh-1 fw-500 text-dark-1">
                          <i class="fas fa-university me-2"></i>{{ __('messages.online_bank_transfer') }}
                        </h5>
                      </div>
                    </div>

                    <div class="mt-30">
                      <div class="form-radio d-flex items-center">
                        <div class="radio">
                          <input type="radio" name="payment_method" value="cash_on_delivery">
                          <div class="radio__mark">
                            <div class="radio__icon"></div>
                          </div>
                        </div>
                        <h5 class="ml-15 text-15 lh-1 text-dark-1">{{ __('messages.cash_on_delivery') }}</h5>
                      </div>
                    </div>
                  </div>
                  @else
                  <!-- Free Order Message -->
                  <div class="py-30 px-30 bg-white mt-30 border-light rounded-8 bg-light-4">
                    <div class="text-center">
                      <i class="fas fa-gift text-40 text-green-1 mb-15"></i>
                      <h5 class="text-20 fw-500 text-green-1">{{ __('messages.free_order_title') }}</h5>
                      <p class="text-15 text-dark-1 mt-10">{{ __('messages.free_order_message') }}</p>
                    </div>
                  </div>
                  @endif

                  <!-- Place Order Button -->
                  <div class="mt-30">
                    <button type="submit" form="checkout-form" class="button -md -accent col-12 -uppercase text-white">
                      <span class="button-text">
                        @if(isset($isFreeCheckout) && $isFreeCheckout)
                          {{ __('messages.complete_order') }}
                        @else
                          {{ __('messages.place_order') }}
                        @endif
                      </span>
                      <span class="button-loading" style="display: none;">
                        <i class="fa fa-spinner fa-spin"></i> {{ __('messages.processing') }}...
                      </span>
                    </button>
                  </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </main>

  @include('layouts.footer')

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Gestion de la connexion depuis checkout
      const loginForm = document.getElementById('checkout-login-form');
      if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
          e.preventDefault();

          const loginBtn = document.getElementById('checkout-login-btn');
          const buttonText = loginBtn.querySelector('.button-text');
          const buttonLoading = loginBtn.querySelector('.button-loading');
          const errorMessage = document.getElementById('login-error-message');
          const errorText = document.getElementById('login-error-text');

          // Afficher le loading
          loginBtn.disabled = true;
          buttonText.style.display = 'none';
          buttonLoading.style.display = 'inline';
          errorMessage.style.display = 'none';

          // Préparer les données
          const formData = new FormData(loginForm);

          // Envoyer la requête AJAX
          fetch('{{ route("login.post") }}', {
            method: 'POST',
            headers: {
              'X-Requested-With': 'XMLHttpRequest',
              'Accept': 'application/json'
            },
            body: formData
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              // Connexion réussie, recharger la page pour afficher le formulaire de checkout
              window.location.reload();
            } else {
              // Afficher l'erreur
              errorText.textContent = data.message || '{{ __("messages.invalid_credentials") }}';
              errorMessage.style.display = 'block';

              // Réactiver le bouton
              loginBtn.disabled = false;
              buttonText.style.display = 'inline';
              buttonLoading.style.display = 'none';
            }
          })
          .catch(error => {
            console.error('Error:', error);
            errorText.textContent = '{{ __("messages.something_went_wrong") }}';
            errorMessage.style.display = 'block';

            // Réactiver le bouton
            loginBtn.disabled = false;
            buttonText.style.display = 'inline';
            buttonLoading.style.display = 'none';
          });
        });
      }

      const form = document.getElementById('checkout-form');
      if (!form) return; // Si le formulaire n'existe pas (utilisateur non connecté), arrêter

      const submitButtons = document.querySelectorAll('#checkout-form button[type="submit"]');

      // Handle payment type selection (Full vs Installment)
      const paymentTypeRadios = document.querySelectorAll('input[name="payment_type"]');
      paymentTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
          // No immediate action - just track selection
          console.log('Payment type selected:', this.value);
        });
      });

      // Show/hide bank transfer details based on payment method selection
      const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
      const bankTransferFields = document.getElementById('bank-transfer-fields');

      paymentRadios.forEach(radio => {
        radio.addEventListener('change', function() {
          // Update hidden field
          document.getElementById('payment_method_input').value = this.value;

          // Show/hide bank transfer fields
          if (this.value === 'bank_transfer') {
            if (bankTransferFields) bankTransferFields.style.display = 'block';
          } else {
            if (bankTransferFields) bankTransferFields.style.display = 'none';
          }
        });
      });

      // Fonction pour activer le loading
      function showLoading() {
        submitButtons.forEach(button => {
          const buttonText = button.querySelector('.button-text');
          const buttonLoading = button.querySelector('.button-loading');

          if (buttonText && buttonLoading) {
            button.disabled = true;
            buttonText.style.display = 'none';
            buttonLoading.style.display = 'inline';
          }
        });
      }

      // Handle form submission
      form.addEventListener('submit', function(e) {
        @if(isset($isFreeCheckout) && $isFreeCheckout)
          // For free orders, just show loading and submit
          showLoading();
          return;
        @endif

        const selectedPayment = document.querySelector('input[name="payment_method"]:checked');

        if (!selectedPayment) {
          e.preventDefault();
          alert('{{ __("messages.select_payment_method") }}');
          return;
        }

        // Update hidden field
        document.getElementById('payment_method_input').value = selectedPayment.value;

        // Validate bank transfer fields if bank transfer is selected
        if (selectedPayment.value === 'bank_transfer') {
          const paymentDescription = document.querySelector('textarea[name="payment_description"]');
          const paymentReceipt = document.querySelector('input[name="payment_receipt"]');

          if (!paymentReceipt || !paymentReceipt.files || paymentReceipt.files.length === 0) {
            e.preventDefault();
            alert('{{ __("messages.upload_receipt") }} is required for bank transfer');
            return;
          }

          // For bank transfer, show loading and let form submit
          showLoading();
        } else if (selectedPayment.value === 'credit_card') {
          // For credit card, show loading
          showLoading();
        } else if (selectedPayment.value === 'cash_on_delivery') {
          // For cash on delivery, show loading and let form submit
          showLoading();
        }
      });

      // Coupon functionality for checkout (seulement si connecté)
      const couponCodeInput = document.getElementById('coupon-code-checkout');
      const applyCouponBtn = document.getElementById('apply-coupon-checkout-btn');
      const couponMessage = document.getElementById('coupon-message-checkout');

      if (applyCouponBtn && couponCodeInput) {
        // Add remove coupon buttons dynamically
        @php
          $appliedCoupons = session('applied_coupons', []);
          $appliedCoupon = session('applied_coupon');
          $hasCoupons = !empty($appliedCoupons) || $appliedCoupon;
        @endphp

        @if($hasCoupons)
          const couponSection = document.querySelector('.coupon-section-checkout');

          @if(!empty($appliedCoupons))
            // Multiple coupons - show individual remove buttons
            const appliedCoupons = @json($appliedCoupons);

            appliedCoupons.forEach(coupon => {
              const removeCouponBtn = document.createElement('button');
              removeCouponBtn.type = 'button';
              removeCouponBtn.className = 'button -sm -outline-light-1 text-light-1 mt-10 me-10';
              removeCouponBtn.style.fontSize = '13px';
              removeCouponBtn.style.padding = '6px 12px';
              removeCouponBtn.innerHTML = '{{ __("messages.remove_coupon") }}: ' + coupon.code;
              removeCouponBtn.addEventListener('click', function() {
                removeCoupon(coupon.code);
              });

              if (couponSection) {
                couponSection.appendChild(removeCouponBtn);
              }
            });

            // Add "Remove All" button if more than one coupon
            if (appliedCoupons.length > 1) {
              const removeAllBtn = document.createElement('button');
              removeAllBtn.type = 'button';
              removeAllBtn.className = 'button -sm -outline-purple-1 text-purple-1 mt-10';
              removeAllBtn.style.fontSize = '13px';
              removeAllBtn.style.padding = '6px 12px';
              removeAllBtn.innerHTML = '{{ __("messages.remove_coupon") }} ({{ __("messages.all") }})';
              removeAllBtn.addEventListener('click', function() {
                removeCoupon(); // No code = remove all
              });

              if (couponSection) {
                couponSection.appendChild(removeAllBtn);
              }
            }
          @else
            // Single coupon (backward compatibility)
            const removeCouponBtn = document.createElement('button');
            removeCouponBtn.type = 'button';
            removeCouponBtn.className = 'button -sm -outline-light-1 text-light-1 mt-10';
            removeCouponBtn.style.fontSize = '13px';
            removeCouponBtn.style.padding = '6px 12px';
            removeCouponBtn.innerHTML = '{{ __("messages.remove_coupon") }}';
            removeCouponBtn.addEventListener('click', function() {
              removeCoupon();
            });

            if (couponSection) {
              couponSection.appendChild(removeCouponBtn);
            }
          @endif
        @endif

        applyCouponBtn.addEventListener('click', function() {
          const couponCode = couponCodeInput.value.trim();

          if (!couponCode) {
            showCouponMessage('{{ __("messages.enter_coupon_code") }}', 'error');
            return;
          }

          applyCouponBtn.disabled = true;
          applyCouponBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> {{ __("messages.applying") }}...';

          fetch('{{ route("cart.apply-coupon") }}', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
              coupon_code: couponCode
            })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              showCouponMessage(data.message, 'success');
              couponCodeInput.value = '';
              // Reload page to show updated totals
              setTimeout(() => {
                window.location.reload();
              }, 1500);
            } else {
              let errorMessage = data.message;
              if (data.details) {
                if (data.details.minimum_required && data.details.total_amount < data.details.minimum_required) {
                  errorMessage = `{{ __("messages.coupon_minimum_amount") }}: $${data.details.minimum_required}. {{ __("messages.current_total") }}: $${data.details.total_amount}`;
                }
              }
              showCouponMessage(errorMessage, 'error');
            }
          })
          .catch(error => {
            console.error('Coupon application error:', error);
            showCouponMessage('{{ __('messages.coupon_error_occurred') }}', 'error');
          })
          .finally(() => {
            applyCouponBtn.disabled = false;
            applyCouponBtn.innerHTML = '{{ __("messages.apply_coupon") }}';
          });
        });
      }

      function showCouponMessage(message, type) {
        if (couponMessage) {
          couponMessage.textContent = message;
          let colorClass = 'text-red-1';

          if (type === 'success') {
            colorClass = 'text-green-1';
          } else if (type === 'info') {
            colorClass = 'text-blue-1';
          }

          couponMessage.className = `mt-10 text-14 ${colorClass}`;
          couponMessage.style.display = 'block';

          // Add browse coupons link for error messages
          if (type === 'error') {
            const browseLink = document.createElement('a');
            browseLink.href = '{{ route("coupons.index") }}';
            browseLink.className = 'text-purple-1 d-block mt-10';
            browseLink.style.textDecoration = 'underline';
            browseLink.innerHTML = '<i class="icon-tag mr-5"></i>{{ __("messages.browse_available_coupons") }}';
            couponMessage.appendChild(document.createElement('br'));
            couponMessage.appendChild(browseLink);
          }

          setTimeout(() => {
            couponMessage.style.display = 'none';
          }, 5000);
        }
      }

      // Function for removing coupon (used by dynamically created buttons)
      function removeCoupon(couponCode = null) {
        const url = '{{ route("cart.remove-coupon") }}' + (couponCode ? '?coupon_code=' + couponCode : '');

        // Fade out coupon section immediately for better UX
        const couponSection = document.querySelector('.coupon-section-checkout');
        if (couponSection) {
          couponSection.style.opacity = '0.3';
        }

        fetch(url, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Reload page to show updated totals
            setTimeout(() => {
              window.location.reload();
            }, 500);
          }
        })
        .catch(error => {
          console.error('Error removing coupon:', error);
          if (couponSection) {
            couponSection.style.opacity = '1';
          }
        });
      }

      // Make removeCoupon available globally for onclick handlers
      window.removeCoupon = removeCoupon;

      // Custom Country Dropdown - Always opens downward
      initCustomCountryDropdown();

      function initCustomCountryDropdown() {
        const select = document.getElementById('country-select');
        const trigger = document.getElementById('country-trigger');
        const optionsContainer = document.getElementById('country-options');
        const optionsList = document.getElementById('country-options-list');
        const searchInput = document.getElementById('country-search');
        const selectedDisplay = document.getElementById('country-selected');

        if (!select || !trigger || !optionsContainer || !optionsList) return;

        // Hide the native select
        select.style.display = 'none';

        // Build options list from native select
        const options = Array.from(select.options);
        optionsList.innerHTML = '';

        options.forEach((option, index) => {
          if (index === 0 && option.value === '') return; // Skip placeholder

          const optionDiv = document.createElement('div');
          optionDiv.className = 'custom-select-option';
          optionDiv.setAttribute('data-value', option.value);
          optionDiv.textContent = option.textContent;

          if (option.selected && option.value !== '') {
            optionDiv.classList.add('selected');
            selectedDisplay.textContent = option.textContent;
          }

          optionDiv.addEventListener('click', function() {
            selectOption(option.value, option.textContent);
          });

          optionsList.appendChild(optionDiv);
        });

        // Toggle dropdown on trigger click
        trigger.addEventListener('click', function(e) {
          e.stopPropagation();
          const isOpen = optionsContainer.classList.contains('open');

          // Close all other custom dropdowns first
          document.querySelectorAll('.custom-select-options.open').forEach(el => {
            el.classList.remove('open');
          });

          if (!isOpen) {
            optionsContainer.classList.add('open');
            searchInput.focus();
          } else {
            optionsContainer.classList.remove('open');
          }
        });

        // Search functionality
        searchInput.addEventListener('input', function() {
          const searchTerm = this.value.toLowerCase();
          const allOptions = optionsList.querySelectorAll('.custom-select-option');

          allOptions.forEach(option => {
            const text = option.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
              option.style.display = 'block';
            } else {
              option.style.display = 'none';
            }
          });
        });

        // Prevent search input from closing dropdown
        searchInput.addEventListener('click', function(e) {
          e.stopPropagation();
        });

        // Close on click outside
        document.addEventListener('click', function(e) {
          if (!e.target.closest('.custom-select-wrapper')) {
            optionsContainer.classList.remove('open');
          }
        });

        // Close on escape key
        document.addEventListener('keydown', function(e) {
          if (e.key === 'Escape') {
            optionsContainer.classList.remove('open');
          }
        });

        function selectOption(value, text) {
          // Update native select
          select.value = value;

          // Update display
          selectedDisplay.textContent = text;

          // Update selected class
          optionsList.querySelectorAll('.custom-select-option').forEach(opt => {
            opt.classList.remove('selected');
            if (opt.getAttribute('data-value') === value) {
              opt.classList.add('selected');
            }
          });

          // Close dropdown
          optionsContainer.classList.remove('open');

          // Clear search
          searchInput.value = '';
          optionsList.querySelectorAll('.custom-select-option').forEach(opt => {
            opt.style.display = 'block';
          });

          // Trigger change event on native select
          select.dispatchEvent(new Event('change'));
        }
      }

      // Points "Use All" button functionality
      const useAllPointsBtn = document.getElementById('use-all-points-btn');
      const pointsInput = document.getElementById('points-input');

      if (useAllPointsBtn && pointsInput) {
        useAllPointsBtn.addEventListener('click', function() {
          const maxPoints = parseInt(pointsInput.getAttribute('max')) || 0;
          pointsInput.value = maxPoints;
        });
      }
    });
  </script>
</body>
</html>
