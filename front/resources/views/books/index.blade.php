<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
  @include('layouts.head')
  <link rel="stylesheet" href="{{ asset('assets/css/books-custom.css') }}">
  <style>
    /* Sidebar Search - Inlined to ensure application */
    .sidebar-search .form-input {
        position: relative;
        display: flex;
        align-items: center;
    }

    .sidebar-search .form-input input {
        flex: 1;
        padding: 15px 50px 15px 20px;
        border: 1px solid #e1e5e9;
        border-radius: 12px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .sidebar-search .form-input input:focus {
        border-color: #0071ae;
        box-shadow: 0 0 0 3px rgba(0, 113, 174, 0.1);
        outline: none;
    }

    .sidebar-search .form-input button {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        width: 40px;
        height: 40px;
        border: none;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        background: #0071ae !important;
        color: white !important;
        transition: all 0.3s ease;
    }

    .sidebar-search .form-input button:hover {
        background: #005a8c !important;
        transform: translateY(-50%) scale(1.05);
    }

    /* Pagination Styling */
    .custom-pagination {
      display: flex !important;
      flex-direction: row !important;
      justify-content: center;
      gap: 10px;
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .custom-pagination .page-item {
      list-style: none;
      margin: 0;
      padding: 0;
    }

    .custom-pagination .page-link {
      display: flex;
      align-items: center;
      justify-content: center;
      min-width: 50px;
      height: 50px;
      padding: 0 15px;
      background: white;
      border: 1px solid #e5e7eb;
      border-radius: 10px;
      color: #374151;
      font-size: 18px;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.3s ease;
    }

    .custom-pagination .page-link:hover {
      background: #f3f4f6;
      border-color: #0071ae;
      color: #0071ae;
      transform: translateY(-2px);
    }

    .custom-pagination .page-item.active .page-link {
      background: #0071ae;
      border-color: #0071ae;
      color: white;
      box-shadow: 0 4px 12px rgba(0, 113, 174, 0.3);
    }

    .custom-pagination .page-item.disabled .page-link {
      background: #f9fafb;
      border-color: #e5e7eb;
      color: #9ca3af;
      cursor: not-allowed;
      pointer-events: none;
    }

    /* Pagination arrows */
    .custom-pagination .page-link[rel="prev"],
    .custom-pagination .page-link[rel="next"] {
      font-weight: 700;
      font-size: 20px;
    }

    /* Price Slider Styles */
    .price-slider-wrapper {
      width: 100%;
      padding: 10px 5px 20px 5px;
    }

    .price-values {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
      font-weight: 600;
      color: #333;
      font-size: 13px;
      position: relative;
      height: 20px;
    }

    .price-values span:first-child,
    .price-values span:last-child {
      position: absolute;
      transform: translateX(-50%);
      transition: left 0.1s ease;
      white-space: nowrap;
    }

    .price-values .separator {
      display: none;
    }

    .slider-container {
      position: relative;
      width: 100%;
      height: 30px;
      display: flex;
      align-items: center;
    }

    .slider-track {
      width: 100%;
      height: 6px;
      background-color: #e5e5e5;
      position: absolute;
      z-index: 1;
      top: 50%;
      transform: translateY(-50%);
      margin-top: 2px;
    }

    .slider-input {
      -webkit-appearance: none;
      appearance: none;
      width: 100%;
      outline: none;
      position: absolute;
      margin: 0;
      top: 0;
      background-color: transparent;
      pointer-events: none;
      z-index: 2;
      height: 30px;
      border: none !important;
      padding: 0 !important;
      box-shadow: none !important;
    }

    .slider-input::-webkit-slider-runnable-track {
      background: transparent;
      border: none;
      height: 30px;
    }

    .slider-input::-moz-range-track {
      background: transparent;
      border: none;
      height: 30px;
    }

    /* Webkit Thumbs */
    .slider-input::-webkit-slider-thumb {
      -webkit-appearance: none;
      appearance: none;
      height: 20px;
      width: 20px;
      background-color: #0071ae;
      cursor: pointer;
      pointer-events: auto;
      border-radius: 50%;
      border: 2px solid white;
      box-shadow: 0 2px 6px rgba(0,0,0,0.2);
      transition: transform 0.2s ease, background-color 0.2s;
      margin-top: 5px;
    }

    /* Firefox Thumbs */
    .slider-input::-moz-range-thumb {
      height: 20px;
      width: 20px;
      background-color: #0071ae;
      cursor: pointer;
      pointer-events: auto;
      border-radius: 50%;
      border: 3px solid white;
      box-shadow: 0 2px 6px rgba(0,0,0,0.2);
      transition: transform 0.2s ease, background-color 0.2s;
    }

    .slider-input:active::-webkit-slider-thumb {
      transform: scale(1.1);
      background-color: #005a8c;
    }

    .slider-input:active::-moz-range-thumb {
      transform: scale(1.1);
      background-color: #005a8c;
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
                  <h1 class="page-header__title">{{ __('messages.library') }}</h1>
                </div>

                <div data-anim="slide-up delay-2">
                  <p class="page-header__text">{{ __('messages.library_description') }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Books Content -->
      <section class="layout-pt-md layout-pb-lg">
        <div class="container">
          <div class="row y-gap-30">
            <!-- Sidebar Filters -->
            <div class="col-lg-3">
              <div class="sidebar -courses">
                <form method="GET" action="{{ route('books.index') }}" class="filter-form">
                    @if(request()->has('sort'))
                      <input type="hidden" name="sort" value="{{ request('sort') }}">
                    @endif
                    <!-- Search -->
                    <div class="sidebar__item">
                      <h5 class="sidebar__title">{{ __('messages.search') }}</h5>
                      <div class="sidebar-search">
                        <div class="form-input">
                          <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('messages.search_books') }}">
                          <button type="submit">
                            <i class="icon icon-search"></i>
                          </button>
                        </div>
                      </div>
                    </div>

                    <!-- Price Filter -->
                    <div class="sidebar__item">
                      <h5 class="sidebar__title">{{ __('messages.price') }}</h5>
                      <div class="sidebar-checkbox">
                        <label class="sidebar-checkbox__item" style="cursor: pointer;">
                          <div class="form-radio mr-10">
                            <div class="radio">
                              <input type="radio" name="price_type" value="" {{ request('price_type') == '' ? 'checked' : '' }} onchange="this.form.submit()">
                              <div class="radio__mark">
                                <div class="radio__icon"></div>
                              </div>
                            </div>
                          </div>
                          <div class="sidebar-checkbox__title">{{ __('messages.all') }}</div>
                          <div class="sidebar-checkbox__count">({{ $stats['total'] }})</div>
                        </label>

                        <label class="sidebar-checkbox__item" style="cursor: pointer;">
                          <div class="form-radio mr-10">
                            <div class="radio">
                              <input type="radio" name="price_type" value="free" {{ request('price_type') == 'free' ? 'checked' : '' }} onchange="this.form.submit()">
                              <div class="radio__mark">
                                <div class="radio__icon"></div>
                              </div>
                            </div>
                          </div>
                          <div class="sidebar-checkbox__title">{{ __('messages.free') }}</div>
                          <div class="sidebar-checkbox__count">({{ $stats['free'] }})</div>
                        </label>

                        <label class="sidebar-checkbox__item" style="cursor: pointer;">
                          <div class="form-radio mr-10">
                            <div class="radio">
                              <input type="radio" name="price_type" value="paid" {{ request('price_type') == 'paid' ? 'checked' : '' }} onchange="this.form.submit()">
                              <div class="radio__mark">
                                <div class="radio__icon"></div>
                              </div>
                            </div>
                          </div>
                          <div class="sidebar-checkbox__title">{{ __('messages.paid') }}</div>
                          <div class="sidebar-checkbox__count">({{ $stats['paid'] }})</div>
                        </label>
                      </div>
                    </div>

                    <!-- Price Range -->
                    <div class="sidebar__item">
                      <h5 class="sidebar__title">{{ __('messages.price_range') }}</h5>
                      <div class="sidebar-price-filter">
                        <div class="price-slider-wrapper">
                          <div class="price-values">
                            <span id="price-min-display">${{ request('prix_min', 0) }}</span>
                            <span class="separator">-</span>
                            <span id="price-max-display">${{ request('prix_max', $maxPrice) }}</span>
                          </div>
                          <div class="slider-container">
                            <div class="slider-track"></div>
                            <input type="range" min="0" max="{{ $maxPrice }}" value="{{ request('prix_min', 0) }}" id="slider-min" class="slider-input">
                            <input type="range" min="0" max="{{ $maxPrice }}" value="{{ request('prix_max', $maxPrice) }}" id="slider-max" class="slider-input">
                          </div>
                        </div>

                        <input type="hidden" name="prix_min" id="input-min" value="{{ request('prix_min', 0) }}">
                        <input type="hidden" name="prix_max" id="input-max" value="{{ request('prix_max', $maxPrice) }}">

                        <button type="submit" class="button -sm -purple-1 text-white mt-10 w-100">{{ __('messages.filter') }}</button>
                      </div>
                    </div>
                </form>

                <!-- Clear Filters -->
                @if(request()->hasAny(['search', 'price_type', 'prix_min', 'prix_max', 'sort']))
                <div class="sidebar__item">
                  <a href="{{ route('books.index') }}" class="button -sm -outline-purple-1 text-purple-1 w-100">{{ __('messages.clear_filters') }}</a>
                </div>
                @endif
              </div>
            </div>

            <!-- Books Grid -->
            <div class="col-lg-9">
              <!-- Header with Sort -->
              <div class="row y-gap-20 justify-between items-center mb-30">
                <div class="col-auto">
                  <h3 class="text-24 fw-600 text-dark-1">{{ __('messages.books') }} ({{ $books->total() }})</h3>
                </div>

                <div class="col-auto">
                  <div class="d-flex items-center">
                    <div class="text-14 text-light-1 mr-15">{{ __('messages.sort_by') }}:</div>
                    <form method="GET" action="{{ route('books.index') }}" class="d-flex">
                      @foreach(request()->except('sort') as $key => $value)
                        @if(is_array($value))
                          @foreach($value as $val)
                            <input type="hidden" name="{{ $key }}[]" value="{{ $val }}">
                          @endforeach
                        @else
                          <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                      @endforeach
                      <select name="sort" onchange="this.form.submit()" class="form-select">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>{{ __('messages.latest') }}</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>{{ __('messages.price_low_to_high') }}</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>{{ __('messages.price_high_to_low') }}</option>
                        <option value="title_az" {{ request('sort') == 'title_az' ? 'selected' : '' }}>{{ __('messages.title_a_to_z') }}</option>
                        <option value="title_za" {{ request('sort') == 'title_za' ? 'selected' : '' }}>{{ __('messages.title_z_to_a') }}</option>
                      </select>
                    </form>
                  </div>
                </div>
              </div>

              <!-- Books Grid -->
              @if($books->count() > 0)
                <div class="row y-gap-30">
                  @foreach($books as $book)
                    <div class="col-lg-4 col-md-6">
                      <a href="{{ route('books.show', $book) }}" class="coursesCard -type-1 book-card-link">
                        <div class="relative">
                          <div class="coursesCard__image overflow-hidden rounded-8">
                            <img class="w-1/1" src="{{ $book->image_url }}" alt="{{ $book->titre }}">
                            <div class="coursesCard__image_overlay rounded-8"></div>
                          </div>
                          @if($book->prix > 0)
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
                          <div class="text-17 lh-15 fw-500 text-dark-1 mt-10">{{ $book->titre }}</div>
                          @if($book->description_short)
                            <p class="text-14 lh-1 text-light-1 mt-10">{{ Str::limit($book->description_short, 100) }}</p>
                          @endif
                          <div class="coursesCard-footer">
                            <div class="">
                              @if($book->prix > 0)
                                <div>@price($book->prix)</div>
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

                <!-- Pagination -->
                <div class="row justify-center pt-60 lg:pt-40">
                  <div class="col-auto">
                    {{ $books->links('vendor.pagination.custom') }}
                  </div>
                </div>
              @else
                <!-- No Books Found -->
                <div class="text-center py-80">
                  <div class="icon-book text-80 text-light-1 mb-30"></div>
                  <h3 class="text-24 fw-600 text-dark-1 mb-15">{{ __('messages.no_books_found') }}</h3>
                  <p class="text-16 text-light-1 mb-30">{{ __('messages.no_books_description') }}</p>
                  <a href="{{ route('books.index') }}" class="button -md -purple-1 text-white">{{ __('messages.clear_filters') }}</a>
                </div>
              @endif
            </div>
          </div>
        </div>
      </section>
    </div>
  </main>

  @include('layouts.footer')

  <script>
    // Auto-submit forms when radios change
    document.querySelectorAll('input[type="radio"]').forEach(input => {
      input.addEventListener('change', function() {
        this.form.submit();
      });
    });

    // Price Slider Logic
    function initPriceSlider() {
      const sliders = document.querySelectorAll('.sidebar-price-filter');
      const isRTL = document.documentElement.dir === 'rtl' || document.documentElement.lang === 'ar';

      sliders.forEach(sliderContext => {
          const sliderMin = sliderContext.querySelector("#slider-min");
          const sliderMax = sliderContext.querySelector("#slider-max");
          const displayMin = sliderContext.querySelector("#price-min-display");
          const displayMax = sliderContext.querySelector("#price-max-display");
          const inputMin = sliderContext.querySelector("#input-min");
          const inputMax = sliderContext.querySelector("#input-max");
          const sliderTrack = sliderContext.querySelector(".slider-track");
          const minGap = 0;

          if(!sliderMin || !sliderMax) return;

          const sliderMaxValue = parseInt(sliderMax.max);

          function slideMin() {
            let gap = parseInt(sliderMax.value) - parseInt(sliderMin.value);
            if (gap <= minGap) {
              sliderMin.value = parseInt(sliderMax.value) - minGap;
            }
            displayMin.textContent = "$" + sliderMin.value;
            inputMin.value = sliderMin.value;
            fillColor();
            updatePricePositions();
          }

          function slideMax() {
            let gap = parseInt(sliderMax.value) - parseInt(sliderMin.value);
            if (gap <= minGap) {
              sliderMax.value = parseInt(sliderMin.value) + minGap;
            }
            displayMax.textContent = "$" + sliderMax.value;
            inputMax.value = sliderMax.value;
            fillColor();
            updatePricePositions();
          }

          function fillColor() {
            let percent1 = (sliderMin.value / sliderMaxValue) * 100;
            let percent2 = (sliderMax.value / sliderMaxValue) * 100;
            sliderTrack.style.background = `linear-gradient(to right, #e5e5e5 ${percent1}%, #0071ae ${percent1}%, #0071ae ${percent2}%, #e5e5e5 ${percent2}%)`;
          }

          function updatePricePositions() {
            let percent1 = (sliderMin.value / sliderMaxValue) * 100;
            let percent2 = (sliderMax.value / sliderMaxValue) * 100;
            displayMin.style.left = `${percent1}%`;
            displayMax.style.left = `${percent2}%`;
          }

          // Force LTR direction on range inputs for consistent behavior
          sliderMin.style.direction = 'ltr';
          sliderMax.style.direction = 'ltr';

          sliderMin.addEventListener("input", slideMin);
          sliderMax.addEventListener("input", slideMax);

          // Initial fill and position
          fillColor();
          updatePricePositions();
      });
    }

    document.addEventListener('DOMContentLoaded', function() {
      initPriceSlider();
    });
  </script>
</body>
</html>
