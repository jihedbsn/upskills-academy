<div class="pt-60 lg:pt-40 to-over">
  @php
    $hasPurchased = auth()->check() && $product->hasStudentAccess(auth()->id());
    $studentId = auth()->id();
    $hasInstallmentOrder = isset($installmentContentInfo) && $installmentContentInfo && ($installmentContentInfo['has_installment_order'] ?? false);
    $isInstallmentSuspended = $hasInstallmentOrder ? ($installmentContentInfo['is_suspended'] ?? false) : false;
    $paidInstallmentMonths = (int) ($installmentContentInfo['unlocked_months'] ?? 0);
    $installmentService = app(\App\Services\InstallmentService::class);
    $enrollmentRecord = isset($enrollment) ? $enrollment : null;
    $quizQuizzes = $product->quizzes ? $product->quizzes->filter(function($quiz) {
        return $quiz->type_id == 2; // type_id = 2 pour les quizzes
    }) : collect();
    $examQuizzes = $product->quizzes ? $product->quizzes->filter(function($quiz) {
        return $quiz->type_id == 1; // type_id = 1 pour les exams
    }) : collect();
    $practicalExamQuizzes = $product->quizzes ? $product->quizzes->filter(function($quiz) {
        return $quiz->type_id == 3; // type_id = 3 pour les examens pratiques
    }) : collect();

    // Question counts
    $examQuestionsCount = $examQuizzes->sum(function($quiz) {
        return $quiz->pivot->nb_question_affiche ?? $quiz->questions->count();
    });
    $quizQuestionsCount = $quizQuizzes->sum(function($quiz) {
        return $quiz->pivot->nb_question_affiche ?? $quiz->questions->count();
    });

    // ==== COMPLETION STATUS ====
    $allQuizzesCompleted = $hasPurchased && $quizQuizzes->every(function($quiz) use ($product, $studentId) {
        $result = $product->getStudentQuizResult($studentId, $quiz->id);
        return $result && $result->success;
    });

    $allExamsCompleted = $hasPurchased && $examQuizzes->every(function($quiz) use ($product, $studentId) {
        $result = $product->getStudentQuizResult($studentId, $quiz->id);
        return $result && $result->success;
    });

    $allPracticalExamsCompleted = $hasPurchased && $practicalExamQuizzes->every(function($quiz) use ($product, $studentId) {
        $result = $product->getStudentQuizResult($studentId, $quiz->id);
        return $result && $result->success;
    });

    // ==== STAGE (INTERNSHIP) ====
    $hasStage = $product->is_stage;
    $stageCompleted = false;
    $stagePending = false;

    if ($hasPurchased && $hasStage) {
        $stageSubmission = \App\Models\StudentStageCourse::where('student_id', $studentId)
            ->where('product_id', $product->id)
            ->latest()
            ->first();

        if ($stageSubmission) {
            // Force fresh from DB to avoid cache issues
            $stageSubmission->refresh();

            if ($stageSubmission->is_valid == 1) {
                $stageCompleted = true;
                $stagePending = false;
            } elseif (in_array($stageSubmission->is_valid, [0, -1])) {
                $stageCompleted = false;
                $stagePending = true; // Pending or rejected
            }
        }
    }

    // ==== PRACTICAL EXAM ====
    $linkedTrainingCasesCount = $product->trainingCases()->count();
    $hasPracticalExam = (bool) $product->has_practical_exam || !empty($product->practical_exam_type) || $linkedTrainingCasesCount > 0;
    $practicalExamCompleted = false;
    $practicalExamAttempt = null;
    $canStartPracticalExam = false;
    $trainingCasesCount = 0;

    if ($hasPurchased && $hasPracticalExam) {
        $practicalExamAttempt = \App\Models\PracticalExamAttempt::where('user_id', $studentId)
            ->where('product_id', $product->id)
            ->latest()
            ->first();

        $practicalExamCompleted = $practicalExamAttempt && $practicalExamAttempt->status === 'passed';
        $trainingCasesCount = $linkedTrainingCasesCount;
    }

    // ==== ACCESS CONTROL LOGIC ====
    // 1. Quiz must be completed first
    $canAccessQuiz = $hasPurchased;
    $quizBlocked = false;
    $quizBlockReason = null;

    // 2. Stage is available immediately after purchase (no quiz completion required)
    $canAccessStage = $hasPurchased;
    $stageBlocked = !$canAccessStage;
    $stageBlockReason = !$hasPurchased ? 'Purchase course to access' : null;

    // 3. Exam is available after Quiz completion only
    $canAccessExam = $hasPurchased && $allQuizzesCompleted;
    $examBlocked = !$canAccessExam;
    $examBlockReason = null;
    if (!$allQuizzesCompleted) {
        $examBlockReason = 'Complete all quizzes first';
    }

    // 4. Practical Exam is available after Exam is completed AND Stage is validated (if applicable)
    $stageRequirementMet = !$hasStage || $stageCompleted; // If no stage, requirement is met
    $canAccessPracticalExam = $hasPurchased && $allExamsCompleted && $stageRequirementMet;
    $practicalExamBlocked = !$canAccessPracticalExam;
    $practicalExamBlockReason = null;
    if (!$allExamsCompleted) {
        $practicalExamBlockReason = 'Complete the exam first';
    } elseif ($hasStage && !$stageCompleted) {
        $practicalExamBlockReason = 'Submit and get your internship work validated first';
    }

    // Video exam logic (if exists)
    $videoExamCompleted = false;
    if ($hasPurchased && $product->is_exam_video) {
        $videoExamSubmission = \App\Models\StudentVideoExam::where('student_id', $studentId)
            ->where('product_id', $product->id)
            ->where('is_valid', 1)
            ->exists();
        $videoExamCompleted = $videoExamSubmission;
    }
  @endphp

  <script>
    console.log('=== COURSE EXAM PAGE DEBUG ===');
    console.log('Product ID:', {{ $product->id }});
    console.log('Product has_practical_exam field:', {{ isset($product->has_practical_exam) ? ($product->has_practical_exam ? 'true' : 'false') : 'NOT SET' }});
    console.log('Has purchased:', {{ $hasPurchased ? 'true' : 'false' }});
    console.log('All quizzes completed:', {{ $allQuizzesCompleted ? 'true' : 'false' }});
    console.log('All exams completed:', {{ $allExamsCompleted ? 'true' : 'false' }});
    console.log('Quiz count:', {{ $quizQuizzes->count() }});
    console.log('Exam count:', {{ $examQuizzes->count() }});
    console.log('Practical exam quiz count (type 3):', {{ $practicalExamQuizzes->count() }});
    console.log('Linked training cases:', {{ $linkedTrainingCasesCount }});
  </script>

  <!-- Section Quizzes -->
  @if($quizQuizzes->count() > 0)
    <div class="exam-section mb-50">
      <div class="section-header mb-30" onclick="toggleSection('quizzes-section')">
        <div class="d-flex items-center justify-between cursor-pointer">
          <div class="d-flex items-center">
            <div class="d-flex justify-center items-center size-48 rounded-full bg-gradient-to-r from-orange-400 to-orange-500 mr-20 shadow-sm">
              <div class="icon-file-text text-16 text-white"></div>
            </div>
            <div>
              <div class="d-flex items-center">
                <h4 class="text-20 fw-600 text-dark-1">{{ __('messages.quizzes') }}</h4>
                @if($allQuizzesCompleted)
                  <div class="section-success-badge ml-15">
                    <div class="icon-check-circle text-14" style="color: #059669;"></div>
                    <span class="text-12 fw-500" style="color: #059669;">Completed</span>
                  </div>
                @endif
              </div>
              <div class="section-stats">
                <span class="stat-item">{{ $quizQuizzes->count() }} {{ __('messages.quizzes') }}</span>
                <span class="stat-separator">•</span>
                <span class="stat-item">{{ $quizQuestionsCount }} {{ __('messages.questions') }}</span>
              </div>
            </div>
          </div>
          <div class="section-toggle">
            <div class="toggle-arrow" id="quizzes-arrow">
              <div class="icon-chevron-down"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="section-content" id="quizzes-section">
      <div class="accordion -block-2 text-left js-accordion">
        @foreach($quizQuizzes as $index => $quiz)
          @php
            $quizResult = auth()->check() ? $product->getStudentQuizResult(auth()->id(), $quiz->id) : null;
            $isExpired = auth()->check() && isset($enrollment) && $enrollment && $enrollment->is_expired;
            $canTakeQuiz = $hasPurchased && !$isExpired && (!$quizResult || ($quizResult && !$quizResult->isBlocked()));
            $isCompleted = $quizResult && $quizResult->success;
            $displayQuestionCount = $quiz->pivot->nb_question_affiche ?? $quiz->questions->count();
            $pointsValue = $displayQuestionCount;
            $successRate = $quiz->pivot->score_success;
            $quizMaxAttemptsDisplay = $quiz->type_id == 1
                ? ($product->max_exam_attempts ?? 3)
                : ($quiz->max_attempts ?? '∞');
          @endphp

          @php
            $isLockedByInstallment = false;
            if ($hasPurchased && $isInstallmentSuspended) {
                $isLockedByInstallment = true;
            } elseif ($hasPurchased) {
                $isLockedByInstallment = ! $installmentService->isQuizUnlockedForInstallmentProgress($paidInstallmentMonths, $quiz->pivot->installment_month ?? null);
            }
            $isLockedByPurchaseSchedule = $hasPurchased && ! $installmentService->isUnlockedAfterPurchaseDays($enrollmentRecord, $quiz->pivot->opens_after_purchase_days ?? null);
            $isQuizGated = $isLockedByInstallment || $isLockedByPurchaseSchedule;
          @endphp

          <div class="accordion__item quiz-item {{ $isQuizGated ? 'opacity-75' : '' }}">
            <div class="accordion__button py-20 px-30 {{ $isQuizGated ? 'bg-light-6' : 'bg-light-4' }}">
              <div class="d-flex items-center justify-between">
                <div class="d-flex items-center">
                  <div class="accordion__icon">
                    @if($isQuizGated)
                      <div class="icon text-light-1"><i class="fas fa-lock" style="font-size: 14px;"></i></div>
                      <div class="icon text-light-1"><i class="fas fa-lock" style="font-size: 14px;"></i></div>
                    @else
                      <div class="icon" data-feather="chevron-down"></div>
                      <div class="icon" data-feather="chevron-up"></div>
                    @endif
                  </div>
                  <div class="quiz-info">
                  <span class="text-17 fw-500 {{ $isQuizGated ? 'text-light-1' : 'text-dark-1' }}">{{ app()->getLocale() == 'ar' ? $quiz->name_ar : $quiz->name_en }}</span>
                  @if($isCompleted)
                      <div class="status-badge completed">
                        <div class="icon-check text-10"></div>
                        <span>{{ __('messages.completed') }}</span>
                      </div>
                    @elseif($quizResult && $quizResult->isBlocked())
                      <div class="status-badge blocked">
                        <div class="icon-lock text-10"></div>
                        <span>{{ __('messages.blocked') }}</span>
                      </div>
                    @elseif($quizResult && !$quizResult->success)
                      <div class="status-badge failed">
                        <div class="icon-x text-10"></div>
                        <span>{{ __('messages.failed') }}</span>
                      </div>
                    @elseif($isQuizGated)
                      <div class="status-badge blocked">
                        <div class="icon-lock text-10"></div>
                        <span>{{ __('messages.locked') ?? 'Locked' }}</span>
                      </div>
                    @else
                      <div class="status-badge available">
                        <div class="icon-play text-10"></div>
                        <span>{{ __('messages.available') }}</span>
                    </div>
                  @endif
                </div>
                </div>
                <div class="quiz-meta">
                  <div class="meta-item">
                    <div class="icon-file-text text-12 text-light-1"></div>
                    <span>{{ $displayQuestionCount }} {{ __('messages.questions') }}</span>
                  </div>
                  <div class="meta-item">
                    <div class="icon-star text-12 text-light-1"></div>
                    <span>{{ $pointsValue }} {{ __('messages.points') }}</span>
                  </div>
                  @if(!is_null($successRate))
                    <div class="meta-item">
                      <div class="icon-target text-12 text-light-1"></div>
                      <span>{{ __('messages.success_rate') }}: {{ $successRate }}%</span>
                    </div>
                  @endif
                  @if($quizResult && !$quizResult->success)
                    <div class="attempts-info">
                      <span class="text-12 text-orange-1">{{ $quizResult->attempts }}/{{ $quizMaxAttemptsDisplay }} {{ __('messages.attempts') }}</span>
                    </div>
                  @endif
                </div>
              </div>
            </div>

            <div class="accordion__content">
              <div class="accordion__content__inner px-30 py-30">
                <div class="y-gap-20">
                  <!-- Quiz Info -->
                  <div class="d-flex justify-between items-center mb-20">
                    <div class="d-flex items-center">
                      <div class="d-flex justify-center items-center size-36 rounded-full bg-gradient-to-r from-orange-400 to-orange-500 mr-12 shadow-sm">
                        <div class="icon-file-text text-12 text-white"></div>
                      </div>
                      <div>
                        <div class="text-16 fw-500 text-dark-1">{{ app()->getLocale() == 'ar' ? $quiz->name_ar : $quiz->name_en }}</div>
                        @if($quiz->type)
                          <div class="text-14 text-light-1">{{ app()->getLocale() == 'ar' ? $quiz->type->titre_ar : $quiz->type->titre_en }}</div>
                        @endif
                      </div>
                    </div>

                  <div class="d-flex x-gap-20 items-center">
                      @if($hasPurchased)
                        @if($isCompleted)
                          <div class="text-14 lh-1 text-green-1">{{ __('messages.quiz_completed') }}</div>
                        @elseif($quizResult && $quizResult->isBlocked())
                          <div class="text-14 lh-1 text-red-1">{{ __('messages.quiz_blocked') }}</div>
                        @elseif($isQuizGated)
                          <div class="text-14 lh-1 text-warning">
                            <i class="fas fa-lock me-1"></i>{{ __('messages.locked') ?? 'Locked' }}
                          </div>
                        @elseif($canTakeQuiz)
                          <button onclick="openQuizModal({{ $quiz->id }}, {{ json_encode(app()->getLocale() == 'ar' ? $quiz->name_ar : $quiz->name_en) }}, {{ (int) $displayQuestionCount }}, {{ (int) $pointsValue }}, 'quiz')"
                                  class="text-14 lh-1 text-purple-1 underline bg-none border-none cursor-pointer">
                            {{ __('messages.start_quiz') }}
                          </button>
                        @endif
                      @else
                        <div class="text-14 lh-1 text-light-1">{{ __('messages.purchase_required') }}</div>
                      @endif
                      <span class="text-14 lh-1 text-light-1">
                        {{ __('messages.points') }}: {{ $pointsValue }}
                        @if(!is_null($successRate))
                          • {{ __('messages.success_rate') }}: {{ $successRate }}%
                        @endif
                      </span>
                    </div>
                  </div>

                  @if($quizResult)
                    <div class="bg-light-4 rounded-8 p-20">
                      <div class="d-flex items-center justify-between">
                        <div class="text-14 text-dark-1">
                          <strong>{{ __('messages.last_attempt') }}:</strong> {{ $quizResult->created_at->format('d/m/Y H:i') }}
                        </div>
                        <div class="text-14 text-dark-1">
                          <strong>{{ __('messages.score') }}:</strong> {{ number_format($quizResult->score, 1) }}%
                        </div>
                      </div>
                      @if($quizResult->attempts > 0)
                        <div class="text-14 text-dark-1 mt-10">
                          <strong>{{ __('messages.attempts') }}:</strong> {{ $quizResult->attempts }}/{{ $quizMaxAttemptsDisplay }}
                        </div>
                      @endif
                    </div>
                  @endif
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
      </div>
    </div>
  @endif

  <!-- Section Stage Course -->
  @if($product->is_stage)
    <div class="exam-section mb-50">
      <div class="section-header mb-30" onclick="toggleSection('stage-section')">
        <div class="d-flex items-center justify-between cursor-pointer">
          <div class="d-flex items-center">
            <div class="d-flex justify-center items-center size-48 rounded-full bg-gradient-to-r from-green-400 to-green-500 mr-20 shadow-sm">
              <div class="icon-briefcase text-16 text-white"></div>
            </div>
            <div>
              <div class="d-flex items-center">
                <h4 class="text-20 fw-600 text-dark-1">{{ __('messages.stage_course') }}</h4>
                @if($stageCompleted)
                  <div class="section-success-badge ml-15">
                    <div class="icon-check-circle text-14" style="color: #059669;"></div>
                    <span class="text-12 fw-500" style="color: #059669;">Completed</span>
                  </div>
                @endif
              </div>
              <div class="section-stats">
                <span class="stat-item">{{ __('messages.practical_work') }}</span>
                <span class="stat-separator">•</span>
                <span class="stat-item">{{ __('messages.certification_required') }}</span>
              </div>
            </div>
          </div>
          <div class="section-toggle">
            <div class="toggle-arrow" id="stage-arrow">
              <div class="icon-chevron-down"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="section-content" id="stage-section">

      <!-- Description du Stage depuis la variation selon la langue -->
      @php
        $currentLang = app()->getLocale();
        $stageVariation = $product->variations()->where('langue', $currentLang)->first();
      @endphp

      @if($stageVariation && $stageVariation->description_stage)
        <div class="description-card mb-30">
          <div class="d-flex items-center mb-15">
            <div class="d-flex justify-center items-center size-36 rounded-full bg-gradient-to-r from-green-400 to-green-500 mr-15 shadow-sm">
              <div class="icon-info text-12 text-white"></div>
            </div>
            <h5 class="text-16 fw-600 text-dark-1">{{ __('messages.stage_description') }}</h5>
          </div>
          <div class="text-14 text-dark-1 leading-relaxed pl-51">
            {!! $stageVariation->description_stage !!}
          </div>
        </div>
      @endif

      @if($hasPurchased)
        @php
          // Vérifier si l'étudiant peut soumettre un stage (tous les quiz terminés)
          $canSubmitStage = $product->canStudentSubmitStage(auth()->id());
          // Récupérer la dernière soumission de stage pour décider d'afficher ou non le formulaire
          $existingStageSubmission = \App\Models\StudentStageCourse::where('student_id', auth()->id())
              ->where('product_id', $product->id)
              ->orderByDesc('id')
              ->first();

          // Force refresh to get latest data from database
          if ($existingStageSubmission) {
              $existingStageSubmission->refresh();
          }
        @endphp

        <!-- STAGE BLOCKED: Prerequisites not met -->
        @if($stageBlocked)
          <div class="blocked-section-card mb-30">
            <div class="blocked-content">
              <div class="blocked-icon">
                <i class="fas fa-lock text-28 text-orange-1"></i>
              </div>
              <h5 class="text-16 fw-600 text-dark-1 mb-8">{{ __('messages.stage_locked') }}</h5>
              <p class="text-14 text-light-1 mb-15">{{ $stageBlockReason }}</p>
              <div class="prerequisites-list">
                <div class="prerequisite-item">
                  <i class="fas fa-{{ $allQuizzesCompleted ? 'check-circle text-green-1' : 'circle text-light-1' }} text-12 mr-8"></i>
                  <span class="text-13 {{ $allQuizzesCompleted ? 'text-dark-1 fw-500' : 'text-light-1' }}">{{ __('messages.complete_all_quizzes') }}</span>
                </div>
              </div>
            </div>
          </div>
        @else
          <!-- Stage Documents Download Section -->
          @php
            $stageDocuments = \App\Models\StageDocument::where('product_id', $product->id)->get();
          @endphp

          @if($stageDocuments->count() > 0)
          <div class="stage-documents-section mb-30">
            <div class="rounded-16 bg-white shadow-2 px-30 py-30">
              <h5 class="text-17 fw-500 text-dark-1 mb-20">{{ __('messages.required_documents') }}</h5>

              <div class="row y-gap-15">
                @foreach($stageDocuments as $doc)
                <div class="col-lg-6">
                  <div class="d-flex items-center justify-between px-20 py-15 rounded-8 border-light">
                    <div class="text-15 fw-500 text-dark-1">{{ $doc->type_name }}</div>
                    <a href="/{{ app()->getLocale() }}/stage-documents/{{ $doc->id }}/download"
                       class="text-14 fw-500" style="color: #0071ae;">
                      {{ __('messages.download') }}
                    </a>
                  </div>
                </div>
                @endforeach
              </div>
            </div>
          </div>
          @endif

          <!-- Vérifier si l'étudiant a déjà soumis -->
          <div id="stageSubmissionStatus" class="mb-30">
            <!-- Le contenu sera chargé via AJAX -->
          </div>

          @if($canSubmitStage && (!$existingStageSubmission || $existingStageSubmission->is_valid == -1))
          <!-- Formulaire de soumission amélioré -->
          <div class="stage-submission-card" id="stageSubmissionCard">
          <div class="submission-header">
            <div class="d-flex items-center mb-20">
              <div class="d-flex justify-center items-center size-48 rounded-full mr-20 shadow-sm" style="background: linear-gradient(135deg, #0071ae 0%, #00a8cc 100%);">
                <div class="icon-upload text-16 text-white"></div>
              </div>
              <div>
                <h5 class="text-18 fw-600 text-dark-1">{{ __('messages.submit_stage_work') }}</h5>
              </div>
            </div>
          </div>

          <form id="stageSubmissionForm" enctype="multipart/form-data" class="stage-form">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">

            <div class="form-grid">
              <!-- Fichier 1 -->
              <div class="form-group">
                <label class="form-label">
                  <div class="label-icon">
                    <div class="icon-file text-14"></div>
                  </div>
                  <span>{{ __('messages.file_1') }} *</span>
                </label>
                <div class="file-upload-area">
                  <input type="file"
                         id="stageFile1"
                         name="file1"
                         class="file-input"
                         accept="image/*,.pdf"
                         required>
                  <div class="upload-content">
                    <div class="upload-icon">
                      <div class="icon-upload text-24 text-light-1"></div>
                    </div>
                    <div class="upload-text">
                      <span class="text-14 fw-500 text-dark-1">{{ __('messages.click_to_upload') }}</span>
                      <span class="text-12 text-light-1">{{ __('messages.file_requirements') }} ({{ __('messages.max_size') }}: 10MB)</span>
                    </div>
                  </div>
                </div>
                <div class="file-preview" id="file1Preview"></div>
              </div>

              <!-- Fichier 2 -->
              <div class="form-group">
                <label class="form-label">
                  <div class="label-icon">
                    <div class="icon-file text-14"></div>
                  </div>
                  <span>{{ __('messages.file_2') }} ({{ __('messages.optional') }})</span>
                </label>
                <div class="file-upload-area">
                  <input type="file"
                         id="stageFile2"
                         name="file2"
                         class="file-input"
                         accept="image/*,.pdf">
                  <div class="upload-content">
                    <div class="upload-icon">
                      <div class="icon-upload text-24 text-light-1"></div>
                    </div>
                    <div class="upload-text">
                      <span class="text-14 fw-500 text-dark-1">{{ __('messages.click_to_upload') }}</span>
                      <span class="text-12 text-light-1">{{ __('messages.file_requirements') }} ({{ __('messages.max_size') }}: 10MB)</span>
                    </div>
                  </div>
                </div>
                <div class="file-preview" id="file2Preview"></div>
              </div>
            </div>

            <!-- Bouton de soumission -->
            <div class="form-actions">
              <button type="submit" class="submit-btn" id="submitStageBtn">
                <div class="btn-icon">
                  <div class="icon-upload text-16"></div>
                </div>
                <span>{{ __('messages.submit_stage_work') }}</span>
              </button>
            </div>
          </form>
        </div>
        @else
          <!-- Message si l'étudiant ne peut pas encore soumettre -->
          @if($stageCompleted)
            <!-- Stage validated by admin -->
            <div class="locked-stage-card">
              <div class="locked-content">
                <div class="locked-icon">
                  <i class="fas fa-check-circle text-32 text-green-1"></i>
                </div>
                <h5 class="text-18 fw-600 text-dark-1 mb-10">{{ __('messages.stage_validated_title') }}</h5>
                <p class="text-14 text-light-1 mb-20">{{ __('messages.stage_validated_message') }}</p>
              </div>
            </div>
          @elseif($stagePending)
            <!-- Stage submitted but awaiting admin validation -->
            <div class="locked-stage-card">
              <div class="locked-content">
                <div class="locked-icon">
                  <i class="fas fa-hourglass-half text-32 text-orange-1"></i>
                </div>
                <h5 class="text-18 fw-600 text-dark-1 mb-10">{{ __('messages.stage_work_uploaded') }}</h5>
                <p class="text-14 text-light-1 mb-20">{{ __('messages.stage_work_uploaded_message') }}</p>
              </div>
            </div>
          @endif
        @endif
        @endif
      @else
        <div class="locked-stage-card">
          <div class="locked-content">
            <div class="locked-icon">
              <div class="icon-lock text-32 text-light-1"></div>
            </div>
            <h5 class="text-18 fw-600 text-dark-1 mb-10">{{ __('messages.stage_course_locked') }}</h5>
            <p class="text-14 text-light-1 mb-20">{{ __('messages.purchase_course_to_access_stage') }}</p>
            <a href="#" class="purchase-btn">
              <div class="btn-icon">
                <div class="icon-shopping-cart text-14"></div>
              </div>
              <span>{{ __('messages.purchase_course') }}</span>
            </a>
          </div>
        </div>
      @endif
      </div>
    </div>
  @endif

  <!-- Section Exams -->
  @if($examQuizzes->count() > 0)
    <div class="exam-section mb-50">
      <div class="section-header mb-30" onclick="toggleSection('exams-section')">
        <div class="d-flex items-center justify-between cursor-pointer">
          <div class="d-flex items-center">
            <div class="d-flex justify-center items-center size-48 rounded-full bg-gradient-to-r from-purple-400 to-purple-500 mr-20 shadow-sm">
              <div class="icon-award text-16 text-white"></div>
            </div>
            <div>
              <div class="d-flex items-center">
                <h4 class="text-20 fw-600 text-dark-1">{{ __('messages.exams') }}</h4>
                @if($allExamsCompleted)
                  <div class="section-success-badge ml-15">
                    <div class="icon-check-circle text-14" style="color: #059669;"></div>
                    <span class="text-12 fw-500" style="color: #059669;">Completed</span>
                  </div>
                @endif
              </div>
              <div class="section-stats">
                <span class="stat-item">{{ $examQuizzes->count() }} {{ __('messages.exams') }}</span>
                <span class="stat-separator">•</span>
                <span class="stat-item">{{ $examQuestionsCount }} {{ __('messages.questions') }}</span>
              </div>
            </div>
          </div>
          <div class="section-toggle">
            <div class="toggle-arrow" id="exams-arrow">
              <div class="icon-chevron-down"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="section-content" id="exams-section">

    <!-- EXAM BLOCKED: Prerequisites not met -->
    @if($examBlocked)
      <div class="blocked-section-card mb-30">
        <div class="blocked-content">
          <div class="blocked-icon">
            <i class="fas fa-lock text-28 text-orange-1"></i>
          </div>
          <h5 class="text-16 fw-600 text-dark-1 mb-8">{{ __('messages.exams_locked') }}</h5>
          <p class="text-14 text-light-1 mb-15">{{ $examBlockReason }}</p>
          <div class="prerequisites-list">
            <div class="prerequisite-item">
              <i class="fas fa-{{ $allQuizzesCompleted ? 'check-circle text-green-1' : 'circle text-light-1' }} text-12 mr-8"></i>
              <span class="text-13 {{ $allQuizzesCompleted ? 'text-dark-1 fw-500' : 'text-light-1' }}">{{ __('messages.complete_all_quizzes') }}</span>
            </div>
            @if($hasStage)
              <div class="prerequisite-item">
                <i class="fas fa-{{ $stageCompleted ? 'check-circle text-green-1' : 'circle text-light-1' }} text-12 mr-8"></i>
                <span class="text-13 {{ $stageCompleted ? 'text-dark-1 fw-500' : 'text-light-1' }}">{{ __('messages.complete_stage_internship') }}</span>
              </div>
            @endif
          </div>
        </div>
      </div>
    @else
      <!-- Description des Exams depuis la variation -->
      @if($product->currentVariation && $product->currentVariation->description_the_exams)
          <div class="description-card mb-30">
            <div class="d-flex items-center mb-15">
              <div class="d-flex justify-center items-center size-36 rounded-full bg-gradient-to-r from-purple-400 to-purple-500 mr-15 shadow-sm">
                <div class="icon-info text-12 text-white"></div>
            </div>
              <h5 class="text-16 fw-600 text-dark-1">{{ __('messages.exam_description') }}</h5>
          </div>
            <div class="text-14 text-dark-1 leading-relaxed pl-51">
            {!! $product->currentVariation->description_the_exams !!}
          </div>
        </div>
      @endif

        <div class="accordion -block-2 text-left js-accordion">
        @foreach($examQuizzes as $index => $exam)
          @php
            $examResult = auth()->check() ? $product->getStudentQuizResult(auth()->id(), $exam->id) : null;

            // Get exam attempt info for exams (type_id = 1)
            $examAttemptInfo = null;
            if ($examResult && $exam->type_id == 1) {
                $examAttemptInfo = $examResult->getExamAttemptInfo();
            }

            $allQuizzesCompleted = $hasPurchased && $quizQuizzes->every(function($quiz) use ($product) {
                $result = $product->getStudentQuizResult(auth()->id(), $quiz->id);
                return $result && $result->success;
            });
            // Vérifier si le stage est validé (si le cours en a un)
            $stageValidated = true; // Par défaut, pas de stage
            if ($product->is_stage) {
                $stageSubmission = \App\Models\StudentStageCourse::where('student_id', auth()->id())
                    ->where('product_id', $product->id)
                    ->where('is_valid', 1)
                    ->exists();
                $stageValidated = $stageSubmission;
            }

            $isExpired = auth()->check() && isset($enrollment) && $enrollment && $enrollment->is_expired;
            $isFinalExamLockedByInstallments = false;
            if ($hasPurchased && $isInstallmentSuspended) {
                $isFinalExamLockedByInstallments = true;
            } elseif ($hasPurchased && $hasInstallmentOrder && !($installmentContentInfo['is_all_unlocked'] ?? false)) {
                $isFinalExamLockedByInstallments = true;
            }
            $isFinalExamLockedByPurchaseSchedule = $hasPurchased && ! $installmentService->isUnlockedAfterPurchaseDays($enrollmentRecord, $exam->pivot->opens_after_purchase_days ?? null);
            $isFinalExamGatedDisplay = $isFinalExamLockedByInstallments || $isFinalExamLockedByPurchaseSchedule;
            $canTakeExam = $hasPurchased && !$isExpired && $allQuizzesCompleted && (!$examResult || ($examResult && !$examResult->isBlocked()));
            $canTakeExam = $canTakeExam && !$isFinalExamLockedByInstallments && !$isFinalExamLockedByPurchaseSchedule;
            $isCompleted = $examResult && $examResult->success;
            $displayQuestionCount = $exam->pivot->nb_question_affiche ?? $exam->questions->count();
            $pointsValue = $displayQuestionCount;
            $successRate = $exam->pivot->score_success;
          @endphp

          <div class="accordion__item exam-item">
            <div class="accordion__button py-20 px-30 bg-light-4">
                             <div class="d-flex items-center justify-between">
                 <div class="d-flex items-center">
                   <div class="accordion__icon">
                     <div class="icon" data-feather="chevron-down"></div>
                     <div class="icon" data-feather="chevron-up"></div>
                   </div>
                  <div class="exam-info">
                   <span class="text-17 fw-500 text-dark-1">{{ app()->getLocale() == 'ar' ? $exam->name_ar : $exam->name_en }}</span>
                   @if($isCompleted)
                      <div class="status-badge completed">
                        <div class="icon-check text-10"></div>
                        <span>{{ __('messages.completed') }}</span>
                      </div>
                    @elseif($examResult && $examResult->isBlocked())
                      <div class="status-badge blocked">
                        <div class="icon-lock text-10"></div>
                        <span>{{ __('messages.blocked') }}</span>
                      </div>
                    @elseif($examResult && !$examResult->success)
                      <div class="status-badge failed">
                        <div class="icon-x text-10"></div>
                        <span>{{ __('messages.failed') }}</span>
                      </div>
                    @elseif(!$allQuizzesCompleted)
                      <div class="status-badge locked">
                        <div class="icon-lock text-10"></div>
                        <span>{{ __('messages.locked') }}</span>
                      </div>
                    @elseif($isFinalExamGatedDisplay)
                      <div class="status-badge locked">
                        <div class="icon-lock text-10"></div>
                        <span>{{ __('messages.locked') }}</span>
                      </div>
                    @else
                      <div class="status-badge available">
                        <div class="icon-play text-10"></div>
                        <span>{{ __('messages.available') }}</span>
                      </div>
                   @endif
                  </div>
                </div>
                <div class="exam-meta">
                  <div class="meta-item">
                    <div class="icon-award text-12 text-light-1"></div>
                    <span>{{ $displayQuestionCount }} {{ __('messages.questions') }}</span>
                  </div>
                  <div class="meta-item">
                    <div class="icon-star text-12 text-light-1"></div>
                    <span>{{ $pointsValue }} {{ __('messages.points') }}</span>
                  </div>
                  @if(!is_null($successRate))
                    <div class="meta-item">
                      <div class="icon-target text-12 text-light-1"></div>
                      <span>{{ __('messages.success_rate') }}: {{ $successRate }}%</span>
                    </div>
                  @endif
                   @if($examAttemptInfo)
                    <div class="attempts-info">
                      <span class="text-12 text-orange-1">{{ $examAttemptInfo['attempts'] }}/{{ $examAttemptInfo['max_attempts'] }} {{ __('messages.attempts') }}</span>
                    </div>
                   @endif
                 </div>
              </div>
            </div>

            <div class="accordion__content">
              <div class="accordion__content__inner px-30 py-30">
                <div class="y-gap-20">
                  <!-- Exam Info -->
                  <div class="d-flex justify-between items-center mb-20">
                    <div class="d-flex items-center">
                      <div class="d-flex justify-center items-center size-36 rounded-full bg-gradient-to-r from-purple-400 to-purple-500 mr-12 shadow-sm">
                        <div class="icon-file-text text-12 text-white"></div>
                      </div>
                      <div>
                        <div class="text-16 fw-500 text-dark-1">{{ app()->getLocale() == 'ar' ? $exam->name_ar : $exam->name_en }}</div>
                        @if($exam->type)
                          <div class="text-14 text-light-1">{{ app()->getLocale() == 'ar' ? $exam->type->titre_ar : $exam->type->titre_en }}</div>
                        @endif
                      </div>
                    </div>

                    <div class="d-flex x-gap-20 items-center">
                      @if($hasPurchased)
                        @if($allQuizzesCompleted)
                          @if($isCompleted)
                            <div class="text-14 lh-1 text-green-1">{{ __('messages.exam_completed') }}</div>
                          @elseif($examResult && $examResult->isBlocked())
                            <div class="text-14 lh-1 text-red-1">{{ __('messages.exam_blocked') }}</div>
                          @elseif($isFinalExamGatedDisplay)
                            <div class="text-14 lh-1 text-warning">
                              <i class="fas fa-lock me-1"></i>{{ __('messages.pay_all_installments_exam', ['default' => 'Pay all installments to unlock the final exam.']) }}
                            </div>
                          @elseif($canTakeExam)
                            <button onclick="openQuizModal({{ $exam->id }}, {{ json_encode(app()->getLocale() == 'ar' ? $exam->name_ar : $exam->name_en) }}, {{ (int) $displayQuestionCount }}, {{ (int) $pointsValue }}, 'exam')"
                                     class="text-14 lh-1 text-purple-1 underline bg-none border-none cursor-pointer">
                               {{ __('messages.start_exam') }}
                             </button>
                          @endif
                        @else
                          <div class="text-14 lh-1 text-orange-1">{{ __('messages.complete_quizzes_first') }}</div>
                        @endif
                      @else
                        <div class="text-14 lh-1 text-light-1">{{ __('messages.purchase_required') }}</div>
                      @endif
                      <span class="text-14 lh-1 text-light-1">
                        {{ __('messages.points') }}: {{ $pointsValue }}
                        @if(!is_null($successRate))
                          • {{ __('messages.success_rate') }}: {{ $successRate }}%
                        @endif
                      </span>
                    </div>
                  </div>

                  @if($examResult)
                    <div class="bg-light-4 rounded-8 p-20">
                      <div class="d-flex items-center justify-between">
                        <div class="text-14 text-dark-1">
                          <strong>{{ __('messages.last_attempt') }}:</strong> {{ $examResult->created_at->format('d/m/Y H:i') }}
                        </div>
                        <div class="text-14 text-dark-1">
                          <strong>{{ __('messages.score') }}:</strong> {{ number_format($examResult->score, 1) }}%
                        </div>
                      </div>
                      @if($examAttemptInfo)
                        <div class="text-14 text-dark-1 mt-10">
                          <strong>{{ __('messages.attempts') }}:</strong> {{ $examAttemptInfo['attempts'] }}/{{ $examAttemptInfo['max_attempts'] }}
                        </div>
                      @endif
                    </div>
                  @endif
                </div>
              </div>
            </div>
          </div>
        @endforeach
        </div>
    @endif
      </div>
    </div>
  @endif

  <!-- Section Training Cases (Practical Exam) - Always show if product has practical exam -->
  @if($hasPracticalExam)
    <script>
      console.log('=== PRACTICAL EXAM DEBUG ===');
      console.log('Product has_practical_exam:', {{ $hasPracticalExam ? 'true' : 'false' }});
      console.log('Training cases count:', {{ $linkedTrainingCasesCount }});
      console.log('Has purchased:', {{ $hasPurchased ? 'true' : 'false' }});
      console.log('All exams completed:', {{ $allExamsCompleted ? 'true' : 'false' }});
      console.log('Practical exam section SHOULD BE VISIBLE');
    </script>
    @php
      // Get student's practical exam attempt status
      $practicalExamAttempt = null;
      $practicalExamStatus = null;
      $canStartPracticalExam = false;

      if($hasPurchased && auth()->check()) {
        $practicalExamAttempt = $product->practicalExamAttempts()
            ->where('user_id', auth()->id())
            ->orderBy('attempt_number', 'desc')
            ->first();

        if($practicalExamAttempt) {
          $practicalExamStatus = $practicalExamAttempt->status;
        }

        // Student can start practical exam anytime if they haven't passed
        $canStartPracticalExam = !$practicalExamAttempt || $practicalExamAttempt->status !== 'passed';
      }

      $practicalExamCompleted = $practicalExamAttempt && $practicalExamAttempt->status === 'passed';
      $trainingCasesCount = $linkedTrainingCasesCount;
    @endphp

    <div class="exam-section mb-50">
      <div class="section-header mb-30" onclick="toggleSection('practical-exam-section')">
        <div class="d-flex items-center justify-between cursor-pointer">
          <div class="d-flex items-center">
            <div class="d-flex justify-center items-center size-48 rounded-full bg-gradient-to-r from-purple-400 to-purple-500 mr-20 shadow-sm">
              <div class="icon-clipboard text-16 text-white"></div>
            </div>
            <div>
              <div class="d-flex items-center">
                <h4 class="text-20 fw-600 text-dark-1">{{ __('messages.practical_exam') }}</h4>
                @if($practicalExamCompleted)
                  <div class="section-success-badge ml-15">
                    <div class="icon-check-circle text-14" style="color: #059669;"></div>
                    <span class="text-12 fw-500" style="color: #059669;">Completed</span>
                  </div>
                @elseif(!$canAccessPracticalExam)
                  <div class="section-lock-badge ml-15">
                    <div class="icon-lock text-14 text-orange-1"></div>
                    <span class="text-12 fw-500 text-orange-1">{{ __('messages.locked') }}</span>
                  </div>
                @endif
              </div>
              <div class="section-stats">
                <span class="stat-item">{{ __('messages.hands_on_assessment') }}</span>
                @if($product->practical_exam_type)
                  <span class="stat-separator">•</span>
                  <span class="stat-item">{{ ucfirst($product->practical_exam_type) }}</span>
                @endif
              </div>
            </div>
          </div>
          <div class="section-toggle">
            <div class="toggle-arrow" id="practical-exam-arrow">
              <div class="icon-chevron-down"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="section-content" id="practical-exam-section">
        @if(!$canAccessPracticalExam)
          <div class="blocked-section-card mb-30">
            <div class="blocked-content">
              <div class="blocked-icon">
                <i class="fas fa-lock text-28 text-orange-1"></i>
              </div>
              <h5 class="text-16 fw-600 text-dark-1 mb-8">{{ __('messages.practical_exams_locked') }}</h5>
              <p class="text-14 text-light-1 mb-15">{{ __('messages.complete_exams_first') }}</p>
              <div class="prerequisites-list">
                <div class="prerequisite-item">
                  <i class="fas fa-{{ $allExamsCompleted ? 'check-circle text-green-1' : 'circle text-light-1' }} text-12 mr-8"></i>
                  <span class="text-13 {{ $allExamsCompleted ? 'text-dark-1 fw-500' : 'text-light-1' }}">{{ __('messages.complete_all_exams') }}</span>
                </div>
                @if($hasStage)
                <div class="prerequisite-item">
                  <i class="fas fa-{{ $stageCompleted ? 'check-circle text-green-1' : 'circle text-light-1' }} text-12 mr-8"></i>
                  <span class="text-13 {{ $stageCompleted ? 'text-dark-1 fw-500' : 'text-light-1' }}">{{ __('messages.submit_and_validate_stage') }}</span>
                </div>
                @endif
              </div>
            </div>
          </div>
        @else
        <div class="rounded-16 bg-white shadow-2 px-30 py-30">
          @if($hasPurchased)
            @if($practicalExamCompleted)
              <div class="success-card">
                <div class="d-flex items-center justify-between">
                  <div class="d-flex items-center">
                    <div class="icon-check-circle text-32 text-green-1 mr-15"></div>
                    <div>
                      <div class="text-17 fw-600 text-dark-1">{{ __('messages.practical_exam_passed') }}</div>
                      <div class="text-14 text-light-1">{{ __('messages.congratulations_practical_exam') }}</div>
                    </div>
                  </div>
                  <a href="{{ route('student.practical-exam.show', $product->id) }}" class="button -sm -purple-1 text-white">
                    {{ __('messages.view_details') }}
                  </a>
                </div>
              </div>
            @elseif($canStartPracticalExam)
              <div class="practical-exam-info">
                <div class="text-15 text-dark-1 mb-20">
                  {{ __('messages.practical_exam_description') }}
                </div>

                <div class="info-grid mb-30">
                  <div class="info-item">
                    <div class="icon-settings text-20 text-purple-1 mb-10"></div>
                    <div class="text-13 text-light-1">{{ __('messages.exam_type') }}</div>
                    <div class="text-15 fw-500 text-dark-1">{{ $product->practical_exam_type ? ucfirst($product->practical_exam_type) : __('messages.online_or_classroom') }}</div>
                  </div>
                  <div class="info-item">
                    <div class="icon-file-text text-20 text-purple-1 mb-10"></div>
                    <div class="text-13 text-light-1">{{ __('messages.training_cases') }}</div>
                    <div class="text-15 fw-500 text-dark-1">{{ $trainingCasesCount }}</div>
                  </div>
                  @if($practicalExamAttempt && $practicalExamAttempt->status !== 'passed')
                    <div class="info-item">
                      <div class="icon-clock text-20 text-orange-1 mb-10"></div>
                      <div class="text-13 text-light-1">{{ __('messages.current_status') }}</div>
                      <div class="text-15 fw-500 text-dark-1">
                        @if($practicalExamAttempt->status === 'pending_submission')
                          <span class="badge bg-info">{{ __('messages.pending_submission') }}</span>
                        @elseif($practicalExamAttempt->status === 'pending_review')
                          <span class="badge bg-warning">{{ __('messages.pending_review') }}</span>
                        @elseif($practicalExamAttempt->status === 'failed')
                          <span class="badge bg-danger">{{ __('messages.failed') }}</span>
                        @else
                          <span class="badge bg-secondary">{{ __('messages.in_progress') }}</span>
                        @endif
                      </div>
                    </div>
                  @endif
                </div>

                <a href="{{ route('student.practical-exam.show', $product->id) }}" class="button -md -purple-1 text-white">
                  @if($practicalExamAttempt && $practicalExamAttempt->status === 'in_progress')
                    {{ __('messages.continue_practical_exam') }}
                  @else
                    {{ __('messages.start_practical_exam') }}
                  @endif
                </a>
              </div>
            @endif
          @else
            <div class="alert-card bg-light-4">
              <div class="d-flex items-center">
                <div class="icon-lock text-20 text-light-1 mr-15"></div>
                <div class="text-15 text-dark-1">{{ __('messages.purchase_course_to_access_practical_exam') }}</div>
              </div>
            </div>
          @endif
        </div>
        @endif
      </div>
    </div>
  @else
    <script>
      console.log('=== PRACTICAL EXAM NOT SHOWING ===');
      console.log('Product has_practical_exam:', {{ $hasPracticalExam ? 'true' : 'false' }});
      console.log('Training cases count:', {{ $linkedTrainingCasesCount }});
      console.log('Condition failed: has_practical_exam={{  $hasPracticalExam ? "true" : "false" }} AND trainingCases={{ $linkedTrainingCasesCount }} > 0');
    </script>
  @endif

  <!-- Section Exam Vidéo -->
  @if($product->is_exam_video && $product->practical_exam_type !== 'classroom')
    @php
      // Vérifier si le stage est validé (si le cours en a un)
      $stageValidatedForVideo = true; // Par défaut, pas de stage requis
      if ($product->is_stage) {
          $stageSubmission = \App\Models\StudentStageCourse::where('student_id', auth()->id())
              ->where('product_id', $product->id)
              ->where('is_valid', 1)
              ->exists();
          $stageValidatedForVideo = $stageSubmission;
      }

      // Allow video exam submission without requiring quiz/exam completion
      $canUploadVideo = $hasPurchased && $stageValidatedForVideo;
    @endphp

    <div class="exam-section mb-50">
      <div class="section-header mb-30" onclick="toggleSection('video-exam-section')">
        <div class="d-flex items-center justify-between cursor-pointer">
          <div class="d-flex items-center">
            <div class="d-flex justify-center items-center size-48 rounded-full bg-gradient-to-r from-indigo-400 to-indigo-500 mr-20 shadow-sm">
              <div class="icon-video text-16 text-white"></div>
            </div>
            <div>
              <div class="d-flex items-center">
                <h4 class="text-20 fw-600 text-dark-1">{{ __('messages.video_exam') }}</h4>
                @if($videoExamCompleted)
                  <div class="section-success-badge ml-15">
                    <div class="icon-check-circle text-14" style="color: #059669;"></div>
                    <span class="text-12 fw-500" style="color: #059669;">Completed</span>
                  </div>
                @endif
              </div>
              <div class="section-stats">
                <span class="stat-item">{{ __('messages.video_submission') }}</span>
                <span class="stat-separator">•</span>
                <span class="stat-item">{{ __('messages.final_step') }}</span>
              </div>
            </div>
          </div>
          <div class="section-toggle">
            <div class="toggle-arrow" id="video-exam-arrow">
              <div class="icon-chevron-down"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="section-content" id="video-exam-section">

      <!-- Description de l'Examen Vidéo depuis la variation -->
      @if($product->currentVariation && $product->currentVariation->description_video_exam)
        <div class="description-card mb-30">
          <div class="d-flex items-center mb-15">
            <div class="d-flex justify-center items-center size-36 rounded-full bg-gradient-to-r from-indigo-400 to-indigo-500 mr-15 shadow-sm">
              <div class="icon-info text-12 text-white"></div>
            </div>
            <h5 class="text-16 fw-600 text-dark-1">{{ __('messages.video_exam_description') }}</h5>
          </div>
          <div class="text-14 text-dark-1 leading-relaxed pl-51">
            {!! $product->currentVariation->description_video_exam !!}
          </div>
        </div>
      @endif

      @if($hasPurchased)
        <!-- Vérifier si l'étudiant a déjà soumis -->
        <div id="videoSubmissionStatus" class="mb-30">
          <!-- Le contenu sera chargé via AJAX -->
        </div>

        @if(!$stageValidatedForVideo && $product->is_stage && $product->is_exam_video)
          <div class="alert alert-warning">
            <div class="d-flex items-center">
              <i class="icon-clock text-warning text-20 mr-15"></i>
              <div>
                <strong>{{ __('messages.stage_must_be_validated_first') }}</strong>
                <div class="text-14 mt-5">{{ __('messages.complete_stage_before_video_exam') }}</div>
              </div>
            </div>
          </div>
        @elseif($canUploadVideo || ($hasPurchased && $product->is_exam_video))
          <!-- Formulaire de soumission vidéo amélioré -->
          <div class="video-submission-card" id="videoSubmissionCard" style="display: block;">
            <div class="submission-header">
          <div class="d-flex items-center mb-20">
                <div class="d-flex justify-center items-center size-48 rounded-full bg-gradient-to-r from-indigo-400 to-indigo-500 mr-20 shadow-sm">
                  <div class="icon-video text-16 text-white"></div>
            </div>
                <div>
                  <h5 class="text-18 fw-600 text-dark-1">{{ __('messages.video_exam_title') }}</h5>
          </div>
              </div>
            </div>

            <form id="videoSubmissionForm" class="video-form">
              @csrf
              <input type="hidden" name="product_id" value="{{ $product->id }}">

              <!-- Lien vidéo -->
              <div class="form-group">
                <label class="form-label">
                  <div class="label-icon">
                    <div class="icon-link text-14"></div>
                  </div>
                  <span>{{ __('messages.video_link') }} *</span>
              </label>
                <input type="url"
                       name="lien"
                       id="videoLink"
                       class="form-input"
                       placeholder="{{ __('messages.enter_video_link_placeholder') }}"
                     required>
                <div class="form-help">
                  {{ __('messages.video_link_help') }}
              </div>
            </div>

            <!-- Description optionnelle -->
              <div class="form-group">
                <label class="form-label">
                  <div class="label-icon">
                    <div class="icon-edit text-14"></div>
                  </div>
                  <span>{{ __('messages.video_description') }}</span>
                  <span class="optional-badge">{{ __('messages.optional') }}</span>
              </label>
              <textarea name="video_description"
                          id="videoDescription"
                        rows="4"
                          class="form-textarea"
                          placeholder="{{ __('messages.describe_your_video_placeholder') }}"></textarea>
            </div>

            <!-- Bouton de soumission -->
              <div class="form-actions">
                <button type="submit" class="submit-btn" id="submitVideoBtn">
                  <div class="btn-icon">
                    <div class="icon-video text-16"></div>
                  </div>
                  <span>{{ __('messages.submit_video_exam') }}</span>
              </button>
          </div>
        </form>
          </div>
      @else
          <div class="locked-video-card">
            <div class="locked-content">
              <div class="locked-icon">
                <div class="icon-lock text-32 text-light-1"></div>
            </div>
              <h5 class="text-18 fw-600 text-dark-1 mb-10">{{ __('messages.complete_all_quizzes_exams') }}</h5>
              <p class="text-14 text-light-1 mb-20">{{ __('messages.then_upload_video') }}</p>
          </div>
          </div>
        @endif
      @else
        <div class="locked-video-card">
          <div class="locked-content">
            <div class="locked-icon">
              <div class="icon-lock text-32 text-light-1"></div>
            </div>
            <h5 class="text-18 fw-600 text-dark-1 mb-10">{{ __('messages.video_exam_locked') }}</h5>
            <p class="text-14 text-light-1 mb-20">{{ __('messages.purchase_course_to_access_video') }}</p>
            <a href="#" class="purchase-btn">
              <div class="btn-icon">
                <div class="icon-shopping-cart text-14"></div>
              </div>
              <span>{{ __('messages.purchase_course') }}</span>
            </a>
          </div>
        </div>
      @endif
      </div>
    </div>
  @endif

  <!-- Section Exam Pratique -->
  @if($practicalExamQuizzes->count() > 0)
    <div class="exam-section mb-50">
      <div class="section-header mb-30" onclick="toggleSection('practical-exams-section')">
        <div class="d-flex items-center justify-between cursor-pointer">
          <div class="d-flex items-center">
            <div class="d-flex justify-center items-center size-48 rounded-full bg-gradient-to-r from-blue-400 to-blue-500 mr-20 shadow-sm">
              <div class="icon-settings text-16 text-white"></div>
            </div>
            <div>
              <div class="d-flex items-center">
                <h4 class="text-20 fw-600 text-dark-1">{{ __('messages.practical_exams') }}</h4>
                @if($allPracticalExamsCompleted)
                  <div class="section-success-badge ml-15">
                    <div class="icon-check-circle text-14" style="color: #059669;"></div>
                    <span class="text-12 fw-500" style="color: #059669;">Completed</span>
                  </div>
                @endif
              </div>
              <div class="section-stats">
                <span class="stat-item">{{ $practicalExamQuizzes->count() }} {{ __('messages.practical_exams') }}</span>
                <span class="stat-separator">•</span>
                <span class="stat-item">{{ $practicalExamQuizzes->sum(function($exam) { return $exam->pivot->nb_question_affiche ?? $exam->questions->count(); }) }} {{ __('messages.questions') }}</span>
              </div>
            </div>
          </div>
          <div class="section-toggle">
            <div class="toggle-arrow" id="practical-exams-arrow">
              <div class="icon-chevron-down"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="section-content" id="practical-exams-section">

      <!-- PRACTICAL EXAM BLOCKED: Prerequisites not met -->
      @if($practicalExamBlocked)
        <div class="blocked-section-card mb-30">
          <div class="blocked-content">
            <div class="blocked-icon">
              <i class="fas fa-lock text-28 text-orange-1"></i>
            </div>
            <h5 class="text-16 fw-600 text-dark-1 mb-8">{{ __('messages.practical_exams_locked') }}</h5>
            <p class="text-14 text-light-1 mb-15">{{ $practicalExamBlockReason }}</p>
            <div class="prerequisites-list">
              <div class="prerequisite-item">
                <i class="fas fa-{{ $allQuizzesCompleted ? 'check-circle text-green-1' : 'circle text-light-1' }} text-12 mr-8"></i>
                <span class="text-13 {{ $allQuizzesCompleted ? 'text-dark-1 fw-500' : 'text-light-1' }}">{{ __('messages.complete_all_quizzes') }}</span>
              </div>
              @if($hasStage)
                <div class="prerequisite-item">
                  <i class="fas fa-{{ $stageCompleted ? 'check-circle text-green-1' : 'circle text-light-1' }} text-12 mr-8"></i>
                  <span class="text-13 {{ $stageCompleted ? 'text-dark-1 fw-500' : 'text-light-1' }}">{{ __('messages.complete_stage_internship') }}</span>
                </div>
              @endif
              <div class="prerequisite-item">
                <i class="fas fa-{{ $allExamsCompleted ? 'check-circle text-green-1' : 'circle text-light-1' }} text-12 mr-8"></i>
                <span class="text-13 {{ $allExamsCompleted ? 'text-dark-1 fw-500' : 'text-light-1' }}">{{ __('messages.complete_all_exams') }}</span>
              </div>
            </div>
          </div>
        </div>
      @else

      <!-- Description des Examens Pratiques depuis la variation -->
      @if($product->currentVariation && $product->currentVariation->description_practical_exams)
        <div class="description-card mb-30">
          <div class="d-flex items-center mb-15">
            <div class="d-flex justify-center items-center size-36 rounded-full bg-gradient-to-r from-blue-400 to-blue-500 mr-15 shadow-sm">
              <div class="icon-info text-12 text-white"></div>
            </div>
            <h5 class="text-16 fw-600 text-dark-1">{{ __('messages.practical_exam_description') }}</h5>
          </div>
          <div class="text-14 text-dark-1 leading-relaxed pl-51">
            {!! $product->currentVariation->description_practical_exams !!}
          </div>
        </div>
      @endif

      <div class="accordion -block-2 text-left js-accordion">
        @foreach($practicalExamQuizzes as $index => $practicalExam)
          @php
            $practicalExamResult = auth()->check() ? $product->getStudentQuizResult(auth()->id(), $practicalExam->id) : null;
            $allExamsCompleted = $hasPurchased && $examQuizzes->every(function($exam) use ($product) {
          $result = $product->getStudentQuizResult(auth()->id(), $exam->id);
          return $result && $result->success;
      });
            $isPracticalLockedByInstallment = false;
            if ($hasPurchased && $isInstallmentSuspended) {
                $isPracticalLockedByInstallment = true;
            } elseif ($hasPurchased) {
                $isPracticalLockedByInstallment = ! $installmentService
                    ->isQuizUnlockedForInstallmentProgress($paidInstallmentMonths, $practicalExam->pivot->installment_month ?? null);
            }
            $isPracticalLockedByPurchaseSchedule = $hasPurchased && ! $installmentService->isUnlockedAfterPurchaseDays($enrollmentRecord, $practicalExam->pivot->opens_after_purchase_days ?? null);
            $isPracticalGatedDisplay = $isPracticalLockedByInstallment || $isPracticalLockedByPurchaseSchedule;
            $canTakePracticalExam = $hasPurchased && $allExamsCompleted && (!$practicalExamResult || ($practicalExamResult && !$practicalExamResult->isBlocked()));
            $canTakePracticalExam = $canTakePracticalExam && !$isPracticalLockedByInstallment && !$isPracticalLockedByPurchaseSchedule;
            $isCompleted = $practicalExamResult && $practicalExamResult->success;
            $displayQuestionCount = $practicalExam->pivot->nb_question_affiche ?? $practicalExam->questions->count();
            $pointsValue = $displayQuestionCount;
            $successRate = $practicalExam->pivot->score_success;
    @endphp

          <div class="accordion__item practical-exam-item">
            <div class="accordion__button py-20 px-30 bg-light-4">
              <div class="d-flex items-center justify-between">
                <div class="d-flex items-center">
                  <div class="accordion__icon">
                    <div class="icon" data-feather="chevron-down"></div>
                    <div class="icon" data-feather="chevron-up"></div>
          </div>
                  <div class="practical-exam-info">
                    <span class="text-17 fw-500 text-dark-1">{{ app()->getLocale() == 'ar' ? $practicalExam->name_ar : $practicalExam->name_en }}</span>
                    @if($isCompleted)
                      <div class="status-badge completed">
                        <div class="icon-check text-10"></div>
                        <span>{{ __('messages.completed') }}</span>
        </div>
                    @elseif($practicalExamResult && $practicalExamResult->isBlocked())
                      <div class="status-badge blocked">
                        <div class="icon-lock text-10"></div>
                        <span>{{ __('messages.blocked') }}</span>
        </div>
                    @elseif($practicalExamResult && !$practicalExamResult->success)
                      <div class="status-badge failed">
                        <div class="icon-x text-10"></div>
                        <span>{{ __('messages.failed') }}</span>
                      </div>
                    @elseif(!$allExamsCompleted)
                      <div class="status-badge locked">
                        <div class="icon-lock text-10"></div>
                        <span>{{ __('messages.locked') }}</span>
                      </div>
                    @elseif($isPracticalGatedDisplay)
                      <div class="status-badge locked">
                        <div class="icon-lock text-10"></div>
                        <span>{{ __('messages.locked') }}</span>
                      </div>
                    @else
                      <div class="status-badge available">
                        <div class="icon-play text-10"></div>
                        <span>{{ __('messages.available') }}</span>
                      </div>
                    @endif
                  </div>
                </div>
                <div class="practical-exam-meta">
                  <div class="meta-item">
                    <div class="icon-settings text-12 text-light-1"></div>
                    <span>{{ $displayQuestionCount }} {{ __('messages.questions') }}</span>
                  </div>
                  <div class="meta-item">
                    <div class="icon-star text-12 text-light-1"></div>
                    <span>{{ $pointsValue }} {{ __('messages.points') }}</span>
                  </div>
                  @if(!is_null($successRate))
                    <div class="meta-item">
                      <div class="icon-target text-12 text-light-1"></div>
                      <span>{{ __('messages.success_rate') }}: {{ $successRate }}%</span>
                    </div>
                  @endif
                  @if($practicalExamResult && !$practicalExamResult->success)
                    <div class="attempts-info">
                      <span class="text-12 text-orange-1">{{ $practicalExamResult->attempts }}/1 {{ __('messages.attempts') }}</span>
                    </div>
                  @endif
                </div>
              </div>
          </div>

            <div class="accordion__content">
              <div class="accordion__content__inner px-30 py-30">
                <div class="y-gap-20">
                  <!-- Practical Exam Info -->
                  <div class="d-flex justify-between items-center mb-20">
                    <div class="d-flex items-center">
                      <div class="d-flex justify-center items-center size-36 rounded-full bg-gradient-to-r from-blue-400 to-blue-500 mr-12 shadow-sm">
                        <div class="icon-settings text-12 text-white"></div>
                      </div>
                      <div>
                        <div class="text-16 fw-500 text-dark-1">{{ app()->getLocale() == 'ar' ? $practicalExam->name_ar : $practicalExam->name_en }}</div>
                        @if($practicalExam->type)
                          <div class="text-14 text-light-1">{{ app()->getLocale() == 'ar' ? $practicalExam->type->titre_ar : $practicalExam->type->titre_en }}</div>
                        @endif
                      </div>
          </div>

                    <div class="d-flex x-gap-20 items-center">
                      @if($hasPurchased)
                        @if($isCompleted)
                          <div class="text-14 lh-1 text-green-1">{{ __('messages.practical_exam_completed') }}</div>
                        @elseif($practicalExamResult && $practicalExamResult->isBlocked())
                          <div class="text-14 lh-1 text-red-1">{{ __('messages.practical_exam_blocked') }}</div>
                        @elseif($isPracticalGatedDisplay)
                          <div class="text-14 lh-1 text-warning">
                            <i class="fas fa-lock me-1"></i>{{ __('messages.pay_to_unlock') ?? 'Pay the required installment to unlock.' }}
                          </div>
                        @elseif($canTakePracticalExam)
                          <button onclick="openQuizModal({{ $practicalExam->id }}, {{ json_encode(app()->getLocale() == 'ar' ? $practicalExam->name_ar : $practicalExam->name_en) }}, {{ (int) $displayQuestionCount }}, {{ (int) $pointsValue }}, 'practical_exam')"
                                  class="text-14 lh-1 text-blue-1 underline bg-none border-none cursor-pointer">
                            {{ __('messages.start_practical_exam') }}
                          </button>
                        @endif
                      @else
                        <div class="text-14 lh-1 text-light-1">{{ __('messages.purchase_required') }}</div>
                      @endif
                      <span class="text-14 lh-1 text-light-1">
                        {{ __('messages.points') }}: {{ $pointsValue }}
                        @if(!is_null($successRate))
                          • {{ __('messages.success_rate') }}: {{ $successRate }}%
                        @endif
                      </span>
                    </div>
                  </div>

                  @if($practicalExamResult)
                    <div class="bg-light-4 rounded-8 p-20">
                      <div class="d-flex items-center justify-between">
                        <div class="text-14 text-dark-1">
                          <strong>{{ __('messages.last_attempt') }}:</strong> {{ $practicalExamResult->created_at->format('d/m/Y H:i') }}
                        </div>
                        <div class="text-14 text-dark-1">
                          <strong>{{ __('messages.score') }}:</strong> {{ number_format($practicalExamResult->score, 1) }}%
                        </div>
                      </div>
                      @if($practicalExamResult->attempts > 0)
                        <div class="text-14 text-dark-1 mt-10">
                          <strong>{{ __('messages.attempts') }}:</strong> {{ $practicalExamResult->attempts }}/1
      </div>
    @endif
                    </div>
                  @endif
                </div>
              </div>
            </div>
          </div>
        @endforeach
        </div>
      @endif
      </div>
    </div>
  @endif

