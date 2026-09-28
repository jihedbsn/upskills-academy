@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
  <div class="row y-gap-30">
    <div class="col-12">
      <div class="dashboard__content bg-white rounded-8 shadow-1">
        <div class="py-30 px-30">
          <div class="d-flex items-center justify-between mb-30">
            <h4 class="text-18 lh-1 fw-500">{{ __('messages.certificates') }}</h4>
          </div>

          @if(empty($certificates))
            <div class="text-center py-50">
              <i class="text-60 icon-award text-light-3 mb-20"></i>
              <h5 class="text-16 lh-1 fw-500 mb-10">{{ __('messages.no_certificates_yet') }}</h5>
              <p class="text-14 lh-1 text-light-1">{{ __('messages.complete_courses_message') }}</p>
              <a href="{{ route('dashboard.courses') }}" class="button -md -green-1 text-white mt-20">
                {{ __('messages.browse_courses') }}
              </a>
            </div>
          @else
            <div class="row y-gap-30">
              @foreach($certificates as $certificate)
                <div class="col-lg-4 col-md-6">
                  <div class="coursesCard -type-1">
                    <div class="coursesCard__image">
                      <img src="{{ $certificate->image }}" alt="{{ $certificate->title }}">
                      <div class="coursesCard__image_overlay">
                        <div class="coursesCard__level">{{ __('messages.completed') }}</div>
                      </div>
                    </div>
                    <div class="coursesCard__content">
                      <h5 class="coursesCard__title">
                        <a href="#">{{ $certificate->title }}</a>
                      </h5>
                      <p class="coursesCard__text">{{ $certificate->course_name }}</p>
                      <div class="coursesCard__footer">
                        <div class="coursesCard__footer_left">
                          <div class="text-14 text-light-1">{{ $certificate->issued_date }}</div>
                        </div>
                        <div class="coursesCard__footer_right">
                          <a href="#" class="button -sm -green-1 text-white">{{ __('messages.download') }}</a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
