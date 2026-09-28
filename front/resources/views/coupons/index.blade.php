@extends('layouts.app')

@section('content')

<!-- Page Title -->
<section class="page-header -type-1 mt-80">
  <div class="container">
    <div class="page-header__content">
      <div class="row justify-center text-center">
        <div class="col-auto">
          <div>
            <h1 class="page-header__title">{{ __('coupon.title') }}</h1>
          </div>
          <div>
            <p class="page-header__text">{{ __('coupon.subtitle') }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Coupons Section -->
<section class="layout-pt-md layout-pb-lg">
  <div class="container">
    <div class="row y-gap-30">

      @forelse($coupons as $coupon)
      <div class="col-lg-4 col-md-6">
        <div class="coupon-card">
          <!-- Card Inner Container -->
          <div class="coupon-card-inner">
            <!-- Top Section with Gradient Background -->
            <div class="coupon-top">
              <div class="coupon-pattern"></div>
              <div class="coupon-content-top">
                <div class="coupon-title">
                  <h3 class="text-white fw-600 text-18 mb-5">{{ $coupon->nom }}</h3>
                  @if($coupon->custom_message)
                    <p class="text-white-70 text-13 mb-0">{{ Str::limit($coupon->custom_message, 50) }}</p>
                  @endif
                </div>

                <!-- Discount Display -->
                <div class="discount-circle">
                  @if($coupon->type === 'percentage')
                    <div class="discount-amount text-50 fw-700 text-white lh-1">{{ number_format($coupon->valeur, 0) }}%</div>
                  @else
                    <div class="discount-amount text-40 fw-700 text-white lh-1">@price($coupon->valeur)</div>
                  @endif
                  <div class="text-12 text-white fw-500 mt-5 text-uppercase">{{ __('coupon.off') }}</div>
                </div>
              </div>
            </div>

            <!-- Middle Section with Coupon Code -->
            <div class="coupon-middle">
              <div class="coupon-code-wrapper">
                <label class="text-11 text-light-1 fw-500 text-uppercase mb-8">
                  <i class="icon-ticket mr-5"></i>{{ __('coupon.coupon_code') }}
                </label>
                <div class="code-display copy-code-area" data-code="{{ $coupon->code }}" title="{{ __('coupon.copy_code') }}">
                  <span class="code-value">{{ $coupon->code }}</span>
                  <div class="copy-icon-wrapper">
                    <i class="icon-copy copy-icon"></i>
                    <span class="copy-text" data-copy-text="{{ __('coupon.copy_code') }}" data-copied-text="{{ __('coupon.code_copied') }}">{{ __('coupon.copy_code') }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Bottom Section with Details -->
            <div class="coupon-bottom">
              <!-- Details Grid -->
              <div class="details-grid">
                <div class="detail-box">
                  <i class="icon-clock text-purple-1 text-16 mb-8"></i>
                  <div class="text-11 text-light-1 mb-3">{{ __('coupon.valid_until') }}</div>
                  <div class="text-13 fw-600 text-dark-1">{{ $coupon->date_fin->format('d M Y') }}</div>
                </div>

                @if($coupon->min_purchase_amount)
                <div class="detail-box">
                  <i class="icon-shopping-cart text-purple-1 text-16 mb-8"></i>
                  <div class="text-11 text-light-1 mb-3">{{ __('coupon.minimum_purchase') }}</div>
                  <div class="text-13 fw-600 text-dark-1">@price($coupon->min_purchase_amount)</div>
                </div>
                @endif

                @if($coupon->usage_limit)
                <div class="detail-box">
                  <i class="icon-users text-purple-1 text-16 mb-8"></i>
                  <div class="text-11 text-light-1 mb-3">{{ __('coupon.usage_limit') }}</div>
                  <div class="text-13 fw-600 text-dark-1">{{ $coupon->usage_limit - $coupon->usage_count }} {{ __('coupon.remaining') }}</div>
                </div>
                @endif
              </div>

              <!-- Feature Badges -->
              @if($coupon->auto_apply || $coupon->first_purchase_only || $coupon->is_stackable)
              <div class="feature-badges">
                @if($coupon->auto_apply)
                <span class="feature-badge badge-purple">
                  <i class="icon-lightning text-10"></i> {{ __('coupon.auto_apply_badge') }}
                </span>
                @endif

                @if($coupon->first_purchase_only)
                <span class="feature-badge badge-orange">
                  <i class="icon-star text-10"></i> {{ __('coupon.new_customers_only') }}
                </span>
                @endif

                @if($coupon->is_stackable)
                <span class="feature-badge badge-green">
                  <i class="icon-layers text-10"></i> {{ __('coupon.stackable_badge') }}
                </span>
                @endif
              </div>
              @endif

              <!-- Apply Button -->
              <button type="button" class="apply-btn apply-coupon-btn" data-code="{{ $coupon->code }}">
                <span class="apply-btn-text">{{ __('coupon.apply_to_cart') }}</span>
                <i class="icon-arrow-right ml-10"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
      @empty
      <div class="col-12">
        <div class="text-center py-60">
          <i class="icon-tag text-100 text-light-1 mb-20"></i>
          <h3 class="text-24 fw-500 mb-10">{{ __('coupon.no_coupons_available') }}</h3>
          <p class="text-light-1">{{ __('coupon.check_back_later') }}</p>
          <div class="mt-30">
            <a href="{{ route('courses.index') }}" class="button -md -purple-1 text-white">
              {{ __('coupon.browse_courses') }}
            </a>
          </div>
        </div>
      </div>
      @endforelse

    </div>

    <!-- Pagination -->
    @if(method_exists($coupons, 'hasPages') && $coupons->hasPages())
    <div class="row justify-center pt-60">
      <div class="col-auto">
        {{ $coupons->links() }}
      </div>
    </div>
    @endif
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
  console.log('Coupons page script loaded');

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

  const translations = {
    codeCopied: '@lang("coupon.code_copied")',
    copyCode: '@lang("coupon.copy_code")',
    applying: '@lang("Applying...")',
    applied: '@lang("Applied!")',
    failed: '@lang("Failed")',
    errorMsg: '@lang("An error occurred. Please try again.")'
  };

  // Copy code functionality - Make entire code area clickable
  const copyCodeAreas = document.querySelectorAll('.copy-code-area');
  console.log('Found copy areas:', copyCodeAreas.length);

  copyCodeAreas.forEach(codeArea => {
    codeArea.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();

      const code = this.getAttribute('data-code');
      console.log('Copying code:', code);

      const copyIcon = this.querySelector('.copy-icon');

      // Modern clipboard API
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(code).then(() => {
          console.log('Copied successfully via Clipboard API');
          showCopySuccess(this, copyIcon);
        }).catch(err => {
          console.error('Clipboard API failed:', err);
          fallbackCopy(code, this, copyIcon);
        });
      } else {
        // Fallback for older browsers
        console.log('Using fallback copy method');
        fallbackCopy(code, this, copyIcon);
      }
    });

    // Add hover effect
    codeArea.style.cursor = 'pointer';
  });

  function showCopySuccess(element, icon) {
    element.classList.add('copied');
    icon.className = 'icon-check copy-icon';

    // Change text to "Copied!" using translation
    const copyText = element.querySelector('.copy-text');
    if (copyText) {
      const originalText = copyText.getAttribute('data-copy-text');
      const copiedText = copyText.getAttribute('data-copied-text');
      copyText.textContent = copiedText;

      setTimeout(() => {
        element.classList.remove('copied');
        icon.className = 'icon-copy copy-icon';
        copyText.textContent = originalText;
      }, 2000);
    } else {
      setTimeout(() => {
        element.classList.remove('copied');
        icon.className = 'icon-copy copy-icon';
      }, 2000);
    }
  }

  function fallbackCopy(text, element, icon) {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.style.position = 'fixed';
    textarea.style.top = '-9999px';
    textarea.style.left = '-9999px';
    document.body.appendChild(textarea);
    textarea.focus();
    textarea.select();

    try {
      const successful = document.execCommand('copy');
      console.log('Fallback copy result:', successful);
      if (successful) {
        showCopySuccess(element, icon);
      } else {
        alert('Failed to copy code: ' + text);
      }
    } catch(err) {
      console.error('Fallback copy failed:', err);
      alert('Failed to copy code: ' + text);
    }

    document.body.removeChild(textarea);
  }

  // Apply coupon to cart functionality
  const applyButtons = document.querySelectorAll('.apply-coupon-btn');
  console.log('Found apply buttons:', applyButtons.length);

  applyButtons.forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();

      const code = this.getAttribute('data-code');
      console.log('Applying coupon:', code);

      const btn = this;
      const originalHTML = btn.innerHTML;

      // Disable button and show loading
      btn.disabled = true;
      btn.innerHTML = '<i class="icon-spinner"></i><span class="ml-10">' + translations.applying + '</span>';

      // Store coupon code in session storage for cart page
      sessionStorage.setItem('coupon_to_apply', code);
      console.log('Stored in sessionStorage:', code);

      // Redirect to cart immediately
      setTimeout(() => {
        console.log('Redirecting to cart...');
        window.location.href = '{{ route("cart.index") }}';
      }, 300);
    });
  });
});
</script>

