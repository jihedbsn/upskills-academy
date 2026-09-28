/**
 * Certificate Editor avec Boutons - Solution Simple et Visible
 * Boutons clairs pour glisser les champs sur le certificat
 */

class CertificateEditorButtons {
    constructor() {
        this.certificateCanvas = document.getElementById('certificateCanvas');
        this.certificateOverlay = document.getElementById('certificateOverlay');
        this.certificateImage = document.getElementById('certificateImage');
        this.selectedField = null;
        this.zoomLevel = 1;
        this.isDragging = false;
        this.dragOffset = { x: 0, y: 0 };

        // Données du template
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
        console.log('🚀 Initialisation Certificate Editor avec Boutons');

        // Charger les données existantes
        this.loadExistingData();

        // Créer les boutons de glissement
        this.createDragButtons();

        // Initialiser les champs
        this.initializeFields();

        // Configurer les événements
        this.setupEventListeners();

        // Configurer les contrôles de style
        this.setupStyleControls();

        // Configurer le test de génération
        this.setupTestGeneration();

        console.log('✅ Certificate Editor avec Boutons initialisé');
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

    createDragButtons() {
        // Créer un conteneur pour les boutons de glissement
        const buttonsContainer = document.createElement('div');
        buttonsContainer.id = 'dragButtonsContainer';
        buttonsContainer.className = 'drag-buttons-container';
        buttonsContainer.style.cssText = `
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            border: 2px solid #007bff;
            min-width: 250px;
        `;

        // Titre
        const title = document.createElement('h6');
        title.innerHTML = '🎯 Glisser les Champs';
        title.style.cssText = 'margin-bottom: 15px; color: #007bff; font-weight: bold;';
        buttonsContainer.appendChild(title);

        // Boutons pour chaque champ
        const fields = [
            { key: 'name_student', icon: '👤', label: 'Nom Étudiant', color: '#007bff' },
            { key: 'date', icon: '📅', label: 'Date', color: '#28a745' },
            { key: 'serial_number', icon: '🔢', label: 'Numéro Série', color: '#ffc107' },
            { key: 'qr_code', icon: '📱', label: 'QR Code', color: '#dc3545' }
        ];

        fields.forEach(field => {
            const button = this.createDragButton(field);
            buttonsContainer.appendChild(button);
        });

        // Instructions
        const instructions = document.createElement('div');
        instructions.className = 'drag-instructions';
        instructions.style.cssText = `
            margin-top: 15px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
            font-size: 12px;
            color: #6c757d;
        `;
        instructions.innerHTML = `
            <strong>Instructions :</strong><br>
            1. Cliquez sur un bouton<br>
            2. Glissez sur le certificat<br>
            3. Relâchez pour positionner
        `;
        buttonsContainer.appendChild(instructions);

        document.body.appendChild(buttonsContainer);
    }

    createDragButton(field) {
        const button = document.createElement('button');
        button.className = 'drag-button';
        button.dataset.field = field.key;
        button.style.cssText = `
            width: 100%;
            padding: 10px;
            margin-bottom: 8px;
            border: 2px solid ${field.color};
            background: ${field.color};
            color: white;
            border-radius: 6px;
            cursor: grab;
            font-weight: bold;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
        `;

        button.innerHTML = `
            <span>
                <span style="font-size: 16px; margin-right: 8px;">${field.icon}</span>
                ${field.label}
            </span>
            <span style="font-size: 12px; opacity: 0.8;">Glisser</span>
        `;

        // Événements de drag & drop
        button.draggable = true;

        button.addEventListener('dragstart', (e) => {
            this.handleDragStart(e, field.key);
        });

        button.addEventListener('dragend', (e) => {
            this.handleDragEnd(e, field.key);
        });

        // Événements de survol
        button.addEventListener('mouseenter', () => {
            button.style.transform = 'scale(1.05)';
            button.style.boxShadow = '0 4px 12px rgba(0,0,0,0.2)';
        });

        button.addEventListener('mouseleave', () => {
            button.style.transform = 'scale(1)';
            button.style.boxShadow = 'none';
        });

        return button;
    }

    handleDragStart(e, fieldKey) {
        console.log('🎯 Début du glissement:', fieldKey);

        // Données de drag
        e.dataTransfer.effectAllowed = 'copy';
        e.dataTransfer.setData('text/plain', fieldKey);

        // Style du bouton pendant le drag
        e.target.style.opacity = '0.5';
        e.target.style.transform = 'scale(0.95)';

        // Ajouter un indicateur visuel sur le canvas
        this.addDragIndicator();
    }

    handleDragEnd(e, fieldKey) {
        console.log('✅ Fin du glissement:', fieldKey);

        // Restaurer le style du bouton
        e.target.style.opacity = '1';
        e.target.style.transform = 'scale(1)';

        // Supprimer l'indicateur
        this.removeDragIndicator();
    }

    addDragIndicator() {
        const indicator = document.createElement('div');
        indicator.id = 'dragIndicator';
        indicator.style.cssText = `
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 123, 255, 0.1);
            border: 2px dashed #007bff;
            border-radius: 8px;
            pointer-events: none;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #007bff;
            font-weight: bold;
        `;
        indicator.innerHTML = '🎯 Glissez ici pour positionner le champ';

        this.certificateCanvas.appendChild(indicator);
    }

    removeDragIndicator() {
        const indicator = document.getElementById('dragIndicator');
        if (indicator) {
            indicator.remove();
        }
    }

    initializeFields() {
        // Nettoyer l'overlay
        this.certificateOverlay.innerHTML = '';

        // Créer les champs basés sur les données du template
        Object.keys(this.templateData).forEach(fieldKey => {
            const fieldData = this.templateData[fieldKey];
            if (fieldData.show) {
                this.createField(fieldKey, fieldData);
            }
        });
    }

    createField(fieldKey, fieldData) {
        const field = document.createElement('div');
        field.className = 'certificate-field-button';
        field.dataset.field = fieldKey;

        // Appliquer les styles
        this.applyFieldStyles(field, fieldData);

        // Contenu du champ
        field.innerHTML = `
            <div class="field-content">${fieldData.text}</div>
            <div class="field-controls">
                <button class="field-btn field-edit" title="Éditer le texte">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="field-btn field-move" title="Déplacer">
                    <i class="fas fa-arrows-alt"></i>
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
            border: 2px solid #007bff;
            background: rgba(0, 123, 255, 0.1);
            border-radius: 6px;
            padding: 8px;
            cursor: move;
            user-select: none;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 100px;
            min-height: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        `;
    }

    setupFieldEvents(field, fieldKey) {
        // Événements de sélection
        field.addEventListener('click', (e) => {
            e.stopPropagation();
            this.selectField(field, fieldKey);
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
        const moveBtn = field.querySelector('.field-move');
        const deleteBtn = field.querySelector('.field-delete');

        if (editBtn) {
            editBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.editFieldText(fieldKey);
            });
        }

        if (moveBtn) {
            moveBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.startFieldMove(field, fieldKey);
            });
        }

