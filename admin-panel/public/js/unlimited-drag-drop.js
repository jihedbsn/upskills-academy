/**
 * Système de Drag & Drop sans limite pour l'éditeur de certificats
 * Permet le positionnement libre des éléments sans contraintes de limites
 */

class UnlimitedDragDrop {
    constructor() {
        this.isDragging = false;
        this.draggedElement = null;
        this.offset = { x: 0, y: 0 };
        this.unlimitedMode = true; // Mode sans limite activé par défaut
        
        this.init();
    }

    init() {
        console.log('🚀 Initialisation du système de drag & drop sans limite');
        
        // Ajouter les contrôles d'interface
        this.addUnlimitedControls();
        
        // Initialiser le drag & drop sur tous les éléments draggables
        this.setupDragAndDrop();
        
        console.log('✅ Système de drag & drop sans limite initialisé');
    }

    addUnlimitedControls() {
        // Créer un panneau de contrôle pour le mode sans limite
        const controlPanel = document.createElement('div');
        controlPanel.id = 'unlimitedControls';
        controlPanel.className = 'unlimited-controls';
        controlPanel.style.cssText = `
            position: fixed;
            top: 10px;
            left: 10px;
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 10000;
            font-family: Arial, sans-serif;
            font-size: 12px;
        `;

        controlPanel.innerHTML = `
            <div style="margin-bottom: 8px;">
                <strong>🎯 Mode de Positionnement</strong>
            </div>
            <label style="display: flex; align-items: center; cursor: pointer; margin-bottom: 5px;">
                <input type="radio" name="positioningMode" value="unlimited" checked style="margin-right: 5px;">
                <span>Positionnement libre (sans limite)</span>
            </label>
            <label style="display: flex; align-items: center; cursor: pointer;">
                <input type="radio" name="positioningMode" value="limited" style="margin-right: 5px;">
                <span>Positionnement limité (dans les limites)</span>
            </label>
            <div style="margin-top: 8px; padding-top: 8px; border-top: 1px solid #eee;">
                <button id="resetPositions" style="
                    background: #dc3545;
                    color: white;
                    border: none;
                    padding: 4px 8px;
                    border-radius: 4px;
                    cursor: pointer;
                    font-size: 11px;
                ">Réinitialiser positions</button>
            </div>
        `;

        document.body.appendChild(controlPanel);

        // Écouter les changements de mode
        controlPanel.addEventListener('change', (e) => {
            if (e.target.name === 'positioningMode') {
                this.unlimitedMode = e.target.value === 'unlimited';
                console.log('🔄 Mode de positionnement changé:', this.unlimitedMode ? 'Sans limite' : 'Avec limite');
                this.updateModeIndicator();
            }
        });

        // Bouton de réinitialisation
        document.getElementById('resetPositions').addEventListener('click', () => {
            this.resetAllPositions();
        });

        this.updateModeIndicator();
    }

    updateModeIndicator() {
        const controlPanel = document.getElementById('unlimitedControls');
        const modeText = controlPanel.querySelector('strong');
        modeText.textContent = this.unlimitedMode ? '🎯 Positionnement libre' : '🎯 Positionnement limité';
    }

    setupDragAndDrop() {
        // Rendre tous les éléments draggables
        const draggableElements = document.querySelectorAll('.draggable-field');
        
        draggableElements.forEach(element => {
            this.makeDraggable(element);
        });

        console.log(`🎯 ${draggableElements.length} éléments rendus draggables`);
    }

    makeDraggable(element) {
        let isPointerDown = false;
        let startX = 0, startY = 0;
        let origLeft = 0, origTop = 0;

        element.style.touchAction = 'none';
        element.style.cursor = 'move';

        element.addEventListener('pointerdown', (e) => {
            if (e.button !== 0 && e.pointerType !== 'touch') return;
            
            isPointerDown = true;
            this.isDragging = true;
            this.draggedElement = element;
            
            element.classList.add('dragging');
            element.style.cursor = 'grabbing';
            
            const rect = element.getBoundingClientRect();
            origLeft = parseFloat(element.style.left) || 0;
            origTop = parseFloat(element.style.top) || 0;
            startX = e.clientX;
            startY = e.clientY;
            
            if (element.setPointerCapture) {
                element.setPointerCapture(e.pointerId);
            }
            
            e.preventDefault();
        });

        document.addEventListener('pointermove', (e) => {
            if (!isPointerDown || !this.isDragging) return;
            
            const dx = e.clientX - startX;
            const dy = e.clientY - startY;
            
            let newLeft = origLeft + dx;
            let newTop = origTop + dy;
            
            // Appliquer les limites seulement si le mode limité est activé
            if (!this.unlimitedMode) {
                const container = document.getElementById('certificate-div');
                if (container) {
                    const containerRect = container.getBoundingClientRect();
                    const fieldRect = element.getBoundingClientRect();
                    
                    const maxLeft = containerRect.width - fieldRect.width;
                    const maxTop = containerRect.height - fieldRect.height;
                    
                    newLeft = Math.max(0, Math.min(newLeft, maxLeft));
                    newTop = Math.max(0, Math.min(newTop, maxTop));
                }
            }
            
            // Utiliser transform pour une performance optimale pendant le drag
            element.style.transform = `translate(${dx}px, ${dy}px)`;
            
            // Mettre à jour l'indicateur de position
            this.updatePositionIndicator(element, newLeft, newTop);
        });

        document.addEventListener('pointerup', (e) => {
            if (!isPointerDown) return;
            
            isPointerDown = false;
            this.isDragging = false;
            
            const dx = e.clientX - startX;
            const dy = e.clientY - startY;
            
            let finalLeft = origLeft + dx;
            let finalTop = origTop + dy;
            
            // Appliquer les limites seulement si le mode limité est activé
            if (!this.unlimitedMode) {
                const container = document.getElementById('certificate-div');
                if (container) {
                    const containerRect = container.getBoundingClientRect();
                    const fieldRect = element.getBoundingClientRect();
                    
                    const maxLeft = containerRect.width - fieldRect.width;
                    const maxTop = containerRect.height - fieldRect.height;
                    
                    finalLeft = Math.max(0, Math.min(finalLeft, maxLeft));
                    finalTop = Math.max(0, Math.min(finalTop, maxTop));
                }
            }
            
            // Appliquer la position finale
            element.style.left = finalLeft + 'px';
            element.style.top = finalTop + 'px';
            element.style.transform = '';
            element.classList.remove('dragging');
            element.style.cursor = 'move';
            
            // Mettre à jour les champs de formulaire
            this.updateFormFields(element, finalLeft, finalTop);
            
            console.log(`✅ Position mise à jour: ${finalLeft}, ${finalTop}`);
        });
    }

