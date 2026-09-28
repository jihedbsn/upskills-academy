/**
 * Solution Certificate Editor V2 - Système de Drag & Drop Moderne
 * Utilise l'API HTML5 Drag & Drop native pour une meilleure compatibilité
 */

class CertificateEditorV2 {
    constructor() {
        this.certificateCanvas = document.getElementById('certificateCanvas');
        this.certificateOverlay = document.getElementById('certificateOverlay');
        this.certificateImage = document.getElementById('certificateImage');
        this.selectedField = null;
        this.zoomLevel = 1;
        this.gridSize = 10; // Taille de la grille magnétique
        this.showGrid = false;

        // Données du template avec positions par défaut
        this.templateData = {
            name_student: {
                x: 100,
                y: 200,
                width: 200,
                height: 30,
                show: true,
                text: 'Nom de l\'Étudiant',
                font_size: 16,
                color: '#000000',
                font_family: 'Arial',
                font_weight: 'normal',
                font_style: 'normal',
                text_decoration: 'none'
            },
            date: {
                x: 100,
                y: 250,
                width: 150,
                height: 30,
                show: true,
                text: 'Date',
                font_size: 14,
                color: '#000000',
                font_family: 'Arial',
                font_weight: 'normal',
                font_style: 'normal',
                text_decoration: 'none'
            },
            serial_number: {
                x: 100,
                y: 300,
                width: 180,
                height: 30,
                show: true,
                text: 'Numéro de Série',
                font_size: 12,
                color: '#666666',
                font_family: 'Arial',
                font_weight: 'normal',
                font_style: 'normal',
                text_decoration: 'none'
            },
            qr_code: {
                x: 400,
                y: 300,
                width: 80,
                height: 80,
                show: true,
                text: 'QR',
                font_size: 10,
                color: '#000000',
                font_family: 'Arial',
                font_weight: 'normal',
                font_style: 'normal',
                text_decoration: 'none'
            }
        };

        this.init();
    }

    init() {
        console.log('🚀 Initialisation Certificate Editor V2');

        // Charger les données existantes
        this.loadExistingData();

        // Initialiser l'interface
        this.initializeInterface();

        // Configurer les événements
        this.setupEventListeners();

        // Initialiser les champs
        this.initializeFields();

        console.log('✅ Certificate Editor V2 initialisé');
    }

