<!-- CTA band: between testimonials and partners -->
<section class="us-cta-wrap">
  <div data-anim-wrap class="container">
    <div data-anim-child="slide-up delay-1" class="us-cta">
      <div class="us-cta__grid" aria-hidden="true"></div>
      <div class="us-cta__glow" aria-hidden="true"></div>

      <div class="row items-center justify-between y-gap-30">
        <div class="col-lg-7">
          <p class="us-cta__eyebrow">{{ __('messages.cta_eyebrow') }}</p>
          <h2 class="us-cta__title">{{ __('messages.cta_title') }}</h2>
          <p class="us-cta__text">{{ __('messages.cta_text') }}</p>
        </div>
        <div class="col-auto">
          <div class="us-cta__actions">
            <a href="{{ route('courses.show', ['product' => 8]) }}" class="us-btn us-btn--teal">
              {{ __('messages.cta_primary') }}
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="us-btn__arrow"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </a>
            <a href="{{ route('filter.index') }}" class="us-btn us-btn--ghost">{{ __('messages.cta_secondary') }}</a>
          </div>
          <p class="us-cta__note">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
            {{ __('messages.cta_note') }}
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<style>
.us-cta-wrap { background: #f7f8fa; padding-bottom: 90px; }
.us-cta {
  position: relative;
  overflow: hidden;
  border-radius: 24px;
  padding: 56px 56px;
  background:
    radial-gradient(46rem 22rem at 100% 0%, rgba(60, 180, 176, 0.18) 0%, transparent 55%),
    linear-gradient(150deg, #081d36 0%, #093162 70%, #0d3a6e 100%);
  box-shadow: 0 24px 48px rgba(8, 23, 54, 0.18);
}
.us-cta__grid {
  position: absolute; inset: 0;
  background-image:
    linear-gradient(rgba(247,248,250,0.05) 1px, transparent 1px),
    linear-gradient(90deg, rgba(247,248,250,0.05) 1px, transparent 1px);
  background-size: 44px 44px;
  mask-image: radial-gradient(50rem 26rem at 18% 30%, #000 0%, transparent 72%);
  -webkit-mask-image: radial-gradient(50rem 26rem at 18% 30%, #000 0%, transparent 72%);
  pointer-events: none;
}
.us-cta__glow {
  position: absolute; right: -8rem; bottom: -12rem;
  width: 30rem; height: 30rem; border-radius: 50%;
  background: radial-gradient(circle, rgba(60,180,176,0.16) 0%, transparent 60%);
  pointer-events: none;
}
.us-cta__eyebrow {
  font-size: 13px; font-weight: 700;
  letter-spacing: 0.14em; text-transform: uppercase;
  color: #3cb4b0; margin-bottom: 12px;
}
.us-cta__title {
  font-family: 'Newsreader', Georgia, serif;
  font-size: clamp(26px, 3vw, 38px);
  line-height: 1.2; letter-spacing: -0.02em;
  color: #f7f8fa; margin-bottom: 12px; font-weight: 600;
}
.us-cta__text { font-size: 16px; line-height: 1.65; color: rgba(247,248,250,0.72); margin: 0; max-width: 44rem; }
.us-cta__actions { display: flex; flex-wrap: wrap; gap: 12px; }
.us-cta__note {
  display: flex; align-items: center; gap: 8px;
  margin: 14px 0 0; font-size: 13px; color: rgba(247,248,250,0.6);
}
.us-cta__note svg { color: #3cb4b0; flex-shrink: 0; }
@media (max-width: 991px) {
  .us-cta { padding: 40px 30px; }
}
</style>
