@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="avatar avatar-lg bg-warning rounded-circle d-flex align-items-center justify-content-center">
                                    <i data-feather="edit" class="text-white" style="width: 24px; height: 24px;"></i>
                                </div>
                            </div>
                            <div>
                                <h1 class="page-title">Modifier Template Email</h1>
                                <p class="text-muted mb-0">Modifiez le template "{{ $emailTemplate->name }}"</p>
                            </div>
                        </div>
                        <div class="btn-group" role="group">
                            <a href="{{ route('email-templates.index') }}" class="btn btn-secondary">
                                <i data-feather="arrow-left" class="me-2"></i>
                                Retour
                            </a>
                            <a href="{{ route('email-templates.preview', $emailTemplate) }}" 
                               class="btn btn-info" target="_blank">
                                <i data-feather="eye" class="me-2"></i>
                                Aperçu
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('email-templates.update', $emailTemplate) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <!-- Formulaire principal -->
                    <div class="col-md-8">
                        <div class="card mb-4">
                            <div class="card-header">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="avatar avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center">
                                            <i data-feather="settings" class="text-white" style="width: 24px; height: 24px;"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="card-title mb-1">Informations du Template</h4>
                                        <p class="text-muted mb-0">Configurez les paramètres de base</p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Nom du Template <span class="text-danger">*</span></label>
                                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                                   value="{{ old('name', $emailTemplate->name) }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="type">Type <span class="text-danger">*</span></label>
                                            <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                                                <option value="">Sélectionner un type</option>
                                                @foreach(App\Models\EmailTemplate::TYPES as $key => $value)
                                                    <option value="{{ $key }}" {{ old('type', $emailTemplate->type) == $key ? 'selected' : '' }}>
                                                        {{ $value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="status">Statut <span class="text-danger">*</span></label>
                                            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                                <option value="">Sélectionner un statut</option>
                                                @foreach(App\Models\EmailTemplate::STATUSES as $key => $value)
                                                    <option value="{{ $key }}" {{ old('status', $emailTemplate->status) == $key ? 'selected' : '' }}>
                                                        {{ $value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="subject">Sujet de l'Email <span class="text-danger">*</span></label>
                                    <input type="text" name="subject" id="subject" class="form-control @error('subject') is-invalid @enderror" 
                                           value="{{ old('subject', $emailTemplate->subject) }}" required
                                           placeholder="Ex: Congratulations! Your &#123;&#123;quizType&#125;&#125; has been validated">
                                    @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Utilisez des variables comme &#123;&#123;student_name&#125;&#125;, &#123;&#123;course_name&#125;&#125;, etc.
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                                              rows="2" placeholder="Description du template...">{{ old('description', $emailTemplate->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                                               {{ old('is_active', $emailTemplate->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Template actif</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contenu HTML du Template -->
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="avatar avatar-lg bg-success rounded-circle d-flex align-items-center justify-content-center">
                                            <i data-feather="code" class="text-white" style="width: 24px; height: 24px;"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="card-title mb-1">Contenu HTML du Template</h4>
                                        <p class="text-muted mb-0">Éditez le code HTML complet de votre email</p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="content">Code HTML :</label>
                                    <textarea name="content" id="content" class="form-control @error('content') is-invalid @enderror" 
                                              rows="20" required>{{ old('content', $emailTemplate->content) }}</textarea>
                                    @error('content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Vous pouvez utiliser du HTML complet. Utilisez les variables comme &#123;&#123;student_name&#125;&#125;, &#123;&#123;course_name&#125;&#125;, etc.
                                    </small>
                                </div>

                                <!-- Contenu texte optionnel -->
                                <div class="form-group mt-3">
                                    <label for="text_content">Contenu texte (optionnel) :</label>
                                    <textarea name="text_content" id="text_content" class="form-control @error('text_content') is-invalid @enderror" 
                                              rows="8" placeholder="Contenu texte alternatif...">{{ old('text_content', $emailTemplate->text_content) }}</textarea>
                                    @error('text_content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Contenu texte optionnel qui peut être utilisé comme alternative au HTML.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar avec variables et actions -->
                    <div class="col-md-4">
                        <!-- Variables disponibles -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="avatar avatar-lg bg-info rounded-circle d-flex align-items-center justify-content-center">
                                            <i data-feather="tag" class="text-white" style="width: 24px; height: 24px;"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="card-title mb-1">Variables Disponibles</h4>
                                        <p class="text-muted mb-0">Cliquez pour insérer</p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Variables personnalisées (JSON)</label>
                                    <textarea name="variables" id="variables" class="form-control" rows="5" 
                                              placeholder='["student_name", "course_name", "score"]'>{{ old('variables', json_encode($emailTemplate->variables ?? [])) }}</textarea>
                                    <small class="form-text text-muted">Format JSON array</small>
                                </div>

                                <div class="mt-3">
                                    <h6>Variables communes :</h6>
                                    <div class="variable-list">
                                        <span class="badge bg-light text-dark me-1 mb-1" onclick="insertVariable('student_name')" style="cursor: pointer;">&#123;&#123;student_name&#125;&#125;</span>
                                        <span class="badge bg-light text-dark me-1 mb-1" onclick="insertVariable('student_first_name')" style="cursor: pointer;">&#123;&#123;student_first_name&#125;&#125;</span>
                                        <span class="badge bg-light text-dark me-1 mb-1" onclick="insertVariable('course_name')" style="cursor: pointer;">&#123;&#123;course_name&#125;&#125;</span>
                                        <span class="badge bg-light text-dark me-1 mb-1" onclick="insertVariable('quiz_name')" style="cursor: pointer;">&#123;&#123;quiz_name&#125;&#125;</span>
                                        <span class="badge bg-light text-dark me-1 mb-1" onclick="insertVariable('score')" style="cursor: pointer;">&#123;&#123;score&#125;&#125;</span>
                                        <span class="badge bg-light text-dark me-1 mb-1" onclick="insertVariable('submission_date')" style="cursor: pointer;">&#123;&#123;submission_date&#125;&#125;</span>
                                        <span class="badge bg-light text-dark me-1 mb-1" onclick="insertVariable('validation_date')" style="cursor: pointer;">&#123;&#123;validation_date&#125;&#125;</span>
                                        <span class="badge bg-light text-dark me-1 mb-1" onclick="insertVariable('admin_notes')" style="cursor: pointer;">&#123;&#123;admin_notes&#125;&#125;</span>
                                        <span class="badge bg-light text-dark me-1 mb-1" onclick="insertVariable('video_link')" style="cursor: pointer;">&#123;&#123;video_link&#125;&#125;</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="avatar avatar-lg bg-warning rounded-circle d-flex align-items-center justify-content-center">
                                            <i data-feather="zap" class="text-white" style="width: 24px; height: 24px;"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="card-title mb-1">Actions</h4>
                                        <p class="text-muted mb-0">Actions rapides</p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-success">
                                        <i data-feather="save" class="me-2"></i>
                                        Sauvegarder
                                    </button>
                                    <a href="{{ route('email-templates.preview', $emailTemplate) }}" 
                                       class="btn btn-info" target="_blank">
                                        <i data-feather="eye" class="me-2"></i>
                                        Aperçu
                                    </a>
                                    <a href="{{ route('email-templates.index') }}" class="btn btn-secondary">
                                        <i data-feather="arrow-left" class="me-2"></i>
                                        Retour
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Insérer une variable dans le contenu HTML
    function insertVariable(variable) {
        const textarea = document.getElementById('content');
        const cursorPos = textarea.selectionStart;
        const textBefore = textarea.value.substring(0, cursorPos);
        const textAfter = textarea.value.substring(cursorPos);
        
        textarea.value = textBefore + '{{' + variable + '}}' + textAfter;
        textarea.focus();
        textarea.setSelectionRange(cursorPos + variable.length + 4, cursorPos + variable.length + 4);
    }

    // Validation JSON pour les variables
    document.getElementById('variables').addEventListener('blur', function() {
        try {
            if (this.value.trim()) {
                JSON.parse(this.value);
                this.classList.remove('is-invalid');
            }
        } catch (e) {
            this.classList.add('is-invalid');
        }
    });

    // Hover effect for variables
    document.querySelectorAll('.variable-list .badge').forEach(function(badge) {
        badge.addEventListener('mouseenter', function() {
            this.classList.remove('bg-light', 'text-dark');
            this.classList.add('bg-primary', 'text-white');
        });
        badge.addEventListener('mouseleave', function() {
            this.classList.remove('bg-primary', 'text-white');
            this.classList.add('bg-light', 'text-dark');
        });
    });

    // Auto-resize textarea
    function autoResize(textarea) {
        textarea.style.height = 'auto';
        textarea.style.height = textarea.scrollHeight + 'px';
    }

    // Auto-resize on input
    document.getElementById('content').addEventListener('input', function() {
        autoResize(this);
    });

    document.getElementById('text_content').addEventListener('input', function() {
        autoResize(this);
    });

    // Initial resize
    document.addEventListener('DOMContentLoaded', function() {
        autoResize(document.getElementById('content'));
        autoResize(document.getElementById('text_content'));
    });
</script>
@endsection