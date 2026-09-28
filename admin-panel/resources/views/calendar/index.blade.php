@extends('layouts.app')

@section('title', 'Calendar')

@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">
                            <i class="fa fa-calendar-alt me-2"></i>Dynamic Calendar
                        </h2>
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active">Calendar</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <!-- Calendar Container -->
        <div class="row">
            <div class="col-12">
                <div class="card modern-calendar-card">
                    <div class="card-header calendar-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="calendar-title-section">
                                <h4 class="card-title mb-0">
                                    <i class="fa fa-calendar-alt me-2"></i>Events Calendar
                                </h4>
                                <p class="text-muted mb-0">Manage your events and appointments with FullCalendar</p>
                            </div>
                            <div class="calendar-actions d-flex align-items-center gap-3">
                                <div class="language-selector">
                                    <label class="text-muted me-2 small">Language:</label>
                                    <select id="languageSelect" class="form-select form-select-sm" style="width: auto;">
                                        <option value="fr" {{ $language == 'fr' ? 'selected' : '' }}>Français</option>
                                        <option value="en" {{ $language == 'en' ? 'selected' : '' }}>English</option>
                                    </select>
                                </div>
                                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addEventModal">
                                    <i class="fa fa-plus me-1"></i>New Event
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-3">
                        <!-- FullCalendar Container -->
                        <div id="fullcalendar-container">
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Event Statistics -->
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <div class="stats-icon quiz">
                            <i class="fa fa-question-circle"></i>
                        </div>
                        <h4 class="stats-number" id="quizCount">0</h4>
                        <p class="stats-label">Quiz</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <div class="stats-icon exam">
                            <i class="fa fa-graduation-cap"></i>
                        </div>
                        <h4 class="stats-number" id="examCount">0</h4>
                        <p class="stats-label">Exams</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <div class="stats-icon meeting">
                            <i class="fa fa-users"></i>
                        </div>
                        <h4 class="stats-number" id="meetingCount">0</h4>
                        <p class="stats-label">Meetings</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <div class="stats-icon certificate">
                            <i class="fa fa-certificate"></i>
                        </div>
                        <h4 class="stats-number" id="certificateCount">0</h4>
                        <p class="stats-label">Certificates</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Event Modal -->
<div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addEventModalLabel">
                    <i class="fa fa-plus-circle me-2"></i>Ajouter une Tâche Planifiée
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addEventForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="eventCourse" class="form-label">
                            <i class="fa fa-book me-2"></i>Cours <span class="text-danger">*</span>
                        </label>
                        <div class="position-relative">
                            <select class="form-select" id="eventCourse" name="course_id" required>
                                <option value="">Rechercher et sélectionner un cours...</option>
                            </select>
                            <div class="invalid-feedback" id="courseError">
                                Veuillez sélectionner un cours.
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="eventMessage" class="form-label">
                            <i class="fa fa-comment me-2"></i>Message de la Tâche
                        </label>
                        <textarea class="form-control" id="eventMessage" name="title" rows="3" required placeholder="Décrivez la tâche à planifier..."></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="eventDate" class="form-label">
                                    <i class="fa fa-calendar me-1"></i>Date
                                </label>
                                <input type="date" class="form-control" id="eventDate" name="date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="eventTime" class="form-label">
                                    <i class="fa fa-clock me-1"></i>Heure
                                </label>
                                <input type="time" class="form-control" id="eventTime" name="time" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save me-1"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Event Details Modal -->
<div class="modal fade" id="eventDetailsModal" tabindex="-1" aria-labelledby="eventDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" id="eventDetailsHeader">
                <h5 class="modal-title" id="eventDetailsModalLabel">
                    <i class="fa fa-info-circle me-2"></i>Détails de l'Événement
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="eventDetailsContent">
                    <!-- Event details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="deleteEventBtn">
                    <i class="fa fa-trash me-1"></i>Supprimer
                </button>
                <button type="button" class="btn btn-warning" id="editEventBtn">
                    <i class="fa fa-edit me-1"></i>Modifier
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa fa-times me-1"></i>Fermer
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* Modern Calendar Styles */
.modern-calendar-card {
    border: none;
    box-shadow: 0 4px 25px rgba(0,0,0,0.1);
    border-radius: 15px;
    overflow: hidden;
}

