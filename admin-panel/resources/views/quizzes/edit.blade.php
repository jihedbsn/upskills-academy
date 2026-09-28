@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Modifier le Quiz</h1>
        <div class="page-actions">
            <a href="{{ route('quizzes.index') }}" class="btn btn-outline-secondary">
                <i data-feather="arrow-left" class="me-2"></i>
                Retour
            </a>
        </div>
    </div>
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-warning rounded-circle d-flex align-items-center justify-content-center">
                                <i data-feather="edit" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">Modifier le Quiz</h4>
                            <p class="text-muted mb-0">Modifiez les informations du quiz</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <div class="d-flex align-items-center">
                                <i data-feather="alert-triangle" class="me-3" style="width: 20px; height: 20px;"></i>
                                <div>
                                    <strong>Erreurs de validation</strong>
                                    <ul class="mb-0 mt-1">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('quizzes.update', $quiz) }}" id="quizForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Section Arabe -->
                            <div class="col-md-6">
                                <div class="card border-primary">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center">
                                            <i data-feather="align-right" class="me-2"></i>
                                            <h6 class="mb-0">Section Arabe</h6>
                                            <span class="badge bg-light text-dark ms-auto">العربية</span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i data-feather="type" class="me-1" style="width: 14px; height: 14px;"></i>
                                                Nom (Arabe) *
                                            </label>
                                            <input type="text" name="name_ar" class="form-control"
                                                   value="{{ old('name_ar', $quiz->name_ar) }}" required
                                                   placeholder="Nom du quiz en arabe">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section Anglaise -->
                            <div class="col-md-6">
                                <div class="card border-success">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center">
                                            <i data-feather="align-left" class="me-2"></i>
                                            <h6 class="mb-0">Section Anglaise</h6>
                                            <span class="badge bg-light text-dark ms-auto">English</span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i data-feather="type" class="me-1" style="width: 14px; height: 14px;"></i>
                                                Nom (Anglais) *
                                            </label>
                                            <input type="text" name="name_en" class="form-control"
                                                   value="{{ old('name_en', $quiz->name_en) }}" required
                                                   placeholder="Quiz name in English">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section Configuration -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card border-info">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center">
                                            <i data-feather="settings" class="me-2"></i>
                                            <h6 class="mb-0">Configuration</h6>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">
                                                        <i data-feather="tag" class="me-1" style="width: 14px; height: 14px;"></i>
                                                        Type de Quiz *
                                                    </label>
                                                    <select name="type_id" class="form-select" required>
                                                        <option value="">Sélectionnez un type</option>
                                                        @foreach($types as $type)
                                                            <option value="{{ $type->id }}"
                                                                {{ old('type_id', $quiz->type_id) == $type->id ? 'selected' : '' }}>
                                                                {{ $type->titre }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">
                                                        <i data-feather="award" class="me-1" style="width: 14px; height: 14px;"></i>
                                                        Score Maximum *
                                                    </label>
                                                    <div class="input-group">
                                                        <input type="number" name="score" class="form-control"
                                                               value="{{ old('score', $quiz->score) }}" required
                                                               min="1" max="100" step="1">
                                                        <span class="input-group-text">/ 100</span>
                                                    </div>
                                                    <div class="form-text">Score maximum possible pour ce quiz</div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">
                                                        <i data-feather="repeat" class="me-1" style="width: 14px; height: 14px;"></i>
                                                        Tentatives Max
                                                    </label>
                                                    <input type="number" name="max_attempts" class="form-control"
                                                           value="{{ old('max_attempts', $quiz->max_attempts) }}"
                                                           min="1" step="1" placeholder="Illimité">
                                                    <div class="form-text">Laisser vide pour illimité</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section Questions -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card border-warning">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <i data-feather="list" class="me-2"></i>
                                                <h6 class="mb-0">Questions du Quiz</h6>
                                                <span class="badge bg-light text-dark ms-2">MULTILINGUE</span>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <select id="quizSelectDropdown" class="form-select form-select-sm" style="width: 250px; display: none;">
                                                    <option value="">Sélectionnez un quiz...</option>
                                                </select>
                                                <button type="button" class="btn btn-sm btn-info me-2" id="importQuestionsBtn">
                                                    <i data-feather="download" class="me-1"></i>
                                                    Importer Questions
                                                </button>
                                                <button type="button" class="btn btn-sm btn-success" id="addQuestionBtn">
                                                    <i data-feather="plus" class="me-1"></i>
                                                    Ajouter une Question
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div id="questionsContainer">
                                            @if($quiz->questions->count() > 0)
                                                @foreach($quiz->questions as $index => $question)
                                                    <div class="question-row border rounded p-3 mb-3">
                                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                                            <h6 class="mb-0">
                                                                <i data-feather="help-circle" class="me-2" style="width: 16px; height: 16px;"></i>
                                                                Question #{{ $index + 1 }}
                                                            </h6>
                                                            <button type="button" class="btn btn-sm btn-outline-danger remove-question" title="Supprimer cette question">
                                                                <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                                                            </button>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-5">
                                                                <div class="mb-3">
                                                                    <label class="form-label">
                                                                        <i data-feather="align-right" class="me-1" style="width: 14px; height: 14px;"></i>
                                                                        Question (Arabe) *
                                                                    </label>
                                                                    <input type="text" name="questions[{{ $index }}][name_ar]" class="form-control"
                                                                           value="{{ $question->name_ar }}" required placeholder="Question en arabe">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <div class="mb-3">
                                                                    <label class="form-label">
                                                                        <i data-feather="align-left" class="me-1" style="width: 14px; height: 14px;"></i>
                                                                        Question (Anglais) *
                                                                    </label>
                                                                    <input type="text" name="questions[{{ $index }}][name_en]" class="form-control"
                                                                           value="{{ $question->name_en }}" required placeholder="Question in English">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="mb-3">
                                                                    <label class="form-label">
                                                                        <i data-feather="award" class="me-1" style="width: 14px; height: 14px;"></i>
                                                                        Points
                                                                    </label>
                                                                    <input type="number" name="questions[{{ $index }}][point]" class="form-control"
                                                                           value="{{ $question->point }}" min="1" max="100" placeholder="10">
                                                                    <div class="form-text small">Défaut: 10</div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Section des réponses -->
                                                        <div class="reponses-section mt-3">
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <h6 class="mb-0">
                                                                    <i data-feather="list" class="me-2" style="width: 16px; height: 16px;"></i>
                                                                    Réponses
                                                                </h6>
                                                                <button type="button" class="btn btn-sm btn-outline-primary add-reponse" data-question="{{ $index }}" title="Ajouter une réponse">
                                                                    <i data-feather="plus" style="width: 14px; height: 14px;"></i>
                                                                    Ajouter Réponse
                                                                </button>
                                                            </div>
                                                            <div class="reponses-container" id="reponses-{{ $index }}">
                                                                @if($question->reponses->count() > 0)
                                                                    @foreach($question->reponses as $reponseIndex => $reponse)
                                                                        <div class="reponse-row border rounded p-2 mb-2 bg-light">
                                                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                                                <h6 class="mb-0">
                                                                                    <i data-feather="check-circle" class="me-2" style="width: 16px; height: 16px;"></i>
                                                                                    Réponse #{{ $reponseIndex + 1 }}
                                                                                </h6>
                                                                                <button type="button" class="btn btn-sm btn-outline-danger remove-reponse" title="Supprimer cette réponse">
                                                                                    <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                                                                                </button>
                                                                            </div>

                                                                            <div class="row">
                                                                                <div class="col-md-4">
                                                                                    <div class="mb-2">
                                                                                        <label class="form-label small">
                                                                                            <i data-feather="align-right" class="me-1" style="width: 12px; height: 12px;"></i>
                                                                                            Réponse (Arabe) *
                                                                                        </label>
                                                                                        <input type="text" name="questions[{{ $index }}][reponses][{{ $reponseIndex }}][titre_ar]"
                                                                                               class="form-control form-control-sm" required placeholder="Réponse en arabe"
                                                                                               value="{{ $reponse->titre_ar }}">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-4">
                                                                                    <div class="mb-2">
                                                                                        <label class="form-label small">
                                                                                            <i data-feather="align-left" class="me-1" style="width: 12px; height: 12px;"></i>
                                                                                            Réponse (Anglais) *
                                                                                        </label>
                                                                                        <input type="text" name="questions[{{ $index }}][reponses][{{ $reponseIndex }}][titre_en]"
                                                                                               class="form-control form-control-sm" required placeholder="Response in English"
                                                                                               value="{{ $reponse->titre_en }}">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-3">
                                                                                    <div class="mb-2">
                                                                                        <label class="form-label small">
                                                                                            <i data-feather="check" class="me-1" style="width: 12px; height: 12px;"></i>
                                                                                            Correcte
                                                                                        </label>
                                                                                        <div class="form-check">
                                                                                            <input type="checkbox" name="questions[{{ $index }}][reponses][{{ $reponseIndex }}][is_correcte]"
                                                                                                   class="form-check-input" value="1" {{ $reponse->is_correcte ? 'checked' : '' }}>
                                                                                            <label class="form-check-label small">Cette réponse est correcte</label>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                @else
                                                                    <div class="alert alert-info">
                                                                        <i data-feather="info" class="me-2" style="width: 14px; height: 14px;"></i>
                                                                        Aucune réponse ajoutée pour cette question
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>

                                        <div class="text-center py-3" id="noQuestionsMessage" style="{{ $quiz->questions->count() > 0 ? 'display: none;' : '' }}">
                                            <i data-feather="help-circle" class="text-muted" style="width: 48px; height: 48px;"></i>
                                            <p class="text-muted mt-2">Aucune question ajoutée</p>
                                            <p class="text-muted small">Cliquez sur "Ajouter une Question" pour commencer</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informations du Quiz -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card border-secondary">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center">
                                            <i data-feather="info" class="me-2"></i>
                                            <h6 class="mb-0">Informations du Quiz</h6>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label text-muted">ID du Quiz</label>
                                                    <input type="text" class="form-control" value="{{ $quiz->id }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label text-muted">Date de création</label>
                                                    <input type="text" class="form-control"
                                                           value="{{ $quiz->created_at->format('d/m/Y H:i') }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label text-muted">Dernière modification</label>
                                                    <input type="text" class="form-control"
                                                           value="{{ $quiz->updated_at->format('d/m/Y H:i') }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                                                 <!-- Actions -->
                         <div class="d-flex justify-content-between mt-4">
                             <a href="{{ route('quizzes.index') }}" class="btn btn-outline-secondary">
                                 <i data-feather="arrow-left" class="me-2"></i>
                                 Retour
                             </a>
                             <button type="submit" class="btn btn-primary">
                                 <i data-feather="save" class="me-2"></i>
                                 Mettre à jour le Quiz
                             </button>
                         </div>
                     </form>
                 </div>
             </div>
         </div>
     </div>
 </div>
 @endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let questionIndex = {{ $quiz->questions->count() }};
    let reponseIndex = {};

    // Initialiser les index des réponses pour les questions existantes
    @foreach($quiz->questions as $index => $question)
        reponseIndex[{{ $index }}] = {{ $question->reponses->count() }};
    @endforeach

    const questionsContainer = document.getElementById('questionsContainer');
    const noQuestionsMessage = document.getElementById('noQuestionsMessage');
    const addQuestionBtn = document.getElementById('addQuestionBtn');
    const importQuestionsBtn = document.getElementById('importQuestionsBtn');
    const quizSelectDropdown = document.getElementById('quizSelectDropdown');

    // Bail out early if core DOM nodes are missing (prevents runtime errors)
    if (!questionsContainer || !addQuestionBtn) {
        return;
    }

    // Charger les quiz disponibles au clic sur le bouton d'import
    if (importQuestionsBtn) {
        importQuestionsBtn.addEventListener('click', async function() {
            // Si le dropdown est déjà visible, procéder à l'import
            if (quizSelectDropdown.style.display !== 'none' && quizSelectDropdown.value) {
                await importSelectedQuiz();
                return;
            }

            // Sinon, charger les quiz disponibles
            const btn = this;
            const originalHTML = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Chargement...';

            try {
                const response = await fetch('/api/import/available-quizzes?_t=' + Date.now(), {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Cache-Control': 'no-cache'
                    }
                });

                if (!response.ok) throw new Error('Erreur lors du chargement des quiz');

                const data = await response.json();

                if (data.success && data.quizzes && data.quizzes.length > 0) {
                    // Remplir le dropdown
                    quizSelectDropdown.innerHTML = '<option value="">Sélectionnez un quiz...</option>';
                    data.quizzes.forEach(quiz => {
                        const option = document.createElement('option');
                        option.value = quiz.index;
                        option.textContent = `${quiz.name_ar} / ${quiz.name_en} (${quiz.questions_count} questions)`;
                        quizSelectDropdown.appendChild(option);
                    });

                    // Afficher le dropdown
                    quizSelectDropdown.style.display = 'inline-block';
                    btn.innerHTML = '<i data-feather="download" class="me-1"></i> Importer';
                    if (typeof feather !== 'undefined') feather.replace();

                    alert(`✅ ${data.quizzes.length} quiz disponible(s). Sélectionnez un quiz puis cliquez à nouveau sur "Importer".`);
                } else {
                    alert('❌ Aucun quiz disponible à importer.');
                }

            } catch (error) {
                console.error('Erreur chargement quiz:', error);
                alert('❌ Erreur lors du chargement des quiz disponibles.');
            } finally {
                btn.disabled = false;
                if (quizSelectDropdown.style.display === 'none') {
                    btn.innerHTML = originalHTML;
                }
            }
        });
    }

    // Fonction pour importer le quiz sélectionné
    async function importSelectedQuiz() {
        const selectedIndex = quizSelectDropdown.value;

        if (!selectedIndex && selectedIndex !== '0') {
            alert('⚠️ Veuillez sélectionner un quiz.');
            return;
        }

        const btn = importQuestionsBtn;
        const originalHTML = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Importation...';

        try {
            const response = await fetch(`/api/import/questions?quiz_index=${selectedIndex}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error('Erreur lors de l\'import');

            const data = await response.json();

            if (data.success && data.questions && data.questions.length > 0) {
                // Ne pas effacer les questions existantes, juste ajouter les nouvelles
                // questionsContainer.innerHTML = ''; // REMOVED - keep existing questions
                // questionIndex = 0; // REMOVED - continue from current index
                // reponseIndex = {}; // REMOVED - keep existing response indices

                // Ajouter chaque question importée
                data.questions.forEach((questionData) => {
                    addImportedQuestionToForm(questionData);
                });

                if (noQuestionsMessage) noQuestionsMessage.style.display = 'none';
                if (typeof feather !== 'undefined') feather.replace();

                // Masquer le dropdown et réinitialiser
                quizSelectDropdown.style.display = 'none';
                quizSelectDropdown.value = '';

                alert(`✅ ${data.questions.length} question(s) ajoutée(s) au formulaire. Cliquez sur "Enregistrer" pour sauvegarder.`);
            } else {
                alert('❌ Aucune question trouvée à importer.');
            }

        } catch (error) {
            console.error('Erreur import:', error);
            alert('❌ Erreur lors de l\'importation des questions. Veuillez réessayer.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i data-feather="download" class="me-1"></i> Importer Questions';
            if (typeof feather !== 'undefined') feather.replace();
        }
    }

    // Fonction pour ajouter une question importée au formulaire
    function addImportedQuestionToForm(questionData) {
        const qIndex = questionIndex;
        reponseIndex[qIndex] = 0;

        const questionRow = document.createElement('div');
        questionRow.className = 'question-row border rounded p-3 mb-3';
        questionRow.innerHTML = `
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h6 class="mb-0">
                    <i data-feather="help-circle" class="me-2" style="width: 16px; height: 16px;"></i>
                    Question #${qIndex + 1}
                </h6>
                <button type="button" class="btn btn-sm btn-outline-danger remove-question" title="Supprimer cette question">
                    <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                </button>
            </div>

            <div class="row">
                <div class="col-md-5">
                    <div class="mb-3">
                        <label class="form-label">
                            <i data-feather="align-right" class="me-1" style="width: 14px; height: 14px;"></i>
                            Question (Arabe) *
                        </label>
                        <input type="text" name="questions[${qIndex}][name_ar]" class="form-control"
                               required value="${escapeHtml(questionData.name_ar || '')}">
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="mb-3">
                        <label class="form-label">
                            <i data-feather="align-left" class="me-1" style="width: 14px; height: 14px;"></i>
                            Question (Anglais) *
                        </label>
                        <input type="text" name="questions[${qIndex}][name_en]" class="form-control"
                               required value="${escapeHtml(questionData.name_en || '')}">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label class="form-label">
                            <i data-feather="award" class="me-1" style="width: 14px; height: 14px;"></i>
                            Points
                        </label>
                        <input type="number" name="questions[${qIndex}][point]" class="form-control"
                               value="10" min="1" max="100">
                        <div class="form-text small">Défaut: 10</div>
                    </div>
                </div>
            </div>

            <!-- Section des réponses -->
            <div class="reponses-section mt-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">
                        <i data-feather="list" class="me-2" style="width: 16px; height: 16px;"></i>
                        Réponses
                    </h6>
                    <button type="button" class="btn btn-sm btn-outline-primary add-reponse" data-question="${qIndex}" title="Ajouter une réponse">
                        <i data-feather="plus" style="width: 14px; height: 14px;"></i>
                        Ajouter Réponse
                    </button>
                </div>
                <div class="reponses-container" id="reponses-${qIndex}"></div>
            </div>
        `;

        questionsContainer.appendChild(questionRow);

        // Ajouter les réponses importées
        if (questionData.answers && questionData.answers.length > 0) {
            const reponsesContainer = questionRow.querySelector(`#reponses-${qIndex}`);
            questionData.answers.forEach(answerData => {
                addImportedAnswer(qIndex, answerData, reponsesContainer);
            });
        }

        // Ajouter les événements
        const removeQuestionBtn = questionRow.querySelector('.remove-question');
        if (removeQuestionBtn) {
            removeQuestionBtn.addEventListener('click', function() {
                questionRow.remove();
                updateQuestionNumbers();
                checkEmptyQuestions();
            });
        }

        const addReponseBtn = questionRow.querySelector('.add-reponse');
        if (addReponseBtn) {
            addReponseBtn.addEventListener('click', function() {
                const questionId = this.getAttribute('data-question');
                addReponse(questionId);
            });
        }

        questionIndex++;
    }

    // Fonction pour ajouter une réponse importée
    function addImportedAnswer(qIndex, answerData, container) {
        const rIndex = reponseIndex[qIndex];

        const reponseRow = document.createElement('div');
        reponseRow.className = 'reponse-row border rounded p-2 mb-2 bg-light';
        reponseRow.innerHTML = `
            <div class="d-flex justify-content-between align-items-start mb-2">
                <h6 class="mb-0">
                    <i data-feather="check-circle" class="me-2" style="width: 16px; height: 16px;"></i>
                    Réponse #${rIndex + 1}
                </h6>
                <button type="button" class="btn btn-sm btn-outline-danger remove-reponse" title="Supprimer cette réponse">
                    <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                </button>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-2">
                        <label class="form-label small">
                            <i data-feather="align-right" class="me-1" style="width: 12px; height: 12px;"></i>
                            Réponse (Arabe) *
                        </label>
                        <input type="text" name="questions[${qIndex}][reponses][${rIndex}][titre_ar]"
                               class="form-control form-control-sm" required value="${escapeHtml(answerData.name_ar || '')}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-2">
                        <label class="form-label small">
                            <i data-feather="align-left" class="me-1" style="width: 12px; height: 12px;"></i>
                            Réponse (Anglais) *
                        </label>
                        <input type="text" name="questions[${qIndex}][reponses][${rIndex}][titre_en]"
                               class="form-control form-control-sm" required value="${escapeHtml(answerData.name_en || '')}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-2">
                        <label class="form-label small">
                            <i data-feather="check" class="me-1" style="width: 12px; height: 12px;"></i>
                            Correcte
                        </label>
                        <div class="form-check">
                            <input type="checkbox" name="questions[${qIndex}][reponses][${rIndex}][is_correcte]"
                                   class="form-check-input" value="1" ${answerData.is_correct ? 'checked' : ''}>
                            <label class="form-check-label small">Cette réponse est correcte</label>
                        </div>
                    </div>
                </div>
            </div>
        `;

        container.appendChild(reponseRow);

        // Ajouter l'événement de suppression
        const removeBtn = reponseRow.querySelector('.remove-reponse');
        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                reponseRow.remove();
                updateReponseNumbers(qIndex);
                checkEmptyReponses(qIndex);
            });
        }

        reponseIndex[qIndex]++;
    }

    // Fonction pour échapper le HTML (sécurité XSS)
    function escapeHtml(text) {
        const map = {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'};
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }

    // Fonction pour ajouter une nouvelle question
    function addQuestion() {
        // Initialiser l'index des réponses pour cette question
        reponseIndex[questionIndex] = 0;

        const questionRow = document.createElement('div');
        questionRow.className = 'question-row border rounded p-3 mb-3';
        questionRow.innerHTML = `
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h6 class="mb-0">
                    <i data-feather="help-circle" class="me-2" style="width: 16px; height: 16px;"></i>
                    Question #${questionIndex + 1}
                </h6>
                <button type="button" class="btn btn-sm btn-outline-danger remove-question" title="Supprimer cette question">
                    <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                </button>
            </div>

            <div class="row">
                <div class="col-md-5">
                    <div class="mb-3">
                        <label class="form-label">
                            <i data-feather="align-right" class="me-1" style="width: 14px; height: 14px;"></i>
                            Question (Arabe) *
                        </label>
                        <input type="text" name="questions[${questionIndex}][name_ar]" class="form-control"
                               required placeholder="Question en arabe">
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="mb-3">
                        <label class="form-label">
                            <i data-feather="align-left" class="me-1" style="width: 14px; height: 14px;"></i>
                            Question (Anglais) *
                        </label>
                        <input type="text" name="questions[${questionIndex}][name_en]" class="form-control"
                               required placeholder="Question in English">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label class="form-label">
                            <i data-feather="award" class="me-1" style="width: 14px; height: 14px;"></i>
                            Points
                        </label>
                        <input type="number" name="questions[${questionIndex}][point]" class="form-control"
                               value="10" min="1" max="100" placeholder="10">
                        <div class="form-text small">Défaut: 10</div>
                    </div>
                </div>
            </div>

            <!-- Section des réponses -->
            <div class="reponses-section mt-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">
                        <i data-feather="list" class="me-2" style="width: 16px; height: 16px;"></i>
                        Réponses
                    </h6>
                    <button type="button" class="btn btn-sm btn-outline-primary add-reponse" data-question="${questionIndex}" title="Ajouter une réponse">
                        <i data-feather="plus" style="width: 14px; height: 14px;"></i>
                        Ajouter Réponse
                    </button>
                </div>
                <div class="reponses-container" id="reponses-${questionIndex}">
                    <div class="alert alert-info">
                        <i data-feather="info" class="me-2" style="width: 14px; height: 14px;"></i>
                        Aucune réponse ajoutée pour cette question
                    </div>
                </div>
            </div>
        `;

        questionsContainer.appendChild(questionRow);
        questionIndex++;

        // Masquer le message "aucune question"
        noQuestionsMessage.style.display = 'none';

        // Réinitialiser Feather Icons si disponibles
        if (window.feather) {
            feather.replace();
        }

        // Ajouter l'événement de suppression
        questionRow.querySelector('.remove-question').addEventListener('click', function() {
            questionRow.remove();
            updateQuestionNumbers();
            checkEmptyQuestions();
        });

        // Ajouter l'événement pour ajouter des réponses
        const addReponseBtn = questionRow.querySelector('.add-reponse');
        if (addReponseBtn) {
            addReponseBtn.addEventListener('click', function() {
                const questionId = this.getAttribute('data-question');
                addReponse(questionId);
            });
        }
    }

    // Fonction pour mettre à jour les numéros des questions
    function updateQuestionNumbers() {
        const questionRows = questionsContainer.querySelectorAll('.question-row');
        questionRows.forEach((row, index) => {
            const title = row.querySelector('h6');
            title.innerHTML = `<i data-feather="help-circle" class="me-2" style="width: 16px; height: 16px;"></i>Question #${index + 1}`;
        });
        questionIndex = questionRows.length;
    }

    // Fonction pour vérifier s'il y a des questions
    function checkEmptyQuestions() {
        const questionRows = questionsContainer.querySelectorAll('.question-row');
        if (questionRows.length === 0) {
            noQuestionsMessage.style.display = 'block';
        } else {
            noQuestionsMessage.style.display = 'none';
        }
    }

    // Fonction pour ajouter une nouvelle réponse
    function addReponse(questionId) {
        const reponsesContainer = document.getElementById(`reponses-${questionId}`);
        if (!reponsesContainer) {
            console.error('❌ Reponses container not found for question:', questionId);
            return;
        }

        // Supprimer le message "aucune réponse" s'il existe
        const noReponsesMessage = reponsesContainer.querySelector('.alert-info');
        if (noReponsesMessage) {
            noReponsesMessage.remove();
        }

        const reponseRow = document.createElement('div');
        reponseRow.className = 'reponse-row border rounded p-2 mb-2 bg-light';
        reponseRow.innerHTML = `
            <div class="d-flex justify-content-between align-items-start mb-2">
                <h6 class="mb-0">
                    <i data-feather="check-circle" class="me-2" style="width: 16px; height: 16px;"></i>
                    Réponse #${reponseIndex[questionId] + 1}
                </h6>
                <button type="button" class="btn btn-sm btn-outline-danger remove-reponse" title="Supprimer cette réponse">
                    <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                </button>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-2">
                        <label class="form-label small">
                            <i data-feather="align-right" class="me-1" style="width: 12px; height: 12px;"></i>
                            Réponse (Arabe) *
                        </label>
                        <input type="text" name="questions[${questionId}][reponses][${reponseIndex[questionId]}][titre_ar]"
                               class="form-control form-control-sm" required placeholder="Réponse en arabe">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-2">
                        <label class="form-label small">
                            <i data-feather="align-left" class="me-1" style="width: 12px; height: 12px;"></i>
                            Réponse (Anglais) *
                        </label>
                        <input type="text" name="questions[${questionId}][reponses][${reponseIndex[questionId]}][titre_en]"
                               class="form-control form-control-sm" required placeholder="Response in English">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-2">
                        <label class="form-label small">
                            <i data-feather="check" class="me-1" style="width: 12px; height: 12px;"></i>
                            Correcte
                        </label>
                        <div class="form-check">
                            <input type="checkbox" name="questions[${questionId}][reponses][${reponseIndex[questionId]}][is_correcte]"
                                   class="form-check-input" value="1">
                            <label class="form-check-label small">Cette réponse est correcte</label>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Ajouter la réponse au conteneur
        reponsesContainer.appendChild(reponseRow);
        reponseIndex[questionId]++;

        // Réinitialiser Feather Icons
        if (window.feather) {
            feather.replace();
        }

        // Ajouter l'événement de suppression
        const removeBtn = reponseRow.querySelector('.remove-reponse');
        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                reponseRow.remove();
                updateReponseNumbers(questionId);
                checkEmptyReponses(questionId);
            });
        }
    }

    // Fonction pour mettre à jour les numéros des réponses
    function updateReponseNumbers(questionId) {
        const reponsesContainer = document.getElementById(`reponses-${questionId}`);
        if (!reponsesContainer) return;

        const reponseRows = reponsesContainer.querySelectorAll('.reponse-row');
        reponseRows.forEach((row, index) => {
            const title = row.querySelector('h6');
            if (title) {
                title.innerHTML = `<i data-feather="check-circle" class="me-2" style="width: 16px; height: 16px;"></i>Réponse #${index + 1}`;
                if (window.feather) {
                    feather.replace();
                }
            }
        });
        reponseIndex[questionId] = reponseRows.length;
    }

    // Fonction pour vérifier s'il y a des réponses
    function checkEmptyReponses(questionId) {
        const reponsesContainer = document.getElementById(`reponses-${questionId}`);
        if (!reponsesContainer) return;

        const reponseRows = reponsesContainer.querySelectorAll('.reponse-row');
        if (reponseRows.length === 0) {
            reponsesContainer.innerHTML = `
                <div class="alert alert-info">
                    <i data-feather="info" class="me-2" style="width: 14px; height: 14px;"></i>
                    Aucune réponse ajoutée pour cette question
                </div>
            `;
            if (window.feather) {
                feather.replace();
            }
        }
    }

    // Événement pour ajouter une question
    addQuestionBtn.addEventListener('click', addQuestion);

    // Ajouter les événements de suppression aux questions existantes
    document.querySelectorAll('.remove-question').forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.question-row').remove();
            updateQuestionNumbers();
            checkEmptyQuestions();
        });
    });

    // Ajouter les événements pour les réponses existantes
    document.querySelectorAll('.remove-reponse').forEach(button => {
        button.addEventListener('click', function() {
            const reponseRow = this.closest('.reponse-row');
            const reponsesContainer = reponseRow.closest('.reponses-container');
            const questionId = reponsesContainer.id.replace('reponses-', '');
            reponseRow.remove();
            updateReponseNumbers(questionId);
            checkEmptyReponses(questionId);
        });
    });

    // Ajouter les événements pour ajouter des réponses aux questions existantes
    document.querySelectorAll('.add-reponse').forEach(button => {
        button.addEventListener('click', function() {
            const questionId = this.getAttribute('data-question');
            addReponse(questionId);
        });
    });

    // Validation du formulaire
    const form = document.getElementById('quizForm');
    form.addEventListener('submit', function(e) {
        const requiredFields = this.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });

        // Vérifier qu'il y a au moins une question
        const questionRows = questionsContainer.querySelectorAll('.question-row');
        if (questionRows.length === 0) {
            alert('Veuillez ajouter au moins une question au quiz.');
            e.preventDefault();
            return;
        }

        if (!isValid) {
            e.preventDefault();
            alert('Veuillez remplir tous les champs obligatoires.');
        }
    });

    // Validation en temps réel
    const inputs = form.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            if (this.hasAttribute('required') && this.value.trim()) {
                this.classList.remove('is-invalid');
            }
        });
    });

    // Handle question toggle status
    const questionToggles = document.querySelectorAll('.question-toggle');
    questionToggles.forEach(toggle => {
        toggle.addEventListener('change', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const questionId = this.dataset.questionId;
            const isChecked = this.checked;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            const url = `/questions/${questionId}/toggle-status`;

            console.log('Sending PUT request to:', url);

            fetch(url, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ is_active: isChecked })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the label
                    const label = this.nextElementSibling;
                    if (label) {
                        label.textContent = data.is_active ? 'Actif' : 'Inactif';
                    }

                    // Show toast notification
                    const toastHTML = `
                        <div class="toast-notification" style="position: fixed; top: 20px; right: 20px; background-color: ${data.is_active ? '#28a745' : '#dc3545'}; color: white; padding: 12px 20px; border-radius: 4px; z-index: 9999;">
                            ${data.message}
                        </div>
                    `;
                    document.body.insertAdjacentHTML('beforeend', toastHTML);

                    setTimeout(() => {
                        document.querySelector('.toast-notification').remove();
                    }, 2000);
                } else {
                    alert('Erreur: ' + data.message);
                    this.checked = !isChecked; // Revert the toggle
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Une erreur est survenue');
                this.checked = !isChecked; // Revert the toggle
            });
        });
    });

});
</script>
@endpush
