<!-- Required meta tags -->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-XXXXXXXXXX');
</script>

<!-- Meta Pixel Code - Deferred to avoid Android "access other apps" prompt -->
<script>
window.loadFacebookPixel = function() {
    if (window.fbPixelLoaded) return;
    window.fbPixelLoaded = true;
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '0000000000000000');
    fbq('track', 'PageView');
};
// Load after user interaction to prevent Android permission prompt
if (document.readyState === 'complete') {
    setTimeout(window.loadFacebookPixel, 3000);
} else {
    window.addEventListener('load', function() {
        setTimeout(window.loadFacebookPixel, 3000);
    });
}
// Also load on first user interaction
['scroll', 'click', 'touchstart', 'keydown'].forEach(function(evt) {
    document.addEventListener(evt, window.loadFacebookPixel, { once: true, passive: true });
});
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=0000000000000000&ev=PageView&noscript=1"
/></noscript>
<!-- End Meta Pixel Code -->

<!-- TikTok Pixel Code -->
{{-- <script>
!function (w, d, t) {
  w.TiktokAnalyticsObject = t;
  var ttq = w[t] = w[t] || [];
  ttq.methods = ["page", "track", "identify", "instances", "debug", "on", "off", "once", "ready", "alias", "group", "enableCookie", "disableCookie", "holdConsent", "revokeConsent", "grantConsent"],
  ttq.setAndDefer = function (t, e) { t[e] = function () { t.push([e].concat(Array.prototype.slice.call(arguments, 0))) } };
  for (var i = 0; i < ttq.methods.length; i++) ttq.setAndDefer(ttq, ttq.methods[i]);
  ttq.instance = function (t) { for (var e = ttq._i[t] || [], n = 0; n < ttq.methods.length; n++) ttq.setAndDefer(e, ttq.methods[n]); return e };
  ttq.load = function (e, n) {
    var r = "https://analytics.tiktok.com/i18n/pixel/events.js", o = n && n.partner;
    ttq._i = ttq._i || {}, ttq._i[e] = [], ttq._i[e]._u = r, ttq._t = ttq._t || {}, ttq._t[e] = +new Date, ttq._o = ttq._o || {}, ttq._o[e] = n || {};
    n = d.createElement("script");
    n.type = "text/javascript", n.async = !0, n.src = r + "?sdkid=" + e + "&lib=" + t;
    e = d.getElementsByTagName("script")[0];
    e.parentNode.insertBefore(n, e)
  };
  ttq.load('D5PI923C77UCMC4TG120');
  ttq.page();
}(window, document, 'ttq');
</script> --}}
<!-- End TikTok Pixel Code -->

<!-- Favicon -->
<link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
<link rel="shortcut icon" href="{{ asset('favicon.svg') }}">

<!-- Google fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

<!-- Arabic font for RTL support -->
@if(app()->getLocale() == 'ar')
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
@endif

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/base.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

<link href="https://fonts.googleapis.com/css2?family=Material+Icons+Outlined" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin="" />

<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<!-- Stylesheets -->
<link rel="stylesheet" href="{{ asset('assets/css/vendors.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/upskills-theme.css') }}">

@if(request('noanim'))
<style>
  .preloader { display: none !important; }
  [data-anim], [data-anim-child] { opacity: 1 !important; transform: none !important; clip-path: none !important; visibility: visible !important; }
  .us-hero-anim, .us-float { animation: none !important; opacity: 1 !important; transform: none !important; }
</style>
@endif
<!-- Custom Preloader Style -->
<style>
  .preloader__bg {
    background-color: #081d36 !important;
  }
</style>

<!-- RTL Support for Arabic -->
@if(app()->getLocale() == 'ar')
<style>
  body {
    direction: rtl;
    font-family: 'Cairo', sans-serif;
  }

  .text-left {
    text-align: right !important;
  }

  .text-right {
    text-align: left !important;
  }

  .icon-chevron-right::before {
    content: "\f053" !important; /* left arrow */
  }

  .icon-chevron-left::before {
    content: "\f054" !important; /* right arrow */
  }

  .icon-arrow-right::before {
    content: "\f053" !important; /* left arrow */
  }

  .icon-arrow-left::before {
    content: "\f054" !important; /* right arrow */
  }

  /* Simple Logo Styles */
  .header__logo img {
    transition: all 0.3s ease;
  }

  .header__logo img:hover {
    transform: scale(1.05);
  }

  /* Cart icon RTL fix */
  .header-right__icons .icon-basket {
    position: relative !important;
  }

  /* Cart badge RTL positioning */
  .cart-badge {
    right: auto !important;
    left: -8px !important;
  }
</style>
@endif

<!-- Cart Badge Styles -->
<style>
.cart-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: linear-gradient(135deg, #ef4444, #dc2626) !important;
    color: white !important;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex !important;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 600;
    border: 2px solid white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    z-index: 10;
    opacity: 1 !important;
    visibility: visible !important;
}

[dir="rtl"] .cart-badge {
    right: auto;
    left: -5px;
}


/* Header Cart Icon - Specific Styles */
.header-right__icons {
    align-items: center;
    gap: 20px;
}

.header-right__icons .ml-20 {
    margin-left: 20px;
}

.header-right__icons a {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 8px;
    border-radius: 8px;
    transition: all 0.3s ease;
    text-decoration: none;
    position: relative;
    min-width: 40px;
    min-height: 40px;
}

.header-right__icons a:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-2px);
}

.header-right__icons .icon-basket {
    font-size: 18px;
    color: white;
    line-height: 1;
}

