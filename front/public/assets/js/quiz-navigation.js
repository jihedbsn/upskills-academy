/**
 * Quiz Navigation System - Navigation sans rechargement de page
 * Permet de naviguer entre les questions sans incrémenter le compteur
 */

class QuizNavigation {
    constructor(config) {
        this.currentQuestionIndex = config.currentQuestionIndex || 0;
        this.totalQuestions = config.totalQuestions || 0;
        this.timeRemaining = config.timeRemaining || 0;
        this.questionsData = config.questionsData || [];
        this.userAnswers = config.userAnswers || {};
        this.timerInterval = null;
        this.locale = config.locale || 'en';
        
        this.init();
    }
    
    init() {
        this.startTimer();
        this.attachAnswerEvents();
        this.attachNavigationEvents();
        this.checkAnswerSelection();
    }
    
    // Fonction pour démarrer le timer
    startTimer() {
        this.timerInterval = setInterval(() => {
            this.updateTimer();
        }, 1000);
    }
    
    // Fonction pour mettre à jour le timer
    updateTimer() {
        if (this.timeRemaining <= 0) {
            clearInterval(this.timerInterval);
            this.submitTimeout();
            return;
        }
        
        const minutes = Math.floor(this.timeRemaining / 60);
        const seconds = this.timeRemaining % 60;
        const timeString = (minutes < 10 ? '0' : '') + minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
        
        // Update all timer elements
        const timerElements = ['timerDesktop', 'timerMini', 'timerMobile', 'timerCentered'];
        timerElements.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                if (id === 'timerDesktop') {
                    const timerText = element.querySelector('.timer-text');
                    if (timerText) timerText.textContent = timeString;
                } else if (id === 'timerCentered') {
                    const timerText = element.querySelector('.timer-centered-text');
                    if (timerText) timerText.textContent = timeString;
                } else {
                    element.textContent = timeString;
                }
            }
        });
        
        // Update timer styling based on time remaining
        this.updateTimerStyling();
        
        this.timeRemaining--;
    }
    
    // Fonction pour mettre à jour le style du timer
    updateTimerStyling() {
        const timerCircles = document.querySelectorAll('.timer-circle, .timer-circle-large, .timer-mini, .timer-centered-circle');
        timerCircles.forEach(circle => {
            circle.classList.remove('timer-warning', 'timer-urgent');
            
            if (this.timeRemaining <= 30) {
                circle.classList.add('timer-urgent');
            } else if (this.timeRemaining <= 60) {
                circle.classList.add('timer-warning');
            }
        });
    }
    
    // Fonction pour naviguer vers une question spécifique
    navigateToQuestion(questionIndex) {
        if (questionIndex <= this.currentQuestionIndex) {
            this.loadQuestion(questionIndex);
        }
    }
    
    // Fonction pour charger une question
    loadQuestion(questionIndex) {
        if (questionIndex < 0 || questionIndex >= this.totalQuestions) {
            return;
        }
        
        // Sauvegarder les réponses actuelles
        this.saveCurrentAnswers();
        
        // Mettre à jour l'index de la question courante
        this.currentQuestionIndex = questionIndex;
        
        // Récupérer les données de la question
        const question = this.questionsData[questionIndex];
        
        // Mettre à jour l'interface
        this.updateQuestionDisplay(question, questionIndex);
        this.updateNavigationButtons(questionIndex);
        this.updateProgress(questionIndex);
        this.updateQuestionGrid(questionIndex);
        
        // Restaurer les réponses précédentes
        this.restoreAnswers(questionIndex);
    }
    
    // Fonction pour mettre à jour l'affichage de la question
    updateQuestionDisplay(question, questionIndex) {
        const questionText = document.getElementById('questionText');
        const answerOptions = document.getElementById('answerOptions');
        const questionCounter = document.getElementById('questionCounter');
        const currentQuestionNumber = document.getElementById('currentQuestionNumber');
        
        // Mettre à jour le texte de la question
        if (questionText) {
            questionText.textContent = this.locale === 'ar' ? question.name_ar : question.name_en;
        }
        
        // Mettre à jour le compteur
        if (questionCounter) questionCounter.textContent = questionIndex + 1;
        if (currentQuestionNumber) currentQuestionNumber.textContent = questionIndex + 1;
        
        // Générer les options de réponse
        if (answerOptions) {
            answerOptions.innerHTML = '';
            question.reponses.forEach((reponse, index) => {
                const optionHtml = `
                    <div class="answer-option-wrapper">
                        <input type="radio" name="answer_id" value="${reponse.id}" id="answer_${reponse.id}" class="answer-input" data-question="${questionIndex}">
                        <label for="answer_${reponse.id}" class="answer-option">
                            <div class="answer-marker">
                                <span class="answer-letter">${String.fromCharCode(65 + index)}</span>
                            </div>
                            <div class="answer-content">
                                <p class="answer-text">
                                    ${this.locale === 'ar' ? reponse.titre_ar : reponse.titre_en}
                                </p>
                            </div>
                            <div class="answer-check">
                                <i class="icon-check"></i>
                            </div>
                        </label>
                    </div>
                `;
                answerOptions.innerHTML += optionHtml;
            });
        }
        
        // Réattacher les événements
        this.attachAnswerEvents();
    }
    
    // Fonction pour mettre à jour les boutons de navigation
    updateNavigationButtons(questionIndex) {
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const btnText = document.getElementById('btnText');
        
        // Bouton précédent
        if (prevBtn) {
            if (questionIndex === 0) {
                prevBtn.style.display = 'none';
            } else {
                prevBtn.style.display = 'inline-flex';
            }
        }
        
        // Bouton suivant
        if (nextBtn && btnText) {
            if (questionIndex === this.totalQuestions - 1) {
                btnText.textContent = this.getTranslation('finish_quiz');
            } else {
                btnText.textContent = this.getTranslation('next_question');
            }
        }
        
        // Vérifier si des réponses sont sélectionnées
        this.checkAnswerSelection();
    }
    
    // Fonction pour mettre à jour la progression
    updateProgress(questionIndex) {
        const progress = ((questionIndex + 1) / this.totalQuestions) * 100;
        const progressFillDesktop = document.getElementById('progressFillDesktop');
        const progressFillMobile = document.getElementById('progressFillMobile');
        const progressPercentage = document.getElementById('progressPercentage');
        
        if (progressFillDesktop) progressFillDesktop.style.width = progress + '%';
        if (progressFillMobile) progressFillMobile.style.width = progress + '%';
        if (progressPercentage) progressPercentage.textContent = Math.round(progress) + '%';
    }
    
    // Fonction pour mettre à jour la grille de questions
    updateQuestionGrid(questionIndex) {
        const questionItems = document.querySelectorAll('.question-item');
        questionItems.forEach((item, index) => {
            item.classList.remove('completed', 'current', 'pending');
            
            if (index < questionIndex) {
                item.classList.add('completed');
            } else if (index === questionIndex) {
                item.classList.add('current');
            } else {
                item.classList.add('pending');
            }
        });
    }
    
    // Fonction pour sauvegarder les réponses actuelles
    saveCurrentAnswers() {
        const checkedAnswer = document.querySelector('.answer-input:checked');
        if (checkedAnswer) {
            this.userAnswers[this.currentQuestionIndex] = parseInt(checkedAnswer.value);
        } else {
            delete this.userAnswers[this.currentQuestionIndex];
        }
    }
    
    // Fonction pour restaurer les réponses
    restoreAnswers(questionIndex) {
        // Décocher toutes les réponses
        document.querySelectorAll('.answer-input').forEach(input => {
            input.checked = false;
        });
        
        // Cocher la réponse sauvegardée
        if (this.userAnswers[questionIndex]) {
            const input = document.getElementById('answer_' + this.userAnswers[questionIndex]);
            if (input) {
                input.checked = true;
            }
        }
        
        // Mettre à jour l'état du bouton
        this.checkAnswerSelection();
    }
    
    // Fonction pour vérifier la sélection des réponses
    checkAnswerSelection() {
        const checkedAnswer = document.querySelector('.answer-input:checked');
        const nextBtn = document.getElementById('nextBtn');
        
        if (nextBtn) {
            if (checkedAnswer) {
                nextBtn.disabled = false;
            } else {
                nextBtn.disabled = true;
            }
        }
    }
    
    // Fonction pour attacher les événements aux réponses
    attachAnswerEvents() {
        document.querySelectorAll('.answer-input').forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                this.checkAnswerSelection();
            });
        });
    }
    
    // Fonction pour attacher les événements de navigation
    attachNavigationEvents() {
        document.querySelectorAll('.question-item').forEach(item => {
            item.addEventListener('click', () => {
                const questionIndex = parseInt(item.dataset.question);
                this.navigateToQuestion(questionIndex);
            });
        });
    }
    
    // Fonction pour aller à la question précédente
    goToPreviousQuestion() {
        if (this.currentQuestionIndex > 0) {
            this.loadQuestion(this.currentQuestionIndex - 1);
        }
    }
    
    // Fonction pour aller à la question suivante
    goToNextQuestion() {
        if (this.currentQuestionIndex < this.totalQuestions - 1) {
            this.loadQuestion(this.currentQuestionIndex + 1);
        } else {
            // C'est la dernière question, soumettre le quiz
            this.submitQuiz();
        }
    }
    
    // Fonction pour soumettre le quiz
    submitQuiz() {
        // Sauvegarder les réponses actuelles
        this.saveCurrentAnswers();
        
        // Créer un formulaire temporaire pour soumettre
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = this.getSubmitUrl();
        
        // Ajouter le token CSRF
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = this.getCsrfToken();
        form.appendChild(csrfToken);
        
        // Ajouter le temps restant
        const timeInput = document.createElement('input');
        timeInput.type = 'hidden';
        timeInput.name = 'time_remaining';
        timeInput.value = this.timeRemaining;
        form.appendChild(timeInput);
        
        // Ajouter toutes les réponses
        Object.keys(this.userAnswers).forEach(questionIndex => {
            const answerInput = document.createElement('input');
            answerInput.type = 'hidden';
            answerInput.name = 'answer_ids[]';
            answerInput.value = this.userAnswers[questionIndex];
            form.appendChild(answerInput);
        });
        
        // Soumettre le formulaire
        document.body.appendChild(form);
        form.submit();
    }
    
    // Fonction pour soumettre le timeout avec alerte
    submitTimeout() {
        // Afficher l'alerte de timeout
        this.showTimeoutAlert();
        
        // Attendre 3 secondes puis soumettre
        setTimeout(() => {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = this.getTimeoutUrl();
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = this.getCsrfToken();
            
            form.appendChild(csrfToken);
            document.body.appendChild(form);
            form.submit();
        }, 3000);
    }
    
    // Fonction pour afficher l'alerte de timeout
    showTimeoutAlert() {
        // Créer l'alerte
        const alertDiv = document.createElement('div');
        alertDiv.className = 'timeout-alert';
        alertDiv.innerHTML = `
            <div class="timeout-alert-content">
                <div class="timeout-alert-icon">
                    <i class="icon-clock"></i>
                </div>
                <h3>${this.getTranslation('time_up')}</h3>
                <p>${this.getTranslation('time_up_message')}</p>
                <div class="timeout-alert-progress">
                    <div class="timeout-alert-progress-bar"></div>
                </div>
            </div>
        `;
        
        // Ajouter les styles
        const style = document.createElement('style');
        style.textContent = `
            .timeout-alert {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.8);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 9999;
                animation: fadeIn 0.3s ease;
            }
            
            .timeout-alert-content {
                background: white;
                padding: 40px;
                border-radius: 20px;
                text-align: center;
                max-width: 400px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            }
            
            .timeout-alert-icon {
                width: 80px;
                height: 80px;
                background: linear-gradient(135deg, #ef4444, #dc2626);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 20px;
                color: white;
                font-size: 32px;
                animation: pulse 1s infinite;
            }
            
            .timeout-alert h3 {
                color: #dc2626;
                font-size: 24px;
                margin: 0 0 15px 0;
            }
            
            .timeout-alert p {
                color: #666;
                font-size: 16px;
                margin: 0 0 25px 0;
            }
            
            .timeout-alert-progress {
                height: 6px;
                background: #f0f0f0;
                border-radius: 3px;
                overflow: hidden;
            }
            
            .timeout-alert-progress-bar {
                height: 100%;
                background: linear-gradient(90deg, #ef4444, #dc2626);
                border-radius: 3px;
                animation: progressShrink 3s linear;
            }
            
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            
            @keyframes progressShrink {
                from { width: 100%; }
                to { width: 0%; }
            }
        `;
        
        document.head.appendChild(style);
        document.body.appendChild(alertDiv);
    }
    
    // Fonction pour obtenir l'URL de soumission
    getSubmitUrl() {
        const quizId = window.location.pathname.split('/').pop();
        return `/quiz/${quizId}/answer`;
    }
    
    // Fonction pour obtenir l'URL de timeout
    getTimeoutUrl() {
        const quizId = window.location.pathname.split('/').pop();
        return `/quiz/${quizId}/timeout`;
    }
    
    // Fonction pour obtenir le token CSRF
    getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }
    
    // Fonction pour obtenir les traductions
    getTranslation(key) {
        const translations = {
            'finish_quiz': 'Finish Quiz',
            'next_question': 'Next Question',
            'previous': 'Previous',
            'quiz_warning': 'Are you sure you want to leave the quiz?',
            'time_up': 'Time\'s up!',
            'time_up_message': 'Quiz time has expired. Redirecting to results...',
            'score': 'Score',
            'correct_answers': 'Correct Answers',
            'time_taken': 'Time Taken',
            'timeout_warning': 'Warning',
            'timeout_message': 'Quiz automatically ended - time expired',
            'your_score': 'Your Score',
            'passing_score': 'Passing Score',
            'questions_review': 'Questions Review',
            'question': 'Question',
            'correct': 'Correct',
            'incorrect': 'Incorrect',
            'not_answered': 'Not Answered',
            'explanation': 'Explanation',
            'correct_answer_explanation': 'Here is the correct answer with detailed explanation.',
            'quiz_completed': 'Quiz completed successfully'
        };
        return translations[key] || key;
    }
    
    // Fonction pour nettoyer les ressources
    destroy() {
        if (this.timerInterval) {
            clearInterval(this.timerInterval);
        }
    }
}

// Exposer la classe globalement
window.QuizNavigation = QuizNavigation;
