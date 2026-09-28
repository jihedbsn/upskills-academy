<!DOCTYPE html>
<html class="loading semi-dark-layout" lang="fr" data-layout="semi-dark-layout" data-textdirection="ltr">
<!-- BEGIN: Head-->
@include('layouts.head')
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern content-detached-left-sidebar navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="content-detached-left-sidebar">

    <!-- BEGIN: Header-->
    @include('layouts.nav')
  
    <!-- END: Header-->


    <!-- BEGIN: Main Menu-->
   @include('layouts.header')
    <!-- END: Main Menu-->

    <!-- BEGIN: Content-->
    <div class="app-content content ecommerce-application">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-xxl p-0">
            @php 
                    $title="test";
                    $page=3;
            @endphp

                  @include('layouts.headerpage')
            <div class="content-detached content-right" style="min-height: 800px">
                
              
            </div>
    </div>
    <!-- END: Content-->

  @include('layouts.footer')
</body>
<!-- END: Body-->

</html>