@extends('layouts.app')

@section('content')
<section class="form-page">
  <div class="form-page__img bg-dark-1" style="background-image: url('/login_images/2.jpg');background-size:cover;background-repeat:no-repeat;background-position: center;">
    <div class="form-page-composition">
      <div class="-bg"><img data-move="30" class="js-mouse-move" src="{{ asset('assets/img/login/bg.png') }}" alt="bg"></div>
    </div>
  </div>

  <div class="form-page__content lg:py-50">
    <div class="container">
      <div class="row justify-center items-center">
        <div class="col-xl-6 col-lg-8">
          <div class="px-50 py-50 md:px-25 md:py-25 bg-white shadow-1 rounded-16">
            <h3 class="text-30 lh-13">{{ __('login.verify_code') }}</h3>
            <p class="mt-10">{{ __('login.enter_6_digit_code') }}</p>

            @if (session('success'))
              <div class="alert alert-{{ str_contains(session('success'), 'Email not sent') ? 'warning' : 'success' }} mt-20">
                <strong>{{ str_contains(session('success'), 'Email not sent') ? '⚠️ Warning:' : '✅ Success!' }}</strong> {{ session('success') }}
              </div>
            @endif

            @if ($errors->any())
              <div class="alert alert-danger mt-20">
                <ul class="mb-0">
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            <form class="contact-form respondForm__form row y-gap-20 pt-30" action="{{ route('verify-code-post') }}" method="POST">
              @csrf
              <input type="hidden" name="email" value="{{ $email }}">
              
              <div class="col-12">
                <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('login.verification_code') }} *</label>
                <input type="text" name="code" id="verification_code" placeholder="000000" maxlength="6" pattern="[0-9]{6}" required style="letter-spacing: 8px; font-size: 24px; text-align: center; font-weight: bold;">
                <small class="text-14 text-light-1">{{ __('login.code_expires_15_minutes') }}</small>
                <div id="code-error" class="text-14 text-red-1 mt-5" style="display: none;"></div>
              </div>

              <div class="col-12">
                <button type="submit" class="button -md -green-1 text-dark-1 fw-500 w-1/1">
                  {{ __('login.verify_code') }}
                </button>
              </div>

              <div class="col-12 text-center">
                <a href="{{ route('forgot-password') }}" class="text-14 text-purple-1">{{ __('login.didnt_receive_code') }}</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const codeInput = document.getElementById('verification_code');
    const codeError = document.getElementById('code-error');
    
    // Auto-format code input (only numbers)
    codeInput.addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/[^0-9]/g, '');
        
        if (e.target.value.length === 6) {
            codeError.style.display = 'none';
        }
    });
    
    // Validate on submit
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        if (codeInput.value.length !== 6) {
            e.preventDefault();
            codeError.textContent = '{{ __("login.code_must_be_6_digits") }}';
            codeError.style.display = 'block';
        }
    });
});
</script>

<style>
/* Minimum height for form pages on desktop */
@media (min-width: 992px) {
  .form-page {
    min-height: 800px;
  }
}
</style>
@endsection
