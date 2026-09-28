<!-- Testimonials Section — scroll-snap carousel -->
<section class="us-testimonials layout-pt-lg layout-pb-lg">
  <div data-anim-wrap class="container">
    <div class="row y-gap-20 justify-between items-end">
      <div data-anim-child="slide-up delay-1" class="col-lg-7">
        <div class="sectionTitle text-start">
          <p class="us-eyebrow">{{ __('messages.testimonials_eyebrow') }}</p>
          <h2 class="sectionTitle__title">{{ __('messages.testimonials_title') }}</h2>
          <p class="sectionTitle__text">{{ __('messages.testimonials_subtitle') }}</p>
        </div>
      </div>
      <div data-anim-child="slide-up delay-2" class="col-auto">
        <div class="us-car-nav">
          <button class="us-car-btn" data-us-car-prev aria-label="{{ __('messages.previous') ?? 'Previous' }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M19 12H5M11 18l-6-6 6-6"/></svg>
          </button>
          <button class="us-car-btn" data-us-car-next aria-label="{{ __('messages.next') ?? 'Next' }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
          </button>
        </div>
      </div>
    </div>

    @if(isset($avisAcceuil) && $avisAcceuil->count() > 0)
      <div class="us-car pt-40" data-us-car>
        <div class="us-car__viewport" data-us-car-viewport>
          @foreach($avisAcceuil as $index => $avis)
            <div class="us-car__slide">
              <figure class="us-quote-card">
                <svg class="us-quote-card__mark" width="34" height="26" viewBox="0 0 34 26" fill="currentColor" aria-hidden="true">
                  <path d="M0 26V14.7C0 6.9 4.6 1.6 12.4 0l1.8 4.1c-4.4 1.5-6.8 4.3-7.1 8.1H14V26H0zm20 0V14.7C20 6.9 24.6 1.6 32.4 0l1.6 4.1c-4.4 1.5-6.8 4.3-7.1 8.1H34V26H20z"/>
                </svg>
                <blockquote class="us-quote-card__text">{{ $avis->avis }}</blockquote>
                <figcaption class="us-quote-card__footer">
                  <img class="us-quote-card__avatar" src="{{ $avis->image_url }}" alt="{{ $avis->client }}" loading="lazy">
                  <div>
                    <div class="us-quote-card__name">{{ $avis->client }}</div>
                    <div class="us-quote-card__role">{{ __('messages.client') }}</div>
                  </div>
                  <div class="us-quote-card__stars" aria-label="5/5">★★★★★</div>
                </figcaption>
              </figure>
            </div>
          @endforeach
        </div>
        <div class="us-car__dots" data-us-car-dots></div>
      </div>
    @endif
  </div>
</section>

