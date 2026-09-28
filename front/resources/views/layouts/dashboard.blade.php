<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-JB2GJ0KWED"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-JB2GJ0KWED');
  </script>

  <!-- Meta Pixel Code -->
  <script>
  !function(f,b,e,v,n,t,s)
  {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
  n.callMethod.apply(n,arguments):n.queue.push(arguments)};
  if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
  n.queue=[];t=b.createElement(e);t.async=!0;
  t.src=v;s=b.getElementsByTagName(e)[0];
  s.parentNode.insertBefore(t,s)}(window, document,'script',
  'https://connect.facebook.net/en_US/fbevents.js');
  fbq('init', '1902058279805929');
  fbq('track', 'PageView');
  </script>
  <noscript><img height="1" width="1" style="display:none"
  src="https://www.facebook.com/tr?id=1902058279805929&ev=PageView&noscript=1"
  /></noscript>
  <!-- End Meta Pixel Code -->

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Material+Icons+Outlined" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin="" />
  <link rel="stylesheet" href="{{ asset('assets/css/vendors.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/dashboard-custom.css') }}">
  <link rel="stylesheet" href="{{ asset('css/notification-badge.css') }}">
  <title>{{ __('messages.dashboard') }} - {{ __('messages.swedish_academy') }}</title>
  @if(app()->getLocale() == 'ar')
  <style>
    body {
      direction: rtl;
      font-family: 'Cairo', sans-serif;
    }
    .text-left { text-align: right !important; }
    .text-right { text-align: left !important; }
    .ml-auto { margin-left: 0 !important; margin-right: auto !important; }
    .mr-auto { margin-right: 0 !important; margin-left: auto !important; }
    .icon-chevron-right::before { content: "\f053" !important; }
    .icon-chevron-left::before { content: "\f054" !important; }
    .icon-arrow-right::before { content: "\f060" !important; }
    .icon-arrow-left::before { content: "\f061" !important; }

    /* Fix sidebar menu icon spacing for RTL - remove right margin, add left margin */
    html[dir="rtl"] .sidebar__item a i,
    html[dir="rtl"] .menu__nav a i,
    html[dir="rtl"] .sidebar__item button i,
    html[dir="rtl"] .menu__nav li a i,
    html[dir="rtl"] .btn-modern i,
    html[dir="rtl"] .quick-actions a i {
      margin-right: 0 !important;
      margin-left: 10px !important;
    }

    /* Fix sidebar link alignment for RTL - reverse the flex direction */
    html[dir="rtl"] .sidebar__item a.d-flex,
    html[dir="rtl"] .sidebar__item button.d-flex,
    html[dir="rtl"] .menu__nav li a,
    html[dir="rtl"] .btn-modern,
    html[dir="rtl"] .quick-actions a {
      flex-direction: row-reverse !important;
      justify-content: flex-start !important;
      text-align: right !important;
    }
  </style>
  @endif
</head>

