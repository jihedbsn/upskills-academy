<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
  @include('layouts.head')
  <link rel="stylesheet" href="{{ asset('assets/css/filter-custom.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/packages-custom.css') }}">
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
                  <h1 class="page-header__title">{{ $package->formatted_title }}</h1>
                </div>

                <div data-anim="slide-up delay-2">
                  <p class="page-header__text">{{ __('messages.package_details_description') }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Package Details -->
      <section class="layout-pt-md layout-pb-lg">
        <div class="container">
          <div class="row y-gap-30">
            <!-- Package Image -->
            <div class="col-lg-5">
              <div class="book-image-wrapper">
                <img src="{{ $package->image ? config('app.file_url') . 'uploads/package/' . $package->image : 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22300%22%3E%3Crect fill=%22%23e5e7eb%22 width=%22400%22 height=%22300%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 font-size=%2218%22 fill=%22%23999%22 text-anchor=%22middle%22 dominant-baseline=%22middle%22%3ENo Image Available%3C/text%3E%3C/svg%3E' }}"
                     alt="{{ $package->formatted_title }}"
                     class="w-1/1 rounded-8"
                     style="background-color: #f3f4f6;">
                <div class="book-badge paid">
                  <span>{{ __('messages.package') }}</span>
                </div>
              </div>
            </div>

            <!-- Package Info -->
            <div class="col-lg-7">
              <div class="book-info">
                <h2 class="text-32 fw-600 text-dark-1 mb-20">{{ $package->formatted_title }}</h2>

                @if(app()->getLocale() === 'ar' && $package->description_ar)
                  <p class="text-16 text-light-1 mb-30">{{ $package->description_ar }}</p>
                @elseif($package->description)
                  <p class="text-16 text-light-1 mb-30">{{ $package->description }}</p>
                @endif

                <div class="book-meta mb-30">
                  <div class="meta-item">
                    <div class="meta-label">{{ __('messages.courses') }}</div>
                    <div class="meta-value">{{ $package->active_products_count }} {{ __('messages.courses') }}</div>
                  </div>

                  @if($package->average_reduction > 0)
                    <div class="meta-item">
                      <div class="meta-label">{{ __('messages.average_discount') }}</div>
                      <div class="meta-value text-green-1">{{ number_format($package->average_reduction, 0) }}%</div>
                    </div>
                  @endif

                  <div class="meta-item">
                    <div class="meta-label">{{ __('messages.total_price') }}</div>
                    @if($package->savings_amount > 0)
                      <div class="meta-value">
                        <span style="text-decoration: line-through; color: #6b7280; font-weight: 500;">@price($package->total_price)</span>
                        <span style="color: #0071ae; font-weight: 700; margin-left: 6px;">@price($package->discounted_price)</span>
                        <span style="color: #e6b800; font-weight: 700; margin-left: 6px;">-{{ number_format($package->average_reduction, 0) }}% ({{ __('messages.save') }} @price($package->savings_amount))</span>
                      </div>
                    @else
                      <div class="meta-value" style="color: #0071ae; font-weight: 700;">@price($package->total_price)</div>
                    @endif
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="book-actions">
                  <button type="button" class="button -md -purple-1 text-white mr-15" onclick="addPackageToCart({{ $package->id }})">
                    <i class="icon icon-cart mr-10"></i>
                    {{ __('messages.purchase_package') }}
                  </button>

                  <a href="{{ route('checkout.index') }}" class="button -md -green-1 text-white mr-15">
                    <i class="icon icon-credit-card mr-10"></i>
                    {{ __('messages.proceed_to_checkout') }}
                  </a>
                </div>
              </div>
            </div>
          </div>

          <!-- Package Description -->
          @if((app()->getLocale() === 'ar' && $package->description_ar) || (app()->getLocale() !== 'ar' && $package->description))
            <div class="row mt-60">
              <div class="col-12">
                <div class="book-description">
                  <h3 class="text-24 fw-600 text-dark-1 mb-30">{{ __('messages.description') }}</h3>
                  <div class="text-16 text-light-1">
                    @if(app()->getLocale() === 'ar' && $package->description_ar)
                      {!! nl2br(e($package->description_ar)) !!}
                    @else
                      {!! nl2br(e($package->description)) !!}
                    @endif
                  </div>
                </div>
              </div>
            </div>
          @endif

          <!-- Included Courses -->
          @if(isset($products) && $products->count() > 0)
            <div class="row mt-60">
              <div class="col-12">
                <div class="book-description">
                  <h3 class="text-24 fw-600 text-dark-1 mb-30">{{ __('messages.included_courses') }} ({{ $products->count() }})</h3>

                  <div class="row y-gap-30">
                    @foreach($products as $index => $packageProduct)
                      <div class="col-lg-4 col-md-6">
                        @php
                          $discountType = $packageProduct->discount_type ?? 'percentage';
                          $discountValue = $discountType === 'fixed'
                            ? $packageProduct->fixed_discount
                            : $packageProduct->valeur_reduction;
                        @endphp
                        @include('filter.components.products', [
                          'course' => $packageProduct->product,
                          'package_discount' => $discountValue,
                          'package_discount_type' => $discountType,
                          'package_id' => $package->id
                        ])
                      </div>
                    @endforeach
                  </div>
                </div>
              </div>
            </div>
          @else
            <!-- No Courses Found -->
            <div class="row mt-60">
              <div class="col-12">
                <div class="book-description">
                  <h3 class="text-24 fw-600 text-dark-1 mb-30">{{ __('messages.included_courses') }}</h3>
                  <div class="text-center py-60">
                    <div class="icon-graduation-cap text-60 text-light-1 mb-20"></div>
                    <h4 class="text-18 fw-500 text-dark-1 mb-10">{{ __('messages.no_courses_in_package') }}</h4>
                    <p class="text-14 text-light-1">{{ __('messages.no_courses_in_package_description') }}</p>
                  </div>
                </div>
              </div>
            </div>
          @endif
        </div>
      </section>
    </div>
  </main>

  @include('layouts.footer')

  <script>
    function removePackageFromCart(packageId) {
      if (!confirm('{{ __("messages.remove_from_cart_confirm") }}')) {
        return;
      }

      // Send AJAX request to remove package
      const locale = '{{ app()->getLocale() }}';
      fetch(`/${locale}/cart/remove/package_${packageId}`, {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showToast('{{ __("messages.package_removed_from_cart") }}', 'success');

          // Update cart count in header if exists
          const cartBadge = document.querySelector('.cart-badge');
          if (cartBadge) {
            cartBadge.textContent = data.cart_count || 0;
            if (data.cart_count === 0) {
              cartBadge.style.display = 'none';
            }
          }
        } else {
          showToast('{{ __("messages.package_not_in_cart") }}', 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showToast('{{ __("messages.something_went_wrong") }}', 'error');
      });
    }

    function addPackageToCart(packageId) {
      const button = event.target;
      const originalText = button.innerHTML;

      // Show loading state
      button.innerHTML = '<i class="icon icon-spinner mr-10"></i>{{ __("messages.adding") }}...';
      button.disabled = true;

      // Send AJAX request
      const locale = '{{ app()->getLocale() }}';
      fetch(`/${locale}/cart/add-package/${packageId}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
          quantity: 1
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Success - show success toast
          showToast('{{ __("messages.package_added_successfully") }}', 'success');

          // Update cart count in header if exists
          let cartBadge = document.querySelector('.cart-badge');
          if (cartBadge) {
            cartBadge.textContent = data.cart_count;
          } else {
            // Create badge if it doesn't exist
            const cartIcon = document.querySelector('.js-cart-icon');
            if (cartIcon && data.cart_count > 0) {
              cartBadge = document.createElement('span');
              cartBadge.className = 'cart-badge';
              cartBadge.textContent = data.cart_count;
              cartIcon.appendChild(cartBadge);
            }
          }

          // Reset button after 1 second
          setTimeout(() => {
            button.innerHTML = originalText;
            button.disabled = false;
          }, 1000);
        } else {
          // Error - show error toast
          showToast('{{ __("messages.package_already_exists") }}', 'error');

          // Reset button after 1 second
          setTimeout(() => {
            button.innerHTML = originalText;
            button.disabled = false;
          }, 1000);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showToast('{{ __("messages.something_went_wrong") }}', 'error');

        // Reset button after 1 second
        setTimeout(() => {
          button.innerHTML = originalText;
          button.disabled = false;
        }, 1000);
      });
    }

    function showToast(message, type) {
      // Create toast container if it doesn't exist
      let toastContainer = document.getElementById('toast-container');
      if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.style.cssText = `
          position: fixed;
          top: 20px;
          right: 20px;
          z-index: 9999;
          display: flex;
          flex-direction: column;
          gap: 10px;
        `;
        document.body.appendChild(toastContainer);
      }

      // Create toast element
      const toast = document.createElement('div');
      toast.style.cssText = `
        background: ${type === 'success' ? '#10b981' : '#ef4444'};
        color: white;
        padding: 16px 24px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 300px;
        max-width: 400px;
        transform: translateX(100%);
        transition: transform 0.3s ease;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        font-size: 14px;
        font-weight: 500;
      `;

      // Add icon
      const icon = document.createElement('div');
      icon.innerHTML = type === 'success' ? '✓' : '✕';
      icon.style.cssText = `
        font-size: 18px;
        font-weight: bold;
      `;

      // Add message
      const messageDiv = document.createElement('div');
      messageDiv.textContent = message;
      messageDiv.style.cssText = `
        flex: 1;
        line-height: 1.4;
      `;

      // Add close button
      const closeBtn = document.createElement('button');
      closeBtn.innerHTML = '×';
      closeBtn.style.cssText = `
        background: none;
        border: none;
        color: white;
        font-size: 20px;
        font-weight: bold;
        cursor: pointer;
        padding: 0;
        margin-left: 8px;
        opacity: 0.7;
        transition: opacity 0.2s ease;
      `;
      closeBtn.onmouseover = () => closeBtn.style.opacity = '1';
      closeBtn.onmouseout = () => closeBtn.style.opacity = '0.7';
      closeBtn.onclick = () => removeToast(toast);

      // Assemble toast
      toast.appendChild(icon);
      toast.appendChild(messageDiv);
      toast.appendChild(closeBtn);

      // Add to container
      toastContainer.appendChild(toast);

      // Animate in
      setTimeout(() => {
        toast.style.transform = 'translateX(0)';
      }, 100);

      // Auto remove after 4 seconds
      setTimeout(() => {
        removeToast(toast);
      }, 4000);
    }

    function removeToast(toast) {
      toast.style.transform = 'translateX(100%)';
      setTimeout(() => {
        if (toast.parentNode) {
          toast.parentNode.removeChild(toast);
        }
      }, 300);
    }

    document.addEventListener('DOMContentLoaded', function() {
      // Debug package image
      const packageImage = document.querySelector('.book-image-wrapper img');
      if (packageImage) {
        console.log('Package image source:', packageImage.src);
        console.log('Package image alt:', packageImage.alt);

        // Check if image loads successfully
        packageImage.addEventListener('load', function() {
          console.log('✅ Package image loaded successfully:', this.src);
        });

        packageImage.addEventListener('error', function() {
          console.log('❌ Package image failed to load:', this.src);
        });
      }

      // Debug course images
      const courseImages = document.querySelectorAll('.coursesCard__image img');
      courseImages.forEach((img, index) => {
        console.log(`Course ${index + 1} image source:`, img.src);

        img.addEventListener('load', function() {
          console.log(`✅ Course ${index + 1} image loaded:`, this.src);
        });

        img.addEventListener('error', function() {
          console.log(`❌ Course ${index + 1} image failed:`, this.src);
        });
      });
    });
  </script>
</body>
</html>
