@extends('layouts.app')

@section('title', 'Student Import')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Student Import</h4>
                    <button type="button" class="btn btn-success" data-bs-toggle="offcanvas" data-bs-target="#addStudentManuallyOffcanvas">
                        <i class="fas fa-user-plus me-2"></i>
                        Ajouter Manuellement
                    </button>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger alert-dismissible fade show" id="session_error" role="alert" style="display: none;">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <span id="session_error_message"></span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    
                    <form id="importForm" action="{{ route('import-students.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Course Selection -->
                        <div class="mb-4">
                            <label for="product_id" class="form-label">
                                <i class="fas fa-book me-2"></i>
                                Select Course
                            </label>
                            <select class="form-select" id="product_id" name="product_id" required>
                                <option value="">-- Choose a course --</option>
                                @if(isset($products) && $products && $products->count() > 0)
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">
                                            {{ $product->titre ?? 'Course #' . $product->id }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="" disabled>No courses available</option>
                                @endif
                            </select>
                            <div class="invalid-feedback" id="product_id_error" style="display: none;"></div>
                        </div>

                        <!-- File Upload -->
                        <div class="mb-4">
                            <label for="excel_file" class="form-label">
                                <i class="fas fa-file-excel me-2"></i>
                                Excel File
                            </label>
                            <input type="file" class="form-control" id="excel_file" name="excel_file" 
                                   accept=".xlsx,.xls,.csv" required>
                            <div class="form-text">
                                Accepted formats: .xlsx, .xls, .csv
                            </div>
                            <div id="fileValidationMessage" class="mt-2" style="display: none;"></div>
                            <div class="invalid-feedback" id="excel_file_error" style="display: none;"></div>
                        </div>

                        <!-- Preview (Real-time) -->
                        <div class="mb-4" id="excelPreviewWrapper" style="display: none;">
                            <label class="form-label d-flex align-items-center mb-2">
                                <i class="fas fa-eye me-2"></i>
                                Excel File Preview
                                <span class="badge bg-info ms-2" id="previewBadge">0 rows</span>
                            </label>
                            
                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-striped table-hover" id="excelPreviewTable">
                                    <thead class="table-dark sticky-top">
                                        <tr id="previewHeaders"></tr>
                                    </thead>
                                    <tbody id="previewBody"></tbody>
                                </table>
                            </div>
                            
                            <div class="mt-2">
                                <small class="text-muted" id="previewMetadata"></small>
                            </div>
                            
                            <div class="mt-3">
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="clearPreview">
                                    <i class="fas fa-times me-1"></i> Clear Preview
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm" id="reloadPreview">
                                    <i class="fas fa-sync me-1"></i> Reload
                                </button>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-upload me-2"></i>
                                Import Students
                            </button>
                            
                            <div class="text-muted">
                                <small>
                                    <i class="fas fa-info-circle me-1"></i>
                                    A random password will be generated and sent by email.
                                </small>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Offcanvas for Manual Student Addition -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="addStudentManuallyOffcanvas" aria-labelledby="addStudentManuallyLabel" style="width: 500px;">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="addStudentManuallyLabel">
            <i class="fas fa-user-plus me-2"></i>
            Ajouter Étudiant Manuellement
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form id="manualAddForm">
            @csrf
            
            <!-- Course Selection -->
            <div class="mb-3">
                <label for="manual_product_id" class="form-label">
                    <i class="fas fa-book me-1"></i>
                    Cours <span class="text-danger">*</span>
                </label>
                <select class="form-select" id="manual_product_id" name="product_id" required>
                    <option value="">-- Choisir un cours --</option>
                    @if(isset($products) && $products && $products->count() > 0)
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">
                                {{ $product->titre ?? 'Course #' . $product->id }}
                            </option>
                        @endforeach
                    @else
                        <option value="" disabled>Aucun cours disponible</option>
                    @endif
                </select>
                <div class="invalid-feedback" id="manual_product_id_error"></div>
            </div>

            <!-- First Name -->
            <div class="mb-3">
                <label for="manual_first_name" class="form-label">
                    <i class="fas fa-user me-1"></i>
                    Prénom <span class="text-danger">*</span>
                </label>
                <input type="text" class="form-control" id="manual_first_name" name="first_name" required>
                <div class="invalid-feedback" id="manual_first_name_error"></div>
            </div>

            <!-- Last Name -->
            <div class="mb-3">
                <label for="manual_last_name" class="form-label">
                    <i class="fas fa-user me-1"></i>
                    Nom <span class="text-danger">*</span>
                </label>
                <input type="text" class="form-control" id="manual_last_name" name="last_name" required>
                <div class="invalid-feedback" id="manual_last_name_error"></div>
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label for="manual_email" class="form-label">
                    <i class="fas fa-envelope me-1"></i>
                    Email <span class="text-danger">*</span>
                </label>
                <input type="email" class="form-control" id="manual_email" name="email" required>
                <div class="invalid-feedback" id="manual_email_error"></div>
            </div>

            <!-- Phone -->
            <div class="mb-3">
                <label for="manual_phone" class="form-label">
                    <i class="fas fa-phone me-1"></i>
                    Téléphone <span class="text-danger">*</span>
                </label>
                <input type="text" class="form-control" id="manual_phone" name="phone" required>
                <div class="invalid-feedback" id="manual_phone_error"></div>
            </div>

            <!-- Info Box -->
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <small>Un mot de passe aléatoire sera généré et envoyé par email.</small>
            </div>

            <!-- Submit Button -->
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-success" id="manualAddBtn">
                    <i class="fas fa-plus me-2"></i>
                    Ajouter Étudiant
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">
                    <i class="fas fa-times me-2"></i>
                    Annuler
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay" style="display: none;">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
    <div class="mt-2">Processing...</div>
</div>

<style>
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    color: white;
}

