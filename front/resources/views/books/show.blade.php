<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
  @include('layouts.head')
  <link rel="stylesheet" href="{{ asset('assets/css/books-custom.css') }}">
  <style>
    /* Toast Notification Styles */
    .toast-container {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 9999;
      max-width: 350px;
    }

    .toast {
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      margin-bottom: 10px;
      padding: 16px 20px;
      display: flex;
      align-items: center;
      transform: translateX(100%);
      transition: transform 0.3s ease;
      border-left: 4px solid #6366f1;
    }

    .toast.show {
      transform: translateX(0);
    }

    .toast.success {
      border-left-color: #10b981;
    }

    .toast.error {
      border-left-color: #ef4444;
    }

    .toast-icon {
      margin-right: 12px;
      font-size: 20px;
    }

    .toast.success .toast-icon {
      color: #10b981;
    }

    .toast.error .toast-icon {
      color: #ef4444;
    }

    .toast-content {
      flex: 1;
    }

    .toast-title {
      font-weight: 600;
      font-size: 14px;
      margin-bottom: 4px;
      color: #1f2937;
    }

    .toast-message {
      font-size: 13px;
      color: #6b7280;
    }

    .toast-close {
      background: none;
      border: none;
      color: #9ca3af;
      cursor: pointer;
      font-size: 18px;
      padding: 0;
      margin-left: 12px;
    }

    .toast-close:hover {
      color: #6b7280;
    }

    /* RTL Support */
    [dir="rtl"] .toast-container {
      right: auto;
      left: 20px;
    }

    [dir="rtl"] .toast {
      transform: translateX(-100%);
    }

    [dir="rtl"] .toast.show {
      transform: translateX(0);
    }

    [dir="rtl"] .toast-icon {
      margin-right: 0;
      margin-left: 12px;
    }

    [dir="rtl"] .toast-close {
      margin-left: 0;
      margin-right: 12px;
    }
  </style>
