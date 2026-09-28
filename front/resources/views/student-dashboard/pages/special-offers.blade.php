@extends('student-dashboard.layouts.app')

@section('title', __('coupon.special_offers'))

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('student-dashboard.index') }}">{{ __('messages.dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('coupon.special_offers') }}</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">{{ __('coupon.special_offers') }}</h1>
            <p class="text-muted">{{ __('coupon.subtitle') }}</p>
        </div>
    </div>

    <!-- Coupons Grid -->
    <div class="row g-4">
        @forelse($coupons as $coupon)
        <div class="col-xl-4 col-lg-6 col-md-6">
            <div class="coupon-card">
                <div class="coupon-card-inner">
                    <!-- Top Section with Gradient Background -->
                    <div class="coupon-top">
                        <div class="coupon-pattern"></div>
                        <div class="coupon-content-top">
                            <div class="coupon-title">
                                <h3 class="text-white fw-600 mb-2">{{ $coupon->nom }}</h3>
                                @if($coupon->custom_message)
                                    <p class="text-white-70 small mb-0">{{ Str::limit($coupon->custom_message, 50) }}</p>
                                @endif
                            </div>

                            <!-- Discount Display -->
                            <div class="discount-circle">
                                @if($coupon->type === 'percentage')
                                    <div class="discount-amount h2 fw-bold text-white mb-0">{{ number_format($coupon->valeur, 0) }}%</div>
                                @else
                                    <div class="discount-amount h4 fw-bold text-white mb-0">@price($coupon->valeur)</div>
                                @endif
                                <div class="small text-white fw-500 text-uppercase">{{ __('coupon.off') }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Middle Section with Coupon Code -->
                    <div class="coupon-middle">
                        <div class="coupon-code-wrapper">
                            <label class="small text-muted fw-500 text-uppercase mb-2 d-block">
                                <i class="bi bi-ticket-perforated me-1"></i>{{ __('coupon.coupon_code') }}
                            </label>
                            <div class="code-display copy-code-area" data-code="{{ $coupon->code }}" title="{{ __('coupon.copy_code') }}">
                                <span class="code-value">{{ $coupon->code }}</span>
                                <div class="copy-icon-wrapper">
                                    <i class="bi bi-clipboard copy-icon"></i>
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
                                <i class="bi bi-clock text-primary mb-2 d-block"></i>
                                <div class="small text-muted mb-1">{{ __('coupon.valid_until') }}</div>
                                <div class="small fw-bold text-dark">{{ $coupon->date_fin->format('d M Y') }}</div>
                            </div>

                            @if($coupon->min_purchase_amount)
                            <div class="detail-box">
                                <i class="bi bi-cart text-primary mb-2 d-block"></i>
                                <div class="small text-muted mb-1">{{ __('coupon.minimum_purchase') }}</div>
                                <div class="small fw-bold text-dark">@price($coupon->min_purchase_amount)</div>
                            </div>
                            @endif

                            @if($coupon->usage_limit)
                            <div class="detail-box">
                                <i class="bi bi-people text-primary mb-2 d-block"></i>
                                <div class="small text-muted mb-1">{{ __('coupon.usage_limit') }}</div>
                                <div class="small fw-bold text-dark">{{ $coupon->usage_limit - $coupon->usage_count }} {{ __('coupon.remaining') }}</div>
                            </div>
                            @endif
                        </div>

                        <!-- Feature Badges -->
                        @if($coupon->auto_apply || $coupon->first_purchase_only || $coupon->is_stackable)
                        <div class="feature-badges mb-3">
                            @if($coupon->auto_apply)
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary">
                                <i class="bi bi-lightning-charge me-1"></i> {{ __('coupon.auto_apply_badge') }}
                            </span>
                            @endif

                            @if($coupon->first_purchase_only)
                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning">
                                <i class="bi bi-star me-1"></i> {{ __('coupon.new_customers_only') }}
                            </span>
                            @endif

                            @if($coupon->is_stackable)
                            <span class="badge bg-success bg-opacity-10 text-success border border-success">
                                <i class="bi bi-layers me-1"></i> {{ __('coupon.stackable_badge') }}
                            </span>
                            @endif
                        </div>
                        @endif

                        <!-- Apply Button -->
                        <button type="button" class="btn btn-primary w-100 apply-coupon-btn" data-code="{{ $coupon->code }}">
                            <span class="apply-btn-text">{{ __('coupon.apply_to_cart') }}</span>
                            <i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-tag text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                    </div>
                    <h5 class="mb-2 fw-bold">{{ __('coupon.no_coupons_available') }}</h5>
                    <p class="text-muted mb-4">{{ __('coupon.check_back_later') }}</p>
                    <a href="{{ route('courses.index') }}" class="btn btn-lg btn-primary">
                        <i class="bi bi-search me-2"></i> {{ __('coupon.browse_courses') }}
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if(method_exists($coupons, 'hasPages') && $coupons->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $coupons->links() }}
    </div>
    @endif
</div>

<style>
/* Main Card Container */
.coupon-card {
    position: relative;
    height: 100%;
    min-height: 420px;
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
    box-shadow: 0 12px 40px rgba(0, 113, 174, 0.2);
    border-color: rgba(0, 113, 174, 0.3);
}

/* Top Section - Gradient Header */
.coupon-top {
    background: linear-gradient(135deg, #0071ae 0%, #005a8a 100%);
    padding: 25px 20px;
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
}

.coupon-content-top {
    position: relative;
    z-index: 2;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
}

.coupon-title {
    flex: 1;
}

.text-white-70 {
    color: rgba(255, 255, 255, 0.7);
}

/* Discount Circle */
.discount-circle {
    width: 90px;
    height: 90px;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border: 3px solid rgba(255, 255, 255, 0.25);
    flex-shrink: 0;
}

/* Middle Section - Coupon Code */
.coupon-middle {
    background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);
    padding: 15px 20px;
    border-bottom: 2px dashed #e5e7eb;
}

