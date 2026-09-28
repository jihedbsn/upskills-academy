
<div class="sidenav-overlay"></div>
<div class="drag-target"></div>
<style>
    .cke_notifications_area{
        display:none
    }
</style>
<!-- BEGIN: Footer-->
<footer class="footer footer-static footer-light">
    <p class="clearfix mb-0"><span class="float-md-start d-block d-md-inline-block mt-25">COPYRIGHT &copy; 2025<a class="ms-25">Upskills Academy</a><span class="d-none d-sm-inline-block">, All rights Reserved</span></span></p>
</footer>
<button class="btn btn-primary btn-icon scroll-top" type="button"><i data-feather="arrow-up"></i></button>
<!-- END: Footer-->

<!-- BEGIN: Vendor JS-->
<script src="/admin/app-assets/vendors/js/vendors.min.js"></script>
<!-- BEGIN Vendor JS-->
<script src="/admin/app-assets/vendors/js/pickers/pickadate/picker.js"></script>
<script src="/admin/app-assets/vendors/js/pickers/pickadate/picker.date.js"></script>
<script src="/admin/app-assets/vendors/js/pickers/pickadate/picker.time.js"></script>
<script src="/admin/app-assets/vendors/js/pickers/pickadate/legacy.js"></script>
<script src="/admin/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js"></script>
<script src="/admin/app-assets/vendors/js/editors/quill/katex.min.js"></script>
<script src="/admin/app-assets/vendors/js/editors/quill/highlight.min.js"></script>

<!-- BEGIN: Page Vendor JS-->
<script src="/admin/app-assets/vendors/js/extensions/wNumb.min.js"></script>
<script src="/admin/app-assets/vendors/js/extensions/nouislider.min.js"></script>
<script src="/admin/app-assets/vendors/js/extensions/toastr.min.js"></script>
<script src="/admin/app-assets/vendors/js/editors/quill/highlight.min.js"></script>

<!-- END: Page Vendor JS-->
<script src="/admin/app-assets/vendors/js/forms/select/select2.full.min.js"></script>
<!-- BEGIN: Theme JS-->
<script src="/admin/app-assets/js/core/app-menu.js"></script>
<script src="/admin/app-assets/js/core/app.js"></script>
<script src="/admin/app-assets/js/scripts/forms/form-wizard.js"></script>
<!-- END: Theme JS-->
<script src="/admin/app-assets/vendors/js/forms/wizard/bs-stepper.min.js"></script>
<script src="/admin/app-assets/js/scripts/forms/form-select2.js"></script>
    <script src="/assets/assets/vendor/libs/quill/katex.js"></script>

    <script src="/assets/assets/vendor/libs/quill/quill.js"></script>

<!-- BEGIN: Page JS-->
<script src="/admin/app-assets/js/scripts/pages/app-ecommerce.js"></script>
<script src="/admin/app-assets/js/scripts/forms/pickers/form-pickers.js"></script>
<script src="/admin/app-assets/vendors/js/extensions/sweetalert2.all.min.js"></script>
<script src="/admin/app-assets/js/scripts/extensions/ext-component-sweet-alerts.js"></script>
<script src="/admin/app-assets/js/scripts/components/components-popovers.js"></script>
    <script src="/assets/assets/vendor/libs/dropzone/dropzone.js"></script>


<script src="/admin/assets/anis.js"></script>
<!-- END: Page JS-->

<!-- Le gestionnaire des icônes Feather est maintenant géré par feather-icons-manager.js -->
<script>
    // Écouteur pour tous les formulaires - animations désactivées
    document.querySelectorAll('form').forEach(function(form) {
        form.addEventListener('submit', function() {
            // Indicateur de chargement désactivé pour améliorer les performances
            // document.getElementById('loading').style.display = 'block';

            // Optionnel : Désactiver les boutons pour empêcher plusieurs soumissions
            form.querySelectorAll('button').forEach(function(button) {
                button.disabled = true;
            });
        });
    });
</script>

<script>
 document.addEventListener('DOMContentLoaded', function () {
    const forms = document.querySelectorAll('form');

    forms.forEach(form => {
      form.addEventListener('submit', function (e) {
        // Trouver le bouton submit actif (peut varier selon navigateur)
        const submitButton = form.querySelector('button[type="submit"]:not([disabled])');

        if (submitButton) {
          const originalText = submitButton.innerHTML;
          submitButton.dataset.originalText = originalText;
          submitButton.innerHTML = `${originalText}...`;
          // submitButton.classList.add('btn-loading');
          submitButton.disabled = true;
        }
      });
    });
  });
</script>
 <script src="/assets/assets/vendor/libs/tagify/tagify.js"></script>
 <script src="/js/vuexy-custom.js"></script>

<script>
// Initialize Feather Icons - Simple and clean
$(document).ready(function() {
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>

@stack('scripts')
