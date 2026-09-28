/**
 * Certificate Editor Form - JavaScript
 * Approche simple basée sur des formulaires
 * Sauvegarde directe des coordonnées X,Y dans template_data
 */

class CertificateEditorForm {
    constructor() {
        this.certifId = null;
        this.templateData = {};
        this.previewCanvas = null;

        this.init();
    }

    init() {
        console.log('📝 Initialisation du Certificate Editor Form');

        // Récupérer l'ID du certificat
        this.certifId = this.getCertifId();

        // Initialiser les éléments DOM
        this.initElements();

        // Charger les données existantes
        this.loadTemplateData();

        // Configurer les événements
        this.setupEventListeners();

        console.log('✅ Certificate Editor Form initialisé');
    }

    getCertifId() {
        const path = window.location.pathname;
        const match = path.match(/\/certifs\/(\d+)\/edit/);
        return match ? match[1] : null;
    }

    initElements() {
        this.previewCanvas = document.getElementById('certificateCanvas');

        if (!this.previewCanvas) {
            console.error('❌ Canvas de prévisualisation non trouvé');
            return;
        }

        console.log('✅ Éléments DOM initialisés');
    }

    async loadTemplateData() {
        try {
            const response = await fetch(`/certifs/${this.certifId}/template-data`);
            const data = await response.json();

            if (data.success) {
                this.templateData = data.template_data || {};
                console.log('📊 Données template chargées:', this.templateData);
                this.populateForm();
                this.updatePreview();
            } else {
                console.warn('⚠️ Aucune donnée template trouvée, utilisation des valeurs par défaut');
                this.templateData = this.getDefaultTemplateData();
                this.populateForm();
                this.updatePreview();
            }
        } catch (error) {
            console.error('❌ Erreur lors du chargement des données:', error);
            this.templateData = this.getDefaultTemplateData();
            this.populateForm();
            this.updatePreview();
        }
    }

    getDefaultTemplateData() {
        return {
            name_student: {
                x: 100,
                y: 100,
                width: 200,
                height: 30,
                show: true,
                text: "Nom de l'Étudiant",
                font_size: 16,
                color: "#007bff",
                font_family: "Arial",
                type: "text",
                is_dynamic: true
            },
            date: {
                x: 100,
                y: 150,
                width: 150,
                height: 30,
                show: true,
                text: "Date",
                font_size: 14,
                color: "#000000",
                font_family: "Arial",
                type: "date",
                is_dynamic: true
            },
            serial_number: {
                x: 100,
                y: 200,
                width: 150,
                height: 30,
                show: true,
                text: "Numéro de Série",
                font_size: 14,
                color: "#000000",
                font_family: "Arial",
                type: "text",
                is_dynamic: true
            },
            qr_code: {
                x: 100,
                y: 250,
                width: 100,
                height: 100,
                show: true,
                text: "QR Code",
                font_size: 12,
                color: "#000000",
                font_family: "Arial",
                type: "qr",
                is_dynamic: true
            }
        };
    }

    populateForm() {
        // Remplir le formulaire avec les données existantes
        Object.keys(this.templateData).forEach(fieldKey => {
            const fieldData = this.templateData[fieldKey];

            // Remplir les champs du formulaire
            const xInput = document.getElementById(`${fieldKey}_x`);
            const yInput = document.getElementById(`${fieldKey}_y`);
            const widthInput = document.getElementById(`${fieldKey}_width`);
            const heightInput = document.getElementById(`${fieldKey}_height`);
            const textInput = document.getElementById(`${fieldKey}_text`);
            const showInput = document.getElementById(`${fieldKey}_show`);

            if (xInput) xInput.value = fieldData.x || 0;
            if (yInput) yInput.value = fieldData.y || 0;
            if (widthInput) widthInput.value = fieldData.width || 200;
            if (heightInput) heightInput.value = fieldData.height || 30;
            if (textInput) textInput.value = fieldData.text || '';
            if (showInput) showInput.checked = fieldData.show || false;
        });

        console.log('📝 Formulaire rempli avec les données existantes');
    }

    setupEventListeners() {
        // Écouter les changements dans tous les champs du formulaire
        const form = document.getElementById('fieldsForm');
        if (form) {
            form.addEventListener('input', () => {
                this.updateFormData();
            });

            form.addEventListener('change', () => {
                this.updateFormData();
            });
        }

        // Mise à jour de l'affichage de la taille de police par défaut
        const fontSizeControl = document.getElementById('defaultFontSize');
        const fontSizeValue = document.getElementById('defaultFontSizeValue');
        if (fontSizeControl && fontSizeValue) {
            fontSizeControl.addEventListener('input', () => {
                fontSizeValue.textContent = fontSizeControl.value + 'px';
            });
        }

        console.log('👂 Événements configurés');
    }

