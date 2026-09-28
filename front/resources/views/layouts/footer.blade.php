<footer class="footer -type-1 bg-dark-1 -green-links footer-dark" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" style="background-color: #081d36 !important;">
  <style>
    /* Upskills footer columns */
    .us-foot-title {
      font-size: 13px;
      font-weight: 700;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      color: #f7f8fa !important;
      margin-bottom: 22px;
    }
    .us-foot-blurb {
      font-size: 14px;
      line-height: 1.7;
      color: rgba(247, 248, 250, 0.62) !important;
      margin: 0 0 18px;
      max-width: 30rem;
    }
    .us-foot-links { display: flex; flex-direction: column; gap: 12px; }
    .us-foot-links a { font-size: 14.5px; color: rgba(247,248,250,0.72) !important; }
    .us-foot-links a:hover { color: #69c5c2 !important; }
    .us-foot-contact { display: flex; flex-direction: column; gap: 10px; }
    .us-foot-contact__row {
      display: inline-flex; align-items: center; gap: 10px;
      font-size: 14px; color: rgba(247,248,250,0.72) !important;
    }
    .us-foot-contact__row:hover { color: #69c5c2 !important; }
    .us-foot-contact__row svg { color: #3cb4b0; flex-shrink: 0; }

    .us-news-form { display: flex; gap: 8px; }
    .us-news-input {
      flex: 1 1 auto;
      min-width: 0;
      padding: 12px 16px;
      font-size: 14px;
      color: #f7f8fa;
      background: rgba(247, 248, 250, 0.08);
      border: 1px solid rgba(247, 248, 250, 0.18);
      border-radius: 10px;
      transition: border-color 150ms ease, background 150ms ease;
    }
    .us-news-input::placeholder { color: rgba(247, 248, 250, 0.45); }
    .us-news-input:focus {
      outline: none;
      border-color: #3cb4b0;
      background: rgba(247, 248, 250, 0.12);
      box-shadow: 0 0 0 3px rgba(60, 180, 176, 0.25);
    }
    .us-news-btn {
      flex-shrink: 0;
      padding: 12px 20px;
      font-size: 14px;
      font-weight: 600;
      color: #081d36 !important;
      background: #3cb4b0;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      transition: background 150ms ease;
    }
    .us-news-btn:hover { background: #69c5c2; }

    /* Dark Footer Styles */
    .footer-dark {
      background-color: #081d36 !important;
      color: white !important;
    }

    .footer-dark .text-17,
    .footer-dark .text-white,
    .footer-dark h1, .footer-dark h2, .footer-dark h3,
    .footer-dark h4, .footer-dark h5, .footer-dark h6,
    .footer-dark p, .footer-dark span, .footer-dark div {
      color: white !important;
    }

    .footer-dark a {
      color: #d1d5db !important;
      transition: color 0.3s ease;
    }

    .footer-dark a:hover {
      color: #ffffff !important;
    }

    .footer-dark .footer-header-socials__title {
      color: white !important;
    }

    .footer-dark .footer-header-socials__list a {
      background: rgba(255, 255, 255, 0.1);
      color: white !important;
      transition: all 0.3s ease;
    }

    .footer-dark .footer-header-socials__list a:hover {
      background: rgba(255, 255, 255, 0.2);
      transform: translateY(-2px);
    }

    .footer-dark .form-control {
      background: rgba(255, 255, 255, 0.1);
      border-color: rgba(255, 255, 255, 0.2);
      color: white;
    }

    .footer-dark .form-control::placeholder {
      color: rgba(255, 255, 255, 0.5);
    }

    .footer-dark .form-control:focus {
      background: rgba(255, 255, 255, 0.15);
      border-color: rgba(255, 255, 255, 0.3);
      color: white;
    }

    .footer-dark .footer-copyright {
      border-top: 1px solid rgba(255, 255, 255, 0.1);
      color: #9ca3af !important;
    }

    .footer-dark .footer-copyright a {
      color: #d1d5db !important;
    }

    .footer-dark .footer-copyright a:hover {
      color: white !important;
    }

    /* Newsletter Submit Button */
    .footer-columns-form button,
    .footer-columns-form button[type="submit"] {
      background-color: #3cb4b0 !important;
      color: #081d36 !important;
      font-weight: 600;
    }

    .footer-columns-form button:hover,
    .footer-columns-form button[type="submit"]:hover {
      background-color: #69c5c2 !important;
    }

    /* Newsletter Form Styles */
    .newsletter-description {
      color: white;
      margin-bottom: 15px;
      font-size: 14px;
    }

    .newsletter-form {
      margin-top: 10px;
    }

    .newsletter-input-wrapper {
      position: relative;
      display: flex;
      align-items: center;
      width: 100%;
    }

    .newsletter-input {
      width: 100%;
      padding: 12px 110px 12px 15px;
      border-radius: 8px;
      border: 2px solid #c5d7f2;
      background: #e9f1ff;
      color: #0b2545;
      font-size: 14px;
    }

    .newsletter-input::placeholder {
      color: #4a5d7a;
    }

    .newsletter-input:focus {
      outline: none;
      background: #e1ebff;
      border-color: #9db7ec;
      box-shadow: 0 0 0 2px rgba(157, 183, 236, 0.35);
    }

    .newsletter-submit-btn {
      position: absolute;
      right: 5px;
      top: 50%;
      transform: translateY(-50%);
      background-color: #3cb4b0 !important;
      color: #081d36 !important;
      padding: 8px 20px;
      border-radius: 6px;
      border: none;
      font-weight: 600;
      cursor: pointer;
      font-size: 14px;
    }

    .newsletter-submit-btn:hover {
      background-color: #69c5c2 !important;
    }

    /* RTL Support for Newsletter */
    [dir="rtl"] .newsletter-input {
      padding: 12px 15px 12px 110px;
      text-align: right;
    }

    [dir="rtl"] .newsletter-submit-btn {
      right: auto;
      left: 5px;
    }
  </style>
  <div class="container">
    <div class="footer-header">
      <div class="row y-gap-20 justify-between items-center">
        <div class="col-auto">
          <div class="footer-header__logo">
            <a href="{{ route('accueil.index') }}">
              <img src="{{ asset('assets/img/general/logo-upskills-white.svg') }}" alt="Upskills Academy" style="max-height: 80px;">
            </a>
          </div>
        </div>
        <div class="col-auto">
          <div class="footer-header-socials">
            <div class="footer-header-socials__title text-white">{{ __('messages.follow_us_social') }}</div>
            <div class="footer-header-socials__list">
              @if(isset($information) && $information->facebook)
                <a href="{{ $information->facebook }}" target="_blank"><i class="icon-facebook"></i></a>
              @endif
              @if(isset($information) && $information->youtube)
                <a href="{{ $information->youtube }}" target="_blank"><img src="/youtube/youtube.png" alt="youtube" style="max-height: 30px;"></a>
              @endif
              @if(isset($information) && $information->instagram)
                <a href="{{ $information->instagram }}" target="_blank"><i class="icon-instagram"></i></a>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="footer-columns">
      <div class="row y-gap-40">
        <div class="col-xl-3 col-lg-6 col-md-6">
          <div class="us-foot-title">{{ __('messages.swedish_academy') }}</div>
          <p class="us-foot-blurb">{{ __('messages.footer_about_blurb') }}</p>
          @if(isset($information))
            <div class="us-foot-contact">
              @if($information->email)
                <a href="mailto:{{ $information->email }}" class="us-foot-contact__row">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="3"/><path d="m3 6 9 7 9-7"/></svg>
                  <span dir="ltr">{{ $information->email }}</span>
                </a>
              @endif
              @if($information->phone)
                <a href="tel:{{ preg_replace('/\s+/', '', $information->phone) }}" class="us-foot-contact__row">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                  <span dir="ltr">{{ $information->phone }}</span>
                </a>
              @endif
            </div>
          @endif
        </div>

        <div class="col-xl-2 col-lg-3 col-md-6 col-6">
          <div class="us-foot-title">{{ __('messages.quick_links') }}</div>
          <div class="us-foot-links">
            <a href="{{ route('accueil.index') }}">{{ __('messages.home') }}</a>
            <a href="{{ route('filter.index') }}">{{ __('messages.courses') }}</a>
            <a href="{{ route('books.index') }}">{{ __('messages.library') }}</a>
            <a href="{{ route('graduates.index', ['locale' => app()->getLocale()]) }}">{{ __('messages.graduates') }}</a>
            <a href="{{ route('partnership.index') }}">{{ __('messages.organizations') }}</a>
          </div>
        </div>

        <div class="col-xl-2 col-lg-3 col-md-6 col-6">
          <div class="us-foot-title">{{ __('messages.resources') }}</div>
          <div class="us-foot-links">
            <a href="{{ route('articles.index') }}">{{ __('messages.articles') }}</a>
            <a href="{{ route('blog.index') }}">{{ __('messages.news') }}</a>
            <a href="{{ route('contact.index') }}">{{ __('messages.contact_us') }}</a>
          </div>
        </div>

        <div class="col-xl-2 col-lg-3 col-md-6 col-6">
          <div class="us-foot-title">{{ __('messages.why_academy') }}</div>
          <div class="us-foot-links">
            @php
              $pages = \App\Http\Controllers\PageController::getMenuPages();
            @endphp
            @forelse($pages as $page)
              <a href="{{ route('pages.show', $page->slug) }}">{{ $page->titre }}</a>
            @empty
              <a href="#">{{ __('messages.who_we_are') }}</a>
            @endforelse
          </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
          <div class="us-foot-title">{{ __('messages.stay_connected') }}</div>
          <div id="newsletter-section">
            <p class="us-foot-blurb mb-15">{{ __('messages.no_spam_message') }}</p>

            @foreach(['newsletter_success','newsletter_error','newsletter_info'] as $flash)
              @if(session($flash))
                <div class="mb-10" style="font-size: 13px; color: #c2e8e6 !important;">{{ session($flash) }}</div>
              @endif
            @endforeach

            <form action="{{ route('newsletter.subscribe', ['locale' => app()->getLocale()]) }}" method="POST" class="us-news-form">
              @csrf
              <input type="email" name="email" class="us-news-input" placeholder="{{ __('messages.email_placeholder') }}" required>
              <button type="submit" class="us-news-btn">{{ __('messages.submit') }}</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="py-30 border-top-light-15">
      <div class="row justify-between items-center y-gap-20">
        <div class="col-auto">
          <div class="d-flex items-center h-100 text-white">
            © {{ date('Y') }} {{ __('messages.swedish_academy') }}. {{ __('messages.all_rights_reserved') }}.
          </div>
        </div>

        <div class="col-auto">
          <div class="d-flex x-gap-20 y-gap-20 items-center flex-wrap">
            <div>
              @include('layouts.language-switcher')
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</footer>



<script>
  document.addEventListener('DOMContentLoaded', function() {
    const target = document.getElementById('newsletter-section');
    const hasFlash = Boolean({{ session()->has('newsletter_success') || session()->has('newsletter_error') || session()->has('newsletter_info') || session()->has('scroll_to_newsletter') ? 'true' : 'false' }});
    const shouldScroll = hasFlash || sessionStorage.getItem('newsletterScroll') === '1';

    if (shouldScroll && target && typeof target.scrollIntoView === 'function') {
      target.scrollIntoView({ behavior: 'smooth', block: 'center' });
      sessionStorage.removeItem('newsletterScroll');
    }

    const form = document.querySelector('form.newsletter-form');
    if (form) {
      form.addEventListener('submit', function() {
        sessionStorage.setItem('newsletterScroll', '1');
      });
    }
  });
</script>

<!-- JavaScript -->
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
<script src="{{ asset('assets/js/vendors.js') }}"></script>
<script src="{{ asset('assets/js/main.js') }}"></script>
<script type="text/javascript" src="http://127.0.0.1:8000/analytic-js/scripts.js"></script>

@include('partials.ai-chatbot-widget')

