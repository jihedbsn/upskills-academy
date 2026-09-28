/**
 * Système de Sauvegarde Automatique et Validation en Temps Réel
 * Pour l'Éditeur de Certificats
 */

class CertificateAutoSave {
    constructor(editor) {
        this.editor = editor;
        this.autoSaveInterval = 30000; // 30 secondes
        this.lastSave = null;
        this.hasUnsavedChanges = false;
        this.validationRules = this.setupValidationRules();
        this.setupAutoSave();
        this.setupValidation();
    }

    setupValidationRules() {
        return {
            positions: {
                name: 'Positions des champs',
                validate: () => this.validatePositions()
            },
            styles: {
                name: 'Styles des champs',
                validate: () => this.validateStyles()
            },
            template: {
                name: 'Structure du template',
                validate: () => this.validateTemplate()
            }
        };
    }

    setupAutoSave() {
        // Sauvegarde automatique toutes les 30 secondes
        setInterval(() => {
            if (this.hasUnsavedChanges) {
                this.autoSave();
            }
        }, this.autoSaveInterval);

        // Sauvegarde avant de quitter la page
        window.addEventListener('beforeunload', (e) => {
            if (this.hasUnsavedChanges) {
                e.preventDefault();
                e.returnValue = 'Vous avez des modifications non sauvegardées. Êtes-vous sûr de vouloir quitter ?';
            }
        });

        // Détecter les changements
        this.setupChangeDetection();
    }

