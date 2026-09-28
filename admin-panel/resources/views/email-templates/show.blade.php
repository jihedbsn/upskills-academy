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
                                <div class="avatar avatar-lg bg-info rounded-circle d-flex align-items-center justify-content-center">
                                    <i data-feather="eye" class="text-white" style="width: 24px; height: 24px;"></i>
                                </div>
                            </div>
                            <div>
                                <h1 class="page-title">Détails Template Email</h1>
                                <p class="text-muted mb-0">{{ $emailTemplate->name }}</p>
                            </div>
                        </div>
                        <div class="btn-group" role="group">
                            <a href="{{ route('email-templates.edit', $emailTemplate) }}" class="btn btn-warning">
                                <i data-feather="edit" class="me-2"></i>
                                Modifier
                            </a>
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

            <div class="row">
                <div class="col-md-8">
                    <!-- Informations du template -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <div class="avatar avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center">
                                        <i data-feather="info" class="text-white" style="width: 24px; height: 24px;"></i>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="card-title mb-1">Informations du Template</h4>
                                    <p class="text-muted mb-0">Détails et configuration</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="30%">Nom:</th>
                                            <td>{{ $emailTemplate->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Type:</th>
                                            <td><span class="badge bg-info">{{ $emailTemplate->type_name }}</span></td>
                                        </tr>
                                        <tr>
                                            <th>Statut:</th>
                                            <td><span class="badge bg-secondary">{{ $emailTemplate->status_name }}</span></td>
                                        </tr>
                                        <tr>
                                            <th>État:</th>
                                            <td>
                                                @if($emailTemplate->is_active)
                                                    <span class="badge bg-success">Actif</span>
                                                @else
                                                    <span class="badge bg-danger">Inactif</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="30%">Créé le:</th>
                                            <td>{{ $emailTemplate->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Modifié le:</th>
                                            <td>{{ $emailTemplate->updated_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Variables:</th>
                                            <td>{{ count($emailTemplate->variables ?? []) }} variables</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            @if($emailTemplate->description)
                                <div class="mt-3">
                                    <h6>Description:</h6>
                                    <p class="text-muted">{{ $emailTemplate->description }}</p>
                                </div>
                            @endif

                            <div class="mt-3">
                                <h6>Sujet de l'Email:</h6>
                                <div class="alert alert-info">
                                    <i data-feather="mail" class="me-2"></i>
                                    {{ $emailTemplate->subject }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Variables disponibles -->
                    @if($emailTemplate->variables && count($emailTemplate->variables) > 0)
                        <div class="card mb-4">
                            <div class="card-header">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="avatar avatar-lg bg-warning rounded-circle d-flex align-items-center justify-content-center">
                                            <i data-feather="tag" class="text-white" style="width: 24px; height: 24px;"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="card-title mb-1">Variables Disponibles</h4>
                                        <p class="text-muted mb-0">Variables définies pour ce template</p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($emailTemplate->variables as $variable)
                                        <div class="col-md-3 mb-2">
                                            <span class="badge bg-light text-dark p-2">{!! '{{' . $variable . '}}' !!}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Contenu Texte du Template -->
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <div class="avatar avatar-lg bg-success rounded-circle d-flex align-items-center justify-content-center">
                                        <i data-feather="file-text" class="text-white" style="width: 24px; height: 24px;"></i>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="card-title mb-1">Contenu Texte du Template</h4>
                                    <p class="text-muted mb-0">Contenu texte éditable</p>
                                </div>
                                <div class="ms-auto">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyTextContent()">
                                        <i data-feather="copy" class="me-2"></i>
                                        Copier Texte
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($emailTemplate->text_content)
                                <div class="alert alert-light border" style="max-height: 400px; overflow-y: auto; white-space: pre-wrap; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6;">{{ $emailTemplate->text_content }}</div>
                            @else
                                <div class="alert alert-warning">
                                    <i data-feather="alert-triangle" class="me-2"></i>
                                    Aucun contenu texte défini. Utilisez l'édition pour ajouter du contenu.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Template HTML (lecture seule) -->
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <div class="avatar avatar-lg bg-secondary rounded-circle d-flex align-items-center justify-content-center">
                                        <i data-feather="code" class="text-white" style="width: 24px; height: 24px;"></i>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="card-title mb-1">Template HTML (Design Fixe)</h4>
                                    <p class="text-muted mb-0">Structure HTML non modifiable</p>
                                </div>
                                <div class="ms-auto">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleHtmlView()">
                                        <i data-feather="eye" class="me-2"></i>
                                        <span id="toggle-text">Voir HTML</span>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyHtmlContent()">
                                        <i data-feather="copy" class="me-2"></i>
                                        Copier HTML
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="html-content" style="display: none;">
                                <pre style="max-height: 400px; overflow-y: auto; background: #f8f9fa; padding: 15px; border-radius: 4px; font-size: 12px;"><code>{{ $emailTemplate->content }}</code></pre>
                                <small class="text-muted mt-2 d-block">Ce template HTML est fixe et ne peut être modifié que par un développeur.</small>
                            </div>
                            <div id="html-collapsed">
                                <div class="alert alert-info">
                                    <i data-feather="info" class="me-2"></i>
                                    Cliquez sur "Voir HTML" pour afficher la structure du template.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-md-4">
                    <!-- Actions -->
                    <div class="card mb-4">
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
                                <a href="{{ route('email-templates.preview', $emailTemplate) }}" 
                                   class="btn btn-info" target="_blank">
                                    <i data-feather="eye" class="me-2"></i>
                                    Aperçu
                                </a>
                                <a href="{{ route('email-templates.edit', $emailTemplate) }}" 
                                   class="btn btn-warning">
                                    <i data-feather="edit" class="me-2"></i>
                                    Modifier
                                </a>
                                <form action="{{ route('email-templates.toggle-status', $emailTemplate) }}" 
                                      method="POST" style="display: inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="btn btn-{{ $emailTemplate->is_active ? 'outline-danger' : 'outline-success' }} w-100">
                                        <i data-feather="{{ $emailTemplate->is_active ? 'x' : 'check' }}" class="me-2"></i>
                                        {{ $emailTemplate->is_active ? 'Désactiver' : 'Activer' }}
                                    </button>
                                </form>
                                <hr>
                                <a href="{{ route('email-templates.index') }}" class="btn btn-secondary">
                                    <i data-feather="arrow-left" class="me-2"></i>
                                    Retour à la Liste
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Statistiques d'utilisation -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <div class="avatar avatar-lg bg-secondary rounded-circle d-flex align-items-center justify-content-center">
                                        <i data-feather="bar-chart" class="text-white" style="width: 24px; height: 24px;"></i>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="card-title mb-1">Informations Techniques</h4>
                                    <p class="text-muted mb-0">Détails techniques</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <th>Taille du contenu:</th>
                                    <td>{{ number_format(strlen($emailTemplate->content)) }} caractères</td>
                                </tr>
                                <tr>
                                    <th>Variables définies:</th>
                                    <td>{{ count($emailTemplate->variables ?? []) }}</td>
                                </tr>
                                <tr>
                                    <th>Type technique:</th>
                                    <td><code>{{ $emailTemplate->type }}</code></td>
                                </tr>
                                <tr>
                                    <th>Statut technique:</th>
                                    <td><code>{{ $emailTemplate->status }}</code></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Variables communes -->
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <div class="avatar avatar-lg bg-info rounded-circle d-flex align-items-center justify-content-center">
                                        <i data-feather="tag" class="text-white" style="width: 24px; height: 24px;"></i>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="card-title mb-1">Variables Communes</h4>
                                    <p class="text-muted mb-0">Disponibles partout</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="variable-list">
                                <small class="text-muted">Variables disponibles dans tous les templates:</small>
                                <div class="mt-2">
                                    <span class="badge bg-light text-dark me-1 mb-1">&#123;&#123;student_name&#125;&#125;</span>
                                    <span class="badge bg-light text-dark me-1 mb-1">&#123;&#123;student_first_name&#125;&#125;</span>
                                    <span class="badge bg-light text-dark me-1 mb-1">&#123;&#123;course_name&#125;&#125;</span>
                                    <span class="badge bg-light text-dark me-1 mb-1">&#123;&#123;submission_date&#125;&#125;</span>
                                    <span class="badge bg-light text-dark me-1 mb-1">&#123;&#123;validation_date&#125;&#125;</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let showingHtml = false;
    
    function toggleHtmlView() {
        const htmlContent = document.getElementById('html-content');
        const htmlCollapsed = document.getElementById('html-collapsed');
        const toggleText = document.getElementById('toggle-text');
        
        if (showingHtml) {
            // Masquer le HTML
            htmlContent.style.display = 'none';
            htmlCollapsed.style.display = 'block';
            toggleText.textContent = 'Voir HTML';
            showingHtml = false;
        } else {
            // Afficher le HTML
            htmlContent.style.display = 'block';
            htmlCollapsed.style.display = 'none';
            toggleText.textContent = 'Masquer HTML';
            showingHtml = true;
        }
    }
    
    function copyTextContent() {
        const textElement = document.querySelector('.alert.alert-light');
        if (textElement) {
            const content = textElement.textContent;
            navigator.clipboard.writeText(content).then(function() {
                alert('✅ Contenu texte copié dans le presse-papiers!');
            }).catch(function(err) {
                console.error('Erreur lors de la copie: ', err);
                const textArea = document.createElement('textarea');
                textArea.value = content;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                alert('✅ Contenu texte copié dans le presse-papiers!');
            });
        }
    }
    
    function copyHtmlContent() {
        const htmlElement = document.querySelector('#html-content code');
        if (htmlElement) {
            const content = htmlElement.textContent;
            navigator.clipboard.writeText(content).then(function() {
                alert('✅ Code HTML copié dans le presse-papiers!');
            }).catch(function(err) {
                console.error('Erreur lors de la copie: ', err);
                const textArea = document.createElement('textarea');
                textArea.value = content;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                alert('✅ Code HTML copié dans le presse-papiers!');
            });
        }
    }
</script>
@endsection