

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
  @include('layouts.head')
  <link rel="stylesheet" href="{{ asset('assets/css/filter-custom.css') }}">
  <style>
    /* Add margin-top to page header to prevent it from being hidden under menu */
    .page-header.-type-1 {
      margin-top: 20px;
    }

    @media (max-width: 991px) {
      .page-header.-type-1 {
        margin-top: 20px;
      }
    }

    /* Mobile Filter Toggle Button */
    .mobile-filter-toggle {
      display: none;
    }

    /* Hide mobile sidebar content on desktop */
    .mobile-filter-sidebar {
      display: none;
    }

    .mobile-filter-overlay {
      display: none;
    }

    @media (max-width: 991px) {
      .mobile-filter-toggle {
        display: flex;
        position: fixed;
        top: 50%;
        left: 0;
        transform: translateY(-50%);
        z-index: 999;
        background: #0071ae;
        color: white;
        border: none;
        border-radius: 0 8px 8px 0;
        width: 50px;
        height: 50px;
        font-size: 20px;
        box-shadow: 2px 0 10px rgba(0, 113, 174, 0.3);
        cursor: pointer;
        transition: all 0.3s ease;
        align-items: center;
        justify-content: center;
      }

      .mobile-filter-toggle:hover {
        background: #005a8c;
        box-shadow: 2px 0 15px rgba(0, 113, 174, 0.5);
        width: 55px;
      }

      .mobile-filter-toggle i {
        margin: 0;
      }

      /* Hide desktop sidebar on mobile */
      .filter-sidebar-wrapper {
        display: none !important;
      }

      /* Show mobile sidebar content */
      .mobile-filter-sidebar .sidebar {
        display: block;
      }

      /* Show mobile elements on mobile */
      .mobile-filter-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        opacity: 0;
        transition: opacity 0.3s ease;
      }

      .mobile-filter-overlay.active {
        display: block;
        opacity: 1;
      }

      /* Mobile Sidebar */
      .mobile-filter-sidebar {
        display: block;
        position: fixed;
        top: 0;
        left: -100%;
        width: 85%;
        max-width: 350px;
        height: 100%;
        background: white;
        z-index: 1001;
        overflow-y: auto;
        padding: 20px;
        transition: left 0.3s ease;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
      }

      .mobile-filter-sidebar.active {
        left: 0;
      }

      /* Close button */
      .mobile-filter-close {
        position: absolute;
        top: 15px;
        right: 15px;
        background: #f3f4f6;
        border: none;
        border-radius: 50%;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        z-index: 10;
      }

      .mobile-filter-close:hover {
        background: #e5e7eb;
        transform: rotate(90deg);
      }

      .mobile-filter-close i {
        font-size: 18px;
        color: #374151;
      }

      /* Sidebar header */
      .mobile-filter-header {
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid #e5e7eb;
      }

      .mobile-filter-header h3 {
        font-size: 22px;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
      }
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

    /* Enhanced Filter Sidebar Styles */
    .sidebar.-courses {
      background: #fff;
      border-radius: 12px;
      padding: 25px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.05);
      border: 1px solid #f1f2f4;
    }

    .sidebar__item {
      margin-bottom: 30px;
      padding-bottom: 25px;
      border-bottom: 1px solid #f1f2f4;
    }

    .sidebar__item:last-child {
      margin-bottom: 0;
      padding-bottom: 0;
      border-bottom: none;
    }

    .sidebar__title {
      font-size: 18px;
      font-weight: 700;
      color: #1f2937;
      margin-bottom: 20px;
      position: relative;
      padding-left: 15px;
    }

    .sidebar__title::before {
      content: '';
      position: absolute;
      left: 0;
      top: 50%;
      transform: translateY(-50%);
      width: 4px;
      height: 18px;
      background: #0071ae;
      border-radius: 2px;
    }

    /* Search Input */
    .sidebar-search .form-input {
      position: relative;
      display: block;
      width: 100%;
      max-width: 100%;
      overflow: hidden;
      box-sizing: border-box;
    }

    .sidebar-search input {
      display: block;
      width: 100%;
      max-width: 100%;
      min-width: 0;
      height: 50px;
      padding: 0 58px 0 14px;
      border: 1px solid #e5e7eb;
      border-radius: 8px;
      background: #f9fafb;
      transition: all 0.3s ease;
      font-size: 15px;
      color: #111827;
      caret-color: #111827;
      pointer-events: auto;
      position: relative;
      z-index: 1;
      box-sizing: border-box;
    }

    .sidebar-search input:focus {
      background: #fff;
      border-color: #0071ae;
      box-shadow: 0 0 0 4px rgba(0, 113, 174, 0.1);
    }

    .sidebar-search .form-input button {
      position: absolute;
      top: 0;
      right: 0;
      width: 50px;
      height: 50px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #0071ae;
      color: white;
      border-radius: 0 8px 8px 0;
      transition: all 0.3s ease;
      border: none;
      cursor: pointer;
      transform: none;
      z-index: 3;
      margin: 0;
      padding: 0;
    }

    .sidebar-search .form-input button:hover {
      background: #005a8c;
      transform: none;
    }

    .sidebar-search input:focus + button {
      background: #005a8c;
      color: white;
    }

    [dir="rtl"] .sidebar-search input {
      padding: 0 14px 0 58px;
    }

    [dir="rtl"] .sidebar-search .form-input button {
      right: auto;
      left: 0;
      border-radius: 8px 0 0 8px;
    }

    /* Checkbox & Radio Items */
    .sidebar-checkbox__item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 12px;
      cursor: pointer;
      transition: all 0.2s ease;
      padding: 8px 12px;
      border-radius: 8px;
    }

    .sidebar-checkbox__item:hover {
      background: #f3f4f6;
    }

    .sidebar-checkbox__title {
      font-size: 15px;
      color: #4b5563;
      flex-grow: 1;
      margin-left: 10px;
      transition: color 0.2s ease;
    }

    .sidebar-checkbox__item:hover .sidebar-checkbox__title {
      color: #1f2937;
    }

    .sidebar-checkbox__count {
      font-size: 13px;
      color: #9ca3af;
      background: #f3f4f6;
      padding: 2px 8px;
      border-radius: 12px;
      transition: all 0.2s ease;
    }

    .sidebar-checkbox__item:hover .sidebar-checkbox__count {
      background: #e5e7eb;
      color: #6b7280;
    }

    /* Custom Radio & Checkbox Styles */
    .form-radio .radio,
    .form-checkbox {
      position: relative;
      width: 20px;
      height: 20px;
      border: 2px solid #d1d5db;
      border-radius: 50%; /* Radio default */
      transition: all 0.2s ease;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .form-checkbox {
      border-radius: 6px; /* Checkbox square */
    }

    .form-radio input,
    .form-checkbox input {
      position: absolute;
      width: 100%;
      height: 100%;
      opacity: 0;
      cursor: pointer;
      z-index: 2;
    }

    .radio__mark,
    .form-checkbox__mark {
      display: none;
    }

    .form-radio input:checked + .radio__mark,
    .form-checkbox input:checked + .form-checkbox__mark {
      display: block;
      width: 10px;
      height: 10px;
      background: #0071ae;
      border-radius: 50%;
    }

    .form-checkbox input:checked + .form-checkbox__mark {
      width: 100%;
      height: 100%;
      background: #0071ae;
      border-radius: 4px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .form-checkbox__icon {
      color: white;
      font-size: 12px;
    }

    /* Active State for Inputs */
    .form-radio:has(input:checked),
    .form-checkbox:has(input:checked) {
      border-color: #0071ae;
      box-shadow: 0 0 0 3px rgba(0, 113, 174, 0.1);
    }

    /* Active Item Highlight */
    .sidebar-checkbox__item:has(input:checked) {
      background: #f0f9ff;
    }

    .sidebar-checkbox__item:has(input:checked) .sidebar-checkbox__title {
      color: #0071ae;
      font-weight: 600;
    }

    .sidebar-checkbox__item:has(input:checked) .sidebar-checkbox__count {
      background: #0071ae;
      color: white;
    }

    /* Price Filter Styles */
    .sidebar-price-filter input {
      width: 100%;
      height: 40px;
      border: 1px solid #e5e5e5;
      border-radius: 8px;
      padding: 0 15px;
      font-size: 14px;
      color: #333;
      transition: all 0.3s ease;
    }

    .sidebar-price-filter input:focus {
      border-color: #0071ae;
      box-shadow: 0 0 0 3px rgba(0, 113, 174, 0.1);
      outline: none;
    }

    .sidebar-price-filter button {
      background: #0071ae;
      color: white;
      border: none;
      border-radius: 8px;
      height: 40px;
      font-weight: 500;
      font-size: 14px;
      transition: all 0.3s ease;
      margin-top: 15px;
    }

    .sidebar-price-filter button:hover {
      background: #005a8c;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(0, 113, 174, 0.2);
    }

    /* Price Slider Styles */
    .price-slider-wrapper {
      width: 100%;
      padding: 10px 5px 20px 5px; /* Added padding to prevent thumb cutoff */
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
      margin-top: 2px; /* Lower the bar slightly */
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
      margin-top: 5px; /* Center the thumb vertically (30px track - 20px thumb) / 2 */
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
                  <h1 class="page-header__title">{{ __('messages.courses') }}</h1>
                </div>

                <div data-anim="slide-up delay-2">
                  <p class="page-header__text">{{ __('messages.filter_description') }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Mobile Filter Toggle Button -->
      <button class="mobile-filter-toggle" onclick="toggleMobileFilter()" title="{{ __('messages.filter') }}">
        <i class="icon-filter"></i>
      </button>

      <!-- Mobile Filter Overlay -->
      <div class="mobile-filter-overlay" id="mobileFilterOverlay" onclick="closeMobileFilter()"></div>

      <!-- Mobile Filter Sidebar -->
      <div class="mobile-filter-sidebar" id="mobileFilterSidebar">
        <button class="mobile-filter-close" onclick="closeMobileFilter()">
          <i class="icon-close"></i>
        </button>

        <div class="mobile-filter-header">
          <h3>{{ __('messages.filter') }}</h3>
        </div>

        @include('filter.components.left-menu')
      </div>

      <!-- Filter Content -->
      <section class="layout-pt-md layout-pb-lg">
        <div class="container">
          <div class="row y-gap-30">
            <!-- Sidebar Filters (Desktop) -->
            <div class="col-lg-3 filter-sidebar-wrapper">
              @include('filter.components.left-menu')
            </div>

            <!-- Courses Grid -->
            <div class="col-lg-9">
              @include('filter.components.contenu')
            </div>
          </div>
        </div>
      </section>
    </div>
  </main>

  @include('layouts.footer')

  <script>
    // Mobile Filter Toggle Functions
    function toggleMobileFilter() {
      const sidebar = document.getElementById('mobileFilterSidebar');
      const overlay = document.getElementById('mobileFilterOverlay');

      sidebar.classList.add('active');
      overlay.classList.add('active');

      // Prevent body scroll when sidebar is open
      document.body.style.overflow = 'hidden';
    }

    function closeMobileFilter() {
      const sidebar = document.getElementById('mobileFilterSidebar');
      const overlay = document.getElementById('mobileFilterOverlay');

      sidebar.classList.remove('active');
      overlay.classList.remove('active');

      // Restore body scroll
      document.body.style.overflow = '';
    }

    // Close sidebar when pressing ESC key
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        closeMobileFilter();
      }
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

      // AJAX Filter Logic
      document.addEventListener('DOMContentLoaded', function() {
        const contentContainer = document.querySelector('.col-lg-9');

        // Init slider on load
        initPriceSlider();

        function enableSearchInputs() {
          const searchInputs = document.querySelectorAll('.filter-form .sidebar-search input[name="search"]');

          searchInputs.forEach(input => {
            input.removeAttribute('readonly');
            input.removeAttribute('disabled');
            input.style.pointerEvents = 'auto';

            input.addEventListener('click', function(event) {
              event.stopPropagation();
            });

            input.addEventListener('touchstart', function(event) {
              event.stopPropagation();
            }, { passive: true });
          });
        }

        function attachFormListeners() {
          const filterForms = document.querySelectorAll('.filter-form');

          filterForms.forEach(form => {
            if (form.dataset.listenersAttached) return;

            form.addEventListener('submit', function(e) {
              e.preventDefault();
              const formData = new FormData(form);
              const params = new URLSearchParams(formData);
              fetchCourses(`${form.action}?${params.toString()}`);

              if (window.innerWidth <= 991) {
                closeMobileFilter();
              }
            });

            form.addEventListener('change', function(e) {
              // Skip price range inputs (slider inputs don't have name, hidden inputs do)
              // Also skip search text
              if (e.target.name === 'prix_min' || e.target.name === 'prix_max' || e.target.name === 'search' || e.target.classList.contains('slider-input')) {
                return;
              }
              form.dispatchEvent(new Event('submit'));
            });

            form.dataset.listenersAttached = 'true';
          });
        }

        function fetchCourses(url) {
          contentContainer.style.opacity = '0.5';
          contentContainer.style.pointerEvents = 'none';

          fetch(url, {
            headers: {
              'X-Requested-With': 'XMLHttpRequest'
            }
          })
          .then(response => response.json())
          .then(data => {
            contentContainer.innerHTML = data.html;

            // Update sidebars
            const sidebars = document.querySelectorAll('.sidebar.-courses');
            sidebars.forEach(sidebar => {
               sidebar.outerHTML = data.sidebar;
            });

            // Re-attach listeners to the new sidebar forms
            attachFormListeners();
            enableSearchInputs();
            // Re-init slider
            initPriceSlider();

            contentContainer.style.opacity = '1';
            contentContainer.style.pointerEvents = 'auto';

            window.history.pushState({}, '', url);

            if (window.innerWidth <= 991) {
               const resultsTop = document.querySelector('.col-lg-9').offsetTop - 100;
               window.scrollTo({ top: resultsTop, behavior: 'smooth' });
            }
          })
          .catch(error => {
            console.error('Error:', error);
            contentContainer.style.opacity = '1';
            contentContainer.style.pointerEvents = 'auto';
          });
        }

      // Initial attachment
      attachFormListeners();
      enableSearchInputs();

      // Sort Change Delegation
      document.addEventListener('change', function(e) {
        if (e.target.name === 'sort') {
          e.preventDefault();
          const currentUrl = new URL(window.location.href);
          currentUrl.searchParams.set('sort', e.target.value);
          fetchCourses(currentUrl.toString());
        }
      });

      // Pagination Click Delegation
      document.addEventListener('click', function(e) {
        const link = e.target.closest('.pagination a');
        if (link) {
          e.preventDefault();
          fetchCourses(link.href);
        }
      });
    });
  </script>
</body>
</html>
