@extends('layouts.app')

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-start mb-0">Email Send</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
                        <li class="breadcrumb-item active">Email Send</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content-body">
    @include('emails._tabs')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h4 class="card-title mb-1">Formulaire d'envoi d'email</h4>
                        <p class="card-subtitle text-muted mb-0">Communications externes — texte + pièces jointes multiples (PDF, images, Word)</p>
                    </div>
                </div>
                <div class="card-body">
                    @if(empty($mailDeliversExternally))
                        <div class="alert alert-warning" role="alert">
                            <strong>Configuration courrier&nbsp;:</strong>
                            MAIL_MAILER est actuellement <code>{{ $mailDriver ?? config('mail.default') }}</code>.
                            Les messages ne sont <strong>pas</strong> envoyés vers une messagerie réelle (ils sont simulés / écrits en local).
                            Passez à <code>smtp</code> (Sendmail, Brevo, etc.) dans <code>.env</code> pour une livraison réelle.
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            {{ session('warning') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('emails.send') }}" method="POST" id="emailForm" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-1">
                                <label class="form-label" for="email">
                                    <i data-feather="mail" class="me-1"></i>Email du destinataire
                                </label>
                                <input type="email" id="email" class="form-control" name="email" 
                                       placeholder="exemple@email.com" value="{{ old('email', $prefillEmail ?? '') }}" required>
                                <div class="form-text">L'email sera envoyé à cette adresse</div>
                            </div>
                            
                            <div class="col-md-6 mb-1">
                                <label class="form-label" for="subject">
                                    <i data-feather="type" class="me-1"></i>Sujet
                                </label>
                                <input type="text" id="subject" class="form-control" name="subject" 
                                       placeholder="Sujet de l'email" value="{{ old('subject') }}" required>
                                <div class="form-text">Titre qui apparaîtra dans l'email</div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-12 mb-1">
                                <label class="form-label" for="content">
                                    <i data-feather="edit-3" class="me-1"></i>Contenu de l'email
                                </label>
                                <textarea id="content" class="form-control" name="content" rows="12" 
                                          placeholder="Tapez votre message ici...

Exemple de contenu :
Bonjour,

J'espère que vous allez bien. Je vous écris pour...

Cordialement" required>{{ old('content') }}</textarea>
                                <div class="form-text">
                                    <span id="charCount">0</span> caractères | 
                                    <span class="text-primary">Conseil :</span> Utilisez des paragraphes courts pour une meilleure lisibilité
                                </div>
                            </div>
                        </div>

                        <div class="card mt-3 border-primary email-attachments-card">
                            <div class="card-header bg-primary bg-opacity-10 py-2">
                                <h5 class="card-title mb-0 text-primary">
                                    <i class="fa fa-paperclip me-2"></i>
                                    Pièces jointes / Attach Files
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-3">
                                    Ajoutez un ou plusieurs fichiers à votre email. Formats : PDF, JPG, PNG, DOC, DOCX (max 10 Mo par fichier, 10 fichiers max).
                                </p>

                                <input type="file"
                                       id="attachments"
                                       name="attachments[]"
                                       class="form-control mb-3"
                                       multiple
                                       accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">

                                <div id="attachmentDropZone" class="attachment-drop-zone mb-2">
                                    <i class="fa fa-cloud-upload attachment-drop-fa-icon"></i>
                                    <p class="mb-2 fw-semibold">Glissez-déposez vos fichiers ici</p>
                                    <button type="button" class="btn btn-primary btn-sm" id="attachFilesBtn">
                                        <i class="fa fa-paperclip me-1"></i>Joindre un ou plusieurs fichiers
                                    </button>
                                </div>

                                <div id="attachmentFileList" class="attachment-file-list d-none"></div>
                                <div id="attachmentError" class="alert alert-danger py-2 px-3 mt-2 d-none mb-0" role="alert"></div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div>
                                        <button type="submit" class="btn btn-primary me-2" id="sendBtn">
                                            <i class="fa fa-paper-plane me-1"></i>Envoyer l'email
                                        </button>
                                        <button type="reset" class="btn btn-outline-secondary">
                                            <i class="fa fa-refresh me-1"></i>Réinitialiser
                                        </button>
                                    </div>
                                    <div class="text-muted">
                                        @if(!empty($mailDeliversExternally))
                                            <small><i class="fa fa-shield me-1"></i>Transport SMTP / API (SSL/TLS selon configuration)</small>
                                        @else
                                            <small><i class="fa fa-exclamation-triangle me-1"></i>Pas de transport réel&nbsp;: <code>{{ $mailDriver ?? 'log' }}</code></small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.attachment-drop-zone {
    border: 2px dashed #cbd5e1;
    border-radius: 0.5rem;
    background: #f8fafc;
    padding: 1.75rem 1rem;
    text-align: center;
    transition: border-color 0.2s, background-color 0.2s;
    cursor: pointer;
}

.attachment-drop-zone.drag-over {
    border-color: #093162;
    background: #f0efff;
}

.email-attachments-card {
    border-width: 2px !important;
}

.attachment-drop-fa-icon {
    font-size: 2.5rem;
    color: #093162;
    display: block;
    margin-bottom: 0.75rem;
}

.attachment-file-list {
    margin-top: 0.75rem;
    border: 1px solid #e3e6f0;
    border-radius: 0.375rem;
    overflow: hidden;
}

.attachment-file-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.65rem 0.85rem;
    background: #fff;
    border-bottom: 1px solid #f1f3f5;
    gap: 0.75rem;
}

.attachment-file-item:last-child {
    border-bottom: none;
}

.attachment-file-meta {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    min-width: 0;
}

