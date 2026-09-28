@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
  <div class="row y-gap-30">
    <div class="col-12">
      <div class="dashboard__content bg-white rounded-8 shadow-1">
        <div class="py-30 px-30">
          <div class="d-flex items-center justify-between mb-30">
            <h4 class="text-18 lh-1 fw-500">{{ __('messages.profile_settings') }}</h4>
          </div>

          @if(session('success'))
            <div class="alert alert-success mb-30">
              {{ session('success') }}
            </div>
          @endif

          @if($errors->any())
            <div class="alert alert-danger mb-30">
              <ul class="mb-0">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form action="{{ route('dashboard.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row y-gap-30">
              <!-- Profile Image -->
              <div class="col-12">
                <div class="d-flex items-center">
                  <div class="mr-30">
                    <img src="{{ $student->image_url }}" alt="Profile" class="size-100 rounded-full">
                  </div>
                  <div>
                    <h5 class="text-16 lh-1 fw-500 mb-10">{{ __('messages.profile_image') }}</h5>
                    <p class="text-14 lh-1 text-light-1 mb-20">{{ __('messages.image_optional') }}</p>
                    <input type="file" name="image" accept="image/*" class="form-control">
                  </div>
                </div>
              </div>

              <!-- Personal Information -->
              <div class="col-lg-6">
                <div class="form-input">
                  <input type="text" name="first_name" value="{{ old('first_name', $student->first_name) }}" required>
                  <label class="lh-1 text-14 text-light-1">{{ __('messages.first_name') }} *</label>
                </div>
              </div>

              <div class="col-lg-6">
                <div class="form-input">
                  <input type="text" name="last_name" value="{{ old('last_name', $student->last_name) }}" required>
                  <label class="lh-1 text-14 text-light-1">{{ __('messages.last_name') }} *</label>
                </div>
              </div>

              <div class="col-lg-6">
                <div class="form-input">
                  <input type="email" value="{{ $student->email }}" disabled>
                  <label class="lh-1 text-14 text-light-1">{{ __('messages.email') }} ({{ __('messages.cannot_change') }})</label>
                </div>
              </div>

              <div class="col-lg-6">
                <div class="form-input">
                  <input type="tel" name="phone" value="{{ old('phone', $student->phone) }}">
                  <label class="lh-1 text-14 text-light-1">{{ __('messages.phone') }}</label>
                </div>
              </div>

              <div class="col-lg-6">
                <div class="form-select">
                  <select name="country">
                    <option value="">{{ __('messages.select_country') }}</option>
                    @php
                      $countries = \App\Helpers\CountryHelper::getAllCountries();
                      $countriesArabic = \App\Helpers\CountryHelper::getAllCountriesArabic();
                      $isArabic = app()->getLocale() === 'ar';
                      $currentCountry = old('country', $student->country);
                    @endphp
                    @foreach($countries as $country)
                      <option value="{{ $country }}" {{ $currentCountry == $country ? 'selected' : '' }}>
                        {{ $isArabic && isset($countriesArabic[$country]) ? $countriesArabic[$country] : $country }}
                      </option>
                    @endforeach
                  </select>
                  <label class="lh-1 text-14 text-light-1">{{ __('messages.country') }}</label>
                </div>
              </div>

              <!-- Account Information -->
              <div class="col-12">
                <div class="border-top-light pt-30">
                  <h5 class="text-16 lh-1 fw-500 mb-20">{{ __('messages.account_information') }}</h5>

                  <div class="row y-gap-20">
                    <div class="col-lg-6">
                      <div class="form-input">
                        <input type="text" value="{{ $student->email }}" disabled>
                        <label class="lh-1 text-14 text-light-1">{{ __('messages.email') }}</label>
                      </div>
                    </div>

                    <div class="col-lg-6">
                      <div class="form-input">
                        <input type="text" value="{{ $student->created_at->format('d/m/Y') }}" disabled>
                        <label class="lh-1 text-14 text-light-1">{{ __('messages.member_since') }}</label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Submit Button -->
              <div class="col-12">
                <div class="border-top-light pt-30">
                  <button type="submit" class="button -md -purple-1 text-white">
                    {{ __('messages.save_changes') }}
                  </button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
