@extends('emails.base-template')

@section('title', $is_passed ? 'You Passed!' : 'Exam Result')

@section('header-type', $is_passed ? 'success' : 'warning')

@section('email-title', $is_passed ? 'Congratulations! 🎉' : 'Practical Exam Result')

@section('content')
    <p class="greeting">Hello {{ $student_name }},</p>

    @if($is_passed)
        <p class="content-text">
            Congratulations! We're pleased to inform you that you have <strong>successfully passed</strong> your practical exam!
        </p>
    @else
        <p class="content-text">
            Thank you for completing your practical exam. After careful review, we regret to inform you that you did not pass this attempt. However, you can retry the exam to improve your results.
        </p>
    @endif

    <div class="info-card {{ $is_passed ? 'success' : 'warning' }}">
        <h3 class="info-card-title">Exam Details</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Course:</strong> {{ $course_name }}</li>
            <li class="info-list-item"><strong>Attempt Number:</strong> #{{ $attempt_number }}</li>
            <li class="info-list-item"><strong>Training Case:</strong> {{ $training_case_name }}</li>
            <li class="info-list-item"><strong>Result:</strong>
                <span style="color: {{ $is_passed ? '#10b981' : '#ef4444' }}; font-weight: 600;">
                    {{ $is_passed ? 'PASSED' : 'FAILED' }}
                </span>
            </li>
            <li class="info-list-item"><strong>Reviewed By:</strong> {{ $reviewer_name }}</li>
            <li class="info-list-item"><strong>Reviewed On:</strong> {{ $reviewed_at }}</li>
        </ul>
    </div>

    <div class="info-card neutral">
        <h3 class="info-card-title">Instructor Feedback</h3>
        <p style="white-space: pre-wrap; line-height: 1.6;">{{ $admin_comment }}</p>
    </div>

    @if(!$is_passed)
    <div class="info-card primary">
        <h3 class="info-card-title">Next Steps</h3>
        <ul class="info-list">
            <li class="info-list-item">Review the instructor's feedback carefully</li>
            <li class="info-list-item">Practice the areas that need improvement</li>
            <li class="info-list-item">You can start a new attempt when you're ready</li>
            <li class="info-list-item">Use the provided resources to prepare better</li>
        </ul>
    </div>
    @endif

    <div class="button-container">
        <a href="{{ $exam_url }}" class="button {{ $is_passed ? 'button-primary' : 'button-warning' }}">
            View Exam History
        </a>
    </div>

    <p class="content-text" style="font-size: 14px; color: #6b7280; margin-top: 30px;">
        @if($is_passed)
            Well done! Keep up the excellent work!
        @else
            Don't be discouraged. Use this feedback to improve and try again!
        @endif
    </p>
@endsection
