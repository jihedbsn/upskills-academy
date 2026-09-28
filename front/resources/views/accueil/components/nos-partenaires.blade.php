<!-- Partners Section — auto-looping marquee -->
@if(isset($partenaires) && $partenaires->count() > 0)
<section class="us-partners layout-pt-md layout-pb-md">
  <div class="container">
    <div class="row justify-center text-center">
      <div class="col-lg-8">
        <p class="us-partners__label">{{ __('messages.our_partners') }}</p>
      </div>
    </div>
  </div>

  <div class="us-marquee pt-30" aria-hidden="false">
    <div class="us-marquee__track">
      @foreach([0, 1, 2, 3] as $dup) {{-- 4 identical groups; -50% loop point is always seamless --}}
        <div class="us-marquee__group" @if($dup) aria-hidden="true" @endif>
          @foreach($partenaires as $partenaire)
            @if($partenaire->url && $partenaire->url !== '#')
              <a class="us-marquee__item" href="{{ $partenaire->url }}" target="_blank" rel="noopener" @if($dup) tabindex="-1" @endif>
                <img src="{{ config('app.file_url') }}uploads/{{ $partenaire->logo }}" alt="{{ $dup ? '' : $partenaire->nom }}" loading="lazy">
              </a>
            @else
              <span class="us-marquee__item">
                <img src="{{ config('app.file_url') }}uploads/{{ $partenaire->logo }}" alt="{{ $dup ? '' : $partenaire->nom }}" loading="lazy">
              </span>
            @endif
          @endforeach
        </div>
      @endforeach
    </div>
  </div>
</section>
@endif

<style>
.us-partners { background: #ffffff; border-top: 1px solid #eef0f3; }
.us-partners__label {
  font-size: 13px;
  font-weight: 700;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  color: #767b83;
  margin: 0;
}

/* Marquee: two identical groups translate -50%; pauses on hover */
.us-marquee {
  overflow: hidden;
  position: relative;
  direction: ltr; /* decorative strip: normalize so layout & animation agree in RTL */
  -webkit-mask-image: linear-gradient(90deg, transparent, #000 8%, #000 92%, transparent);
  mask-image: linear-gradient(90deg, transparent, #000 8%, #000 92%, transparent);
}
.us-marquee__track {
  display: flex;
  width: max-content;
  animation: usMarquee 36s linear infinite;
}
.us-marquee:hover .us-marquee__track { animation-play-state: paused; }
.us-marquee__group {
  display: flex;
  align-items: center;
  gap: 84px;
  padding-inline-end: 84px;
}
.us-marquee__item {
  display: flex;
  align-items: center;
  height: 72px;
  flex-shrink: 0;
  filter: grayscale(1);
  opacity: 0.6;
  transition: filter 150ms ease, opacity 150ms ease;
}
.us-marquee__item:hover { filter: grayscale(0); opacity: 1; }
.us-marquee__item img { height: 56px; width: auto; display: block; }

@keyframes usMarquee {
  from { transform: translateX(0); }
  to { transform: translateX(-50%); }
}
@media (prefers-reduced-motion: reduce) {
  .us-marquee__track { animation: none; flex-wrap: wrap; justify-content: center; width: 100%; }
  .us-marquee__group[aria-hidden="true"] { display: none; }
}
@media (max-width: 576px) {
  .us-marquee__group { gap: 48px; padding-inline-end: 48px; }
  .us-marquee__item { height: 56px; }
  .us-marquee__item img { height: 42px; }
}
</style>
