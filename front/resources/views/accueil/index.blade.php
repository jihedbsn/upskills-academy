<!DOCTYPE html>
<html lang="fr">

<head>
  @include('layouts.head')
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
      <!-- Carousel Section -->
      @include('accueil.components.carousel')

      <!-- Welcome/About Us Section -->
      @include('accueil.components.stats')

      <!-- Popular Courses Section -->
      @include('accueil.components.courses')
      @include('accueil.components.welcome')

      <!-- Why Choose Upskills Section -->
      @include('accueil.components.learn-skills')

      <!-- Stats Section -->


      <!-- Testimonials/Avis Section -->
      @include('accueil.components.avis')
      @include('accueil.components.cta')
      @include('accueil.components.nos-partenaires')
    </div>
  </main>

  @include('layouts.footer')
</body>
</html>