    setupChangeDetection() {
        // Observer les changements dans les champs
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'attributes' &&
                    (mutation.attributeName === 'style' || mutation.attributeName === 'class')) {
                    this.markAsChanged();
                }
            });
        });

        // Observer tous les champs de certificat
        document.querySelectorAll('.certificate-field').forEach(field => {
            observer.observe(field, {
                attributes: true,
                attributeFilter: ['style', 'class']
            });
        });

        // Observer les changements dans les contrôles
        document.querySelectorAll('#fontFamily, #fontSize, #textColor').forEach(control => {
            control.addEventListener('change', () => this.markAsChanged());
        });
    }

    setupValidation() {
        // Validation en temps réel
        setInterval(() => {
            this.validateAll();
        }, 5000); // Toutes les 5 secondes
    }

    markAsChanged() {
        this.hasUnsavedChanges = true;
        this.updateSaveIndicator();
    }

    updateSaveIndicator() {
        const saveButton = document.getElementById('saveTemplate');
        if (saveButton) {
            if (this.hasUnsavedChanges) {
                saveButton.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Modifications non sauvegardées';
                saveButton.className = 'btn btn-warning';
            } else {
                saveButton.innerHTML = '<i class="fas fa-save me-2"></i>Sauvegarder';
                saveButton.className = 'btn btn-success';
            }
        }
    }

    async autoSave() {
        try {
            // Mettre à jour toutes les données avant de sauvegarder
            document.querySelectorAll('.certificate-field').forEach(field => {
                this.editor.config.selectedField = field;
                this.editor.updateFieldData();
            });

            const response = await fetch(window.saveUrl, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    template_data: this.editor.config.templateData
                })
            });

            const result = await response.json();

            if (result.success) {
                this.hasUnsavedChanges = false;
                this.lastSave = new Date();
                this.updateSaveIndicator();
                this.showAutoSaveNotification();
            }
        } catch (error) {
            console.error('Erreur lors de la sauvegarde automatique:', error);
        }
    }

    showAutoSaveNotification() {
        // Notification discrète de sauvegarde automatique
        const notification = document.createElement('div');
        notification.className = 'auto-save-notification';
        notification.innerHTML = `
            <i class="fas fa-save me-2"></i>
            Sauvegardé automatiquement
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.classList.add('show');
        }, 100);

        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 2000);
    }

    validateAll() {
        const results = [];

        Object.entries(this.validationRules).forEach(([key, rule]) => {
            try {
                const result = rule.validate();
                results.push({
                    rule: key,
                    name: rule.name,
                    valid: result.valid,
                    message: result.message,
                    details: result.details
                });
            } catch (error) {
                results.push({
                    rule: key,
                    name: rule.name,
                    valid: false,
                    message: 'Erreur de validation',
                    details: error.message
                });
            }
        });

        this.updateValidationStatus(results);
        return results;
    }

    validatePositions() {
        const fields = document.querySelectorAll('.certificate-field');
        const issues = [];

        fields.forEach(field => {
            const rect = field.getBoundingClientRect();
            const canvasRect = this.editor.canvas.getBoundingClientRect();

            const x = rect.left - canvasRect.left;
            const y = rect.top - canvasRect.top;

            // Vérifier les limites
            if (x < 0 || y < 0) {
                issues.push(`${field.dataset.field}: Position négative`);
            }

            if (x + rect.width > this.editor.config.imageWidth) {
                issues.push(`${field.dataset.field}: Déborde à droite`);
            }

            if (y + rect.height > this.editor.config.imageHeight) {
                issues.push(`${field.dataset.field}: Déborde en bas`);
            }
        });

        return {
            valid: issues.length === 0,
            message: issues.length === 0 ? 'Toutes les positions sont valides' : `${issues.length} problème(s) de position`,
            details: issues
        };
    }

    validateStyles() {
        const fields = document.querySelectorAll('.certificate-field');
        const issues = [];

        fields.forEach(field => {
            const styles = {
                fontSize: field.style.fontSize,
                color: field.style.color,
                fontFamily: field.style.fontFamily
            };

            if (!styles.fontSize || parseInt(styles.fontSize) <= 0) {
                issues.push(`${field.dataset.field}: Taille de police invalide`);
            }

            if (!styles.color || styles.color === '') {
                issues.push(`${field.dataset.field}: Couleur manquante`);
            }

            if (!styles.fontFamily || styles.fontFamily === '') {
                issues.push(`${field.dataset.field}: Police manquante`);
            }
        });

        return {
            valid: issues.length === 0,
            message: issues.length === 0 ? 'Tous les styles sont valides' : `${issues.length} problème(s) de style`,
            details: issues
        };
    }

    validateTemplate() {
        const requiredFields = ['name_student', 'date', 'qr_code', 'serial_number'];
        const missingFields = [];

        requiredFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (!field || field.style.display === 'none') {
                missingFields.push(fieldId);
            }
        });

        return {
            valid: missingFields.length === 0,
            message: missingFields.length === 0 ? 'Template complet' : `${missingFields.length} champ(s) manquant(s)`,
            details: missingFields
        };
    }

    updateValidationStatus(results) {
        // Mettre à jour l'indicateur de validation
        const validationIndicator = this.getOrCreateValidationIndicator();

        const validCount = results.filter(r => r.valid).length;
        const totalCount = results.length;

        validationIndicator.innerHTML = `
            <i class="fas fa-${validCount === totalCount ? 'check-circle text-success' : 'exclamation-triangle text-warning'} me-2"></i>
            Validation: ${validCount}/${totalCount}
        `;

        // Afficher les détails si nécessaire
        if (validCount < totalCount) {
            this.showValidationDetails(results);
        }
    }

    getOrCreateValidationIndicator() {
        let indicator = document.getElementById('validation-indicator');
        if (!indicator) {
            indicator = document.createElement('div');
            indicator.id = 'validation-indicator';
            indicator.className = 'validation-indicator';

            const header = document.querySelector('.card-header .d-flex');
            if (header) {
                header.appendChild(indicator);
            }
        }
        return indicator;
    }

    showValidationDetails(results) {
            // Créer un tooltip ou une popup avec les détails de validation
            const invalidResults = results.filter(r => !r.valid);

            if (invalidResults.length > 0) {
                const details = invalidResults.map(r =>
                        `<div class="validation-issue">
                    <strong>${r.name}:</strong> ${r.message}
                    ${r.details ? `<br><small class="text-muted">${r.details.join(', ')}</small>` : ''}
                </div>`
            ).join('');
            
            // Afficher dans une notification ou un modal
            this.showValidationModal(details);
        }
    }

    showValidationModal(details) {
        // Créer un modal de validation si nécessaire
        let modal = document.getElementById('validation-modal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'validation-modal';
            modal.className = 'modal fade';
            modal.innerHTML = `
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Problèmes de Validation</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body" id="validation-details">
                            ${details}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        } else {
            document.getElementById('validation-details').innerHTML = details;
        }
        
        // Afficher le modal
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
    }
}

// Styles CSS pour la sauvegarde automatique
const autoSaveStyles = `
    .auto-save-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        background: #28a745;
        color: white;
        padding: 10px 15px;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        z-index: 1000;
        opacity: 0;
        transform: translateX(100%);
        transition: all 0.3s ease;
    }
    
    .auto-save-notification.show {
        opacity: 1;
        transform: translateX(0);
    }
    
    .validation-indicator {
        background: rgba(255, 255, 255, 0.1);
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 0.9em;
    }
    
    .validation-issue {
        margin-bottom: 10px;
        padding: 8px;
        background: #f8f9fa;
        border-left: 3px solid #dc3545;
        border-radius: 3px;
    }
`;

// Ajouter les styles
const styleSheet = document.createElement('style');
styleSheet.textContent = autoSaveStyles;
document.head.appendChild(styleSheet);

// Exporter la classe
window.CertificateAutoSave = CertificateAutoSave;