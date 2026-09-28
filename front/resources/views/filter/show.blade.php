
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
  @include('layouts.head')
  <link rel="stylesheet" href="{{ asset('assets/css/filter-custom.css') }}">
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
                  <h1 class="page-header__title">{{ $product->titre }}</h1>
                </div>

                <div data-anim="slide-up delay-2">
                  <p class="page-header__text">{{ __('messages.course_details') }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Course Details -->
      <section class="layout-pt-md layout-pb-lg">
        <div class="container">
          <div class="row y-gap-30">
            <!-- Course Image -->
            <div class="col-lg-5">
              <div class="course-image-wrapper">
                <img src="{{ $product->image ? config('app.file_url') . 'uploads/products/images/' . $product->image : asset('assets/img/placeholders/course-not-found.png') }}"
                     alt="{{ $product->titre }}"
                     class="w-1/1 rounded-8"
                     onerror="this.src='{{ asset('assets/img/placeholders/course-not-found.png') }}'">
                @if($product->prix > 0)
                  <div class="course-badge paid">
                    <span>{{ __('messages.paid') }}</span>
                  </div>
                @else
                  <div class="course-badge free">
                    <span>{{ __('messages.free') }}</span>
                  </div>
                @endif
              </div>
            </div>

            <!-- Course Info -->
            <div class="col-lg-7">
              <div class="course-info">
                <h2 class="text-32 fw-600 text-dark-1 mb-20">{{ $product->titre }}</h2>

                @if($product->description_short)
                  <p class="text-16 text-light-1 mb-30">{{ $product->description_short }}</p>
                @endif

                <div class="course-meta mb-30">
                  <div class="row y-gap-20">
                    <div class="col-sm-6">
                      <div class="meta-item">
                        <div class="meta-label">{{ __('messages.price') }}</div>
                        <div class="meta-value">
                          @if($product->prix > 0)
                            <span class="text-24 fw-600 text-purple-1">@price($product->prix)</span>
                          @else
                            <span class="text-24 fw-600 text-green-1">{{ __('messages.free') }}</span>
                          @endif
                        </div>
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="meta-item">
                        <div class="meta-label">{{ __('messages.level') }}</div>
                        <div class="meta-value">{{ __('messages.' . $product->niveau) }}</div>
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="meta-item">
                        <div class="meta-label">{{ __('messages.type') }}</div>
                        <div class="meta-value">{{ __('messages.' . $product->type) }}</div>
                      </div>
                    </div>

                    @if($product->teacher)
                      <div class="col-sm-6">
                        <div class="meta-item">
                          <div class="meta-label">{{ __('messages.teacher') }}</div>
                          <div class="meta-value">{{ $product->teacher->full_name }}</div>
                        </div>
                      </div>
                    @endif

                    @if($product->category)
                      <div class="col-sm-6">
                        <div class="meta-item">
                          <div class="meta-label">{{ __('messages.category') }}</div>
                          <div class="meta-value">{{ $product->category->name }}</div>
                        </div>
                      </div>
                    @endif

                    @if($product->duration)
                      <div class="col-sm-6">
                        <div class="meta-item">
                          <div class="meta-label">{{ __('messages.duration') }}</div>
                          <div class="meta-value">{{ $product->duration }}</div>
                        </div>
                      </div>
                    @endif
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="course-actions">
                  @if($product->prix > 0)
                    <button type="button" class="button -md -purple-1 text-white">
                      <i class="icon icon-cart mr-10"></i>
                      {{ __('messages.enroll_now') }}
                    </button>
                  @else
                    <button type="button" class="button -md -green-1 text-white">
                      <i class="icon icon-play mr-10"></i>
                      {{ __('messages.start_learning') }}
                    </button>
                  @endif

                  <a href="{{ route('filter.index') }}" class="button -md -outline-purple-1 text-purple-1">
                    <i class="icon icon-arrow-left mr-10"></i>
                    {{ __('messages.back_to_courses') }}
                  </a>
                </div>
              </div>
            </div>
          </div>

          <!-- Course Description -->
          @if($product->description)
            <div class="row mt-60">
              <div class="col-12">
                <div class="course-description">
                  <h3 class="text-24 fw-600 text-dark-1 mb-30">{{ __('messages.description') }}</h3>
                  <div class="text-16 text-light-1">
                    {!! $product->description !!}
                  </div>
                </div>
              </div>
            </div>
          @endif

          <!-- Related Courses -->
          @if($relatedCourses->count() > 0)
            <div class="row mt-60">
              <div class="col-12">
                <h3 class="text-24 fw-600 text-dark-1 mb-30">{{ __('messages.related_courses') }}</h3>
                <div class="row y-gap-30">
                  @foreach($relatedCourses as $relatedCourse)
                    <div class="col-lg-3 col-md-6">
                      @include('filter.components.products', ['course' => $relatedCourse])
                    </div>
                  @endforeach
                </div>
              </div>
            </div>
          @endif
        </div>
      </section>
    </div>
  </main>

  @include('layouts.footer')
</body>
</html>
