<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
  @include('layouts.head')
  <title>{{ __('messages.pages') }} - Upskills Academy</title>
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
                    <span class="us-crumb-current">{{ __('messages.pages') }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Pages Header -->
      <section class="layout-pt-md layout-pb-sm">
        <div class="container">
          <div class="row justify-center text-center">
            <div class="col-xl-7 col-lg-9">
              <div class="sectionTitle">
                <h2 class="sectionTitle__title">{{ __('messages.why_academy') }}</h2>
                <p class="sectionTitle__text mt-5">{{ __('messages.why_academy_description') }}</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Pages Grid -->
      <section class="layout-pb-md">
        <div class="container">
          <div class="row y-gap-30">
            @forelse($pages as $page)
              <div class="col-lg-4 col-md-6">
                <div class="page-card">
                  <div class="page-card__content">
                    <h3 class="page-card__title text-18 lh-1 mb-15">
                      <a href="{{ route('pages.show', $page->slug) }}" class="text-dark-1">
                        {{ $page->titre }}
                      </a>
                    </h3>
                                         @if(app()->getLocale() == 'ar' && $page->description_ar)
                       <p class="page-card__text text-14 lh-1 text-light-1 mb-20">
                         {!! Str::limit(strip_tags($page->description_ar), 120) !!}
                       </p>
                     @elseif(app()->getLocale() == 'en' && $page->description_en)
                       <p class="page-card__text text-14 lh-1 text-light-1 mb-20">
                         {!! Str::limit(strip_tags($page->description_en), 120) !!}
                       </p>
                     @elseif($page->description)
                       <p class="page-card__text text-14 lh-1 text-light-1 mb-20">
                         {!! Str::limit(strip_tags($page->description), 120) !!}
                       </p>
                     @endif
                    <a href="{{ route('pages.show', $page->slug) }}" class="button -sm -outline-dark-1 text-dark-1">
                      {{ __('messages.read_more') }}
                      <i class="icon-arrow-right text-11 ml-10"></i>
                    </a>
                  </div>
                </div>
              </div>
            @empty
              <div class="col-12">
                <div class="text-center py-60">
                  <div class="text-18 lh-1 text-light-1">{{ __('messages.no_pages_found') }}</div>
                </div>
              </div>
            @endforelse
          </div>
        </div>
      </section>
    </div>
  </main>

  @include('layouts.footer')

  <!-- Scripts -->
  <script src="{{ asset('assets/js/vendors.js') }}"></script>
  <script src="{{ asset('assets/js/main.js') }}"></script>

  <style>
    .page-card {
      background: white;
      border-radius: 12px;
      padding: 30px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
      transition: all 0.3s ease;
      height: 100%;
    }

    .page-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    }

    .page-card__title a {
      text-decoration: none;
      transition: color 0.3s ease;
    }

    .page-card__title a:hover {
      color: #6f42c1;
    }

    .page-card__text {
      line-height: 1.6;
    }
  </style>
</body>

</html>
