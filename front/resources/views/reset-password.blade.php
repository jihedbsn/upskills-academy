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
            <h3 class="text-30 lh-13">{{ __('login.reset_password') }}</h3>
            <p class="mt-10">{{ __('login.create_new_password') }}</p>

            @if ($errors->any())
              <div class="alert alert-danger mt-20">
                <ul class="mb-0">
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            <form class="contact-form respondForm__form row y-gap-20 pt-30" action="{{ route('reset-password-post') }}" method="POST">
              @csrf
              <input type="hidden" name="email" value="{{ $email }}">
              <input type="hidden" name="code" value="{{ $code }}">
              
              <div class="col-12">
                <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('login.new_password') }} *</label>
                <input type="password" name="password" id="password" placeholder="{{ __('login.enter_new_password') }}" 
                       pattern=".{8,}" 
                       title="{{ __('login.password_min') }}"
                       required>
                <small class="text-14 text-light-1">{{ __('login.password_min') }}</small>
              </div>

              <div class="col-12">
                <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('login.confirm_password') }} *</label>
                <input type="password" name="password_confirmation" id="password_confirmation" 
                       placeholder="{{ __('login.confirm_password') }}" 
                       required>
                <div id="password-match-error" class="text-14 text-red-1 mt-5" style="display: none;"></div>
              </div>

              <div class="col-12">
                <button type="submit" class="button -md -green-1 text-dark-1 fw-500 w-1/1">
                  {{ __('login.reset_password_button') }}
                </button>
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
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirmation');
    const passwordMatchError = document.getElementById('password-match-error');
    
    // Validate password confirmation in real-time
    passwordConfirmInput.addEventListener('input', function() {
        if (passwordInput.value !== passwordConfirmInput.value) {
            passwordMatchError.textContent = '{{ __("login.password_confirmed") }}';
            passwordMatchError.style.display = 'block';
        } else {
            passwordMatchError.style.display = 'none';
        }
    });
    
    // Validate password on submit
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        if (passwordInput.value.length < 8) {
            e.preventDefault();
            passwordMatchError.textContent = '{{ __("login.password_min") }}';
            passwordMatchError.style.display = 'block';
            return false;
        }
        
        if (passwordInput.value !== passwordConfirmInput.value) {
            e.preventDefault();
            passwordMatchError.textContent = '{{ __("login.password_confirmed") }}';
            passwordMatchError.style.display = 'block';
            return false;
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
