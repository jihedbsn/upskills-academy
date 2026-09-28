@php
  $hero = isset($carousels) ? $carousels->where('is_active', 1)->sortBy('order')->first() : null;
  $heroTitle = $hero->slug ?? __('messages.hero_title');
  $heroText  = $hero->description ?? __('messages.hero_subtitle');
@endphp

<!-- Hero Section -->
<section class="us-hero">
  <div class="us-hero__glow" aria-hidden="true"></div>
  <div class="us-hero__grid" aria-hidden="true"></div>

  <div class="container">
    <div class="row items-center justify-between y-gap-40">
      <div class="col-xl-6 col-lg-7">
        <div class="us-hero__content">
          <p class="us-hero__eyebrow us-hero-anim" style="--d:0ms">{{ __('messages.swedish_academy') }}</p>
          <h1 class="us-hero__title us-hero-anim" style="--d:90ms">{{ $heroTitle }}</h1>
          <p class="us-hero__text us-hero-anim" style="--d:180ms">{{ $heroText }}</p>

          <div class="us-hero__actions us-hero-anim" style="--d:270ms">
            <a href="{{ route('filter.index') }}" class="us-btn us-btn--teal">
              {{ __('messages.courses') }}
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="us-btn__arrow"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </a>
            <a href="{{ route('partnership.index') }}" class="us-btn us-btn--ghost">{{ __('messages.be_partner') }}</a>
          </div>

          <ul class="us-hero__trust us-hero-anim" style="--d:360ms">
            <li>
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
              {{ __('messages.hero_trust_cefr') }}
            </li>
            <li>
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
              {{ __('messages.hero_trust_teachers') }}
            </li>
            <li>
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
              {{ __('messages.hero_trust_live') }}
            </li>
          </ul>
        </div>
      </div>

      <div class="col-xl-5 col-lg-5 us-hero__visualcol">
        <div class="us-hero__visual us-hero-anim" style="--d:240ms">
          <div class="us-hero-deco us-hero-deco--dots" aria-hidden="true"></div>
          <div class="us-hero-deco us-hero-deco--ring" aria-hidden="true"></div>
          <div class="us-hero-deco us-hero-deco--spark" aria-hidden="true">
            <svg width="34" height="34" viewBox="0 0 34 34" fill="none"><path d="M17 2v30M2 17h30" stroke="#3cb4b0" stroke-width="3" stroke-linecap="round"/><path d="M7 7l20 20M27 7L7 27" stroke="#3cb4b0" stroke-width="2" stroke-linecap="round" opacity="0.55"/></svg>
          </div>
          <div class="us-hero-photo">
            <img src="{{ asset('assets/img/hero/hero.webp') }}"
                 srcset="{{ asset('assets/img/hero/hero-sm.webp') }} 561w, {{ asset('assets/img/hero/hero.webp') }} 1122w"
                 sizes="(min-width: 1200px) 400px, 340px"
                 width="1122" height="1402" fetchpriority="high"
                 alt="{{ __('messages.hero_photo_alt') }}">
          </div>

          <div class="us-hero-chip us-hero-chip--band us-float" style="--f:1.2s">
            <div class="us-hero-chip__icon">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div>
              <strong>{{ __('messages.hero_chip_band') }}</strong>
              <small>{{ __('messages.hero_chip_band_sub') }}</small>
            </div>
          </div>

          <div class="us-hero-chip us-hero-chip--hello us-float" style="--f:2.1s">
            <span class="us-live-dot" aria-hidden="true"></span>
            <span class="us-hero-chip__live-label">{{ __('messages.hero_chip_live_label') }}</span>
            <span class="us-hero-chip__live-text">{{ __('messages.hero_chip_live_text') }}</span>
          </div>

          <div class="us-hero-levels us-float" style="--f:0.6s">
            <span>A1</span><span>A2</span><span>B1</span><span>B2</span><span class="is-accent">C1</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<style>
