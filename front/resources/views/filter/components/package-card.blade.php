@php
    $locale = app()->getLocale();
    $packageTitle = ($locale === 'ar' && $package->title_ar) ? $package->title_ar : ($package->title ?? 'Package');
    $packageDescription = ($locale === 'ar' && $package->description_ar) ? $package->description_ar : ($package->description ?? '');
    $totalPrice = $package->total_price;
    $discountedPrice = $package->discounted_price;
    $savings = $package->savings_amount;
    $productsCount = $package->products->count();
@endphp

<a href="{{ route('packages.show', ['locale' => $locale, 'package' => $package->id]) }}" class="coursesCard -type-1" data-package-id="{{ $package->id }}">
  <div class="coursesCard__image overflow-hidden rounded-8">
    <img class="w-1/1"
         src="{{ $package->image ? config('app.file_url') . 'uploads/package/' . $package->image : asset('assets/img/placeholders/course-not-found.png') }}"
         alt="{{ $packageTitle }}"
         onerror="this.src='{{ asset('assets/img/placeholders/course-not-found.png') }}'">
    <div class="coursesCard__image_overlay rounded-8"></div>

    <!-- Package Badge -->
    <div class="course-badge-overlay">
      <div class="px-15 rounded-200" style="background-color: #0071ae;">
        <span class="text-11 lh-1 uppercase fw-500 text-white">{{ __('messages.package') }}</span>
      </div>
    </div>

    <!-- Savings Badge -->
    @if($savings > 0)
      <div class="discount-badge-overlay">
        <div class="discount-badge-package" style="background: linear-gradient(135deg, #4CAF50, #45a049); border-color: #4CAF50;">
          @php
            $currencySymbol = session('currency', 'USD') === 'TND' ? 'TND' : '$';
          @endphp
          <span class="text-13 fw-700 text-white">{{ __('messages.save') }} {{ $currencySymbol }} {{ number_format($savings, 2) }}</span>
        </div>
      </div>
    @endif
  </div>

  <div class="h-100 pt-15">
    <div class="text-17 lh-15 fw-500 text-dark-1 mt-10">
      {{ Str::limit($packageTitle, 60) }}
    </div>

    <!-- Package Meta -->
    <div class="d-flex items-center mt-15">
      <div class="d-flex items-center mr-20">
        <i class="fas fa-cube text-14 mr-5" style="color: #0071ae;"></i>
        <div class="text-12 text-light-1">{{ __('messages.products_count', ['count' => $productsCount]) }}</div>
      </div>

      <div class="d-flex items-center">
        <i class="fas fa-tags text-14 mr-5" style="color: #0071ae;"></i>
        <div class="text-12 text-light-1">{{ __('messages.bundle_offer') }}</div>
      </div>
    </div>

    @if($packageDescription)
      <div class="text-13 text-light-1 mt-15" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
        {{ strip_tags($packageDescription) }}
      </div>
    @endif

    <div class="coursesCard-footer">
      <div class="coursesCard-footer__price">
        @if($savings > 0)
          <div class="price-with-discount">
            <div class="price-original">@price($totalPrice)</div>
            <div class="price-discounted" style="color: #0071ae; font-weight: 700;">@price($discountedPrice)</div>
            <div class="price-savings" style="display: inline-block; background-color: #4CAF50; color: #ffffff; padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-top: 5px; box-shadow: 0 2px 6px rgba(76, 175, 80, 0.2);">
              {{ __('messages.save') }} @price($savings)
            </div>
          </div>
        @else
          <div style="color: #0071ae; font-weight: 700; font-size: 18px;">@price($totalPrice)</div>
        @endif
      </div>
    </div>
  </div>
</a>
