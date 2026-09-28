@if($responses && $responses->count() > 0)
    <div class="accordion mt-3" id="accordion{{ $accordionId }}">
        <div class="accordion-item">
            <h2 class="accordion-header" id="heading{{ $accordionId }}">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse{{ $accordionId }}" aria-expanded="false"
                        aria-controls="collapse{{ $accordionId }}">
                    <i class="bi bi-eye me-2"></i>
                    {{ __('messages.view_question_details') }}
                    ({{ $responses->count() }} / {{ $totalQuestions }} {{ __('messages.questions') }})
                </button>
            </h2>
            <div id="collapse{{ $accordionId }}" class="accordion-collapse collapse"
                 aria-labelledby="heading{{ $accordionId }}" data-bs-parent="#accordion{{ $accordionId }}">
                <div class="accordion-body">
                    @foreach($responses as $response)
                        @php
                            $hasAnswer = !empty($response->response_id);
                            $answerModel = $response->response ?? $response->reponse ?? null;
                            $studentAnswer = $answerModel?->reponse;
                        @endphp
                        <div class="border rounded p-3 mb-3 {{ $response->is_correct ? 'border-success bg-light-success' : 'border-danger bg-light-danger' }}">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="mb-1">
                                    <span class="badge {{ $response->is_correct ? 'bg-success' : 'bg-danger' }} me-2">
                                        Q{{ $loop->iteration }}
                                    </span>
                                    {!! $response->question?->question ?? __('messages.question_not_found') !!}
                                </h6>
                                <span class="badge bg-{{ $response->is_correct ? 'success' : 'danger' }}">
                                    @if(!$hasAnswer)
                                        {{ __('messages.incorrect') }} — {{ __('messages.not_answered') }}
                                    @else
                                        {{ $response->is_correct ? __('messages.correct') : __('messages.incorrect') }}
                                    @endif
                                </span>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted">{{ __('messages.your_answer') }}:</small>
                                    <div class="fw-bold {{ $response->is_correct ? 'text-success' : 'text-danger' }}">
                                        <i class="bi {{ $response->is_correct ? 'bi-check-lg' : 'bi-x-lg' }} me-1"></i>
                                        {{ $studentAnswer ?? __('messages.no_answer_provided') }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">{{ __('messages.correct_answer') }}:</small>
                                    <div class="fw-bold text-success">
                                        <i class="bi bi-check-circle me-1"></i>
                                        @if($response->question)
                                            @php
                                                $correctAnswer = $response->question->reponses->firstWhere('is_correcte', true)
                                                    ?? $response->question->reponses()->where('is_correcte', true)->first();
                                            @endphp
                                            {{ $correctAnswer?->reponse ?? __('messages.correct_answer_not_found') }}
                                        @else
                                            {{ __('messages.question_not_found') }}
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
        <i class="bi bi-info-circle me-2"></i>
        {{ __('messages.no_detailed_responses_available') }}
    </div>
@endif
