/**
 * Custom Form JavaScript
 * Fonctionnalités communes pour tous les formulaires
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Custom Form JS initialisé');

    // ===== FONCTIONS UTILITAIRES =====

    /**
     * Génère un slug à partir d'un texte
     */
    function generateSlug(text) {
        return text
            .toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim('-');
    }

    /**
     * Affiche une notification
     */
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(notification);

        // Auto-remove after 3 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 3000);
    }

    /**
     * Valide un formulaire
     */
    function validateForm(form) {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });

        return isValid;
    }

    // ===== GESTION DES ONGLETS =====

    const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
    if (tabButtons.length > 0) {
        console.log('📑 Onglets détectés:', tabButtons.length);

        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-bs-target');
                const target = document.querySelector(targetId);

                if (target) {
                    // Désactiver tous les onglets
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    document.querySelectorAll('.tab-pane').forEach(pane => {
                        pane.classList.remove('show', 'active');
                    });

                    // Activer l'onglet cliqué
                    this.classList.add('active');
                    target.classList.add('show', 'active');

                    console.log('✅ Onglet activé:', targetId);
                }
            });
        });
    }

    // ===== GÉNÉRATION AUTOMATIQUE DES SLUGS =====

    const nameInputs = document.querySelectorAll('input[name*="name"]');
    nameInputs.forEach(input => {
        const slugInput = document.querySelector(`input[name="${input.name.replace('name', 'slug')}"]`);
        if (slugInput) {
            input.addEventListener('input', function() {
                slugInput.value = generateSlug(this.value);
            });
        }
    });

    // ===== VALIDATION EN TEMPS RÉEL =====

    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        // Validation à la soumission
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
                showNotification('Veuillez remplir tous les champs obligatoires.', 'danger');
            }
        });

        // Validation en temps réel
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                if (this.hasAttribute('required') && this.value.trim()) {
                    this.classList.remove('is-invalid');
                }
            });

            input.addEventListener('blur', function() {
                if (this.hasAttribute('required') && !this.value.trim()) {
                    this.classList.add('is-invalid');
                }
            });
        });
    });

    // ===== GESTION DES BOUTONS DE SUPPRESSION =====

    const removeButtons = document.querySelectorAll('.remove-student, .remove-question, .remove-item');
    removeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const item = this.closest('.student-row, .question-row, .item-row');
            if (item) {
                item.remove();
                showNotification('Élément supprimé avec succès.', 'success');
            }
        });
    });

    // ===== GESTION DES BOUTONS D'AJOUT =====

    const addButtons = document.querySelectorAll('#addStudentBtn, #addQuestionBtn, .add-item-btn');
    addButtons.forEach(button => {
        button.addEventListener('click', function() {
            console.log('➕ Bouton d\'ajout cliqué:', this.id);
            // La logique spécifique sera gérée dans chaque formulaire
        });
    });

    // ===== GESTION DES RECHERCHES =====

    const searchInputs = document.querySelectorAll('input[type="search"], .search-input');
    searchInputs.forEach(input => {
        input.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const items = document.querySelectorAll(this.dataset.target || '.searchable-item');

            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });

    // ===== GESTION DES CHECKBOXES =====

    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const target = document.querySelector(this.dataset.target);
            if (target) {
                if (this.checked) {
                    target.style.display = 'block';
                } else {
                    target.style.display = 'none';
                }
            }
        });
    });

    // ===== GESTION DES SELECTS MULTIPLES =====

    const multiSelects = document.querySelectorAll('select[multiple]');
    multiSelects.forEach(select => {
        select.addEventListener('change', function() {
            const selectedOptions = Array.from(this.selectedOptions).map(option => option.text);
            console.log('Options sélectionnées:', selectedOptions);
        });
    });

    // ===== GESTION DES FICHIERS =====

    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const maxSize = this.dataset.maxSize || 5242880; // 5MB par défaut
                if (file.size > maxSize) {
                    showNotification('Le fichier est trop volumineux.', 'danger');
                    this.value = '';
                } else {
                    showNotification(`Fichier sélectionné: ${file.name}`, 'success');
                }
            }
        });
    });

    // ===== GESTION DES DATES =====

    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(input => {
        // Définir la date minimale à aujourd'hui pour les dates futures
        if (input.dataset.minToday) {
            const today = new Date().toISOString().split('T')[0];
            input.setAttribute('min', today);
        }

        // Définir la date maximale à aujourd'hui pour les dates passées
        if (input.dataset.maxToday) {
            const today = new Date().toISOString().split('T')[0];
            input.setAttribute('max', today);
        }
    });

    // ===== GESTION DES NOMBRES =====

    const numberInputs = document.querySelectorAll('input[type="number"]');
    numberInputs.forEach(input => {
        input.addEventListener('input', function() {
            const min = parseFloat(this.min);
            const max = parseFloat(this.max);
            const value = parseFloat(this.value);

            if (!isNaN(min) && value < min) {
                this.value = min;
            }
            if (!isNaN(max) && value > max) {
                this.value = max;
            }
        });
    });

    // ===== GESTION DES TOOLTIPS =====

    const tooltipElements = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    if (tooltipElements.length > 0 && typeof bootstrap !== 'undefined') {
        tooltipElements.forEach(element => {
            new bootstrap.Tooltip(element);
        });
    }

    // ===== GESTION DES POPOVERS =====

    const popoverElements = document.querySelectorAll('[data-bs-toggle="popover"]');
    if (popoverElements.length > 0 && typeof bootstrap !== 'undefined') {
        popoverElements.forEach(element => {
            new bootstrap.Popover(element);
        });
    }

    // ===== GESTION DES MODALES =====

    const modalTriggers = document.querySelectorAll('[data-bs-toggle="modal"]');
    modalTriggers.forEach(trigger => {
        trigger.addEventListener('click', function() {
            const targetId = this.getAttribute('data-bs-target');
            const modal = document.querySelector(targetId);
            if (modal && typeof bootstrap !== 'undefined') {
                const modalInstance = new bootstrap.Modal(modal);
                modalInstance.show();
            }
        });
    });

    console.log('✅ Custom Form JS initialisé avec succès');
});

// ===== FONCTIONS GLOBALES =====

/**
 * Fonction globale pour afficher des notifications
 */
window.showNotification = function(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 3000);
};

/**
 * Fonction globale pour valider un formulaire
 */
window.validateForm = function(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;

    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });

    return isValid;
};

/**
 * Fonction globale pour générer un slug
 */
window.generateSlug = function(text) {
    return text
        .toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim('-');
};