.calendar-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border: none;
}

.calendar-title-section h4 {
    color: white;
    font-weight: 600;
}

.calendar-title-section p {
    color: rgba(255,255,255,0.8);
    font-size: 0.9rem;
}

.calendar-actions .btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.calendar-actions .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* FullCalendar Custom Styles */
#fullcalendar-container {
    background: white;
    border-radius: 0 0 15px 15px;
    overflow: hidden;
}

#calendar {
    font-family: 'Montserrat', sans-serif;
}

/* FullCalendar Theme Customization */
.fc {
    font-family: 'Montserrat', sans-serif !important;
}

.fc-theme-standard .fc-scrollgrid {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    overflow: hidden;
}

.fc-theme-standard th {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
    border-color: #e9ecef !important;
    color: #495057 !important;
    font-weight: 600 !important;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.85rem;
    padding: 1rem 0.5rem;
}

.fc-theme-standard td {
    border-color: #f1f3f4 !important;
    transition: all 0.3s ease;
}

.fc-theme-standard td:hover {
    background-color: #f8f9fa !important;
}

.fc-day-today {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    color: white !important;
}

.fc-day-today .fc-daygrid-day-number {
    color: white !important;
    font-weight: 700 !important;
}

.fc-daygrid-day-number {
    font-weight: 600;
    font-size: 1rem;
    padding: 0.5rem;
    transition: all 0.3s ease;
}

.fc-daygrid-day-number:hover {
    transform: scale(1.1);
}

/* Event Styles */
.fc-event {
    border: none !important;
    border-radius: 6px !important;
    padding: 0.25rem 0.5rem !important;
    font-size: 0.75rem !important;
    font-weight: 500 !important;
    cursor: pointer !important;
    transition: all 0.3s ease !important;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
    margin: 1px 0 !important;
}

.fc-event:hover {
    transform: translateY(-1px) !important;
    box-shadow: 0 4px 8px rgba(0,0,0,0.15) !important;
    opacity: 0.9 !important;
}

.fc-event-title {
    font-weight: 500 !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
}

/* Event Type Colors */
.fc-event.quiz-event {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    color: white !important;
}

.fc-event.exam-event {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important;
    color: white !important;
}

.fc-event.review-event {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%) !important;
    color: white !important;
}

.fc-event.certificate-event {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%) !important;
    color: #000 !important;
}

.fc-event.meeting-event {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%) !important;
    color: white !important;
}

.fc-event.other-event {
    background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%) !important;
    color: #000 !important;
}

/* Toolbar Styles */
.fc-toolbar {
    margin-bottom: 1.5rem !important;
    padding: 1rem 0 !important;
}

.fc-toolbar-title {
    font-size: 1.5rem !important;
    font-weight: 700 !important;
    color: #2c3e50 !important;
    text-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.fc-button {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    border: none !important;
    border-radius: 8px !important;
    padding: 0.5rem 1rem !important;
    font-weight: 500 !important;
    font-size: 0.875rem !important;
    transition: all 0.3s ease !important;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
}

.fc-button:hover {
    background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%) !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
}

.fc-button:focus {
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25) !important;
}

.fc-button-primary:not(:disabled):active {
    background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%) !important;
}

.fc-button-group > .fc-button {
    margin: 0 2px !important;
}

/* More/Less Links */
.fc-more-link {
    color: #667eea !important;
    font-weight: 500 !important;
    text-decoration: none !important;
    transition: all 0.3s ease !important;
}

.fc-more-link:hover {
    color: #5a6fd8 !important;
    text-decoration: underline !important;
}

/* Popover Styles */
.fc-popover {
    border: none !important;
    border-radius: 10px !important;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2) !important;
}

.fc-popover-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    color: white !important;
    border-radius: 10px 10px 0 0 !important;
    padding: 0.75rem 1rem !important;
    font-weight: 600 !important;
}

.fc-popover-body {
    padding: 1rem !important;
    background: white !important;
    border-radius: 0 0 10px 10px !important;
}

/* Statistics Cards */
.stats-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    overflow: hidden;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.stats-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 1.5rem;
    color: white;
}

