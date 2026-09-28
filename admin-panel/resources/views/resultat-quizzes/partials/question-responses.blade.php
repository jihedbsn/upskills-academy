@if($responses && $responses->count() > 0)
    <div class="accordion mt-3" id="accordionAttempt{{ $attemptNumber }}">
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingAttempt{{ $attemptNumber }}">
                <button class="accordion-button {{ ($expanded ?? false) ? '' : 'collapsed' }}" type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#collapseAttempt{{ $attemptNumber }}"
                        aria-expanded="{{ ($expanded ?? false) ? 'true' : 'false' }}"
                        aria-controls="collapseAttempt{{ $attemptNumber }}">
                    <i class="fa fa-eye me-2"></i>
                    View Question Details ({{ $responses->count() }} / {{ $totalQuestions }} questions)
                </button>
            </h2>
            <div id="collapseAttempt{{ $attemptNumber }}"
                 class="accordion-collapse collapse {{ ($expanded ?? false) ? 'show' : '' }}"
                 aria-labelledby="headingAttempt{{ $attemptNumber }}"
                 data-bs-parent="#accordionAttempt{{ $attemptNumber }}">
                <div class="accordion-body">
                    @foreach($responses as $response)
                        @php
                            $questionText = $response->question
                                ? ($response->question->name_en ?: $response->question->name_ar)
                                : null;
                            $answerModel = $response->response ?? $response->reponse ?? null;
                            $studentAnswer = $answerModel
                                ? ($answerModel->titre_en ?: $answerModel->titre_ar)
                                : null;
                        @endphp
                        <div class="border rounded p-3 mb-3 {{ $response->is_correct ? 'border-success bg-success-light' : 'border-danger bg-danger-light' }}">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="mb-1">
                                    <span class="badge {{ $response->is_correct ? 'bg-success' : 'bg-danger' }} me-2">
                                        Q{{ $loop->iteration }}
                                    </span>
                                    {!! $questionText ?? 'Question not found' !!}
                                </h6>
                                <span class="badge bg-{{ $response->is_correct ? 'success' : 'danger' }}">
                                    {{ $response->is_correct ? 'Correct' : 'Incorrect' }}
                                </span>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted">Student's Answer:</small>
                                    <div class="fw-bold {{ $response->is_correct ? 'text-success' : 'text-danger' }}">
                                        <i class="fa {{ $response->is_correct ? 'fa-check' : 'fa-times' }} me-1"></i>
                                        {{ $studentAnswer ?? 'No answer provided' }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">Correct Answer:</small>
                                    <div class="fw-bold text-success">
                                        <i class="fa fa-check-circle me-1"></i>
                                        @if($response->question)
                                            @php
                                                $correctAnswer = $response->question->reponses->firstWhere('is_correcte', true)
                                                    ?? $response->question->reponses()->where('is_correcte', true)->first();
                                            @endphp
                                            {{ $correctAnswer ? ($correctAnswer->titre_en ?: $correctAnswer->titre_ar) : 'Correct answer not found' }}
                                        @else
                                            Question not found
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@else
    <div class="alert alert-info mb-0 mt-3">
        <i class="fa fa-info-circle me-2"></i>
        No detailed question responses available for this attempt.
    </div>
@endif
