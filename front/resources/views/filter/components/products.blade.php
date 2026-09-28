<a href="{{ route('courses.show', $course->id) }}" class="coursesCard -type-1" data-course-id="{{ $course->id }}">
  <div class="coursesCard__image overflow-hidden rounded-8">
    <img class="w-1/1"
         src="{{ $course->image ? config('app.file_url') . 'uploads/products/images/' . $course->image : asset('assets/img/placeholders/course-not-found.png') }}"
         alt="{{ $course->titre }}"
         onerror="this.src='{{ asset('assets/img/placeholders/course-not-found.png') }}'">
    <div class="coursesCard__image_overlay rounded-8"></div>

    <!-- Course Badge - Show category -->
    <div class="course-badge-overlay">
      @if($course->category)
        <div class="px-15 rounded-200" style="background-color: #0071ae;">
          <span class="text-11 lh-1 uppercase fw-500 text-white">{{ $course->category->titre }}</span>
        </div>
      @else
        <div class="px-15 rounded-200" style="background-color: #6b7280;">
          <span class="text-11 lh-1 uppercase fw-500 text-white">{{ __('messages.uncategorized') }}</span>
        </div>
      @endif
    </div>

    <!-- Package Discount Badge -->
    @if(isset($package_discount) && $package_discount > 0)
      <div class="discount-badge-overlay">
        <div class="discount-badge-package" style="background: linear-gradient(135deg, #4CAF50, #45a049); border-color: #4CAF50;">
          @php
            $discountType = $package_discount_type ?? 'percentage';
          @endphp
          @if($discountType === 'fixed')
            <span class="text-13 fw-700 text-white">-@price($package_discount)</span>
          @else
            <span class="text-13 fw-700 text-white">-{{ number_format($package_discount, 0) }}%</span>
          @endif
        </div>
      </div>
    @endif
  </div>

  <div class="h-100 pt-15">
    <div class="text-17 lh-15 fw-500 text-dark-1 mt-10">
      @if($course->current_variation)
        {{ $course->current_variation->name }}
      @else
        {{ $course->titre }}
      @endif
    </div>

    <!-- Course Meta -->
    <div class="d-flex items-center mt-15">
      @if($course->teacher)
        <div class="d-flex items-center mr-20">
          <div class="icon-user text-14 mr-5" style="color: #0071ae;"></div>
          <div class="text-12 text-light-1">{{ $course->teacher->full_name }}</div>
        </div>
      @endif

      @if($course->category)
        <div class="d-flex items-center">
          <div class="icon-folder text-14 mr-5" style="color: #0071ae;"></div>
          <div class="text-12 text-light-1">{{ $course->category->titre }}</div>
        </div>
      @endif
    </div>

    <!-- Course Stats -->
    <div class="d-flex items-center mt-15">
      @if($course->lessons_count)
        <div class="d-flex items-center mr-20">
          <div class="icon-play text-14 mr-5" style="color: #0071ae;"></div>
          <div class="text-12 text-light-1">{{ $course->lessons_count }} {{ __('messages.lessons') }}</div>
        </div>
      @endif

      @if($course->duration)
        <div class="d-flex items-center">
          <div class="icon-clock text-14 mr-5" style="color: #0071ae;"></div>
          <div class="text-12 text-light-1">{{ $course->duration }}</div>
        </div>
      @endif
    </div>

    <div class="coursesCard-footer">
      <div class="coursesCard-footer__price">
        @if($course->prix > 0)
          @if(isset($package_discount) && $package_discount > 0)
            <div class="price-with-discount">
              <div class="price-original">@price($course->prix)</div>
              @php
                $discountType = $package_discount_type ?? 'percentage';
                if ($discountType === 'fixed') {
                  $discountAmount = $package_discount;
                  $discountedPrice = max(0, $course->prix - $discountAmount);
                } else {
                  $discountAmount = $course->prix * $package_discount / 100;
                  $discountedPrice = $course->prix - $discountAmount;
                }
                $savingsAmount = $course->prix - $discountedPrice;
              @endphp
              <div class="price-discounted" style="color: #0071ae; font-weight: 700;">@price($discountedPrice)</div>
              <div class="price-savings" style="display: inline-block; background-color: #4CAF50; color: #ffffff; padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-top: 5px; box-shadow: 0 2px 6px rgba(76, 175, 80, 0.2);">
                {{ __('messages.save') }} @price($savingsAmount)
              </div>
            </div>
          @else
            <div>@price($course->prix)</div>
          @endif
        @else
          <div class="text-green-1">{{ __('messages.free') }}</div>
        @endif
      </div>
    </div>
  </div>
</a>
