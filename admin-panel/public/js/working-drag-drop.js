/**
 * Système de Drag & Drop fonctionnel pour l'éditeur de certificats
 * Gère le positionnement des attributs : full_name, serial_number, date, qr_code
 */

class CertificateEditor {
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
            name_student: { x: 100, y: 200, width: 200, height: 30, show: true, text: 'Nom de l\'Étudiant', font_size: 16, color: '#000000', font_family: 'Arial' },
            date: { x: 100, y: 250, width: 150, height: 30, show: true, text: 'Date', font_size: 14, color: '#000000', font_family: 'Arial' },
            serial_number: { x: 100, y: 300, width: 180, height: 30, show: true, text: 'Numéro de Série', font_size: 12, color: '#666666', font_family: 'Arial' },
            qr_code: { x: 400, y: 300, width: 80, height: 80, show: true, text: 'QR', font_size: 10, color: '#000000', font_family: 'Arial' }
        };

        this.init();
    }

    init() {
        console.log('🚀 Initialisation de l\'éditeur de certificats');

        // Charger les données existantes si disponibles
        this.loadExistingData();

        // Initialiser les champs
        this.initializeFields();

        // Configurer les événements
        this.setupEventListeners();

        // Configurer les contrôles de style
        this.setupStyleControls();

        // Configurer le test de génération
        this.setupTestGeneration();

        console.log('✅ Éditeur de certificats initialisé avec succès');
    }

    loadExistingData() {
        // Charger les données du template depuis le serveur si disponibles
        const certifId = window.location.pathname.split('/').pop();
        if (certifId && certifId !== 'edit') {
            fetch(`/certifs/${certifId}/template-data`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.template_data) {
                        this.templateData = {...this.templateData, ...data.template_data };
                        console.log('📥 Données du template chargées:', this.templateData);
                    }
                })
                .catch(error => {
                    console.warn('⚠️ Impossible de charger les données du template:', error);
                });
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

        console.log('🎯 Champs initialisés:', Object.keys(this.templateData));
    }

    createField(fieldKey, fieldData) {
        const field = document.createElement('div');
        field.className = 'certificate-field';
        field.dataset.field = fieldKey;
        field.style.left = fieldData.x + 'px';
        field.style.top = fieldData.y + 'px';
        field.style.width = fieldData.width + 'px';
        field.style.height = fieldData.height + 'px';
        field.style.fontSize = fieldData.font_size + 'px';
        field.style.color = fieldData.color;
        field.style.fontFamily = fieldData.font_family;
        field.style.fontWeight = fieldData.font_weight || 'normal';
        field.style.fontStyle = fieldData.font_style || 'normal';
        field.style.textDecoration = fieldData.text_decoration || 'none';

        // Contenu du champ
        field.innerHTML = `
            <span class="field-content">${fieldData.text}</span>
            <div class="position-indicator">
                X: ${fieldData.x}, Y: ${fieldData.y}
            </div>
            <div class="drag-handle">
                <i class="fas fa-grip-vertical"></i>
            </div>
        `;

        // Ajouter au canvas
        this.certificateOverlay.appendChild(field);

        // Configurer les événements de drag & drop
        this.setupFieldEvents(field, fieldKey);
    }

    setupFieldEvents(field, fieldKey) {
        let isDragging = false;
        let startX, startY, startLeft, startTop;

        // Événement de sélection
        field.addEventListener('click', (e) => {
            e.stopPropagation();
            this.selectField(field, fieldKey);
        });

        // Événements de drag & drop
        field.addEventListener('mousedown', (e) => {
            if (e.target.closest('.drag-handle') || e.target === field) {
                isDragging = true;
                this.isDragging = true;

                // Calculer l'offset
                const rect = field.getBoundingClientRect();
                const canvasRect = this.certificateCanvas.getBoundingClientRect();

                this.dragOffset.x = e.clientX - rect.left;
                this.dragOffset.y = e.clientY - rect.top;

                // Position de départ
                startX = e.clientX;
                startY = e.clientY;
                startLeft = parseInt(field.style.left);
                startTop = parseInt(field.style.top);

                field.classList.add('dragging');
                field.style.cursor = 'grabbing';

                // Sélectionner le champ
                this.selectField(field, fieldKey);

                e.preventDefault();
            }
        });

        // Événement de mouvement de la souris
        document.addEventListener('mousemove', (e) => {
            if (isDragging && this.isDragging) {
                const canvasRect = this.certificateCanvas.getBoundingClientRect();
                const newX = e.clientX - canvasRect.left - this.dragOffset.x;
                const newY = e.clientY - canvasRect.top - this.dragOffset.y;

                // Permettre le positionnement libre sans limite
                const constrainedX = newX;
                const constrainedY = newY;

                field.style.left = constrainedX + 'px';
                field.style.top = constrainedY + 'px';

                // Mettre à jour l'indicateur de position
                const positionIndicator = field.querySelector('.position-indicator');
                if (positionIndicator) {
                    positionIndicator.textContent = `X: ${Math.round(constrainedX)}, Y: ${Math.round(constrainedY)}`;
                }

                // Mettre à jour les données du template
                this.templateData[fieldKey].x = Math.round(constrainedX);
                this.templateData[fieldKey].y = Math.round(constrainedY);
            }
        });

        // Événement de relâchement
        document.addEventListener('mouseup', () => {
            if (isDragging) {
                isDragging = false;
                this.isDragging = false;
                field.classList.remove('dragging');
                field.style.cursor = 'move';

                // Sauvegarder automatiquement
                this.autoSave();
            }
        });

        // Événement de survol pour l'indicateur de position
        field.addEventListener('mouseenter', () => {
            const positionIndicator = field.querySelector('.position-indicator');
            if (positionIndicator) {
                positionIndicator.style.opacity = '1';
            }
        });

        field.addEventListener('mouseleave', () => {
            const positionIndicator = field.querySelector('.position-indicator');
            if (positionIndicator) {
                positionIndicator.style.opacity = '0';
            }
        });
    }

    selectField(field, fieldKey) {
        // Désélectionner tous les autres champs
        document.querySelectorAll('.certificate-field').forEach(f => {
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

        // Mettre à jour les contrôles de style
        const fontFamily = document.getElementById('fontFamily');
        const fontSize = document.getElementById('fontSize');
        const fontSizeValue = document.getElementById('fontSizeValue');
        const textColor = document.getElementById('textColor');
        const bold = document.getElementById('bold');
        const italic = document.getElementById('italic');
        const underline = document.getElementById('underline');

        if (fontFamily) fontFamily.value = fieldData.font_family || 'Arial';
        if (fontSize) {
            fontSize.value = fieldData.font_size || 16;
            if (fontSizeValue) fontSizeValue.textContent = (fieldData.font_size || 16) + 'px';
        }
        if (textColor) textColor.value = fieldData.color || '#000000';
        if (bold) bold.checked = fieldData.font_weight === 'bold';
        if (italic) italic.checked = fieldData.font_style === 'italic';
        if (underline) underline.checked = fieldData.text_decoration === 'underline';
    }

    setupStyleControls() {
        // Contrôle de la police
        const fontFamily = document.getElementById('fontFamily');
        if (fontFamily) {
            fontFamily.addEventListener('change', (e) => {
                if (this.selectedField) {
                    this.applyStyle('font_family', e.target.value);
                }
            });
        }

        // Contrôle de la taille
        const fontSize = document.getElementById('fontSize');
        const fontSizeValue = document.getElementById('fontSizeValue');
        if (fontSize) {
            fontSize.addEventListener('input', (e) => {
                if (fontSizeValue) {
                    fontSizeValue.textContent = e.target.value + 'px';
                }
                if (this.selectedField) {
                    this.applyStyle('font_size', parseInt(e.target.value));
                }
            });
        }

        // Contrôle de la couleur
        const textColor = document.getElementById('textColor');
        if (textColor) {
            textColor.addEventListener('change', (e) => {
                if (this.selectedField) {
                    this.applyStyle('color', e.target.value);
                }
            });
        }

        // Contrôles de style
        const bold = document.getElementById('bold');
        const italic = document.getElementById('italic');
        const underline = document.getElementById('underline');

        if (bold) {
            bold.addEventListener('change', (e) => {
                if (this.selectedField) {
                    this.applyStyle('font_weight', e.target.checked ? 'bold' : 'normal');
                }
            });
        }

        if (italic) {
            italic.addEventListener('change', (e) => {
                if (this.selectedField) {
                    this.applyStyle('font_style', e.target.checked ? 'italic' : 'normal');
                }
            });
        }

        if (underline) {
            underline.addEventListener('change', (e) => {
                if (this.selectedField) {
                    this.applyStyle('text_decoration', e.target.checked ? 'underline' : 'none');
                }
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
            // Créer le champ s'il n'existe pas
            if (!document.querySelector(`[data-field="${fieldKey}"]`)) {
                this.createField(fieldKey, this.templateData[fieldKey]);
            }
        } else {
            // Supprimer le champ
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

        // Récupérer l'ID du certificat depuis l'URL
        const certifId = window.location.pathname.split('/').pop();

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
        // Sauvegarder automatiquement après un délai
        clearTimeout(this.autoSaveTimeout);
        this.autoSaveTimeout = setTimeout(() => {
            this.saveTemplate(false); // Sauvegarde silencieuse
        }, 2000);
    }

    saveTemplate(showMessage = true) {
        const certifId = window.location.pathname.split('/').pop();

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
        // Créer une notification de succès
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
        // Créer une notification d'erreur
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
}

// Fonction de debug
function debugInfo() {
    if (window.certificateEditor) {
        console.log('🐛 Informations de debug:');
        console.log('- Template Data:', window.certificateEditor.templateData);
        console.log('- Champ sélectionné:', window.certificateEditor.selectedField);
        console.log('- Niveau de zoom:', window.certificateEditor.zoomLevel);
        console.log('- Canvas dimensions:', {
            width: window.certificateEditor.certificateCanvas.offsetWidth,
            height: window.certificateEditor.certificateCanvas.offsetHeight
        });
    } else {
        console.log('❌ Éditeur de certificats non initialisé');
    }
}

// Initialiser l'éditeur quand le DOM est prêt
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Démarrage de l\'éditeur de certificats...');

    // Vérifier que les éléments nécessaires existent
    if (document.getElementById('certificateCanvas') && document.getElementById('certificateOverlay')) {
        window.certificateEditor = new CertificateEditor();
    } else {
        console.error('❌ Éléments de l\'éditeur de certificats non trouvés');
    }
});

// Exporter pour utilisation globale
window.CertificateEditor = CertificateEditor;