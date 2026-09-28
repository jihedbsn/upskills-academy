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
                <h1 class="pageHeader__title">{{ __('messages.partnership_courses_title') }}</h1>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Partnership Courses Content -->
      <section class="layout-pt-md layout-pb-lg">
        <div class="container">
          <!-- Intro -->
          <div class="row justify-center text-center mb-50">
            <div class="col-lg-9 col-xl-8">
              <div data-anim="slide-up delay-1">
                <p class="text-16 lh-1_7 text-dark-1 fw-400">{{ __('messages.partnership_courses_description') }}</p>
              </div>
              <div data-anim="slide-up delay-2">
                <p class="text-16 lh-1_7 text-dark-1 fw-400 mt-15">{{ __('messages.partnership_courses_description_2') }}</p>
              </div>
            </div>
          </div>

          <!-- Requirements Section -->
          <div class="row justify-center mb-60">
            <div class="col-lg-10">
              <div data-anim="slide-up delay-1">
                <h2 class="text-30 lh-15 fw-700 text-center mb-30">{{ __('messages.partnership_courses_requirements') }}</h2>
                <div class="row y-gap-20">
                  <div class="col-12">
                    <p class="text-15 lh-1_6">{{ __('messages.partnership_courses_requirements') }}</p>
                  </div>
                  <div class="col-12">
                    <p class="text-15 lh-1_6">{{ __('messages.partnership_courses_requirements_2') }}</p>
                  </div>
                  <div class="col-12">
                    <p class="text-15 lh-1_6">{{ __('messages.partnership_courses_requirements_3') }}</p>
                  </div>
                  <div class="col-12">
                    <p class="text-15 lh-1_6">{{ __('messages.partnership_courses_requirements_4') }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- How to Apply Section -->
          <div class="row justify-center mb-60">
            <div class="col-lg-10">
              <div data-anim="slide-up delay-2">
                <h2 class="text-30 lh-15 fw-700 text-center mb-30">{{ __('messages.partnership_courses_how_to_apply') }}</h2>
                <div class="row y-gap-15">
                  <div class="col-12">
                    <p class="text-15 lh-1_6">1. {{ __('messages.partnership_courses_how_to_apply_1') }}</p>
                  </div>
                  <div class="col-12">
                    <p class="text-15 lh-1_6">2. {{ __('messages.partnership_courses_how_to_apply_2') }}</p>
                  </div>
                  <div class="col-12">
                    <p class="text-15 lh-1_6">3. {{ __('messages.partnership_courses_how_to_apply_3') }}</p>
                  </div>
                  <div class="col-12">
                    <p class="text-15 lh-1_6">4. {{ __('messages.partnership_courses_how_to_apply_4') }}</p>
                  </div>
                  <div class="col-12">
                    <p class="text-15 lh-1_6">5. {{ __('messages.partnership_courses_how_to_apply_5') }}</p>
                  </div>
                  <div class="col-12">
                    <p class="text-15 lh-1_6">6. {{ __('messages.partnership_courses_how_to_apply_6') }}</p>
                  </div>
                  <div class="col-12">
                    <p class="text-15 lh-1_6">7. {{ __('messages.partnership_courses_how_to_apply_7') }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Pricing Section -->
          <div class="row justify-center mb-60">
            <div class="col-lg-10">
              <div data-anim="slide-up delay-3">
                <h2 class="text-30 lh-15 fw-700 text-center mb-30">{{ __('messages.partnership_courses_pricing') }}</h2>
                <div class="row y-gap-15">
                  <div class="col-12">
                    <p class="text-15 lh-1_6">{{ __('messages.partnership_courses_pricing_1') }}</p>
                  </div>
                  <div class="col-12">
                    <p class="text-15 lh-1_6">{{ __('messages.partnership_courses_pricing_2') }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Important Notes Section -->
          <div class="row justify-center mb-60">
            <div class="col-lg-10">
              <div data-anim="slide-up delay-4">
                <h2 class="text-30 lh-15 fw-700 text-center mb-30">{{ __('messages.partnership_courses_important_notes') }}</h2>
                <div class="row y-gap-15">
                  <div class="col-12">
                    <p class="text-15 lh-1_6">{{ __('messages.partnership_courses_important_notes_1') }}</p>
                  </div>
                  <div class="col-12">
                    <p class="text-15 lh-1_6">{{ __('messages.partnership_courses_important_notes_2') }}</p>
                  </div>
                  <div class="col-12">
                    <p class="text-15 lh-1_6">{{ __('messages.partnership_courses_important_notes_3') }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Partnership Course Form -->
          <div class="row justify-center">
            <div class="col-lg-8">
              <div data-anim="slide-up delay-1">
                <div class="contactForm -type-1">
                  <div class="contactForm__content">
                    <h3 class="contactForm__title text-center">{{ __('messages.partnership_courses') }}</h3>
                    
                    @if(request('success'))
                      <script>
                        Swal.fire({
                          icon: 'success',
                          title: 'Success!',
                          text: 'Partnership course request submitted successfully!',
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

                    <form action="{{ route('partnership-courses.store') }}" method="POST" class="contact-form row y-gap-30 pt-60 lg:pt-40">
                      @csrf
                      
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

                      <!-- Course Title -->
                      <div class="col-md-6">
                        <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.course_title_en') }} *</label>
                        <input 
                          type="text" 
                          name="course_title_en" 
                          placeholder="{{ __('messages.course_title_en') }}..."
                          value="{{ old('course_title_en') }}"
                          class="@error('course_title_en') is-invalid @enderror"
                          required
                        >
                        @error('course_title_en')
                          <div class="text-red-1 text-12 mt-5">{{ $message }}</div>
                        @enderror
                      </div>

                      <!-- Course Type -->
                      <div class="col-md-6">
                        <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.course_type') }} *</label>
                        <select 
                          name="course_type" 
                          class="@error('course_type') is-invalid @enderror"
                          required
                        >
                          <option value="">{{ __('messages.course_type') }}...</option>
                          <option value="training_course" {{ old('course_type') == 'training_course' ? 'selected' : '' }}>{{ __('messages.training_course') }}</option>
                          <option value="workshop" {{ old('course_type') == 'workshop' ? 'selected' : '' }}>{{ __('messages.workshop') }}</option>
                        </select>
                        @error('course_type')
                          <div class="text-red-1 text-12 mt-5">{{ $message }}</div>
                        @enderror
                      </div>

                      <!-- Delivery Method -->
                      <div class="col-md-6">
                        <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.delivery_method') }} *</label>
                        <select 
                          name="delivery_method" 
                          class="@error('delivery_method') is-invalid @enderror"
                          required
                        >
                          <option value="">{{ __('messages.delivery_method') }}...</option>
                          <option value="classroom" {{ old('delivery_method') == 'classroom' ? 'selected' : '' }}>{{ __('messages.classroom') }}</option>
                          <option value="online" {{ old('delivery_method') == 'online' ? 'selected' : '' }}>{{ __('messages.online') }}</option>
                        </select>
                        @error('delivery_method')
                          <div class="text-red-1 text-12 mt-5">{{ $message }}</div>
                        @enderror
                      </div>

                      <!-- Evaluation Method -->
                      <div class="col-12">
                        <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.evaluation_method') }} *</label>
                        <select 
                          name="evaluation_method" 
                          class="@error('evaluation_method') is-invalid @enderror"
                          required
                        >
                          <option value="">{{ __('messages.evaluation_method') }}...</option>
                          <option value="self_assessment" {{ old('evaluation_method') == 'self_assessment' ? 'selected' : '' }}>{{ __('messages.self_assessment') }}</option>
                          <option value="theoretical_test" {{ old('evaluation_method') == 'theoretical_test' ? 'selected' : '' }}>{{ __('messages.theoretical_test') }}</option>
                          <option value="practical_test" {{ old('evaluation_method') == 'practical_test' ? 'selected' : '' }}>{{ __('messages.practical_test') }}</option>
                          <option value="theoretical_practical_test" {{ old('evaluation_method') == 'theoretical_practical_test' ? 'selected' : '' }}>{{ __('messages.theoretical_practical_test') }}</option>
                        </select>
                        @error('evaluation_method')
                          <div class="text-red-1 text-12 mt-5">{{ $message }}</div>
                        @enderror
                      </div>

                      <!-- Website URL -->
                      <div class="col-md-6">
                        <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.website') }}</label>
                        <input 
                          type="text" 
                          name="website_url" 
                          placeholder="https://example.com"
                          value="{{ old('website_url') }}"
                          class="@error('website_url') is-invalid @enderror"
                        >
                        @error('website_url')
                          <div class="text-red-1 text-12 mt-5">{{ $message }}</div>
                        @enderror
                      </div>

                      <!-- Course Description -->
                      <div class="col-12">
                        <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.course_description') }} *</label>
                        <textarea 
                          name="course_description" 
                          placeholder="{{ __('messages.course_description') }}..."
                          rows="5"
                          class="@error('course_description') is-invalid @enderror"
                          required
                        >{{ old('course_description') }}</textarea>
                        @error('course_description')
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