    updateFormData() {
        // Mettre à jour les données template à partir du formulaire
        const fields = ['name_student', 'date', 'serial_number', 'qr_code'];

        fields.forEach(fieldKey => {
            const xInput = document.getElementById(`${fieldKey}_x`);
            const yInput = document.getElementById(`${fieldKey}_y`);
            const widthInput = document.getElementById(`${fieldKey}_width`);
            const heightInput = document.getElementById(`${fieldKey}_height`);
            const textInput = document.getElementById(`${fieldKey}_text`);
            const showInput = document.getElementById(`${fieldKey}_show`);

            if (xInput && yInput && widthInput && heightInput && textInput && showInput) {
                this.templateData[fieldKey] = {
                    ...this.templateData[fieldKey],
                    x: parseInt(xInput.value) || 0,
                    y: parseInt(yInput.value) || 0,
                    width: parseInt(widthInput.value) || 200,
                    height: parseInt(heightInput.value) || 30,
                    text: textInput.value || '',
                    show: showInput.checked
                };
            }
        });

        console.log('📊 Données du formulaire mises à jour');
    }

    updatePreview() {
        if (!this.previewCanvas) {
            console.error('❌ Canvas de prévisualisation non trouvé');
            return;
        }

        console.log('🔄 Mise à jour de la prévisualisation...');
        console.log('📊 Données template:', this.templateData);

        // Nettoyer le canvas
        this.previewCanvas.innerHTML = '';

        // Ajouter chaque champ visible
        let fieldsCreated = 0;
        Object.keys(this.templateData).forEach(fieldKey => {
            const fieldData = this.templateData[fieldKey];

            console.log(`🔍 Vérification du champ ${fieldKey}:`, fieldData);

            if (fieldData && fieldData.show) {
                this.createPreviewField(fieldKey, fieldData);
                fieldsCreated++;
            } else {
                console.log(`⏭️ Champ ${fieldKey} ignoré (show: ${fieldData?.show})`);
            }
        });

        console.log(`✅ Prévisualisation mise à jour - ${fieldsCreated} champs créés`);

        // Vérifier que les champs sont bien dans le DOM
        const createdFields = this.previewCanvas.querySelectorAll('.preview-field');
        console.log(`🎯 Champs dans le DOM: ${createdFields.length}`);
    }

    createPreviewField(fieldKey, fieldData) {
        const field = document.createElement('div');
        field.className = 'preview-field';
        field.dataset.field = fieldKey;

        // Appliquer les styles directement avec des couleurs différentes selon le type
        const colors = {
            name_student: { bg: 'rgba(0, 123, 255, 0.3)', border: '#007bff' },
            date: { bg: 'rgba(40, 167, 69, 0.3)', border: '#28a745' },
            serial_number: { bg: 'rgba(255, 193, 7, 0.3)', border: '#ffc107' },
            qr_code: { bg: 'rgba(220, 53, 69, 0.3)', border: '#dc3545' }
        };

        const fieldColor = colors[fieldKey] || { bg: 'rgba(0, 123, 255, 0.3)', border: '#007bff' };

        field.style.cssText = `
            position: absolute !important;
            left: ${fieldData.x}px !important;
            top: ${fieldData.y}px !important;
            width: ${fieldData.width}px !important;
            height: ${fieldData.height}px !important;
            font-size: ${fieldData.font_size}px !important;
            color: ${fieldData.color} !important;
            font-family: ${fieldData.font_family} !important;
            font-weight: ${fieldData.font_weight || 'normal'} !important;
            font-style: ${fieldData.font_style || 'normal'} !important;
            text-decoration: ${fieldData.text_decoration || 'none'} !important;
            background: ${fieldColor.bg} !important;
            border: 2px solid ${fieldColor.border} !important;
            border-radius: 6px !important;
            padding: 8px !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            align-items: center !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
            z-index: 10 !important;
            min-width: 50px !important;
            min-height: 20px !important;
            pointer-events: none !important;
        `;

        // Contenu du champ
        field.innerHTML = `
            <div class="field-content" style="font-weight: bold; text-align: center; flex: 1; display: flex; align-items: center; justify-content: center;">${fieldData.text}</div>
            <div class="field-coordinates" style="font-size: 10px; opacity: 0.7; margin-top: 2px; font-family: monospace;">X: ${fieldData.x}, Y: ${fieldData.y}</div>
        `;

        // Ajouter au canvas
        this.previewCanvas.appendChild(field);

        console.log('🎨 Champ créé:', fieldKey, 'à la position:', fieldData.x, fieldData.y, 'avec couleur:', fieldColor.border);
    }

