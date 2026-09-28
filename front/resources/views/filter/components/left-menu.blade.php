<div class="sidebar -courses">
  <form method="GET" action="{{ route('filter.index') }}" class="filter-form">
    @if(request()->has('sort'))
      <input type="hidden" name="sort" value="{{ request('sort') }}">
    @endif

    <!-- Search -->
    <div class="sidebar__item">
      <h5 class="sidebar__title">{{ __('messages.search') }}</h5>
      <div class="sidebar-search">
        <div class="form-input">
          <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('messages.search_courses') }}">
          <button type="submit">
            <i class="icon icon-search"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Categories -->
    <div class="sidebar__item">
      <h5 class="sidebar__title">{{ __('messages.categories') }}</h5>
      <div class="sidebar-checkbox">
        <label class="sidebar-checkbox__item" style="cursor: pointer;">
          <div class="form-radio mr-10">
            <div class="radio">
              <input type="radio" name="category" value="" {{ request('category') == '' ? 'checked' : '' }}>
              <div class="radio__mark">
                <div class="radio__icon"></div>
              </div>
            </div>
          </div>
          <div class="sidebar-checkbox__title">{{ __('messages.all_categories') }}</div>
          <div class="sidebar-checkbox__count">({{ $stats['total'] }})</div>
        </label>

        @foreach($categories as $category)
        <label class="sidebar-checkbox__item" style="cursor: pointer;">
          <div class="form-radio mr-10">
            <div class="radio">
              <input type="radio" name="category" value="{{ $category->id }}" {{ request('category') == $category->id ? 'checked' : '' }}>
              <div class="radio__mark">
                <div class="radio__icon"></div>
              </div>
            </div>
          </div>
          <div class="sidebar-checkbox__title">{{ $category->titre }}</div>
          <div class="sidebar-checkbox__count">({{ $category->products_count }})</div>
        </label>
        @endforeach
      </div>
    </div>

    <!-- Type -->
    <div class="sidebar__item">
      <h5 class="sidebar__title">{{ __('messages.type') }}</h5>
      <div class="sidebar-checkbox">
        @foreach($types as $type)
        <label class="sidebar-checkbox__item" style="cursor: pointer;">
          <div class="form-checkbox">
            <input type="checkbox" name="type" value="{{ $type }}" {{ in_array($type, (array)request('type')) ? 'checked' : '' }}>
            <div class="form-checkbox__mark">
              <div class="form-checkbox__icon icon-check"></div>
            </div>
          </div>
          <div class="sidebar-checkbox__title">{{ __('messages.' . $type) }}</div>
        </label>
        @endforeach
      </div>
    </div>

    <!-- Price Range -->
    <div class="sidebar__item">
      <h5 class="sidebar__title">{{ __('messages.price_range') }}</h5>
      <div class="sidebar-price-filter">
        <div class="price-slider-wrapper">
          <div class="price-values">
            <span id="price-min-display">${{ request('prix_min', 0) }}</span>
            <span class="separator">-</span>
            <span id="price-max-display">${{ request('prix_max', $maxPrice) }}</span>
          </div>
          <div class="slider-container">
            <div class="slider-track"></div>
            <input type="range" min="0" max="{{ $maxPrice }}" value="{{ request('prix_min', 0) }}" id="slider-min" class="slider-input">
            <input type="range" min="0" max="{{ $maxPrice }}" value="{{ request('prix_max', $maxPrice) }}" id="slider-max" class="slider-input">
          </div>
        </div>

        <input type="hidden" name="prix_min" id="input-min" value="{{ request('prix_min', 0) }}">
        <input type="hidden" name="prix_max" id="input-max" value="{{ request('prix_max', $maxPrice) }}">

        <button type="submit" class="button -sm -purple-1 text-white mt-10 w-100">{{ __('messages.filter') }}</button>
      </div>
    </div>

    <!-- Price Filter -->
    <div class="sidebar__item">
      <h5 class="sidebar__title">{{ __('messages.price') }}</h5>
      <div class="sidebar-checkbox">
        <label class="sidebar-checkbox__item" style="cursor: pointer;">
          <div class="form-radio mr-10">
            <div class="radio">
              <input type="radio" name="price_type" value="" {{ request('price_type') == '' ? 'checked' : '' }}>
              <div class="radio__mark">
                <div class="radio__icon"></div>
              </div>
            </div>
          </div>
          <div class="sidebar-checkbox__title">{{ __('messages.all') }}</div>
          <div class="sidebar-checkbox__count">({{ $stats['total'] }})</div>
        </label>

        <label class="sidebar-checkbox__item" style="cursor: pointer;">
          <div class="form-radio mr-10">
            <div class="radio">
              <input type="radio" name="price_type" value="free" {{ request('price_type') == 'free' ? 'checked' : '' }}>
              <div class="radio__mark">
                <div class="radio__icon"></div>
              </div>
            </div>
          </div>
          <div class="sidebar-checkbox__title">{{ __('messages.free') }}</div>
          <div class="sidebar-checkbox__count">({{ $stats['free'] }})</div>
        </label>

        <label class="sidebar-checkbox__item" style="cursor: pointer;">
          <div class="form-radio mr-10">
            <div class="radio">
              <input type="radio" name="price_type" value="paid" {{ request('price_type') == 'paid' ? 'checked' : '' }}>
              <div class="radio__mark">
                <div class="radio__icon"></div>
              </div>
            </div>
          </div>
          <div class="sidebar-checkbox__title">{{ __('messages.paid') }}</div>
          <div class="sidebar-checkbox__count">({{ $stats['paid'] }})</div>
        </label>
      </div>
    </div>
  </form>

  <!-- Clear Filters -->
  @if(request()->hasAny(['search', 'category', 'type', 'price_type', 'prix_min', 'prix_max', 'sort', 'show_type']))
  <div class="sidebar__item">
    <a href="{{ route('filter.index') }}" class="button -sm -outline-purple-1 text-purple-1 w-100">{{ __('messages.clear_filters') }}</a>
  </div>
  @endif
</div>
