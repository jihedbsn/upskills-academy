/**
 * Script de test pour Certificate Editor V2
 * Vérifie que toutes les fonctionnalités fonctionnent correctement
 */

class CertificateEditorTest {
    constructor() {
        this.tests = [];
        this.results = [];
    }

    runAllTests() {
        console.log('🧪 Démarrage des tests Certificate Editor V2');

        this.tests = [
            { name: 'Initialisation', test: () => this.testInitialization() },
            { name: 'Drag & Drop', test: () => this.testDragAndDrop() },
            { name: 'Sélection de champs', test: () => this.testFieldSelection() },
            { name: 'Contrôles de style', test: () => this.testStyleControls() },
            { name: 'Grille magnétique', test: () => this.testGridSnapping() },
            { name: 'Sauvegarde', test: () => this.testSaveFunction() },
            { name: 'Génération de test', test: () => this.testGeneration() }
        ];

        this.runTests();
    }

    async runTests() {
        for (const test of this.tests) {
            try {
                console.log(`🔍 Test: ${test.name}`);
                const result = await test.test();
                this.results.push({ name: test.name, success: result, error: null });
                console.log(`${result ? '✅' : '❌'} ${test.name}: ${result ? 'SUCCÈS' : 'ÉCHEC'}`);
            } catch (error) {
                this.results.push({ name: test.name, success: false, error: error.message });
                console.error(`❌ ${test.name}: ERREUR - ${error.message}`);
            }
        }

        this.displayResults();
    }

    testInitialization() {
        const editor = window.certificateEditorV2;
        if (!editor) {
            throw new Error('Certificate Editor V2 non initialisé');
        }

        const requiredElements = [
            'certificateCanvas',
            'certificateOverlay',
            'certificateImage'
        ];

        for (const elementId of requiredElements) {
            if (!editor[elementId]) {
                throw new Error(`Élément manquant: ${elementId}`);
            }
        }

        if (!editor.templateData) {
            throw new Error('Données du template non initialisées');
        }

        return true;
    }

    testDragAndDrop() {
        const editor = window.certificateEditorV2;
        if (!editor) return false;

        // Vérifier que les champs sont draggables
        const fields = document.querySelectorAll('.certificate-field-v2');
        if (fields.length === 0) {
            throw new Error('Aucun champ draggable trouvé');
        }

        for (const field of fields) {
            if (!field.draggable) {
                throw new Error(`Champ non draggable: ${field.dataset.field}`);
            }
        }

        return true;
    }

    testFieldSelection() {
        const editor = window.certificateEditorV2;
        if (!editor) return false;

        const fields = document.querySelectorAll('.certificate-field-v2');
        if (fields.length === 0) {
            throw new Error('Aucun champ à sélectionner');
        }

        // Simuler une sélection
        const firstField = fields[0];
        firstField.click();

        if (!firstField.classList.contains('selected')) {
            throw new Error('Sélection de champ non fonctionnelle');
        }

        if (!editor.selectedField) {
            throw new Error('Champ sélectionné non enregistré');
        }

        return true;
    }

    testStyleControls() {
        const editor = window.certificateEditorV2;
        if (!editor) return false;

        const styleControls = [
            'fontFamily',
            'fontSize',
            'textColor',
            'bold',
            'italic',
            'underline'
        ];

        for (const controlId of styleControls) {
            const control = document.getElementById(controlId);
            if (!control) {
                throw new Error(`Contrôle de style manquant: ${controlId}`);
            }
        }

        return true;
    }

    testGridSnapping() {
        const editor = window.certificateEditorV2;
        if (!editor) return false;

        // Tester la fonction de grille
        const testValue = 15;
        const snappedValue = editor.snapToGrid(testValue);

        if (snappedValue !== 10) {
            throw new Error(`Grille magnétique non fonctionnelle: ${testValue} -> ${snappedValue} (attendu: 10)`);
        }

        return true;
    }

    testSaveFunction() {
        const editor = window.certificateEditorV2;
        if (!editor) return false;

        if (typeof editor.saveTemplate !== 'function') {
            throw new Error('Fonction de sauvegarde manquante');
        }

        if (typeof editor.autoSave !== 'function') {
            throw new Error('Fonction de sauvegarde automatique manquante');
        }

        return true;
    }

    testGeneration() {
        const editor = window.certificateEditorV2;
        if (!editor) return false;

        if (typeof editor.generateTestCertificate !== 'function') {
            throw new Error('Fonction de génération de test manquante');
        }

        return true;
    }

    displayResults() {
        const successCount = this.results.filter(r => r.success).length;
        const totalCount = this.results.length;

        console.log('\n📊 RÉSULTATS DES TESTS:');
        console.log(`✅ Succès: ${successCount}/${totalCount}`);
        console.log(`❌ Échecs: ${totalCount - successCount}/${totalCount}`);

        if (successCount === totalCount) {
            console.log('🎉 TOUS LES TESTS SONT PASSÉS !');
        } else {
            console.log('⚠️ CERTAINS TESTS ONT ÉCHOUÉ:');
            this.results.filter(r => !r.success).forEach(result => {
                console.log(`   - ${result.name}: ${result.error || 'Échec'}`);
            });
        }

        // Afficher les résultats dans l'interface
        this.showTestResults();
    }

    showTestResults() {
        const resultsContainer = document.createElement('div');
        resultsContainer.id = 'testResults';
        resultsContainer.className = 'alert alert-info position-fixed';
        resultsContainer.style.cssText = `
            top: 20px;
            left: 20px;
            z-index: 9999;
            min-width: 300px;
            max-width: 400px;
        `;

        const successCount = this.results.filter(r => r.success).length;
        const totalCount = this.results.length;

        resultsContainer.innerHTML = `
            <h6>🧪 Résultats des Tests V2</h6>
            <p><strong>Succès:</strong> ${successCount}/${totalCount}</p>
            <div class="progress mb-2">
                <div class="progress-bar ${successCount === totalCount ? 'bg-success' : 'bg-warning'}" 
                     style="width: ${(successCount / totalCount) * 100}%"></div>
            </div>
            <button class="btn btn-sm btn-outline-secondary" onclick="this.parentElement.remove()">
                Fermer
            </button>
        `;

        document.body.appendChild(resultsContainer);

        // Supprimer automatiquement après 10 secondes
        setTimeout(() => {
            if (resultsContainer.parentElement) {
                resultsContainer.remove();
            }
        }, 10000);
    }
}

// Fonction globale pour lancer les tests
function runCertificateEditorTests() {
    const tester = new CertificateEditorTest();
    tester.runAllTests();
}

// Lancer les tests automatiquement après 3 secondes
setTimeout(() => {
    if (window.certificateEditorV2) {
        runCertificateEditorTests();
    } else {
        console.warn('⚠️ Certificate Editor V2 non initialisé, tests reportés');
    }
}, 3000);

// Exporter pour utilisation globale
window.CertificateEditorTest = CertificateEditorTest;
window.runCertificateEditorTests = runCertificateEditorTests;