<style>
.us-testimonials { background: #f7f8fa; }
.us-testimonials .sectionTitle__title { color: #081d36; }
.us-testimonials .sectionTitle__text { color: #5c626b; margin-top: 10px; }

.us-eyebrow {
  font-size: 13px; font-weight: 700;
  letter-spacing: 0.14em; text-transform: uppercase;
  color: #297a7b; margin-bottom: 12px;
}

/* Carousel — native scroll-snap, no library */
.us-car__viewport {
  display: flex;
  gap: 30px;
  overflow-x: auto;
  scroll-snap-type: x mandatory;
  scroll-behavior: smooth;
  scrollbar-width: none;
  -ms-overflow-style: none;
  padding: 6px 6px 26px; /* room for card hover shadow */
  margin: -6px -6px 0;
}
.us-car__viewport::-webkit-scrollbar { display: none; }
.us-car__slide {
  flex: 0 0 calc((100% - 60px) / 3);
  scroll-snap-align: start;
  display: flex;
}
@media (max-width: 991px) { .us-car__slide { flex-basis: calc((100% - 30px) / 2); } }
@media (max-width: 640px) { .us-car__slide { flex-basis: 100%; } }

.us-car-nav { display: flex; gap: 10px; }
.us-car-btn {
  width: 46px; height: 46px;
  display: flex; align-items: center; justify-content: center;
  border-radius: 50%;
  border: 1.5px solid #c3c6ca;
  background: #ffffff;
  color: #093162;
  cursor: pointer;
  transition: background 150ms ease, border-color 150ms ease, color 150ms ease;
}
.us-car-btn:hover { background: #093162; border-color: #093162; color: #f7f8fa; }
.us-car-btn:focus-visible { outline: none; box-shadow: 0 0 0 3px rgba(60,180,176,0.45); }
html[dir="rtl"] .us-car-btn svg { transform: scaleX(-1); }

.us-car__dots { display: flex; justify-content: center; gap: 8px; margin-top: 4px; }
.us-car__dots button {
  width: 8px; height: 8px; border-radius: 999px;
  border: none; padding: 0; cursor: pointer;
  background: #c3c6ca;
  transition: background 150ms ease, width 240ms cubic-bezier(.645,.045,.355,1);
}
.us-car__dots button.is-active { background: #093162; width: 26px; }

/* Quote cards (unchanged design) */
.us-quote-card {
  display: flex; flex-direction: column;
  width: 100%; margin: 0;
  padding: 30px 30px 26px;
  background: #ffffff;
  border: 1px solid #dddfe2;
  border-radius: 16px;
  box-shadow: 0 1px 3px rgba(8, 23, 54, 0.05);
  transition: transform 240ms cubic-bezier(.645,.045,.355,1), box-shadow 240ms cubic-bezier(.645,.045,.355,1), border-color 150ms ease;
}
.us-quote-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 16px 36px rgba(8, 23, 54, 0.10);
  border-color: #96d6d4;
}
.us-quote-card__mark { color: #c2e8e6; margin-bottom: 16px; flex-shrink: 0; }
.us-quote-card__text { font-size: 15.5px; line-height: 1.7; color: #29303b; margin: 0 0 24px; flex-grow: 1; }
.us-quote-card__footer { display: flex; align-items: center; gap: 12px; padding-top: 18px; border-top: 1px solid #eef0f3; }
.us-quote-card__avatar { width: 44px; height: 44px; border-radius: 50%; object-fit: cover; flex-shrink: 0; }
.us-quote-card__name { font-size: 15px; font-weight: 600; color: #081d36; line-height: 1.3; }
.us-quote-card__role { font-size: 13px; color: #767b83; }
.us-quote-card__stars { margin-inline-start: auto; color: #e59819; font-size: 13px; letter-spacing: 1.5px; white-space: nowrap; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
  var root = document.querySelector('[data-us-car]');
  if (!root) return;
  var vp = root.querySelector('[data-us-car-viewport]');
  var slides = Array.prototype.slice.call(vp.children);
  var dotsBox = root.querySelector('[data-us-car-dots]');
  var prev = document.querySelector('[data-us-car-prev]');
  var next = document.querySelector('[data-us-car-next]');
  var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function perView() {
    var w = window.innerWidth;
    return w <= 640 ? 1 : (w <= 991 ? 2 : 3);
  }
  function pages() { return Math.max(1, Math.ceil(slides.length / perView())); }
  var GAP = 30;
  function pageWidth() { return vp.clientWidth + GAP; }
  function currentPage() { return Math.round(Math.abs(vp.scrollLeft) / pageWidth()); }

  function renderDots() {
    dotsBox.innerHTML = '';
    for (var i = 0; i < pages(); i++) {
      var b = document.createElement('button');
      b.type = 'button';
      b.setAttribute('aria-label', 'Slide ' + (i + 1));
      if (i === currentPage()) b.className = 'is-active';
      (function (idx) {
        b.addEventListener('click', function () { goTo(idx); });
      })(i);
      dotsBox.appendChild(b);
    }
  }
  function goTo(page) {
    var max = pages() - 1;
    if (page < 0) page = max; if (page > max) page = 0;
    var dir = getComputedStyle(vp).direction === 'rtl' ? -1 : 1;
    vp.scrollTo({ left: dir * page * pageWidth(), behavior: reduced ? 'auto' : 'smooth' });
  }
  function step(delta) { goTo(currentPage() + delta); }

  if (prev) prev.addEventListener('click', function () { step(-1); });
  if (next) next.addEventListener('click', function () { step(1); });

  var t;
  vp.addEventListener('scroll', function () { clearTimeout(t); t = setTimeout(renderDots, 80); }, { passive: true });
  window.addEventListener('resize', function () { clearTimeout(t); t = setTimeout(renderDots, 120); });

  // gentle auto-advance; pauses on hover/touch and under reduced motion
  if (!reduced) {
    var auto = setInterval(function () { step(1); }, 5200);
    ['mouseenter', 'touchstart', 'focusin'].forEach(function (ev) {
      root.addEventListener(ev, function () { clearInterval(auto); auto = null; }, { once: true, passive: true });
    });
  }
  renderDots();
});
</script>
