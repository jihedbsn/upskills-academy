<!-- Header with Sort -->
<div class="row y-gap-20 justify-between items-center mb-30">
  <div class="col-auto">
    @php
      $totalCount = 0;
      if(request('show_type') == 'packages') {
        $totalCount = $packages->total();
      } elseif(request('show_type') == 'courses') {
        $totalCount = $courses->total();
      } else {
        $totalCount = $courses->total() + $packages->total();
      }
    @endphp
    <h3 class="text-24 fw-600 text-dark-1">
      @if(request('show_type') == 'packages')
        {{ __('messages.packages_available', ['count' => $totalCount]) }}
      @elseif(request('show_type') == 'courses')
        {{ __('messages.courses_available', ['count' => $totalCount]) }}
      @else
        {{ __('messages.courses_and_packages_available', ['count' => $totalCount]) }}
      @endif
    </h3>
  </div>

  <div class="col-auto">
    <div class="d-flex items-center">
      <div class="text-14 text-light-1 mr-15">{{ __('messages.sort_by') }}:</div>
      <form method="GET" action="{{ route('filter.index') }}" class="d-flex">
        @foreach(request()->except('sort') as $key => $value)
          @if(is_array($value))
            @foreach($value as $val)
              <input type="hidden" name="{{ $key }}[]" value="{{ $val }}">
            @endforeach
          @else
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
          @endif
        @endforeach
        <select name="sort" class="form-select">
          <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>{{ __('messages.latest') }}</option>
          <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>{{ __('messages.popular') }}</option>
          <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>{{ __('messages.price_low_to_high') }}</option>
          <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>{{ __('messages.price_high_to_low') }}</option>
          <option value="title_az" {{ request('sort') == 'title_az' ? 'selected' : '' }}>{{ __('messages.title_a_to_z') }}</option>
          <option value="title_za" {{ request('sort') == 'title_za' ? 'selected' : '' }}>{{ __('messages.title_z_to_a') }}</option>
        </select>
      </form>
    </div>
  </div>
</div>

<!-- Courses Grid -->
@php
  $showCourses = request('show_type', 'all') == 'all' || request('show_type') == 'courses';
  $showPackages = request('show_type', 'all') == 'all' || request('show_type') == 'packages';
  $hasContent = ($showCourses && $courses->count() > 0) || ($showPackages && $packages->count() > 0);
@endphp

@if($hasContent)
  <div class="row y-gap-30">
    {{-- Display Packages --}}
    @if($showPackages && $packages->count() > 0)
      @foreach($packages as $package)
        <div class="col-lg-4 col-md-6">
          @include('filter.components.package-card', ['package' => $package])
        </div>
      @endforeach
    @endif

    {{-- Display Courses --}}
    @if($showCourses && $courses->count() > 0)
      @foreach($courses as $course)
        <div class="col-lg-4 col-md-6">
          @include('filter.components.products', ['course' => $course])
        </div>
      @endforeach
    @endif
  </div>

  <!-- Pagination -->
  <div class="row justify-center pt-60 lg:pt-40">
    <div class="col-auto">
      @if(request('show_type') == 'packages')
        {{ $packages->appends(request()->except('page'))->links('vendor.pagination.custom') }}
      @elseif(request('show_type') == 'courses')
        {{ $courses->appends(request()->except('page'))->links('vendor.pagination.custom') }}
      @else
        {{-- For 'all' type, show both pagination if needed --}}
        @if($courses->hasPages())
          <div class="mb-3">
            <span class="text-14 text-dark-1">{{ __('messages.courses') }}:</span>
            {{ $courses->appends(request()->except('page'))->links('vendor.pagination.custom') }}
          </div>
        @endif
        @if($packages->hasPages())
          <div>
            <span class="text-14 text-dark-1">{{ __('messages.packages') }}:</span>
            {{ $packages->appends(request()->except('page'))->links('vendor.pagination.custom') }}
          </div>
        @endif
      @endif
    </div>
  </div>
@else
  <!-- No Courses Found -->
  <div class="text-center py-80">
    <div class="icon-graduation-cap text-80 text-light-1 mb-30"></div>
    <h3 class="text-24 fw-600 text-dark-1 mb-15">{{ __('messages.no_courses_found') }}</h3>
    <p class="text-16 text-light-1 mb-30">{{ __('messages.no_courses_description') }}</p>
    <a href="{{ route('filter.index') }}" class="button -md -purple-1 text-white">{{ __('messages.clear_filters') }}</a>
  </div>
@endif
