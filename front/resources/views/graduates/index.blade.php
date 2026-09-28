<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
  @include('layouts.head')
  <title>{{ __('messages.graduates') }} - Upskills Academy</title>

  <style>
    .graduate-card {
      background: #ffffff;
      border-radius: 16px;
      padding: 30px;
      border: 2px solid #e5e7eb;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      height: 100%;
      display: flex;
      flex-direction: column;
    }

    .graduate-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 5px;
      height: 100%;
      background: linear-gradient(135deg, #ffcb05 0%, #f59e0b 100%);
    }

    .graduate-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
      border-color: #ffcb05;
    }

    .graduate-card__year {
      font-size: 36px;
      font-weight: 700;
      color: #1f2937;
      margin-bottom: 15px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
    }

    .graduate-card__year-badge {
      background: linear-gradient(135deg, #ffcb05 0%, #f59e0b 100%);
      color: #1f2937;
      padding: 6px 18px;
      border-radius: 50px;
      font-size: 22px;
      font-weight: 700;
    }

    .graduate-card__stats {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 30px;
      margin-top: 30px;
    }

    .graduate-stat {
      text-align: center;
      padding: 20px;
      background: #f9fafb;
      border-radius: 12px;
      transition: all 0.3s ease;
    }

    .graduate-stat:hover {
      background: #fff9e6;
      transform: scale(1.05);
    }

    .graduate-stat__number {
      font-size: 36px;
      font-weight: 700;
      color: #0071ae;
      margin-bottom: 10px;
    }

    .graduate-stat__label {
      font-size: 18px;
      color: #6b7280;
      font-weight: 600;
    }

    .graduate-card__description {
      font-size: 16px;
      color: #4b5563;
      line-height: 1.5;
      margin-top: 15px;
      margin-bottom: 20px;
      text-align: justify;
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .graduate-card__description p {
      margin: 0;
      padding: 0 10px;
      width: 100%;
    }

    .graduates-header {
      background: transparent;
      padding: 40px 0;
      margin-top: 100px;
      margin-bottom: 60px;
      position: relative;
    }

    .graduates-header__title {
      font-size: 48px;
      font-weight: 700;
      color: #000000;
      text-align: center;
      margin-bottom: 15px;
    }

    .graduates-header__subtitle {
      font-size: 20px;
      color: #4b5563;
      text-align: center;
    }

    @media (max-width: 768px) {
      .graduates-header {
        margin-top: 80px;
        padding: 30px 0;
        margin-bottom: 40px;
      }

      .graduates-header__title {
        font-size: 32px;
      }

      .graduates-header__subtitle {
        font-size: 16px;
      }

      .graduate-card {
        padding: 25px;
      }

      .graduate-card__year {
        font-size: 32px;
        flex-direction: column;
        gap: 10px;
      }

      .graduate-card__year-badge {
        font-size: 18px;
      }

      .graduate-card__stats {
        grid-template-columns: 1fr;
        gap: 15px;
      }

      .graduate-stat__number {
        font-size: 28px;
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
      <section class="graduates-header">
        <div class="container">
          <h1 class="graduates-header__title">{{ __('messages.graduates') }}</h1>
          <p class="graduates-header__subtitle">{{ __('messages.graduates_intro') }}</p>
        </div>
      </section>

      <!-- Graduates Section -->
      <section class="layout-pb-lg">
        <div class="container">

          @if($graduatesByYear->count() > 0)
            <div class="row y-gap-30">
              @foreach($graduatesByYear as $yearData)
                <div class="col-lg-4 col-md-6">
                  <a href="{{ route('graduates.year', ['locale' => app()->getLocale(), 'year' => $yearData->year]) }}" class="text-decoration-none">
                    <div class="graduate-card" style="cursor: pointer;">
                      <div class="graduate-card__year">
                        <span class="graduate-card__year-badge">{{ $yearData->year }}</span>
                      </div>

                      <div class="graduate-card__description">
                        <p>
                          @if(app()->getLocale() == 'ar')
                            هذا العام تمكنا من عقد <strong>{{ $yearData->total_courses }}</strong> دورة في مجالات مختلفة وتخرج <strong>{{ $yearData->total_students }}</strong> طالب
                          @elseif(app()->getLocale() == 'fr')
                            Cette année, nous avons pu organiser <strong>{{ $yearData->total_courses }}</strong> cours dans divers domaines et <strong>{{ $yearData->total_students }}</strong> étudiants ont obtenu leur diplôme
                          @else
                            This year we have been able to hold <strong>{{ $yearData->total_courses }}</strong> courses in various fields and <strong>{{ $yearData->total_students }}</strong> students graduated
                          @endif
                        </p>
                      </div>

                      <div class="graduate-card__stats">
                        <div class="graduate-stat">
                          <div class="graduate-stat__number">{{ $yearData->total_courses }}</div>
                          <div class="graduate-stat__label">{{ __('messages.total_courses') }}</div>
                        </div>

                        <div class="graduate-stat">
                          <div class="graduate-stat__number">{{ $yearData->total_students }}</div>
                          <div class="graduate-stat__label">{{ __('messages.total_graduates') }}</div>
                        </div>
                      </div>
                    </div>
                  </a>
                </div>
              @endforeach
            </div>
          @else
            <div class="text-center py-60">
              <h3 class="text-24 fw-500 text-dark-1">{{ __('messages.no_graduates_found') }}</h3>
              <p class="text-16 text-light-1 mt-10">{{ __('messages.no_graduates_description') }}</p>
            </div>
          @endif

        </div>
      </section>

    </div>
  </main>

  @include('layouts.footer')
</body>
</html>