</div>


    <!-- Quiz Modal -->
  <div id="quizModal" class="modal">
    <div class="modal-content">
      <div class="modal-container">
        <!-- Header avec gradient -->
        <div class="modal-header">
          <div class="header-content">
            <div class="quiz-icon">
              <div class="icon-bg">
                <div class="icon-file-text"></div>
              </div>
            </div>
            <h3 id="quizTitle" class="quiz-title"></h3>
            <button class="close-btn" onclick="closeQuizModal()">
              <div class="close-icon"></div>
            </button>
          </div>
        </div>

        <!-- Body -->
        <div class="modal-body">
          <!-- Quiz Info Cards -->
          <div class="info-cards">
            <div class="info-card">
              <div class="card-icon questions-icon">
                <div class="icon-file-text"></div>
              </div>
              <div class="card-content">
                <div class="card-label">{{ __('messages.questions') }}</div>
                <div id="quizQuestions" class="card-value"></div>
              </div>
            </div>
          </div>

          <!-- Instructions Section -->
          <div class="instructions-section">
            <div class="instructions-header">
              <div class="instructions-icon">
                <div class="icon-info"></div>
              </div>
              <h5 id="instructionsTitle" class="instructions-title"></h5>
            </div>
            <div class="instructions-list">
              <div class="instruction-item">
                <div class="instruction-number">1</div>
                <div class="instruction-text"><span id="instruction1"></span></div>
              </div>
              <div class="instruction-item">
                <div class="instruction-number">2</div>
                <div class="instruction-text"><span id="instruction2"></span></div>
              </div>
              <div class="instruction-item">
                <div class="instruction-number">3</div>
                <div class="instruction-text"><span id="instruction3"></span></div>
              </div>
              <div class="instruction-item">
                <div class="instruction-number">4</div>
                <div class="instruction-text"><span id="instruction4"></span></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="modal-footer">
          <div class="footer-buttons">
            <button onclick="closeQuizModal()" class="btn-cancel">
              <span>{{ __('messages.cancel') }}</span>
            </button>
            <button onclick="startQuiz()" class="btn-start" id="startButton">
              <span>{{ __('messages.start_quiz') }}</span>
              <div class="btn-icon">
                <div class="icon-play"></div>
              </div>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

   <style>
    /* Modal Styles - Design Moderne */
    .modal {
      display: none;
      position: fixed;
      z-index: 9999;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, rgba(0, 113, 174, 0.95), rgba(0, 90, 138, 0.95));
      backdrop-filter: blur(10px);
      animation: modalFadeIn 0.3s ease-out;
    }

    @keyframes modalFadeIn {
      from {
        opacity: 0;
        transform: scale(0.9);
      }
      to {
        opacity: 1;
        transform: scale(1);
      }
    }

    .modal-content {
      position: relative;
      width: 100%;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .modal-container {
      background: white;
      border-radius: 24px;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
      max-width: 600px;
      width: 100%;
      overflow: hidden;
      animation: modalSlideIn 0.4s ease-out;
      position: relative;
    }

    @keyframes modalSlideIn {
      from {
        opacity: 0;
        transform: translateY(-50px) scale(0.9);
      }
      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    /* Header */
    .modal-header {
      background: linear-gradient(135deg, #0071ae 0%, #005a8a 100%);
      padding: 30px;
      position: relative;
      overflow: hidden;
    }

    .modal-header::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    }

    .header-content {
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: relative;
      z-index: 1;
    }

    .quiz-icon {
      display: flex;
      align-items: center;
    }

    .icon-bg {
      width: 60px;
      height: 60px;
      background: rgba(255, 255, 255, 0.2);
      border-radius: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .icon-bg .icon-file-text {
      width: 24px;
      height: 24px;
      background: white;
      mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z'/%3E%3Cpath d='M14 2v6h6'/%3E%3Cpath d='M16 13H8'/%3E%3Cpath d='M16 17H8'/%3E%3Cpath d='M10 9H8'/%3E%3C/svg%3E") no-repeat center;
      mask-size: contain;
    }

    .quiz-title {
      color: white;
      font-size: 24px;
      font-weight: 700;
      margin: 0;
      flex: 1;
      margin-left: 20px;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .close-btn {
      width: 40px;
      height: 40px;
      background: rgba(255, 255, 255, 0.2);
      border: none;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.3s ease;
      backdrop-filter: blur(10px);
    }

    .close-btn:hover {
      background: rgba(255, 255, 255, 0.3);
      transform: scale(1.1);
    }

    .close-icon {
      width: 16px;
      height: 16px;
      position: relative;
    }

    .close-icon::before,
    .close-icon::after {
      content: '';
      position: absolute;
      width: 100%;
      height: 2px;
      background: white;
      top: 50%;
      left: 0;
      transform: translateY(-50%);
    }

    .close-icon::before {
      transform: translateY(-50%) rotate(45deg);
    }

    .close-icon::after {
      transform: translateY(-50%) rotate(-45deg);
    }

    /* Body */
    .modal-body {
      padding: 30px;
    }

    /* Info Cards */
    .info-cards {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      margin-bottom: 30px;
    }

    .info-card {
      background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
      border-radius: 16px;
      padding: 20px;
      display: flex;
      align-items: center;
      transition: all 0.3s ease;
      border: 1px solid #e2e8f0;
    }

    .info-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .card-icon {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 16px;
    }

    .questions-icon {
      background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    }

    .card-icon .icon-file-text,
    .card-icon .icon-star {
      width: 20px;
      height: 20px;
      background: white;
      mask-size: contain;
    }

    .card-icon .icon-file-text {
      mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z'/%3E%3Cpath d='M14 2v6h6'/%3E%3Cpath d='M16 13H8'/%3E%3Cpath d='M16 17H8'/%3E%3Cpath d='M10 9H8'/%3E%3C/svg%3E") no-repeat center;
    }

    .card-icon .icon-star {
      mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z'/%3E%3C/svg%3E") no-repeat center;
    }

    .card-content {
      flex: 1;
    }

    .card-label {
      font-size: 12px;
      color: #64748b;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 4px;
    }

    .card-value {
      font-size: 18px;
      font-weight: 700;
      color: #1e293b;
    }

    /* Instructions Section */
    .instructions-section {
      background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
      border-radius: 16px;
      padding: 24px;
      border: 1px solid #e2e8f0;
    }

    .instructions-header {
      display: flex;
      align-items: center;
      margin-bottom: 20px;
    }

    .instructions-icon {
      width: 32px;
      height: 32px;
      background: linear-gradient(135deg, #0071ae 0%, #005a8a 100%);
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 12px;
    }

    .instructions-icon .icon-info {
      width: 16px;
      height: 16px;
      background: white;
      mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z'/%3E%3C/svg%3E") no-repeat center;
      mask-size: contain;
    }

    .instructions-title {
      font-size: 18px;
      font-weight: 700;
      color: #1e293b;
      margin: 0;
    }

    .instructions-list {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .instruction-item {
      display: flex;
      align-items: flex-start;
      gap: 12px;
    }

    .instruction-number {
      width: 24px;
      height: 24px;
      background: linear-gradient(135deg, #0071ae 0%, #005a8a 100%);
      color: white;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      font-weight: 700;
      flex-shrink: 0;
      margin-top: 2px;
    }

    .instruction-text {
      font-size: 14px;
      color: #475569;
      line-height: 1.5;
      flex: 1;
    }

    /* Footer */
    .modal-footer {
      padding: 24px 30px;
      background: #f8fafc;
      border-top: 1px solid #e2e8f0;
    }

    .footer-buttons {
      display: flex;
      gap: 16px;
    }

    .btn-cancel {
      flex: 1;
      padding: 14px 24px;
      background: white;
      border: 2px solid #e2e8f0;
      border-radius: 12px;
      color: #64748b;
      font-weight: 600;
      font-size: 14px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn-cancel:hover {
      border-color: #cbd5e1;
      background: #f1f5f9;
      transform: translateY(-1px);
    }

    .btn-start {
      flex: 2;
      padding: 14px 24px;
      background: linear-gradient(135deg, #0071ae 0%, #005a8a 100%);
      border: none;
      border-radius: 12px;
      color: white;
      font-weight: 700;
      font-size: 14px;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      position: relative;
      overflow: hidden;
    }

    .btn-start::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: left 0.5s;
    }

    .btn-start:hover::before {
      left: 100%;
    }

    .btn-start:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(0, 113, 174, 0.3);
    }

    .btn-icon {
      width: 16px;
      height: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .btn-icon .icon-play {
      width: 12px;
      height: 12px;
      background: white;
      mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M8 5v14l11-7z'/%3E%3C/svg%3E") no-repeat center;
      mask-size: contain;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .modal-container {
        margin: 20px;
        max-width: none;
      }

      .info-cards {
        grid-template-columns: 1fr;
      }

      .footer-buttons {
        flex-direction: column;
      }

      .quiz-title {
        font-size: 20px;
      }
    }

    /* Utility Classes */
    .bg-none {
      background: none;
    }

    .border-none {
      border: none;
    }

    .cursor-pointer {
      cursor: pointer;
    }

    .flex-1 {
      flex: 1;
    }

    .border-top-light {
      border-top: 1px solid #e5e5e5;
    }

    .video-exam-form .form-control {
      border: 1px solid #e5e5e5;
      border-radius: 8px;
      padding: 12px 16px;
      font-size: 14px;
      transition: border-color 0.3s ease;
    }

    .video-exam-form .form-control:focus {
      border-color: #6f42c1;
      box-shadow: 0 0 0 0.2rem rgba(111, 66, 193, 0.25);
    }

    .video-exam-form .form-control.is-invalid {
      border-color: #dc3545;
    }

    .video-exam-form .invalid-feedback {
      display: block;
      color: #dc3545;
      font-size: 12px;
      margin-top: 5px;
    }

    /* Styles pour les badges de succès des sections */
    .section-success-badge {
      display: flex;
      align-items: center;
      gap: 6px;
      background: linear-gradient(135deg, #10b981 0%, #059669 100%);
      color: white;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
      box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
      animation: successPulse 2s infinite;
    }

    @keyframes successPulse {
      0%, 100% {
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
      }
      50% {
        box-shadow: 0 4px 16px rgba(16, 185, 129, 0.5);
      }
    }

    /* Styles pour les sections d'examens améliorées */
    .exam-section {
      border: 1px solid #e5e7eb;
      border-radius: 16px;
      padding: 30px;
      background: #ffffff;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
    }

    .exam-section:hover {
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      transform: translateY(-2px);
    }

    .section-header {
      border-bottom: 2px solid #f3f4f6;
      padding-bottom: 20px;
      transition: all 0.3s ease;
    }

    .section-header:hover {
      background: rgba(0, 0, 0, 0.02);
      border-radius: 8px;
      padding: 15px;
      margin: -15px;
    }

    .cursor-pointer {
      cursor: pointer;
    }

    .section-toggle {
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .toggle-arrow {
      width: 32px;
      height: 32px;
      background: #f3f4f6;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.3s ease;
    }

    .section-header:hover .toggle-arrow {
      background: #e5e7eb;
      transform: scale(1.1);
    }

    .toggle-arrow .icon-chevron-down {
      width: 16px;
      height: 16px;
      background: #6b7280;
      mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z'/%3E%3C/svg%3E") no-repeat center;
      mask-size: contain;
      transition: all 0.3s ease;
    }

    .toggle-arrow.rotated .icon-chevron-down {
      transform: rotate(180deg);
    }

    .section-content {
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.4s ease-out, opacity 0.3s ease-out;
      opacity: 0;
    }

    .section-content.expanded {
      max-height: 2000px;
      opacity: 1;
      transition: max-height 0.5s ease-in, opacity 0.4s ease-in;
    }

    .section-stats {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-top: 10px;
    }

    .stat-item {
      font-size: 14px;
      color: #6b7280;
      font-weight: 500;
    }

    .stat-separator {
      color: #d1d5db;
      font-weight: bold;
    }

    .description-card {
      background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
      border: 1px solid #e2e8f0;
      border-radius: 12px;
      padding: 20px;
    }

    /* Styles pour les badges de statut */
    .status-badge {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 4px 8px;
      border-radius: 6px;
      font-size: 11px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-left: 12px;
    }

    .status-badge.completed {
      background: linear-gradient(135deg, #10b981 0%, #059669 100%);
      color: white;
    }

    .status-badge.available {
      background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
      color: white;
    }

    .status-badge.failed {
      background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
      color: white;
    }

    .status-badge.blocked {
      background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
      color: white;
    }

    .status-badge.locked {
      background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
      color: white;
    }

    /* Styles pour les métadonnées */
    .quiz-meta, .exam-meta, .practical-exam-meta {
      display: flex;
      align-items: center;
      gap: 16px;
    }

    .meta-item {
      display: flex;
      align-items: center;
      gap: 4px;
      font-size: 12px;
      color: #6b7280;
    }

    .attempts-info {
      background: #fef3c7;
      color: #92400e;
      padding: 2px 6px;
      border-radius: 4px;
      font-size: 11px;
      font-weight: 600;
    }

    /* Styles pour les éléments d'accordéon */
    .quiz-item, .exam-item, .practical-exam-item {
      border: 1px solid #e5e7eb;
      border-radius: 12px;
      margin-bottom: 12px;
      overflow: hidden;
      transition: all 0.3s ease;
    }

    .quiz-item:hover, .exam-item:hover, .practical-exam-item:hover {
      border-color: #d1d5db;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .quiz-item .accordion__button {
      background: linear-gradient(135deg, #fff7ed 0%, #fed7aa 100%);
    }

    .exam-item .accordion__button {
      background: linear-gradient(135deg, #faf5ff 0%, #e9d5ff 100%);
    }

    .practical-exam-item .accordion__button {
      background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    }

    /* Amélioration des icônes */
    .icon-check, .icon-x, .icon-lock, .icon-play {
      width: 12px;
      height: 12px;
      background: currentColor;
      mask-size: contain;
      mask-repeat: no-repeat;
      mask-position: center;
    }

    .icon-check {
      mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z'/%3E%3C/svg%3E") no-repeat center;
    }

    .icon-x {
      mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z'/%3E%3C/svg%3E") no-repeat center;
    }

    .icon-lock {
      mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z'/%3E%3C/svg%3E") no-repeat center;
    }

    .icon-play {
      mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M8 5v14l11-7z'/%3E%3C/svg%3E") no-repeat center;
    }

    .icon-award {
      mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M12 15.39l-3.76 2.27 1-4.28-3.32-2.88 4.38-.38L12 6.1l1.71 4.04 4.38.38-3.32 2.88 1 4.28L12 15.39zM22 9.24l-7.19-.62L12 2 9.19 8.62 2 9.24l5.46 4.73L5.82 21 12 17.27 18.18 21l-1.64-7.03L22 9.24z'/%3E%3C/svg%3E") no-repeat center;
    }

    .icon-settings {
      mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M19.14,12.94c0.04-0.3,0.06-0.61,0.06-0.94c0-0.32-0.02-0.64-0.07-0.94l2.03-1.58c0.18-0.14,0.23-0.41,0.12-0.61 l-1.92-3.32c-0.12-0.22-0.37-0.29-0.59-0.22l-2.39,0.96c-0.5-0.38-1.03-0.7-1.62-0.94L14.4,2.81c-0.04-0.24-0.24-0.41-0.48-0.41 h-3.84c-0.24,0-0.43,0.17-0.47,0.41L9.25,5.35C8.66,5.59,8.12,5.92,7.63,6.29L5.24,5.33c-0.22-0.08-0.47,0-0.59,0.22L2.74,8.87 C2.62,9.08,2.66,9.34,2.86,9.48l2.03,1.58C4.84,11.36,4.8,11.69,4.8,12s0.02,0.64,0.07,0.94l-2.03,1.58 c-0.18,0.14-0.23,0.41-0.12,0.61l1.92,3.32c0.12,0.22,0.37,0.29,0.59,0.22l2.39-0.96c0.5,0.38,1.03,0.7,1.62,0.94l0.36,2.54 c0.05,0.24,0.24,0.41,0.48,0.41h3.84c0.24,0,0.44-0.17,0.47-0.41l0.36-2.54c0.59-0.24,1.13-0.56,1.62-0.94l2.39,0.96 c0.22,0.08,0.47,0,0.59-0.22l1.92-3.32c0.12-0.22,0.07-0.47-0.12-0.61L19.14,12.94z M12,15.6c-1.98,0-3.6-1.62-3.6-3.6 s1.62-3.6,3.6-3.6s3.6,1.62,3.6,3.6S13.98,15.6,12,15.6z'/%3E%3C/svg%3E") no-repeat center;
    }

    .icon-briefcase {
      mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M20 6h-3V4c0-1.11-.89-2-2-2H9c-1.11 0-2 .89-2 2v2H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2v2H9V4h6zM4 8h16v11H4V8z'/%3E%3C/svg%3E") no-repeat center;
    }

    .icon-info {
      mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z'/%3E%3C/svg%3E") no-repeat center;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .exam-section {
        padding: 20px;
        margin-bottom: 30px;
      }

      .section-stats {
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
      }

      .quiz-meta, .exam-meta, .practical-exam-meta {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
      }

      .status-badge {
        margin-left: 8px;
        font-size: 10px;
      }

      .description-card {
        padding: 15px;
      }
    }

    /* Animation pour les transitions */
    .exam-section, .quiz-item, .exam-item, .practical-exam-item {
      animation: fadeInUp 0.6s ease-out;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Styles pour la section Stage améliorée */
    .stage-submission-card {
      background: #ffffff;
      border: 1px solid #e5e7eb;
      border-radius: 16px;
      padding: 30px;
      margin-top: 20px;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .submission-header {
      border-bottom: 1px solid #e5e7eb;
      padding-bottom: 20px;
      margin-bottom: 30px;
    }

    .stage-form {
      display: flex;
      flex-direction: column;
      gap: 24px;
    }

    .form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 24px;
    }

    .form-group {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .form-label {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 14px;
      font-weight: 600;
      color: #1f2937;
    }

    .label-icon {
      width: 20px;
      height: 20px;
      background: #10b981;
      border-radius: 4px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .label-icon .icon-file, .label-icon .icon-edit {
      width: 12px;
      height: 12px;
      background: white;
      mask-size: contain;
    }

    .label-icon .icon-file {
      mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z'/%3E%3Cpath d='M14 2v6h6'/%3E%3C/svg%3E") no-repeat center;
    }

    .label-icon .icon-edit {
      mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z'/%3E%3C/svg%3E") no-repeat center;
    }

    .optional-badge {
      background: #f3f4f6;
      color: #6b7280;
      padding: 2px 6px;
      border-radius: 4px;
      font-size: 10px;
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .file-upload-area {
      border: 2px dashed #d1d5db;
      border-radius: 12px;
      padding: 24px;
      text-align: center;
      cursor: pointer;
      transition: all 0.3s ease;
      background: white;
      position: relative;
    }

    .file-upload-area:hover {
      border-color: #10b981;
      background: #f0fdf4;
      transform: translateY(-2px);
    }

    .file-upload-area.dragover {
      border-color: #10b981;
      background: #f0fdf4;
      transform: scale(1.02);
    }

    .file-input {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      opacity: 0;
      cursor: pointer;
    }

    .upload-content {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 12px;
    }

    .upload-icon {
      width: 48px;
      height: 48px;
      background: #f3f4f6;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.3s ease;
    }

    .file-upload-area:hover .upload-icon {
      background: #10b981;
    }

    .file-upload-area:hover .upload-icon .icon-upload {
      background: white;
    }

    .upload-icon .icon-upload {
      width: 24px;
      height: 24px;
      background: #6b7280;
      mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z'/%3E%3Cpath d='M14 2v6h6'/%3E%3Cpath d='M16 13H8'/%3E%3Cpath d='M16 17H8'/%3E%3Cpath d='M10 9H8'/%3E%3C/svg%3E") no-repeat center;
      mask-size: contain;
    }

    .upload-text {
      display: flex;
      flex-direction: column;
      gap: 4px;
    }

    .file-preview {
      margin-top: 12px;
      padding: 12px;
      background: #f9fafb;
      border-radius: 8px;
      border: 1px solid #e5e7eb;
      display: none;
    }

    .file-preview.show {
      display: block;
    }

    .file-preview-item {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 8px;
      background: white;
      border-radius: 6px;
      border: 1px solid #e5e7eb;
    }

    .file-preview-icon {
      width: 32px;
      height: 32px;
      background: #10b981;
      border-radius: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .file-preview-icon .icon-file {
      width: 16px;
      height: 16px;
      background: white;
      mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z'/%3E%3Cpath d='M14 2v6h6'/%3E%3C/svg%3E") no-repeat center;
      mask-size: contain;
    }

    .file-preview-info {
      flex: 1;
    }

    .file-preview-name {
      font-size: 14px;
      font-weight: 500;
      color: #1f2937;
      margin-bottom: 2px;
    }

    .file-preview-size {
      font-size: 12px;
      color: #6b7280;
    }

    .file-preview-remove {
      width: 24px;
      height: 24px;
      background: #ef4444;
      border-radius: 4px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .file-preview-remove:hover {
      background: #dc2626;
      transform: scale(1.1);
    }

    .file-preview-remove .icon-x {
      width: 12px;
      height: 12px;
      background: white;
      mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z'/%3E%3C/svg%3E") no-repeat center;
      mask-size: contain;
    }

    .form-textarea {
      width: 100%;
      padding: 12px 16px;
      border: 2px solid #e5e7eb;
      border-radius: 8px;
      font-size: 14px;
      line-height: 1.5;
      resize: vertical;
      transition: all 0.3s ease;
      background: white;
    }

    .form-textarea:focus {
      outline: none;
      border-color: #10b981;
      box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }

    .form-textarea::placeholder {
      color: #9ca3af;
    }

    .form-actions {
      display: flex;
      justify-content: center;
      margin-top: 20px;
    }

    .submit-btn {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 16px 32px;
      background: linear-gradient(135deg, #0071ae 0%, #005a8d 100%);
      color: white;
      border: none;
      border-radius: 12px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 12px rgba(0, 113, 174, 0.3);
    }

    .submit-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(0, 113, 174, 0.4);
    }

    .submit-btn:active {
      transform: translateY(0);
    }

    .submit-btn:disabled {
      opacity: 0.6;
      cursor: not-allowed;
      transform: none;
    }

    .btn-icon {
      width: 20px;
      height: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .btn-icon .icon-upload {
      width: 16px;
      height: 16px;
      background: white;
      mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z'/%3E%3Cpath d='M14 2v6h6'/%3E%3C/svg%3E") no-repeat center;
      mask-size: contain;
    }

    .locked-stage-card {
      background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
      border: 2px solid #e5e7eb;
      border-radius: 16px;
      padding: 40px;
      text-align: center;
    }

    .locked-content {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 16px;
    }

    .locked-icon {
      width: 80px;
      height: 80px;
      background: #f3f4f6;
      border-radius: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 10px;
    }

    .purchase-btn {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 12px 24px;
      background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
      color: white;
      text-decoration: none;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 600;
      transition: all 0.3s ease;
      box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .purchase-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
      color: white;
      text-decoration: none;
    }

    .purchase-btn .btn-icon .icon-shopping-cart {
      width: 14px;
      height: 14px;
      background: white;
      mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M7 18c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12L8.1 13h7.45c.75 0 1.41-.41 1.75-1.03L21.7 4H5.21l-.94-2H1zm16 16c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z'/%3E%3C/svg%3E") no-repeat center;
      mask-size: contain;
    }

    /* Styles pour la section Video Exam améliorée */
    .video-submission-card {
      background: linear-gradient(135deg, #f0f4ff 0%, #e0e7ff 100%);
      border: 2px solid #c7d2fe;
      border-radius: 16px;
      padding: 30px;
      margin-top: 20px;
    }

    .video-form {
      display: flex;
      flex-direction: column;
      gap: 24px;
    }

    .form-input {
      width: 100%;
      padding: 12px 16px;
      border: 2px solid #e5e7eb;
      border-radius: 8px;
      font-size: 14px;
      transition: all 0.3s ease;
      background: white;
    }

    .form-input:focus {
      outline: none;
      border-color: #6366f1;
      box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    .form-input::placeholder {
      color: #9ca3af;
    }

    .form-help {
      font-size: 12px;
      color: #6b7280;
      margin-top: 6px;
      line-height: 1.4;
    }

    .locked-video-card {
      background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
      border: 2px solid #e5e7eb;
      border-radius: 16px;
      padding: 40px;
      text-align: center;
    }

    .icon-link {
      mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71'/%3E%3Cpath d='M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71'/%3E%3C/svg%3E") no-repeat center;
      mask-size: contain;
    }

    .icon-video {
      mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M23 7l-7 5 7 5V7z'/%3E%3Cpath d='M1 5v14h14V5H1zm12 10H3V7h10v8z'/%3E%3C/svg%3E") no-repeat center;
      mask-size: contain;
    }

    /* Responsive pour la section Stage et Video */
    @media (max-width: 768px) {
      .form-grid {
        grid-template-columns: 1fr;
        gap: 20px;
      }

      .stage-submission-card, .video-submission-card {
        padding: 20px;
      }

      .file-upload-area {
        padding: 20px;
      }

      .submit-btn {
        width: 100%;
        justify-content: center;
      }

      .locked-stage-card, .locked-video-card {
        padding: 30px 20px;
      }
    }
  </style>

  <!-- SweetAlert2 CDN -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
   let currentQuizId = null;
   let currentQuizType = null;

   // Quiz Modal Functions
   function openQuizModal(quizId, quizName, questionsCount, points, type) {
     currentQuizId = quizId;
     currentQuizType = type;
     document.getElementById('quizTitle').textContent = quizName;
     document.getElementById('quizQuestions').textContent = questionsCount + ' {{ __("messages.questions") }}';

     // Adapter le contenu selon le type (quiz, examen ou examen pratique)
     if (type === 'exam') {
       document.getElementById('instructionsTitle').textContent = '{{ __("messages.exam_instructions") }}';
       document.getElementById('instruction1').textContent = '{{ __("messages.exam_instruction_1") }}';
       document.getElementById('instruction2').textContent = '{{ __("messages.exam_instruction_2") }}';
       document.getElementById('instruction3').textContent = '{{ __("messages.exam_instruction_3") }}';
       document.getElementById('instruction4').textContent = '{{ __("messages.exam_instruction_4") }}';
       document.getElementById('startButton').querySelector('span').textContent = '{{ __("messages.start_exam") }}';
     } else if (type === 'practical_exam') {
       document.getElementById('instructionsTitle').textContent = '{{ __("messages.practical_exam_instructions") }}';
       document.getElementById('instruction1').textContent = '{{ __("messages.practical_exam_instruction_1") }}';
       document.getElementById('instruction2').textContent = '{{ __("messages.practical_exam_instruction_2") }}';
       document.getElementById('instruction3').textContent = '{{ __("messages.practical_exam_instruction_3") }}';
       document.getElementById('instruction4').textContent = '{{ __("messages.practical_exam_instruction_4") }}';
       document.getElementById('startButton').querySelector('span').textContent = '{{ __("messages.start_practical_exam") }}';
     } else {
       document.getElementById('instructionsTitle').textContent = '{{ __("messages.quiz_instructions") }}';
       document.getElementById('instruction1').textContent = '{{ __("messages.quiz_instruction_1") }}';
       document.getElementById('instruction2').textContent = '{{ __("messages.quiz_instruction_2") }}';
       document.getElementById('instruction3').textContent = '{{ __("messages.quiz_instruction_3") }}';
       document.getElementById('instruction4').textContent = '{{ __("messages.quiz_instruction_4") }}';
       document.getElementById('startButton').querySelector('span').textContent = '{{ __("messages.start_quiz") }}';
     }

     document.getElementById('quizModal').style.display = 'block';
     document.body.style.overflow = 'hidden';
   }

   function closeQuizModal() {
     document.getElementById('quizModal').style.display = 'none';
     document.body.style.overflow = 'auto';
     currentQuizId = null;
     currentQuizType = null;
   }

   function startQuiz() {
     if (currentQuizId) {
       // Rediriger vers la page de démarrage du quiz avec le produit
       const productId = {{ $product->id }};
       const locale = '{{ app()->getLocale() }}';
       window.location.href = `/${locale}/courses/${productId}/quiz/${currentQuizId}/start`;
       closeQuizModal();
     }
   }

   // Close modals when clicking outside
   window.onclick = function(event) {
     const quizModal = document.getElementById('quizModal');

     if (event.target == quizModal) {
       closeQuizModal();
     }
   }

   // Close modals with Escape key
   document.addEventListener('keydown', function(event) {
     if (event.key === 'Escape') {
       closeQuizModal();
     }
   });

   // Gestion du formulaire d'examen vidéo
   document.addEventListener('DOMContentLoaded', function() {
     const videoSubmissionForm = document.getElementById('videoSubmissionForm');
     if (videoSubmissionForm) {
       videoSubmissionForm.addEventListener('submit', function(e) {
           e.preventDefault();

         const videoLink = document.getElementById('videoLink').value.trim();
         if (!videoLink) {
           Swal.fire({
             icon: 'warning',
             title: '{{ __("messages.validation_error") }}',
             text: '{{ __("messages.please_enter_video_link") }}',
             confirmButtonText: '{{ __("messages.ok") }}',
             confirmButtonColor: '#7c3aed'
           });
           return;
         }

         // Validation basique de l'URL
         try {
           new URL(videoLink);
         } catch {
           Swal.fire({
             icon: 'warning',
             title: '{{ __("messages.validation_error") }}',
             text: '{{ __("messages.please_enter_valid_url") }}',
             confirmButtonText: '{{ __("messages.ok") }}',
             confirmButtonColor: '#7c3aed'
           });
           return;
         }

         // Confirmation avant soumission
         Swal.fire({
           title: '{{ __("messages.confirm_video_submission") }}',
           text: '{{ __("messages.are_you_sure_submit_video") }}',
           icon: 'question',
           showCancelButton: true,
           confirmButtonColor: '#6366f1',
           cancelButtonColor: '#6b7280',
           confirmButtonText: '{{ __("messages.yes_submit") }}',
           cancelButtonText: '{{ __("messages.cancel") }}'
         }).then((result) => {
           if (result.isConfirmed) {
             submitVideoExam();
           }
         });
       });
     }
   });

   function submitVideoExam() {
     const formData = new FormData(document.getElementById('videoSubmissionForm'));
     const submitBtn = document.getElementById('submitVideoBtn');

     // Désactiver le bouton
     submitBtn.disabled = true;
     submitBtn.innerHTML = '<i class="icon-spinner mr-10"></i>{{ __("messages.submitting") }}...';

     // Afficher le loading
     Swal.fire({
       title: '{{ __("messages.submitting_video_exam") }}',
       text: '{{ __("messages.please_wait") }}',
       allowOutsideClick: false,
       didOpen: () => {
         Swal.showLoading();
       }
     });

     fetch('/{{ app()->getLocale() }}/student-video-exams', {
       method: 'POST',
       body: formData,
       headers: {
         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
       }
     })
     .then(response => response.json())
     .then(data => {
       if (data.success) {
         Swal.fire({
           icon: 'success',
           title: '{{ __("messages.success") }}',
           text: data.message,
           confirmButtonText: '{{ __("messages.ok") }}',
           confirmButtonColor: '#6366f1'
         }).then(() => {
           // Recharger le statut
           loadVideoSubmissionStatus();

           // Réinitialiser le formulaire
           document.getElementById('videoSubmissionForm').reset();
         });
       } else {
         Swal.fire({
           icon: 'error',
           title: '{{ __("messages.error") }}',
           text: data.message,
           confirmButtonText: '{{ __("messages.ok") }}',
           confirmButtonColor: '#ef4444'
         });
       }
     })
     .catch(error => {
       console.error('Error:', error);
       Swal.fire({
         icon: 'error',
         title: '{{ __("messages.error") }}',
         text: '{{ __("messages.unexpected_error_occurred") }}',
         confirmButtonText: '{{ __("messages.ok") }}',
         confirmButtonColor: '#ef4444'
       });
     })
     .finally(() => {
       // Réactiver le bouton
       submitBtn.disabled = false;
       submitBtn.innerHTML = '<div class="btn-icon"><div class="icon-video text-16"></div></div><span>{{ __("messages.submit_video_exam") }}</span>';
     });
   }

   // Gestion de la soumission de stage
   @if($product->is_stage && $hasPurchased)
   // Charger le statut de soumission au chargement de la page
   loadStageSubmissionStatus();
   @endif

   // Gestion de la soumission d'examen vidéo
   @if($product->is_exam_video && $hasPurchased)
   // Charger le statut de soumission au chargement de la page
   loadVideoSubmissionStatus();
   @endif

   function loadVideoSubmissionStatus() {
     fetch(`/{{ app()->getLocale() }}/student-video-exams/student-submission/{{ $product->id }}`)
       .then(response => response.json())
       .then(data => {
         const statusDiv = document.getElementById('videoSubmissionStatus');
         const formDiv = document.getElementById('videoSubmissionCard');

         if (data.submission) {
           const submission = data.submission;
           let statusHtml = '';

           // Déterminer le statut et les classes CSS
           let statusClass, statusIcon, statusText;
           switch(submission.is_valid) {
             case 1: // Validé
               statusClass = 'alert-success';
               statusIcon = 'icon-check-circle text-success';
               statusText = '{{ __("messages.video_exam_validated") }}';
               // Masquer le formulaire si validé
               if (formDiv) {
                 formDiv.style.display = 'none';
               }
               break;
             case -1: // Rejeté
               statusClass = 'alert-danger';
               statusIcon = 'icon-x-circle text-danger';
               statusText = '{{ __("messages.video_exam_rejected") }}';
               // Afficher le formulaire si rejeté (pour permettre une nouvelle soumission)
               if (formDiv) {
                 formDiv.style.display = 'block';
               }
               break;
             default: // En attente (0)
               statusClass = 'alert-warning';
               statusIcon = 'icon-clock text-warning';
               statusText = '{{ __("messages.video_exam_pending") }}';
               // Masquer le formulaire si en attente (pas de nouvelle soumission possible)
               if (formDiv) {
                 formDiv.style.display = 'none';
               }
               break;
           }

           statusHtml = `
             <div class="alert ${statusClass}">
               <div class="d-flex items-center">
                 <i class="${statusIcon} text-20 mr-15"></i>
                 <div>
                   <strong>${statusText}</strong>
                   <p class="mb-0">{{ __('messages.submitted_at') }}: ${new Date(submission.submitted_at).toLocaleDateString()}</p>
                 </div>
               </div>
             </div>
           `;

           statusDiv.innerHTML = statusHtml;
         } else {
           // Pas de soumission - afficher le formulaire
           statusDiv.innerHTML = '';
           if (formDiv) {
             formDiv.style.display = 'block';
           }
         }
       })
       .catch(error => {
         console.error('Error loading video submission status:', error);
       });
   }

   // Gestion des fichiers uploadés avec prévisualisation
   function setupFileUpload() {
     const fileInputs = ['stageFile1', 'stageFile2'];

     fileInputs.forEach(inputId => {
       const input = document.getElementById(inputId);

       // Skip if element doesn't exist (no stage in this course)
       if (!input) return;

       const previewId = inputId.replace('stageFile', 'file') + 'Preview';
       const preview = document.getElementById(previewId);
       const uploadArea = input.closest('.file-upload-area');

       // Skip if required elements don't exist
       if (!uploadArea) return;

       // Gestion du changement de fichier
       input.addEventListener('change', function(e) {
         handleFileSelect(e.target, preview, uploadArea);
       });

       // Gestion du drag & drop
       uploadArea.addEventListener('dragover', function(e) {
         e.preventDefault();
         uploadArea.classList.add('dragover');
       });

       uploadArea.addEventListener('dragleave', function(e) {
         e.preventDefault();
         uploadArea.classList.remove('dragover');
       });

       uploadArea.addEventListener('drop', function(e) {
         e.preventDefault();
         uploadArea.classList.remove('dragover');
         const files = e.dataTransfer.files;
         if (files.length > 0) {
           input.files = files;
           handleFileSelect(input, preview, uploadArea);
         }
       });
     });
   }

   function handleFileSelect(input, preview, uploadArea) {
     const file = input.files[0];
     if (file) {
       // Vérifier la taille du fichier (10MB max)
       const maxSize = 10 * 1024 * 1024;
       if (file.size > maxSize) {
         Swal.fire({
           icon: 'warning',
           title: '{{ __("messages.file_too_large") }}',
           text: '{{ __("messages.file_size_limit_10mb") }}',
           confirmButtonText: '{{ __("messages.ok") }}',
           confirmButtonColor: '#7c3aed'
         });
         input.value = '';
         return;
       }

       // Afficher la prévisualisation
       showFilePreview(file, preview, input);
     }
   }

   function showFilePreview(file, preview, input) {
     const fileSize = (file.size / 1024 / 1024).toFixed(2);
     const fileName = file.name.length > 30 ? file.name.substring(0, 30) + '...' : file.name;

     preview.innerHTML = `
       <div class="file-preview-item">
         <div class="file-preview-icon">
           <div class="icon-file"></div>
         </div>
         <div class="file-preview-info">
           <div class="file-preview-name">${fileName}</div>
           <div class="file-preview-size">${fileSize} MB</div>
         </div>
         <div class="file-preview-remove" onclick="removeFile('${input.id}', '${preview.id}')">
           <div class="icon-x"></div>
         </div>
       </div>
     `;
     preview.classList.add('show');
   }

   function removeFile(inputId, previewId) {
     const input = document.getElementById(inputId);
     const preview = document.getElementById(previewId);

     input.value = '';
     preview.classList.remove('show');
     preview.innerHTML = '';
   }

   // Initialiser la gestion des fichiers
   document.addEventListener('DOMContentLoaded', function() {
     setupFileUpload();
     initializeSectionToggles();
   });

   // Gestion des toggles de sections
   function initializeSectionToggles() {
     // Par défaut, toutes les sections sont fermées
     const sections = ['quizzes-section', 'stage-section', 'exams-section', 'practical-exams-section', 'video-exam-section'];

     sections.forEach(sectionId => {
       const section = document.getElementById(sectionId);
       if (section) {
         section.classList.remove('expanded');
         const arrowId = sectionId.replace('-section', '-arrow');
         const arrow = document.getElementById(arrowId);
         if (arrow) {
           arrow.classList.remove('rotated');
         }
       }
     });
   }

   // toggleSection function is now defined globally in the main course page

   function loadStageSubmissionStatus() {
     fetch(`/{{ app()->getLocale() }}/student-stage-courses/student-submission/{{ $product->id }}`)
       .then(response => response.json())
       .then(data => {
         const statusDiv = document.getElementById('stageSubmissionStatus');
         const formDiv = document.getElementById('stageSubmissionCard');

         if (data.submission) {
           const submission = data.submission;
           let statusHtml = '';

           // Déterminer le statut et les classes CSS
           let statusClass, statusIcon, statusText;
           switch(submission.is_valid) {
             case 1: // Validé
               statusClass = 'alert-success';
               statusIcon = 'icon-check-circle text-success';
               statusText = '{{ __("messages.submission_validated") }}';
               // Masquer le formulaire si validé
               if (formDiv) {
                 formDiv.style.display = 'none';
               }
               break;
             case -1: // Rejeté
               statusClass = 'alert-danger';
               statusIcon = 'icon-x-circle text-danger';
               statusText = '{{ __("messages.submission_rejected") }}';
               // Afficher le formulaire si rejeté (pour permettre une nouvelle soumission)
               if (formDiv) {
                 formDiv.style.display = 'block';
               }
               break;
             default: // En attente (0)
               statusClass = 'alert-warning';
               statusIcon = 'icon-clock text-warning';
               statusText = '{{ __("messages.submission_pending") }}';
               // Masquer le formulaire si en attente
               if (formDiv) {
                 formDiv.style.display = 'none';
               }
               break;
           }

           statusHtml = `
             <div class="alert ${statusClass}">
               <div class="d-flex items-center">
                 <i class="${statusIcon} text-20 mr-15"></i>
                 <div>
                   <strong>${statusText}</strong>
                   <div class="text-14 mt-5">{{ __('messages.submitted_on') }}: ${new Date(submission.submitted_at).toLocaleDateString()}</div>
                   ${submission.admin_notes ? `<div class="text-14 text-dark-1 mt-5"><strong>{{ __('messages.admin_notes') }}:</strong> ${submission.admin_notes}</div>` : ''}
                 </div>
               </div>
             </div>
           `;

           statusDiv.innerHTML = statusHtml;
         } else {
           // Pas de soumission - afficher le formulaire
           statusDiv.innerHTML = '';
           if (formDiv) {
             formDiv.style.display = 'block';
           }
         }
       })
       .catch(error => {
         console.error('Error loading stage submission status:', error);
       });
   }

   // Gestion du formulaire de soumission de stage
   const stageSubmissionForm = document.getElementById('stageSubmissionForm');
   if (stageSubmissionForm) {
     stageSubmissionForm.addEventListener('submit', function(e) {
       e.preventDefault();

       // Validation des fichiers
       const file1 = document.getElementById('stageFile1').files[0];
       const file2 = document.getElementById('stageFile2').files[0];

       if (!file1 || !file2) {
         Swal.fire({
           icon: 'warning',
         title: '{{ __("messages.validation_error") }}',
         text: '{{ __("messages.please_select_both_files") }}',
         confirmButtonText: '{{ __("messages.ok") }}',
         confirmButtonColor: '#7c3aed'
       });
       return;
     }

     // Vérifier la taille des fichiers (10MB max)
     const maxSize = 10 * 1024 * 1024; // 10MB
     if (file1.size > maxSize || file2.size > maxSize) {
       Swal.fire({
         icon: 'warning',
         title: '{{ __("messages.file_too_large") }}',
         text: '{{ __("messages.file_size_limit_10mb") }}',
         confirmButtonText: '{{ __("messages.ok") }}',
         confirmButtonColor: '#7c3aed'
       });
       return;
     }

     // Confirmation avant soumission
     Swal.fire({
       title: '{{ __("messages.confirm_submission") }}',
       text: '{{ __("messages.are_you_sure_submit_stage_work") }}',
       icon: 'question',
       showCancelButton: true,
       confirmButtonColor: '#10b981',
       cancelButtonColor: '#6b7280',
       confirmButtonText: '{{ __("messages.yes_submit") }}',
       cancelButtonText: '{{ __("messages.cancel") }}'
     }).then((result) => {
       if (result.isConfirmed) {
         submitStageWork();
       }
     });
   });

   function submitStageWork() {
     const formData = new FormData(document.getElementById('stageSubmissionForm'));
     const submitBtn = document.getElementById('submitStageBtn');

     // Désactiver le bouton
     submitBtn.disabled = true;
     submitBtn.innerHTML = '<i class="icon-spinner mr-10"></i>{{ __("messages.submitting") }}...';

     // Afficher le loading
     Swal.fire({
       title: '{{ __("messages.submitting_stage_work") }}',
       text: '{{ __("messages.please_wait") }}',
       allowOutsideClick: false,
       didOpen: () => {
         Swal.showLoading();
       }
     });

     fetch('/{{ app()->getLocale() }}/student-stage-courses', {
       method: 'POST',
       body: formData,
       headers: {
         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
       }
     })
     .then(response => response.json())
     .then(data => {
       if (data.success) {
         Swal.fire({
           icon: 'success',
           title: '{{ __("messages.success") }}',
           text: data.message,
           confirmButtonText: '{{ __("messages.ok") }}',
           confirmButtonColor: '#10b981'
         }).then(() => {
           // Reload the page to show updated status
           window.location.reload();
         });
       } else {
         Swal.fire({
           icon: 'error',
           title: '{{ __("messages.error") }}',
           text: data.message,
           confirmButtonText: '{{ __("messages.ok") }}',
           confirmButtonColor: '#ef4444'
         });
       }
     })
     .catch(error => {
       console.error('Error:', error);
       Swal.fire({
         icon: 'error',
         title: '{{ __("messages.error") }}',
         text: '{{ __("messages.unexpected_error_occurred") }}',
         confirmButtonText: '{{ __("messages.ok") }}',
         confirmButtonColor: '#ef4444'
       });
     })
     .finally(() => {
       // Réactiver le bouton
       submitBtn.disabled = false;
       submitBtn.innerHTML = '<i class="icon-upload mr-10"></i>{{ __("messages.submit") }}';
     });
   }
 }
 </script>

<style>
  /* Course Progression Blocking Styles */
  .blocked-section-card {
    background: linear-gradient(135deg, #fff5f5 0%, #fff9e6 100%);
    border: 1px solid #ffc107;
    border-radius: 12px;
    padding: 24px;
    text-align: center;
  }

  .blocked-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
  }

  .blocked-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 56px;
    height: 56px;
    background: rgba(255, 193, 7, 0.1);
    border-radius: 50%;
    margin-bottom: 8px;
  }

  .blocked-section-card h5 {
    color: #2c3e50;
    font-weight: 600;
    margin: 0;
  }

  .blocked-section-card p {
    color: #7f8c8d;
    margin: 0;
  }

  .prerequisites-list {
    width: 100%;
    max-width: 300px;
    text-align: left;
    margin-top: 12px;
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  .prerequisite-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background: rgba(255, 255, 255, 0.6);
    border-radius: 6px;
    font-size: 13px;
  }

  .prerequisite-item i {
    min-width: 16px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  /* Success states styles */
  .success-card {
    background: linear-gradient(135deg, #f0fdf4 0%, #f0f9ff 100%);
    border: 1px solid #10b981;
    border-radius: 12px;
    padding: 24px;
  }

  .section-header {
    transition: all 0.3s ease;
  }

  .section-header:hover {
    background-color: rgba(0, 0, 0, 0.02);
  }

  .section-success-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: rgba(16, 185, 129, 0.1);
    padding: 4px 8px;
    border-radius: 4px;
  }

  .section-stats {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-top: 4px;
    font-size: 13px;
    color: #6b7280;
  }

  .stat-separator {
    opacity: 0.4;
  }

  @media (max-width: 768px) {
    .blocked-section-card {
      padding: 16px;
    }

    .blocked-icon {
      width: 48px;
      height: 48px;
    }

    .prerequisites-list {
      max-width: 100%;
    }
  }
</style>
