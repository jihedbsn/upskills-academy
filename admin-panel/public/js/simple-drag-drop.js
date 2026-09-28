/**
 * Système de Glisser-Déposer Simple et Fonctionnel
 * Pour l'Éditeur de Certificats
 */

class SimpleDragDrop {
    constructor() {
        this.isDragging = false;
        this.draggedElement = null;
        this.offset = { x: 0, y: 0 };
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.createDraggableFields();
    }

    createDraggableFields() {
        const overlay = document.getElementById('certificateOverlay');
        if (!overlay) return;

        // Nettoyer les champs existants
        overlay.innerHTML = '';

        const fields = [
            { id: 'name_student', label: 'Nom de l\'Étudiant', x: 400, y: 300 },
            { id: 'date', label: 'Date', x: 400, y: 350 },
            { id: 'qr_code', label: 'QR Code', x: 441, y: 1510, width: 100, height: 100 },
            { id: 'serial_number', label: 'Numéro de Série', x: 100, y: 1570 }
        ];

        fields.forEach(fieldData => {
            this.createField(fieldData);
        });
    }

    createField(fieldData) {
        const overlay = document.getElementById('certificateOverlay');
        if (!overlay) return;

        const field = document.createElement('div');
        field.className = 'draggable-field';
        field.id = fieldData.id;
        field.dataset.field = fieldData.id;

        // Position et taille
        field.style.left = fieldData.x + 'px';
        field.style.top = fieldData.y + 'px';
        if (fieldData.width) field.style.width = fieldData.width + 'px';
        if (fieldData.height) field.style.height = fieldData.height + 'px';

        // Contenu
        field.innerHTML = `
            <div class="position-indicator">X: ${fieldData.x}, Y: ${fieldData.y}</div>
            <div class="drag-handle">⋮⋮</div>
            <span class="field-text">${fieldData.label}</span>
        `;

        overlay.appendChild(field);
    }

    setupEventListeners() {
        document.addEventListener('mousedown', (e) => {
            const field = e.target.closest('.draggable-field');
            if (field) {
                this.startDrag(e, field);
            }
        });

        document.addEventListener('mousemove', (e) => {
            if (this.isDragging) {
                this.drag(e);
            }
        });

        document.addEventListener('mouseup', () => {
            if (this.isDragging) {
                this.stopDrag();
            }
        });
    }

    startDrag(e, field) {
        e.preventDefault();

        this.isDragging = true;
        this.draggedElement = field;

        // Sélectionner le champ
        this.selectField(field);

        // Calculer l'offset
        const rect = field.getBoundingClientRect();
        this.offset.x = e.clientX - rect.left;
        this.offset.y = e.clientY - rect.top;

        // Ajouter la classe dragging
        field.classList.add('dragging');

        // Empêcher la sélection de texte
        document.body.style.userSelect = 'none';
        document.body.style.cursor = 'grabbing';
    }

    drag(e) {
        if (!this.isDragging || !this.draggedElement) return;

        e.preventDefault();

        // Calculer la nouvelle position
        const canvas = document.getElementById('certificateCanvas');
        const canvasRect = canvas.getBoundingClientRect();

        const x = e.clientX - canvasRect.left - this.offset.x;
        const y = e.clientY - canvasRect.top - this.offset.y;

        // Permettre le positionnement libre sans limite
        const finalX = x;
        const finalY = y;

        // Appliquer la position
        this.draggedElement.style.left = finalX + 'px';
        this.draggedElement.style.top = finalY + 'px';

        // Mettre à jour l'indicateur de position
        this.updatePositionIndicator(this.draggedElement, finalX, finalY);
    }

    stopDrag() {
        if (this.draggedElement) {
            this.draggedElement.classList.remove('dragging');
            this.updateFieldData(this.draggedElement);
        }

        this.isDragging = false;
        this.draggedElement = null;

        // Restaurer les styles
        document.body.style.userSelect = '';
        document.body.style.cursor = '';
    }

    selectField(field) {
        // Désélectionner tous les champs
        document.querySelectorAll('.draggable-field').forEach(f => {
            f.classList.remove('selected');
        });

        // Sélectionner le champ cliqué
        field.classList.add('selected');

        // Mettre à jour les contrôles de style
        this.updateStyleControls(field);
    }

    updatePositionIndicator(field, x, y) {
        const indicator = field.querySelector('.position-indicator');
        if (indicator) {
            indicator.textContent = `X: ${Math.round(x)}, Y: ${Math.round(y)}`;
        }
    }

    updateFieldData(field) {
        const fieldId = field.dataset.field;
        const rect = field.getBoundingClientRect();
        const canvas = document.getElementById('certificateCanvas');
        const canvasRect = canvas.getBoundingClientRect();

        const x = rect.left - canvasRect.left;
        const y = rect.top - canvasRect.top;

        // Mettre à jour les données du template
        if (!window.templateData) {
            window.templateData = {};
        }

        if (!window.templateData[fieldId]) {
            window.templateData[fieldId] = {};
        }

        window.templateData[fieldId].x = Math.round(x);
        window.templateData[fieldId].y = Math.round(y);
        window.templateData[fieldId].width = Math.round(rect.width);
        window.templateData[fieldId].height = Math.round(rect.height);
        window.templateData[fieldId].show = true;
        window.templateData[fieldId].type = fieldId === 'qr_code' ? 'qr' : 'text';
        window.templateData[fieldId].text = field.querySelector('.field-text').textContent;

        console.log('Données mises à jour pour', fieldId, ':', window.templateData[fieldId]);
    }

