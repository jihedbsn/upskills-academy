<header data-anim="fade" data-add-bg="" class="header -type-1 js-header header-white" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" style="background-color: white !important;">
  <style>
    /* White Header Styles */
    .header-white {
      background-color: white !important;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }


    .header-white .menu > li > a,
    .header-white .header-right__icons a,
    .header-white .header-right__icons,
    .header-white .text-white {
      color: #1f2937 !important;
    }

    .header-white .icon {
      color: #1f2937 !important;
    }

    .header-white .button.-underline {
      color: #1f2937 !important;
    }

    .header-white .cart-badge {
      background: linear-gradient(135deg, #ef4444, #dc2626) !important;
      color: white !important;
    }

    /* Language Switcher */
    .header-white .js-language-active .dropdown__button {
      background: #f8f9fa;
      border-color: #e5e5e5;
      color: #1f2937;
    }

    .header-white .js-language-active .dropdown__button:hover {
      background: #e9ecef;
    }

    /* Mobile menu button */
    .header-white .icon-mobile-menu {
      color: #1f2937 !important;
    }
  </style>
  <style>
    .avatar-circle {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      background: linear-gradient(135deg, #093162, #3cb4b0);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 600;
      font-size: 14px;
      text-transform: uppercase;
      box-shadow: 0 2px 8px rgba(124, 58, 237, 0.3);
    }
    .user-avatar {
      position: relative;
    }
    .user-avatar:hover .avatar-circle {
      transform: scale(1.05);
      transition: transform 0.2s ease;
    }

    /* User Dropdown Styles */
    .user-dropdown {
      position: relative;
    }

    .user-dropdown__trigger {
      transition: all 0.2s ease;
    }

    .user-dropdown__trigger:hover {
      opacity: 0.8;
    }

    .user-dropdown__menu {
      position: absolute;
      top: 100%;
      right: 0;
      background: white;
      border-radius: 12px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
      min-width: 250px;
      z-index: 1000;
      opacity: 0;
      visibility: hidden;
      transform: translateY(-10px);
      transition: all 0.3s ease;
      margin-top: 10px;
    }

    .user-dropdown:hover .user-dropdown__menu {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }

    .user-dropdown__header {
      padding: 20px;
      border-bottom: 1px solid #f1f1f1;
      text-align: center;
    }

    .user-dropdown__content {
      padding: 10px 0;
    }

    .user-dropdown__item {
      display: flex;
      align-items: center;
      padding: 12px 20px;
      color: #333;
      text-decoration: none;
      transition: all 0.2s ease;
      font-size: 14px;
    }

    .user-dropdown__item:hover {
      background: #f8f9fa;
      color: #7c3aed;
    }

    .user-dropdown__item--logout {
      color: #dc3545;
    }

    .user-dropdown__item--logout:hover {
      background: #fff5f5;
      color: #dc3545;
    }

    .user-dropdown__divider {
      height: 1px;
      background: #f1f1f1;
      margin: 10px 0;
    }

    .cursor-pointer {
      cursor: pointer;
    }

    /* Mobile responsive adjustments */
    @media (max-width: 1199px) {
      .user-dropdown__menu {
        right: -50px;
        min-width: 220px;
      }
    }

    /* RTL Menu Spacing Fixes - More Specific */
    [dir="rtl"] .header-right__buttons {
      flex-direction: row-reverse;
      gap: 20px;
    }

    [dir="rtl"] .header-right__buttons .button {
      margin-left: 0 !important;
      margin-right: 0 !important;
    }

    [dir="rtl"] .header-right__buttons .button.-underline {
      margin-left: 0 !important;
      margin-right: 0 !important;
    }

    [dir="rtl"] .header-right__buttons .button.-sm {
      margin-left: 0 !important;
      margin-right: 0 !important;
    }

    /* RTL Specific Button Spacing */
    [dir="rtl"] .header-right__buttons a[href*="login"] {
      margin-left: 0 !important;
      margin-right: 20px !important;
    }

    [dir="rtl"] .header-right__buttons a[href*="signup"] {
      margin-left: 0 !important;
      margin-right: 0 !important;
    }

    /* RTL Language Switcher Spacing */
    [dir="rtl"] .header-right__buttons .ml-20 {
      margin-left: 0 !important;
      margin-right: 10px !important;
    }

    [dir="rtl"] .header-right__buttons .ml-30 {
      margin-left: 0 !important;
      margin-right: 15px !important;
    }

    /* RTL Language Switcher Position - Next to Cart */
    [dir="rtl"] .language-switcher {
      margin-left: 0 !important;
      margin-right: 8px !important;
    }

    [dir="rtl"] .header-right__buttons .language-switcher {
      margin-left: 0 !important;
      margin-right: 8px !important;
    }

    /* RTL Language Switcher in Header Right Icons */
    [dir="rtl"] .header-right__icons .language-switcher {
      margin-left: 0 !important;
      margin-right: 8px !important;
    }

    /* RTL Cart Icon Spacing */
    [dir="rtl"] .header-right__icons .icon-basket {
      margin-left: 0 !important;
      margin-right: 20px !important;
    }

    /* Hide Signup button in RTL */
    [dir="rtl"] .header-right__buttons a[href*="signup"] {
      display: none !important;
    }

    [dir="rtl"] .header-right__buttons .button[href*="signup"] {
      display: none !important;
    }

    /* Hide Signup button in mobile menu RTL */
    [dir="rtl"] .d-none.xl\\:d-flex a[href*="signup"] {
      display: none !important;
    }

    /* RTL Header Right Container */
    [dir="rtl"] .header-right {
      flex-direction: row-reverse;
    }

    [dir="rtl"] .header-right__icons {
      margin-left: 0;
      margin-right: 20px;
    }

    /* RTL Main Menu Spacing - Force Small Spacing */
    [dir="rtl"] .menu__nav {
      flex-direction: row; /* Keep normal order */
      margin-right: 0 !important;
      margin-left: 30px !important; /* Increase space between logo and menu */
    }

    /* RTL Header Menu Container - Reduce spacing from logo */
    [dir="rtl"] .header-menu {
      margin-right: 0 !important;
      margin-left: 30px !important;
    }

    /* RTL Header Left - Reduce spacing */
    [dir="rtl"] .header-left {
      margin-right: 0 !important;
    }

    [dir="rtl"] .header__logo {
      margin-right: 0 !important;
    }

    [dir="rtl"] .menu__nav li {
      margin-left: 12px !important;
      margin-right: 0 !important;
      padding-left: 0;
      padding-right: 0;
    }

    [dir="rtl"] .menu__nav li:first-child {
      margin-left: 0 !important;
    }

    [dir="rtl"] .menu__nav li:last-child {
      margin-right: 0 !important;
    }

    /* Force small spacing between all menu items */
    [dir="rtl"] .menu__nav li + li {
      margin-left: 12px !important;
    }

    [dir="rtl"] .menu__nav li:nth-child(5) {
      margin-left: 20px !important;
    }

    [dir="rtl"] .menu__nav li:nth-child(6) {
      margin-left: 20px !important;
    }

    [dir="rtl"] .menu__nav li:nth-child(4) {
      margin-left: 20px !important;
    }

    [dir="rtl"] .menu__nav li:nth-child(5) {
      margin-left: 20px !important;
      margin-left: 8px !important;
    }

    [dir="rtl"] .menu__nav li:nth-child(6) {
      margin-left: 8px !important;
    }

    /* RTL Menu Icons Spacing */
    [dir="rtl"] .menu__nav .icon-chevron-right {
      margin-left: 0 !important;
      margin-right: 10px !important;
      display: inline-block;
      width: 0;
      height: 0;
      border-style: solid;
      border-width: 4px 0 4px 6px;
      border-color: transparent transparent transparent currentColor;
      font-size: 0;
      line-height: 0;
      vertical-align: middle;
    }

    [dir="rtl"] .menu__nav .icon-chevron-right::before {
      content: none !important;
    }

    [dir="rtl"] .menu__nav .icon-chevron-left {
      margin-right: 0 !important;
      margin-left: 10px !important;
      display: inline-block;
      width: 0;
      height: 0;
      border-style: solid;
      border-width: 4px 6px 4px 0;
      border-color: transparent currentColor transparent transparent;
      font-size: 0;
      line-height: 0;
      vertical-align: middle;
    }

    [dir="rtl"] .menu__nav .icon-chevron-left::before {
      content: none !important;
    }

    /* RTL Submenu Spacing */
    [dir="rtl"] .subnav li {
      padding-right: 0;
      padding-left: 20px;
    }

    [dir="rtl"] .subnav .menu__backButton {
      padding-left: 20px;
      padding-right: 0;
    }

    /* RTL Submenu Hover Effects */
    [dir="rtl"] .header .header-menu .menu .subnav > li > a::before {
      left: auto;
      right: 0;
      border-radius: 0 3px 3px 0;
    }

    [dir="rtl"] .header .header-menu .menu .subnav > li > a:hover {
      transform: translateX(-4px);
    }

    /* Menu Position Adjustment - English (LTR) only */
    @media (min-width: 1200px) {
      .header .header-menu .menu {
        position: absolute;
        left: 38%;
        transform: translateX(-50%);
      }

      .menu__nav > li {
        white-space: nowrap;
      }
    }
  </style>

  @if(app()->getLocale() == 'ar')
  <style>
    /* Arabic (RTL) - Position menu properly for RTL layout */
    @media (min-width: 1200px) {
      .header .header-menu .menu {
        left: auto !important;
        right: 42% !important;
        transform: translateX(50%) !important;
      }

      [dir="rtl"] .menu__nav > li > a {
        font-size: 14px !important;
      }
    }
  </style>
  @endif

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Close dropdown when clicking outside
      document.addEventListener('click', function(event) {
        const dropdown = document.querySelector('.user-dropdown');
        if (dropdown && !dropdown.contains(event.target)) {
          // Remove any active states if needed
        }
      });
    });
  </script>
  <div class="header__container">
    <div class="row justify-between items-center">
      <div class="col-auto">
        <div class="header-left">
          <div class="header__logo">
            <a data-barba href="{{ route('accueil.index') }}">
              <img src="{{ asset('assets/img/general/logo-upskills.svg') }}" alt="Upskills Academy" style="max-height: 50px;">
            </a>
          </div>
        </div>
      </div>

      <div class="header-menu js-mobile-menu-toggle">
        <div class="header-menu__content">
          <div class="mobile-bg js-mobile-bg"></div>

          <div class="d-none xl:d-flex items-center px-20 py-20 border-bottom-light">
            @auth
              <a href="{{ route('student-dashboard.index') }}" class="d-flex items-center text-decoration-none">
                <div class="user-avatar mr-15">
                  <div class="avatar-circle">
                    {{ strtoupper(substr(Auth::user()->first_name ?? 'U', 0, 1)) }}
                  </div>
                </div>
                <span class="text-dark-1 fw-500">{{ Auth::user()->first_name ?? 'User' }}</span>
              </a>
            @else
              <a href="{{ route('login') }}" class="text-dark-1">{{ __('messages.login') }}</a>
              <a href="{{ route('signup') }}" class="text-dark-1 ml-30">{{ __('messages.signup') }}</a>
            @endauth
          </div>

          <div class="menu js-navList">
            <ul class="menu__nav text-white -is-active">
              <!-- Why the Academy -->
              <li class="menu-item-has-children">
                <a data-barba href="#">
                  {{ __('messages.why_academy') }} <i class="icon-chevron-right text-13 ml-10"></i>
                </a>
                <ul class="subnav">
                  <li class="menu__backButton js-nav-list-back">
                    <a href="#"><i class="icon-chevron-left text-13 mr-10"></i> {{ __('messages.why_academy') }}</a>
                  </li>
                  @php
                    $pages = \App\Http\Controllers\PageController::getMenuPages();
                  @endphp
                  @forelse($pages as $page)
                    <li><a href="{{ route('pages.show', $page->slug) }}">{{ $page->titre }}</a></li>
                  @empty
                    <li><a href="#">{{ __('messages.who_we_are') }}</a></li>
                    <li><a href="#">{{ __('messages.our_goals') }}</a></li>
                    <li><a href="#">{{ __('messages.our_strategy') }}</a></li>
                    <li><a href="#">{{ __('messages.international_accreditation') }}</a></li>
                    <li><a href="#">{{ __('messages.our_advantages') }}</a></li>
                    <li><a href="#">{{ __('messages.usage_policy') }}</a></li>

                                     @endforelse

                   <li><a href="{{ route('articles.index') }}">{{ __('messages.articles') }}</a></li>
                   <li><a href="{{ route('blog.index') }}">{{ __('messages.news') }}</a></li>
                </ul>
              </li>

              <!-- Courses -->
              <li class="menu-item-has-children">
                <a data-barba href="#">
                  {{ __('messages.courses') }} <i class="icon-chevron-right text-13 ml-10"></i>
                </a>
                <ul class="subnav">
                  <li class="menu__backButton js-nav-list-back">
                    <a href="#"><i class="icon-chevron-left text-13 mr-10"></i> {{ __('messages.courses') }}</a>
                  </li>
                  <li><a href="{{ route('filter.index') }}">{{ __('messages.all_courses') }}</a></li>
                  <li><a href="{{ route('packages.index') }}">{{ __('messages.packages') }}</a></li>
                  @foreach($categories as $category)
                  <li><a href="{{ route('filter.index', ['category' => $category->id]) }}">{{ $category->titre }}</a></li>
                  @endforeach
                </ul>
              </li>

              <!-- Library -->
              <li>
                <a data-barba href="{{ route('books.index') }}">
                  {{ __('messages.library') }}
                </a>
              </li>

              <!-- Partnership -->
              <li class="menu-item-has-children">
                <a data-barba href="#">
                  {{ __('messages.partnership') }} <i class="icon-chevron-right text-13 ml-10"></i>
                </a>
                <ul class="subnav">
                  <li class="menu__backButton js-nav-list-back">
                    <a href="#"><i class="icon-chevron-left text-13 mr-10"></i> {{ __('messages.partnership') }}</a>
                  </li>
                  <li><a href="{{ route('partnership.index') }}">{{ __('messages.organizations') }}</a></li>
                  <li><a href="{{ route('partnership-courses.index') }}">{{ __('messages.courses') }}</a></li>

                </ul>
              </li>

              <!-- Contact -->
              <li>
                <a data-barba href="{{ route('contact.index') }}">
                  {{ __('messages.contact_us') }}
                </a>
              </li>
            </ul>
          </div>

          <div class="mobile-footer px-20 py-20 border-top-light js-mobile-footer">
            <!-- Language Switcher in Mobile Menu -->
            <div class="mobile-language-switcher">
              @include('layouts.language-switcher')
            </div>

            <!-- Currency Switcher in Mobile Menu -->
            <div class="mobile-currency-switcher mt-10">
              @include('layouts.currency-switcher')
            </div>

            <div class="mobile-socials mt-10">
              <a href="#" class="d-flex items-center justify-center rounded-full size-40">
                <i class="fa fa-facebook"></i>
              </a>
              <a href="#" class="d-flex items-center justify-center rounded-full size-40">
                <i class="fa fa-twitter"></i>
              </a>
              <a href="#" class="d-flex items-center justify-center rounded-full size-40">
                <i class="fa fa-instagram"></i>
              </a>
              <a href="#" class="d-flex items-center justify-center rounded-full size-40">
                <i class="fa fa-linkedin"></i>
              </a>
            </div>
          </div>
        </div>

        <div class="header-menu-close" data-el-toggle=".js-mobile-menu-toggle">
          <div class="size-40 d-flex items-center justify-center rounded-full bg-white">
            <div class="icon-close text-dark-1 text-16"></div>
          </div>
        </div>

        <div class="header-menu-bg"></div>
      </div>

      <div class="col-auto">
        <div class="header-right d-flex items-center" style="flex-shrink: 0; position: relative; z-index: 10;">
          <div class="header-right__icons text-white d-flex items-center" style="gap: 12px;">
            <!-- Language Switcher - Next to Cart (Hidden on mobile) -->
            <div class="xl:d-none">
              @include('layouts.language-switcher')
            </div>

            <!-- Currency Switcher - Next to Language (Hidden on mobile) -->
            <div class="xl:d-none">
              @include('layouts.currency-switcher')
            </div>

            <!-- Cart Icon -->
            <div class="ml-20">
              <a href="{{ route('cart.index') }}" class="d-flex items-center text-white position-relative js-cart-icon">
                <i class="text-20 icon icon-basket"></i>
                @php
                  $cartCount = 0;
                  if (Auth::check()) {
                    $cartCount = \App\Models\Cart::where('student_id', Auth::id())->sum('quantity');
                  } else {
                    $cartCount = session('cart_count', 0);
                  }
                @endphp
                @if($cartCount > 0)
                  <span class="cart-badge">{{ $cartCount }}</span>
                @endif
              </a>
            </div>

            <div class="d-none xl:d-block ml-20">
              <button class="text-white items-center" data-el-toggle=".js-mobile-menu-toggle">
                <i class="text-11 icon icon-mobile-menu"></i>
              </button>
            </div>
          </div>

                     <div class="header-right__buttons d-flex items-center ml-15 md:d-none">
           @auth
             <!-- User Profile Section with Dropdown -->
             <div class="d-flex items-center">
               <div class="user-dropdown">
                 <div class="user-dropdown__trigger d-flex items-center cursor-pointer">
                   <div class="user-avatar mr-15">
                     <div class="avatar-circle">
                       {{ strtoupper(substr(Auth::user()->first_name ?? 'U', 0, 1)) }}
                     </div>
                   </div>
                   <span class="text-white fw-500 mr-10">{{ Auth::user()->first_name ?? 'User' }}</span>
                   <i class="icon-chevron-down text-12 text-white"></i>
                 </div>

                 <div class="user-dropdown__menu">
                   <div class="user-dropdown__header">
                     <div class="user-info">
                       <div class="user-avatar mb-10">
                         <div class="avatar-circle">
                           {{ strtoupper(substr(Auth::user()->first_name ?? 'U', 0, 1)) }}
                         </div>
                       </div>
                       <div class="text-14 fw-600 text-dark-1">{{ Auth::user()->first_name ?? 'User' }}</div>
                       <div class="text-12 text-light-1">{{ Auth::user()->email ?? '' }}</div>
                     </div>
                   </div>

                   <div class="user-dropdown__content">
                     <a href="{{ route('student-dashboard.index') }}" class="user-dropdown__item">
                       <i class="icon-dashboard text-16 mr-10"></i>
                       {{ __('messages.dashboard') }}
                     </a>
                     <a href="{{ route('student-dashboard.profile') }}" class="user-dropdown__item">
                       <i class="icon-user text-16 mr-10"></i>
                       {{ __('messages.profile') }}
                     </a>
                     <a href="{{ route('student-dashboard.courses.index') }}" class="user-dropdown__item">
                       <i class="icon-play text-16 mr-10"></i>
                       {{ __('messages.my_courses') }}
                     </a>
                     <a href="{{ route('student-dashboard.certificates.index') }}" class="user-dropdown__item">
                       <i class="icon-certificate text-16 mr-10"></i>
                       {{ __('messages.certificates') }}
                     </a>
                     <div class="user-dropdown__divider"></div>
                     <a href="{{ route('logout') }}" class="user-dropdown__item user-dropdown__item--logout"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                       <i class="icon-logout text-16 mr-10"></i>
                       {{ __('messages.logout') }}
                     </a>
                   </div>
                 </div>
               </div>

               <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                 @csrf
               </form>
             </div>
          @else
            <a href="{{ route('login') }}" class="button -underline text-white ml-20">{{ __('messages.login') }}</a>
            <a href="{{ route('signup') }}" class="button -sm -white text-dark-1 ml-30">{{ __('messages.signup') }}</a>
          @endauth
           </div>
        </div>
      </div>
    </div>
  </div>
</header>
