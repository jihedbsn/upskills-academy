@if($product->teacher)
  <div id="coach" class="pt-60 lg:pt-40">
    <h2 class="text-20 fw-500">{{ __('messages.coach') }}</h2>
    <div class="mt-30">
      <div class="d-flex items-center">
        <div class="bg-image size-180 rounded-full js-lazy" data-bg="{{ $product->teacher->image_url }}"></div>
        <div class="ml-20 coach-info">
          <h4 class="text-18 fw-500">{{ $product->teacher->full_name }}</h4>
          <p class="text-14 text-light-1 mt-5">{{ __('messages.instructor') }}</p>
        </div>
      </div>
    </div>
  </div>
@endif
