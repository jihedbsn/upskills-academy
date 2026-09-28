@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Nouveau Quiz</h1>
        <div class="page-actions">
            <a href="{{ route('quizzes.index') }}" class="btn btn-outline-secondary">
                <i data-feather="arrow-left" class="me-2"></i>
                Retour
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center">
                                <i data-feather="help-circle" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">Créer un Nouveau Quiz</h4>
                            <p class="text-white-50 mb-0">Remplissez les informations du quiz en arabe et en anglais</p>
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

                    <form method="POST" action="{{ route('quizzes.store') }}" id="quizForm">
                        @csrf

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
                                                   value="{{ old('name_ar') }}" required
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
                                                   value="{{ old('name_en') }}" required
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
                                                            <option value="{{ $type->id }}" {{ old('type_id') == $type->id ? 'selected' : '' }}>
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
                                                               value="{{ old('score', 100) }}" required
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
                                                           value="{{ old('max_attempts') }}"
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
                                            <!-- Les questions seront ajoutées ici dynamiquement -->
                                        </div>

                                        <div class="text-center py-3" id="noQuestionsMessage">
                                            <i data-feather="help-circle" class="text-muted" style="width: 48px; height: 48px;"></i>
                                            <p class="text-muted mt-2">Aucune question ajoutée</p>
                                            <p class="text-muted small">Cliquez sur "Ajouter une Question" pour commencer</p>
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
                                Créer le Quiz
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
// Attendre que le DOM soit chargé
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing quiz form...');

    // Initialiser les variables (use window for global access)
    window.questionIndex = 0;
    window.reponseIndex = {};

    // Récupérer les éléments DOM
    const questionsContainer = document.getElementById('questionsContainer');
    const noQuestionsMessage = document.getElementById('noQuestionsMessage');
    const addQuestionBtn = document.getElementById('addQuestionBtn');
    const form = document.getElementById('quizForm');

    // Vérifier que tous les éléments existent
    if (!questionsContainer) {
        console.error('❌ questionsContainer not found');
        return;
    }
    if (!noQuestionsMessage) {
        console.error('❌ noQuestionsMessage not found');
        return;
    }
    if (!addQuestionBtn) {
        console.error('❌ addQuestionBtn not found');
        return;
    }
    if (!form) {
        console.error('❌ quizForm not found');
        return;
    }

    console.log('✅ All elements found successfully');

    // Initialiser Feather Icons de manière sécurisée
    function initFeatherIcons() {
        if (typeof feather !== 'undefined') {
            try {
                feather.replace({
                    width: 14,
                    height: 14
                });
                console.log('✅ Feather Icons initialisées avec succès');
            } catch (error) {
                console.warn('⚠️ Erreur lors de l\'initialisation des icônes Feather:', error);
            }
        } else {
            console.warn('⚠️ Feather Icons non disponible');
        }
    }



    // Fonction pour ajouter une nouvelle question
    function addQuestion() {
        console.log('➕ Adding question, current index:', questionIndex);

        // Initialiser l'index des réponses pour cette question
        reponseIndex[questionIndex] = 0;

        const questionRow = document.createElement('div');
        questionRow.className = 'question-row border rounded p-3 mb-3';
        questionRow.innerHTML = `
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h6 class="mb-0">
                    <i data-feather="help-circle" class="me-2" style="width: 16px; height: 16px;"></i>
                    Question #${window.questionIndex + 1}
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
                        <input type="text" name="questions[${window.questionIndex}][name_ar]" class="form-control"
                               required placeholder="Question en arabe">
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="mb-3">
                        <label class="form-label">
                            <i data-feather="align-left" class="me-1" style="width: 14px; height: 14px;"></i>
                            Question (Anglais) *
                        </label>
                        <input type="text" name="questions[${window.questionIndex}][name_en]" class="form-control"
                               required placeholder="Question in English">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label class="form-label">
                            <i data-feather="award" class="me-1" style="width: 14px; height: 14px;"></i>
                            Points
                        </label>
                        <input type="number" name="questions[${window.questionIndex}][point]" class="form-control"
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
                    <button type="button" class="btn btn-sm btn-outline-primary add-reponse" data-question="${window.questionIndex}" title="Ajouter une réponse">
                        <i data-feather="plus" style="width: 14px; height: 14px;"></i>
                        Ajouter Réponse
                    </button>
                </div>
                <div class="reponses-container" id="reponses-${window.questionIndex}">
                    <div class="alert alert-info">
                        <i data-feather="info" class="me-2" style="width: 14px; height: 14px;"></i>
                        Aucune réponse ajoutée pour cette question
                    </div>
                </div>
            </div>
        `;

        // Ajouter la question au conteneur
        questionsContainer.appendChild(questionRow);
        questionIndex++;

        console.log('✅ Question added, new index:', questionIndex);

        // Masquer le message "aucune question"
        noQuestionsMessage.style.display = 'none';

        // Réinitialiser Feather Icons de manière sécurisée
        initFeatherIcons();

        // Ajouter l'événement de suppression
        const removeBtn = questionRow.querySelector('.remove-question');
        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                console.log('🗑️ Removing question');
                questionRow.remove();
                updateQuestionNumbers();
                checkEmptyQuestions();
            });
        }

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
            if (title) {
                title.innerHTML = `<i data-feather="help-circle" class="me-2" style="width: 16px; height: 16px;"></i>Question #${index + 1}`;
                // Réinitialiser les icônes après modification du HTML
                initFeatherIcons();
            }
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
        console.log('➕ Adding response for question:', questionId, 'index:', reponseIndex[questionId]);

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
                    Réponse #${window.reponseIndex[questionId] + 1}
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
                        <input type="text" name="questions[${questionId}][reponses][${window.reponseIndex[questionId]}][titre_ar]"
                               class="form-control form-control-sm" required placeholder="Réponse en arabe">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-2">
                        <label class="form-label small">
                            <i data-feather="align-left" class="me-1" style="width: 12px; height: 12px;"></i>
                            Réponse (Anglais) *
                        </label>
                        <input type="text" name="questions[${questionId}][reponses][${window.reponseIndex[questionId]}][titre_en]"
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
                            <input type="checkbox" name="questions[${questionId}][reponses][${window.reponseIndex[questionId]}][is_correcte]"
                                   class="form-check-input" value="1">
                            <label class="form-check-label small">Cette réponse est correcte</label>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Ajouter la réponse au conteneur
        reponsesContainer.appendChild(reponseRow);
        window.reponseIndex[questionId]++;

        console.log('✅ Response added, new index:', window.reponseIndex[questionId]);

        // Réinitialiser Feather Icons
        initFeatherIcons();

        // Ajouter l'événement de suppression
        const removeBtn = reponseRow.querySelector('.remove-reponse');
        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                console.log('🗑️ Removing response');
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
                initFeatherIcons();
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
            initFeatherIcons();
        }
    }

    // Événement pour ajouter une question
    addQuestionBtn.addEventListener('click', function(e) {
        console.log('🖱️ Add question button clicked');
        e.preventDefault();
        e.stopPropagation();
        addQuestion();
    });

    // Validation du formulaire
    form.addEventListener('submit', function(e) {
        console.log('📝 Form submission');

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

    console.log('🎉 Quiz form initialized successfully');
});
</script>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const importQuestionsBtn = document.getElementById('importQuestionsBtn');
    const quizSelectDropdown = document.getElementById('quizSelectDropdown');
    const questionsContainer = document.getElementById('questionsContainer');
    const noQuestionsMessage = document.getElementById('noQuestionsMessage');

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
                // window.questionIndex = 0; // REMOVED - continue from current index
                // window.reponseIndex = {}; // REMOVED - keep existing response indices

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


    function addImportedQuestionToForm(questionData) {
        const qIndex = window.questionIndex || 0;
        window.reponseIndex = window.reponseIndex || {};
        window.reponseIndex[qIndex] = 0;

        const questionRow = document.createElement('div');
        questionRow.className = 'question-row border rounded p-3 mb-3';
        questionRow.innerHTML = `
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h6 class="mb-0">Question #${qIndex + 1}</h6>
                <button type="button" class="btn btn-sm btn-outline-danger remove-question">
                    <i data-feather="trash-2"></i>
                </button>
            </div>
            <div class="row">
                <div class="col-md-5">
                    <div class="mb-3">
                        <label class="form-label">Question (Arabe) *</label>
                        <input type="text" name="questions[${qIndex}][name_ar]" class="form-control"
                               required value="${escapeHtml(questionData.name_ar || '')}">
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="mb-3">
                        <label class="form-label">Question (Anglais) *</label>
                        <input type="text" name="questions[${qIndex}][name_en]" class="form-control"
                               required value="${escapeHtml(questionData.name_en || '')}">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label class="form-label">Points</label>
                        <input type="number" name="questions[${qIndex}][point]" class="form-control" value="10">
                    </div>
                </div>
            </div>
            <div class="reponses-section mt-3">
                <h6>Réponses</h6>
                <div class="reponses-container" id="reponses-${qIndex}"></div>
                <button type="button" class="btn btn-sm btn-outline-primary add-reponse" data-question="${qIndex}">
                    <i data-feather="plus"></i> Ajouter Réponse
                </button>
            </div>
        `;

        questionsContainer.appendChild(questionRow);

        if (questionData.answers && questionData.answers.length > 0) {
            const reponsesContainer = questionRow.querySelector(`#reponses-${qIndex}`);
            questionData.answers.forEach(answerData => {
                addImportedAnswer(qIndex, answerData, reponsesContainer);
            });
        }

        window.questionIndex = qIndex + 1;
    }

    function addImportedAnswer(qIndex, answerData, container) {
        const rIndex = window.reponseIndex[qIndex] || 0;

        const reponseRow = document.createElement('div');
        reponseRow.className = 'reponse-row border rounded p-2 mb-2';
        reponseRow.innerHTML = `
            <div class="row align-items-center">
                <div class="col-md-5">
                    <input type="text" name="questions[${qIndex}][reponses][${rIndex}][titre_ar]"
                           class="form-control" required value="${escapeHtml(answerData.name_ar || '')}">
                </div>
                <div class="col-md-5">
                    <input type="text" name="questions[${qIndex}][reponses][${rIndex}][titre_en]"
                           class="form-control" required value="${escapeHtml(answerData.name_en || '')}">
                </div>
                <div class="col-md-1">
                    <div class="form-check">
                        <input type="checkbox" name="questions[${qIndex}][reponses][${rIndex}][is_correcte]"
                               class="form-check-input" value="1" ${answerData.is_correct ? 'checked' : ''}>
                    </div>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-reponse">
                        <i data-feather="x"></i>
                    </button>
                </div>
            </div>
        `;

        container.appendChild(reponseRow);
        window.reponseIndex[qIndex] = rIndex + 1;
    }

    function escapeHtml(text) {
        const map = {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'};
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }
});
</script>
@endpush