.stats-icon.quiz {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.stats-icon.exam {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.stats-icon.meeting {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
}

.stats-icon.certificate {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}

.stats-number {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: #2c3e50;
}

.stats-label {
    color: #6c757d;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.85rem;
}

/* Modal Styles */
.modal-content {
    border: none;
    border-radius: 15px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
}

.modal-header.bg-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    border-radius: 15px 15px 0 0;
}

.modal-header {
    border-radius: 15px 15px 0 0;
}

.modal-footer {
    border-radius: 0 0 15px 15px;
}

/* Color Picker */
.color-picker {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.color-picker input[type="radio"] {
    display: none;
}

.color-option {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    cursor: pointer;
    border: 3px solid transparent;
    transition: all 0.3s ease;
    position: relative;
}

.color-option:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.color-picker input[type="radio"]:checked + .color-option {
    border-color: #667eea;
    transform: scale(1.15);
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
}

.color-picker input[type="radio"]:checked + .color-option::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-weight: bold;
    font-size: 1.2rem;
    text-shadow: 0 1px 2px rgba(0,0,0,0.5);
}

/* Event Details */
.event-details {
    padding: 1rem;
}

.event-details h6 {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: #2c3e50;
}

.event-details p {
    margin-bottom: 0.75rem;
    color: #495057;
}

.event-type-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.event-type-quiz {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.event-type-exam {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.event-type-review {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
}

.event-type-certificate {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    color: #000;
}

.event-type-meeting {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    color: white;
}

.event-type-other {
    background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
    color: #000;
}

/* Loading Animation */
.fc-loading {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 200px;
}

.fc-loading::after {
    content: '';
    width: 40px;
    height: 40px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Success/Error Messages */
.alert-modern {
    border: none;
    border-radius: 10px;
    padding: 1rem 1.5rem;
    font-weight: 500;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.alert-success {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    color: #000;
}

.alert-danger {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

/* Course Search Dropdown */
.course-option {
    cursor: pointer;
    transition: all 0.3s ease;
    border-bottom: 1px solid #f1f3f4;
}

.course-option:hover {
    background-color: #f8f9fa !important;
    transform: translateX(2px);
}

.course-option.active {
    background-color: #667eea !important;
    color: white !important;
}

.course-option.active .text-muted {
    color: rgba(255, 255, 255, 0.8) !important;
}

.course-option.active .badge {
    background-color: rgba(255, 255, 255, 0.2) !important;
    color: white !important;
}

#courseDropdown {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    z-index: 1050;
}

#courseSearch.is-valid {
    border-color: #28a745;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='m2.3 6.73.94-.94 1.85-1.85L6.73 4.3 5.78 5.25l-1.85 1.85z'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

#courseSearch.is-invalid {
    border-color: #dc3545;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 4.6 1.4 1.4m0-1.4-1.4 1.4'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

#courseError {
    font-size: 0.875rem;
    margin-top: 0.25rem;
    color: #dc3545;
}

/* Select with search styling */
#eventCourse {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m1 6 7 7 7-7'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 16px 12px;
    padding-right: 2.5rem;
    cursor: pointer;
}

#eventCourse:focus {
    border-color: #80bdff;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

#eventCourse option {
    padding: 0.5rem;
    font-size: 0.9rem;
}

#eventCourse option[value=""] {
    color: #6c757d;
    font-style: italic;
}

#eventCourse option:not([value=""]) {
    color: #212529;
}

/* Loading state for select */
#eventCourse.loading {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='%23343a40' d='M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0zM7 2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5zM8 14a6 6 0 1 1 0-12 6 6 0 0 1 0 12zm-1-9a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5z'/%3e%3c/svg%3e");
    background-size: 16px 16px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Validation states for select */
#eventCourse.is-valid {
    border-color: #28a745;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='m2.3 6.73.94-.94 1.85-1.85L6.73 4.3 5.78 5.25l-1.85 1.85z'/%3e%3c/svg%3e"), url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m1 6 7 7 7-7'/%3e%3c/svg%3e");
    background-repeat: no-repeat, no-repeat;
    background-position: right 2.5rem center, right 0.75rem center;
    background-size: 16px 12px, 16px 12px;
}