        if (deleteBtn) {
            deleteBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.deleteField(fieldKey);
            });
        }

        // Événements de déplacement avec la souris
        let isMoving = false;
        let startX, startY, startLeft, startTop;

        field.addEventListener('mousedown', (e) => {
            if (e.target.closest('.field-controls')) return;

            isMoving = true;
            this.isDragging = true;

            const rect = field.getBoundingClientRect();
            const canvasRect = this.certificateCanvas.getBoundingClientRect();

            this.dragOffset.x = e.clientX - rect.left;
            this.dragOffset.y = e.clientY - rect.top;

            startX = e.clientX;
            startY = e.clientY;
            startLeft = parseInt(field.style.left);
            startTop = parseInt(field.style.top);

            field.classList.add('moving');
            field.style.cursor = 'grabbing';

            this.selectField(field, fieldKey);

            e.preventDefault();
        });

        document.addEventListener('mousemove', (e) => {
            if (isMoving && this.isDragging) {
                const canvasRect = this.certificateCanvas.getBoundingClientRect();
                const newX = e.clientX - canvasRect.left - this.dragOffset.x;
                const newY = e.clientY - canvasRect.top - this.dragOffset.y;

                const maxX = this.certificateCanvas.offsetWidth - field.offsetWidth;
                const maxY = this.certificateCanvas.offsetHeight - field.offsetHeight;

                const constrainedX = Math.max(0, Math.min(newX, maxX));
                const constrainedY = Math.max(0, Math.min(newY, maxY));

                field.style.left = constrainedX + 'px';
                field.style.top = constrainedY + 'px';

                this.updatePositionIndicator(field, constrainedX, constrainedY);
            }
        });

        document.addEventListener('mouseup', () => {
            if (isMoving) {
                isMoving = false;
                this.isDragging = false;
                field.classList.remove('moving');
                field.style.cursor = 'move';

                const finalX = parseInt(field.style.left);
                const finalY = parseInt(field.style.top);

                this.templateData[fieldKey].x = finalX;
                this.templateData[fieldKey].y = finalY;

                this.autoSave();
            }
        });
    }

    selectField(field, fieldKey) {
        // Désélectionner tous les autres champs
        document.querySelectorAll('.certificate-field-button').forEach(f => {
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

        if (controls.fontFamily) {
            controls.fontFamily.addEventListener('change', (e) => {
                this.applyStyle('font_family', e.target.value);
            });
        }

        if (controls.fontSize) {
            controls.fontSize.addEventListener('input', (e) => {
                if (controls.fontSizeValue) {
                    controls.fontSizeValue.textContent = e.target.value + 'px';
                }
                this.applyStyle('font_size', parseInt(e.target.value));
            });
        }

        if (controls.textColor) {
            controls.textColor.addEventListener('change', (e) => {
                this.applyStyle('color', e.target.value);
            });
        }

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

        this.templateData[fieldKey][property] = value;

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

        // Gérer le drop sur le canvas
        this.certificateCanvas.addEventListener('dragover', (e) => {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'copy';
        });

        this.certificateCanvas.addEventListener('drop', (e) => {
            e.preventDefault();
            const fieldKey = e.dataTransfer.getData('text/plain');
            this.handleFieldDrop(e, fieldKey);
        });
    }

    handleFieldDrop(e, fieldKey) {
        const canvasRect = this.certificateCanvas.getBoundingClientRect();
        const x = e.clientX - canvasRect.left;
        const y = e.clientY - canvasRect.top;

        // Mettre à jour la position du champ
        this.templateData[fieldKey].x = x;
        this.templateData[fieldKey].y = y;
        this.templateData[fieldKey].show = true;

        // Créer ou mettre à jour le champ
        let field = document.querySelector(`[data-field="${fieldKey}"]`);
        if (!field) {
            this.createField(fieldKey, this.templateData[fieldKey]);
        } else {
            field.style.left = x + 'px';
            field.style.top = y + 'px';
            this.updatePositionIndicator(field, x, y);
        }

        this.autoSave();

        console.log('🎯 Champ déposé:', fieldKey, 'à la position:', x, y);
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
        document.querySelectorAll('.certificate-field-button').forEach(f => {
            f.classList.remove('selected');
        });
        this.selectedField = null;
    }

    updatePositionIndicator(field, x, y) {
        const indicator = field.querySelector('.position-indicator');
        if (indicator) {
            indicator.textContent = `X: ${Math.round(x)}, Y: ${Math.round(y)}`;
        }
    }
}

// Fonction de debug
function debugInfo() {
    if (window.certificateEditorButtons) {
        console.log('🐛 Informations de debug avec Boutons:');
        console.log('- Template Data:', window.certificateEditorButtons.templateData);
        console.log('- Champ sélectionné:', window.certificateEditorButtons.selectedField);
        console.log('- Niveau de zoom:', window.certificateEditorButtons.zoomLevel);
        console.log('- Canvas dimensions:', {
            width: window.certificateEditorButtons.certificateCanvas.offsetWidth,
            height: window.certificateEditorButtons.certificateCanvas.offsetHeight
        });
    } else {
        console.log('❌ Certificate Editor avec Boutons non initialisé');
    }
}

// Initialiser l'éditeur avec boutons
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Démarrage Certificate Editor avec Boutons...');

    if (document.getElementById('certificateCanvas') && document.getElementById('certificateOverlay')) {
        window.certificateEditorButtons = new CertificateEditorButtons();
    } else {
        console.error('❌ Éléments de l\'éditeur de certificats non trouvés');
    }
});

// Exporter pour utilisation globale
window.CertificateEditorButtons = CertificateEditorButtons;