/**
 * Système de Test d'Intégration pour l'Éditeur de Certificats
 * Tests automatisés et validation en temps réel
 */

class CertificateTestIntegration {
    constructor(editor) {
        this.editor = editor;
        this.testResults = [];
        this.isRunning = false;
        this.setupTestInterface();
    }

    setupTestInterface() {
        this.createTestPanel();
        this.setupTestListeners();
    }

    createTestPanel() {
        // Ajouter un onglet de test avancé
        const testTab = document.getElementById('test-tab');
        if (testTab) {
            testTab.innerHTML = `
                <i class="fas fa-vial me-1"></i>Tests
            `;
        }

        // Enrichir l'onglet test
        const testContent = document.getElementById('test');
        if (testContent) {
            testContent.innerHTML = `
                <div class="p-3">
                    <h6 class="text-muted mb-3">Tests d'Intégration</h6>
                    
                    <!-- Tests automatiques -->
                    <div class="mb-4">
                        <h6 class="text-primary">Tests Automatiques</h6>
                        <div class="test-buttons">
                            <button class="btn btn-sm btn-outline-primary me-2" id="testPositions">
                                <i class="fas fa-crosshairs me-1"></i>Positions
                            </button>
                            <button class="btn btn-sm btn-outline-success me-2" id="testStyles">
                                <i class="fas fa-palette me-1"></i>Styles
                            </button>
                            <button class="btn btn-sm btn-outline-warning me-2" id="testGeneration">
                                <i class="fas fa-cogs me-1"></i>Génération
                            </button>
                            <button class="btn btn-sm btn-outline-info" id="runAllTests">
                                <i class="fas fa-play-circle me-1"></i>Tout Tester
                            </button>
                        </div>
                    </div>

                    <!-- Résultats des tests -->
                    <div class="mb-4">
                        <h6 class="text-success">Résultats</h6>
                        <div id="testResults" class="test-results">
                            <div class="text-muted">Aucun test exécuté</div>
                        </div>
                    </div>

                    <!-- Test de génération manuel -->
                    <div class="mb-3">
                        <h6 class="text-info">Test Manuel</h6>
                        <div class="mb-3">
                            <label class="form-label">Nom de test</label>
                            <input type="text" class="form-control" id="testName" value="Jean Dupont" placeholder="Nom de l'étudiant">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date de test</label>
                            <input type="date" class="form-control" id="testDate" value="${new Date().toISOString().split('T')[0]}">
                        </div>
                        <button class="btn btn-success w-100 mb-3" id="generateTest">
                            <i class="fas fa-play me-2"></i>
                            Générer le Test
                        </button>
                        <div id="testResult" class="d-none">
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                Certificat généré avec succès !
                            </div>
                            <a href="#" id="downloadLink" class="btn btn-primary w-100">
                                <i class="fas fa-download me-2"></i>
                                Télécharger
                            </a>
                        </div>
                    </div>

                    <!-- Statistiques -->
                    <div class="test-stats">
                        <h6 class="text-secondary">Statistiques</h6>
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="stat-item">
                                    <div class="stat-value" id="totalTests">0</div>
                                    <div class="stat-label">Tests</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-value text-success" id="passedTests">0</div>
                                <div class="stat-label">Réussis</div>
                            </div>
                            <div class="col-4">
                                <div class="stat-value text-danger" id="failedTests">0</div>
                                <div class="stat-label">Échoués</div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    }

    setupTestListeners() {
        // Tests automatiques
        document.getElementById('testPositions') ? .addEventListener('click', () => this.testPositions());
        document.getElementById('testStyles') ? .addEventListener('click', () => this.testStyles());
        document.getElementById('testGeneration') ? .addEventListener('click', () => this.testGeneration());
        document.getElementById('runAllTests') ? .addEventListener('click', () => this.runAllTests());

        // Test manuel
        document.getElementById('generateTest') ? .addEventListener('click', () => this.generateManualTest());
    }

    async testPositions() {
        this.addTestResult('testPositions', 'Test des positions des champs', 'running');

        const results = [];
        const fields = document.querySelectorAll('.certificate-field');

        fields.forEach(field => {
            const fieldId = field.dataset.field;
            const rect = field.getBoundingClientRect();
            const canvasRect = this.editor.canvas.getBoundingClientRect();

            const x = rect.left - canvasRect.left;
            const y = rect.top - canvasRect.top;

            // Vérifier que le champ est dans les limites
            const isInBounds = x >= 0 && y >= 0 &&
                x + rect.width <= this.editor.config.imageWidth &&
                y + rect.height <= this.editor.config.imageHeight;

            results.push({
                field: fieldId,
                position: { x: Math.round(x), y: Math.round(y) },
                inBounds: isInBounds,
                valid: isInBounds
            });
        });

        const allValid = results.every(r => r.valid);
        this.addTestResult('testPositions', 'Test des positions des champs',
            allValid ? 'passed' : 'failed', results);

        return results;
    }

    async testStyles() {
        this.addTestResult('testStyles', 'Test des styles des champs', 'running');

        const results = [];
        const fields = document.querySelectorAll('.certificate-field');

        fields.forEach(field => {
            const fieldId = field.dataset.field;
            const styles = {
                fontSize: field.style.fontSize,
                color: field.style.color,
                fontFamily: field.style.fontFamily,
                display: field.style.display
            };

            // Vérifier que les styles sont cohérents
            const hasValidFontSize = styles.fontSize && parseInt(styles.fontSize) > 0;
            const hasValidColor = styles.color && styles.color !== '';
            const isVisible = styles.display !== 'none';

            results.push({
                field: fieldId,
                styles: styles,
                valid: hasValidFontSize && hasValidColor && isVisible
            });
        });

        const allValid = results.every(r => r.valid);
        this.addTestResult('testStyles', 'Test des styles des champs',
            allValid ? 'passed' : 'failed', results);

        return results;
    }

    async testGeneration() {
        this.addTestResult('testGeneration', 'Test de génération de certificat', 'running');

        try {
            // Simuler une génération de test
            const testData = {
                template_data: this.editor.config.templateData,
                test_name: 'Test Integration',
                test_date: new Date().toISOString().split('T')[0]
            };

            const response = await fetch(window.testUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(testData)
            });

            const result = await response.json();

            if (result.success) {
                this.addTestResult('testGeneration', 'Test de génération de certificat',
                    'passed', {
                        serial_number: result.serial_number,
                        file_path: result.file_path,
                        download_url: result.download_url
                    });
                return true;
            } else {
                this.addTestResult('testGeneration', 'Test de génération de certificat',
                    'failed', { error: result.message });
                return false;
            }
        } catch (error) {
            this.addTestResult('testGeneration', 'Test de génération de certificat',
                'failed', { error: error.message });
            return false;
        }
    }

    async runAllTests() {
        if (this.isRunning) return;

        this.isRunning = true;
        this.clearTestResults();

        const tests = [
            () => this.testPositions(),
            () => this.testStyles(),
            () => this.testGeneration()
        ];

        for (const test of tests) {
            try {
                await test();
                await new Promise(resolve => setTimeout(resolve, 500)); // Pause entre les tests
            } catch (error) {
                console.error('Erreur lors du test:', error);
            }
        }

        this.isRunning = false;
        this.updateStatistics();
    }

    generateManualTest() {
        const testName = document.getElementById('testName').value;
        const testDate = document.getElementById('testDate').value;

        if (!testName) {
            this.showNotification('Veuillez entrer un nom de test', 'warning');
            return;
        }

        // Utiliser la méthode de l'éditeur
        this.editor.generateTest();
    }

    addTestResult(testId, testName, status, details = null) {
        const result = {
            id: testId,
            name: testName,
            status: status,
            details: details,
            timestamp: new Date()
        };

        this.testResults.push(result);
        this.updateTestResults();
    }

    updateTestResults() {
            const container = document.getElementById('testResults');
            if (!container) return;

            if (this.testResults.length === 0) {
                container.innerHTML = '<div class="text-muted">Aucun test exécuté</div>';
                return;
            }

            const html = this.testResults.map(result => {
                        const statusIcon = result.status === 'passed' ? 'check-circle text-success' :
                            result.status === 'failed' ? 'times-circle text-danger' :
                            'clock text-warning';

                        return `
                <div class="test-result-item mb-2 p-2 border rounded">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-${statusIcon} me-2"></i>
                        <span class="flex-grow-1">${result.name}</span>
                        <small class="text-muted">${result.timestamp.toLocaleTimeString()}</small>
                    </div>
                    ${result.details ? `<div class="test-details mt-1 small text-muted">${this.formatTestDetails(result.details)}</div>` : ''}
                </div>
            `;
        }).join('');
        
        container.innerHTML = html;
    }

    formatTestDetails(details) {
        if (typeof details === 'object') {
            return Object.entries(details)
                .map(([key, value]) => `${key}: ${JSON.stringify(value)}`)
                .join(', ');
        }
        return details;
    }

    clearTestResults() {
        this.testResults = [];
        this.updateTestResults();
    }

    updateStatistics() {
        const total = this.testResults.length;
        const passed = this.testResults.filter(r => r.status === 'passed').length;
        const failed = this.testResults.filter(r => r.status === 'failed').length;
        
        document.getElementById('totalTests').textContent = total;
        document.getElementById('passedTests').textContent = passed;
        document.getElementById('failedTests').textContent = failed;
    }

    showNotification(message, type) {
        // Utiliser la méthode de l'éditeur
        this.editor.showNotification(message, type);
    }
}

// Styles CSS pour les tests
const testStyles = `
    .test-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }
    
    .test-results {
        max-height: 200px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 10px;
        background: #f8f9fa;
    }
    
    .test-result-item {
        background: white;
    }
    
    .test-details {
        font-family: monospace;
        background: #f1f3f4;
        padding: 4px 8px;
        border-radius: 3px;
        margin-top: 4px;
    }
    
    .test-stats {
        border-top: 1px solid #dee2e6;
        padding-top: 15px;
    }
    
    .stat-item {
        text-align: center;
    }
    
    .stat-value {
        font-size: 1.5em;
        font-weight: bold;
    }
    
    .stat-label {
        font-size: 0.8em;
        color: #6c757d;
        text-transform: uppercase;
    }
`;

// Ajouter les styles
const styleSheet = document.createElement('style');
styleSheet.textContent = testStyles;
document.head.appendChild(styleSheet);

// Exporter la classe
window.CertificateTestIntegration = CertificateTestIntegration;