@extends('layouts.app')

@section('content')
<style>
  /* Fix zoom and responsive issues */
  .form-page {
    min-height: 100vh !important;
    height: auto !important;
    display: flex !important;
    flex-wrap: wrap !important;
  }

  .form-page__img {
    width: 40% !important;
    min-height: 100vh !important;
    flex-shrink: 0 !important;
  }

  .form-page__content {
    width: 60% !important;
    min-height: 100vh !important;
    flex-shrink: 0 !important;
    overflow-x: hidden !important;
  }

  /* Tablet and below */
  @media (max-width: 991px) {
    .form-page {
      flex-direction: column !important;
    }
    .form-page__img {
      width: 100% !important;
      min-height: 300px !important;
      height: 300px !important;
    }
    .form-page__content {
      width: 100% !important;
      min-height: auto !important;
    }
  }

  /* Handle high zoom levels */
  @media (min-width: 992px) and (max-width: 1400px) {
    .form-page__img {
      width: 35% !important;
    }
    .form-page__content {
      width: 65% !important;
    }
  }

  /* Form container responsive */
  .form-page__content .container {
    max-width: 100% !important;
    padding-left: 20px !important;
    padding-right: 20px !important;
  }

  .form-page__content .px-50 {
    padding-left: 30px !important;
    padding-right: 30px !important;
  }

  @media (max-width: 767px) {
    .form-page__content .px-50 {
      padding-left: 20px !important;
      padding-right: 20px !important;
    }
  }

  /* Remember Me Checkbox Styling */
  .form-checkbox {
    position: relative;
    display: flex !important;
    align-items: center;
  }

  .form-checkbox input[type="checkbox"] {
    width: 18px !important;
    height: 18px !important;
    cursor: pointer;
    accent-color: #65C2F9;
    display: inline-block !important;
    opacity: 1 !important;
    visibility: visible !important;
    position: relative !important;
    margin: 0 !important;
    flex-shrink: 0;
  }

  .form-checkbox label {
    margin-bottom: 0;
    user-select: none;
    display: inline-block;
  }

  @media (max-width: 576px) {
    .col-12.d-flex.justify-between {
      flex-direction: column;
      align-items: flex-start !important;
      gap: 15px;
    }
  }

  /* Password visibility toggle */
  .password-wrapper {
    position: relative;
  }

  .password-wrapper input {
    padding-right: 45px !important;
  }

  .password-toggle {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    color: #6c757d;
    font-size: 18px;
    padding: 5px;
    z-index: 10;
  }

  .password-toggle:hover {
    color: #333;
  }

  .password-toggle:focus {
    outline: none;
  }
</style>
<section class="form-page js-mouse-move-container">
  <div class="form-page__img bg-dark-1" style="background-image: linear-gradient(200deg, rgba(8,29,54,0.18) 0%, rgba(9,49,98,0.14) 50%, rgba(8,29,54,0.55) 100%), url('{{ asset('assets/img/hero/login.webp') }}');background-size:cover;background-repeat:no-repeat;background-position: center;">
    <div class="us-auth-decowrap" aria-hidden="true">
      <div class="us-auth-deco us-auth-deco--dots" aria-hidden="true"></div>
      <div class="us-auth-deco us-auth-deco--ring" aria-hidden="true"></div>
      <div class="us-auth-chip us-auth-chip--top us-auth-float" style="--f:0s" aria-hidden="true">
        <span>{{ __('messages.auth_chip_welcome') }}</span>
        <span>👋</span>
      </div>
      <div class="us-auth-levels us-auth-float" style="--f:1.2s" aria-hidden="true">
        <span>A1</span><span>A2</span><span>B1</span><span>B2</span><span class="is-accent">C1</span>
      </div>
    </div>
  </div>

  <div class="form-page__content lg:py-50">
    <div class="container">
      <div class="row justify-center items-center">
        <div class="col-xl-6 col-lg-8">
          <div class="px-50 py-50 md:px-25 md:py-25 bg-white shadow-1 rounded-16">
                         <h3 class="text-30 lh-13">{{ __('login.login_title') }}</h3>
             <p class="mt-10">{{ __('login.dont_have_account') }}</p>
             <a href="{{ route('signup') }}" class="button -md -purple-1 text-white fw-500 mt-10" style="display: inline-block; padding: 12px 30px; border-radius: 8px;">
               {{ __('login.signup_free') }}
             </a>

            @if ($errors->any())
              <div class="alert alert-danger mt-20">
                <ul class="mb-0">
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            <form class="contact-form respondForm__form row y-gap-20 pt-30" action="{{ route('login.post') }}" method="POST">
              @csrf
              <div class="col-12">
                <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('login.email') }}</label>
                <input type="email" name="email" placeholder="{{ __('login.enter_email') }}" value="{{ old('email') }}" required>
              </div>
              <div class="col-12">
                <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('login.password') }}</label>
                <div class="password-wrapper">
                  <input type="password" name="password" id="login-password" placeholder="{{ __('login.enter_password') }}" required>
                  <button type="button" class="password-toggle" onclick="togglePassword('login-password', this)" aria-label="Toggle password visibility">
                    <i class="fa fa-eye"></i>
                  </button>
                </div>
              </div>
              <div class="col-12 d-flex justify-between items-center">
                <div class="form-checkbox d-flex items-center">
                  <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                  <label for="remember" class="text-14 text-dark-1 ml-10 cursor-pointer">{{ __('login.remember_me') }}</label>
                </div>
                <a href="{{ route('forgot-password') }}" class="text-14 text-blue-1">{{ __('login.forgot_password') }}?</a>
              </div>
              <div class="col-12">
                <button type="submit" name="submit" id="submit" class="button -md -green-1 text-dark-1 fw-500 w-1/1">
                  {{ __('login.login_button') }}
                </button>
              </div>
            </form>

            {{-- Social login hidden for now (Google) — restore by removing this comment wrapper
            <!-- Social Login Divider -->
            <div class="mt-30 mb-20">
              <div class="text-center">
                <span class="text-14 text-grey-1">{{ __('messages.or_continue_with') }}</span>
              </div>
            </div>

            <!-- Social Login Buttons -->
            <div class="row y-gap-15">
              <div class="col-12">
                <a href="{{ route('login.google') }}" class="button -md -outline-dark-1 text-dark-1 fw-500 w-1/1 d-flex align-items-center justify-center">
                  <i class="fab fa-google me-2 text-18"></i>
                  {{ __('messages.login_with_google') }}
                </a>
              </div>
              {{-- Facebook login disabled for now
              <div class="col-12">
                <a href="{{ route('login.facebook') }}" class="button -md -outline-dark-1 text-dark-1 fw-500 w-1/1 d-flex align-items-center justify-center">
                  <i class="fab fa-facebook me-2 text-18"></i>
                  {{ __('messages.login_with_facebook') }}
                </a>
              </div>
            </div>
            --}}

          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
function togglePassword(inputId, button) {
  const input = document.getElementById(inputId);
  const icon = button.querySelector('i');

  if (input.type === 'password') {
    input.type = 'text';
    icon.classList.remove('fa-eye');
    icon.classList.add('fa-eye-slash');
  } else {
    input.type = 'password';
    icon.classList.remove('fa-eye-slash');
    icon.classList.add('fa-eye');
  }
}
</script>
@endsection
