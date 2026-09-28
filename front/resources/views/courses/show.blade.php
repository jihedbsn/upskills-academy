<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
  @include('layouts.head')

  <style>
    /* Fix instructor name spacing in RTL */
    [dir="rtl"] .instructor-inline {
      margin-left: 0 !important;
      margin-right: 15px !important;
    }

    [dir="rtl"] .d-flex.items-center.pt-20 {
      gap: 15px;
    }

    /* Fix coach/trainer section spacing in RTL */
    [dir="rtl"] .coach-info {
      margin-left: 0 !important;
      margin-right: 15px !important;
    }

    [dir="rtl"] .coach-image {
      margin-left: 0 !important;
      margin-right: 15px !important;
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
      @if(session('error') || session('warning') || session('info') || session('success'))
      <section class="layout-pt-sm layout-pb-sm">
        <div class="container">
          @if(session('error'))
            <div class="alert alert-danger mb-20" role="alert">
              {{ session('error') }}
            </div>
          @endif

          @if(session('warning'))
            <div class="alert alert-warning mb-20" role="alert">
              {{ session('warning') }}
            </div>
          @endif

          @if(session('info'))
            <div class="alert alert-info mb-20" role="alert">
              {{ session('info') }}
            </div>
          @endif

          @if(session('success'))
            <div class="alert alert-success mb-20" role="alert">
              {{ session('success') }}
            </div>
          @endif
        </div>
      </section>
      @endif

      <!-- Breadcrumbs -->
      <section class="layout-pt-sm layout-pb-sm bg-light-6">
        <div class="container">
          <div class="row y-gap-30 justify-between items-center">
            <div class="col-auto">
              <div class="d-flex items-center h-100 fw-500">
                <div class="breadcrumbs__content">
                  <div class="breadcrumbs__item">
                    <a href="{{ route('accueil.index') }}">{{ __('messages.home') }}</a>
                  </div>
                  <div class="breadcrumbs__item">
                    <a href="{{ route('filter.index') }}">{{ __('messages.courses') }}</a>
                  </div>
                  <div class="breadcrumbs__item">
                    <span class="us-crumb-current">{{ $product->current_variation ? $product->current_variation->name : $product->titre }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Course Header -->
      <div>
        <section class="page-header -type-5 bg-light-6">
          <div class="page-header__bg">
            <div class="bg-image js-lazy" data-bg="{{ asset('assets/img/event-single/bg.png') }}"></div>
          </div>

          <div class="container">
            <div class="page-header__content pt-90 pb-90">
              <div class="row y-gap-30 relative">
                <div class="col-xl-7 col-lg-8">
                  <div class="d-flex x-gap-15 y-gap-10 pb-20">
                    @if($product->prix > 0)
                      <div>
                        <div class="badge px-15 py-8 text-11 bg-purple-1 text-white fw-400">{{ __('messages.paid') }}</div>
                      </div>
                    @else
                      <div>
                        <div class="badge px-15 py-8 text-11 bg-green-1 text-dark-1 fw-400">{{ __('messages.free') }}</div>
                      </div>
                    @endif
                    <div>
                      <div class="badge px-15 py-8 text-11 bg-yellow-1 text-white fw-400">{{ __('messages.popular') }}</div>
                    </div>
                    @if($product->category)
                      <div>
                        <div class="badge px-15 py-8 text-11 bg-blue-1 text-white fw-400">{{ $product->category->titre }}</div>
                      </div>
                    @endif
                  </div>

                  <div data-anim="slide-up delay-1">
                    <h1 class="text-30 lh-14 pr-60 lg:pr-0">
                      {{ $product->current_variation ? $product->current_variation->name : $product->titre }}
                    </h1>
                  </div>



                  <div class="d-flex x-gap-30 y-gap-10 items-center flex-wrap pt-20">
                    @php
                      $averageRating = $product->getAverageRating();
                      $ratingsCount = $product->getRatingsCount();
                      $fullStars = floor($averageRating);
                      $hasHalfStar = ($averageRating - $fullStars) >= 0.5;
                    @endphp

                    @if($ratingsCount > 0)
                    <div class="d-flex items-center">
                      <div class="text-14 lh-1 text-yellow-1 mr-10">{{ number_format($averageRating, 1) }}</div>
                      <div class="d-flex x-gap-5 items-center">
                        @for($i = 1; $i <= 5; $i++)
                          @if($i <= $fullStars)
                            <div class="icon-star text-11 text-yellow-1"></div>
                          @elseif($i == $fullStars + 1 && $hasHalfStar)
                            <div class="icon-star text-11 text-yellow-1" style="opacity: 0.5;"></div>
                          @else
                            <div class="icon-star text-11 text-light-1"></div>
                          @endif
                        @endfor
                      </div>
                      <div class="text-14 lh-1 text-light-1 ml-10">({{ $ratingsCount }})</div>
                    </div>
                    @endif

                    @if($product->lessons_count)
                      <div class="d-flex items-center text-light-1">
                        <div class="icon icon-person-3 text-13"></div>
                        <div class="text-14 ml-8">{{ $product->lessons_count }} {{ __('messages.lessons') }}</div>
                      </div>
                    @endif

                    @if($product->duration)
                      <div class="d-flex items-center text-light-1">
                        <div class="icon icon-wall-clock text-13"></div>
                        <div class="text-14 ml-8">{{ $product->duration }}</div>
                      </div>
                    @endif
                  </div>

                  @if($product->teacher)
                    <div class="d-flex items-center pt-20">
                      <img src="{{ $product->teacher->image_url }}" alt="{{ $product->teacher->full_name }}" class="size-50 rounded-full object-cover">
                      <div class="text-14 lh-1 ml-10 instructor-inline">{{ $product->teacher->full_name }}</div>
                    </div>
                  @endif
                </div>


              </div>
            </div>
          </div>
        </section>

                 <!-- Course Content -->
         <section class="layout-pt-md layout-pb-md">
          <div class="container">
            <div class="row y-gap-30 us-course-row">
              <div class="col-lg-8">
                @php
                  $isExpired = isset($enrollment) && $enrollment && $enrollment->is_expired;
                @endphp

                @if($isExpired)
                  <!-- Course Expired Message -->
                  <div class="bg-white rounded-16 shadow-3 border-light overflow-hidden">
                    <!-- Header Section -->
                    <div class="py-40 px-40 text-center" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);">
                      <h2 class="text-30 fw-700 text-dark-1 mb-15">{{ __('messages.course_expired') }}</h2>
                      <p class="text-17 text-dark-2 lh-16 mb-0 max-w-400 mx-auto">{{ __('messages.course_access_expired') }}</p>
                    </div>

                    <!-- Action Section -->
                    <div class="py-35 px-40 text-center">
                      <div class="d-flex justify-center flex-wrap" style="gap: 30px;">
                        <a href="{{ route('student-dashboard.course-extensions.checkout', ['product' => $product->id]) }}" class="button -md text-white px-30 py-12" style="background-color: #093162; border-radius: 8px; box-shadow: 0 4px 15px rgba(9, 49, 98, 0.3); min-width: 180px;">
                          <i class="icon-refresh mr-10"></i>
                          {{ __('messages.extend_subscription') }}
                        </a>
                        <a href="{{ route('courses.index') }}" class="button -md -outline-dark-1 text-dark-1 px-30 py-12" style="border-radius: 8px; min-width: 180px;">
                          <i class="icon-arrow-left mr-10"></i>
                          {{ __('messages.browse_courses') }}
                        </a>
                      </div>
                    </div>
                  </div>
                @else
                  <!-- Navigation Tabs -->
                  <div class="nav-tabs-wrapper">
                    <div class="nav-tabs">
                      <button class="nav-tab active" data-tab="course-information">
                        {{ __('messages.course_information') }}
                      </button>
                      <button class="nav-tab" data-tab="course-of-study">
                        {{ __('messages.course_of_study') }}
                      </button>
                      <button class="nav-tab" data-tab="discussion">
                        {{ __('messages.discussion') }}
                      </button>
                      <button class="nav-tab" data-tab="exams">
                        {{ __('messages.exams_tab') }}
                      </button>
                      <button class="nav-tab" data-tab="coach">
                        {{ __('messages.coach') }}
                      </button>
                      @if($enrollment && $enrollment->is_active)
                      <button class="nav-tab" data-tab="course-schedule">
                        {{ __('messages.course_schedule') }}
                      </button>
                      @endif
                      <button class="nav-tab" data-tab="course-ratings">
                        {{ __('messages.course_ratings') }}
                      </button>
                    </div>
                  </div>

                  <!-- Tab Content -->
                  <div class="tab-content">
                    <!-- Course Information Section -->
                    <div class="tab-pane active" id="course-information">
                      @include('courses.components.course-information')
                    </div>

                    <!-- Course of Study Section -->
                    <div class="tab-pane" id="course-of-study">
                      @include('courses.components.course-of-study')
                    </div>

                    <!-- Discussion Section -->
                    <div class="tab-pane" id="discussion">
                      @include('courses.components.discussion')
                    </div>

                    <!-- Exams Section -->
                    <div class="tab-pane" id="exams">
                      @include('courses.components.exams')
                    </div>

                    <!-- Coach Section -->
                    <div class="tab-pane" id="coach">
                      @include('courses.components.coach')
                    </div>

                    <!-- Course Schedule Section -->
                    @if($enrollment && $enrollment->is_active)
                    <div class="tab-pane" id="course-schedule">
                      @include('courses.components.course-schedule')
                    </div>
                    @endif

                    <!-- Course Ratings Section -->
                    <div class="tab-pane" id="course-ratings">
                      @include('courses.components.course-ratings')
                    </div>
                  </div>
                @endif
              </div>
              <div class="col-lg-4">
<!-- Course Info Card -->
                <div class="courses-single-info us-course-side">
                  <div class="bg-white shadow-2 rounded-8 border-light py-10 px-10">
                    <div class="relative">
                      <img class="w-1/1"
                           src="{{ $product->image ? config('app.file_url') . 'uploads/products/images/' . $product->image : asset('assets/img/placeholders/course-not-found.png') }}"
                           alt="{{ $product->titre }}"
                           style="max-height:250px"
                           onerror="this.src='{{ asset('assets/img/placeholders/course-not-found.png') }}'">

                    </div>

                    <div class="courses-single-info__content scroll-bar-1 pt-30 pb-20 px-20">
                      <div class="d-flex justify-between items-center mb-15">
                        @if($product->prix > 0)
                          <div class="text-24 lh-1 text-dark-1 fw-500">@price($product->prix)</div>
                        @else
                          <div class="text-24 lh-1 text-green-1 fw-500">{{ __('messages.free') }}</div>
                        @endif
                      </div>

                      {{-- Points earned section - visible to all visitors --}}
                      @if($product->prix > 0)
                        @php
                          $pointsEarned = (int) floor($product->prix * \App\Services\PointsService::POINTS_EARNED_PER_DOLLAR);
                        @endphp
                        <div class="bg-light-4 rounded-8 p-15 mb-20 text-center" style="border: 1px dashed #093162;">
                          <div class="d-flex align-items-center justify-content-center mb-5">
                            <i class="fas fa-gift text-purple-1 mr-10" style="font-size: 18px;"></i>
                            <span class="text-16 fw-600 text-purple-1">{{ $pointsEarned }} {{ __('messages.free_points_on_course') }}</span>
                          </div>
                          <p class="text-13 text-light-1 mb-0">{{ __('messages.redeem_points_for_coupon') }}</p>
                        </div>
                      @endif

                      @php
                        $hasPurchased = auth()->check() && $product->hasStudentAccess(auth()->id());
                      @endphp

                      @if(auth()->check())
                        @if($hasPurchased)
                          @if(isset($enrollment) && $enrollment && $enrollment->is_expired)
                            <!-- Course Expired Badge -->
                            <div class="course-expired-badge rounded-12 py-20 px-20 mb-15" style="background: linear-gradient(135deg, #093162 0%, #0056a3 100%); box-shadow: 0 6px 20px rgba(0, 113, 174, 0.25);">
                              <div class="text-white">
                                <div class="text-15 fw-600 mb-3">{{ __('messages.course_expired') }}</div>
                                <div class="text-12 lh-14" style="color: rgba(255, 255, 255, 0.9);">{{ __('messages.extend_subscription_message') }}</div>
                              </div>
                            </div>
                          @else
                            <!-- Course Purchased Badge -->
                            <div class="course-purchased-badge">
                              <div class="badge-content">
                                <div class="badge-icon">
                                  <div class="icon-check"></div>
                                </div>
                                <div class="badge-text">
                                  <div class="badge-title">{{ __('messages.course_purchased') }}</div>
                                  <div class="badge-subtitle">{{ __('messages.full_access_granted') }}</div>
                                </div>
                              </div>
                              <div class="badge-decoration">
                                <div class="decoration-circle"></div>
                                <div class="decoration-circle"></div>
                              </div>
                            </div>
                          @endif
                        @else
                          <button type="button" class="button -md -purple-1 text-white w-1/1 mb-10 add-to-cart-course-btn" data-product-id="{{ $product->id }}" data-product-title="{{ $product->titre }}">
                            <i class="icon icon-cart mr-10"></i>
                            {{ __('messages.add_to_cart') }}
                          </button>
                          <form action="{{ route('cart.buy-now-course', ['locale' => app()->getLocale(), 'product' => $product]) }}" method="POST" class="w-1/1 mb-10">
                            @csrf
                            <button type="submit" class="button -md -outline-dark-1 text-dark-1 w-1/1 buy-now-course-btn">
                              <i class="icon icon-arrow-right mr-10"></i>
                              {{ __('messages.buy_now') }}
                            </button>
                          </form>

                          {{-- Installment Payment Option --}}
                          @if($product->installment_allowed && $product->validity_months > 0)
                            <div class="bg-light-4 rounded-8 p-15 mb-10 text-center" style="border: 1px solid #093162;">
                              <div class="d-flex align-items-center justify-content-center mb-8">
                                <i class="icon-calendar text-14 text-purple-1 mr-8"></i>
                                <span class="text-14 fw-600 text-purple-1">{{ __('messages.pay_in_installments') }}</span>
                              </div>
                              <p class="text-12 text-light-1 mb-8">{{ $product->validity_months }} {{ __('messages.monthly_payments') }} @price($product->installment_price)</p>
                              <a href="{{ route('student-dashboard.installments.checkout', ['locale' => app()->getLocale(), 'product' => $product->id]) }}" class="button -sm -outline-purple-1 text-purple-1 w-1/1">
                                <i class="icon-credit-card mr-8"></i>
                                {{ __('messages.choose_installment_plan') }}
                              </a>
                            </div>
                          @endif
                        @endif
                      @else
                        <button type="button" class="button -md -purple-1 text-white w-1/1 mb-10 add-to-cart-course-btn" data-product-id="{{ $product->id }}" data-product-title="{{ $product->titre }}">
                          <i class="icon icon-cart mr-10"></i>
                          {{ __('messages.add_to_cart') }}
                        </button>
                        <form action="{{ route('cart.buy-now-course', ['locale' => app()->getLocale(), 'product' => $product]) }}" method="POST" class="w-1/1 mb-10">
                          @csrf
                          <button type="submit" class="button -md -outline-dark-1 text-dark-1 w-1/1 buy-now-course-btn">
                            <i class="icon icon-arrow-right mr-10"></i>
                            {{ __('messages.buy_now') }}
                          </button>
                        </form>

                        {{-- Installment Payment Option --}}
                        @if($product->installment_allowed && $product->validity_months > 0)
                          <div class="bg-light-4 rounded-8 p-15 mb-10 text-center" style="border: 1px solid #093162;">
                            <div class="d-flex align-items-center justify-content-center mb-8">
                              <i class="icon-calendar text-14 text-purple-1 mr-8"></i>
                              <span class="text-14 fw-600 text-purple-1">{{ __('messages.pay_in_installments') }}</span>
                            </div>
                            <p class="text-12 text-light-1 mb-8">{{ $product->validity_months }} {{ __('messages.monthly_payments') }} @price($product->installment_price)</p>
                            <a href="{{ route('student-dashboard.installments.checkout', ['locale' => app()->getLocale(), 'product' => $product->id]) }}" class="button -sm -outline-purple-1 text-purple-1 w-1/1">
                              <i class="icon-credit-card mr-8"></i>
                              {{ __('messages.choose_installment_plan') }}
                            </a>
                          </div>
                        @endif
                      @endif


                      <div class="mt-25">
                        @if($product->lessons_count)
                          <div class="d-flex justify-between py-8">
                            <div class="d-flex items-center text-dark-1">
                              <div class="icon-video-file"></div>
                              <div class="ml-10">{{ __('messages.lessons') }}</div>
                            </div>
                            <div>{{ $product->lessons_count }}</div>
                          </div>
                        @endif

                        @if($product->duration)
                          <div class="d-flex justify-between py-8 border-top-light">
                            <div class="d-flex items-center text-dark-1">
                              <div class="icon-clock-2"></div>
                              <div class="ml-10">{{ __('messages.duration') }}</div>
                            </div>
                            <div>{{ $product->duration }}</div>
                          </div>
                        @endif

                        <div class="d-flex justify-between py-8 border-top-light">
                          <div class="d-flex items-center text-dark-1">
                            <div class="icon-translate"></div>
                            <div class="ml-10">{{ __('messages.language') }}</div>
                          </div>
                          <div>{{ app()->getLocale() == 'ar' ? 'العربية' : 'English' }}</div>
                        </div>

                        <div class="d-flex justify-between py-8 border-top-light">
                          <div class="d-flex items-center text-dark-1">
                            <div class="icon-badge"></div>
                            <div class="ml-10">{{ __('messages.certificate') }}</div>
                          </div>
                          <div>{{ __('messages.yes') }}</div>
                        </div>

                        <div class="d-flex justify-between py-8 border-top-light">
                          <div class="d-flex items-center text-dark-1">
                            <div class="icon-infinity"></div>
                            <div class="ml-10">{{ __('messages.course_access') }}</div>
                          </div>
                          <div>
                            @if(isset($enrollment) && $enrollment)
                              @if($enrollment->is_expired)
                                <span class="text-dark-1 fw-600">{{ __('messages.course_expired') }}</span>
                              @elseif($enrollment->expiration_date)
                                @php
                                  $daysLeft = $enrollment->days_left;
                                @endphp
                                @if($daysLeft !== null && $daysLeft <= 5 && $daysLeft > 0)
                                  <span class="text-red-1 fw-600">{{ $daysLeft }} {{ $daysLeft == 1 ? 'day' : 'days' }}</span>
                                @else
                                  <span class="text-dark-1 fw-500">{{ $enrollment->access_left }}</span>
                                @endif
                              @else
                                <span class="text-dark-1 fw-500">{{ __('messages.lifetime_access') }}</span>
                              @endif
                            @elseif($product->validity_months)
                              <span class="text-dark-1">{{ $product->validity_months }} {{ __('messages.months_after_purchase') }}</span>
                            @else
                              <span class="text-dark-1 fw-500">{{ __('messages.lifetime_access') }}</span>
                            @endif
                          </div>
                        </div>
                      </div>

                      <div class="d-flex justify-center pt-15">
                        @php
                          $shareUrl = urlencode(request()->url());
                          $shareTitle = urlencode($product->current_variation ? $product->current_variation->name : $product->titre);
                          $shareDescription = urlencode($product->current_variation ? ($product->current_variation->short_description ?? '') : '');
                        @endphp
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="d-flex justify-center items-center size-40 rounded-full"
                           title="{{ __('messages.share_on_facebook') }}">
                          <i class="fa fa-facebook"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareTitle }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="d-flex justify-center items-center size-40 rounded-full"
                           title="{{ __('messages.share_on_twitter') }}">
                          <i class="fa fa-twitter"></i>
                        </a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="d-flex justify-center items-center size-40 rounded-full"
                           title="{{ __('messages.share_on_linkedin') }}">
                          <i class="fa fa-linkedin"></i>
                        </a>
                        <a href="https://wa.me/?text={{ $shareTitle }}%20{{ $shareUrl }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="d-flex justify-center items-center size-40 rounded-full"
                           title="{{ __('messages.share_on_whatsapp') }}">
                          <i class="fa fa-whatsapp"></i>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
  </main>

  @include('layouts.footer')

           <!-- CSS pour les onglets -->
         <style>
       /* Course Purchased Badge - Modern Design */
       .course-purchased-badge {
         position: relative;
         background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
         border-radius: 16px;
         padding: 24px;
         margin-bottom: 20px;
         overflow: hidden;
         box-shadow: 0 8px 32px rgba(16, 185, 129, 0.3);
         border: 1px solid rgba(255, 255, 255, 0.1);
       }

       .course-purchased-badge::before {
         content: '';
         position: absolute;
         top: 0;
         left: 0;
         right: 0;
         bottom: 0;
         background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.1) 50%, transparent 70%);
         animation: shimmer 2s infinite;
       }

       @keyframes shimmer {
         0% { transform: translateX(-100%); }
         100% { transform: translateX(100%); }
       }

       .badge-content {
         position: relative;
         z-index: 2;
         display: flex;
         align-items: center;
         justify-content: center;
         text-align: center;
       }

       .badge-icon {
         background: rgba(255, 255, 255, 0.2);
         border-radius: 50%;
         width: 48px;
         height: 48px;
         display: flex;
         align-items: center;
         justify-content: center;
         margin-right: 16px;
         backdrop-filter: blur(10px);
         border: 1px solid rgba(255, 255, 255, 0.3);
       }

       .badge-icon .icon-check {
         color: white;
         font-size: 20px;
         font-weight: bold;
       }

       .badge-text {
         flex: 1;
       }

       .badge-title {
         color: white;
         font-size: 16px;
         font-weight: 600;
         margin-bottom: 4px;
         text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
       }

       .badge-subtitle {
         color: rgba(255, 255, 255, 0.9);
         font-size: 14px;
         font-weight: 400;
       }

       .badge-decoration {
         position: absolute;
         top: 0;
         right: 0;
         bottom: 0;
         left: 0;
         pointer-events: none;
         z-index: 1;
       }

       .decoration-circle {
         position: absolute;
         border-radius: 50%;
         background: rgba(255, 255, 255, 0.1);
         animation: float 3s ease-in-out infinite;
       }

       .decoration-circle:nth-child(1) {
         width: 60px;
         height: 60px;
         top: -20px;
         right: -20px;
         animation-delay: 0s;
       }

       .decoration-circle:nth-child(2) {
         width: 40px;
         height: 40px;
         bottom: -10px;
         left: -10px;
         animation-delay: 1.5s;
       }

       @keyframes float {
         0%, 100% { transform: translateY(0px); }
         50% { transform: translateY(-10px); }
       }

       /* RTL Support for Course Purchased Badge */
       [dir="rtl"] .badge-icon {
         margin-right: 0;
         margin-left: 16px;
       }

       [dir="rtl"] .badge-content {
         flex-direction: row-reverse;
       }



       /* RTL Fix for Course Content Layout */
       [dir="rtl"] .col-lg-8 {
         order: 2 !important;
         margin-right: 0 !important;
         margin-left: 0 !important;
       }


       /* RTL: mirror the course layout — content right, sidebar left */
       [dir="rtl"] .us-course-row {
         flex-direction: row-reverse;
       }

       /* Sidebar: in-flow sticky column (was absolute + GSAP pin) */
       .us-course-side {
         position: static !important;
         width: 100% !important;
         top: auto; right: auto; left: auto;
       }
       .us-course-side > div {
         position: sticky;
         top: 110px;
       }
       @media (min-width: 992px) {
         .us-course-side { margin-top: -270px; z-index: 20; }
       }
       .us-course-side .courses-single-info__content {
         height: auto !important;
         overflow-y: visible !important;
       }

       .nav-tabs-wrapper {
         position: relative;
         border-bottom: none;
         margin-bottom: 30px;
       }
       /* full-bleed divider line under the tabs */
       .nav-tabs-wrapper::after {
         content: '';
         position: absolute;
         bottom: 0;
         left: 50%;
         transform: translateX(-50%);
         width: 100vw;
         max-width: calc(100vw - 17px);
         height: 1px;
         background: #dddfe2;
       }
      .nav-tabs {
        display: flex;
        gap: 30px;
        border: none;
      }
      .nav-tab {
        border: none;
        background: none;
        color: #6c757d;
        font-weight: 500;
        padding: 12px 0;
        position: relative;
        transition: all 0.3s ease;
        cursor: pointer;
        font-size: 14px;
      }
      .nav-tab:hover {
        color: #297a7b;
      }
      .nav-tab.active {
        color: #093162;
      }
      .nav-tab.active::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 0;
        right: 0;
        height: 2px;
        background: #3cb4b0;
      }
      .tab-content {
        padding-top: 30px;
      }
      .tab-pane {
        display: none;
        opacity: 0;
        transition: opacity 0.3s ease;
      }
      .tab-pane.active {
        display: block;
        opacity: 1;
      }

      @media (max-width: 768px) {
        .nav-tabs-wrapper {
          overflow-x: auto;
          -webkit-overflow-scrolling: touch;
        }

        .nav-tabs {
          flex-wrap: nowrap;
          min-width: max-content;
          gap: 16px;
          padding-bottom: 4px;
        }

        .tab-content,
        .tab-pane {
          max-width: 100%;
          overflow-x: hidden;
        }
      }

      @media (max-width: 991.98px) {
        .page-header__content > .row {
          display: flex;
          flex-direction: column !important;
          gap: 20px;
        }

        [dir="rtl"] .page-header__content > .row {
          flex-direction: column !important;
        }

        .page-header__content > .row > [class*="col-"] {
          width: 100%;
          max-width: 100%;
          flex: 0 0 100%;
        }

        .page-header,
        .page-header .container,
        .page-header__content {
          overflow-x: hidden;
        }

        .courses-single-info {
          position: relative !important;
          top: auto !important;
          right: auto !important;
          left: auto !important;
          bottom: auto !important;
          transform: none !important;
          width: 100% !important;
          max-width: 100% !important;
          margin: 0 !important;
          margin-top: 12px !important;
          z-index: 1;
        }

        .courses-single-info .bg-white {
          width: 100%;
          max-width: 100%;
        }

        [dir="rtl"] .col-lg-8 {
          order: initial !important;
        }
      }
    </style>



    <!-- JavaScript -->
   <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
   <script src="{{ asset('assets/js/vendors.js') }}"></script>
   <script src="{{ asset('assets/js/main.js') }}"></script>

           <script>
      // Désactiver GSAP pour éviter les conflits
      if (typeof gsap !== 'undefined') {
        gsap.registerPlugin = function() {};
      }

      document.addEventListener('DOMContentLoaded', function() {
        // Système d'onglets simple
        const tabs = document.querySelectorAll('.nav-tab');
        const tabPanes = document.querySelectorAll('.tab-pane');

        function switchTab(tabId) {
          // Masquer tous les onglets
          tabPanes.forEach(pane => {
            pane.classList.remove('active');
          });

          // Retirer la classe active de tous les boutons
          tabs.forEach(tab => {
            tab.classList.remove('active');
          });

          // Afficher l'onglet sélectionné
          const targetPane = document.getElementById(tabId);
          const targetTab = document.querySelector(`[data-tab="${tabId}"]`);

          if (targetPane && targetTab) {
            targetPane.classList.add('active');
            targetTab.classList.add('active');
          }
        }

        // Ajouter les événements de clic
        tabs.forEach(tab => {
          tab.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            switchTab(tabId);
          });
        });

        // Initialiser avec le premier onglet
        if (tabs.length > 0) {
          const firstTabId = tabs[0].getAttribute('data-tab');
          switchTab(firstTabId);
        }

        // Fix RTL positioning for Course Info Card
        const isRTL = document.documentElement.dir === 'rtl';
        if (isRTL) {
          // Ensure proper RTL layout without breaking header
          const courseInfoCard = document.querySelector('.courses-single-info');
          if (courseInfoCard) {
            // Remove any conflicting inline styles
            courseInfoCard.style.position = '';
            courseInfoCard.style.left = '';
            courseInfoCard.style.right = '';
            courseInfoCard.style.transform = '';
            courseInfoCard.style.margin = '';
            courseInfoCard.style.float = '';
          }

          // No need to force row direction - CSS handles it
          // Force column order is already handled by CSS
        }

        // Global toggleSection function for course components
        window.toggleSection = function(sectionId) {
          const section = document.getElementById(sectionId);
          const arrowId = sectionId.replace('-section', '-arrow');
          const arrow = document.getElementById(arrowId);

          if (section && arrow) {
            const isExpanded = section.classList.contains('expanded');

            if (isExpanded) {
              // Fermer la section
              section.classList.remove('expanded');
              arrow.classList.remove('rotated');
            } else {
              // Ouvrir la section
              section.classList.add('expanded');
              arrow.classList.add('rotated');
            }
          }
        };

        // Add to Cart functionality for courses
        document.querySelectorAll('.add-to-cart-course-btn').forEach(button => {
          button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const productTitle = this.getAttribute('data-product-title');

            // Désactiver le bouton pendant la requête
            this.disabled = true;
            this.innerHTML = '<i class="icon icon-loader mr-10"></i>{{ __("messages.adding") }}...';

                         // Envoyer la requête AJAX
             const formData = new FormData();
             formData.append('quantity', 1);
             formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

             const locale = '{{ app()->getLocale() }}';
             fetch(`/${locale}/cart/add-course/${productId}`, {
               method: 'POST',
               headers: {
                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                 'X-Requested-With': 'XMLHttpRequest'
               },
               body: formData
             })
                         .then(response => {
               if (!response.ok) {
                 throw new Error(`HTTP error! status: ${response.status}`);
               }
               return response.json();
             })
             .then(data => {
              if (data.success) {
                // Afficher un message de succès
                this.innerHTML = '<i class="icon icon-check mr-10"></i>{{ __("messages.added") }}';
                this.classList.remove('-purple-1');
                this.classList.add('-green-1');

                // Mettre à jour le compteur du panier dans le header
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

                // Réactiver le bouton après 2 secondes
                setTimeout(() => {
                  this.disabled = false;
                  this.innerHTML = '<i class="icon icon-cart mr-10"></i>{{ __("messages.add_to_cart") }}';
                  this.classList.remove('-green-1');
                  this.classList.add('-purple-1');
                }, 2000);
              } else {
                // Afficher un message d'erreur
                this.innerHTML = `<i class="icon icon-alert mr-10"></i>${data.message}`;
                this.classList.remove('-purple-1');
                this.classList.add('-red-1');

                // Réactiver le bouton après 2 secondes
                setTimeout(() => {
                  this.disabled = false;
                  this.innerHTML = '<i class="icon icon-cart mr-10"></i>{{ __("messages.add_to_cart") }}';
                  this.classList.remove('-red-1');
                  this.classList.add('-purple-1');
                }, 2000);
              }
            })
            .catch(error => {
              console.error('Error:', error);
              this.innerHTML = '<i class="icon icon-alert mr-10"></i>{{ __("messages.error") }}';
              this.classList.remove('-purple-1');
              this.classList.add('-red-1');

              // Réactiver le bouton après 2 secondes
              setTimeout(() => {
                this.disabled = false;
                this.innerHTML = '<i class="icon icon-cart mr-10"></i>{{ __("messages.add_to_cart") }}';
                this.classList.remove('-red-1');
                this.classList.add('-purple-1');
              }, 2000);
            });
          });
        });
      });
    </script>
</body>
</html>
