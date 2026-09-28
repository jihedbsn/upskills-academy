<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
  @include('layouts.head')
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
      <!-- Header -->
      <section class="pageHeader -type-1 us-pagehead">
  <div class="us-pagehead__grid" aria-hidden="true"></div>
  <div class="container">
    <div class="row justify-center text-center">
      <div class="col-lg-10 col-xl-8">
        <div data-anim="slide-up delay-1">
          <h1 class="pageHeader__title">{{ __('messages.partnership_title') }}</h1>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Partnership Content -->
<section class="layout-pt-md layout-pb-lg">
  <div class="container">
    <!-- Partnership Intro -->
    <div class="row justify-center text-center">
      <div class="col-lg-9 col-xl-8">
        <div data-anim="slide-up delay-1">
          <p class="text-16 lh-1_7 text-dark-1 fw-400">{{ __('messages.partnership_description') }}</p>
        </div>
        <div data-anim="slide-up delay-2">
          <p class="text-16 lh-1_7 text-dark-1 fw-400 mt-15">{{ __('messages.partnership_description_2') }}</p>
        </div>
        <div data-anim="slide-up delay-3">
          <p class="text-16 lh-1_7 text-dark-1 fw-400 mt-15">{{ __('messages.partnership_description_3') }}</p>
        </div>
      </div>
    </div>

    <!-- Partnership Form -->
    <div class="row justify-center mt-30">
      <div class="col-lg-8">
        <div data-anim="slide-up delay-1">
          <div class="contactForm -type-1">
            <div class="contactForm__content">
              <h3 class="contactForm__title text-center">{{ __('messages.partnership') }}</h3>

              @if(request('success'))
                <script>
                  Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Partnership request submitted successfully!',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#7c3aed'
                  });
                </script>
              @endif

              @if(request('error'))
                <script>
                  var errorMsg = decodeURIComponent('{{ request('error') }}');
                  Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred: ' + errorMsg,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#ef4444'
                  });
                </script>
              @endif

              <form action="{{ route('partnership.store') }}" method="POST" enctype="multipart/form-data" class="contact-form row y-gap-30 pt-60 lg:pt-40">
                @csrf

                <!-- Honeypot field - hidden from humans, bots will fill it -->
                <div style="position: absolute; left: -9999px;" aria-hidden="true">
                  <input type="text" name="website_url" tabindex="-1" autocomplete="off">
                </div>

                <!-- Institution Name -->
                <div class="col-md-6">
                  <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.institution_name') }} *</label>
                  <input
                    type="text"
                    name="institution_name"
                    placeholder="{{ __('messages.institution_name') }}..."
                    value="{{ old('institution_name') }}"
                    class="@error('institution_name') is-invalid @enderror"
                    required
                  >
                  @error('institution_name')
                    <div class="text-red-1 text-12 mt-5">{{ $message }}</div>
                  @enderror
                </div>

                <!-- Email -->
                <div class="col-md-6">
                  <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.email') }} *</label>
                  <input
                    type="email"
                    name="email"
                    placeholder="{{ __('messages.email') }}..."
                    value="{{ old('email') }}"
                    class="@error('email') is-invalid @enderror"
                    required
                  >
                  @error('email')
                    <div class="text-red-1 text-12 mt-5">{{ $message }}</div>
                  @enderror
                </div>

                <!-- Institution Address -->
                <div class="col-12">
                  <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.institution_address') }} *</label>
                  <textarea
                    name="institution_address"
                    placeholder="{{ __('messages.institution_address') }}..."
                    rows="3"
                    class="@error('institution_address') is-invalid @enderror"
                    required
                  >{{ old('institution_address') }}</textarea>
                  @error('institution_address')
                    <div class="text-red-1 text-12 mt-5">{{ $message }}</div>
                  @enderror
                </div>

                <!-- Phone -->
                <div class="col-md-6">
                  <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.phone') }} *</label>
                  <input
                    type="text"
                    name="phone"
                    placeholder="{{ __('messages.phone') }}..."
                    value="{{ old('phone') }}"
                    class="@error('phone') is-invalid @enderror"
                    required
                  >
                  @error('phone')
                    <div class="text-red-1 text-12 mt-5">{{ $message }}</div>
                  @enderror
                </div>

                <!-- Website -->
                <div class="col-md-6">
                  <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.website') }}</label>
                  <input
                    type="text"
                    name="website"
                    placeholder="https://example.com"
                    value="{{ old('website') }}"
                    class="@error('website') is-invalid @enderror"
                  >
                  @error('website')
                    <div class="text-red-1 text-12 mt-5">{{ $message }}</div>
                  @enderror
                </div>

                <!-- Profile File -->
                <div class="col-12">
                  <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.profile_file') }} *</label>
                  <input
                    type="file"
                    name="profile_file"
                    class="@error('profile_file') is-invalid @enderror"
                    accept=".pdf,.doc,.docx"
                    required
                  >
                  <div class="text-12 text-light-1 mt-5">
                    {{ __('messages.profile_file_mimes') }} (Max: 10MB)
                  </div>
                  @error('profile_file')
                    <div class="text-red-1 text-12 mt-5">{{ $message }}</div>
                  @enderror
                </div>

                <!-- Requested Courses -->
                <div class="col-12">
                  <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.requested_courses') }} *</label>
                  <div class="row y-gap-10">
                    <div class="col-md-6">
                      <label class="checkbox">
                        <input type="checkbox" name="requested_courses[]" value="sports_injuries" {{ in_array('sports_injuries', old('requested_courses', [])) ? 'checked' : '' }}>
                        <span class="checkbox__mark"></span>
                        <span class="checkbox__text">{{ __('messages.sports_injuries') }}</span>
                      </label>
                    </div>
                    <div class="col-md-6">
                      <label class="checkbox">
                        <input type="checkbox" name="requested_courses[]" value="sports_nutrition" {{ in_array('sports_nutrition', old('requested_courses', [])) ? 'checked' : '' }}>
                        <span class="checkbox__mark"></span>
                        <span class="checkbox__text">{{ __('messages.sports_nutrition') }}</span>
                      </label>
                    </div>
                    <div class="col-md-6">
                      <label class="checkbox">
                        <input type="checkbox" name="requested_courses[]" value="fitness_assistant" {{ in_array('fitness_assistant', old('requested_courses', [])) ? 'checked' : '' }}>
                        <span class="checkbox__mark"></span>
                        <span class="checkbox__text">{{ __('messages.fitness_assistant') }}</span>
                      </label>
                    </div>
                    <div class="col-md-6">
                      <label class="checkbox">
                        <input type="checkbox" name="requested_courses[]" value="fitness_trainer" {{ in_array('fitness_trainer', old('requested_courses', [])) ? 'checked' : '' }}>
                        <span class="checkbox__mark"></span>
                        <span class="checkbox__text">{{ __('messages.fitness_trainer') }}</span>
                      </label>
                    </div>
                    <div class="col-md-6">
                      <label class="checkbox">
                        <input type="checkbox" name="requested_courses[]" value="personal_trainer" {{ in_array('personal_trainer', old('requested_courses', [])) ? 'checked' : '' }}>
                        <span class="checkbox__mark"></span>
                        <span class="checkbox__text">{{ __('messages.personal_trainer') }}</span>
                      </label>
                    </div>
                    <div class="col-md-6">
                      <label class="checkbox">
                        <input type="checkbox" name="requested_courses[]" value="sports_rehabilitation" {{ in_array('sports_rehabilitation', old('requested_courses', [])) ? 'checked' : '' }}>
                        <span class="checkbox__mark"></span>
                        <span class="checkbox__text">{{ __('messages.sports_rehabilitation') }}</span>
                      </label>
                    </div>
                    <div class="col-md-6">
                      <label class="checkbox">
                        <input type="checkbox" name="requested_courses[]" value="sports_psychology" {{ in_array('sports_psychology', old('requested_courses', [])) ? 'checked' : '' }}>
                        <span class="checkbox__mark"></span>
                        <span class="checkbox__text">{{ __('messages.sports_psychology') }}</span>
                      </label>
                    </div>
                    <div class="col-md-6">
                      <label class="checkbox">
                        <input type="checkbox" name="requested_courses[]" value="sports_management" {{ in_array('sports_management', old('requested_courses', [])) ? 'checked' : '' }}>
                        <span class="checkbox__mark"></span>
                        <span class="checkbox__text">{{ __('messages.sports_management') }}</span>
                      </label>
                    </div>
                  </div>
                  @error('requested_courses')
                    <div class="text-red-1 text-12 mt-5">{{ $message }}</div>
                  @enderror
                </div>

                <!-- Additional Courses -->
                <div class="col-12">
                  <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.additional_courses') }}</label>
                  <textarea
                    name="additional_courses"
                    placeholder="{{ __('messages.additional_courses') }}"
                    rows="3"
                    class="@error('additional_courses') is-invalid @enderror"
                  >{{ old('additional_courses') }}</textarea>
                  @error('additional_courses')
                    <div class="text-red-1 text-12 mt-5">{{ $message }}</div>
                  @enderror
                </div>

                <!-- Submit Button -->
                <div class="col-12">
                  <button type="submit" name="submit" id="submit" class="button -md -purple-1 text-white">
                    {{ __('messages.submit') }}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
    </div>
  </main>

  @include('layouts.footer')
</body>
</html>