@endsection

@section('styles')
<style>
/* Main Card Container */
.coupon-card {
  position: relative;
  height: 100%;
  min-height: 480px;
}

.coupon-card-inner {
  background: #fff;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  height: 100%;
  display: flex;
  flex-direction: column;
  border: 2px solid transparent;
}

.coupon-card-inner:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 40px rgba(0, 87, 166, 0.2);
  border-color: rgba(0, 87, 166, 0.3);
}

/* Top Section - Gradient Header */
.coupon-top {
  background: linear-gradient(135deg, #0057A6 0%, #004080 100%);
  padding: 30px 25px;
  position: relative;
  overflow: hidden;
}

/* Animated Pattern Background */
.coupon-pattern {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-image:
    radial-gradient(circle at 20% 80%, rgba(255, 255, 255, 0.08) 0%, transparent 50%),
    radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.08) 0%, transparent 50%),
    radial-gradient(circle at 40% 40%, rgba(255, 255, 255, 0.04) 0%, transparent 50%);
  animation: patternMove 20s ease-in-out infinite;
}

@keyframes patternMove {
  0%, 100% { transform: translate(0, 0) scale(1); }
  50% { transform: translate(10px, 10px) scale(1.05); }
}

.coupon-content-top {
  position: relative;
  z-index: 2;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 20px;
}

