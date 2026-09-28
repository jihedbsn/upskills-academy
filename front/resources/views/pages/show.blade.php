<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
  @include('layouts.head')
  <title>{{ $page->{'meta_title_' . app()->getLocale()} ?? $page->{'titre_' . app()->getLocale()} }} - Upskills Academy</title>
  <meta name="description" content="{{ $page->{'description_' . app()->getLocale()} }}">
  
  <style>
    /* CSS spécifique pour le contenu des pages - préserve les styles des listes et espacement */
    .page-content .content-area {
      line-height: 1.6;
    }
    
    /* Styles pour les listes non ordonnées */
    .page-content .content-area ul {
      list-style-type: disc !important;
      margin: 16px 0 !important;
      padding-left: 30px !important;
    }
    
    .page-content .content-area ul li {
      list-style-type: disc !important;
      margin-bottom: 8px !important;
      display: list-item !important;
    }
    
    /* Styles pour les listes ordonnées */
    .page-content .content-area ol {
      list-style-type: decimal !important;
      margin: 16px 0 !important;
      padding-left: 30px !important;
    }
    
    .page-content .content-area ol li {
      list-style-type: decimal !important;
      margin-bottom: 8px !important;
      display: list-item !important;
    }
    
    /* Styles pour les paragraphes */
    .page-content .content-area p {
      margin: 16px 0 !important;
      line-height: 1.6 !important;
    }
    
    /* Styles pour les titres dans le contenu */
    .page-content .content-area h1,
    .page-content .content-area h2,
    .page-content .content-area h3,
    .page-content .content-area h4,
    .page-content .content-area h5,
    .page-content .content-area h6 {
      margin: 24px 0 16px 0 !important;
      font-weight: bold !important;
    }
    
    .page-content .content-area h1 {
      font-size: 28px !important;
    }
    
    .page-content .content-area h2 {
      font-size: 24px !important;
    }
    
    .page-content .content-area h3 {
      font-size: 20px !important;
    }
    
    /* Styles pour le texte en gras */
    .page-content .content-area strong,
    .page-content .content-area b {
      font-weight: bold !important;
    }
    
    /* Styles pour le texte en italique */
    .page-content .content-area em,
    .page-content .content-area i {
      font-style: italic !important;
    }
    
    /* Styles pour les liens */
    .page-content .content-area a {
      color: #7c3aed !important;
      text-decoration: underline !important;
    }
    
    .page-content .content-area a:hover {
      color: #5b21b6 !important;
    }
    
    /* Styles pour les sauts de ligne */
    .page-content .content-area br {
      line-height: 1.6 !important;
    }
    
    /* Reset pour les éléments qui pourraient être affectés par le CSS du template */
    .page-content .content-area * {
      box-sizing: border-box;
    }
    
    /* Support RTL pour l'arabe */
    .page-content .content-area[dir="rtl"] ul,
    .page-content .content-area[dir="rtl"] ol {
      padding-right: 30px !important;
      padding-left: 0 !important;
    }
    
    /* Espacement général */
    .page-content .content-area > *:first-child {
      margin-top: 0 !important;
    }
    
    .page-content .content-area > *:last-child {
      margin-bottom: 0 !important;
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

    <div class="content-wrapper js-content-wrapper">
      <!-- Breadcrumbs -->
      <section class="layout-pt-sm layout-pb-sm bg-light-6">
        <div class="container">
          <div class="row y-gap-30 justify-between items-center">
            <div class="col-auto">
              <div class="d-flex items-center h-100 fw-500">
                <div class="breadcrumbs__content">
                  <div class="breadcrumbs__item">
                    <a href="{{ route('accueil.index') }}">{{ __('messages.home') }}</a>
                  </div>
                  <div class="breadcrumbs__item">
                    <a href="{{ route('pages.index') }}">{{ __('messages.pages') }}</a>
                  </div>
                  <div class="breadcrumbs__item">
                    <span class="us-crumb-current">{{ $page->{'titre_' . app()->getLocale()} ?? $page->titre_en }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Page Content -->
      <section class="layout-pt-md layout-pb-md">
        <div class="container">
          <div class="row justify-center">
            <div class="col-xl-8 col-lg-9 col-md-11">
              <div class="page-content">
                                 <!-- Page Header -->
                 <div class="page-header text-center mb-40">
                   <h1 class="text-30 lh-14 mb-20">{{ $page->{'titre_' . app()->getLocale()} }}</h1>
                 </div>

                 <!-- Page Body -->
                 <div class="page-body">
                   <div class="content-area" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
                     @if($page->description_ar && app()->getLocale() == 'ar')
                       <div class="text-16 text-dark-1">
                         {!! $page->description_ar !!}
                       </div>
                     @elseif($page->description_en && app()->getLocale() == 'en')
                       <div class="text-16 text-dark-1">
                         {!! $page->description_en !!}
                       </div>
                     @else
                       <div class="text-16 text-dark-1">
                         {!! $page->description !!}
                       </div>
                     @endif
                   </div>
                 </div>

                <!-- Back to Pages -->
                <div class="text-center mt-40">
                  <a href="{{ route('pages.index') }}" class="button -md -outline-dark-1 text-dark-1">
                    <i class="icon-arrow-left mr-10"></i>
                    {{ __('messages.back_to_pages') }}
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </main>

  @include('layouts.footer')

  <!-- Scripts -->
  <script src="{{ asset('assets/js/vendors.js') }}"></script>
  <script src="{{ asset('assets/js/main.js') }}"></script>
</body>

</html>