    updateStyleControls(field) {
        const fieldId = field.dataset.field;
        const fieldData = window.templateData ? .[fieldId] || {};

        // Mettre à jour les contrôles de style
        const fontFamily = document.getElementById('fontFamily');
        const fontSize = document.getElementById('fontSize');
        const fontSizeValue = document.getElementById('fontSizeValue');
        const textColor = document.getElementById('textColor');

        if (fontFamily) fontFamily.value = fieldData.font_family || 'Arial';
        if (fontSize) {
            fontSize.value = fieldData.font_size || 16;
            if (fontSizeValue) fontSizeValue.textContent = (fieldData.font_size || 16) + 'px';
        }
        if (textColor) textColor.value = fieldData.color || '#000000';
    }

    // Méthode pour appliquer les styles
    applyStyle(property, value) {
        const selectedField = document.querySelector('.draggable-field.selected');
        if (!selectedField) return;

        const fieldId = selectedField.dataset.field;

        // Mettre à jour les données
        if (!window.templateData) {
            window.templateData = {};
        }
        if (!window.templateData[fieldId]) {
            window.templateData[fieldId] = {};
        }

        window.templateData[fieldId][property] = value;

        // Appliquer le style visuellement
        switch (property) {
            case 'font_family':
                selectedField.style.fontFamily = value;
                break;
            case 'font_size':
                selectedField.style.fontSize = value + 'px';
                break;
            case 'color':
                selectedField.style.color = value;
                break;
        }
    }

    // Méthode pour basculer la visibilité d'un champ
    toggleField(fieldId, show) {
        const field = document.getElementById(fieldId);
        if (field) {
            field.style.display = show ? 'flex' : 'none';

            if (!window.templateData) {
                window.templateData = {};
            }
            if (!window.templateData[fieldId]) {
                window.templateData[fieldId] = {};
            }
            window.templateData[fieldId].show = show;
        }
    }

    // Méthode pour sauvegarder
    async saveTemplate() {
        // Mettre à jour toutes les données avant de sauvegarder
        document.querySelectorAll('.draggable-field').forEach(field => {
            this.updateFieldData(field);
        });

        try {
            const response = await fetch(window.saveUrl, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    template_data: window.templateData
                })
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification('Template sauvegardé avec succès !', 'success');
            } else {
                this.showNotification('Erreur lors de la sauvegarde', 'error');
            }
        } catch (error) {
            console.error('Erreur:', error);
            this.showNotification('Erreur lors de la sauvegarde', 'error');
        }
    }

    // Méthode pour générer un test
    async generateTest() {
        const testName = document.getElementById('testName') ? .value || 'Test Student';
        const testDate = document.getElementById('testDate') ? .value || new Date().toISOString().split('T')[0];

        // Mettre à jour toutes les données avant le test
        document.querySelectorAll('.draggable-field').forEach(field => {
            this.updateFieldData(field);
        });

        try {
            const response = await fetch(window.testUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    template_data: window.templateData,
                    test_name: testName,
                    test_date: testDate
                })
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification('Certificat de test généré avec succès !', 'success');

                // Afficher le résultat
                const testResult = document.getElementById('testResult');
                const downloadLink = document.getElementById('downloadLink');

                if (testResult) testResult.classList.remove('d-none');
                if (downloadLink) {
                    downloadLink.href = result.download_url;
                    downloadLink.download = `certificat_test_${result.serial_number}.png`;
                }
            } else {
                this.showNotification('Erreur lors de la génération du test', 'error');
            }
        } catch (error) {
            console.error('Erreur:', error);
            this.showNotification('Erreur lors de la génération du test', 'error');
        }
    }

    showNotification(message, type) {
        // Créer une notification toast
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        document.body.appendChild(toast);

        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();

        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }
}

// Initialiser le système de glisser-déposer simple
document.addEventListener('DOMContentLoaded', function() {
    // Passer les données du serveur au JavaScript
    window.imageDimensions = @json($imageDimensions);
    window.templateData = @json($certif - > template_data ? ? []);
    window.saveUrl = '{{ route("certifs.update", $certif) }}';
    window.testUrl = '{{ route("certifs.test-generate-certificate", $certif) }}';

    // Initialiser le système de glisser-déposer simple
    const dragDrop = new SimpleDragDrop();
    window.simpleDragDrop = dragDrop;

    // Configurer les événements des contrôles
    document.getElementById('fontFamily') ? .addEventListener('change', (e) => {
        dragDrop.applyStyle('font_family', e.target.value);
    });

    document.getElementById('fontSize') ? .addEventListener('input', (e) => {
        dragDrop.applyStyle('font_size', parseInt(e.target.value));
        const fontSizeValue = document.getElementById('fontSizeValue');
        if (fontSizeValue) fontSizeValue.textContent = e.target.value + 'px';
    });

    document.getElementById('textColor') ? .addEventListener('change', (e) => {
        dragDrop.applyStyle('color', e.target.value);
    });

    // Toggles des champs
    document.querySelectorAll('.field-item input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', (e) => {
            const fieldId = e.target.closest('.field-item').dataset.field;
            dragDrop.toggleField(fieldId, e.target.checked);
        });
    });

    // Sauvegarde
    document.getElementById('saveTemplate') ? .addEventListener('click', () => {
        dragDrop.saveTemplate();
    });

    // Test de génération
    document.getElementById('generateTest') ? .addEventListener('click', () => {
        dragDrop.generateTest();
    });
});