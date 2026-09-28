/**
 * Éditeur de Certificats Avancé
 * Utilise des technologies modernes pour une expérience utilisateur exceptionnelle
 */

class CertificateEditor {
    constructor() {
        this.config = {
            imageWidth: window.imageDimensions ? .width || 800,
            imageHeight: window.imageDimensions ? .height || 600,
            zoomLevel: 1,
            selectedField: null,
            templateData: window.templateData || {},
            isDragging: false,
            dragOffset: { x: 0, y: 0 },
            minZoom: 0.3,
            maxZoom: 3
        };

        this.init();
        this.setupEventListeners();
        this.loadTemplateData();
    }

    init() {
        this.canvas = document.getElementById('certificateCanvas');
        this.overlay = document.getElementById('certificateOverlay');
        this.image = document.getElementById('certificateImage');

        if (this.image) {
            this.image.onload = () => {
                this.setupCanvas();
            };
        } else {
            this.setupCanvas();
        }
    }

    setupCanvas() {
        this.createFields();
        this.updateZoom();
        this.setupSortable();
    }

    createFields() {
        const fields = [{
                id: 'name_student',
                label: 'Nom de l\'Étudiant',
                x: 400,
                y: 300,
                icon: 'fas fa-user',
                color: 'primary'
            },
            {
                id: 'date',
                label: 'Date',
                x: 400,
                y: 350,
                icon: 'fas fa-calendar',
                color: 'success'
            },
            {
                id: 'qr_code',
                label: 'QR Code',
                x: 441,
                y: 1510,
                width: 100,
                height: 100,
                icon: 'fas fa-qrcode',
                color: 'warning'
            },
            {
                id: 'serial_number',
                label: 'Numéro de Série',
                x: 100,
                y: 1570,
                icon: 'fas fa-hashtag',
                color: 'info'
            }
        ];

        fields.forEach(field => {
            this.createField(field);
        });
    }

    createField(fieldData) {
        const field = document.createElement('div');
        field.className = 'certificate-field';
        field.id = fieldData.id;
        field.dataset.field = fieldData.id;

        // Position et taille
        field.style.left = fieldData.x + 'px';
        field.style.top = fieldData.y + 'px';
        if (fieldData.width) field.style.width = fieldData.width + 'px';
        if (fieldData.height) field.style.height = fieldData.height + 'px';

        // Contenu simplifié
        field.innerHTML = `
            <div class="position-indicator">X: ${fieldData.x}, Y: ${fieldData.y}</div>
            <div class="drag-handle">⋮⋮</div>
            <div class="field-content">
                <span>${fieldData.label}</span>
            </div>
        `;

        // Événements de glisser-déposer
        field.addEventListener('mousedown', (e) => this.startDrag(e));
        field.addEventListener('click', (e) => this.selectField(e));
        field.addEventListener('dblclick', (e) => this.editFieldText(e));

        this.overlay.appendChild(field);
    }

    setupSortable() {
        // Utilisation de SortableJS pour un glisser-déposer avancé
        if (typeof Sortable !== 'undefined') {
            new Sortable(this.overlay, {
                animation: 150,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                onStart: (evt) => {
                    this.onDragStart(evt);
                },
                onEnd: (evt) => {
                    this.onDragEnd(evt);
                }
            });
        }
    }

    startDrag(e) {
        e.preventDefault();
        e.stopPropagation();

        // Trouver le champ le plus proche
        this.config.selectedField = e.target.closest('.certificate-field');

        if (!this.config.selectedField) return;

        this.config.isDragging = true;
        this.selectField(e);
        this.config.selectedField.classList.add('dragging');

        // Calculer l'offset de la souris
        const rect = this.config.selectedField.getBoundingClientRect();
        this.config.dragOffset.x = e.clientX - rect.left;
        this.config.dragOffset.y = e.clientY - rect.top;

        // Ajouter les événements
        document.addEventListener('mousemove', this.drag.bind(this));
        document.addEventListener('mouseup', this.stopDrag.bind(this));

        // Empêcher la sélection de texte
        document.body.style.userSelect = 'none';
        document.body.style.cursor = 'grabbing';
    }