    updatePositionIndicator(element, x, y) {
        // Créer ou mettre à jour l'indicateur de position
        let indicator = element.querySelector('.position-indicator');
        if (!indicator) {
            indicator = document.createElement('div');
            indicator.className = 'position-indicator';
            indicator.style.cssText = `
                position: absolute;
                top: -20px;
                left: 0;
                background: rgba(0, 0, 0, 0.7);
                color: white;
                padding: 2px 6px;
                border-radius: 3px;
                font-size: 10px;
                font-family: monospace;
                pointer-events: none;
                z-index: 1000;
            `;
            element.appendChild(indicator);
        }
        
        indicator.textContent = `X: ${Math.round(x)}, Y: ${Math.round(y)}`;
        indicator.style.display = this.isDragging ? 'block' : 'none';
    }

    updateFormFields(element, x, y) {
        const fieldId = element.id;
        if (!fieldId) return;
        
        const fieldName = fieldId.replace('Div', '');
        
        // Mettre à jour les champs X et Y
        const xField = document.getElementById(fieldName + 'X');
        const yField = document.getElementById(fieldName + 'Y');
        
        if (xField) xField.value = Math.round(x);
        if (yField) yField.value = Math.round(y);
        
        // Mettre à jour l'affichage de position
        const positionDisplay = document.getElementById('position');
        if (positionDisplay) {
            positionDisplay.innerHTML = `X coords: ${Math.round(x)}, Y coords: ${Math.round(y)}`;
        }
        
        // Mettre à jour l'élément actif
        const activeElement = document.getElementById('activeElement');
        if (activeElement) {
            activeElement.value = fieldName;
        }
        
        // Sauvegarder automatiquement
        if (typeof saveCertificateData === 'function') {
            setTimeout(() => saveCertificateData(), 1000);
        }
    }

    resetAllPositions() {
        if (!confirm('Êtes-vous sûr de vouloir réinitialiser toutes les positions des éléments ?')) {
            return;
        }
        
        const draggableElements = document.querySelectorAll('.draggable-field');
        const defaultPositions = {
            'name_student': { x: 100, y: 100 },
            'date': { x: 100, y: 150 },
            'serial_number': { x: 100, y: 200 },
            'qr_code': { x: 100, y: 250 }
        };
        
        draggableElements.forEach(element => {
            const fieldName = element.id.replace('Div', '');
            const defaultPos = defaultPositions[fieldName] || { x: 100, y: 100 };
            
            element.style.left = defaultPos.x + 'px';
            element.style.top = defaultPos.y + 'px';
            element.style.transform = '';
            
            this.updateFormFields(element, defaultPos.x, defaultPos.y);
        });
        
        console.log('🔄 Toutes les positions ont été réinitialisées');
    }

    // Méthode pour activer/désactiver le mode sans limite
    setUnlimitedMode(enabled) {
        this.unlimitedMode = enabled;
        
        // Mettre à jour l'interface
        const radioButtons = document.querySelectorAll('input[name="positioningMode"]');
        radioButtons.forEach(radio => {
            radio.checked = (radio.value === 'unlimited' && enabled) || 
                          (radio.value === 'limited' && !enabled);
        });
        
        this.updateModeIndicator();
        console.log('🔄 Mode sans limite:', enabled ? 'Activé' : 'Désactivé');
    }
}

// Initialiser le système quand le DOM est prêt
document.addEventListener('DOMContentLoaded', function() {
    // Attendre que les éléments draggables soient créés
    setTimeout(() => {
        if (document.querySelector('.draggable-field')) {
            window.unlimitedDragDrop = new UnlimitedDragDrop();
        }
    }, 1000);
});

// Exporter pour utilisation globale
window.UnlimitedDragDrop = UnlimitedDragDrop;
