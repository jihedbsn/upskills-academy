<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
  @include('layouts.head')

  <!-- Custom Styles -->
  @yield('styles')
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
            @yield('content')
        </div>
    </main>

    <!-- Footer (includes AI Chatbot Widget) -->
    @include('layouts.footer')

    <!-- Custom Scripts -->
    @yield('scripts')
</body>
</html>
