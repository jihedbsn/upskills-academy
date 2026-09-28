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
<section class="form-page">
  <div class="form-page__img bg-dark-1" style="background-image: linear-gradient(200deg, rgba(8,29,54,0.18) 0%, rgba(9,49,98,0.14) 50%, rgba(8,29,54,0.55) 100%), url('{{ asset('assets/img/hero/register.webp') }}');background-size:cover;background-repeat:no-repeat;background-position: center;">
    <div class="us-auth-decowrap" aria-hidden="true">
      <div class="us-auth-deco us-auth-deco--dots" aria-hidden="true"></div>
      <div class="us-auth-deco us-auth-deco--ring" aria-hidden="true"></div>
      <div class="us-auth-chip us-auth-chip--top us-auth-float" style="--f:0s" aria-hidden="true">
        <span>🎉</span>
        <span>{{ __('messages.auth_chip_join') }}</span>
      </div>
      <div class="us-auth-chip us-auth-chip--mid us-auth-float" style="--f:1.4s" aria-hidden="true">
        <span class="us-auth-chip__stars">★★★★★</span>
        <span>{{ __('messages.auth_chip_rating') }}</span>
      </div>
    </div>
  </div>

  <div class="form-page__content lg:py-50">
    <div class="container">
      <div class="row justify-center items-center">
        <div class="col-xl-8 col-lg-9">
          <div class="px-50 py-50 md:px-25 md:py-25 bg-white shadow-1 rounded-16">
            <h3 class="text-30 lh-13">{{ __('login.signup_title') }}</h3>
            <p class="mt-10">{{ __('login.already_have_account') }} <a href="{{ route('login') }}" class="text-purple-1">{{ __('login.login_title') }}</a></p>

            @if ($errors->any())
              <div class="alert alert-danger mt-20">
                <ul class="mb-0">
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            <form class="contact-form respondForm__form row y-gap-20 pt-30" action="{{ route('signup.post') }}" method="POST">
              @csrf
              @if(request('ref'))
                <input type="hidden" name="ref" value="{{ request('ref') }}">
              @endif

              <!-- Email -->
              <div class="col-12">
                <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('login.email') }} *</label>
                <input type="email" id="email" name="email" placeholder="{{ __('login.enter_email') }}" value="{{ old('email') }}" required>
                <small id="email-error" class="text-danger" style="display: none;"></small>
              </div>

              <!-- Password -->
              <div class="col-lg-6">
                <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('login.password') }} *</label>
                <div class="password-wrapper">
                  <input type="password" id="password" name="password" placeholder="{{ __('login.enter_password') }}" required minlength="8">
                  <button type="button" class="password-toggle" onclick="togglePassword('password', this)" aria-label="Toggle password visibility">
                    <i class="fa fa-eye"></i>
                  </button>
                </div>
                <div id="password-strength" class="mt-5" style="display: none;">
                  <small id="password-strength-text" class="text-muted"></small>
                </div>
                <small id="password-error" class="text-danger" style="display: none;"></small>
                <small class="text-muted d-block mt-5">Minimum 8 characters</small>
              </div>

              <!-- Confirm Password -->
              <div class="col-lg-6">
                <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('login.confirm_password') }} *</label>
                <div class="password-wrapper">
                  <input type="password" id="password_confirmation" name="password_confirmation" placeholder="{{ __('login.confirm_password') }}" required minlength="8">
                  <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation', this)" aria-label="Toggle password visibility">
                    <i class="fa fa-eye"></i>
                  </button>
                </div>
                <small id="password_confirmation-error" class="text-danger" style="display: none;"></small>
              </div>

              <div class="col-12">
                <button type="submit" name="submit" id="submit" class="button -md -green-1 text-dark-1 fw-500 w-1/1">
                  <span class="button-text">{{ __('login.register') }}</span>
                  <span class="button-loading" style="display: none;">
                    <i class="fa fa-spinner fa-spin"></i> {{ __('login.registering') }}...
                  </span>
                </button>
              </div>
            </form>

            <!-- Divider -->
            {{-- Social signup hidden for now (Google) — restore by removing this comment wrapper
            <div class="d-flex align-items-center my-20">
              <div class="flex-grow-1 border-top"></div>
              <span class="mx-3 text-muted">{{ __('login.or') }}</span>
              <div class="flex-grow-1 border-top"></div>
            </div>

            <!-- Social signup buttons -->
            <div class="row y-gap-10">
              <div class="col-12">
                <a href="{{ route('login.google') }}" class="button -md -outline-dark-1 text-dark-1 fw-500 w-1/1 d-flex justify-content-center align-items-center" style="gap: 10px;">
                  <svg width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                  </svg>
                  {{ __('login.signup_google') }}
                </a>
              </div>
              {{-- Facebook signup disabled for now
              <div class="col-12">
                <a href="{{ route('login.facebook') }}" class="button -md -outline-dark-1 text-dark-1 fw-500 w-1/1 d-flex justify-content-center align-items-center" style="gap: 10px;">
                  <i class="fab fa-facebook text-18" style="color: #1877F2;"></i>
                  {{ __('login.signup_facebook') }}
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
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.contact-form');
    const submitButton = document.getElementById('submit');
    const buttonText = submitButton.querySelector('.button-text');
    const buttonLoading = submitButton.querySelector('.button-loading');

    // Validation patterns
    const patterns = {
        name: /^[a-zA-ZÀ-ÿ\s\-']{2,255}$/,
        email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
        password: /^.{8,}$/
    };

    // Error messages
    const errorMessages = {
        email: 'Please enter a valid email address',
        password: 'Password must be at least 8 characters',
        password_confirmation: 'Passwords do not match'
    };

    // Show/hide error
    function showError(fieldId, message) {
        const errorEl = document.getElementById(fieldId + '-error');
        if (errorEl) {
            errorEl.textContent = message;
            errorEl.style.display = 'block';
        }
    }

    function hideError(fieldId) {
        const errorEl = document.getElementById(fieldId + '-error');
        if (errorEl) {
            errorEl.style.display = 'none';
        }
    }

    function resetFieldStyle(fieldId) {
        // No styling changes needed
    }

    // Validate individual fields
    function validateField(fieldId, value) {
        if (fieldId === 'email') {
            if (!value.trim()) {
                showError(fieldId, 'Email is required');
                return false;
            }
            if (!patterns.email.test(value)) {
                showError(fieldId, errorMessages.email);
                return false;
            }
        }

        if (fieldId === 'password') {
            if (!value) {
                showError(fieldId, 'Password is required');
                return false;
            }
            if (!patterns.password.test(value)) {
                showError(fieldId, errorMessages.password);
                return false;
            }
        }

        if (fieldId === 'password_confirmation') {
            const password = document.getElementById('password').value;
            if (value !== password) {
                showError(fieldId, errorMessages.password_confirmation);
                return false;
            }
        }

        hideError(fieldId);
        return true;
    }

    // Password strength checker
    function checkPasswordStrength(password) {
        const strengthText = document.getElementById('password-strength-text');
        const strengthContainer = document.getElementById('password-strength');

        if (!password) {
            strengthContainer.style.display = 'none';
            return;
        }

        strengthContainer.style.display = 'block';

        if (password.length < 8) {
            strengthText.textContent = 'Too short (min 8 characters)';
        } else {
            strengthText.textContent = '✓ Password length OK';
        }
    }

    // Add event listeners for real-time validation
    const fieldsToValidate = ['email', 'password', 'password_confirmation'];

    fieldsToValidate.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('blur', function() {
                validateField(fieldId, this.value);
            });

            field.addEventListener('input', function() {
                if (fieldId === 'password') {
                    checkPasswordStrength(this.value);
                }
                // Clear error styling on input
                if (this.value.trim()) {
                    validateField(fieldId, this.value);
                } else {
                    resetFieldStyle(fieldId);
                }
            });
        }
    });

    // Phone number formatting - allow only digits
    const phoneField = document.getElementById('phone');
    if (phoneField) {
        phoneField.addEventListener('input', function(e) {
            // Remove all non-digit characters
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    }

    // Form submission validation
    form.addEventListener('submit', function(e) {
        let isValid = true;

        // Validate all required fields
        ['email', 'password', 'password_confirmation'].forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field && !validateField(fieldId, field.value)) {
                isValid = false;
            }
        });

        if (!isValid) {
            e.preventDefault();
            // Scroll to first error
            const firstError = document.querySelector('.text-danger[style*="display: block"]');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return false;
        }

        // Disable button and show loading
        submitButton.disabled = true;
        buttonText.style.display = 'none';
        buttonLoading.style.display = 'inline';
        form.style.pointerEvents = 'none';
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

/* Simple validation styles */
.text-danger {
  color: #dc3545;
  font-size: 12px;
  margin-top: 5px;
}

/* Progress bar for password strength */
.progress {
  height: 5px;
  background-color: #e9ecef;
  border-radius: 3px;
  overflow: hidden;
}

.progress-bar {
  height: 100%;
  transition: width 0.3s ease;
  background-color: #28a745;
}

.d-block { display: block; }
.mt-5 { margin-top: 5px; }
</style>

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