.table-responsive {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
}

.error-row {
    background-color: #f8d7da !important;
    color: #721c24;
}

.missing-header {
    background-color: #f8d7da !important;
    color: #721c24;
}

.valid-header {
    background-color: #d1e7dd !important;
    color: #0f5132;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('excel_file');
    const previewWrapper = document.getElementById('excelPreviewWrapper');
    const previewTable = document.getElementById('excelPreviewTable');
    const previewHeaders = document.getElementById('previewHeaders');
    const previewBody = document.getElementById('previewBody');
    const previewBadge = document.getElementById('previewBadge');
    const previewMetadata = document.getElementById('previewMetadata');
    const clearPreviewBtn = document.getElementById('clearPreview');
    const reloadPreviewBtn = document.getElementById('reloadPreview');
    const importForm = document.getElementById('importForm');
    const submitBtn = document.getElementById('submitBtn');
    const loadingOverlay = document.getElementById('loadingOverlay');

    // Charger SheetJS dynamiquement
    function loadSheetJS() {
        return new Promise((resolve, reject) => {
            if (typeof XLSX !== 'undefined') {
                resolve();
                return;
            }
            
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js';
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    // Gestionnaire de sélection de fichier
    fileInput.addEventListener('change', async function(e) {
        const file = e.target.files[0];
        if (!file) {
            hidePreview();
            return;
        }

        try {
            await loadSheetJS();
            await handleFileSelect(file);
        } catch (error) {
            console.error('Error loading SheetJS:', error);
            showError('Error loading Excel library');
        }
    });

    // Traitement du fichier Excel
    async function handleFileSelect(file) {
        try {
            const data = await readExcelFile(file);
            renderExcelPreview(data);
        } catch (error) {
            console.error('Error reading file:', error);
            showError('Error reading Excel file');
        }
    }

    // Lecture du fichier Excel
    function readExcelFile(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                try {
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, { type: 'array' });
                    const firstSheetName = workbook.SheetNames[0];
                    const worksheet = workbook.Sheets[firstSheetName];
                    const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });
                    resolve(jsonData);
                } catch (error) {
                    reject(error);
                }
            };
            reader.onerror = reject;
            reader.readAsArrayBuffer(file);
        });
    }

    // Rendu de l'aperçu Excel
    function renderExcelPreview(data) {
        if (!data || data.length === 0) {
            showError('Excel file is empty');
            return;
        }

        // Supprimer les lignes complètement vides
        const filteredData = data.filter(row => row.some(cell => cell !== null && cell !== undefined && cell !== ''));
        
        if (filteredData.length === 0) {
            showError('No valid data found in file');
            return;
        }

        const headers = filteredData[0] || [];
        const rows = filteredData.slice(1);

        // Champs requis
        const requiredFields = ['first_name', 'last_name', 'email', 'phone'];
        
        // Vérifier les en-têtes
        const missingHeaders = requiredFields.filter(field => 
            !headers.some(header => header && header.toString().toLowerCase().trim() === field)
        );
        
        // Vérifier les données
        let errorRows = 0;
        const processedRows = rows.map((row, index) => {
            const rowData = {};
            let hasError = false;
            
            headers.forEach((header, colIndex) => {
                if (header) {
                    const fieldName = header.toString().toLowerCase().trim();
                    const value = row[colIndex] ? row[colIndex].toString().trim() : '';
                    rowData[fieldName] = value;
                    
                    // Vérifier si c'est un champ requis et s'il est vide
                    if (requiredFields.includes(fieldName) && !value) {
                        hasError = true;
                    }
                }
            });
            
            if (hasError) {
                errorRows++;
            }
            
            return { ...rowData, hasError, originalRow: row };
        });

        // Afficher l'aperçu
        const originalDataLength = data.length;
        const filteredDataLength = filteredData.length;
        const removedEmptyRows = originalDataLength - filteredDataLength;
        displayPreview(headers, processedRows, missingHeaders, errorRows, filteredData.length - 1, removedEmptyRows);
    }

    // Affichage de l'aperçu
    function displayPreview(headers, rows, missingHeaders, errorRows, totalRows, removedEmptyRows = 0) {
        // En-têtes
        previewHeaders.innerHTML = '';
        headers.forEach(header => {
            const th = document.createElement('th');
            th.textContent = header || '';
            
            if (header) {
                const fieldName = header.toString().toLowerCase().trim();
                const requiredFields = ['first_name', 'last_name', 'email', 'phone'];
                
                if (requiredFields.includes(fieldName)) {
                    th.classList.add('valid-header');
                } else if (missingHeaders.length > 0) {
                    th.classList.add('missing-header');
                }
            }
            
            previewHeaders.appendChild(th);
        });

        // Données (limitées à 100 lignes pour les performances)
        previewBody.innerHTML = '';
        const displayRows = rows.slice(0, 100);
        
        displayRows.forEach((row, index) => {
            const tr = document.createElement('tr');
            if (row.hasError) {
                tr.classList.add('error-row');
            }
            
            headers.forEach(header => {
                const td = document.createElement('td');
                const colIndex = headers.indexOf(header);
                td.textContent = row.originalRow[colIndex] || '';
                tr.appendChild(td);
            });
            
            previewBody.appendChild(tr);
        });

        // Métadonnées
        const dataRows = totalRows;
        
        let metadata = `Total: ${dataRows} data rows`;
        if (removedEmptyRows > 0) {
            metadata += ` | ${removedEmptyRows} empty rows removed`;
        }
        if (missingHeaders.length > 0) {
            metadata += ` | ${missingHeaders.length} missing columns`;
        }
        if (errorRows > 0) {
            metadata += ` | ${errorRows} rows with errors`;
        }
        if (rows.length > 100) {
            metadata += ` | Display limited to 100 rows`;
        }
        
        previewMetadata.textContent = metadata;
        previewBadge.textContent = `${dataRows} rows`;
        
        // Afficher l'aperçu
        previewWrapper.style.display = 'block';
    }

    // Masquer l'aperçu
    function hidePreview() {
        previewWrapper.style.display = 'none';
        previewHeaders.innerHTML = '';
        previewBody.innerHTML = '';
        previewMetadata.textContent = '';
        previewBadge.textContent = '0 rows';
    }

    // Afficher une erreur
    function showError(message) {
        console.error(message);
        hidePreview();
    }

    // Boutons d'action
    clearPreviewBtn.addEventListener('click', function() {
        fileInput.value = '';
        hidePreview();
    });

    reloadPreviewBtn.addEventListener('click', function() {
        if (fileInput.files[0]) {
            handleFileSelect(fileInput.files[0]);
        }
    });

    // Manual Add Form Handler
    const manualAddForm = document.getElementById('manualAddForm');
    const manualAddBtn = document.getElementById('manualAddBtn');
    const addStudentOffcanvas = document.getElementById('addStudentManuallyOffcanvas');
    const bsOffcanvas = new bootstrap.Offcanvas(addStudentOffcanvas);

    manualAddForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        // Clear previous errors
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        
        // Show loading
        manualAddBtn.disabled = true;
        manualAddBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Ajout en cours...';
        
        // Send request
        fetch('{{ route("import-students.add-manual") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': formData.get('_token')
            }
        })
        .then(response => response.json())
        .then(data => {
            manualAddBtn.disabled = false;
            manualAddBtn.innerHTML = '<i class="fas fa-plus me-2"></i>Ajouter Étudiant';
            
            if (data.success) {
                Swal.fire({
                    title: 'Succès!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Reset form and close offcanvas
                    manualAddForm.reset();
                    bsOffcanvas.hide();
                });
            } else {
                // Show validation errors
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const input = document.getElementById('manual_' + field);
                        const errorDiv = document.getElementById('manual_' + field + '_error');
                        if (input && errorDiv) {
                            input.classList.add('is-invalid');
                            errorDiv.textContent = data.errors[field][0];
                        }
                    });
                }
                
                Swal.fire({
                    title: 'Erreur!',
                    text: data.message || 'Une erreur est survenue',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        })
        .catch(error => {
            manualAddBtn.disabled = false;
            manualAddBtn.innerHTML = '<i class="fas fa-plus me-2"></i>Ajouter Étudiant';
            
            console.error('Error:', error);
            Swal.fire({
                title: 'Erreur Serveur',
                text: 'Une erreur est survenue lors de l\'ajout de l\'étudiant.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        });
    });

    // Soumission du formulaire
    importForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        // Validation côté client
        const productId = formData.get('product_id');
        const excelFile = formData.get('excel_file');
        
        if (!productId) {
            showError('Please select a course');
            return;
        }
        
        if (!excelFile || excelFile.size === 0) {
            showError('Please select an Excel file');
            return;
        }
        
        // Afficher le loading
        loadingOverlay.style.display = 'flex';
        submitBtn.disabled = true;
        
        // Envoyer la requête
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            loadingOverlay.style.display = 'none';
            submitBtn.disabled = false;
            
            if (data.success) {
                Swal.fire({
                    title: 'Success!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Reset form
                    importForm.reset();
                    hidePreview();
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        })
        .catch(error => {
            loadingOverlay.style.display = 'none';
            submitBtn.disabled = false;
            
            console.error('Error:', error);
            Swal.fire({
                title: 'Server Error',
                text: 'An error occurred during import. Please try again.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        });
    });
});
</script>
@endsection