.coupon-title {
  flex: 1;
}

.text-white-70 {
  color: rgba(255, 255, 255, 0.7);
}

/* Discount Circle */
.discount-circle {
  width: 100px;
  height: 100px;
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(10px);
  border-radius: 50%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  border: 3px solid rgba(255, 255, 255, 0.25);
  flex-shrink: 0;
  animation: float 3s ease-in-out infinite;
}

@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-5px); }
}

.discount-amount {
  line-height: 1;
}

/* Middle Section - Coupon Code */
.coupon-middle {
  background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);
  padding: 20px 25px;
  border-bottom: 2px dashed #e5e7eb;
}

.coupon-code-wrapper label {
  display: block;
  letter-spacing: 0.5px;
  display: flex;
  align-items: center;
}

.code-display {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #fff;
  border: 2px solid #0057A6;
  border-radius: 12px;
  padding: 15px 20px;
  margin-top: 8px;
  transition: all 0.3s ease;
  cursor: pointer;
  position: relative;
}

.code-display:hover {
  border-color: #004080;
  box-shadow: 0 4px 12px rgba(0, 87, 166, 0.15);
  transform: translateY(-2px);
}

.code-display.copied {
  border-color: #10b981;
  background: linear-gradient(to right, rgba(16, 185, 129, 0.05), rgba(5, 150, 105, 0.05));
}

