/**
 * VUEXY CUSTOM JAVASCRIPT
 * Améliorations de l'expérience utilisateur
 */

(function() {
    'use strict';

    // ===================================
    // INITIALIZATION
    // ===================================

    document.addEventListener('DOMContentLoaded', function() {
        initializeAnimations();
        initializeTooltips();
        initializeLoadingStates();
        initializeFormEnhancements();
        initializeTableEnhancements();
        initializeCardInteractions();
    });

    // ===================================
    // ANIMATIONS - DÉSACTIVÉES
    // ===================================

    function initializeAnimations() {
        // Toutes les animations ont été désactivées pour améliorer les performances
        console.log('Animations désactivées pour améliorer les performances');
    }

    function animateCounters() {
        // Animation des compteurs désactivée
        console.log('Animation des compteurs désactivée');
    }

    // ===================================
    // TOOLTIPS
    // ===================================

    function initializeTooltips() {
        // Initialiser les tooltips Bootstrap
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Tooltips personnalisés pour les éléments sans data-bs-toggle
        document.querySelectorAll('[title]').forEach(element => {
            if (!element.hasAttribute('data-bs-toggle')) {
                element.setAttribute('data-bs-toggle', 'tooltip');
                new bootstrap.Tooltip(element);
            }
        });
    }

    // ===================================
    // LOADING STATES
    // ===================================

    function initializeLoadingStates() {
        // Gestionnaire pour les boutons avec état de chargement
        document.addEventListener('click', function(e) {
            if (e.target.matches('.btn-loading') || e.target.closest('.btn-loading')) {
                const button = e.target.matches('.btn-loading') ? e.target : e.target.closest('.btn-loading');
                showButtonLoading(button);
            }
        });

        // Gestionnaire pour les formulaires
        document.addEventListener('submit', function(e) {
            if (e.target.matches('form')) {
                const submitButton = e.target.querySelector('button[type="submit"]');
                if (submitButton) {
                    showButtonLoading(submitButton);
                }
            }
        });
    }

    function showButtonLoading(button) {
        const originalText = button.innerHTML;
        button.classList.add('btn-loading');
        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Chargement...';

        // Restaurer après 3 secondes (ou après la réponse AJAX)
        setTimeout(() => {
            button.classList.remove('btn-loading');
            button.disabled = false;
            button.innerHTML = originalText;
        }, 3000);
    }

    // ===================================
    // FORM ENHANCEMENTS
    // ===================================

    function initializeFormEnhancements() {
        // Validation en temps réel
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('blur', validateField);
            input.addEventListener('input', clearFieldError);
        });

        // Amélioration des selects
        document.querySelectorAll('select').forEach(select => {
            select.classList.add('form-select');
        });

        // Amélioration des textareas
        document.querySelectorAll('textarea').forEach(textarea => {
            textarea.addEventListener('input', autoResize);
        });
    }

    function validateField(e) {
        const field = e.target;
        const value = field.value.trim();

        // Supprimer les classes d'erreur précédentes
        field.classList.remove('is-invalid', 'is-valid');

        // Validation basique
        if (field.hasAttribute('required') && !value) {
            field.classList.add('is-invalid');
            showFieldError(field, 'Ce champ est requis');
        } else if (field.type === 'email' && value && !isValidEmail(value)) {
            field.classList.add('is-invalid');
            showFieldError(field, 'Email invalide');
        } else if (value) {
            field.classList.add('is-valid');
        }
    }

    function clearFieldError(e) {
        const field = e.target;
        field.classList.remove('is-invalid');
        const errorElement = field.parentNode.querySelector('.invalid-feedback');
        if (errorElement) {
            errorElement.remove();
        }
    }

    function showFieldError(field, message) {
        // Supprimer les messages d'erreur existants
        const existingError = field.parentNode.querySelector('.invalid-feedback');
        if (existingError) {
            existingError.remove();
        }

        // Créer le message d'erreur
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = message;
        field.parentNode.appendChild(errorDiv);
    }

    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    function autoResize(e) {
        const textarea = e.target;
        textarea.style.height = 'auto';
        textarea.style.height = textarea.scrollHeight + 'px';
    }

    // ===================================
    // TABLE ENHANCEMENTS
    // ===================================

    function initializeTableEnhancements() {
        // Amélioration des tableaux - animations désactivées
        document.querySelectorAll('.table').forEach(table => {
            // Effets hover désactivés pour améliorer les performances

            // Amélioration de la pagination
            const pagination = table.parentNode.querySelector('.pagination');
            if (pagination) {
                enhancePagination(pagination);
            }
        });
    }

    function enhancePagination(pagination) {
        const links = pagination.querySelectorAll('.page-link');
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                // Ajouter un effet de ripple
                createRippleEffect(this, e);
            });
        });
    }

    // ===================================
    // CARD INTERACTIONS
    // ===================================

    function initializeCardInteractions() {
        // Effets de hover sur les cartes désactivés pour améliorer les performances
        document.querySelectorAll('.card').forEach(card => {
            // Animations désactivées
        });

        // Effet de clic sur les cartes cliquables désactivé
        document.querySelectorAll('.card[onclick], .card a').forEach(card => {
            // Effet ripple désactivé
        });
    }

    // ===================================
    // UTILITY FUNCTIONS
    // ===================================

    function createRippleEffect(element, event) {
        const ripple = document.createElement('span');
        const rect = element.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = event.clientX - rect.left - size / 2;
        const y = event.clientY - rect.top - size / 2;

        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';
        ripple.classList.add('ripple');

        element.style.position = 'relative';
        element.style.overflow = 'hidden';
        element.appendChild(ripple);

        setTimeout(() => {
            ripple.remove();
        }, 600);
    }

    // ===================================
    // NOTIFICATIONS
    // ===================================

    function showNotification(message, type = 'success', duration = 3000) {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} notification-toast`;
        notification.innerHTML = `
            <div class="d-flex align-items-center">
                <i data-feather="${type === 'success' ? 'check-circle' : type === 'error' ? 'alert-circle' : 'info'}" class="me-2"></i>
                <span>${message}</span>
            </div>
        `;

        // Styles pour la notification
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            animation: slideInRight 0.3s ease-out;
        `;

        document.body.appendChild(notification);

        // Supprimer après la durée spécifiée
        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.3s ease-out';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, duration);
    }

    // ===================================
    // GLOBAL FUNCTIONS
    // ===================================

    // Exposer les fonctions globalement
    window.VuexyCustom = {
        showNotification,
        showButtonLoading,
        createRippleEffect
    };

    // ===================================
    // CSS ANIMATIONS
    // ===================================

    // Styles CSS - animations désactivées
    const style = document.createElement('style');
    style.textContent = `
        .animate-in {
            /* Animation désactivée */
        }

        .ripple {
            /* Effet ripple désactivé */
        }

        .notification-toast {
            border: none;
            border-radius: 0.428rem;
            box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.24);
        }

        .navigation-main .nav-item {
            /* Transition désactivée */
        }
    `;
    document.head.appendChild(style);

})();