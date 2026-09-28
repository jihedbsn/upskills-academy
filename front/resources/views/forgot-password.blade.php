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
            <h3 class="text-30 lh-13">{{ __('login.forgot_password_title') }}</h3>
            <p class="mt-10">{{ __('login.forgot_password_message') }}</p>

            @if ($errors->any())
              <div class="alert alert-danger mt-20">
                <ul class="mb-0">
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            @if (session('success'))
              <div class="alert alert-success mt-20">
                {{ session('success') }}
              </div>
            @endif

            <form class="contact-form respondForm__form row y-gap-20 pt-30" action="{{ route('send-code') }}" method="POST">
              @csrf
              
              <div class="col-12">
                <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('login.email') }} *</label>
                <input type="email" name="email" placeholder="{{ __('login.enter_email') }}" value="{{ old('email') }}" required>
              </div>

              <div class="col-12">
                <button type="submit" name="submit" class="button -md -green-1 text-dark-1 fw-500 w-1/1">
                  {{ __('login.send_reset_code') }}
                </button>
              </div>

              <div class="col-12 text-center">
                <a href="{{ route('login') }}" class="text-14 text-purple-1">{{ __('login.back_to_login') }}</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<style>
/* Minimum height for form pages on desktop */
@media (min-width: 992px) {
  .form-page {
    min-height: 800px;
  }
}
</style>
@endsection