    async saveFields() {
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const response = await fetch(`/certifs/${this.certifId}/template-data`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    template_data: this.templateData
                })
            });

            const result = await response.json();

            if (response.ok && result.success) {
                this.showNotification('✅ Positions sauvegardées avec succès !', 'success');
                console.log('💾 Données sauvegardées:', this.templateData);
            } else {
                console.error('❌ Erreur lors de la sauvegarde:', result.message);
                this.showNotification('❌ Erreur de sauvegarde: ' + result.message, 'error');
            }
        } catch (error) {
            console.error('❌ Erreur lors de la sauvegarde:', error);
            this.showNotification('❌ Erreur de connexion lors de la sauvegarde', 'error');
        }
    }

    resetFields() {
        if (confirm('Êtes-vous sûr de vouloir remettre tous les champs à zéro ?')) {
            // Remettre les valeurs par défaut
            this.templateData = this.getDefaultTemplateData();
            this.populateForm();
            this.updatePreview();
            this.showNotification('🔄 Champs remis à zéro', 'info');
        }
    }

    createTestFields() {
        // Créer des champs de test avec des positions visibles
        this.templateData = {
            name_student: {
                x: 50,
                y: 50,
                width: 200,
                height: 40,
                show: true,
                text: "Nom de l'Étudiant",
                font_size: 16,
                color: "#007bff",
                font_family: "Arial",
                type: "text",
                is_dynamic: true
            },
            date: {
                x: 50,
                y: 120,
                width: 150,
                height: 30,
                show: true,
                text: "Date",
                font_size: 14,
                color: "#28a745",
                font_family: "Arial",
                type: "date",
                is_dynamic: true
            },
            serial_number: {
                x: 50,
                y: 180,
                width: 150,
                height: 30,
                show: true,
                text: "Numéro de Série",
                font_size: 14,
                color: "#ffc107",
                font_family: "Arial",
                type: "text",
                is_dynamic: true
            },
            qr_code: {
                x: 50,
                y: 240,
                width: 100,
                height: 100,
                show: true,
                text: "QR Code",
                font_size: 12,
                color: "#dc3545",
                font_family: "Arial",
                type: "qr",
                is_dynamic: true
            }
        };

        this.populateForm();
        this.updatePreview();
        this.showNotification('🧪 Champs de test créés', 'success');
    }

    applyDefaultStyle() {
        const fontFamily = document.getElementById('defaultFontFamily').value;
        const fontSize = document.getElementById('defaultFontSize').value;
        const color = document.getElementById('defaultColor').value;
        const bold = document.getElementById('defaultBold').checked;
        const italic = document.getElementById('defaultItalic').checked;
        const underline = document.getElementById('defaultUnderline').checked;

        // Appliquer le style par défaut à tous les champs
        Object.keys(this.templateData).forEach(fieldKey => {
            this.templateData[fieldKey].font_family = fontFamily;
            this.templateData[fieldKey].font_size = parseInt(fontSize);
            this.templateData[fieldKey].color = color;
            this.templateData[fieldKey].font_weight = bold ? 'bold' : 'normal';
            this.templateData[fieldKey].font_style = italic ? 'italic' : 'normal';
            this.templateData[fieldKey].text_decoration = underline ? 'underline' : 'none';
        });

        this.populateForm();
        this.updatePreview();
        this.showNotification('🎨 Style par défaut appliqué à tous les champs', 'success');
    }

    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'error' ? 'danger' : type === 'success' ? 'success' : 'info'} alert-dismissible fade show position-fixed`;
        notification.style.cssText = `
            top: 20px;
            right: 20px;
            z-index: 2000;
            min-width: 300px;
        `;
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(notification);

        // Supprimer automatiquement après 5 secondes
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
    }

    // Méthode publique pour obtenir les données template
    getTemplateData() {
        return this.templateData;
    }

    // Méthode publique pour obtenir les données au format JSON
    getTemplateDataJSON() {
        return JSON.stringify(this.templateData, null, 2);
    }

    // Méthode publique pour afficher les données dans la console
    logTemplateData() {
        console.log('📊 Données Template Actuelles:');
        console.log(this.getTemplateDataJSON());
        return this.templateData;
    }
}

// Les fonctions globales sont maintenant définies plus bas dans le fichier

// Fonctions globales pour les boutons - DOIVENT être définies AVANT l'initialisation
window.updatePreview = function() {
    if (window.certificateEditorForm) {
        window.certificateEditorForm.updateFormData();
        window.certificateEditorForm.updatePreview();
    } else {
        console.error('❌ certificateEditorForm non initialisé');
    }
};

window.saveFields = function() {
    if (window.certificateEditorForm) {
        window.certificateEditorForm.updateFormData();
        window.certificateEditorForm.saveFields();
    } else {
        console.error('❌ certificateEditorForm non initialisé');
    }
};

window.resetFields = function() {
    if (window.certificateEditorForm) {
        window.certificateEditorForm.resetFields();
    } else {
        console.error('❌ certificateEditorForm non initialisé');
    }
};

window.applyDefaultStyle = function() {
    if (window.certificateEditorForm) {
        window.certificateEditorForm.applyDefaultStyle();
    } else {
        console.error('❌ certificateEditorForm non initialisé');
    }
};

window.debugFields = function() {
    if (window.certificateEditorForm) {
        console.log('🐛 DEBUG - Forçage de l\'affichage des champs');

        // Forcer la mise à jour des données du formulaire
        window.certificateEditorForm.updateFormData();

        // Afficher les données dans la console
        console.log('📊 Données template actuelles:', window.certificateEditorForm.getTemplateData());

        // Forcer l'affichage de tous les champs
        const fields = ['name_student', 'date', 'serial_number', 'qr_code'];
        fields.forEach(fieldKey => {
            if (window.certificateEditorForm.templateData[fieldKey]) {
                window.certificateEditorForm.templateData[fieldKey].show = true;
            }
        });

        // Mettre à jour la prévisualisation
        window.certificateEditorForm.updatePreview();

        // Vérifier le canvas
        const canvas = document.getElementById('certificateCanvas');
        if (canvas) {
            console.log('🎯 Canvas trouvé:', canvas);
            console.log('📏 Dimensions du canvas:', canvas.offsetWidth, 'x', canvas.offsetHeight);
            console.log('🎨 Champs dans le canvas:', canvas.querySelectorAll('.preview-field').length);
        } else {
            console.error('❌ Canvas non trouvé !');
        }

        window.certificateEditorForm.showNotification('🐛 Debug terminé - Vérifiez la console', 'info');
    } else {
        console.error('❌ certificateEditorForm non initialisé');
    }
};

window.createTestFields = function() {
    if (window.certificateEditorForm) {
        window.certificateEditorForm.createTestFields();
    } else {
        console.error('❌ certificateEditorForm non initialisé');
    }
};

window.copyTemplateData = function() {
    if (window.certificateEditorForm) {
        const jsonData = window.certificateEditorForm.getTemplateDataJSON();
        navigator.clipboard.writeText(jsonData).then(() => {
            window.certificateEditorForm.showNotification('📋 JSON copié dans le presse-papiers !', 'success');
        }).catch(err => {
            console.error('Erreur lors de la copie:', err);
            window.certificateEditorForm.showNotification('❌ Erreur lors de la copie', 'error');
        });
    } else {
        console.error('❌ certificateEditorForm non initialisé');
    }
};

// Fonction de test simple
window.testCertificateEditor = function() {
    console.log('🧪 Test du Certificate Editor...');

    // Vérifier que les fonctions sont définies
    const functions = ['updatePreview', 'saveFields', 'resetFields', 'debugFields', 'createTestFields'];
    functions.forEach(func => {
        if (typeof window[func] === 'function') {
            console.log(`✅ ${func} est définie`);
        } else {
            console.error(`❌ ${func} n'est pas définie`);
        }
    });

    // Vérifier le canvas
    const canvas = document.getElementById('certificateCanvas');
    if (canvas) {
        console.log('✅ Canvas trouvé:', canvas);
    } else {
        console.error('❌ Canvas non trouvé');
    }

    // Vérifier l'instance
    if (window.certificateEditorForm) {
        console.log('✅ Instance certificateEditorForm trouvée');
    } else {
        console.error('❌ Instance certificateEditorForm non trouvée');
    }

    console.log('🧪 Test terminé');
};

// Initialisation automatique
document.addEventListener('DOMContentLoaded', () => {
    console.log('🚀 Initialisation du Certificate Editor Form...');
    try {
        window.certificateEditorForm = new CertificateEditorForm();
        console.log('✅ Certificate Editor Form initialisé avec succès');

        // Test automatique après 1 seconde
        setTimeout(() => {
            window.testCertificateEditor();
        }, 1000);

    } catch (error) {
        console.error('❌ Erreur lors de l\'initialisation:', error);
    }
});

// Export pour utilisation externe
if (typeof module !== 'undefined' && module.exports) {
    module.exports = CertificateEditorForm;
}