<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
  @include('layouts.head')
  <title>{{ __('messages.graduates') }} {{ $year }} - Upskills Academy</title>

  <style>
    .year-header {
      background: transparent;
      padding: 40px 0;
      margin-top: 100px;
      margin-bottom: 40px;
    }

    .year-header__title {
      font-size: 48px;
      font-weight: 700;
      color: #000000;
      text-align: center;
      margin-bottom: 10px;
    }

    .year-header__stats {
      display: flex;
      justify-content: center;
      gap: 40px;
      margin-top: 30px;
      flex-wrap: wrap;
    }

    .year-stat {
      text-align: center;
    }

    .year-stat__number {
      font-size: 36px;
      font-weight: 700;
      color: #0071ae;
    }

    .year-stat__label {
      font-size: 14px;
      color: #6b7280;
      font-weight: 500;
      margin-top: 5px;
    }

    .course-section {
      margin-bottom: 60px;
    }

    .course-header {
      background: #0071ae;
      padding: 25px 30px;
      border-radius: 12px;
      margin-bottom: 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 15px;
      cursor: pointer;
      transition: all 0.3s ease;
      position: relative;
    }

    .course-header:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(0, 113, 174, 0.3);
    }

    .course-header__content {
      display: flex;
      justify-content: space-between;
      align-items: center;
      width: 100%;
      gap: 15px;
    }

    .course-header__title {
      font-size: 24px;
      font-weight: 700;
      color: #ffffff;
      margin: 0;
      flex: 1;
      word-wrap: break-word;
      overflow-wrap: break-word;
      white-space: normal;
      line-height: 1.4;
    }

    .course-header__right {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .course-header__count {
      background: rgba(255, 255, 255, 0.2);
      color: #ffffff;
      padding: 8px 20px;
      border-radius: 50px;
      font-size: 16px;
      font-weight: 600;
      backdrop-filter: blur(10px);
    }

    .course-header__toggle {
      width: 32px;
      height: 32px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: rgba(255, 255, 255, 0.2);
      border-radius: 50%;
      transition: all 0.3s ease;
    }

    .course-header__toggle svg {
      width: 20px;
      height: 20px;
      stroke: #ffffff;
      transition: transform 0.3s ease;
    }

    .course-header__toggle svg {
      transform: rotate(-90deg);
    }

    .course-header.open .course-header__toggle svg {
      transform: rotate(0deg);
    }

    .course-certificates {
      overflow: hidden;
      transition: max-height 0.5s ease, opacity 0.3s ease, margin 0.3s ease;
      max-height: 0;
      opacity: 0;
      margin-bottom: -30px;
    }

    .course-certificates.open {
      max-height: 5000px;
      opacity: 1;
      margin-bottom: 0;
    }

    .certificate-card {
      background: #ffffff;
      border-radius: 12px;
      padding: 25px;
      border: 2px solid #e5e7eb;
      transition: all 0.3s ease;
      height: 100%;
      display: flex;
      flex-direction: column;
    }

    .certificate-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 30px rgba(0, 113, 174, 0.15);
      border-color: #0071ae;
    }

    .certificate-card__header {
      margin-bottom: 15px;
      padding-bottom: 15px;
      border-bottom: 2px solid #f3f4f6;
    }

    .certificate-card__student {
      font-size: 20px;
      font-weight: 700;
      color: #1f2937;
      margin-bottom: 5px;
    }

    .certificate-card__serial {
      font-size: 13px;
      color: #9ca3af;
      font-family: monospace;
    }

    .certificate-card__info {
      display: flex;
      flex-direction: column;
      gap: 8px;
      margin-top: auto;
      padding-top: 15px;
    }

    .certificate-card__info-item {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 14px;
      color: #6b7280;
    }

    .certificate-card__info-icon {
      width: 16px;
      height: 16px;
      color: #0071ae;
    }

    .certificate-card__date {
      background: #e6f3fb;
      color: #0071ae;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 13px;
      font-weight: 600;
      display: inline-block;
      margin-top: 10px;
    }

    .back-button {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      color: #0071ae;
      font-weight: 500;
      text-decoration: none;
      transition: all 0.3s ease;
      margin-bottom: 30px;
    }

    .back-button:hover {
      color: #005a8c;
      transform: translateX(-5px);
    }

    @media (max-width: 768px) {
      .year-header {
        margin-top: 80px;
        padding: 30px 0;
      }

      .year-header__title {
        font-size: 32px;
      }

      .year-header__stats {
        gap: 20px;
      }

      .year-stat__number {
        font-size: 28px;
      }

      .course-header {
        padding: 20px;
      }

      .course-header__title {
        font-size: 20px;
      }

      .certificate-card__student {
        font-size: 18px;
      }
    }
  </style>