/* Mobile Language / Currency / Menu layout fixes */
@media (max-width: 1199px) {
  /* Hide language switcher in header on mobile */
  .header-right__icons > .ml-20:has(.language-switcher) {
    display: none !important;
  }

  /* Show language switcher in mobile menu footer */
  .mobile-language-switcher {
    display: block !important;
    width: 100%;
  }

  /* Style the mobile language switcher */
  .mobile-language-switcher .language-switcher {
    width: 100%;
    margin: 0 !important;
  }

  .mobile-language-switcher .js-language-active,
  .mobile-language-switcher .js-dropdown {
    width: 100%;
  }

  .mobile-language-switcher .js-language-active .dropdown__button {
    background: #f8f9fa;
    border-color: #e5e5e5;
    color: #1f2937;
    width: 100%;
    justify-content: space-between;
    padding: 12px 20px;
  }

  .mobile-language-switcher .js-language-active .dropdown__button:hover {
    background: #e9ecef;
    border-color: #d1d5db;
  }

  .mobile-language-switcher .js-language-active .toggle-element {
    left: 0 !important;
    right: auto !important;
    width: 100% !important;
    min-width: 100% !important;
  }

  /* Align currency switcher like language in mobile footer */
  .mobile-currency-switcher {
    display: block !important;
    width: 100%;
  }

  .mobile-currency-switcher .currency-switcher {
    display: block !important;
    width: 100% !important;
    margin: 0 !important;
  }

  .mobile-currency-switcher .js-dropdown,
  .mobile-currency-switcher .js-currency-active {
    width: 100% !important;
  }

  .mobile-currency-switcher .js-currency-active .dropdown__button {
    background: #f8f9fa !important;
    border: 1px solid #e5e5e5 !important;
    color: #1f2937 !important;
    width: 100% !important;
    min-width: 100% !important;
    max-width: 100% !important;
    justify-content: space-between !important;
    padding: 12px 20px !important;
    box-sizing: border-box;
  }

  .mobile-currency-switcher .js-currency-toggle {
    position: absolute !important;
    left: 0 !important;
    right: 0 !important;
    width: 100% !important;
    max-width: 100% !important;
    bottom: auto !important;
    border-radius: 8px !important;
  }

  .mobile-footer {
    width: 100%;
    box-sizing: border-box;
    flex-shrink: 0;
  }

  .mobile-socials {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
  }

  /* Keep mobile menu content scrollable */
  .header .header-menu .header-menu__content {
    overflow: hidden;
    min-height: 0;
  }

  .header .header-menu .menu {
    flex: 1 1 auto !important;
    height: auto !important;
    min-height: 0 !important;
    overflow-y: auto !important;
    overflow-x: hidden !important;
    -webkit-overflow-scrolling: touch;
  }

  /* RTL: open mobile menu from the right */
  [dir="rtl"] .header .header-menu {
    left: auto !important;
    right: 0 !important;
  }

  [dir="rtl"] .header .header-menu .header-menu__content {
    transform: translateX(20%);
  }

  [dir="rtl"] .header .header-menu.-is-el-visible .header-menu__content {
    transform: none;
  }

  [dir="rtl"] .header .header-menu-close {
    left: 20px !important;
    right: auto !important;
  }

  [dir="rtl"] .mobile-language-switcher .js-language-active .toggle-element,
  [dir="rtl"] .mobile-currency-switcher .js-currency-toggle {
    left: 0 !important;
    right: 0 !important;
  }

  /* Mobile menu user link styles */
  .header-menu__content .d-none.xl\\:d-flex a.d-flex.items-center {
    transition: all 0.3s ease;
    padding: 5px;
    border-radius: 8px;
    margin: -5px;
  }

  .header-menu__content .d-none.xl\\:d-flex a.d-flex.items-center:hover {
    background: #f3f4f6;
    transform: translateX(5px);
  }

  .header-menu__content .d-none.xl\\:d-flex a.d-flex.items-center:hover .avatar-circle {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    transform: scale(1.1);
  }
}

@media (min-width: 1200px) {
  /* Hide mobile language/currency switchers on desktop */
  .mobile-language-switcher,
  .mobile-currency-switcher {
    display: none !important;
  }
}

/* Fix for fixed header overlapping content */
.content-wrapper {
  padding-top: 90px; /* Adjust based on your header height */
}

/* Remove padding for homepage hero section */
body:has(.masthead-container) .content-wrapper,
.content-wrapper:has(.masthead-container) {
  padding-top: 0 !important;
}

@media (max-width: 991px) {
  .content-wrapper {
    padding-top: 60px; /* Smaller padding for mobile */
  }

  body:has(.masthead-container) .content-wrapper,
  .content-wrapper:has(.masthead-container) {
    padding-top: 0 !important;
  }
}

/* Navbar menu items hover effects */
.menu__nav > li > a {
  transition: all 0.3s ease;
  position: relative;
}

.menu__nav > li > a:hover {
  color: #3cb4b0 !important;
}

.menu__nav > li > a::after {
  content: '';
  position: absolute;
  bottom: -5px;
  left: 0;
  width: 0;
  height: 2px;
  background: #3cb4b0;
  transition: width 0.3s ease;
}

.menu__nav > li > a:hover::after {
  width: 100%;
}

/* Prevent login/signup buttons from becoming transparent on hover */
.header-right__buttons .button:hover,
.header-right__buttons .button.-white:hover,
.header-right__buttons .button.-underline:hover {
  opacity: 1 !important;
  background-color: inherit !important;
  color: inherit !important;
  text-decoration: none !important;
}
</style>

<title>@yield('title', 'Upskills Academy')</title>
