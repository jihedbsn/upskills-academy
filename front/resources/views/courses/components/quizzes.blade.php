<div class="pt-60 lg:pt-40">
  <h4 class="text-18 fw-500 mb-30">{{ __('messages.quizzes') }}</h4>

  @php
    $hasPurchased = auth()->check() && $product->hasStudentAccess(auth()->id());
    $quizzes = $product->quizzes()->where('type_id', 2)->with('questions')->get(); // Type 2 = Quiz (Type 1 = Exam)
    $exams = $product->quizzes()->where('type_id', 1)->with('questions')->get(); // Type 1 = Exam
  @endphp

  @if($quizzes->count() > 0 || $exams->count() > 0)
    <!-- Quizzes Section -->
    @if($quizzes->count() > 0)
      <div class="mb-40">
        <h5 class="text-16 fw-500 mb-20">{{ __('messages.quizzes') }}</h5>
        <div class="y-gap-20">
          @foreach($quizzes as $quiz)
            @php
              $quizResult = null;
              if (auth()->check()) {
                $quizResult = \App\Models\ResultatQuiz::where('student_id', auth()->id())
                  ->where('quiz_id', $quiz->id)
                  ->latest()
                  ->first();
              }
              $displayQuestionCount = $quiz->pivot->nb_question_affiche ?? $quiz->questions->count();
              $successRate = $quiz->pivot->score_success;
            @endphp

            <div class="bg-light-4 rounded-12 p-20">
              <div class="d-flex justify-between items-center">
                <div class="d-flex items-center">
                  <div class="d-flex justify-center items-center size-40 rounded-full bg-purple-3 mr-15">
                    <div class="icon-quiz text-16 text-purple-1"></div>
                  </div>
                  <div>
                    <h6 class="text-15 fw-500 text-dark-1">{{ app()->getLocale() == 'ar' ? $quiz->name_ar : $quiz->name_en }}</h6>
                    <div class="d-flex x-gap-20 items-center mt-5">
                      <span class="text-12 text-light-1">{{ $displayQuestionCount }} {{ __('messages.questions') }}</span>
                      <span class="text-12 text-light-1">{{ __('messages.points') }}: {{ $displayQuestionCount }}</span>
                      <span class="text-12 text-light-1">{{ $quiz->duration ?? 30 }} {{ __('messages.minutes') }}</span>
                      @if(!is_null($successRate))
                        <span class="text-12 text-light-1">{{ __('messages.success_rate') }}: {{ $successRate }}%</span>
                      @endif
                    </div>
                  </div>
                </div>

                <div class="d-flex items-center">
                  @if(!$hasPurchased)
                    <div class="text-12 text-light-1">{{ __('messages.purchase_required') }}</div>
                  @elseif($quizResult && $quizResult->success)
                    <div class="d-flex items-center">
                      <div class="icon-check text-16 text-green-1 mr-5"></div>
                      <span class="text-12 text-green-1">{{ __('messages.completed') }}</span>
                    </div>
                  @elseif($quizResult && $quizResult->isBlocked())
                    <div class="d-flex items-center">
                      <div class="icon-lock text-16 text-red-1 mr-5"></div>
                      <span class="text-12 text-red-1">{{ __('messages.quiz_blocked') }}</span>
                    </div>
                  @else
                    <a href="{{ route('quiz.start', ['product' => $product->id, 'quiz' => $quiz->id]) }}" class="button -sm -purple-1 text-white">
                      {{ __('messages.start_quiz') }}
                    </a>
                  @endif
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @endif

    <!-- Exams Section -->
    @if($exams->count() > 0)
      <div class="mb-40">
        <h5 class="text-16 fw-500 mb-20">{{ __('messages.exams') }}</h5>
        <div class="y-gap-20">
          @foreach($exams as $exam)
            @php
              $examResult = null;
              $canTakeExam = true;

              if (auth()->check()) {
                $examResult = \App\Models\ResultatQuiz::where('student_id', auth()->id())
                  ->where('quiz_id', $exam->id)
                  ->latest()
                  ->first();

                // Check if all quizzes are completed
                $incompleteQuizzes = $quizzes->filter(function($quiz) {
                  $result = \App\Models\ResultatQuiz::where('student_id', auth()->id())
                    ->where('quiz_id', $quiz->id)
                    ->latest()
                    ->first();
                  return !$result || !$result->success;
                });

                $canTakeExam = $incompleteQuizzes->count() == 0;
              }

              $displayQuestionCount = $exam->pivot->nb_question_affiche ?? $exam->questions->count();
              $successRate = $exam->pivot->score_success;
            @endphp

            <div class="bg-light-4 rounded-12 p-20">
              <div class="d-flex justify-between items-center">
                <div class="d-flex items-center">
                  <div class="d-flex justify-center items-center size-40 rounded-full bg-orange-3 mr-15">
                    <div class="icon-exam text-16 text-orange-1"></div>
                  </div>
                  <div>
                    <h6 class="text-15 fw-500 text-dark-1">{{ app()->getLocale() == 'ar' ? $exam->name_ar : $exam->name_en }}</h6>
                    <div class="d-flex x-gap-20 items-center mt-5">
                      <span class="text-12 text-light-1">{{ $displayQuestionCount }} {{ __('messages.questions') }}</span>
                      <span class="text-12 text-light-1">{{ __('messages.points') }}: {{ $displayQuestionCount }}</span>
                      <span class="text-12 text-light-1">{{ $exam->duration ?? 60 }} {{ __('messages.minutes') }}</span>
                      @if(!is_null($successRate))
                        <span class="text-12 text-light-1">{{ __('messages.success_rate') }}: {{ $successRate }}%</span>
                      @endif
                    </div>
                  </div>
                </div>

                <div class="d-flex items-center">
                  @if(!$hasPurchased)
                    <div class="text-12 text-light-1">{{ __('messages.purchase_required') }}</div>
                  @elseif(!$canTakeExam)
                    <div class="d-flex items-center">
                      <div class="icon-lock text-16 text-orange-1 mr-5"></div>
                      <span class="text-12 text-orange-1">{{ __('messages.complete_quizzes_first') }}</span>
                    </div>
                  @elseif($examResult && $examResult->success)
                    <div class="d-flex items-center">
                      <div class="icon-check text-16 text-green-1 mr-5"></div>
                      <span class="text-12 text-green-1">{{ __('messages.completed') }}</span>
                    </div>
                  @elseif($examResult && $examResult->isBlocked())
                    <div class="d-flex items-center">
                      <div class="icon-lock text-16 text-red-1 mr-5"></div>
                      <span class="text-12 text-red-1">{{ __('messages.exam_blocked') }}</span>
                    </div>
                  @else
                    <a href="{{ route('quiz.start', ['product' => $product->id, 'quiz' => $exam->id]) }}" class="button -sm -orange-1 text-white">
                      {{ __('messages.start_exam') }}
                    </a>
                  @endif
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @endif

    <!-- Video Exam Section -->
    @if($product->is_exam_video)
      @php
        $canUploadVideo = auth()->check() && $hasPurchased;
        if ($canUploadVideo) {
          // Check if all quizzes and exams are completed
          $allQuizzes = $product->quizzes;
          $incompleteQuizzes = $allQuizzes->filter(function($quiz) {
            $result = \App\Models\ResultatQuiz::where('student_id', auth()->id())
              ->where('quiz_id', $quiz->id)
              ->latest()
              ->first();
            return !$result || !$result->success;
          });
          $canUploadVideo = $incompleteQuizzes->count() == 0;
        }
      @endphp

      <div class="mb-40">
        <h5 class="text-16 fw-500 mb-20">{{ __('messages.video_exam') }}</h5>
        <div class="bg-light-4 rounded-12 p-20">
          <div class="d-flex justify-between items-center">
            <div class="d-flex items-center">
              <div class="d-flex justify-center items-center size-40 rounded-full bg-blue-3 mr-15">
                <div class="icon-video text-16 text-blue-1"></div>
              </div>
              <div>
                <h6 class="text-15 fw-500 text-dark-1">{{ __('messages.video_exam_title') }}</h6>
                <p class="text-12 text-light-1 mt-5">{{ __('messages.video_exam_description') }}</p>
              </div>
            </div>

            <div class="d-flex items-center">
              @if(!$hasPurchased)
                <div class="text-12 text-light-1">{{ __('messages.purchase_required') }}</div>
              @elseif(!$canUploadVideo)
                <div class="d-flex items-center">
                  <div class="icon-lock text-16 text-blue-1 mr-5"></div>
                  <span class="text-12 text-blue-1">{{ __('messages.complete_all_quizzes_exams') }}</span>
                </div>
              @else
                <button onclick="openVideoExamModal()" class="button -sm -blue-1 text-white">
                  {{ __('messages.upload_video') }}
                </button>
              @endif
            </div>
          </div>
        </div>
      </div>
    @endif

  @else
    <div class="bg-light-4 rounded-12 p-40 text-center">
      <div class="icon-quiz text-48 text-light-1 mb-20"></div>
      <h5 class="text-18 fw-500 text-dark-1 mb-10">{{ __('messages.no_quizzes_available') }}</h5>
      <p class="text-14 text-light-1">{{ __('messages.quizzes_will_be_added_soon') }}</p>
    </div>
  @endif
