<!-- Why Learn With Us Section -->
<section class="us-why layout-pt-lg layout-pb-lg">
  <div data-anim-wrap class="container">
    <div class="row justify-center text-center">
      <div data-anim-child="slide-up delay-1" class="col-xl-7 col-lg-9">
        <div class="sectionTitle">
          <p class="us-eyebrow us-eyebrow--on-dark">{{ __('messages.why_learn_eyebrow') }}</p>
          <h2 class="sectionTitle__title us-why__title">{{ __('messages.why_learn_title') }}</h2>
          <p class="sectionTitle__text us-why__subtitle">{{ __('messages.why_learn_subtitle') }}</p>
        </div>
      </div>
    </div>

    <div class="row y-gap-30 pt-50 justify-center">
      <div data-anim-child="slide-up delay-2" class="col-lg-4 col-md-6">
        <div class="us-why-card">
          <div class="us-why-card__icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="6"/><path d="M15.5 13.5 17 22l-5-3-5 3 1.5-8.5"/></svg>
          </div>
          <h4 class="us-why-card__title">{{ __('messages.step_1_title') }}</h4>
          <p class="us-why-card__text">{{ __('messages.step_1_text') }}</p>
        </div>
      </div>

      <div data-anim-child="slide-up delay-3" class="col-lg-4 col-md-6">
        <div class="us-why-card">
          <div class="us-why-card__icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
          </div>
          <h4 class="us-why-card__title">{{ __('messages.step_2_title') }}</h4>
          <p class="us-why-card__text">{{ __('messages.step_2_text') }}</p>
        </div>
      </div>

      <div data-anim-child="slide-up delay-4" class="col-lg-4 col-md-6">
        <div class="us-why-card">
          <div class="us-why-card__icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
          </div>
          <h4 class="us-why-card__title">{{ __('messages.step_3_title') }}</h4>
          <p class="us-why-card__text">{{ __('messages.step_3_text') }}</p>
        </div>
      </div>
    </div>
  </div>
</section>

<style>
.us-why {
  background:
    radial-gradient(70rem 34rem at 85% 0%, rgba(60, 180, 176, 0.10) 0%, transparent 60%),
    linear-gradient(170deg, #081d36 0%, #093162 100%);
}
.us-why__title { color: #f7f8fa !important; }
.us-why__subtitle { color: rgba(247, 248, 250, 0.66) !important; margin-top: 10px; }

.us-eyebrow {
  font-size: 13px; font-weight: 700;
  letter-spacing: 0.14em; text-transform: uppercase;
  color: #297a7b; margin-bottom: 12px;
}
.us-eyebrow--on-dark { color: #3cb4b0; }

.us-why-card {
  height: 100%;
  padding: 34px 30px;
  border-radius: 14px;
  background: rgba(247, 248, 250, 0.04);
  border: 1px solid rgba(247, 248, 250, 0.10);
  transition: transform 240ms cubic-bezier(.645,.045,.355,1), border-color 150ms ease, background 150ms ease;
}
.us-why-card:hover {
  transform: translateY(-6px);
  border-color: rgba(60, 180, 176, 0.45);
  background: rgba(247, 248, 250, 0.06);
}
.us-why-card__icon {
  width: 56px; height: 56px;
  border-radius: 14px;
  display: flex; align-items: center; justify-content: center;
  background: rgba(60, 180, 176, 0.14);
  color: #3cb4b0;
  margin-bottom: 22px;
}
.us-why-card__title { color: #f7f8fa; font-size: 19px; font-weight: 600; margin-bottom: 10px; }
.us-why-card__text { color: rgba(247, 248, 250, 0.66); font-size: 15px; line-height: 1.65; margin: 0; }
</style>