.attachment-file-name {
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.attachment-file-size {
    color: #6c757d;
    font-size: 0.85rem;
    white-space: nowrap;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const contentTextarea = document.getElementById('content');
    const charCountSpan = document.getElementById('charCount');
    const sendBtn = document.getElementById('sendBtn');
    const emailForm = document.getElementById('emailForm');
    const fileInput = document.getElementById('attachments');
    const dropZone = document.getElementById('attachmentDropZone');
    const attachFilesBtn = document.getElementById('attachFilesBtn');
    const fileList = document.getElementById('attachmentFileList');
    const attachmentError = document.getElementById('attachmentError');

    const MAX_FILES = 10;
    const MAX_FILE_SIZE = 10 * 1024 * 1024;
    const ALLOWED_EXTENSIONS = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];

    let selectedFiles = new DataTransfer();

    function formatFileSize(bytes) {
        if (bytes >= 1024 * 1024) {
            return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
        }
        return Math.max(1, Math.round(bytes / 1024)) + ' KB';
    }

    function isAllowedFile(file) {
        const extension = file.name.split('.').pop().toLowerCase();
        return ALLOWED_EXTENSIONS.includes(extension);
    }

    function showAttachmentError(message) {
        attachmentError.textContent = message;
        attachmentError.classList.remove('d-none');
    }

    function clearAttachmentError() {
        attachmentError.textContent = '';
        attachmentError.classList.add('d-none');
    }

    function syncFileInput() {
        fileInput.files = selectedFiles.files;
    }

    function renderFileList() {
        if (selectedFiles.files.length === 0) {
            fileList.innerHTML = '';
            fileList.classList.add('d-none');
            return;
        }

        fileList.classList.remove('d-none');
        fileList.innerHTML = Array.from(selectedFiles.files).map(function(file, index) {
            return `
                <div class="attachment-file-item">
                    <div class="attachment-file-meta">
                        <i class="fa fa-file-o"></i>
                        <span class="attachment-file-name" title="${file.name}">${file.name}</span>
                        <span class="attachment-file-size">${formatFileSize(file.size)}</span>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-attachment-btn" data-index="${index}">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            `;
        }).join('');

        fileList.querySelectorAll('.remove-attachment-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                removeFile(parseInt(this.dataset.index, 10));
            });
        });
    }

    function removeFile(index) {
        const nextTransfer = new DataTransfer();
        Array.from(selectedFiles.files).forEach(function(file, i) {
            if (i !== index) {
                nextTransfer.items.add(file);
            }
        });
        selectedFiles = nextTransfer;
        syncFileInput();
        renderFileList();
        clearAttachmentError();
    }

    function addFiles(fileListToAdd) {
        clearAttachmentError();
        let rejected = false;

        Array.from(fileListToAdd).forEach(function(file) {
            if (selectedFiles.files.length >= MAX_FILES) {
                showAttachmentError('Maximum ' + MAX_FILES + ' fichiers autorisés.');
                rejected = true;
                return;
            }

            if (!isAllowedFile(file)) {
                showAttachmentError('Format non supporté : ' + file.name + '. Utilisez PDF, JPG, PNG, DOC ou DOCX.');
                rejected = true;
                return;
            }

            if (file.size > MAX_FILE_SIZE) {
                showAttachmentError('Fichier trop volumineux : ' + file.name + ' (max 10 Mo).');
                rejected = true;
                return;
            }

            const duplicate = Array.from(selectedFiles.files).some(function(existing) {
                return existing.name === file.name && existing.size === file.size;
            });

            if (!duplicate) {
                selectedFiles.items.add(file);
            }
        });

        if (!rejected && selectedFiles.files.length === 0 && fileListToAdd.length > 0) {
            showAttachmentError('Aucun fichier valide sélectionné.');
        }

        syncFileInput();
        renderFileList();
    }

    attachFilesBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        fileInput.click();
    });

    dropZone.addEventListener('click', function(e) {
        if (e.target.closest('#attachFilesBtn')) {
            return;
        }
        fileInput.click();
    });

    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            addFiles(this.files);
        }
    });

    ['dragenter', 'dragover'].forEach(function(eventName) {
        dropZone.addEventListener(eventName, function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.add('drag-over');
        });
    });

    ['dragleave', 'drop'].forEach(function(eventName) {
        dropZone.addEventListener(eventName, function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.remove('drag-over');
        });
    });

    dropZone.addEventListener('drop', function(e) {
        if (e.dataTransfer.files.length > 0) {
            addFiles(e.dataTransfer.files);
        }
    });

    emailForm.addEventListener('reset', function() {
        selectedFiles = new DataTransfer();
        syncFileInput();
        renderFileList();
        clearAttachmentError();
    });

    // Auto-resize textarea
    function autoResize() {
        contentTextarea.style.height = 'auto';
        contentTextarea.style.height = contentTextarea.scrollHeight + 'px';
    }

    // Update character count
    function updateCharCount() {
        const count = contentTextarea.value.length;
        charCountSpan.textContent = count;
        
        if (count > 1000) {
            charCountSpan.className = 'text-warning';
        } else if (count > 2000) {
            charCountSpan.className = 'text-danger';
        } else {
            charCountSpan.className = 'text-muted';
        }
    }

    // Event listeners
    contentTextarea.addEventListener('input', function() {
        autoResize();
        updateCharCount();
    });

    // Form submission with loading state
    emailForm.addEventListener('submit', function() {
        syncFileInput();
        sendBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i>Envoi en cours...';
        sendBtn.disabled = true;

        setTimeout(function() {
            sendBtn.innerHTML = '<i class="fa fa-paper-plane me-1"></i>Envoyer l\'email';
            sendBtn.disabled = false;
        }, 5000);
    });

    // Initialize
    updateCharCount();
    autoResize();
});
</script>
@endsection
