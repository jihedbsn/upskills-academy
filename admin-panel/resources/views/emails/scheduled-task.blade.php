@extends('emails.base-template')

@section('title', 'Task Reminder')

@section('header-type', 'primary')

@section('email-title', '{{ $timing === "today" ? "Task Reminder Today" : "Task Scheduled Tomorrow" }}')

@section('content')
    <p class="greeting">Hello {{ $student_name }},</p>
    
    <p class="content-text">
        @if($timing === 'today')
            This is a reminder that you have a task scheduled for <strong>today</strong>.
        @else
            This is a notification that you have a task scheduled for <strong>tomorrow</strong>.
        @endif
    </p>
    
    <div class="info-card primary">
        <h3 class="info-card-title">Task Details</h3>
        <ul class="info-list">
            <li class="info-list-item"><strong>Course:</strong> {{ $course_name }}</li>
            <li class="info-list-item"><strong>Date:</strong> {{ $task_date }}</li>
            <li class="info-list-item"><strong>Time:</strong> {{ $task_time }}</li>
        </ul>
    </div>
    
    <div class="info-card warning">
        <h3 class="info-card-title">Task Description</h3>
        <p class="content-text">
            {{ $task_message }}
        </p>
    </div>
    
    <p class="content-text">
        @if($timing === 'today')
            Please prepare for this task which will take place today.
        @else
            Please prepare for this task which will take place tomorrow.
        @endif
    </p>
    
    <p class="content-text">
        If you have any questions, please contact us.
    </p>
@endsection