</head>

<body class="preloader-visible" data-barba="wrapper">
  <!-- preloader start -->
  <div class="preloader js-preloader">
    <div class="preloader__bg"></div>
  </div>
  <!-- preloader end -->

  <main class="main-content">
    @include('layouts.header')

    <div class="content-wrapper js-content-wrapper" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

      <!-- Header Section -->
      <section class="year-header">
        <div class="container">
          <a href="{{ route('graduates.index', ['locale' => app()->getLocale()]) }}" class="back-button">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            {{ __('messages.back_to_graduates') }}
          </a>

          <h1 class="year-header__title">{{ __('messages.graduates') }} {{ $year }}</h1>

          <div class="year-header__stats">
            <div class="year-stat">
              <div class="year-stat__number">{{ $stats->total_courses }}</div>
              <div class="year-stat__label">{{ __('messages.total_courses') }}</div>
            </div>
            <div class="year-stat">
              <div class="year-stat__number">{{ $stats->total_students }}</div>
              <div class="year-stat__label">{{ __('messages.total_graduates') }}</div>
            </div>
            <div class="year-stat">
              <div class="year-stat__number">{{ $stats->total_certificates }}</div>
              <div class="year-stat__label">{{ __('messages.total_certificates') }}</div>
            </div>
          </div>
        </div>
      </section>

      <!-- Courses and Certificates Section -->
      <section class="layout-pb-lg">
        <div class="container">

          @if($certificatesByCourse->count() > 0)
            @foreach($certificatesByCourse as $index => $courseData)
              <div class="course-section">

                <!-- Course Header -->
                <div class="course-header" data-course-index="{{ $index }}" onclick="toggleCourse(this)">
                  <div class="course-header__content">
                    <h2 class="course-header__title">{{ $courseData->course_name }}</h2>
                    <div class="course-header__right">
                      <div class="course-header__count">
                        {{ $courseData->total_students }}
                        @if($courseData->total_students > 1)
                          {{ __('messages.students') }}
                        @else
                          {{ __('messages.student') }}
                        @endif
                      </div>
                      <div class="course-header__toggle">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Certificates Grid -->
                <div class="course-certificates" id="course-{{ $index }}">
                  <div class="row y-gap-30">
                  @foreach($courseData->certificates as $certificate)
                    <div class="col-lg-4 col-md-6">
                      <a href="{{ route('graduates.certificate', ['locale' => app()->getLocale(), 'serialnumber' => $certificate->serialnumber]) }}" class="text-decoration-none">
                        <div class="certificate-card" style="cursor: pointer;">
                          <div class="certificate-card__header">
                            <div class="certificate-card__student">{{ $certificate->student_name }}</div>
                            <div class="certificate-card__serial">{{ __('messages.serial') }}: {{ $certificate->serialnumber }}</div>
                          </div>

                          <div class="certificate-card__info">
                            <div class="certificate-card__info-item">
                              <svg class="certificate-card__info-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="8.5" cy="7" r="4"></circle>
                                <polyline points="17 11 19 13 23 9"></polyline>
                              </svg>
                              <span>{{ $certificate->teacher_name }}</span>
                            </div>

                            @if($certificate->nationality != 'N/A')
                              <div class="certificate-card__info-item">
                                <svg class="certificate-card__info-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                  <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                  <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                                <span>{{ $certificate->nationality }}</span>
                              </div>
                            @endif

                            <div class="certificate-card__date">
                              {{ \Carbon\Carbon::parse($certificate->date)->locale(app()->getLocale())->translatedFormat('d M Y') }}
                            </div>
                          </div>
                        </div>
                      </a>
                    </div>
                  @endforeach
                  </div>
                </div>

              </div>
            @endforeach

            <!-- Summary -->
            <div class="text-center mt-60">
              <p class="text-16 text-light-1">
                {{ __('messages.showing_certificates', ['count' => $stats->total_certificates, 'year' => $year]) }}
              </p>
            </div>
          @else
            <div class="text-center py-60">
              <h3 class="text-24 fw-500 text-dark-1">{{ __('messages.no_certificates_found') }}</h3>
              <p class="text-16 text-light-1 mt-10">{{ __('messages.no_certificates_description', ['year' => $year]) }}</p>
            </div>
          @endif

        </div>
      </section>

    </div>
  </main>

  @include('layouts.footer')

  <script>
    function toggleCourse(element) {
      const index = element.getAttribute('data-course-index');
      const courseHeader = element;
      const courseCertificates = document.getElementById('course-' + index);

      // Toggle open class
      courseHeader.classList.toggle('open');
      courseCertificates.classList.toggle('open');
    }
  </script>
</body>
</html>
