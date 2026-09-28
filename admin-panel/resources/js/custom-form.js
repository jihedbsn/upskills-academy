// Modern Form JavaScript - Enhanced UX
document.addEventListener('DOMContentLoaded', function() {
    let currentTab = 0;
    const tabs = ['public', 'data', 'types', 'students'];
    let studentIndex = 0;
    let isAnimating = false;

    // Initialize form with modern animations
    initializeForm();

    function initializeForm() {
        setupTabNavigation();
        setupStudentManagement();
        setupFormValidation();
        setupProgressIndicator();
        setupModernInteractions();
        showTab(0);

        // Add loading animation
        document.body.classList.add('loaded');
    }

    // Modern tab navigation with smooth animations
    function setupTabNavigation() {
        // Next tab button with enhanced UX
        document.getElementById('next-tab').addEventListener('click', function() {
            if (!isAnimating && validateCurrentTab()) {
                animateTabTransition('next');
            }
        });

        // Previous tab button
        document.getElementById('prev-tab').addEventListener('click', function() {
            if (!isAnimating && currentTab > 0) {
                animateTabTransition('prev');
            }
        });

        // Tab click navigation with smooth transitions
        document.querySelectorAll('.nav-link').forEach((link, index) => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                if (!isAnimating && (index <= currentTab || validateCurrentTab())) {
                    animateTabTransition('direct', index);
                }
            });
        });
    }

    function animateTabTransition(direction, targetIndex = null) {
        isAnimating = true;
        const currentTabElement = document.getElementById(tabs[currentTab]);

        // Exit animation
        currentTabElement.style.transform = 'translateX(-20px)';
        currentTabElement.style.opacity = '0';

        setTimeout(() => {
            // Update tab index
            if (direction === 'next') {
                currentTab = Math.min(currentTab + 1, tabs.length - 1);
            } else if (direction === 'prev') {
                currentTab = Math.max(currentTab - 1, 0);
            } else if (direction === 'direct') {
                currentTab = targetIndex;
            }

            showTab(currentTab);

            // Enter animation
            const newTabElement = document.getElementById(tabs[currentTab]);
            newTabElement.style.transform = 'translateX(20px)';
            newTabElement.style.opacity = '0';

            setTimeout(() => {
                newTabElement.style.transform = 'translateX(0)';
                newTabElement.style.opacity = '1';
                isAnimating = false;
            }, 50);
        }, 200);
    }

    function showTab(index) {
        // Hide all tabs with smooth transitions
        document.querySelectorAll('.tab-pane').forEach(tab => {
            tab.classList.remove('show', 'active');
            tab.style.transform = 'translateX(0)';
            tab.style.opacity = '1';
        });
        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
        });

        // Show current tab
        const currentTabElement = document.getElementById(tabs[index]);
        currentTabElement.classList.add('show', 'active');
        document.getElementById(tabs[index] + '-tab').classList.add('active');

        // Update navigation buttons with smooth transitions
        const prevBtn = document.getElementById('prev-tab');
        const nextBtn = document.getElementById('next-tab');
        const submitBtn = document.getElementById('submit-btn');

        if (index === 0) {
            fadeOutElement(prevBtn);
        } else {
            fadeInElement(prevBtn);
        }

        if (index === tabs.length - 1) {
            fadeOutElement(nextBtn);
            fadeInElement(submitBtn);
        } else {
            fadeInElement(nextBtn);
            fadeOutElement(submitBtn);
        }

        // Update progress indicator with animation
        updateProgressIndicator(index);

        // Add success animation to completed tabs
        animateCompletedTabs(index);
    }

    function fadeInElement(element) {
        element.style.display = 'inline-block';
        element.style.opacity = '0';
        element.style.transform = 'translateY(10px)';

        setTimeout(() => {
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        }, 50);
    }

    function fadeOutElement(element) {
        element.style.opacity = '0';
        element.style.transform = 'translateY(10px)';

        setTimeout(() => {
            element.style.display = 'none';
        }, 200);
    }

    // Enhanced student management with modern animations
    function setupStudentManagement() {
        document.getElementById('add-student-btn').addEventListener('click', function() {
            addStudentRow();
        });
    }

    function addStudentRow() {
        const container = document.getElementById('students-container');
        const studentDiv = document.createElement('div');
        studentDiv.className = 'card mb-2 student-row';
        studentDiv.style.opacity = '0';
        studentDiv.style.transform = 'translateY(20px)';

        studentDiv.innerHTML = `
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Nom (Arabe) *</label>
                        <input type="text" name="students[${studentIndex}][name_ar]" class="form-control" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Nom (Anglais) *</label>
                        <input type="text" name="students[${studentIndex}][name_en]" class="form-control" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Langue *</label>
                        <select name="students[${studentIndex}][lang]" class="form-select" required>
                            <option value="">Sélectionner</option>
                            <option value="ar">Arabe</option>
                            <option value="en">Anglais</option>
                            <option value="fr">Français</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Ressource *</label>
                        <select name="students[${studentIndex}][resource_id]" class="form-select" required>
                            <option value="">Sélectionner une ressource</option>
                            @foreach($resources as $resource)
                                <option value="{{ $resource->id }}">{{ $resource->name ?? $resource->title }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-student">
                            <i data-feather="trash-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;

        container.appendChild(studentDiv);

        // Animate in
        setTimeout(() => {
            studentDiv.style.opacity = '1';
            studentDiv.style.transform = 'translateY(0)';
        }, 50);

        studentIndex++;

        // Add remove functionality with animation
        studentDiv.querySelector('.remove-student').addEventListener('click', function() {
            removeStudentRow(studentDiv);
        });

        // Initialize Feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }

    function removeStudentRow(studentDiv) {
        studentDiv.style.opacity = '0';
        studentDiv.style.transform = 'translateX(-100px)';

        setTimeout(() => {
            studentDiv.remove();
        }, 300);
    }

    // Enhanced form validation with modern feedback
    function setupFormValidation() {
        const form = document.getElementById('productForm');

        form.addEventListener('submit', function(e) {
            if (!validateAllTabs()) {
                e.preventDefault();
                showValidationErrors();
                shakeForm();
            } else {
                showSuccessAnimation();
            }
        });

        // Real-time validation with smooth feedback
        document.querySelectorAll('input, select, textarea').forEach(field => {
            field.addEventListener('blur', function() {
                validateField(this);
            });

            field.addEventListener('input', function() {
                if (this.classList.contains('is-invalid')) {
                    validateField(this);
                }
            });
        });
    }

    function validateCurrentTab() {
        const currentTabElement = document.getElementById(tabs[currentTab]);
        const fields = currentTabElement.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;

        fields.forEach(field => {
            if (!validateField(field)) {
                isValid = false;
            }
        });

        return isValid;
    }

    function validateAllTabs() {
        let isValid = true;

        tabs.forEach((tab, index) => {
            const tabElement = document.getElementById(tab);
            const fields = tabElement.querySelectorAll('input[required], select[required], textarea[required]');

            fields.forEach(field => {
                if (!validateField(field)) {
                    isValid = false;
                }
            });
        });

        return isValid;
    }

    function validateField(field) {
        const value = field.value.trim();
        const isValid = field.checkValidity();

        // Remove previous states
        field.classList.remove('is-invalid', 'is-valid', 'error-shake', 'success-animation');

        if (isValid && value !== '') {
            field.classList.add('is-valid');
            field.classList.add('success-animation');
        } else if (!isValid && value !== '') {
            field.classList.add('is-invalid');
            field.classList.add('error-shake');
        }

        return isValid;
    }

    function showValidationErrors() {
        // Show error message with modern design
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-danger modern-alert';
        errorDiv.innerHTML = `
            <div class="d-flex align-items-center">
                <i data-feather="alert-circle" class="me-3"></i>
                <div>
                    <strong>Erreurs de validation</strong>
                    <p class="mb-0">Veuillez corriger les erreurs dans le formulaire avant de continuer.</p>
                </div>
            </div>
        `;

        const form = document.getElementById('productForm');
        form.insertBefore(errorDiv, form.firstChild);

        // Animate in
        errorDiv.style.opacity = '0';
        errorDiv.style.transform = 'translateY(-20px)';

        setTimeout(() => {
            errorDiv.style.opacity = '1';
            errorDiv.style.transform = 'translateY(0)';
        }, 50);

        // Scroll to first error with smooth animation
        const firstError = document.querySelector('.is-invalid');
        if (firstError) {
            firstError.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }

        // Remove error message after 8 seconds
        setTimeout(() => {
            errorDiv.style.opacity = '0';
            errorDiv.style.transform = 'translateY(-20px)';
            setTimeout(() => errorDiv.remove(), 300);
        }, 8000);
    }

    function shakeForm() {
        const form = document.getElementById('productForm');
        form.classList.add('error-shake');
        setTimeout(() => form.classList.remove('error-shake'), 500);
    }

    function showSuccessAnimation() {
        const form = document.getElementById('productForm');
        form.classList.add('success-animation');
        setTimeout(() => form.classList.remove('success-animation'), 600);
    }

    // Enhanced progress indicator
    function setupProgressIndicator() {
        const progressContainer = document.createElement('div');
        progressContainer.className = 'progress-indicator';
        progressContainer.innerHTML = `
            <div class="progress-step" data-step="0">
                <div class="step-number">1</div>
                <div class="step-label">Public</div>
            </div>
            <div class="progress-step" data-step="1">
                <div class="step-number">2</div>
                <div class="step-label">Données</div>
            </div>
            <div class="progress-step" data-step="2">
                <div class="step-number">3</div>
                <div class="step-label">Types</div>
            </div>
            <div class="progress-step" data-step="3">
                <div class="step-number">4</div>
                <div class="step-label">Étudiants</div>
            </div>
        `;

        const form = document.getElementById('productForm');
        form.insertBefore(progressContainer, form.firstChild);
    }

    function updateProgressIndicator(currentIndex) {
        document.querySelectorAll('.progress-step').forEach((step, index) => {
            step.classList.remove('active', 'completed');

            if (index < currentIndex) {
                step.classList.add('completed');
            } else if (index === currentIndex) {
                step.classList.add('active');
            }
        });
    }

    function animateCompletedTabs(currentIndex) {
        document.querySelectorAll('.progress-step').forEach((step, index) => {
            if (index < currentIndex) {
                step.classList.add('pulse-animation');
                setTimeout(() => step.classList.remove('pulse-animation'), 1000);
            }
        });
    }

    // Modern interactions
    function setupModernInteractions() {
        // Add hover effects to cards
        document.querySelectorAll('.card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px) scale(1.02)';
            });

            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Add focus effects to form controls
        document.querySelectorAll('.form-control, .form-select').forEach(control => {
            control.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });

            control.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
            });
        });

        // Add ripple effect to buttons
        document.querySelectorAll('.btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                createRipple(e, this);
            });
        });
    }

    function createRipple(event, element) {
        const ripple = document.createElement('span');
        const rect = element.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = event.clientX - rect.left - size / 2;
        const y = event.clientY - rect.top - size / 2;

        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';
        ripple.classList.add('ripple');

        element.appendChild(ripple);

        setTimeout(() => ripple.remove(), 600);
    }

    // Enhanced auto-save with modern notifications
    function setupAutoSave() {
        const form = document.getElementById('productForm');
        let autoSaveTimer;

        form.addEventListener('input', function() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(() => {
                saveFormData();
            }, 2000);
        });
    }

    function saveFormData() {
        const form = document.getElementById('productForm');
        const formData = new FormData(form);

        // Save to localStorage
        const data = {};
        for (let [key, value] of formData.entries()) {
            data[key] = value;
        }

        localStorage.setItem('productFormData', JSON.stringify(data));

        // Show modern auto-save indicator
        showAutoSaveIndicator();
    }

    function showAutoSaveIndicator() {
        const indicator = document.createElement('div');
        indicator.className = 'auto-save-indicator';
        indicator.innerHTML = `
            <div class="d-flex align-items-center">
                <i data-feather="check-circle" class="me-2"></i>
                <span>Données sauvegardées automatiquement</span>
            </div>
        `;

        document.body.appendChild(indicator);

        // Animate out
        setTimeout(() => {
            indicator.style.opacity = '0';
            indicator.style.transform = 'translateX(100%)';
            setTimeout(() => indicator.remove(), 300);
        }, 3000);
    }

    // Load saved data with animation
    function loadSavedData() {
        const savedData = localStorage.getItem('productFormData');
        if (savedData) {
            const data = JSON.parse(savedData);
            const form = document.getElementById('productForm');

            Object.keys(data).forEach(key => {
                const field = form.querySelector(`[name="${key}"]`);
                if (field) {
                    field.value = data[key];
                    // Add subtle animation
                    field.classList.add('data-loaded');
                    setTimeout(() => field.classList.remove('data-loaded'), 1000);
                }
            });
        }
    }

    // Initialize everything
    addStudentRow();
    setupAutoSave();
    loadSavedData();

    // Add CSS for new animations
    addCustomStyles();
});

// Add custom CSS for new animations
function addCustomStyles() {
    const style = document.createElement('style');
    style.textContent = `
        .modern-alert {
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            box-shadow: 0 10px 25px -5px rgba(239, 68, 68, 0.3);
        }
        
        .pulse-animation {
            animation: pulse 1s ease-in-out;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: scale(0);
            animation: ripple-animation 0.6s linear;
            pointer-events: none;
        }
        
        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
        
        .focused {
            transform: scale(1.02);
        }
        
        .data-loaded {
            animation: dataLoad 1s ease-in-out;
        }
        
        @keyframes dataLoad {
            0% { background-color: rgba(16, 185, 129, 0.1); }
            100% { background-color: transparent; }
        }
        
        .loaded {
            animation: pageLoad 0.5s ease-out;
        }
        
        @keyframes pageLoad {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    `;
    document.head.appendChild(style);
}