<style>
  .learn-skills-section {
    background-color: #ffffff;
  }

  .about-content__title {
    font-size: 48px;
    font-weight: 700;
    line-height: 1.3;
    color: #1f2937;
    margin-bottom: 20px;
  }

  .about-content__title span {
    color: #297a7b;
    position: relative;
  }

  .about-content__text {
    font-size: 18px;
    line-height: 1.7;
    color: #6b7280;
    margin-bottom: 30px;
  }

  .about-content-list__item {
    margin-bottom: 20px;
  }

  .about-content-list__icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #093162 !important;
    border-radius: 50%;
    margin-right: 15px;
    flex-shrink: 0;
  }

  .about-content-list__icon i,
  .about-content-list__icon svg {
    color: white !important;
    stroke: white !important;
    width: 20px;
    height: 20px;
  }

  .about-content-list__title {
    font-size: 16px;
    font-weight: 500;
    color: #1f2937;
    line-height: 1.6;
  }

  .about-content-list__title strong {
    font-size: 17px;
    font-weight: 600;
    display: block;
    margin-bottom: 5px;
    line-height: 1.4;
  }

  .about-content-list__title small {
    font-size: 14px;
    line-height: 1.6;
    display: block;
  }

  .about-image {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    height: 100%;
    min-height: 500px;
  }

  .about-image img {
    width: 100%;
    height: 100%;
    min-height: 500px;
    object-fit: cover;
    border-radius: 12px;
    box-shadow: 0 20px 40px rgba(8, 23, 54, 0.12);
  }

  @media (max-width: 991px) {
    .about-content__title {
      font-size: 36px;
    }

    .about-content__text {
      font-size: 16px;
    }
  }

  @media (max-width: 576px) {
    .about-content__title {
      font-size: 28px;
    }

    .about-image {
      margin-bottom: 30px;
      min-height: 300px;
    }

    .about-image img {
      min-height: 300px;
    }

    .about-content-list__title strong {
      font-size: 16px;
    }

    .about-content-list__title small {
      font-size: 13px;
    }
  }
</style>

<section class="learn-skills-section layout-pt-lg layout-pb-lg">
  <div data-anim-wrap class="container">
    <div class="row y-gap-30 justify-between items-center">
      <div class="col-xl-5 col-lg-6 col-md-10 order-2 order-lg-1">
        <div class="about-content">
          <h2 data-anim-child="slide-up delay-1" class="about-content__title">
            {{ __('messages.why_choose_sast') }}
          </h2>
          <p data-anim-child="slide-up delay-2" class="about-content__text">
            {{ __('messages.why_choose_sast_subtitle') }}
          </p>
          <div data-anim-child="slide-up delay-3" class="pt-30">

            <div class="d-flex items-center about-content-list__item">
              <div class="about-content-list__icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                  <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
              </div>
              <div class="about-content-list__title">
                <strong>{{ __('messages.sast_feature_1_title') }}</strong>
                <small>{{ __('messages.sast_feature_1_desc') }}</small>
              </div>
            </div>

            <div class="d-flex items-center about-content-list__item">
              <div class="about-content-list__icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                  <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
              </div>
              <div class="about-content-list__title">
                <strong>{{ __('messages.sast_feature_2_title') }}</strong>
                <small>{{ __('messages.sast_feature_2_desc') }}</small>
              </div>
            </div>

            <div class="d-flex items-center about-content-list__item">
              <div class="about-content-list__icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                  <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
              </div>
              <div class="about-content-list__title">
                <strong>{{ __('messages.sast_feature_3_title') }}</strong>
                <small>{{ __('messages.sast_feature_3_desc') }}</small>
              </div>
            </div>

            <div class="d-flex items-center about-content-list__item">
              <div class="about-content-list__icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                  <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
              </div>
              <div class="about-content-list__title">
                <strong>{{ __('messages.sast_feature_4_title') }}</strong>
                <small>{{ __('messages.sast_feature_4_desc') }}</small>
              </div>
            </div>

          </div>

          <div data-anim-child="slide-up delay-4" class="d-inline-block mt-30">
            <a href="{{ route('signup') }}" class="button -md text-white" style="background-color: #093162 !important; border-color: #093162 !important;">{{ __('messages.join_free') }}</a>
          </div>
        </div>
      </div>

      <div class="col-xl-5 col-lg-6 order-1 order-lg-2">
        <div data-anim-child="slide-up delay-5" class="about-image">
          <img src="{{ asset('images/learning-illustration.svg') }}" alt="Learn English with Upskills Academy">
        </div>
      </div>
    </div>
  </div>
</section>

