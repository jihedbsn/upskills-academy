@extends('layouts.app')

@section('content')
<section class="form-page" style="min-height: 60vh; display: flex; align-items: center;">
  <div class="container">
    <div class="row justify-center">
      <div class="col-xl-8 col-lg-10">
        <div class="px-50 py-50 md:px-25 md:py-25 bg-white shadow-1 rounded-16 text-center">
          <h3 class="text-30 lh-13">{{ __('messages.account_deleted') ?? 'Your account has been deleted' }}</h3>
          <p class="mt-15 text-16 text-dark-1">{{ __('messages.redirecting_home') ?? 'You will be redirected to the home page shortly.' }}</p>
          <a href="{{ url('/') }}" class="button -md -purple-1 text-white fw-500 mt-20">{{ __('messages.go_home') ?? 'Go to Home' }}</a>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
  setTimeout(function () {
    window.location.href = "{{ url('/') }}";
  }, 3000);
</script>
@endsection
