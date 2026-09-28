
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="Vuexy admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords" content="admin template, Vuexy admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="PIXINVENT">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Upskills Academy — Admin</title>
    <link rel="apple-touch-icon" href="/admin/app-assets/images/ico/apple-icon-120.png">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="shortcut icon" type="image/x-icon" href="/admin/app-assets/images/ico/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="/admin/app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="/admin/app-assets/vendors/css/extensions/nouislider.min.css">
    <link rel="stylesheet" type="text/css" href="/admin/app-assets/vendors/css/extensions/toastr.min.css">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="/admin/app-assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="/admin/app-assets/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="/admin/app-assets/css/colors.css">
    <link rel="stylesheet" type="text/css" href="/admin/app-assets/css/components.css">
    <link rel="stylesheet" type="text/css" href="/admin/app-assets/css/themes/dark-layout.css">
    <link rel="stylesheet" type="text/css" href="/admin/app-assets/css/themes/bordered-layout.css">
    <link rel="stylesheet" type="text/css" href="/admin/app-assets/css/themes/semi-dark-layout.css">
    <link rel="stylesheet" type="text/css" href="/admin/app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="/admin/app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="/admin/app-assets/vendors/css/pickers/pickadate/pickadate.css">
    <link rel="stylesheet" type="text/css" href="/admin/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css">
    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="/admin/app-assets/vendors/css/forms/wizard/bs-stepper.min.css">
    <link rel="stylesheet" type="text/css" href="/admin/app-assets/vendors/css/forms/select/select2.min.css">
    <link rel="stylesheet" type="text/css" href="/admin/app-assets/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="/admin/app-assets/css/plugins/extensions/ext-component-sliders.css">
    <link rel="stylesheet" type="text/css" href="/admin/app-assets/css/pages/app-ecommerce.css">
    <link rel="stylesheet" type="text/css" href="/admin/app-assets/css/plugins/extensions/ext-component-toastr.css">
    <link rel="stylesheet" type="text/css" href="/admin/app-assets/css/plugins/forms/form-validation.css">
    <link rel="stylesheet" type="text/css" href="/admin/app-assets/css/plugins/forms/form-wizard.css">
    <link rel="stylesheet" type="text/css" href="/admin/app-assets/css/pages/app-todo.css">
    <link rel="stylesheet" type="text/css" href="/admin/app-assets/css/plugins/extensions/ext-component-sweet-alerts.css">
    <link rel="stylesheet" type="text/css" href="/admin/app-assets/css/themes/bordered-layout.css">
    <link rel="stylesheet" href="/assets/assets/vendor/libs/typeahead-js/typeahead.css" />
       <link rel="stylesheet" href="/assets/assets/vendor/libs/quill/typography.css" />
    <link rel="stylesheet" href="/assets/assets/vendor/libs/quill/katex.css" />
    <link rel="stylesheet" href="/assets/assets/vendor/libs/quill/editor.css" />
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

    <link rel="stylesheet" href="/assets/assets/vendor/libs/bs-stepper/bs-stepper.css" />

    <!-- CKEditor 5 - Éditeur de texte riche gratuit -->
    <script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>

    <!-- Font Awesome 6.4.0 CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- FullCalendar CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css' rel='stylesheet' />

  <style>
    .btn-loading {
  position: relative;
  opacity: 0.8;
}

.btn-loading::after {
  content: "";
  position: absolute;
  right: 12px;
  top: 30%;
  transform: translateY(-50%);
  width: 1rem;
  height: 1rem;
  border: 2px solid white;
  border-top: 2px solid transparent;
  border-radius: 50%;
  /* animation: spin 0.6s linear infinite; */
}

@keyframes spin {
  to {
    transform: translateY(-50%) rotate(360deg);
  }
}

/* CKEditor Custom Styles */
.ck-editor__editable {
    min-height: 300px !important;
    border-radius: 0.428rem !important;
    border: 1px solid #d8d6de !important;
}

.ck.ck-editor__main>.ck-editor__editable {
    background-color: #fff !important;
    color: #6e6b7b !important;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 0.875rem !important;
}

.ck.ck-toolbar {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
    border-bottom: 1px solid #e9ecef !important;
    border-radius: 0.428rem 0.428rem 0 0 !important;
}

.ck.ck-toolbar .ck-toolbar__items {
    border-radius: 0.25rem !important;
}

.ck.ck-button {
    border-radius: 0.25rem !important;
    transition: all 0.3s ease !important;
}

.ck.ck-button:hover {
    background-color: rgba(9, 49, 98, 0.1) !important;
}

.ck.ck-button.ck-on {
    background-color: var(--primary-color) !important;
    color: #fff !important;
}

/* RTL Support for Arabic */
.ck-editor__editable[dir="rtl"] {
    text-align: right;
    direction: rtl;
}

.ck-editor__editable[dir="rtl"] .ck-toolbar {
    flex-direction: row-reverse;
}
  </style>
    <!-- END: Page CSS-->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="/admin/assets/css/style.css">
    <link rel="stylesheet" href="/assets/assets/vendor/libs/tagify/tagify.css" />
    <link rel="stylesheet" type="text/css" href="/css/vuexy-custom.css">
    <link rel="stylesheet" type="text/css" href="/css/upskills-admin.css">
    <link rel="stylesheet" type="text/css" href="/css/feather-icons-fix.css">
    <!-- END: Custom CSS-->

</head>