</div>

<!-- Video Exam Upload Modal -->
@if($product->is_exam_video)
<div id="videoExamModal" class="modal">
  <div class="modal-content" style="max-width: 600px;">
    <div class="bg-white rounded-16 p-40">
      <div class="d-flex justify-between items-center mb-30">
        <h3 class="text-20 fw-500 text-dark-1">{{ __('messages.video_exam_title') }}</h3>
        <button onclick="closeVideoExamModal()" class="close-modal">&times;</button>
      </div>

      <form id="videoExamForm" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">

        <div class="form-group mb-20">
          <label class="text-14 fw-500 text-dark-1 mb-10">{{ __('messages.upload_video') }}</label>
          <input type="file" name="video" id="videoFile" accept="video/*" class="form-control" required>
          <div class="text-12 text-light-1 mt-5">{{ __('messages.video_requirements') }}</div>
        </div>

        <div class="form-group mb-30">
          <label class="text-14 fw-500 text-dark-1 mb-10">{{ __('messages.video_description') }} ({{ __('messages.optional') }})</label>
          <textarea name="description" rows="4" class="form-control" placeholder="{{ __('messages.describe_your_video') }}"></textarea>
        </div>

        <div class="d-flex justify-end">
          <button type="button" onclick="closeVideoExamModal()" class="button -md -outline-dark-1 text-dark-1 mr-10">
            {{ __('messages.cancel') }}
          </button>
          <button type="submit" class="button -md -blue-1 text-white">
            {{ __('messages.submit_video_exam') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endif

<style>
.modal {
  display: none;
  position: fixed;
  z-index: 9999;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0,0,0,0.5);
}

.modal-content {
  position: relative;
  margin: 5% auto;
  background: white;
  border-radius: 16px;
  box-shadow: 0 20px 40px rgba(0,0,0,0.1);
}

.close-modal {
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
  color: #999;
}

.close-modal:hover {
  color: #333;
}

.form-control {
  width: 100%;
  padding: 12px 16px;
  border: 1px solid #e0e6ed;
  border-radius: 8px;
  font-size: 14px;
  transition: border-color 0.3s ease;
}

.form-control:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
}

