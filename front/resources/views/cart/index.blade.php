<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
  @include('layouts.head')
  <link rel="stylesheet" href="{{ asset('assets/css/books-custom.css') }}">
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
                  <h1 class="page-header__title">{{ __('messages.shopping_cart') }}</h1>
                </div>

                <div data-anim="slide-up delay-2">
                  <p class="page-header__text">{{ __('messages.cart_description') }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Cart Content -->
      <section class="layout-pt-md layout-pb-lg">
        <div class="container">
          @if(session('success'))
            <div class="alert alert-success mb-30">
              {{ session('success') }}
            </div>
          @endif

          @if($cartItems->count() > 0)
            <div class="row justify-end">
              <div class="col-12">
                <!-- Desktop Headers -->
                <div class="px-30 pr-60 py-25 rounded-8 bg-light-6 md:d-none">
                  <div class="row justify-between">
                    <div class="col-md-4">
                      <div class="fw-500 text-purple-1">{{ __('messages.product') }}</div>
                    </div>
                    <div class="col-md-2">
                      <div class="fw-500 text-purple-1">{{ __('messages.price') }}</div>
                    </div>
                    <div class="col-md-2">
                      <div class="fw-500 text-purple-1">{{ __('messages.quantity') }}</div>
                    </div>
                    <div class="col-md-2">
                      <div class="fw-500 text-purple-1">{{ __('messages.subtotal') }}</div>
                    </div>
                    <div class="col-md-1">
                      <div class="d-flex justify-end">
                        <div class="fw-500 text-purple-1">{{ __('messages.remove') }}</div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="px-30 pr-60 md:px-0">
                  @foreach($cartItems as $item)
                    <div class="row y-gap-20 justify-between items-center pt-30 pb-30 border-bottom-light">
                      <!-- Product Info -->
                      <div class="col-md-4">
                        <div class="d-flex items-center">
                          <div class="">
                            <div class="size-100 bg-image rounded-8">
                            @if($item->book)
                                <img src="{{ $item->book->image ? config('app.file_url') . 'uploads/books/images/' . $item->book->image : asset('assets/img/placeholders/course-not-found.png') }}"
                                     alt="{{ $item->book->titre }}"
                                     style="width: 100%; height: 100%; object-fit: cover;"
                                     onerror="this.src='{{ asset('assets/img/placeholders/course-not-found.png') }}'">
                            @elseif($item->product)
                                <img src="{{ $item->product->image ? config('app.file_url') . 'uploads/products/images/' . $item->product->image : asset('assets/img/placeholders/course-not-found.png') }}"
                                     alt="{{ $item->product->titre }}"
                                     style="width: 100%; height: 100%; object-fit: cover;"
                                     onerror="this.src='{{ asset('assets/img/placeholders/course-not-found.png') }}'">
                            @elseif($item->package)
                                <img src="{{ $item->package->image ? config('app.file_url') . 'uploads/package/' . $item->package->image : asset('assets/img/placeholders/course-not-found.png') }}"
                                     alt="{{ $item->package->title }}"
                                     style="width: 100%; height: 100%; object-fit: cover;"
                                     onerror="this.src='{{ asset('assets/img/placeholders/course-not-found.png') }}'">
                            @endif
                            </div>
                          </div>
                          <div class="fw-500 text-dark-1 ml-30">
                            @if($item->book)
                              <a href="{{ route('books.show', $item->book) }}" class="text-dark-1">{{ $item->book->titre }}</a>
                              <div class="text-12 text-light-1 mt-5">{{ __('messages.books') }}</div>
                            @elseif($item->product)
                              <a href="{{ route('courses.show', ['locale' => app()->getLocale(), 'product' => $item->product->id]) }}" class="text-dark-1">{{ $item->product->titre }}</a>
                              <div class="text-12 text-light-1 mt-5">{{ __('messages.courses') }}</div>
                            @elseif($item->package)
                              <a href="{{ route('packages.show', $item->package) }}" class="text-dark-1">{{ $item->package->title }}</a>
                              <div class="text-12 text-light-1 mt-5">{{ __('messages.packages') }}</div>
                            @endif
                          </div>
                        </div>
                      </div>

                      <!-- Price -->
                      <div class="col-md-2 md:mt-15">
                        <div class="">
                          <div class="shopCart-products__title d-none md:d-block mb-10">
                            {{ __('messages.price') }}
                          </div>
                          <p>@price($item->price)</p>
                        </div>
                      </div>

                      <!-- Quantity -->
                      <div class="col-md-2">
                        <div class="">
                          <div class="shopCart-products__title d-none md:d-block mb-10">
                            {{ __('messages.quantity') }}
                          </div>
                          <div class="text-14 text-dark-1">1</div>
                        </div>
                      </div>

                      <!-- Subtotal -->
                      <div class="col-md-2">
                        <div class="">
                          <div class="shopCart-products__title d-none md:d-block mb-10">
                            {{ __('messages.subtotal') }}
                          </div>
                          <p>@price($item->price)</p>
                        </div>
                      </div>

                      <!-- Remove -->
                      <div class="col-md-1">
                        <div class="d-flex justify-end">
                          <form method="POST" action="{{ route('cart.remove', $item->id) }}" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="button -sm -outline-red-1 text-red-1" onclick="return confirm('{{ __('messages.remove') }}?')">
                              <i class="icon" data-feather="x"></i>
                            </button>
                          </form>
                        </div>
                      </div>
                    </div>
                  @endforeach
                </div>

                <!-- Cart Footer -->
                <div class="shopCart-footer px-16 mt-30">
                  <div class="row justify-between y-gap-30">
                    <div class="col-xl-5">
                      <!-- Coupon Code Section -->
                      <div class="coupon-section">
                        <div class="d-flex justify-between items-center mb-15">
                          <h6 class="text-16 fw-500 mb-0">{{ __('coupon.coupon_code') }}</h6>
                          <a href="{{ route('coupons.index') }}" class="text-14 text-purple-1">
                            <i class="icon-tag mr-5"></i>{{ __('coupon.available_coupons') }}
                          </a>
                        </div>
                        <div class="d-flex gap-10">
                          <input type="text" id="coupon-code" class="form-control" placeholder="{{ __('coupon.enter_coupon_code') }}" maxlength="255">
                          <button type="button" id="apply-coupon-btn" class="button -md -purple-1 text-white">
                            {{ __('coupon.apply_coupon') }}
                          </button>
                        </div>
                        <div id="coupon-message" class="mt-10 text-14" style="display: none;"></div>
                      </div>
                    </div>

                    <div class="col-auto">
                      <div class="shopCart-footer__item">
                        <form method="POST" action="{{ route('cart.clear') }}" class="d-inline">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="button -md -outline-red-1 text-red-1" onclick="return confirm('{{ __('messages.clear_cart') }}?')">
                            {{ __('messages.clear_cart') }}
                          </button>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Cart Summary -->
              <div class="col-xl-4 col-lg-5 layout-pt-lg">
                <div class="py-30 bg-light-4 rounded-8 border-light">
                  <h5 class="px-30 text-20 fw-500">
                    {{ __('messages.cart_total') }}
                  </h5>

                  <div class="d-flex justify-between px-30 item mt-25">
                    <div class="py-15 fw-500 text-dark-1">{{ __('messages.subtotal') }}</div>
                    <div class="py-15 fw-500 text-dark-1">@price($total)</div>
                  </div>

                  @php
                    // Support both old single coupon and new multiple coupons
                    $appliedCoupons = session('applied_coupons', []);
                    $appliedCoupon = session('applied_coupon'); // Backward compatibility

                    // Calculate total discount
                    $totalDiscount = 0;
                    if (!empty($appliedCoupons)) {
                      foreach ($appliedCoupons as $coupon) {
                        $totalDiscount += $coupon['discount_amount'] ?? 0;
                      }
                    } elseif ($appliedCoupon) {
                      $totalDiscount = $appliedCoupon['discount_amount'] ?? 0;
                    }

                    $finalTotal = $total - $totalDiscount;
                  @endphp

                  @if(!empty($appliedCoupons))
                    @foreach($appliedCoupons as $coupon)
                      <div class="d-flex justify-between px-30 item" data-coupon-code="{{ $coupon['code'] }}">
                        <div class="py-15 text-green-1">
                          {{ __('coupon.coupon_discount') }} ({{ $coupon['code'] }})
                          @if(isset($coupon['description']))
                            <br><small class="text-12 text-light-1">{{ $coupon['description'] }}</small>
                          @endif
                        </div>
                        <div class="py-15 text-green-1">-@price($coupon['discount_amount'] ?? 0)</div>
                      </div>
                    @endforeach
                  @elseif($appliedCoupon)
                    <div class="d-flex justify-between px-30 item" data-coupon-code="{{ $appliedCoupon['code'] }}">
                      <div class="py-15 text-green-1">
                        {{ __('coupon.coupon_discount') }} ({{ $appliedCoupon['code'] }})
                        @if(isset($appliedCoupon['description']))
                          <br><small class="text-12 text-light-1">{{ $appliedCoupon['description'] }}</small>
                        @endif
                      </div>
                      <div class="py-15 text-green-1">-@price($appliedCoupon['discount_amount'] ?? 0)</div>
                    </div>
                  @endif

                  <div class="d-flex justify-between px-30 item border-top-dark">
                    <div class="pt-15 fw-500 text-dark-1">{{ __('messages.total') }}</div>
                    <div class="pt-15 fw-500 text-dark-1">@price($finalTotal)</div>
                  </div>
                </div>

                <a href="{{ route('checkout.index') }}" class="button -md -purple-1 text-white col-12 mt-30">{{ __('messages.proceed_to_checkout') }}</a>

                <a href="{{ route('filter.index') }}" class="button -md -outline-purple-1 text-purple-1 col-12 mt-15">
                  {{ __('messages.continue_shopping') }}
                </a>
              </div>
            </div>
          @else
            <!-- Empty Cart -->
            <div class="text-center py-80">
              <div class="icon-basket text-80 text-light-1 mb-30"></div>
              <h3 class="text-24 fw-600 text-dark-1 mb-15">{{ __('messages.cart_empty') }}</h3>
              <p class="text-16 text-light-1 mb-30">{{ __('messages.cart_empty_description') }}</p>
              <a href="{{ route('filter.index') }}" class="button -md -purple-1 text-white">
                {{ __('messages.continue_shopping') }}
              </a>
            </div>
          @endif
        </div>
      </section>
    </div>
  </main>

  @include('layouts.footer')

  <script>
    function updateQuantity(button, change) {
      const input = button.closest('.input-counter').querySelector('.input-counter__counter');
      const currentValue = parseInt(input.value);
      const newValue = Math.max(1, Math.min(10, currentValue + change));
      input.value = newValue;
      input.form.submit();
    }

    // Coupon functionality
    document.addEventListener('DOMContentLoaded', function() {
      const couponCodeInput = document.getElementById('coupon-code');
      const applyCouponBtn = document.getElementById('apply-coupon-btn');
      const couponMessage = document.getElementById('coupon-message');

      if (applyCouponBtn) {
        // Check if there's a coupon code to auto-apply from special offers page
        const couponToApply = sessionStorage.getItem('coupon_to_apply');
        if (couponToApply) {
          // Clear from storage
          sessionStorage.removeItem('coupon_to_apply');

          // Set in input field
          couponCodeInput.value = couponToApply;

          // Show visual feedback
          couponCodeInput.style.borderColor = '#1877F2';
          couponCodeInput.style.backgroundColor = '#f0f7ff';

          // Auto-apply after a short delay to ensure page is fully loaded
          setTimeout(() => {
            showMessage('{{ __("messages.applying") }} ' + couponToApply + '...', 'info');
            applyCoupon(couponToApply);
          }, 500);
        }

        // Apply coupon
        applyCouponBtn.addEventListener('click', function() {
          const couponCode = couponCodeInput.value.trim();

          if (!couponCode) {
            showMessage('{{ __("messages.enter_coupon_code") }}', 'error');
            return;
          }

          applyCouponBtn.disabled = true;
          applyCouponBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> {{ __("messages.applying") }}...';

          // First, get coupon details to show preview
          fetch('{{ route("cart.coupon-details") }}', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
              coupon_code: couponCode
            })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              // Show coupon preview
              const coupon = data.coupon;
              let previewMessage = `{{ __("messages.coupon_found") }}: ${coupon.name} (${coupon.discount_description})`;

              if (coupon.minimum_amount) {
                previewMessage += ` - {{ __("messages.minimum_amount") }}: ${coupon.formatted_minimum_amount}`;
              }

              if (coupon.can_be_applied) {
                previewMessage += ` - {{ __("messages.discount_amount") }}: ${coupon.formatted_discount_amount}`;
                showMessage(previewMessage, 'info');

                // Apply the coupon
                setTimeout(() => {
                  applyCoupon(couponCode);
                }, 2000);
              } else {
                showMessage(`{{ __("messages.coupon_minimum_amount") }}: ${coupon.formatted_minimum_amount}`, 'error');
                applyCouponBtn.disabled = false;
                applyCouponBtn.innerHTML = '{{ __("messages.apply_coupon") }}';
              }
            } else {
              let errorMessage = data.message;
              if (data.details) {
                if (data.details.minimum_required && data.details.total_amount < data.details.minimum_required) {
                  errorMessage = `{{ __("messages.coupon_minimum_amount") }}: $${data.details.minimum_required}. {{ __("messages.current_total") }}: $${data.details.total_amount}`;
                }
              }
              showMessage(errorMessage, 'error');
              applyCouponBtn.disabled = false;
              applyCouponBtn.innerHTML = '{{ __("messages.apply_coupon") }}';
            }
          })
          .catch(error => {
            showMessage('{{ __("messages.error") }}', 'error');
            applyCouponBtn.disabled = false;
            applyCouponBtn.innerHTML = '{{ __("messages.apply_coupon") }}';
          });
        });

        function applyCoupon(couponCode) {
          fetch('{{ route("cart.apply-coupon") }}', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
              coupon_code: couponCode
            })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              showMessage(data.message, 'success');
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
              showMessage(errorMessage, 'error');
            }
          })
          .catch(error => {
            showMessage('{{ __("messages.error") }}', 'error');
          })
          .finally(() => {
            applyCouponBtn.disabled = false;
            applyCouponBtn.innerHTML = '{{ __("messages.apply_coupon") }}';
          });
        }

        // Remove coupon buttons
        @php
          $appliedCoupons = session('applied_coupons', []);
          $appliedCoupon = session('applied_coupon');
          $hasCoupons = !empty($appliedCoupons) || $appliedCoupon;
        @endphp

        @if($hasCoupons)
          const couponSection = document.querySelector('.coupon-section');

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

        function removeCoupon(couponCode = null) {
          const url = '{{ route("cart.remove-coupon") }}' + (couponCode ? '?coupon_code=' + couponCode : '');

          fetch(url, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              // Hide coupon section immediately
              const couponSection = document.querySelector('.coupon-section');
              if (couponSection) {
                couponSection.style.opacity = '0.3';
              }

              showMessage(data.message, 'success');
              setTimeout(() => {
                window.location.reload();
              }, 800);
            }
          });
        }

        function showMessage(message, type) {
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
    });
  </script>
</body>
</html>
