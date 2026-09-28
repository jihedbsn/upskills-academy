@extends('layouts.app')

@section('content')
<section class="layout-pt-lg layout-pb-lg">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="bg-white rounded-16 shadow-2 border-light px-40 py-50 text-center">
          <div class="d-inline-flex items-center justify-center size-80 rounded-full bg-red-1-light mb-30">
            <i class="fas fa-ban text-30 text-red-1"></i>
          </div>

          <h1 class="text-30 fw-600 text-dark-1 mb-15">
            {{ __('messages.course_access_blocked_title') }}
          </h1>

          @if($course)
            <p class="text-16 text-dark-1 fw-500 mb-10">
              {{ $course->titre ?? $course->title ?? __('messages.course') }}
            </p>
          @endif

          <p class="text-15 text-light-1 mb-30">
            {{ __('messages.course_access_blocked_message') }}
          </p>

          <div class="d-flex flex-wrap justify-center x-gap-15 y-gap-15">
            <a href="mailto:{{ config('mail.admin_email') }}" class="button -md -purple-1 text-white">
              <i class="fas fa-envelope me-2"></i>
              {{ __('messages.course_access_blocked_contact_admin') }}
            </a>

            @auth
              <a href="{{ route('student-dashboard.support.index', ['locale' => $locale]) }}" class="button -md -outline-purple-1 text-purple-1">
                <i class="fas fa-headset me-2"></i>
                {{ __('messages.contact_support') }}
              </a>
            @else
              <a href="{{ route('contact.index', ['locale' => $locale]) }}" class="button -md -outline-purple-1 text-purple-1">
                <i class="fas fa-headset me-2"></i>
                {{ __('messages.contact_support') }}
              </a>
            @endauth

            <a href="{{ route('courses.index', ['locale' => $locale]) }}" class="button -md -outline-dark-1 text-dark-1">
              {{ __('messages.browse_courses') }}
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