    drag(e) {
        if (!this.config.isDragging || !this.config.selectedField) return;

        e.preventDefault();

        // Calculer la nouvelle position
        const canvasRect = this.canvas.getBoundingClientRect();
        const x = e.clientX - canvasRect.left - this.config.dragOffset.x;
        const y = e.clientY - canvasRect.top - this.config.dragOffset.y;

        // Permettre le positionnement libre sans limite
        const finalX = x;
        const finalY = y;

        // Appliquer la position
        this.config.selectedField.style.left = finalX + 'px';
        this.config.selectedField.style.top = finalY + 'px';

        // Mettre à jour l'indicateur de position
        this.updatePositionIndicator(this.config.selectedField, finalX, finalY);
    }

    stopDrag() {
        if (this.config.selectedField) {
            this.config.selectedField.classList.remove('dragging');
            this.updateFieldData();
        }

        this.config.isDragging = false;
        this.config.selectedField = null;

        // Nettoyer les événements
        document.removeEventListener('mousemove', this.drag.bind(this));
        document.removeEventListener('mouseup', this.stopDrag.bind(this));

        // Restaurer les styles
        document.body.style.userSelect = '';
        document.body.style.cursor = '';
    }

    selectField(e) {
        if (this.config.isDragging) return;

        // Désélectionner tous les champs
        document.querySelectorAll('.certificate-field').forEach(field => {
            field.classList.remove('selected');
        });

        // Désélectionner tous les items de la liste
        document.querySelectorAll('.field-item').forEach(item => {
            item.classList.remove('active');
        });

        // Sélectionner le champ cliqué
        const field = e.target.closest('.certificate-field');
        if (field) {
            field.classList.add('selected');
            this.config.selectedField = field;

            // Sélectionner l'item correspondant dans la liste
            const fieldItem = document.querySelector(`[data-field="${field.dataset.field}"]`);
            if (fieldItem) {
                fieldItem.classList.add('active');
            }

            this.updateStyleControls();
        }
    }

    editFieldText(e) {
        e.preventDefault();
        e.stopPropagation();

        const field = e.target.closest('.certificate-field');
        if (!field) return;

        const currentText = field.querySelector('.field-content span').textContent;
        const newText = prompt('Modifier le texte:', currentText);

        if (newText !== null && newText !== currentText) {
            field.querySelector('.field-content span').textContent = newText;
            this.updateFieldData();
        }
    }

    updatePositionIndicator(field, x, y) {
        const indicator = field.querySelector('.position-indicator');
        if (indicator) {
            indicator.textContent = `X: ${Math.round(x)}, Y: ${Math.round(y)}`;
        }
    }