.code-value {
  font-family: 'Courier New', Courier, monospace;
  font-size: 20px;
  font-weight: 700;
  color: #1a202c;
  letter-spacing: 2px;
  text-transform: uppercase;
  user-select: none;
  flex-grow: 1;
}

.copy-icon-wrapper {
  width: auto;
  min-width: 38px;
  height: 38px;
  background: linear-gradient(135deg, #0057A6 0%, #004080 100%);
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 0 12px;
  transition: all 0.3s ease;
  flex-shrink: 0;
}

.copy-text {
  color: #fff;
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  display: none;
  white-space: nowrap;
}

@media (min-width: 768px) {
  .copy-text {
    display: block;
  }
}

.code-display:hover .copy-icon-wrapper {
  transform: scale(1.05);
  box-shadow: 0 4px 12px rgba(0, 87, 166, 0.4);
}

.code-display:active .copy-icon-wrapper {
  transform: scale(0.95);
}

.copy-icon {
  color: #fff;
  font-size: 16px;
}

.code-display.copied .copy-icon-wrapper {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.code-display.copied .copy-text {
  content: 'Copied!';
}

/* Bottom Section - Details & Actions */
.coupon-bottom {
  padding: 25px;
  background: #fff;
  flex: 1;
  display: flex;
  flex-direction: column;
}

/* Details Grid */
.details-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
  gap: 15px;
  margin-bottom: 20px;
}

.detail-box {
  text-align: center;
  padding: 15px 10px;
  background: linear-gradient(135deg, rgba(0, 87, 166, 0.05) 0%, rgba(0, 64, 128, 0.05) 100%);
  border-radius: 12px;
  transition: all 0.3s ease;
}

.detail-box:hover {
  background: linear-gradient(135deg, rgba(0, 87, 166, 0.1) 0%, rgba(0, 64, 128, 0.1) 100%);
  transform: translateY(-3px);
}

/* Feature Badges */
.feature-badges {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 20px;
  min-height: 28px;
}

.feature-badge {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 600;
  transition: all 0.2s ease;
  white-space: nowrap;
}

.feature-badge:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.badge-purple {
  background: rgba(0, 87, 166, 0.1);
  color: #0057A6;
}

.badge-orange {
  background: rgba(251, 146, 60, 0.1);
  color: #f97316;
}

.badge-green {
  background: rgba(16, 185, 129, 0.1);
  color: #10b981;
}

/* Apply Button */
.apply-btn {
  width: 100%;
  background: linear-gradient(135deg, #0057A6 0%, #004080 100%);
  color: #fff;
  border: none;
  border-radius: 12px;
  padding: 16px 24px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  transition: all 0.3s ease;
  margin-top: auto;
  position: relative;
  overflow: hidden;
}

.apply-btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.5s;
}

.apply-btn:hover::before {
  left: 100%;
}

.apply-btn:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 20px rgba(0, 87, 166, 0.4);
}

.apply-btn:active {
  transform: translateY(-1px);
}

.apply-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none !important;
}

.apply-btn.success {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.apply-btn.error {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
}

/* Responsive Design */
@media (max-width: 991px) {
  .coupon-card {
    min-height: 460px;
  }

  .discount-circle {
    width: 90px;
    height: 90px;
  }

  .discount-amount {
    font-size: 36px !important;
  }
}

/* Copy Button Pulse Animation */
@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.7; }
}

.code-display.copied .copy-icon-wrapper {
  animation: pulse 0.5s ease-in-out;
}

/* Spinner icon animation */
.icon-spinner {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

@media (max-width: 575px) {
  .coupon-content-top {
    flex-direction: column;
    text-align: center;
  }

  .discount-circle {
    width: 100px;
    height: 100px;
  }

  .code-value {
    font-size: 16px;
    letter-spacing: 1px;
  }

  .details-grid {
    grid-template-columns: 1fr;
    gap: 10px;
  }

  .detail-box {
    padding: 12px 10px;
  }
}

/* Loading Animation */
@keyframes spin {
  to { transform: rotate(360deg); }
}

.icon-spinner {
  animation: spin 1s linear infinite;
}
</style>
@endsection