#eventCourse.is-invalid {
    border-color: #dc3545;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 4.6 1.4 1.4m0-1.4-1.4 1.4'/%3e%3c/svg%3e"), url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m1 6 7 7 7-7'/%3e%3c/svg%3e");
    background-repeat: no-repeat, no-repeat;
    background-position: right 2.5rem center, right 0.75rem center;
    background-size: 16px 12px, 16px 12px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .calendar-header {
        padding: 1rem;
    }

    .calendar-title-section h4 {
        font-size: 1.25rem;
    }

    .fc-toolbar {
        flex-direction: column;
        gap: 1rem;
    }

    .fc-toolbar-chunk {
        display: flex;
        justify-content: center;
    }

    .fc-button {
        padding: 0.4rem 0.8rem !important;
        font-size: 0.8rem !important;
    }

    .fc-event {
        font-size: 0.7rem !important;
        padding: 0.2rem 0.4rem !important;
    }
}

@media (max-width: 576px) {
    .fc-daygrid-day-number {
        font-size: 0.9rem;
        padding: 0.25rem;
    }

    .fc-event {
        font-size: 0.65rem !important;
        padding: 0.15rem 0.3rem !important;
    }

    .fc-toolbar-title {
        font-size: 1.25rem !important;
    }
}
</style>
@endpush

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let calendar;
    let selectedEvent = null;

    // Event types configuration
    const eventTypes = {
        quiz: { name: 'Quiz', icon: 'fa-question-circle', color: '#667eea' },
        exam: { name: 'Examen', icon: 'fa-graduation-cap', color: '#f093fb' },
        review: { name: 'Révision', icon: 'fa-book-open', color: '#4facfe' },
        certificate: { name: 'Certificat', icon: 'fa-certificate', color: '#43e97b' },
        meeting: { name: 'Réunion', icon: 'fa-users', color: '#fa709a' },
        other: { name: 'Autre', icon: 'fa-circle', color: '#a8edea' }
    };

    // Initialize FullCalendar
    function initializeCalendar() {
        const calendarEl = document.getElementById('calendar');

        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'fr',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,listWeek'
            },
            buttonText: {
                today: 'Aujourd\'hui',
                month: 'Mois',
                week: 'Semaine',
                list: 'Liste'
            },
            dayMaxEvents: 3,
            moreLinkClick: 'popover',
            eventDisplay: 'block',
            height: 'auto',
            aspectRatio: 1.8,

            // Event sources
            events: function(info, successCallback, failureCallback) {
                loadEvents(info.start, info.end, successCallback, failureCallback);
            },

            // Event interactions
            eventClick: function(info) {
                handleEventClick(info.event);
                info.jsEvent.preventDefault();
            },

            dateClick: function(info) {
                createEventOnDate(info.dateStr);
            },

            eventDrop: function(info) {
                updateEventDate(info.event);
            },

            eventResize: function(info) {
                updateEventDate(info.event);
            },

            // Loading callback
            loading: function(bool) {
                if (bool) {
                    showLoading();
                } else {
                    hideLoading();
                }
            },

            // Event rendering
            eventDidMount: function(info) {
                // Add custom classes based on event type
                const eventType = info.event.extendedProps.type || 'other';
                info.el.classList.add(`${eventType}-event`);

                // Add hover effects
                info.el.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px) scale(1.05)';
                });

                info.el.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            }
        });

        calendar.render();
        updateStatistics();
    }

    // Load events from server
    async function loadEvents(start, end, successCallback, failureCallback) {
        try {
            const startStr = start.toISOString().split('T')[0];
            const endStr = end.toISOString().split('T')[0];

            const response = await fetch(`/calendar/events?start=${startStr}&end=${endStr}`);
            const events = await response.json();

            // Transform events for FullCalendar
            const fcEvents = events.map(event => ({
                id: event.id,
                title: event.title,
                start: event.date + (event.time ? 'T' + event.time : ''),
                allDay: !event.time,
                extendedProps: {
                    type: event.type,
                    description: event.description,
                    color: event.color
                },
                backgroundColor: getEventColor(event.type, event.color),
                borderColor: getEventColor(event.type, event.color),
                textColor: getTextColor(event.type)
            }));

            successCallback(fcEvents);
            updateStatistics(fcEvents);
        } catch (error) {
            console.error('Error loading events:', error);
            failureCallback(error);
        }
    }

    // Get event color based on type
    function getEventColor(type, customColor) {
        if (customColor) return customColor;

        const colors = {
            quiz: '#667eea',
            exam: '#f093fb',
            review: '#4facfe',
            certificate: '#43e97b',
            meeting: '#fa709a',
            other: '#a8edea'
        };

        return colors[type] || colors.other;
    }

    // Get text color based on type
    function getTextColor(type) {
        const lightTypes = ['certificate', 'other'];
        return lightTypes.includes(type) ? '#000' : '#fff';
    }

    // Handle event click - redirect to Zoom details or show popup
    function handleEventClick(event) {
        // Check if it's a Zoom meeting (id starts with 'zoom_')
        if (event.id && event.id.startsWith('zoom_')) {
            // Extract zoom meeting ID and redirect to details page
            const zoomMeetingId = event.id.replace('zoom_', '');
            window.location.href = `/zoom-meetings/${zoomMeetingId}`;
            return;
        }

        // For other events, show the details modal
        showEventDetails(event);
    }

    // Show event details modal
    function showEventDetails(event) {
        selectedEvent = event;

        const eventType = eventTypes[event.extendedProps.type] || eventTypes.other;
        const startDate = event.start ? new Date(event.start) : null;

        const content = `
            <div class="event-details">
                <div class="d-flex align-items-center mb-3">
                    <div class="event-type-icon me-3" style="width: 50px; height: 50px; background: ${event.backgroundColor}; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: ${event.textColor};">
                        <i class="fa ${eventType.icon}"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">${event.title}</h6>
                        <span class="event-type-badge event-type-${event.extendedProps.type}">${eventType.name}</span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <p><strong><i class="fa fa-calendar me-2"></i>Date:</strong> ${startDate ? startDate.toLocaleDateString('fr-FR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) : 'N/A'}</p>
                    </div>
                    <div class="col-md-6">
                        ${event.start && event.start.getHours ? `<p><strong><i class="fa fa-clock me-2"></i>Heure:</strong> ${event.start.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })}</p>` : ''}
                    </div>
                </div>

            </div>
        `;

        document.getElementById('eventDetailsContent').innerHTML = content;

        // Update modal header color
        const header = document.getElementById('eventDetailsHeader');
        header.style.background = `linear-gradient(135deg, ${event.backgroundColor} 0%, ${darkenColor(event.backgroundColor, 20)} 100%)`;
        header.style.color = 'white';

        const modal = new bootstrap.Modal(document.getElementById('eventDetailsModal'));
        modal.show();
    }

    // Create event on specific date
    function createEventOnDate(dateStr) {
        document.getElementById('eventDate').value = dateStr;
        const modal = new bootstrap.Modal(document.getElementById('addEventModal'));
        modal.show();
    }

    // Add new event
    async function addEvent() {
        // Validate course selection first
        if (!validateCourseSelection()) {
            showAlert('Veuillez sélectionner un cours', 'danger');
            return;
        }

        const formData = new FormData(document.getElementById('addEventForm'));
        const eventData = {
            title: formData.get('title'),
            date: formData.get('date'),
            time: formData.get('time'),
            course_id: formData.get('course_id'),
            student_id: formData.get('student_id'),
            priority: null
        };

        try {
            const response = await fetch('/calendar/events', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(eventData)
            });

            if (response.ok) {
                // Close modal and reset form
                bootstrap.Modal.getInstance(document.getElementById('addEventModal')).hide();
                resetForm();

                // Refresh calendar
                calendar.refetchEvents();

                showAlert('Tâche planifiée ajoutée avec succès!', 'success');
            } else {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Failed to add event');
            }
        } catch (error) {
            console.error('Error adding event:', error);
            showAlert('Erreur lors de l\'ajout de l\'événement: ' + error.message, 'danger');
        }
    }

    // Delete event
    async function deleteEvent() {
        if (!selectedEvent) return;

        if (confirm('Êtes-vous sûr de vouloir supprimer cet événement?')) {
            try {
                const response = await fetch(`/calendar/events/${selectedEvent.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    bootstrap.Modal.getInstance(document.getElementById('eventDetailsModal')).hide();
                    calendar.refetchEvents();
                    showAlert('Événement supprimé avec succès!', 'success');
                }
            } catch (error) {
                console.error('Error deleting event:', error);
                showAlert('Erreur lors de la suppression de l\'événement', 'danger');
            }
        }
    }

    // Edit event
    function editEvent() {
        if (!selectedEvent) return;

        bootstrap.Modal.getInstance(document.getElementById('eventDetailsModal')).hide();

        // Pre-fill the form
        document.getElementById('eventMessage').value = selectedEvent.title;

        const startDate = selectedEvent.start ? new Date(selectedEvent.start) : null;
        if (startDate) {
            document.getElementById('eventDate').value = startDate.toISOString().split('T')[0];
            if (!selectedEvent.allDay && startDate.getHours) {
                document.getElementById('eventTime').value = startDate.toTimeString().slice(0, 5);
            }
        }


        // Set course if available
        if (selectedEvent.extendedProps.course_name) {
            const select = document.getElementById('eventCourse');

            // Find the course in allCourses
            const course = allCourses.find(c => c.title === selectedEvent.extendedProps.course_name);
            if (course) {
                select.value = course.id;
                select.classList.remove('is-invalid');
                select.classList.add('is-valid');
                document.getElementById('courseError').style.display = 'none';
            }
        }

        // Change modal title
        document.getElementById('addEventModalLabel').innerHTML = '<i class="fa fa-edit me-2"></i>Modifier la Tâche';

        const modal = new bootstrap.Modal(document.getElementById('addEventModal'));
        modal.show();

        // Reset modal title when closed
        modal._element.addEventListener('hidden.bs.modal', () => {
            document.getElementById('addEventModalLabel').innerHTML = '<i class="fa fa-plus-circle me-2"></i>Ajouter une Tâche';
        });
    }

    // Update event date
    async function updateEventDate(event) {
        try {
            const eventData = {
                title: event.title,
                date: event.start.toISOString().split('T')[0],
                time: event.allDay ? null : event.start.toTimeString().slice(0, 5),
                course_id: event.extendedProps.course_id,
                student_id: event.extendedProps.student_id,
                priority: event.extendedProps.priority
            };

            const response = await fetch(`/calendar/events/${event.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(eventData)
            });

            if (response.ok) {
                showAlert('Événement mis à jour avec succès!', 'success');
            } else {
                throw new Error('Failed to update event');
            }
        } catch (error) {
            console.error('Error updating event:', error);
            showAlert('Erreur lors de la mise à jour de l\'événement', 'danger');
            calendar.refetchEvents(); // Refresh to revert changes
        }
    }

    // Update statistics
    function updateStatistics(events) {
        if (!events) {
            events = calendar.getEvents();
        }

        const stats = {
            quiz: 0,
            exam: 0,
            meeting: 0,
            certificate: 0
        };

        events.forEach(event => {
            const type = event.extendedProps.type;
            if (stats.hasOwnProperty(type)) {
                stats[type]++;
            }
        });

        // Animate statistics
        Object.keys(stats).forEach(type => {
            const element = document.getElementById(`${type}Count`);
            animateNumber(element, stats[type]);
        });
    }

    // Animate number
    function animateNumber(element, targetNumber) {
        const startNumber = parseInt(element.textContent) || 0;
        const duration = 1000;
        const startTime = performance.now();

        const animate = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);

            const currentNumber = Math.round(startNumber + (targetNumber - startNumber) * progress);
            element.textContent = currentNumber;

            if (progress < 1) {
                requestAnimationFrame(animate);
            }
        };

        requestAnimationFrame(animate);
    }

    // Darken color utility
    function darkenColor(color, percent) {
        const num = parseInt(color.replace("#", ""), 16);
        const amt = Math.round(2.55 * percent);
        const R = (num >> 16) - amt;
        const G = (num >> 8 & 0x00FF) - amt;
        const B = (num & 0x0000FF) - amt;
        return "#" + (0x1000000 + (R < 255 ? R < 1 ? 0 : R : 255) * 0x10000 +
            (G < 255 ? G < 1 ? 0 : G : 255) * 0x100 +
            (B < 255 ? B < 1 ? 0 : B : 255)).toString(16).slice(1);
    }

    // Show alert
    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-modern alert-${type} alert-dismissible fade show`;
        alertDiv.style.position = 'fixed';
        alertDiv.style.top = '20px';
        alertDiv.style.right = '20px';
        alertDiv.style.zIndex = '9999';
        alertDiv.style.minWidth = '300px';
        alertDiv.innerHTML = `
            <i class="fa fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(alertDiv);

        // Auto remove after 4 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.classList.remove('show');
                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.parentNode.removeChild(alertDiv);
                    }
                }, 300);
            }
        }, 4000);
    }

    // Show loading
    function showLoading() {
        const calendarEl = document.getElementById('calendar');
        calendarEl.style.opacity = '0.6';
        calendarEl.style.pointerEvents = 'none';
    }

    // Hide loading
    function hideLoading() {
        const calendarEl = document.getElementById('calendar');
        calendarEl.style.opacity = '1';
        calendarEl.style.pointerEvents = 'auto';
    }

    // Event listeners
    document.getElementById('addEventForm').addEventListener('submit', function(e) {
        e.preventDefault();
        addEvent();
    });

    document.getElementById('deleteEventBtn').addEventListener('click', deleteEvent);
    document.getElementById('editEventBtn').addEventListener('click', editEvent);

    // Reset form when modal is closed
    document.getElementById('addEventModal').addEventListener('hidden.bs.modal', function() {
        resetForm();
    });

    // Reset form function
    function resetForm() {
        document.getElementById('addEventForm').reset();
        document.getElementById('eventDate').value = new Date().toISOString().split('T')[0];

        // Reset course selection
        const select = document.getElementById('eventCourse');
        select.value = '';
        select.classList.remove('is-valid', 'is-invalid');

        // Hide error message
        document.getElementById('courseError').style.display = 'none';

        // Reset modal title
        document.getElementById('addEventModalLabel').innerHTML = '<i class="fa fa-plus-circle me-2"></i>Ajouter une Tâche Planifiée';
    }

    // Auto-fill current date
    document.getElementById('eventDate').value = new Date().toISOString().split('T')[0];

    // Course data from controller
    let allCourses = @json($coursesData ?? []);

    // Load courses for dropdown
    function loadCourses() {
        const select = document.getElementById('eventCourse');
        select.classList.add('loading');

        if (allCourses && allCourses.length > 0) {
            populateCourseSelect();
        } else {
            showCourseError();
        }

        select.classList.remove('loading');
    }

    // Load courses for selection
    loadCourses();

    // Populate course select
    function populateCourseSelect() {
        const select = document.getElementById('eventCourse');

        // Clear existing options except the first one
        select.innerHTML = '<option value="">Rechercher et sélectionner un cours...</option>';

        // Add course options
        allCourses.forEach(course => {
            const option = document.createElement('option');
            option.value = course.id;
            option.textContent = `${course.title} (${course.type}) - ${course.langue.toUpperCase()}`;
            if (course.description) {
                option.title = course.description;
            }
            select.appendChild(option);
        });

        // Add validation event listener
        select.addEventListener('change', function() {
            validateCourseSelection();
        });
    }


    // Validate course selection
    function validateCourseSelection() {
        const select = document.getElementById('eventCourse');
        const courseError = document.getElementById('courseError');

        if (!select.value || select.value === '') {
            select.classList.remove('is-valid');
            select.classList.add('is-invalid');
            courseError.style.display = 'block';
            return false;
        } else {
            select.classList.remove('is-invalid');
            select.classList.add('is-valid');
            courseError.style.display = 'none';
            return true;
        }
    }

    // Show course error
    function showCourseError() {
        const select = document.getElementById('eventCourse');
        select.innerHTML = '<option value="">Erreur de chargement des cours</option>';
        select.classList.add('is-invalid');
    }

    // Language selector functionality
    document.getElementById('languageSelect').addEventListener('change', function() {
        const selectedLanguage = this.value;
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.set('lang', selectedLanguage);
        window.location.href = currentUrl.toString();
    });

    // Initialize calendar
    initializeCalendar();
});
</script>
@endpush