/* ============ Upskills hero ============ */
.us-hero {
  position: relative;
  overflow: hidden;
  background:
    radial-gradient(90rem 42rem at 110% -20%, rgba(60, 180, 176, 0.16) 0%, transparent 55%),
    linear-gradient(160deg, #081d36 0%, #093162 62%, #0d3a6e 100%);
  padding: 170px 0 110px;
  min-height: 620px;
  display: flex;
  align-items: center;
}
.us-hero__glow {
  position: absolute; inset: auto auto -30% -12%;
  width: 44rem; height: 44rem; border-radius: 50%;
  background: radial-gradient(circle, rgba(60,180,176,0.12) 0%, transparent 62%);
  pointer-events: none;
}
.us-hero__grid {
  position: absolute; inset: 0;
  background-image:
    linear-gradient(rgba(247,248,250,0.045) 1px, transparent 1px),
    linear-gradient(90deg, rgba(247,248,250,0.045) 1px, transparent 1px);
  background-size: 56px 56px;
  mask-image: radial-gradient(80rem 40rem at 30% 20%, #000 0%, transparent 75%);
  -webkit-mask-image: radial-gradient(80rem 40rem at 30% 20%, #000 0%, transparent 75%);
  pointer-events: none;
}

.us-hero__eyebrow {
  font-size: 13px; font-weight: 700;
  letter-spacing: 0.14em; text-transform: uppercase;
  color: #3cb4b0; margin-bottom: 18px;
}
.us-hero__title {
  font-family: 'Newsreader', Georgia, serif;
  font-size: clamp(38px, 4.6vw, 60px);
  line-height: 1.12; letter-spacing: -0.02em;
  color: #f7f8fa; margin-bottom: 22px; font-weight: 600;
}
.us-hero__text {
  font-size: 18px; line-height: 1.65;
  color: rgba(247,248,250,0.78);
  max-width: 34rem; margin-bottom: 34px;
}
.us-hero__actions { display: flex; flex-wrap: wrap; gap: 14px; margin-bottom: 38px; }

.us-btn {
  display: inline-flex; align-items: center; gap: 10px;
  padding: 15px 28px; border-radius: 10px;
  font-size: 15px; font-weight: 600; line-height: 1;
  text-decoration: none;
  transition: background 150ms ease, color 150ms ease, border-color 150ms ease, transform 150ms ease;
}
.us-btn--teal { background: #3cb4b0; color: #081d36 !important; }
.us-btn--teal:hover { background: #69c5c2; transform: translateY(-1px); }
.us-btn--teal .us-btn__arrow { transition: transform 180ms cubic-bezier(.165,.84,.44,1); }
.us-btn--teal:hover .us-btn__arrow { transform: translateX(4px); }
html[dir="rtl"] .us-btn__arrow { transform: scaleX(-1); }
html[dir="rtl"] .us-btn--teal:hover .us-btn__arrow { transform: scaleX(-1) translateX(4px); }
.us-btn--ghost {
  background: transparent; color: #f7f8fa !important;
  border: 1.5px solid rgba(247,248,250,0.28);
}
.us-btn--ghost:hover { border-color: rgba(247,248,250,0.6); background: rgba(247,248,250,0.06); }

.us-hero__trust {
  display: flex; flex-wrap: wrap; gap: 12px 26px;
  list-style: none; padding: 0; margin: 0;
}
.us-hero__trust li {
  display: inline-flex; align-items: center; gap: 8px;
  font-size: 14px; color: rgba(247,248,250,0.66);
}
.us-hero__trust svg { color: #3cb4b0; flex-shrink: 0; }

/* Floating visual composition: photo anchor + overlapping chips + shapes */
.us-hero__visual { position: relative; height: 530px; }
.us-hero-photo {
  position: absolute; top: 16px; right: 24px;
  width: 385px;
  /* editorial corner: one soft leaf-cut corner, top-right */
  border-radius: 24px 130px 24px 24px;
  box-shadow: 0 32px 64px rgba(4, 12, 26, 0.5);
}
.us-hero-photo::before {
  /* offset teal frame behind the photo for depth */
  content: '';
  position: absolute; inset: 0;
  transform: translate(16px, 16px) rotate(2deg);
  border-radius: 24px 130px 24px 24px;
  background: linear-gradient(140deg, rgba(60,180,176,0.45), rgba(60,180,176,0.06));
  z-index: -1;
}
.us-hero-photo::after {
  /* light grade: melt the photo into the navy scene + teal cast top-right */
  content: '';
  position: absolute; inset: 0;
  border-radius: 24px 130px 24px 24px;
  background:
    radial-gradient(26rem 16rem at 96% 0%, rgba(60, 180, 176, 0.22) 0%, transparent 55%),
    linear-gradient(198deg, rgba(9, 49, 98, 0) 46%, rgba(8, 29, 54, 0.42) 100%);
  pointer-events: none;
}
.us-hero-photo img {
  display: block;
  width: 100%; height: auto;
  border-radius: 24px 130px 24px 24px;
  border: 1px solid rgba(247,248,250,0.16);
}

/* Decorative shapes */
.us-hero-deco { position: absolute; pointer-events: none; }
.us-hero-deco--dots {
  top: -26px; right: 356px;
  width: 150px; height: 190px;
  background-image: radial-gradient(rgba(105, 197, 194, 0.55) 2.2px, transparent 2.6px);
  background-size: 21px 21px;
  mask-image: linear-gradient(200deg, #000 55%, transparent 100%);
  -webkit-mask-image: linear-gradient(200deg, #000 55%, transparent 100%);
}
.us-hero-deco--ring {
  top: -34px; right: 6px;
  width: 148px; height: 148px;
  border: 2.5px solid rgba(60, 180, 176, 0.5);
  border-radius: 50%;
  mask-image: linear-gradient(215deg, #000 45%, transparent 78%);
  -webkit-mask-image: linear-gradient(215deg, #000 45%, transparent 78%);
}
.us-hero-deco--spark { bottom: 84px; right: -6px; opacity: 0.9; }

@media (min-width: 1200px) {
  .us-hero-photo { width: 460px; right: 30px; }
  .us-hero__visual { height: 620px; }
  .us-hero-deco--dots { right: 430px; }
  .us-hero-deco--ring { right: 4px; width: 170px; height: 170px; }
}
.us-hero-chip {
  position: absolute;
  display: flex; align-items: center; gap: 12px;
  background: rgba(247, 248, 250, 0.72);
  backdrop-filter: blur(12px) saturate(1.15);
  -webkit-backdrop-filter: blur(12px) saturate(1.15);
  border: 1px solid rgba(247, 248, 250, 0.45);
  border-radius: 12px; padding: 12px 18px;
  box-shadow: 0 16px 32px rgba(4, 12, 26, 0.30);
}
.us-hero-chip--band { bottom: 118px; right: 344px; }
@media (min-width: 1200px) { .us-hero-chip--band { right: 424px; bottom: 150px; } }
.us-hero-chip__icon {
  width: 36px; height: 36px; border-radius: 10px; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
  background: #eff9f9; color: #297a7b;
}
.us-hero-chip--band strong { display: block; font-size: 14px; color: #081d36; line-height: 1.25; white-space: nowrap; }
.us-hero-chip--band small { font-size: 12px; color: #767b83; }
.us-hero-chip--hello { top: -25px; right: 128px; padding: 11px 18px; }
@media (min-width: 1200px) { .us-hero-chip--hello { top: -17px; right: 188px; } }
.us-live-dot {
  width: 10px; height: 10px; border-radius: 50%;
  background: #2d6cdf; flex-shrink: 0;
  animation: usPulse 2.2s cubic-bezier(.455,.03,.515,.955) infinite;
}
@keyframes usPulse {
  0% { box-shadow: 0 0 0 0 rgba(45, 108, 223, 0.5); }
  70% { box-shadow: 0 0 0 9px rgba(45, 108, 223, 0); }
  100% { box-shadow: 0 0 0 0 rgba(45, 108, 223, 0); }
}
.us-hero-chip__live-label { font-size: 12px; font-weight: 800; letter-spacing: 0.1em; color: #081d36; }
.us-hero-chip__live-text { font-size: 14px; font-weight: 500; color: #29303b; }
.us-hero-levels {
  position: absolute; bottom: -8px; right: 48px;
  display: flex; gap: 8px;
  background: rgba(8, 29, 54, 0.72);
  border: 1px solid rgba(247,248,250,0.14);
  backdrop-filter: blur(8px);
  border-radius: 999px; padding: 11px 16px;
}
.us-hero-levels span {
  padding: 6px 14px; border-radius: 999px;
  font-size: 14px; font-weight: 700; color: rgba(247,248,250,0.62);
}
.us-hero-levels span.is-accent { background: #3cb4b0; color: #081d36; }

/* Entrance: one orchestrated staggered reveal */
.us-hero-anim {
  opacity: 0;
  transform: translateY(18px);
  animation: usHeroIn 650ms cubic-bezier(.23, 1, .32, 1) forwards;
  animation-delay: var(--d, 0ms);
}
@keyframes usHeroIn { to { opacity: 1; transform: translateY(0); } }

/* Gentle float for the visual cards (illustrative, slow) */
.us-float { animation: usFloat 7s ease-in-out infinite; animation-delay: var(--f, 0s); }
@keyframes usFloat {
  0%, 100% { translate: 0 0; }
  50% { translate: 0 -10px; }
}
.us-hero-anim.us-float { animation: usHeroIn 650ms cubic-bezier(.23,1,.32,1) forwards, usFloat 7s ease-in-out calc(700ms + var(--f, 0s)) infinite; animation-delay: var(--d, 0ms), calc(700ms + var(--f, 0s)); }

@media (prefers-reduced-motion: reduce) {
  .us-hero-anim, .us-float, .us-live-dot { animation: none !important; opacity: 1; transform: none; }
}

.us-hero__visualcol { display: none; }
@media (min-width: 992px) {
  .us-hero__visualcol { display: block; }
}
@media (max-width: 991px) {
  .us-hero { padding: 150px 0 80px; min-height: 0; }
  .us-hero__text { font-size: 16px; }
}
</style>
