@extends('emails.base-template')

@section('title', 'Practical Exam Submitted')

@section('header-type', 'success')

@section('email-title', 'Practical Exam Submitted Successfully')

@section('content')
    <p class="greeting">Hello {{ $student_name }},</p>

    <p class="content-text">
        Your practical exam has been successfully submitted and is now pending review by your instructor.
    </p>

    <div class="info-card success">
        <h3 class="info-card-title">Submission Details</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Course:</strong> {{ $course_name }}</li>
            <li class="info-list-item"><strong>Training Case:</strong> {{ $training_case_name }}</li>
            <li class="info-list-item"><strong>Attempt Number:</strong> #{{ $attempt_number }}</li>
            <li class="info-list-item"><strong>Exam Type:</strong> {{ $exam_type }}</li>
            <li class="info-list-item"><strong>Submission Date:</strong> {{ $submission_date }}</li>
        </ul>
    </div>

    <div class="info-card primary">
        <h3 class="info-card-title">What's Next?</h3>
        <p class="content-text">
            Your instructor will review your submission and provide feedback. You will receive an email notification once your exam has been graded.
        </p>
    </div>

    <p class="content-text">
        Thank you for completing your practical exam. Good luck!
    </p>

    <div class="button-container">
        <a href="{{ route('student.practical-exam', $attempt->product_id) }}" class="button button-primary">View Exam Status</a>
    </div>
@endsection

@section('footer')
    <p class="footer-text">
        If you have any questions about your submission, please contact your instructor or support team.
    </p>
@endsection