    updateStyleControls() {
        if (!this.config.selectedField) return;

        const fieldId = this.config.selectedField.dataset.field;
        const fieldData = this.config.templateData[fieldId] || {};

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

    updateFieldData() {
        if (!this.config.selectedField) return;

        const fieldId = this.config.selectedField.dataset.field;
        const rect = this.config.selectedField.getBoundingClientRect();
        const canvasRect = this.canvas.getBoundingClientRect();

        const x = rect.left - canvasRect.left;
        const y = rect.top - canvasRect.top;

        if (!this.config.templateData[fieldId]) {
            this.config.templateData[fieldId] = {};
        }

        this.config.templateData[fieldId].x = Math.round(x);
        this.config.templateData[fieldId].y = Math.round(y);
        this.config.templateData[fieldId].width = Math.round(rect.width);
        this.config.templateData[fieldId].height = Math.round(rect.height);
        this.config.templateData[fieldId].show = true;
        this.config.templateData[fieldId].type = fieldId === 'qr_code' ? 'qr' : 'text';
        this.config.templateData[fieldId].text = this.config.selectedField.querySelector('.field-content span').textContent;
    }

    loadTemplateData() {
        Object.keys(this.config.templateData).forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field && this.config.templateData[fieldId]) {
                const data = this.config.templateData[fieldId];

                if (data.x !== undefined && data.y !== undefined) {
                    field.style.left = data.x + 'px';
                    field.style.top = data.y + 'px';
                }

                if (data.width) field.style.width = data.width + 'px';
                if (data.height) field.style.height = data.height + 'px';
                if (data.font_size) field.style.fontSize = data.font_size + 'px';
                if (data.color) field.style.color = data.color;
                if (data.font_family) field.style.fontFamily = data.font_family;
                if (data.text) {
                    const span = field.querySelector('.field-content span');
                    if (span) span.textContent = data.text;
                }
                if (data.show !== undefined) {
                    field.style.display = data.show ? 'flex' : 'none';
                }
            }
        });
    }

    setupEventListeners() {
        // Zoom
        document.getElementById('zoomIn') ? .addEventListener('click', () => this.zoomIn());
        document.getElementById('zoomOut') ? .addEventListener('click', () => this.zoomOut());

        // Contrôles de style
        document.getElementById('fontFamily') ? .addEventListener('change', (e) => this.updateFieldStyle('font_family', e.target.value));
        document.getElementById('fontSize') ? .addEventListener('input', (e) => {
            this.updateFieldStyle('font_size', parseInt(e.target.value));
            document.getElementById('fontSizeValue').textContent = e.target.value + 'px';
        });
        document.getElementById('textColor') ? .addEventListener('change', (e) => this.updateFieldStyle('color', e.target.value));

        // Toggles des champs
        document.querySelectorAll('.field-item input[type="checkbox"]').forEach(checkbox => {
            checkbox.addEventListener('change', (e) => {
                const fieldId = e.target.closest('.field-item').dataset.field;
                this.toggleField(fieldId, e.target.checked);
            });
        });

        // Clic sur les items de la liste
        document.querySelectorAll('.field-item').forEach(item => {
            item.addEventListener('click', (e) => {
                if (e.target.type === 'checkbox') return;

                const fieldId = item.dataset.field;
                const field = document.getElementById(fieldId);
                if (field) {
                    this.selectField({ target: field });
                }
            });
        });

        // Sauvegarde
        document.getElementById('saveTemplate') ? .addEventListener('click', () => this.showSaveModal());
        document.getElementById('confirmSave') ? .addEventListener('click', () => this.saveTemplate());

        // Test de génération
        document.getElementById('generateTest') ? .addEventListener('click', () => this.generateTest());

        // Raccourcis clavier
        document.addEventListener('keydown', (e) => this.handleKeyboard(e));
    }

    updateFieldStyle(property, value) {
        if (!this.config.selectedField) return;

        const fieldId = this.config.selectedField.dataset.field;
        if (!this.config.templateData[fieldId]) {
            this.config.templateData[fieldId] = {};
        }

        this.config.templateData[fieldId][property] = value;

        // Appliquer le style visuellement
        switch (property) {
            case 'font_family':
                this.config.selectedField.style.fontFamily = value;
                break;
            case 'font_size':
                this.config.selectedField.style.fontSize = value + 'px';
                break;
            case 'color':
                this.config.selectedField.style.color = value;
                break;
        }
    }

    toggleField(fieldId, show) {
        const field = document.getElementById(fieldId);
        if (field) {
            field.style.display = show ? 'flex' : 'none';

            if (!this.config.templateData[fieldId]) {
                this.config.templateData[fieldId] = {};
            }
            this.config.templateData[fieldId].show = show;
        }
    }

    zoomIn() {
        this.config.zoomLevel = Math.min(this.config.zoomLevel + 0.1, this.config.maxZoom);
        this.updateZoom();
    }

    zoomOut() {
        this.config.zoomLevel = Math.max(this.config.zoomLevel - 0.1, this.config.minZoom);
        this.updateZoom();
    }

    updateZoom() {
        this.canvas.style.transform = `scale(${this.config.zoomLevel})`;
        document.getElementById('zoomLevel').textContent = Math.round(this.config.zoomLevel * 100) + '%';
    }

    handleKeyboard(e) {
        if (e.ctrlKey || e.metaKey) {
            switch (e.key) {
                case 's':
                    e.preventDefault();
                    this.showSaveModal();
                    break;
                case '=':
                case '+':
                    e.preventDefault();
                    this.zoomIn();
                    break;
                case '-':
                    e.preventDefault();
                    this.zoomOut();
                    break;
            }
        }

        if (e.key === 'Delete' && this.config.selectedField) {
            this.deleteField();
        }
    }

    deleteField() {
        if (!this.config.selectedField) return;

        if (confirm('Êtes-vous sûr de vouloir supprimer ce champ ?')) {
            const fieldId = this.config.selectedField.dataset.field;
            this.config.selectedField.remove();
            delete this.config.templateData[fieldId];
            this.config.selectedField = null;
        }
    }

    showSaveModal() {
        const modal = new bootstrap.Modal(document.getElementById('saveModal'));
        modal.show();
    }

    saveTemplate() {
        // Mettre à jour toutes les données avant de sauvegarder
        document.querySelectorAll('.certificate-field').forEach(field => {
            this.config.selectedField = field;
            this.updateFieldData();
        });

        fetch(window.saveUrl, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    template_data: this.config.templateData
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.showNotification('Template sauvegardé avec succès !', 'success');
                    bootstrap.Modal.getInstance(document.getElementById('saveModal')).hide();
                } else {
                    this.showNotification('Erreur lors de la sauvegarde', 'error');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                this.showNotification('Erreur lors de la sauvegarde', 'error');
            });
    }

    generateTest() {
        const testName = document.getElementById('testName').value;
        const testDate = document.getElementById('testDate').value;

        if (!testName) {
            this.showNotification('Veuillez entrer un nom de test', 'warning');
            return;
        }

        // Mettre à jour toutes les données avant le test
        document.querySelectorAll('.certificate-field').forEach(field => {
            this.config.selectedField = field;
            this.updateFieldData();
        });

        fetch(window.testUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    template_data: this.config.templateData,
                    test_name: testName,
                    test_date: testDate
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.showNotification('Certificat de test généré avec succès !', 'success');

                    // Afficher le résultat
                    const testResult = document.getElementById('testResult');
                    const downloadLink = document.getElementById('downloadLink');

                    testResult.classList.remove('d-none');
                    downloadLink.href = data.download_url;
                    downloadLink.download = `certificat_test_${data.serial_number}.png`;
                } else {
                    this.showNotification('Erreur lors de la génération du test', 'error');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                this.showNotification('Erreur lors de la génération du test', 'error');
            });
    }

    showNotification(message, type) {
        // Créer une notification toast
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'warning'} border-0`;
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

// Initialiser l'éditeur quand le DOM est prêt
document.addEventListener('DOMContentLoaded', function() {
    // Passer les données du serveur au JavaScript
    window.imageDimensions = @json($imageDimensions);
    window.templateData = @json($certif - > template_data ? ? []);
    window.saveUrl = '{{ route("certifs.update", $certif) }}';
    window.testUrl = '{{ route("certifs.test-generate-certificate", $certif) }}';

    // Initialiser l'éditeur
    const editor = new CertificateEditor();

    // Initialiser le système de test d'intégration
    if (typeof CertificateTestIntegration !== 'undefined') {
        const testIntegration = new CertificateTestIntegration(editor);
        window.certificateTestIntegration = testIntegration;
    }

    // Initialiser la sauvegarde automatique
    if (typeof CertificateAutoSave !== 'undefined') {
        const autoSave = new CertificateAutoSave(editor);
        window.certificateAutoSave = autoSave;
    }
});