<body class="preloader-visible" data-barba="wrapper">
  <div class="preloader js-preloader">
    <div class="preloader__bg"></div>
  </div>

  <div class="barba-container" data-barba="container">
    <main class="main-content">
      <!-- Header Moderne -->
      <header class="header -dashboard -dark-bg-dark-1 js-header" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
        <div class="header__container">
          <div class="row justify-between items-center">
            <div class="col-auto">
              <div class="d-flex items-center">
                <div class="header__explore">
                  <button class="d-flex items-center js-dashboard-home-9-sidebar-toggle">
                    <i class="icon -dark-text-white icon-explore"></i>
                  </button>
                </div>

                                 <div class="header__logo ml-30 md:ml-20">
                   <a data-barba href="{{ route('accueil.index') }}">
                     <img src="{{ asset('assets/img/general/logo-upskills.svg') }}" alt="Upskills Academy" style="max-height: 45px;">
                   </a>
                 </div>
              </div>
            </div>

            <div class="col-auto">
              <div class="d-flex items-center">
                <!-- Mobile Menu Toggle -->
                <div class="text-white d-flex items-center lg:d-none mr-15">
                  <button class="text-white items-center" data-el-toggle=".js-dashboard-mobile-menu-toggle">
                    <i class="text-11 icon icon-mobile-menu"></i>
                  </button>
                </div>

                <!-- Search -->
                <div class="header-right__icons text-white d-flex items-center">
                  <div class="">
                    <button class="d-flex items-center text-white" data-el-toggle=".js-search-toggle">
                      <i class="text-20 icon icon-search"></i>
                    </button>

                    <div class="toggle-element js-search-toggle">
                      <div class="header-search pt-90 bg-white shadow-4">
                        <div class="container">
                          <div class="header-search__field">
                            <div class="icon icon-search text-dark-1"></div>
                            <input type="text" class="col-12 text-18 lh-12 text-dark-1 fw-500" placeholder="{{ __('messages.search_placeholder') }}">
                            <button class="d-flex items-center justify-center size-40 rounded-full bg-purple-3" data-el-toggle=".js-search-toggle">
                              <img src="{{ asset('assets/img/menus/close.svg') }}" alt="icon">
                            </button>
                          </div>
                        </div>
                      </div>
                      <div class="header-search__bg" data-el-toggle=".js-search-toggle"></div>
                    </div>
                  </div>
                </div>

                <!-- Notifications -->
                <div class="relative ml-20">
                  <a href="#" class="d-flex items-center text-light-1 justify-center size-50 rounded-16 -hover-dshb-header-light" data-el-toggle=".js-notif-toggle">
                    <i class="text-24 icon icon-notification"></i>
                    @if($pendingEvaluationsCount > 0)
                      <span class="notification-badge">{{ $pendingEvaluationsCount }}</span>
                    @endif
                  </a>

                  <!-- Notification Dropdown -->
                  <div class="toggle-element js-notif-toggle">
                    <div class="toggle-bottom -notifications bg-white -dark-bg-dark-1 shadow-4 border-light rounded-8 mt-10" style="min-width: 350px;">
                      @if($pendingEvaluationsCount > 0)
                        <div class="px-30 py-20 border-bottom-light">
                          <div class="d-flex justify-between items-center">
                            <h5 class="text-16 fw-500">Pending Evaluations</h5>
                            <span class="badge bg-danger text-white px-10 py-5 rounded-8">{{ $pendingEvaluationsCount }}</span>
                          </div>
                        </div>
                        <div class="py-10" style="max-height: 400px; overflow-y: auto;">
                          @foreach($pendingEvaluations as $product)
                            <a href="{{ route('courses.show', ['locale' => app()->getLocale(), 'product' => $product->id]) }}#rate-course" class="d-flex items-start px-30 py-15 -hover-bg-light-4 text-decoration-none">
                              <div class="flex-grow-1">
                                <div class="text-14 fw-500 text-dark-1 mb-5">
                                  {{ $product->current_variation ? $product->current_variation->name : $product->titre }}
                                </div>
                                <div class="text-12 text-light-1">
                                  <i class="icon-star text-yellow-1 mr-5"></i>
                                  Please rate this course
                                </div>
                              </div>
                              <div class="ml-10">
                                <i class="icon-arrow-right text-14 text-purple-1"></i>
                              </div>
                            </a>
                          @endforeach
                        </div>
                      @else
                        <div class="px-30 py-30 text-center">
                          <i class="icon-check text-40 text-success mb-10"></i>
                          <div class="text-14 text-light-1">No pending evaluations</div>
                        </div>
                      @endif
                    </div>
                  </div>
                </div>

                <!-- Language Switcher -->
                <div class="ml-20">
                  @include('layouts.language-switcher')
                </div>

                <!-- Profile Menu -->
                <div class="relative d-flex items-center ml-10">
                  <a href="#" data-el-toggle=".js-profile-toggle">
                    <img class="size-50" src="{{ Auth::user()->image_url }}" alt="profile">
                  </a>

                  <div class="toggle-element js-profile-toggle">
                    <div class="toggle-bottom -profile bg-white -dark-bg-dark-1 shadow-4 border-light rounded-8 mt-10">
                      <div class="px-30 py-30">
                        <div class="sidebar -dashboard">
                          <div class="sidebar__item {{ request()->routeIs('student-dashboard.index') ? '-is-active -dark-bg-dark-2' : '' }}">
                            <a href="{{ route('student-dashboard.index') }}" class="d-flex items-center text-17 lh-1 fw-500 {{ request()->routeIs('student-dashboard.index') ? '-dark-text-white' : '' }}">
                              <i class="text-20 icon-discovery mr-15"></i>
                              {{ __('messages.dashboard') }}
                            </a>
                          </div>

                          <div class="sidebar__item {{ request()->routeIs('student-dashboard.courses.*') ? '-is-active -dark-bg-dark-2' : '' }}">
                            <a href="{{ route('student-dashboard.courses.index') }}" class="d-flex items-center text-17 lh-1 fw-500 {{ request()->routeIs('student-dashboard.courses.*') ? '-dark-text-white' : '' }}">
                              <i class="text-20 icon-play-button mr-15"></i>
                              {{ __('messages.my_courses') }}
                            </a>
                          </div>

                          <div class="sidebar__item {{ request()->routeIs('student-dashboard.books.*') ? '-is-active -dark-bg-dark-2' : '' }}">
                            <a href="{{ route('student-dashboard.books.index') }}" class="d-flex items-center text-17 lh-1 fw-500 {{ request()->routeIs('student-dashboard.books.*') ? '-dark-text-white' : '' }}">
                              <i class="text-20 icon-bookmark mr-15"></i>
                              {{ __('messages.my_books') }}
                            </a>
                          </div>

                          <div class="sidebar__item {{ request()->routeIs('student-dashboard.certificates.*') ? '-is-active -dark-bg-dark-2' : '' }}">
                            <a href="{{ route('student-dashboard.certificates.index') }}" class="d-flex items-center text-17 lh-1 fw-500 {{ request()->routeIs('student-dashboard.certificates.*') ? '-dark-text-white' : '' }}">
                              <i class="text-20 icon-award mr-15"></i>
                              {{ __('messages.certificates') }}
                            </a>
                          </div>

                          <div class="sidebar__item {{ request()->routeIs('student-dashboard.course-schedule') ? '-is-active -dark-bg-dark-2' : '' }}">
                            <a href="{{ route('student-dashboard.course-schedule') }}" class="d-flex items-center text-17 lh-1 fw-500 {{ request()->routeIs('student-dashboard.course-schedule') ? '-dark-text-white' : '' }}">
                              <i class="text-20 icon-calendar mr-15"></i>
                              {{ __('messages.course_schedule') }}
                            </a>
                          </div>

                          <div class="sidebar__item {{ request()->routeIs('student-dashboard.payment.*') ? '-is-active -dark-bg-dark-2' : '' }}">
                            <a href="{{ route('student-dashboard.payment.index') }}" class="d-flex items-center text-17 lh-1 fw-500 {{ request()->routeIs('student-dashboard.payment.*') ? '-dark-text-white' : '' }}">
                              <i class="text-20 icon-credit-card mr-15"></i>
                              {{ __('messages.payments') }}
                            </a>
                          </div>

                          <div class="sidebar__item {{ request()->routeIs('student-dashboard.support.*') ? '-is-active -dark-bg-dark-2' : '' }}">
                            <a href="{{ route('student-dashboard.support.index') }}" class="d-flex items-center text-17 lh-1 fw-500 {{ request()->routeIs('student-dashboard.support.*') ? '-dark-text-white' : '' }}">
                              <i class="text-20 icon-message mr-15"></i>
                              {{ __('messages.support_tickets') }}
                            </a>
                          </div>

                          <div class="sidebar__item {{ request()->routeIs('student.messages.*') ? '-is-active -dark-bg-dark-2' : '' }}">
                            <a href="{{ route('student.messages.inbox') }}" class="d-flex items-center text-17 lh-1 fw-500 {{ request()->routeIs('student.messages.*') ? '-dark-text-white' : '' }} position-relative">
                              <i class="text-20 icon-mail mr-15"></i>
                              {{ __('messages.messages') }}
                              @php
                                $unreadCount = DB::table('message_recipients')->where('student_id', Auth::id())->where('is_read', 0)->count();
                              @endphp
                              @if($unreadCount > 0)
                                <span class="badge badge-primary ms-2" style="font-size: 10px; padding: 2px 6px;">{{ $unreadCount }}</span>
                              @endif
                            </a>
                          </div>

                          <div class="sidebar__item {{ request()->routeIs('coupons.*') ? '-is-active -dark-bg-dark-2' : '' }}">
                            <a href="{{ route('coupons.index') }}" class="d-flex items-center text-17 lh-1 fw-500 {{ request()->routeIs('coupons.*') ? '-dark-text-white' : '' }}">
                              <i class="text-20 icon-tag mr-15"></i>
                              {{ __('coupon.special_offers') }}
                            </a>
                          </div>

                          <div class="sidebar__item {{ request()->routeIs('student-dashboard.profile') ? '-is-active -dark-bg-dark-2' : '' }}">
                            <a href="{{ route('student-dashboard.profile') }}" class="d-flex items-center text-17 lh-1 fw-500 {{ request()->routeIs('student-dashboard.profile') ? '-dark-text-white' : '' }}">
                              <i class="text-20 icon-setting mr-15"></i>
                              {{ __('messages.settings') }}
                            </a>
                          </div>

                          <div class="sidebar__item">
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                              @csrf
                              <button type="submit" class="d-flex items-center text-17 lh-1 fw-500 w-100 text-left bg-transparent border-0">
                                <i class="text-20 icon-power mr-15"></i>
                                {{ __('messages.logout') }}
                              </button>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </header>

      <!-- Content -->
      <div class="content-wrapper js-content-wrapper">
        <div class="dashboard -home-9 js-dashboard-home-9">
          <!-- Desktop Sidebar Moderne -->
          <div class="dashboard__sidebar scroll-bar-1">
            <div class="sidebar -dashboard">
              <!-- Logo dans la sidebar -->
              <div class="sidebar-header text-center py-20 mb-20">
                <img src="{{ asset('assets/img/general/logo-upskills.svg') }}" alt="Logo" style="max-height: 40px; filter: none;">
              </div>

              <div class="sidebar__item {{ request()->routeIs('student-dashboard.index') ? '-is-active -dark-bg-dark-2' : '' }}">
                <a href="{{ route('student-dashboard.index') }}" class="d-flex items-center text-17 lh-1 fw-500 {{ request()->routeIs('student-dashboard.index') ? '-dark-text-white' : '' }}">
                  <i class="icon-discovery mr-15"></i>
                  {{ __('messages.dashboard') }}
                </a>
              </div>

              <div class="sidebar__item {{ request()->routeIs('student-dashboard.courses.*') ? '-is-active -dark-bg-dark-2' : '' }}">
                <a href="{{ route('student-dashboard.courses.index') }}" class="d-flex items-center text-17 lh-1 fw-500 {{ request()->routeIs('student-dashboard.courses.*') ? '-dark-text-white' : '' }}">
                  <i class="icon-play-button mr-15"></i>
                  {{ __('messages.my_courses') }}
                </a>
              </div>

              <div class="sidebar__item {{ request()->routeIs('student-dashboard.books.*') ? '-is-active -dark-bg-dark-2' : '' }}">
                <a href="{{ route('student-dashboard.books.index') }}" class="d-flex items-center text-17 lh-1 fw-500 {{ request()->routeIs('student-dashboard.books.*') ? '-dark-text-white' : '' }}">
                  <i class="icon-bookmark mr-15"></i>
                  {{ __('messages.my_books') }}
                </a>
              </div>

              <div class="sidebar__item {{ request()->routeIs('student-dashboard.certificates.*') ? '-is-active -dark-bg-dark-2' : '' }}">
                <a href="{{ route('student-dashboard.certificates.index') }}" class="d-flex items-center text-17 lh-1 fw-500 {{ request()->routeIs('student-dashboard.certificates.*') ? '-dark-text-white' : '' }}">
                  <i class="icon-award mr-15"></i>
                  {{ __('messages.certificates') }}
                </a>
              </div>

              <div class="sidebar__item {{ request()->routeIs('student-dashboard.course-schedule') ? '-is-active -dark-bg-dark-2' : '' }}">
                <a href="{{ route('student-dashboard.course-schedule') }}" class="d-flex items-center text-17 lh-1 fw-500 {{ request()->routeIs('student-dashboard.course-schedule') ? '-dark-text-white' : '' }}">
                  <i class="icon-calendar mr-15"></i>
                  {{ __('messages.course_schedule') }}
                </a>
              </div>

              <div class="sidebar__item {{ request()->routeIs('student-dashboard.payment.*') ? '-is-active -dark-bg-dark-2' : '' }}">
                <a href="{{ route('student-dashboard.payment.index') }}" class="d-flex items-center text-17 lh-1 fw-500 {{ request()->routeIs('student-dashboard.payment.*') ? '-dark-text-white' : '' }}">
                  <i class="icon-credit-card mr-15"></i>
                  {{ __('messages.payments') }}
                </a>
              </div>

              <div class="sidebar__item {{ request()->routeIs('student-dashboard.support.*') ? '-is-active -dark-bg-dark-2' : '' }}">
                <a href="{{ route('student-dashboard.support.index') }}" class="d-flex items-center text-17 lh-1 fw-500 {{ request()->routeIs('student-dashboard.support.*') ? '-dark-text-white' : '' }}">
                  <i class="icon-message mr-15"></i>
                  {{ __('messages.support_tickets') }}
                </a>
              </div>

              <div class="sidebar__item {{ request()->routeIs('student.messages.*') ? '-is-active -dark-bg-dark-2' : '' }}">
                <a href="{{ route('student.messages.inbox') }}" class="d-flex items-center text-17 lh-1 fw-500 {{ request()->routeIs('student.messages.*') ? '-dark-text-white' : '' }} position-relative">
                  <i class="icon-mail mr-15"></i>
                  {{ __('messages.messages') }}
                  @php
                    $unreadCount = DB::table('message_recipients')->where('student_id', Auth::id())->where('is_read', 0)->count();
                  @endphp
                  @if($unreadCount > 0)
                    <span class="badge badge-primary ms-2" style="font-size: 10px; padding: 2px 6px;">{{ $unreadCount }}</span>
                  @endif
                </a>
              </div>

              <div class="sidebar__item {{ request()->routeIs('coupons.*') ? '-is-active -dark-bg-dark-2' : '' }}">
                <a href="{{ route('coupons.index') }}" class="d-flex items-center text-17 lh-1 fw-500 {{ request()->routeIs('coupons.*') ? '-dark-text-white' : '' }}">
                  <i class="icon-tag mr-15"></i>
                  {{ __('coupon.special_offers') }}
                </a>
              </div>

              <div class="sidebar__item {{ request()->routeIs('student-dashboard.referrals.*') ? '-is-active -dark-bg-dark-2' : '' }}">
                <a href="{{ route('student-dashboard.referrals.index') }}" class="d-flex items-center text-17 lh-1 fw-500 {{ request()->routeIs('student-dashboard.referrals.*') ? '-dark-text-white' : '' }}">
                  <i class="fas fa-user-friends mr-15" style="font-size: 18px;"></i>
                  {{ __('messages.referrals') }}
                </a>
              </div>

              <div class="sidebar__item {{ request()->routeIs('student-dashboard.profile') ? '-is-active -dark-bg-dark-2' : '' }}">
                <a href="{{ route('student-dashboard.profile') }}" class="d-flex items-center text-17 lh-1 fw-500 {{ request()->routeIs('student-dashboard.profile') ? '-dark-text-white' : '' }}">
                  <i class="icon-setting mr-15"></i>
                  {{ __('messages.settings') }}
                </a>
              </div>

              <!-- Séparateur -->
              <div class="sidebar-divider my-20 mx-15"></div>

              <!-- Informations utilisateur -->
              <div class="sidebar-user-info px-15 py-15">
                <div class="d-flex items-center">
                  <img class="size-40 rounded-full mr-10" src="{{ Auth::user()->image_url }}" alt="profile">
                  <div class="flex-grow-1">
                    <div class="text-14 fw-500 text-dark-1">{{ Auth::user()->full_name }}</div>
                    <div class="text-12 text-light-1">{{ Auth::user()->email }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Mobile Menu Moderne -->
          <div class="dashboard-mobile-menu js-dashboard-mobile-menu-toggle">
            <div class="dashboard-mobile-menu__content">
              <div class="dashboard-mobile-menu__header">
                <h3>{{ __('messages.student_dashboard') }}</h3>
              </div>

              <div class="dashboard-mobile-menu__nav">
                <div class="menu js-navList">
                  <ul class="menu__nav text-white -is-active">
                    <li class="{{ request()->routeIs('student-dashboard.index') ? '-is-active' : '' }}">
                      <a href="{{ route('student-dashboard.index') }}">
                        <i class="text-20 icon-discovery mr-15"></i>
                        {{ __('messages.dashboard') }}
                      </a>
                    </li>

                    <li class="{{ request()->routeIs('student-dashboard.courses.*') ? '-is-active' : '' }}">
                      <a href="{{ route('student-dashboard.courses.index') }}">
                        <i class="text-20 icon-play-button mr-15"></i>
                        {{ __('messages.my_courses') }}
                      </a>
                    </li>

                    <li class="{{ request()->routeIs('student-dashboard.books.*') ? '-is-active' : '' }}">
                      <a href="{{ route('student-dashboard.books.index') }}">
                        <i class="text-20 icon-bookmark mr-15"></i>
                        {{ __('messages.my_books') }}
                      </a>
                    </li>

                    <li class="{{ request()->routeIs('student-dashboard.certificates.*') ? '-is-active' : '' }}">
                      <a href="{{ route('student-dashboard.certificates.index') }}">
                        <i class="text-20 icon-award mr-15"></i>
                        {{ __('messages.certificates') }}
                      </a>
                    </li>

                    <li class="{{ request()->routeIs('student-dashboard.payment.*') ? '-is-active' : '' }}">
                      <a href="{{ route('student-dashboard.payment.index') }}">
                        <i class="text-20 icon-credit-card mr-15"></i>
                        {{ __('messages.payments') }}
                      </a>
                    </li>

                    <li class="{{ request()->routeIs('student-dashboard.support.*') ? '-is-active' : '' }}">
                      <a href="{{ route('student-dashboard.support.index') }}">
                        <i class="text-20 icon-message mr-15"></i>
                        {{ __('messages.support_tickets') }}
                      </a>
                    </li>

                    <li class="{{ request()->routeIs('student.messages.*') ? '-is-active' : '' }}">
                      <a href="{{ route('student.messages.inbox') }}" class="position-relative">
                        <i class="text-20 icon-mail mr-15"></i>
                        {{ __('messages.messages') }}
                        @php
                          $unreadCount = DB::connection('mysql_admin')->table('message_recipients')->where('student_id', Auth::id())->where('is_read', 0)->count();
                        @endphp
                        @if($unreadCount > 0)
                          <span class="badge badge-primary ms-2" style="font-size: 10px; padding: 2px 6px;">{{ $unreadCount }}</span>
                        @endif
                      </a>
                    </li>

                    <li class="{{ request()->routeIs('student-dashboard.referrals.*') ? '-is-active' : '' }}">
                      <a href="{{ route('student-dashboard.referrals.index') }}">
                        <i class="fas fa-user-friends mr-15" style="font-size: 18px;"></i>
                        Referrals
                      </a>
                    </li>

                    <li class="{{ request()->routeIs('student-dashboard.profile') ? '-is-active' : '' }}">
                      <a href="{{ route('student-dashboard.profile') }}">
                        <i class="text-20 icon-setting mr-15"></i>
                        {{ __('messages.settings') }}
                      </a>
                    </li>
                  </ul>
                </div>
              </div>

              <div class="mobile-footer px-20 py-20 border-top-light js-mobile-footer">
                <div class="mobile-footer__number">
                  <div class="text-17 fw-500 text-dark-1">{{ __('messages.call_us') }}</div>
                  <div class="text-17 fw-500 text-purple-1">+33 1 23 45 67 89</div>
                </div>

                <div class="lh-2 mt-10">
                  <div>{{ __('messages.swedish_academy') }}<br> {{ __('messages.paris_france') }}</div>
                  <div>contact@up-skills.academy</div>
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

            <div class="dashboard-mobile-menu-close" data-el-toggle=".js-dashboard-mobile-menu-toggle">
              <div class="size-40 d-flex items-center justify-center rounded-full bg-white">
                <div class="icon-close text-dark-1 text-16"></div>
              </div>
            </div>

            <div class="dashboard-mobile-menu-bg"></div>
          </div>

          <div class="dashboard__main">
            <!-- Pending Evaluations Alert Banner -->
            @if(isset($pendingEvaluationsCount) && $pendingEvaluationsCount > 0)
              <div class="alert-banner bg-warning-light border-warning rounded-8 px-30 py-20 mb-30" style="background: linear-gradient(135deg, #FCD116, #fcef4a); border-left: 4px solid #FCD116;">
                <div class="d-flex items-center justify-between">
                  <div class="d-flex items-center">
                    <i class="icon-star text-40 mr-20" style="color: #FCD116;"></i>
                    <div>
                      <h5 class="text-16 fw-600 text-dark-1 mb-5">
                        {{ trans_choice('student_dashboard.courses_awaiting_evaluation', $pendingEvaluationsCount, ['count' => $pendingEvaluationsCount]) }}
                      </h5>
                      <p class="text-14 text-dark-1 mb-0">
                        {{ __('student_dashboard.please_rate_feedback') }}
                      </p>
                    </div>
                  </div>
                  <div>
                    @if(isset($pendingEvaluations) && $pendingEvaluations->count() > 0)
                      @php
                        $firstPendingCourse = $pendingEvaluations->first();
                      @endphp
                      <a href="{{ route('courses.show', ['locale' => app()->getLocale(), 'product' => $firstPendingCourse->id]) }}#rate-course" class="button -sm text-white" style="background-color: #093162;">
                        {{ __('student_dashboard.view_course') }}
                      </a>
                    @else
                      <a href="{{ route('courses.index', ['locale' => app()->getLocale()]) }}" class="button -sm text-white" style="background-color: #093162;">
                        {{ __('student_dashboard.view_courses') }}
                      </a>
                    @endif
                  </div>
                </div>
              </div>
            @endif

            <div class="dashboard__content d-flex flex-column justify-center">
              @yield('content')
            </div>
          </div>
        </div>
      </div>
    </main>


  <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
  <script src="{{ asset('assets/js/vendors.js') }}"></script>
  <script src="{{ asset('assets/js/main.js') }}"></script>
  <script src="{{ asset('assets/js/dashboard-mobile.js') }}"></script>
</body>
</html>
