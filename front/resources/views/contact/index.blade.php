<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
  @include('layouts.head')
  <title>{{ __('messages.contact_us') }} - Upskills Academy</title>
</head>

<body class="preloader-visible" data-barba="wrapper">
  <!-- preloader start -->
  <div class="preloader js-preloader">
    <div class="preloader__bg"></div>
  </div>
  <!-- preloader end -->

  <main class="main-content">
    @include('layouts.header')

    <div class="content-wrapper js-content-wrapper"  dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

      <!-- Contact Form Section -->
      <section class="layout-pt-md layout-pb-lg">
        <div data-anim-wrap class="container">

          @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <strong>{{ __('messages.success') }}!</strong> {{ session('success') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          @endif

          @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <strong>{{ __('messages.error') }}!</strong> {{ session('error') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          @endif

          <div class="row y-gap-50 justify-between">
            <!-- Contact Info -->
            <div class="col-lg-4">
              <h3 class="text-24 fw-500">{{ __('messages.keep_in_touch') }}</h3>
              <p class="mt-25">{{ __('messages.contact_intro') }}</p>

              <div class="y-gap-30 pt-60 lg:pt-40">
                <!-- Address -->
                @if(isset($information) && $information->adresse)
                  <div class="d-flex items-center">
                    <div class="d-flex justify-center items-center size-60 rounded-full bg-light-7">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                      </svg>
                    </div>
                    <div class="{{ app()->getLocale() == 'ar' ? 'mr-20' : 'ml-20' }}">
                      {{ $information->adresse }}
                    </div>
                  </div>
                @endif

                <!-- Phone -->
                @if(isset($information) && $information->phone)
                  <div class="d-flex items-center">
                    <div class="d-flex justify-center items-center size-60 rounded-full bg-light-7">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                      </svg>
                    </div>
                    <div class="{{ app()->getLocale() == 'ar' ? 'mr-20' : 'ml-20' }}" dir="ltr">
                      <a href="tel:{{ $information->phone }}" class="text-dark-1">{{ $information->phone }}</a>
                    </div>
                  </div>
                @endif

                <!-- Email -->
                @if(isset($information) && $information->email)
                  <div class="d-flex items-center">
                    <div class="d-flex justify-center items-center size-60 rounded-full bg-light-7">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                      </svg>
                    </div>
                    <div class="{{ app()->getLocale() == 'ar' ? 'mr-20' : 'ml-20' }}">
                      <a href="mailto:{{ $information->email }}" class="text-dark-1">{{ $information->email }}</a>
                    </div>
                  </div>
                @endif

                <!-- Social Media -->
                @if(isset($information) && ($information->facebook || $information->youtube || $information->instagram))
                  <div class="d-flex items-center pt-30">
                    <div class="d-flex x-gap-15">
                      @if($information->facebook)
                        <a href="{{ $information->facebook }}" target="_blank" class="d-flex items-center justify-center size-50 rounded-full bg-light-7 -hover-bg-purple-1 -hover-text-white text-18 text-dark-1">
                          <i class="icon-facebook"></i>
                        </a>
                      @endif
                      @if($information->youtube)
                        <a href="{{ $information->youtube }}" target="_blank" class="d-flex items-center justify-center size-50 rounded-full bg-light-7 -hover-bg-red-1 -hover-text-white text-18 text-dark-1">
                          <i class="icon-youtube"></i>
                        </a>
                      @endif
                      @if($information->instagram)
                        <a href="{{ $information->instagram }}" target="_blank" class="d-flex items-center justify-center size-50 rounded-full bg-light-7 -hover-bg-pink-1 -hover-text-white text-18 text-dark-1">
                          <i class="icon-instagram"></i>
                        </a>
                      @endif
                    </div>
                  </div>
                @endif
              </div>
            </div>

            <!-- Contact Form -->
            <div class="col-lg-7">
              <h3 class="text-24 fw-500">{{ __('messages.send_message') }}</h3>
              <p class="mt-25">{{ __('messages.contact_form_intro') }}</p>

              <form class="contact-form row y-gap-30 pt-60 lg:pt-40" action="{{ route('contact.submit') }}" method="POST">
                @csrf

                <!-- Honeypot field - hidden from humans, bots will fill it -->
                <div style="position: absolute; left: -9999px;" aria-hidden="true">
                  <input type="text" name="website_url" tabindex="-1" autocomplete="off">
                </div>

                <div class="col-md-6">
                  <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.name') }}</label>
                  <input type="text" name="name" value="{{ old('name') }}" placeholder="{{ __('messages.enter_name') }}" required>
                  @error('name')
                    <span class="text-red-1 text-13 mt-5">{{ $message }}</span>
                  @enderror
                </div>

                <div class="col-md-6">
                  <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.email') }}</label>
                  <input type="email" name="email" value="{{ old('email') }}" placeholder="{{ __('messages.enter_email') }}" required>
                  @error('email')
                    <span class="text-red-1 text-13 mt-5">{{ $message }}</span>
                  @enderror
                </div>

                <div class="col-12">
                  <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.subject') }} ({{ __('messages.optional') }})</label>
                  <input type="text" name="subject" value="{{ old('subject') }}" placeholder="{{ __('messages.enter_subject') }}">
                  @error('subject')
                    <span class="text-red-1 text-13 mt-5">{{ $message }}</span>
                  @enderror
                </div>

                <div class="col-12">
                  <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.message') }}</label>
                  <textarea name="message" placeholder="{{ __('messages.enter_message') }}" rows="8" required>{{ old('message') }}</textarea>
                  @error('message')
                    <span class="text-red-1 text-13 mt-5">{{ $message }}</span>
                  @enderror
                </div>

                <div class="col-12">
                  <button type="submit" class="button -md -purple-1 text-white">
                    {{ __('messages.send_message') }}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </section>

    </div>
  </main>

  @include('layouts.footer')
</body>
</html>