    loadExistingData() {
        const certifId = this.getCertifId();
        if (certifId) {
            fetch(`/certifs/${certifId}/template-data`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.template_data) {
                        this.templateData = {...this.templateData, ...data.template_data };
                        this.initializeFields();
                        console.log('📥 Données chargées:', this.templateData);
                    }
                })
                .catch(error => {
                    console.warn('⚠️ Erreur chargement données:', error);
                });
        }
    }

    getCertifId() {
        const pathParts = window.location.pathname.split('/');
        return pathParts[pathParts.length - 1];
    }

    initializeInterface() {
        // Créer la grille magnétique
        this.createGrid();

        // Configurer les contrôles de style
        this.setupStyleControls();

        // Configurer le test de génération
        this.setupTestGeneration();

        // Ajouter les boutons de contrôle
        this.addControlButtons();
    }

    createGrid() {
        const gridOverlay = document.createElement('div');
        gridOverlay.id = 'gridOverlay';
        gridOverlay.className = 'grid-overlay';
        gridOverlay.style.cssText = `
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            opacity: 0.1;
            background-image: 
                linear-gradient(to right, #ccc 1px, transparent 1px),
                linear-gradient(to bottom, #ccc 1px, transparent 1px);
            background-size: ${this.gridSize}px ${this.gridSize}px;
            display: none;
            z-index: 1;
        `;

        this.certificateOverlay.appendChild(gridOverlay);
    }

    addControlButtons() {
        const controlsContainer = document.createElement('div');
        controlsContainer.className = 'certificate-controls';
        controlsContainer.style.cssText = `
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1000;
            display: flex;
            gap: 5px;
        `;

        // Bouton grille
        const gridBtn = this.createControlButton('grid', 'Grille', 'fas fa-th');
        gridBtn.addEventListener('click', () => this.toggleGrid());

        // Bouton centrer
        const centerBtn = this.createControlButton('center', 'Centrer', 'fas fa-align-center');
        centerBtn.addEventListener('click', () => this.centerSelectedField());

        // Bouton dupliquer
        const duplicateBtn = this.createControlButton('duplicate', 'Dupliquer', 'fas fa-copy');
        duplicateBtn.addEventListener('click', () => this.duplicateSelectedField());

        controlsContainer.appendChild(gridBtn);
        controlsContainer.appendChild(centerBtn);
        controlsContainer.appendChild(duplicateBtn);

        this.certificateCanvas.appendChild(controlsContainer);
    }

    createControlButton(id, title, icon) {
        const button = document.createElement('button');
        button.id = id;
        button.title = title;
        button.className = 'btn btn-sm btn-outline-secondary';
        button.innerHTML = `<i class="${icon}"></i>`;
        button.style.cssText = `
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        `;
        return button;
    }

    toggleGrid() {
        const gridOverlay = document.getElementById('gridOverlay');
        this.showGrid = !this.showGrid;
        gridOverlay.style.display = this.showGrid ? 'block' : 'none';

        const gridBtn = document.getElementById('grid');
        gridBtn.classList.toggle('btn-primary', this.showGrid);
        gridBtn.classList.toggle('btn-outline-secondary', !this.showGrid);
    }

    centerSelectedField() {
        if (!this.selectedField) return;

        const field = this.selectedField.element;
        const canvasWidth = this.certificateCanvas.offsetWidth;
        const canvasHeight = this.certificateCanvas.offsetHeight;

        const centerX = (canvasWidth - field.offsetWidth) / 2;
        const centerY = (canvasHeight - field.offsetHeight) / 2;

        this.moveFieldTo(field, centerX, centerY);
    }

    duplicateSelectedField() {
        if (!this.selectedField) return;

        const fieldKey = this.selectedField.key;
        const fieldData = {...this.templateData[fieldKey] };

        // Créer une nouvelle clé
        const newKey = fieldKey + '_copy_' + Date.now();
        fieldData.x += 20;
        fieldData.y += 20;
        fieldData.text = fieldData.text + ' (Copie)';

        this.templateData[newKey] = fieldData;
        this.createField(newKey, fieldData);
        this.selectField(document.querySelector(`[data-field="${newKey}"]`), newKey);
    }

    initializeFields() {
        // Nettoyer l'overlay
        this.certificateOverlay.innerHTML = '';

        // Recréer la grille
        this.createGrid();

        // Créer les champs
        Object.keys(this.templateData).forEach(fieldKey => {
            const fieldData = this.templateData[fieldKey];
            if (fieldData.show) {
                this.createField(fieldKey, fieldData);
            }
        });
    }

    createField(fieldKey, fieldData) {
        const field = document.createElement('div');
        field.className = 'certificate-field-v2';
        field.dataset.field = fieldKey;
        field.draggable = true;

        // Appliquer les styles
        this.applyFieldStyles(field, fieldData);

        // Contenu du champ
        field.innerHTML = `
            <div class="field-content">${fieldData.text}</div>
            <div class="field-controls">
                <button class="field-btn field-edit" title="Éditer">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="field-btn field-delete" title="Supprimer">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="position-indicator">
                X: ${fieldData.x}, Y: ${fieldData.y}
            </div>
        `;

        // Ajouter au canvas
        this.certificateOverlay.appendChild(field);

        // Configurer les événements
        this.setupFieldEvents(field, fieldKey);

        return field;
    }

    applyFieldStyles(field, fieldData) {
        field.style.cssText = `
            position: absolute;
            left: ${fieldData.x}px;
            top: ${fieldData.y}px;
            width: ${fieldData.width}px;
            height: ${fieldData.height}px;
            font-size: ${fieldData.font_size}px;
            color: ${fieldData.color};
            font-family: ${fieldData.font_family};
            font-weight: ${fieldData.font_weight};
            font-style: ${fieldData.font_style};
            text-decoration: ${fieldData.text_decoration};
            border: 2px solid transparent;
            background: rgba(0, 123, 255, 0.1);
            border-radius: 4px;
            padding: 8px;
            cursor: move;
            user-select: none;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 100px;
            min-height: 30px;
        `;
    }

    setupFieldEvents(field, fieldKey) {
        // Événements de sélection
        field.addEventListener('click', (e) => {
            e.stopPropagation();
            this.selectField(field, fieldKey);
        });

        // Événements HTML5 Drag & Drop
        field.addEventListener('dragstart', (e) => {
            this.handleDragStart(e, field, fieldKey);
        });

        field.addEventListener('drag', (e) => {
            this.handleDrag(e, field, fieldKey);
        });

        field.addEventListener('dragend', (e) => {
            this.handleDragEnd(e, field, fieldKey);
        });

        // Événements de survol
        field.addEventListener('mouseenter', () => {
            this.showFieldControls(field);
        });

        field.addEventListener('mouseleave', () => {
            this.hideFieldControls(field);
        });

        // Événements des boutons de contrôle
        const editBtn = field.querySelector('.field-edit');
        const deleteBtn = field.querySelector('.field-delete');

        if (editBtn) {
            editBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.editFieldText(fieldKey);
            });
        }

        if (deleteBtn) {
            deleteBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.deleteField(fieldKey);
            });
        }
    }

    handleDragStart(e, field, fieldKey) {
        field.classList.add('dragging');
        this.selectedField = { element: field, key: fieldKey };

        // Calculer l'offset
        const rect = field.getBoundingClientRect();
        const canvasRect = this.certificateCanvas.getBoundingClientRect();

        this.dragOffset = {
            x: e.clientX - rect.left,
            y: e.clientY - rect.top
        };

        // Données de drag
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', fieldKey);

        console.log('🎯 Début du drag:', fieldKey);
    }

    handleDrag(e, field, fieldKey) {
        // Mise à jour en temps réel pendant le drag
        const canvasRect = this.certificateCanvas.getBoundingClientRect();
        const newX = e.clientX - canvasRect.left - this.dragOffset.x;
        const newY = e.clientY - canvasRect.top - this.dragOffset.y;

        // Appliquer la grille magnétique si activée
        const gridX = this.showGrid ? this.snapToGrid(newX) : newX;
        const gridY = this.showGrid ? this.snapToGrid(newY) : newY;

        // Permettre le positionnement libre sans limite
        const constrainedX = gridX;
        const constrainedY = gridY;

        field.style.left = constrainedX + 'px';
        field.style.top = constrainedY + 'px';

        // Mettre à jour l'indicateur de position
        this.updatePositionIndicator(field, constrainedX, constrainedY);
    }

    handleDragEnd(e, field, fieldKey) {
        field.classList.remove('dragging');

        // Mettre à jour les données du template
        const finalX = parseInt(field.style.left);
        const finalY = parseInt(field.style.top);

        this.templateData[fieldKey].x = finalX;
        this.templateData[fieldKey].y = finalY;

        // Sauvegarder automatiquement
        this.autoSave();

        console.log('✅ Fin du drag:', fieldKey, 'Position:', finalX, finalY);
    }

    snapToGrid(value) {
        return Math.round(value / this.gridSize) * this.gridSize;
    }

    updatePositionIndicator(field, x, y) {
        const indicator = field.querySelector('.position-indicator');
        if (indicator) {
            indicator.textContent = `X: ${Math.round(x)}, Y: ${Math.round(y)}`;
        }
    }

    selectField(field, fieldKey) {
        // Désélectionner tous les autres champs
        document.querySelectorAll('.certificate-field-v2').forEach(f => {
            f.classList.remove('selected');
        });

        // Sélectionner le champ actuel
        field.classList.add('selected');
        this.selectedField = { element: field, key: fieldKey };

        // Mettre à jour les contrôles de style
        this.updateStyleControls(fieldKey);

        console.log('🎯 Champ sélectionné:', fieldKey);
    }

    updateStyleControls(fieldKey) {
        const fieldData = this.templateData[fieldKey];

        // Mettre à jour les contrôles
        const controls = {
            fontFamily: document.getElementById('fontFamily'),
            fontSize: document.getElementById('fontSize'),
            fontSizeValue: document.getElementById('fontSizeValue'),
            textColor: document.getElementById('textColor'),
            bold: document.getElementById('bold'),
            italic: document.getElementById('italic'),
            underline: document.getElementById('underline')
        };

        if (controls.fontFamily) controls.fontFamily.value = fieldData.font_family || 'Arial';
        if (controls.fontSize) {
            controls.fontSize.value = fieldData.font_size || 16;
            if (controls.fontSizeValue) controls.fontSizeValue.textContent = (fieldData.font_size || 16) + 'px';
        }
        if (controls.textColor) controls.textColor.value = fieldData.color || '#000000';
        if (controls.bold) controls.bold.checked = fieldData.font_weight === 'bold';
        if (controls.italic) controls.italic.checked = fieldData.font_style === 'italic';
        if (controls.underline) controls.underline.checked = fieldData.text_decoration === 'underline';
    }

    setupStyleControls() {
        const controls = {
            fontFamily: document.getElementById('fontFamily'),
            fontSize: document.getElementById('fontSize'),
            fontSizeValue: document.getElementById('fontSizeValue'),
            textColor: document.getElementById('textColor'),
            bold: document.getElementById('bold'),
            italic: document.getElementById('italic'),
            underline: document.getElementById('underline')
        };

        // Contrôle de la police
        if (controls.fontFamily) {
            controls.fontFamily.addEventListener('change', (e) => {
                this.applyStyle('font_family', e.target.value);
            });
        }

        // Contrôle de la taille
        if (controls.fontSize) {
            controls.fontSize.addEventListener('input', (e) => {
                if (controls.fontSizeValue) {
                    controls.fontSizeValue.textContent = e.target.value + 'px';
                }
                this.applyStyle('font_size', parseInt(e.target.value));
            });
        }

        // Contrôle de la couleur
        if (controls.textColor) {
            controls.textColor.addEventListener('change', (e) => {
                this.applyStyle('color', e.target.value);
            });
        }

        // Contrôles de style
        if (controls.bold) {
            controls.bold.addEventListener('change', (e) => {
                this.applyStyle('font_weight', e.target.checked ? 'bold' : 'normal');
            });
        }

        if (controls.italic) {
            controls.italic.addEventListener('change', (e) => {
                this.applyStyle('font_style', e.target.checked ? 'italic' : 'normal');
            });
        }

        if (controls.underline) {
            controls.underline.addEventListener('change', (e) => {
                this.applyStyle('text_decoration', e.target.checked ? 'underline' : 'none');
            });
        }
    }

    applyStyle(property, value) {
        if (!this.selectedField) return;

        const fieldKey = this.selectedField.key;
        const field = this.selectedField.element;

        // Mettre à jour les données du template
        this.templateData[fieldKey][property] = value;

        // Appliquer le style au champ
        switch (property) {
            case 'font_family':
                field.style.fontFamily = value;
                break;
            case 'font_size':
                field.style.fontSize = value + 'px';
                break;
            case 'color':
                field.style.color = value;
                break;
            case 'font_weight':
                field.style.fontWeight = value;
                break;
            case 'font_style':
                field.style.fontStyle = value;
                break;
            case 'text_decoration':
                field.style.textDecoration = value;
                break;
        }

        // Sauvegarder automatiquement
        this.autoSave();

        console.log(`🎨 Style appliqué: ${property} = ${value}`);
    }

    setupEventListeners() {
        // Contrôles de zoom
        const zoomIn = document.getElementById('zoomIn');
        const zoomOut = document.getElementById('zoomOut');
        const zoomLevel = document.getElementById('zoomLevel');

        if (zoomIn) {
            zoomIn.addEventListener('click', () => {
                this.zoomLevel = Math.min(this.zoomLevel + 0.1, 2);
                this.updateZoom();
            });
        }

        if (zoomOut) {
            zoomOut.addEventListener('click', () => {
                this.zoomLevel = Math.max(this.zoomLevel - 0.1, 0.5);
                this.updateZoom();
            });
        }

        // Sauvegarde
        const saveTemplate = document.getElementById('saveTemplate');
        if (saveTemplate) {
            saveTemplate.addEventListener('click', () => {
                this.saveTemplate();
            });
        }

        // Toggles des champs
        document.querySelectorAll('.field-item input[type="checkbox"]').forEach(checkbox => {
            checkbox.addEventListener('change', (e) => {
                const fieldKey = e.target.closest('.field-item').dataset.field;
                this.toggleField(fieldKey, e.target.checked);
            });
        });

        // Désélectionner en cliquant sur le canvas
        this.certificateCanvas.addEventListener('click', (e) => {
            if (e.target === this.certificateCanvas || e.target === this.certificateImage) {
                this.deselectAllFields();
            }
        });
    }

    updateZoom() {
        const zoomLevel = document.getElementById('zoomLevel');
        if (zoomLevel) {
            zoomLevel.textContent = Math.round(this.zoomLevel * 100) + '%';
        }

        this.certificateCanvas.style.transform = `scale(${this.zoomLevel})`;
    }

    toggleField(fieldKey, show) {
        this.templateData[fieldKey].show = show;

        if (show) {
            if (!document.querySelector(`[data-field="${fieldKey}"]`)) {
                this.createField(fieldKey, this.templateData[fieldKey]);
            }
        } else {
            const field = document.querySelector(`[data-field="${fieldKey}"]`);
            if (field) {
                field.remove();
            }
        }

        this.autoSave();
    }

    setupTestGeneration() {
        const generateTest = document.getElementById('generateTest');
        if (generateTest) {
            generateTest.addEventListener('click', () => {
                this.generateTestCertificate();
            });
        }
    }

    generateTestCertificate() {
        const testName = document.getElementById('testName') ? .value || 'Jean Dupont';
        const testDate = document.getElementById('testDate') ? .value || new Date().toISOString().split('T')[0];

        const testData = {
            fullname_en: testName,
            fullname_ar: testName,
            date: testDate,
            serial_number: 'TEST-' + Date.now(),
            template_data: this.templateData
        };

        console.log('🧪 Génération du certificat de test:', testData);

        // Afficher un indicateur de chargement
        const generateBtn = document.getElementById('generateTest');
        const originalText = generateBtn.innerHTML;
        generateBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Génération...';
        generateBtn.disabled = true;

        const certifId = this.getCertifId();

        fetch(`/certifs/${certifId}/test-generate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? .getAttribute('content')
                },
                body: JSON.stringify(testData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.showTestResult(data);
                } else {
                    this.showTestError(data.message || 'Erreur lors de la génération');
                }
            })
            .catch(error => {
                console.error('❌ Erreur lors de la génération:', error);
                this.showTestError('Erreur de connexion');
            })
            .finally(() => {
                generateBtn.innerHTML = originalText;
                generateBtn.disabled = false;
            });
    }

    showTestResult(data) {
        const testResult = document.getElementById('testResult');
        const downloadLink = document.getElementById('downloadLink');

        if (testResult) {
            testResult.classList.remove('d-none');
        }

        if (downloadLink && data.download_url) {
            downloadLink.href = data.download_url;
        }

        console.log('✅ Certificat de test généré:', data);
    }

    showTestError(message) {
        alert('Erreur: ' + message);
        console.error('❌ Erreur de test:', message);
    }

    autoSave() {
        clearTimeout(this.autoSaveTimeout);
        this.autoSaveTimeout = setTimeout(() => {
            this.saveTemplate(false);
        }, 2000);
    }

    saveTemplate(showMessage = true) {
        const certifId = this.getCertifId();

        const saveData = {
            template_data: this.templateData,
            orientation: 'vertical'
        };

        console.log('💾 Sauvegarde du template:', saveData);

        fetch(`/certifs/${certifId}/update-template`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? .getAttribute('content')
                },
                body: JSON.stringify(saveData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (showMessage) {
                        this.showSuccessMessage('Template sauvegardé avec succès !');
                    }
                    console.log('✅ Template sauvegardé');
                } else {
                    this.showErrorMessage('Erreur lors de la sauvegarde: ' + (data.message || 'Erreur inconnue'));
                }
            })
            .catch(error => {
                console.error('❌ Erreur de sauvegarde:', error);
                this.showErrorMessage('Erreur de connexion lors de la sauvegarde');
            });
    }

    showSuccessMessage(message) {
        const notification = document.createElement('div');
        notification.className = 'alert alert-success position-fixed';
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            <i class="fas fa-check-circle me-2"></i>
            ${message}
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    showErrorMessage(message) {
        const notification = document.createElement('div');
        notification.className = 'alert alert-danger position-fixed';
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            <i class="fas fa-exclamation-circle me-2"></i>
            ${message}
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 5000);
    }

    showFieldControls(field) {
        const controls = field.querySelector('.field-controls');
        if (controls) {
            controls.style.opacity = '1';
        }
    }

    hideFieldControls(field) {
        const controls = field.querySelector('.field-controls');
        if (controls) {
            controls.style.opacity = '0';
        }
    }

    editFieldText(fieldKey) {
        const fieldData = this.templateData[fieldKey];
        const newText = prompt('Nouveau texte:', fieldData.text);

        if (newText !== null) {
            fieldData.text = newText;
            const field = document.querySelector(`[data-field="${fieldKey}"]`);
            if (field) {
                field.querySelector('.field-content').textContent = newText;
            }
            this.autoSave();
        }
    }

    deleteField(fieldKey) {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce champ ?')) {
            const field = document.querySelector(`[data-field="${fieldKey}"]`);
            if (field) {
                field.remove();
            }
            delete this.templateData[fieldKey];
            this.autoSave();
        }
    }

    deselectAllFields() {
        document.querySelectorAll('.certificate-field-v2').forEach(f => {
            f.classList.remove('selected');
        });
        this.selectedField = null;
    }

    moveFieldTo(field, x, y) {
        // Permettre le positionnement libre sans limite
        const constrainedX = x;
        const constrainedY = y;

        field.style.left = constrainedX + 'px';
        field.style.top = constrainedY + 'px';

        this.updatePositionIndicator(field, constrainedX, constrainedY);

        if (this.selectedField) {
            this.templateData[this.selectedField.key].x = constrainedX;
            this.templateData[this.selectedField.key].y = constrainedY;
            this.autoSave();
        }
    }
}

// Fonction de debug améliorée
function debugInfo() {
    if (window.certificateEditorV2) {
        console.log('🐛 Informations de debug V2:');
        console.log('- Template Data:', window.certificateEditorV2.templateData);
        console.log('- Champ sélectionné:', window.certificateEditorV2.selectedField);
        console.log('- Niveau de zoom:', window.certificateEditorV2.zoomLevel);
        console.log('- Grille activée:', window.certificateEditorV2.showGrid);
        console.log('- Taille de grille:', window.certificateEditorV2.gridSize);
        console.log('- Canvas dimensions:', {
            width: window.certificateEditorV2.certificateCanvas.offsetWidth,
            height: window.certificateEditorV2.certificateCanvas.offsetHeight
        });
    } else {
        console.log('❌ Certificate Editor V2 non initialisé');
    }
}

// Initialiser l'éditeur V2
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Démarrage Certificate Editor V2...');

    if (document.getElementById('certificateCanvas') && document.getElementById('certificateOverlay')) {
        window.certificateEditorV2 = new CertificateEditorV2();
    } else {
        console.error('❌ Éléments de l\'éditeur de certificats non trouvés');
    }
});

// Exporter pour utilisation globale
window.CertificateEditorV2 = CertificateEditorV2;