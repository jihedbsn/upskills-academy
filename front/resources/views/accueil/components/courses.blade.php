<!-- Popular Courses Section -->
<section class="layout-pt-sm layout-pb-lg">
  <div data-anim-wrap class="container">
    <div class="row justify-center text-center">
      <div class="col-auto">
        <div class="sectionTitle">
          <h2 class="sectionTitle__title">{{ __('messages.courses_title') }}</h2>
          <p class="sectionTitle__text">{{ __('messages.courses_subtitle') }}</p>
        </div>
      </div>
    </div>

    <div class="row y-gap-30 justify-center pt-50">
      @if(isset($coursesAcceuil) && $coursesAcceuil->count() > 0)
        @foreach($coursesAcceuil as $index => $courseAcceuil)
          <div class="col-lg-4 col-md-6">
            <div data-anim-child="slide-up delay-{{ $index + 1 }}">
              <a href="{{ route('courses.show', $courseAcceuil->product->id) }}" class="coursesCard -type-1 -hover-shadow border-light rounded-8" data-course-id="{{ $courseAcceuil->product->id }}">
                <div class="relative">
                  <div class="coursesCard__image overflow-hidden rounded-top-8">
                    <img class="w-1/1"
                         src="{{ $courseAcceuil->product->image ? config('app.file_url') . 'uploads/products/images/' . $courseAcceuil->product->image : asset('assets/img/coursesCards/' . ($index + 1) . '.png') }}"
                         alt="{{ $courseAcceuil->product->titre ?? 'Course Image' }}"
                         onerror="this.src='{{ asset('assets/img/coursesCards/' . ($index + 1) . '.png') }}'">
                    <div class="coursesCard__image_overlay rounded-top-8"></div>

                    <!-- Course Badge -->

                  </div>
                </div>

                <div class="h-100 pt-15 pb-10 px-20">
                  <!-- Instructor and Rating -->
                  <div class="d-flex items-center justify-between mb-15">
                    <div class="d-flex items-center">
                      <img src="{{ $courseAcceuil->product->teacher ? $courseAcceuil->product->teacher->image_url : asset('assets/img/general/avatar-1.png') }}" alt="instructor" class="w-30 h-30 rounded-full mr-10">
                      <div class="text-13 text-dark-1">{{ $courseAcceuil->product->teacher->full_name ?? 'David Warner' }}</div>
                    </div>
                    <div class="d-flex items-center">
                      <div class="d-flex x-gap-5 items-center mr-10">
                        <div class="icon-star text-9 text-yellow-1"></div>
                        <div class="icon-star text-9 text-yellow-1"></div>
                        <div class="icon-star text-9 text-yellow-1"></div>
                        <div class="icon-star text-9 text-yellow-1"></div>
                        <div class="icon-star text-9 text-yellow-1"></div>
                      </div>
                      <div class="text-12 text-light-1">(0)</div>
                    </div>
                  </div>

                  <!-- Course Title -->
                  <div class="text-17 lh-15 fw-500 text-dark-1 mb-15">
                    @if($courseAcceuil->product->current_variation)
                      {{ $courseAcceuil->product->current_variation->name }}
                    @else
                      {{ $courseAcceuil->product->titre }}
                    @endif
                  </div>

                  <!-- Course Description -->
                  <div class="text-14 lh-15 text-light-1 mb-20">
                    {{ $courseAcceuil->product->description ?? '' }}
                  </div>

                  <!-- Course Stats -->
                  <div class="d-flex items-center justify-between">
                    <div class="d-flex items-center">
                      <div class="text-12 text-light-1">{{ $courseAcceuil->product->lessons_count ?? '0' }} {{ __('messages.lessons') }}</div>
                    </div>

                    <div class="coursesCard-footer">
                      <div class="">
                        @if($courseAcceuil->product->prix > 0)
                          <div class="text-16 fw-500 text-dark-1">@price($courseAcceuil->product->prix)</div>
                        @else
                          <div class="text-green-1">{{ __('messages.free') }}</div>
                        @endif
                      </div>
                    </div>
                  </div>
                </div>
              </a>
            </div>
          </div>
        @endforeach
      @else
        <!-- Course 1 -->
        <div class="col-lg-3 col-md-6">
          <div data-anim-child="slide-up delay-1">
            <a href="#" class="coursesCard -type-1 -hover-shadow border-light rounded-8">
              <div class="relative">
                <div class="coursesCard__image overflow-hidden rounded-top-8">
                  <img class="w-1/1" src="{{ asset('assets/img/coursesCards/1.png') }}" alt="image">
                  <div class="coursesCard__image_overlay rounded-top-8"></div>
                </div>
                <div class="d-flex justify-between py-10 px-10 absolute-full-center z-3">
                </div>
              </div>

              <div class="h-100 pt-15 pb-10 px-20">
                <!-- Instructor and Rating -->
                <div class="d-flex items-center justify-between mb-15">
                  <div class="d-flex items-center">
                    <img src="{{ asset('assets/img/general/avatar-1.png') }}" alt="instructor" class="w-30 h-30 rounded-full mr-10">
                    <div class="text-13 text-dark-1">David Warner</div>
                  </div>
                  <div class="d-flex items-center">
                    <div class="d-flex x-gap-5 items-center mr-10">
                      <div class="icon-star text-9 text-yellow-1"></div>
                      <div class="icon-star text-9 text-yellow-1"></div>
                      <div class="icon-star text-9 text-yellow-1"></div>
                      <div class="icon-star text-9 text-yellow-1"></div>
                      <div class="icon-star text-9 text-yellow-1"></div>
                    </div>
                    <div class="text-12 text-light-1">(0)</div>
                  </div>
                </div>

                <!-- Course Title -->
                <div class="text-17 lh-15 fw-500 text-dark-1 mb-15">{{ __('messages.course_1_title') }}</div>

                <!-- Course Description -->
                <div class="text-14 lh-15 text-light-1 mb-20">
                </div>

                <!-- Course Stats -->
                <div class="d-flex items-center justify-between">
                  <div class="d-flex items-center">
                    <div class="text-12 text-light-1">6 {{ __('messages.lessons') }}</div>
                  </div>

                  <div class="coursesCard-footer">
                    <div class="">
                      <div class="text-16 fw-500 text-dark-1">$188</div>
                    </div>
                  </div>
                </div>
              </div>
            </a>
          </div>
        </div>

        <!-- Course 2 -->
        <div class="col-lg-3 col-md-6">
          <div data-anim-child="slide-up delay-2">
            <a href="#" class="coursesCard -type-1 -hover-shadow border-light rounded-8">
              <div class="relative">
                <div class="coursesCard__image overflow-hidden rounded-top-8">
                  <img class="w-1/1" src="{{ asset('assets/img/coursesCards/2.png') }}" alt="image">
                  <div class="coursesCard__image_overlay rounded-top-8"></div>
                </div>
                <div class="d-flex justify-between py-10 px-10 absolute-full-center z-3">
                  <div>
                    <div class="px-15 rounded-200 bg-purple-1">
                      <span class="text-11 lh-1 uppercase fw-500 text-white">{{ __('messages.popular') }}</span>
                    </div>
                  </div>

                  <div class="d-flex x-gap-5">
                    <div>
                      <div class="px-15 rounded-200 bg-green-1">
                        <span class="text-11 lh-1 uppercase fw-500 text-dark-1">{{ __('messages.best_seller') }}</span>
                      </div>
                    </div>
                    <div>
                      <div class="px-15 rounded-200 bg-blue-1">
                        <span class="text-11 lh-1 uppercase fw-500 text-white">English Courses</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="h-100 pt-15 pb-10 px-20">
                <!-- Instructor and Rating -->
                <div class="d-flex items-center justify-between mb-15">
                  <div class="d-flex items-center">
                    <img src="{{ asset('assets/img/general/avatar-1.png') }}" alt="instructor" class="w-30 h-30 rounded-full mr-10">
                    <div class="text-13 text-dark-1">David Warner</div>
                  </div>
                  <div class="d-flex items-center">
                    <div class="d-flex x-gap-5 items-center mr-10">
                      <div class="icon-star text-9 text-yellow-1"></div>
                      <div class="icon-star text-9 text-yellow-1"></div>
                      <div class="icon-star text-9 text-yellow-1"></div>
                      <div class="icon-star text-9 text-yellow-1"></div>
                      <div class="icon-star text-9 text-yellow-1"></div>
                    </div>
                    <div class="text-12 text-light-1">(0)</div>
                  </div>
                </div>

                <!-- Course Title -->
                <div class="text-17 lh-15 fw-500 text-dark-1 mb-15">{{ __('messages.course_3_title') }}</div>

                <!-- Course Description -->
                <div class="text-14 lh-15 text-light-1 mb-20">
                </div>

                <!-- Course Stats -->
                <div class="d-flex items-center justify-between">
                  <div class="d-flex items-center">
                    <div class="text-12 text-light-1">7 {{ __('messages.lessons') }}</div>
                  </div>

                  <div class="coursesCard-footer">
                    <div class="">
                      <div class="text-16 fw-500 text-dark-1">$140</div>
                    </div>
                  </div>
                </div>
              </div>
            </a>
          </div>
        </div>

        <!-- Course 3 -->
        <div class="col-lg-3 col-md-6">
          <div data-anim-child="slide-up delay-3">
            <a href="#" class="coursesCard -type-1 -hover-shadow border-light rounded-8">
              <div class="relative">
                <div class="coursesCard__image overflow-hidden rounded-top-8">
                  <img class="w-1/1" src="{{ asset('assets/img/coursesCards/3.png') }}" alt="image">
                  <div class="coursesCard__image_overlay rounded-top-8"></div>
                </div>
                <div class="d-flex justify-between py-10 px-10 absolute-full-center z-3">
                </div>
              </div>

              <div class="h-100 pt-15 pb-10 px-20">
                <div class="d-flex items-center">
                  <div class="text-14 lh-1 text-yellow-1 mr-10">4.5</div>
                  <div class="d-flex x-gap-5 items-center">
                    <div class="icon-star text-9 text-yellow-1"></div>
                    <div class="icon-star text-9 text-yellow-1"></div>
                    <div class="icon-star text-9 text-yellow-1"></div>
                    <div class="icon-star text-9 text-yellow-1"></div>
                    <div class="icon-star text-9 text-yellow-1"></div>
                  </div>
                  <div class="text-13 lh-1 ml-10">(1991)</div>
                </div>

                <div class="text-17 lh-15 fw-500 text-dark-1 mt-10">{{ __('messages.course_3_title') }}</div>

                <div class="d-flex x-gap-10 items-center pt-10">
                  <div class="d-flex items-center">
                    <div class="mr-8">
                      <img src="{{ asset('assets/img/coursesCards/icons/1.svg') }}" alt="icon">
                    </div>
                    <div class="text-14 lh-1">6 {{ __('messages.lessons') }}</div>
                  </div>

                  <div class="d-flex items-center">
                    <div class="mr-8">
                      <img src="{{ asset('assets/img/coursesCards/icons/2.svg') }}" alt="icon">
                    </div>
                    <div class="text-14 lh-1">3h 56m</div>
                  </div>

                  <div class="d-flex items-center">
                    <div class="mr-8">
                      <img src="{{ asset('assets/img/coursesCards/icons/3.svg') }}" alt="icon">
                    </div>
                    <div class="text-14 lh-1">{{ __('messages.beginner') }}</div>
                  </div>
                </div>

                <div class="coursesCard-footer">
                  <div class="coursesCard-footer__price">
                    @if($courseAcceuil->product->prix > 0)
                      <div>@price($courseAcceuil->product->prix)</div>
                    @else
                      <div class="text-green-1">{{ __('messages.free') }}</div>
                    @endif
                  </div>
                </div>
              </div>
            </a>
          </div>
        </div>

        <!-- Course 4 -->
        <div class="col-lg-3 col-md-6">
          <div data-anim-child="slide-up delay-4">
            <a href="#" class="coursesCard -type-1 -hover-shadow border-light rounded-8">
              <div class="relative">
                <div class="coursesCard__image overflow-hidden rounded-top-8">
                  <img class="w-1/1" src="{{ asset('assets/img/coursesCards/4.png') }}" alt="image">
                  <div class="coursesCard__image_overlay rounded-top-8"></div>
                </div>
                <div class="d-flex justify-between py-10 px-10 absolute-full-center z-3">
                </div>
              </div>

              <div class="h-100 pt-15 pb-10 px-20">
                <div class="d-flex items-center">
                  <div class="text-14 lh-1 text-yellow-1 mr-10">4.5</div>
                  <div class="d-flex x-gap-5 items-center">
                    <div class="icon-star text-9 text-yellow-1"></div>
                    <div class="icon-star text-9 text-yellow-1"></div>
                    <div class="icon-star text-9 text-yellow-1"></div>
                    <div class="icon-star text-9 text-yellow-1"></div>
                    <div class="icon-star text-9 text-yellow-1"></div>
                  </div>
                  <div class="text-13 lh-1 ml-10">(1991)</div>
                </div>

                <div class="text-17 lh-15 fw-500 text-dark-1 mt-10">{{ __('messages.course_4_title') }}</div>

                <div class="d-flex x-gap-10 items-center pt-10">
                  <div class="d-flex items-center">
                    <div class="mr-8">
                      <img src="{{ asset('assets/img/coursesCards/icons/1.svg') }}" alt="icon">
                    </div>
                    <div class="text-14 lh-1">6 {{ __('messages.lessons') }}</div>
                  </div>

                  <div class="d-flex items-center">
                    <div class="mr-8">
                      <img src="{{ asset('assets/img/coursesCards/icons/2.svg') }}" alt="icon">
                    </div>
                    <div class="text-14 lh-1">3h 56m</div>
                  </div>

                  <div class="d-flex items-center">
                    <div class="mr-8">
                      <img src="{{ asset('assets/img/coursesCards/icons/3.svg') }}" alt="icon">
                    </div>
                    <div class="text-14 lh-1">{{ __('messages.beginner') }}</div>
                  </div>
                </div>

                <div class="coursesCard-footer">
                  <div class="coursesCard-footer__price">
                    @if($courseAcceuil->product->prix > 0)
                      <div>@price($courseAcceuil->product->prix)</div>
                    @else
                      <div class="text-green-1">{{ __('messages.free') }}</div>
                    @endif
                  </div>
                </div>
              </div>
            </a>
          </div>
        </div>

        <!-- Course 5 -->
        <div class="col-lg-3 col-md-6">
          <div data-anim-child="slide-up delay-5">
            <a href="#" class="coursesCard -type-1 -hover-shadow border-light rounded-8">
              <div class="relative">
                <div class="coursesCard__image overflow-hidden rounded-top-8">
                  <img class="w-1/1" src="{{ asset('assets/img/coursesCards/5.png') }}" alt="image">
                  <div class="coursesCard__image_overlay rounded-top-8"></div>
                </div>
                <div class="d-flex justify-between py-10 px-10 absolute-full-center z-3">
                </div>
              </div>

              <div class="h-100 pt-15 pb-10 px-20">
                <div class="d-flex items-center">
                  <div class="text-14 lh-1 text-yellow-1 mr-10">4.5</div>
                  <div class="d-flex x-gap-5 items-center">
                    <div class="icon-star text-9 text-yellow-1"></div>
                    <div class="icon-star text-9 text-yellow-1"></div>
                    <div class="icon-star text-9 text-yellow-1"></div>
                    <div class="icon-star text-9 text-yellow-1"></div>
                    <div class="icon-star text-9 text-yellow-1"></div>
                  </div>
                  <div class="text-13 lh-1 ml-10">(1991)</div>
                </div>

                <div class="text-17 lh-15 fw-500 text-dark-1 mt-10">{{ __('messages.course_5_title') }}</div>

                <div class="d-flex x-gap-10 items-center pt-10">
                  <div class="d-flex items-center">
                    <div class="mr-8">
                      <img src="{{ asset('assets/img/coursesCards/icons/1.svg') }}" alt="icon">
                    </div>
                    <div class="text-14 lh-1">6 {{ __('messages.lessons') }}</div>
                  </div>

                  <div class="d-flex items-center">
                    <div class="mr-8">
                      <img src="{{ asset('assets/img/coursesCards/icons/2.svg') }}" alt="icon">
                    </div>
                    <div class="text-14 lh-1">3h 56m</div>
                  </div>

                  <div class="d-flex items-center">
                    <div class="mr-8">
                      <img src="{{ asset('assets/img/coursesCards/icons/3.svg') }}" alt="icon">
                    </div>
                    <div class="text-14 lh-1">{{ __('messages.beginner') }}</div>
                  </div>
                </div>

                <div class="coursesCard-footer">
                  <div class="coursesCard-footer__price">
                    @if($courseAcceuil->product->prix > 0)
                      <div>@price($courseAcceuil->product->prix)</div>
                    @else
                      <div class="text-green-1">{{ __('messages.free') }}</div>
                    @endif
                  </div>
                </div>
              </div>
            </a>
          </div>
        </div>

        <!-- Course 6 -->
        <div class="col-lg-3 col-md-6">
          <div data-anim-child="slide-up delay-6">
            <a href="#" class="coursesCard -type-1 -hover-shadow border-light rounded-8">
              <div class="relative">
                <div class="coursesCard__image overflow-hidden rounded-top-8">
                  <img class="w-1/1" src="{{ asset('assets/img/coursesCards/6.png') }}" alt="image">
                  <div class="coursesCard__image_overlay rounded-top-8"></div>
                </div>
                <div class="d-flex justify-between py-10 px-10 absolute-full-center z-3">
                </div>
              </div>

              <div class="h-100 pt-15 pb-10 px-20">
                <div class="d-flex items-center">
                  <div class="text-14 lh-1 text-yellow-1 mr-10">4.5</div>
                  <div class="d-flex x-gap-5 items-center">
                    <div class="icon-star text-9 text-yellow-1"></div>
                    <div class="icon-star text-9 text-yellow-1"></div>
                    <div class="icon-star text-9 text-yellow-1"></div>
                    <div class="icon-star text-9 text-yellow-1"></div>
                    <div class="icon-star text-9 text-yellow-1"></div>
                  </div>
                  <div class="text-13 lh-1 ml-10">(1991)</div>
                </div>

                <div class="text-17 lh-15 fw-500 text-dark-1 mt-10">{{ __('messages.course_6_title') }}</div>

                <div class="d-flex x-gap-10 items-center pt-10">
                  <div class="d-flex items-center">
                    <div class="mr-8">
                      <img src="{{ asset('assets/img/coursesCards/icons/1.svg') }}" alt="icon">
                    </div>
                    <div class="text-14 lh-1">6 {{ __('messages.lessons') }}</div>
                  </div>

                  <div class="d-flex items-center">
                    <div class="mr-8">
                      <img src="{{ asset('assets/img/coursesCards/icons/2.svg') }}" alt="icon">
                    </div>
                    <div class="text-14 lh-1">3h 56m</div>
                  </div>

                  <div class="d-flex items-center">
                    <div class="mr-8">
                      <img src="{{ asset('assets/img/coursesCards/icons/3.svg') }}" alt="icon">
                    </div>
                    <div class="text-14 lh-1">{{ __('messages.beginner') }}</div>
                  </div>
                </div>

                <div class="coursesCard-footer">
                  <div class="coursesCard-footer__price">
                    @if($courseAcceuil->product->prix > 0)
                      <div>@price($courseAcceuil->product->prix)</div>
                    @else
                      <div class="text-green-1">{{ __('messages.free') }}</div>
                    @endif
                  </div>
                </div>
              </div>
            </a>
          </div>
        </div>

        <!-- Course 7 -->
        <div class="col-lg-3 col-md-6">
          <div data-anim-child="slide-up delay-7">
            <a href="#" class="coursesCard -type-1 -hover-shadow border-light rounded-8">
              <div class="relative">
                <div class="coursesCard__image overflow-hidden rounded-top-8">
                  <img class="w-1/1" src="{{ asset('assets/img/coursesCards/7.png') }}" alt="image">
                  <div class="coursesCard__image_overlay rounded-top-8"></div>
                </div>
                <div class="d-flex justify-between py-10 px-10 absolute-full-center z-3">
                </div>
              </div>

              <div class="h-100 pt-15 pb-10 px-20">
                <div class="d-flex items-center">
                  <div class="text-14 lh-1 text-yellow-1 mr-10">4.5</div>
                  <div class="d-flex x-gap-5 items-center">
                    <div class="icon-star text-9 text-yellow-1"></div>
                    <div class="icon-star text-9 text-yellow-1"></div>
                    <div class="icon-star text-9 text-yellow-1"></div>
                    <div class="icon-star text-9 text-yellow-1"></div>
                    <div class="icon-star text-9 text-yellow-1"></div>
                  </div>
                  <div class="text-13 lh-1 ml-10">(1991)</div>
                </div>

                <div class="text-17 lh-15 fw-500 text-dark-1 mt-10">{{ __('messages.course_7_title') }}</div>

                <div class="d-flex x-gap-10 items-center pt-10">
                  <div class="d-flex items-center">
                    <div class="mr-8">
                      <img src="{{ asset('assets/img/coursesCards/icons/1.svg') }}" alt="icon">
                    </div>
                    <div class="text-14 lh-1">6 {{ __('messages.lessons') }}</div>
                  </div>

                  <div class="d-flex items-center">
                    <div class="mr-8">
                      <img src="{{ asset('assets/img/coursesCards/icons/2.svg') }}" alt="icon">
                    </div>
                    <div class="text-14 lh-1">3h 56m</div>
                  </div>

                  <div class="d-flex items-center">
                    <div class="mr-8">
                      <img src="{{ asset('assets/img/coursesCards/icons/3.svg') }}" alt="icon">
                    </div>
                    <div class="text-14 lh-1">{{ __('messages.beginner') }}</div>
                  </div>
                </div>

                <div class="coursesCard-footer">
                  <div class="coursesCard-footer__price">
                    @if($courseAcceuil->product->prix > 0)
                      <div>@price($courseAcceuil->product->prix)</div>
                    @else
                      <div class="text-green-1">{{ __('messages.free') }}</div>
                    @endif
                  </div>
                </div>
              </div>
            </a>
          </div>
        </div>

        <!-- Course 8 -->
        <div class="col-lg-3 col-md-6">
          <div data-anim-child="slide-up delay-8">
            <a href="#" class="coursesCard -type-1 -hover-shadow border-light rounded-8">
              <div class="relative">
                <div class="coursesCard__image overflow-hidden rounded-top-8">
                  <img class="w-1/1" src="{{ asset('assets/img/coursesCards/8.png') }}" alt="image">
                  <div class="coursesCard__image_overlay rounded-top-8"></div>
                </div>
                <div class="d-flex justify-between py-10 px-10 absolute-full-center z-3">
                </div>
              </div>

              <div class="h-100 pt-15 pb-10 px-20">
                <div class="d-flex items-center">
                  <div class="text-14 lh-1 text-yellow-1 mr-10">4.5</div>
                  <div class="d-flex x-gap-5 items-center">
                    <div class="icon-star text-9 text-yellow-1"></div>
                    <div class="icon-star text-9 text-yellow-1"></div>
                    <div class="icon-star text-9 text-yellow-1"></div>
                    <div class="icon-star text-9 text-yellow-1"></div>
                    <div class="icon-star text-9 text-yellow-1"></div>
                  </div>
                  <div class="text-13 lh-1 ml-10">(1991)</div>
                </div>

                <div class="text-17 lh-15 fw-500 text-dark-1 mt-10">{{ __('messages.course_8_title') }}</div>

                <div class="d-flex x-gap-10 items-center pt-10">
                  <div class="d-flex items-center">
                    <div class="mr-8">
                      <img src="{{ asset('assets/img/coursesCards/icons/1.svg') }}" alt="icon">
                    </div>
                    <div class="text-14 lh-1">6 {{ __('messages.lessons') }}</div>
                  </div>

                  <div class="d-flex items-center">
                    <div class="mr-8">
                      <img src="{{ asset('assets/img/coursesCards/icons/2.svg') }}" alt="icon">
                    </div>
                    <div class="text-14 lh-1">3h 56m</div>
                  </div>

                  <div class="d-flex items-center">
                    <div class="mr-8">
                      <img src="{{ asset('assets/img/coursesCards/icons/3.svg') }}" alt="icon">
                    </div>
                    <div class="text-14 lh-1">{{ __('messages.beginner') }}</div>
                  </div>
                </div>

                <div class="coursesCard-footer">
                  <div class="coursesCard-footer__price">
                    @if($courseAcceuil->product->prix > 0)
                      <div>@price($courseAcceuil->product->prix)</div>
                    @else
                      <div class="text-green-1">{{ __('messages.free') }}</div>
                    @endif
                  </div>
                </div>
              </div>
            </a>
          </div>
        </div>
      @endif
    </div>

    <!-- View All Courses Button -->
    <div class="row justify-center pt-60 lg:pt-40">
      <div class="col-auto">
        <a href="{{ route('filter.index') }}" class="button -md -outline-purple-1 text-purple-1">
          {{ __('messages.view_all_courses') }}
        </a>
      </div>
    </div>
  </div>
</section>