</head>
<body class="preloader-visible" data-barba="wrapper">
  <!-- Toast Container -->
  <div class="toast-container" id="toastContainer"></div>

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
                  <h1 class="page-header__title">{{ $book->titre }}</h1>
                </div>

                <div data-anim="slide-up delay-2">
                  <p class="page-header__text">{{ __('messages.book_details') }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Book Details -->
      <section class="layout-pt-md layout-pb-lg">
        <div class="container">
          <div class="row y-gap-30">
            <!-- Book Image -->
            <div class="col-lg-5">
              <div class="book-image-wrapper">
                <img src="{{ $book->image_url }}" alt="{{ $book->titre }}" class="w-1/1 rounded-8">
                @if($book->prix > 0)
                  <div class="book-badge paid">
                    <span>{{ __('messages.paid') }}</span>
                  </div>
                @else
                  <div class="book-badge free">
                    <span>{{ __('messages.free') }}</span>
                  </div>
                @endif
              </div>
            </div>

            <!-- Book Info -->
            <div class="col-lg-7">
              <div class="book-info">
                <h2 class="text-32 fw-600 text-dark-1 mb-20">{{ $book->titre }}</h2>

                @if($book->description_short)
                  <p class="text-16 text-light-1 mb-30">{{ $book->description_short }}</p>
                @endif

                <div class="book-meta mb-30">
                  <div class="row y-gap-20">
                    <div class="col-sm-6">
                      <div class="meta-item">
                        <div class="meta-label">{{ __('messages.price') }}</div>
                        <div class="meta-value">
                          @if($book->prix > 0)
                            <span class="text-24 fw-600 text-purple-1">@price($book->prix)</span>
                          @else
                            <span class="text-24 fw-600 text-green-1">{{ __('messages.free') }}</span>
                          @endif
                        </div>
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="meta-item">
                        <div class="meta-label">{{ __('messages.published') }}</div>
                        <div class="meta-value">{{ $book->created_at->format('M d, Y') }}</div>
                      </div>
                    </div>

                    @if($book->file)
                      <div class="col-sm-6">
                        <div class="meta-item">
                          <div class="meta-label">{{ __('messages.file_type') }}</div>
                          <div class="meta-value">{{ strtoupper(pathinfo($book->file, PATHINFO_EXTENSION)) }}</div>
                        </div>
                      </div>
                    @endif
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="book-actions">
                  @if($book->prix > 0)
                    <button type="button" class="button -md -purple-1 text-white add-to-cart-btn" data-book-id="{{ $book->id }}" data-book-title="{{ $book->titre }}">
                      <i class="icon icon-cart mr-10"></i>
                      {{ __('messages.add_to_cart') }}
                    </button>
                  @else
                    <a href="{{ $book->file_url }}" class="button -md -green-1 text-white" download>
                      <i class="icon icon-download mr-10"></i>
                      {{ __('messages.download_book') }}
                    </a>
                  @endif

                  @if($book->summary)
                    <a href="{{ $book->summary_url }}" class="button -md -outline-purple-1 text-purple-1" download>
                      <i class="icon icon-file-text mr-10"></i>
                      {{ __('messages.download_summary') }}
                    </a>
                  @endif
                </div>
              </div>
            </div>
          </div>

          <!-- Book Description -->
          @if($book->description)
            <div class="row mt-60">
              <div class="col-12">
                <div class="book-description">
                  <h3 class="text-24 fw-600 text-dark-1 mb-30">{{ __('messages.description') }}</h3>
                  <div class="text-16 text-light-1">
                    {!! $book->description !!}
                  </div>
                </div>
              </div>
            </div>
          @endif

          <!-- Related Books -->
          @if($relatedBooks->count() > 0)
            <div class="row mt-60">
              <div class="col-12">
                <h3 class="text-24 fw-600 text-dark-1 mb-30">{{ __('messages.related_books') }}</h3>
                <div class="row y-gap-30">
                  @foreach($relatedBooks as $relatedBook)
                    <div class="col-lg-3 col-md-6">
                      <a href="{{ route('books.show', $relatedBook) }}" class="coursesCard -type-1 book-card-link">
                        <div class="relative">
                          <div class="coursesCard__image overflow-hidden rounded-8">
                            <img class="w-1/1" src="{{ $relatedBook->image_url }}" alt="{{ $relatedBook->titre }}">
                            <div class="coursesCard__image_overlay rounded-8"></div>
                          </div>
                          @if($relatedBook->prix > 0)
                            <div class="d-flex justify-between py-10 px-10 absolute-full-center z-3">
                              <div>
                                <div class="px-15 rounded-200 bg-purple-1">
                                  <span class="text-11 lh-1 uppercase fw-500 text-white">{{ __('messages.paid') }}</span>
                                </div>
                              </div>
                            </div>
                          @else
                            <div class="d-flex justify-between py-10 px-10 absolute-full-center z-3">
                              <div>
                                <div class="px-15 rounded-200 bg-green-1">
                                  <span class="text-11 lh-1 uppercase fw-500 text-dark-1">{{ __('messages.free') }}</span>
                                </div>
                              </div>
                            </div>
                          @endif
                        </div>
                        <div class="h-100 pt-15">
                          <div class="text-17 lh-15 fw-500 text-dark-1 mt-10">{{ $relatedBook->titre }}</div>
                          @if($relatedBook->description_short)
                            <p class="text-14 lh-1 text-light-1 mt-10">{{ Str::limit($relatedBook->description_short, 100) }}</p>
                          @endif
                          <div class="coursesCard-footer">
                            <div class="coursesCard-footer__price">
                              @if($relatedBook->prix > 0)
                                <div>@price($relatedBook->prix)</div>
                              @else
                                <div class="text-green-1">{{ __('messages.free') }}</div>
                              @endif
                            </div>
                          </div>
                        </div>
                      </a>
                    </div>
                  @endforeach
                </div>
              </div>
            </div>
          @endif

          <!-- Back to Library -->
          <div class="row mt-60">
            <div class="col-12 text-center">
              <a href="{{ route('books.index') }}" class="button -md -outline-purple-1 text-purple-1">
                <i class="icon icon-arrow-left mr-10"></i>
                {{ __('messages.back_to_library') }}
              </a>
            </div>
          </div>
        </div>
      </section>
    </div>
  </main>

  @include('layouts.footer')

  <script>
    // Toast Notification System
    function showToast(type, title, message, duration = 4000) {
      const container = document.getElementById('toastContainer');
      const toast = document.createElement('div');
      toast.className = `toast ${type}`;

      const icon = type === 'success' ? '✓' : '✕';

      toast.innerHTML = `
        <div class="toast-icon">${icon}</div>
        <div class="toast-content">
          <div class="toast-title">${title}</div>
          <div class="toast-message">${message}</div>
        </div>
        <button class="toast-close" onclick="this.parentElement.remove()">×</button>
      `;

      container.appendChild(toast);

      // Show animation
      setTimeout(() => {
        toast.classList.add('show');
      }, 100);

      // Auto remove
      setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
          if (toast.parentElement) {
            toast.remove();
          }
        }, 300);
      }, duration);
    }

    // Add to Cart functionality
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
      button.addEventListener('click', function() {
        const bookId = this.dataset.bookId;
        const bookTitle = this.dataset.bookTitle;

        // Show loading state
        const originalText = this.innerHTML;
        this.innerHTML = '<i class="icon icon-spinner mr-10"></i>{{ __("messages.adding") }}...';
        this.disabled = true;

        // Send AJAX request
        const locale = '{{ app()->getLocale() }}';
        fetch(`/${locale}/cart/add/${bookId}`, {
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
        .then(response => {
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          return response.json();
        })
        .then(data => {
          if (data.success) {
            showToast('success', '{{ __("messages.success") }}', data.message);

            // Update cart count in header if it exists
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
          } else {
            showToast('error', '{{ __("messages.error") }}', data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          console.error('Error details:', {
            message: error.message,
            stack: error.stack
          });
          showToast('error', '{{ __("messages.error") }}', '{{ __("messages.something_went_wrong") }}');
        })
        .finally(() => {
          // Restore button state
          this.innerHTML = originalText;
          this.disabled = false;
        });
      });
    });
  </script>
</body>
</html>
