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
                  <h1 class="page-header__title">{{ __('messages.packages') }}</h1>
                </div>

                <div data-anim="slide-up delay-2">
                  <p class="page-header__text">{{ __('messages.packages_description') }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Packages Content -->
      <section class="layout-pt-md layout-pb-lg">
        <div class="container">
          <!-- Header with Count -->
          <div class="row y-gap-20 justify-between items-center mb-30">
            <div class="col-auto">
              <h3 class="text-24 fw-600 text-dark-1">{{ __('messages.packages') }} ({{ $packages->count() }})</h3>
            </div>
          </div>

          <!-- Packages Grid -->
          @if($packages->count() > 0)
            <div class="row y-gap-30">
              @foreach($packages as $index => $package)
                <div class="col-lg-4 col-md-6">
                  <a href="{{ route('packages.show', $package->id) }}"
                     data-anim-child="slide-up delay-{{ $index + 1 }}"
                     class="packageCard"
                     data-package-id="{{ $package->id }}"
                     style="display: block; text-decoration: none; color: inherit;">
                    <div class="packageCard__image overflow-hidden">
                      <img class="w-1/1"
                           src="{{ $package->image ? config('app.file_url') . 'uploads/package/' . $package->image : 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22220%22%3E%3Crect fill=%22%23ffffff%22 width=%22400%22 height=%22220%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 font-size=%2216%22 fill=%22%23999%22 text-anchor=%22middle%22 dominant-baseline=%22middle%22%3ENo Image%3C/text%3E%3C/svg%3E' }}"
                           alt="{{ $package->title }}">
                      <div class="packageCard__image_overlay rounded-8"></div>

                      {{-- <!-- Package Badge -->
                      <div class="package-card-badge">
                        <span class="text-11 lh-1 uppercase fw-500 text-white">{{ __('messages.package') }}</span>
                      </div> --}}
                    </div>

                    <div class="packageCard__content">
                      <h3 class="packageCard__title">{{ $package->formatted_title }}</h3>
                      <p class="packageCard__description">
                        @if(app()->getLocale() === 'ar' && $package->description_ar)
                          {{ Str::limit($package->description_ar, 120) }}
                        @elseif($package->description)
                          {{ Str::limit($package->description, 120) }}
                        @endif
                      </p>

                      <!-- Package Meta -->
                      <div class="packageCard__meta">
                        <div class="packageCard__meta-item">
                          <div class="icon-play"></div>
                          <span>{{ $package->active_products_count }} {{ __('messages.courses') }}</span>
                        </div>
                        @if($package->average_reduction > 0)
                          <div class="packageCard__meta-item">
                            <div class="icon-percent"></div>
                            <span>{{ number_format($package->average_reduction, 0) }}% {{ __('messages.discount') }}</span>
                          </div>
                        @endif
                      </div>

                      <!-- Package Price -->
                      <div class="packageCard-footer">
                        <div class="packageCard-footer__price">
                          @if($package->average_reduction > 0)
                            <div class="price-display">
                              <div class="price-original">@price($package->total_price)</div>
                              <div class="price-discounted">@price($package->discounted_price)</div>
                              <div class="price-savings">
                                {{ __('messages.save') }} @price($package->savings_amount)
                              </div>
                            </div>
                          @else
                            <div class="price-single">@price($package->total_price)</div>
                          @endif
                        </div>
                      </div>
                    </div>
                  </a>
                </div>
              @endforeach
            </div>
          @else
            <!-- No Packages Found -->
            <div class="text-center py-80">
              <div class="icon-graduation-cap text-80 text-light-1 mb-30"></div>
              <h3 class="text-24 fw-600 text-dark-1 mb-15">{{ __('messages.no_packages_found') }}</h3>
              <p class="text-16 text-light-1 mb-30">{{ __('messages.no_packages_description') }}</p>
            </div>
          @endif
        </div>
      </section>
    </div>
  </main>

  @include('layouts.footer')

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Initialize animations
      const animElements = document.querySelectorAll('[data-anim-child]');
      animElements.forEach((element, index) => {
        setTimeout(() => {
          element.classList.add('animated');
        }, index * 100);
      });

      // Force package cards to be visible and clickable
      const packageCards = document.querySelectorAll('.packageCard');
      packageCards.forEach(card => {
        card.style.display = 'block';
        card.style.visibility = 'visible';
        card.style.opacity = '1';
        card.style.backgroundColor = '#fff';
        card.style.cursor = 'pointer';
        card.style.pointerEvents = 'auto';

        // Add click event listener as backup
        card.addEventListener('click', function(e) {
          console.log('Package card clicked!', this.getAttribute('href'));
          e.preventDefault();
          const href = this.getAttribute('href');
          if (href) {
            window.location.href = href;
          }
        });

        // Add hover effect
        card.addEventListener('mouseenter', function() {
          console.log('Mouse entered package card:', this.getAttribute('data-package-id'));
          this.style.transform = 'translateY(-8px)';
          this.style.boxShadow = '0 15px 40px rgba(0, 0, 0, 0.15)';
          this.style.borderColor = '#3554d1';
        });

        card.addEventListener('mouseleave', function() {
          this.style.transform = 'translateY(0)';
          this.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.08)';
          this.style.borderColor = '#f0f0f0';
        });
      });

      console.log('Package cards initialized:', packageCards.length);
      console.log('Package cards clickable:', packageCards.length > 0);
    });
  </script>
</body>
</html>