/* RTL Support */
[dir="rtl"] .mr-15 {
  margin-right: 0 !important;
  margin-left: 15px !important;
}

[dir="rtl"] .mr-5 {
  margin-right: 0 !important;
  margin-left: 5px !important;
}

[dir="rtl"] .mr-10 {
  margin-right: 0 !important;
  margin-left: 10px !important;
}
</style>

<script>
function openVideoExamModal() {
  document.getElementById('videoExamModal').style.display = 'block';
  document.body.style.overflow = 'hidden';
}

function closeVideoExamModal() {
  document.getElementById('videoExamModal').style.display = 'none';
  document.body.style.overflow = 'auto';
  document.getElementById('videoExamForm').reset();
}

// Video Exam Form Submission
document.addEventListener('DOMContentLoaded', function() {
  const videoExamForm = document.getElementById('videoExamForm');
  if (videoExamForm) {
    videoExamForm.addEventListener('submit', function(e) {
      e.preventDefault();

      const formData = new FormData(this);
      const submitBtn = this.querySelector('button[type="submit"]');
      const originalText = submitBtn.textContent;

      submitBtn.disabled = true;
      submitBtn.textContent = '{{ __("messages.uploading") }}...';

      fetch('{{ route("video-exam.upload") }}', {
        method: 'POST',
        body: formData,
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('{{ __("messages.video_uploaded_successfully") }}');
          closeVideoExamModal();
        } else {
          alert(data.message || '{{ __("messages.error_uploading_video") }}');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('{{ __("messages.error_uploading_video") }}');
      })
      .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
      });
    });
  }

  // Close modal when clicking outside
  window.onclick = function(event) {
    const modal = document.getElementById('videoExamModal');
    if (event.target == modal) {
      closeVideoExamModal();
    }
  }

  // Close modal with Escape key
  document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
      closeVideoExamModal();
    }
  });
});
</script>
