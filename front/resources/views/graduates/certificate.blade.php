<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
  @include('layouts.head')
  <title>{{ __('messages.certificate') }} {{ $certificate->serialnumber }} - Upskills Academy</title>

  <style>
    .certificate-detail {
      margin-top: 100px;
      margin-bottom: 80px;
    }

    .certificate-header {
      text-align: center;
      margin-bottom: 50px;
    }

    .certificate-header__serial {
      font-size: 14px;
      color: #9ca3af;
      font-family: monospace;
      margin-bottom: 10px;
    }

    .certificate-header__title {
      font-size: 52.5px;
      font-weight: 700;
      color: #1f2937;
      margin-bottom: 15px;
    }

    .certificate-header__course {
      font-size: 24px;
      color: #4b5563;
      font-weight: 500;
    }

    .certificate-content {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 40px;
      margin-top: 50px;
    }

    .certificate-section {
      background: #ffffff;
      border-radius: 16px;
      padding: 30px;
      border: 2px solid #e5e7eb;
    }

    .certificate-section__title {
      font-size: 20px;
      font-weight: 700;
      color: #1f2937;
      margin-bottom: 20px;
      padding-bottom: 15px;
      border-bottom: 3px solid #ffcb05;
    }

    .certificate-info-item {
      display: flex;
      justify-content: space-between;
      padding: 15px 0;
      border-bottom: 1px solid #f3f4f6;
    }

    .certificate-info-item:last-child {
      border-bottom: none;
    }

    .certificate-info-item__label {
      font-size: 15px;
      color: #6b7280;
      font-weight: 500;
    }

    .certificate-info-item__value {
      font-size: 15px;
      color: #1f2937;
      font-weight: 600;
      text-align: right;
    }

    .certificate-badge {
      display: inline-block;
      padding: 6px 16px;
      border-radius: 20px;
      font-size: 13px;
      font-weight: 600;
    }

    .certificate-badge--success {
      background: #d1fae5;
      color: #065f46;
    }

    .certificate-badge--info {
      background: #dbeafe;
      color: #1e40af;
    }

    .certificate-badge--warning {
      background: #fff9e6;
      color: #f59e0b;
    }

    .back-button {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      color: #7c3aed;
      font-weight: 500;
      text-decoration: none;
      transition: all 0.3s ease;
      margin-bottom: 30px;
    }

    .back-button:hover {
      color: #6d28d9;
      transform: translateX(-5px);
    }

    .certificate-image-section {
      grid-column: 1 / -1;
      text-align: center;
    }

    .certificate-image {
      max-width: 100%;
      height: auto;
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    @media (max-width: 768px) {
      .certificate-detail {
        margin-top: 80px;
      }

      .certificate-header__title {
        font-size: 35px;
      }

      .certificate-header__course {
        font-size: 18px;
      }

      .certificate-content {
        grid-template-columns: 1fr;
        gap: 25px;
      }

      .certificate-section {
        padding: 20px;
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

      <section class="certificate-detail">
        <div class="container">

          <a href="{{ url()->previous() }}" class="back-button">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            {{ __('messages.back') }}
          </a>

          <!-- Certificate Header -->
          <div class="certificate-header">
            <div class="certificate-header__serial">{{ __('messages.serial') }}: {{ $certificate->serialnumber }}</div>
            <h1 class="certificate-header__title">{{ $certificate->student_name }}</h1>
            <div class="certificate-header__course">{{ $certificate->course_name }}</div>
          </div>

          <!-- Certificate Details -->
          <div class="certificate-content">

            <!-- Student Information -->
            <div class="certificate-section">
              <h2 class="certificate-section__title">{{ __('messages.student_information') }}</h2>

              <div class="certificate-info-item">
                <span class="certificate-info-item__label">{{ __('messages.full_name') }}</span>
                <span class="certificate-info-item__value">{{ $certificate->student_name }}</span>
              </div>

              @if($certificate->nationality != 'N/A')
                <div class="certificate-info-item">
                  <span class="certificate-info-item__label">{{ __('messages.nationality') }}</span>
                  <span class="certificate-info-item__value">{{ $certificate->nationality }}</span>
                </div>
              @endif

              @if($certificate->email != 'N/A')
                <div class="certificate-info-item">
                  <span class="certificate-info-item__label">{{ __('messages.email') }}</span>
                  <span class="certificate-info-item__value">{{ $certificate->email }}</span>
                </div>
              @endif

              @if($certificate->gender != 'N/A')
                <div class="certificate-info-item">
                  <span class="certificate-info-item__label">{{ __('messages.gender') }}</span>
                  <span class="certificate-info-item__value">{{ ucfirst(strtolower($certificate->gender)) }}</span>
                </div>
              @endif

              @if($certificate->date_of_birth != 'N/A')
                <div class="certificate-info-item">
                  <span class="certificate-info-item__label">{{ __('messages.date_of_birth') }}</span>
                  <span class="certificate-info-item__value">{{ \Carbon\Carbon::parse($certificate->date_of_birth)->locale(app()->getLocale())->translatedFormat('d M Y') }}</span>
                </div>
              @endif
            </div>

            <!-- Course Information -->
            <div class="certificate-section">
              <h2 class="certificate-section__title">{{ __('messages.course_information') }}</h2>

              <div class="certificate-info-item">
                <span class="certificate-info-item__label">{{ __('messages.course_name') }}</span>
                <span class="certificate-info-item__value">{{ $certificate->course_name }}</span>
              </div>

              <div class="certificate-info-item">
                <span class="certificate-info-item__label">{{ __('messages.instructor') }}</span>
                <span class="certificate-info-item__value">{{ $certificate->teacher_name }}</span>
              </div>

              @if($certificate->course_period != 'N/A')
                <div class="certificate-info-item">
                  <span class="certificate-info-item__label">{{ __('messages.duration') }}</span>
                  <span class="certificate-info-item__value">{{ $certificate->course_period }}</span>
                </div>
              @endif

              @if($certificate->course_language != 'N/A')
                <div class="certificate-info-item">
                  <span class="certificate-info-item__label">{{ __('messages.language') }}</span>
                  <span class="certificate-info-item__value">{{ ucfirst($certificate->course_language) }}</span>
                </div>
              @endif

              <div class="certificate-info-item">
                <span class="certificate-info-item__label">{{ __('messages.certificate_date') }}</span>
                <span class="certificate-info-item__value">
                  <span class="certificate-badge certificate-badge--success">
                    {{ \Carbon\Carbon::parse($certificate->date)->locale(app()->getLocale())->translatedFormat('d M Y') }}
                  </span>
                </span>
              </div>
            </div>

            <!-- Certificate Image -->
            @if($certificate->image)
              <div class="certificate-image-section">
                <div class="certificate-section">
                  <h2 class="certificate-section__title">{{ __('messages.certificate_image') }}</h2>
                  <div class="mt-20">
                    @php
                      // Check if image is already a full URL (new site certificates)
                      if (filter_var($certificate->image, FILTER_VALIDATE_URL)) {
                        // Image is already a full URL from new site
                        $imageUrls = [$certificate->image];
                        $imageUrl = $certificate->image;
                      } else {
                        // Try multiple possible image paths for old site certificates
                        $imageUrls = [
                          'https://old.upskillsacademy.com/uploads/kcfinder/upload/image/' . $certificate->image,
                          'https://www.upskillsacademy.com/uploads/kcfinder/upload/image/' . $certificate->image,
                          'https://old.upskillsacademy.com/storage/certificates/' . $certificate->image,
                          'https://www.upskillsacademy.com/storage/certificates/' . $certificate->image,
                          $certificate->image, // If it's already a full URL
                        ];
                        $imageUrl = $imageUrls[0]; // Default to first option
                      }
                    @endphp
                    <img src="{{ $imageUrl }}"
                         alt="{{ $certificate->student_name }}"
                         class="certificate-image"
                         onerror="
                           const urls = {!! json_encode($imageUrls) !!};
                           const currentSrc = this.src;
                           const currentIndex = urls.indexOf(currentSrc);
                           if (currentIndex < urls.length - 1) {
                             this.src = urls[currentIndex + 1];
                           } else {
                             this.src = '{{ asset('assets/img/general/certificate-placeholder.png') }}';
                           }
                         ">
                  </div>
                </div>
              </div>
            @endif

          </div>

        </div>
      </section>

    </div>
  </main>

  @include('layouts.footer')
</body>
</html>