.code-display {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #fff;
    border: 2px solid #0071ae;
    border-radius: 10px;
    padding: 12px 15px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.code-display:hover {
    border-color: #005a8a;
    box-shadow: 0 4px 12px rgba(0, 113, 174, 0.15);
    transform: translateY(-2px);
}

.code-display.copied {
    border-color: #198754;
    background: linear-gradient(to right, rgba(25, 135, 84, 0.05), rgba(25, 135, 84, 0.05));
}

.code-value {
    font-family: 'Courier New', Courier, monospace;
    font-size: 18px;
    font-weight: 700;
    color: #1a202c;
    letter-spacing: 2px;
    text-transform: uppercase;
    user-select: none;
    flex-grow: 1;
}

.copy-icon-wrapper {
    min-width: 36px;
    height: 36px;
    background: linear-gradient(135deg, #0071ae 0%, #005a8a 100%);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    padding: 0 10px;
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
    box-shadow: 0 4px 12px rgba(0, 113, 174, 0.4);
}

.copy-icon {
    color: #fff;
    font-size: 14px;
}

.code-display.copied .copy-icon-wrapper {
    background: linear-gradient(135deg, #198754 0%, #157347 100%);
}

/* Bottom Section - Details & Actions */
.coupon-bottom {
    padding: 20px;
    background: #fff;
    flex: 1;
    display: flex;
    flex-direction: column;
}

/* Details Grid */
.details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(90px, 1fr));
    gap: 10px;
    margin-bottom: 15px;
}

.detail-box {
    text-align: center;
    padding: 12px 8px;
    background: linear-gradient(135deg, rgba(0, 113, 174, 0.05) 0%, rgba(0, 90, 138, 0.05) 100%);
    border-radius: 10px;
    transition: all 0.3s ease;
}

.detail-box:hover {
    background: linear-gradient(135deg, rgba(0, 113, 174, 0.1) 0%, rgba(0, 90, 138, 0.1) 100%);
    transform: translateY(-2px);
}

/* Feature Badges */
.feature-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.feature-badges .badge {
    font-size: 0.7rem;
    padding: 0.4em 0.6em;
}

/* Apply Button */
.apply-coupon-btn {
    margin-top: auto;
    padding: 12px 20px;
    font-weight: 600;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.apply-coupon-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(0, 113, 174, 0.3);
}

.apply-coupon-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none !important;
}

.apply-coupon-btn.success {
    background: linear-gradient(135deg, #198754 0%, #157347 100%);
    border-color: #198754;
}

.apply-coupon-btn.error {
    background: linear-gradient(135deg, #dc3545 0%, #bb2d3b 100%);
    border-color: #dc3545;
}

/* Spinner icon animation */
.bi-arrow-clockwise {
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
        width: 80px;
        height: 80px;
    }

    .code-value {
        font-size: 14px;
        letter-spacing: 1px;
    }

    .details-grid {
        grid-template-columns: 1fr;
        gap: 8px;
    }

    .detail-box {
        padding: 10px 8px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const translations = {
        codeCopied: '@lang("coupon.code_copied")',
        copyCode: '@lang("coupon.copy_code")',
        applying: '@lang("Applying...")',
        applied: '@lang("Applied!")',
        failed: '@lang("Failed")',
        errorMsg: '@lang("An error occurred. Please try again.")'
    };

    // Copy code functionality
    const copyCodeAreas = document.querySelectorAll('.copy-code-area');

    copyCodeAreas.forEach(codeArea => {
        codeArea.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const code = this.getAttribute('data-code');
            const copyIcon = this.querySelector('.copy-icon');

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(code).then(() => {
                    showCopySuccess(this, copyIcon);
                }).catch(err => {
                    fallbackCopy(code, this, copyIcon);
                });
            } else {
                fallbackCopy(code, this, copyIcon);
            }
        });

        codeArea.style.cursor = 'pointer';
    });

    function showCopySuccess(element, icon) {
        element.classList.add('copied');
        icon.className = 'bi bi-check copy-icon';

        const copyText = element.querySelector('.copy-text');
        if (copyText) {
            const originalText = copyText.getAttribute('data-copy-text');
            const copiedText = copyText.getAttribute('data-copied-text');
            copyText.textContent = copiedText;

            setTimeout(() => {
                element.classList.remove('copied');
                icon.className = 'bi bi-clipboard copy-icon';
                copyText.textContent = originalText;
            }, 2000);
        } else {
            setTimeout(() => {
                element.classList.remove('copied');
                icon.className = 'bi bi-clipboard copy-icon';
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
            if (successful) {
                showCopySuccess(element, icon);
            } else {
                alert('Failed to copy code: ' + text);
            }
        } catch(err) {
            alert('Failed to copy code: ' + text);
        }

        document.body.removeChild(textarea);
    }

    // Apply coupon to cart functionality
    const applyButtons = document.querySelectorAll('.apply-coupon-btn');

    applyButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const code = this.getAttribute('data-code');
            const btn = this;
            const originalHTML = btn.innerHTML;

            // Disable button and show loading
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i><span class="ms-2">' + translations.applying + '</span>';

            // Store coupon code in session storage for cart page
            sessionStorage.setItem('coupon_to_apply', code);

            // Redirect to cart
            setTimeout(() => {
                window.location.href = '{{ route("cart.index") }}';
            }, 300);
        });
    });
});
</script>
@endsection
