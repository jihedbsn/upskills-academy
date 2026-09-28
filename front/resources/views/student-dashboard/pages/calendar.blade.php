@extends('student-dashboard.layouts.app')

@section('title', __('messages.my_calendar'))

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('student-dashboard.index') }}">{{ __('messages.dashboard') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('messages.my_calendar') }}</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">{{ __('messages.my_calendar') }}</h1>
            <p class="text-muted mb-0">{{ __('messages.view_scheduled_sessions') }}</p>
        </div>
        <div>
            <button class="btn btn-sm" id="monthViewBtn" style="background-color: #0071ae; color: white;">
                <i class="bi bi-calendar3"></i> {{ __('messages.month') }}
            </button>
            <button class="btn btn-sm btn-outline-secondary" id="listViewBtn">
                <i class="bi bi-list-ul"></i> {{ __('messages.list') }}
            </button>
        </div>
    </div>

    <!-- Calendar View -->
    <div id="calendarView" class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-sm btn-outline-secondary" id="prevMonth">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <h5 class="mb-0" id="currentMonth"></h5>
                    <button class="btn btn-sm btn-outline-secondary" id="nextMonth">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
                <button class="btn btn-sm btn-outline-secondary" id="todayBtn">
                    <i class="bi bi-calendar-day"></i> {{ __('messages.today') }}
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div id="calendar"></div>
        </div>
    </div>

    <!-- List View -->
    <div id="listView" class="d-none">
        <!-- Upcoming Events -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0"><i class="bi bi-calendar-check" style="color: #0071ae;"></i> {{ __('messages.upcoming_events') }}</h5>
            </div>
            <div class="card-body">
                <div id="upcomingEventsList"></div>
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="card shadow-sm border-0 mt-3">
        <div class="card-body">
            <div class="d-flex gap-4 flex-wrap">
                <div class="d-flex align-items-center">
                    <span class="d-inline-block me-2 rounded" style="background-color: #0071ae; width: 20px; height: 20px;"></span>
                    <small>{{ __('messages.course_sessions') }}</small>
                </div>
                <div class="d-flex align-items-center">
                    <span class="d-inline-block me-2 rounded" style="background-color: #28a745; width: 20px; height: 20px;"></span>
                    <small>{{ __('messages.zoom_meetings') }}</small>
                </div>
                <div class="d-flex align-items-center">
                    <span class="d-inline-block me-2 rounded" style="background-color: #f59e0b; width: 20px; height: 20px;"></span>
                    <small>{{ __('messages.scheduled_tasks') }}</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Event Details Modal -->
<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #0071ae; color: white;">
                <h5 class="modal-title" id="eventModalTitle"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="eventModalBody">
                <!-- Event details will be loaded here -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
<style>
#calendar {
    padding: 1.5rem;
    min-height: 600px;
}

.fc-event {
    cursor: pointer;
    border: none !important;
    padding: 4px 8px;
    margin: 2px 0;
    border-radius: 4px;
    font-size: 0.85rem;
    font-weight: 500;
}

.fc-event:hover {
    opacity: 0.85;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.fc-daygrid-event-dot {
    display: none;
}

.fc-event-title {
    font-weight: 500;
}

.fc-daygrid-day-number {
    color: #0071ae !important;
    font-weight: 600;
    font-size: 0.95rem;
    padding: 8px;
}

.fc-col-header-cell {
    background-color: rgba(0, 113, 174, 0.05);
    padding: 12px 0;
}

.fc-col-header-cell-cushion {
    color: #0071ae;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
}

.fc-daygrid-day-frame {
    min-height: 100px;
}

.fc-daygrid-day-top {
    justify-content: center;
}

.fc-day-today {
    background-color: rgba(252, 209, 22, 0.1) !important;
}

.fc-day-today .fc-daygrid-day-number {
    background-color: #FCD116;
    color: #000 !important;
    border-radius: 50%;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.fc-button-primary {
    background-color: #0071ae !important;
    border-color: #0071ae !important;
}

.fc-button-primary:hover {
    background-color: #005a8c !important;
    border-color: #005a8c !important;
}

.fc-button-primary:not(:disabled).fc-button-active {
    background-color: #005a8c !important;
    border-color: #005a8c !important;
}

.event-list-item {
    padding: 1rem;
    border-left: 4px solid #0071ae;
    background-color: rgba(0, 113, 174, 0.05);
    margin-bottom: 0.75rem;
    border-radius: 0.25rem;
    transition: all 0.2s;
}

.event-list-item:hover {
    background-color: rgba(0, 113, 174, 0.1);
    transform: translateX(5px);
}

.event-list-item.zoom {
    border-left-color: #28a745;
    background-color: rgba(40, 167, 69, 0.05);
}

.event-list-item.zoom:hover {
    background-color: rgba(40, 167, 69, 0.1);
}
</style>
@endpush

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const courseSessions = @json($courseSessions);
    const zoomMeetings = @json($zoomMeetings);
    const plannedTasks = @json($plannedTasks);

    // Translations
    const trans = {
        course_session: '{{ __('messages.course_session') }}',
        zoom_meeting: '{{ __('messages.zoom_meeting') }}',
        scheduled_task: '{{ __('messages.scheduled_task') }}',
        course: '{{ __('messages.course') }}',
        description: '{{ __('messages.description') }}',
        date: '{{ __('messages.date') }}',
        time: '{{ __('messages.time') }}',
        instructor: '{{ __('messages.instructor') }}',
        location: '{{ __('messages.location') }}',
        join_meeting: '{{ __('messages.join_meeting') }}',
        meeting_id: '{{ __('messages.meeting_id') }}',
        notes: '{{ __('messages.notes') }}',
        agenda: '{{ __('messages.agenda') }}',
        duration: '{{ __('messages.duration') }}',
        minutes: '{{ __('messages.minutes') }}',
        moderator: '{{ __('messages.moderator') }}',
        password: '{{ __('messages.password') }}',
        status: '{{ __('messages.status') }}',
        priority: '{{ __('messages.priority') }}',
        no_upcoming_events: '{{ __('messages.no_upcoming_events') }}'
    };

    // Prepare events for calendar
    const events = [];


    // Add course sessions
    courseSessions.forEach(session => {
        // Handle datetime fields - they might be full datetime strings
        let startDateTime, endDateTime;

        // Check if session_date contains full datetime or just date
        if (session.session_date && session.start_time) {
            // If start_time is a full datetime, use it directly
            if (session.start_time.includes('T') || session.start_time.includes(' ')) {
                startDateTime = session.start_time.replace(' ', 'T');
                endDateTime = session.end_time.replace(' ', 'T');
            } else {
                // Otherwise combine date and time
                startDateTime = session.session_date + 'T' + session.start_time;
                endDateTime = session.session_date + 'T' + session.end_time;
            }
        } else {
            return;
        }

        const event = {
            id: 'session-' + session.id,
            title: '📚 ' + session.title,
            start: startDateTime,
            end: endDateTime,
            backgroundColor: '#0071ae',
            borderColor: '#0071ae',
            classNames: ['course-event'],
            extendedProps: {
                type: 'course',
                description: session.description,
                instructor: session.instructor_name,
                location: session.location,
                course: session.product?.titre,
                notes: session.notes,
                sessionType: session.session_type,
                zoomUrl: session.zoom_join_url,
                zoomId: session.zoom_meeting_id
            }
        };
        events.push(event);
    });

    // Add zoom meetings
    zoomMeetings.forEach(meeting => {
        const startTime = new Date(meeting.start_time);
        const endTime = new Date(startTime.getTime() + meeting.duration * 60000);

        const event = {
            id: 'zoom-' + meeting.id,
            title: '🎥 ' + meeting.topic,
            start: meeting.start_time,
            end: endTime.toISOString(),
            backgroundColor: '#28a745',
            borderColor: '#28a745',
            classNames: ['zoom-event'],
            extendedProps: {
                type: 'zoom',
                agenda: meeting.agenda,
                joinUrl: meeting.join_url,
                duration: meeting.duration,
                course: meeting.product?.titre,
                moderator: meeting.moderator_email
            }
        };
        events.push(event);
    });

    // Add planned tasks/events created from admin calendar
    plannedTasks.forEach(task => {
        if (!task.date_time) {
            return;
        }

        const startDateTime = String(task.date_time).replace(' ', 'T');

        events.push({
            id: 'task-' + task.id,
            title: '🗓️ ' + (task.message || trans.scheduled_task),
            start: startDateTime,
            backgroundColor: '#f59e0b',
            borderColor: '#f59e0b',
            classNames: ['task-event'],
            extendedProps: {
                type: 'task',
                description: task.message,
                course: task.course_variation_name || task.course_title,
                status: task.status,
                priority: task.priority,
                notes: task.notes
            }
        });
    });

    // Initialize calendar
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: false,
        events: events,
        eventClick: function(info) {
            showEventDetails(info.event);
        },
        eventDisplay: 'block',
        displayEventTime: true,
        displayEventEnd: false,
        eventTimeFormat: {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        },
        height: 'auto',
        firstDay: 1
    });

    calendar.render();
    updateMonthDisplay();

    // Navigation buttons
    document.getElementById('prevMonth').addEventListener('click', function() {
        calendar.prev();
        updateMonthDisplay();
    });

    document.getElementById('nextMonth').addEventListener('click', function() {
        calendar.next();
        updateMonthDisplay();
    });

    document.getElementById('todayBtn').addEventListener('click', function() {
        calendar.today();
        updateMonthDisplay();
    });

    function updateMonthDisplay() {
        const date = calendar.getDate();
        const monthNames = ["January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];
        document.getElementById('currentMonth').textContent =
            monthNames[date.getMonth()] + ' ' + date.getFullYear();
    }

    // View toggle
    document.getElementById('monthViewBtn').addEventListener('click', function() {
        document.getElementById('calendarView').classList.remove('d-none');
        document.getElementById('listView').classList.add('d-none');
        this.style.backgroundColor = '#0071ae';
        this.style.color = 'white';
        this.classList.remove('btn-outline-secondary');
        document.getElementById('listViewBtn').style.backgroundColor = '';
        document.getElementById('listViewBtn').style.color = '';
        document.getElementById('listViewBtn').classList.add('btn-outline-secondary');
    });

    document.getElementById('listViewBtn').addEventListener('click', function() {
        document.getElementById('calendarView').classList.add('d-none');
        document.getElementById('listView').classList.remove('d-none');
        this.style.backgroundColor = '#0071ae';
        this.style.color = 'white';
        this.classList.remove('btn-outline-secondary');
        document.getElementById('monthViewBtn').style.backgroundColor = '';
        document.getElementById('monthViewBtn').style.color = '';
        document.getElementById('monthViewBtn').classList.add('btn-outline-secondary');
        renderListView();
    });

    // Show event details
    function showEventDetails(event) {
        const modal = new bootstrap.Modal(document.getElementById('eventModal'));
        const props = event.extendedProps;

        document.getElementById('eventModalTitle').textContent = event.title;

        let bodyHTML = '<div class="event-details">';

        if (props.type === 'course') {
            bodyHTML += `
                <div class="mb-3">
                    <span class="badge" style="background-color: #0071ae;">${trans.course_session}</span>
                </div>
                ${props.course ? `<p><i class="bi bi-book me-2" style="color: #0071ae;"></i><strong>${trans.course}:</strong> ${props.course}</p>` : ''}
                ${props.description ? `<p><i class="bi bi-info-circle me-2" style="color: #0071ae;"></i><strong>${trans.description}:</strong> ${props.description}</p>` : ''}
                <p><i class="bi bi-calendar3 me-2" style="color: #0071ae;"></i><strong>${trans.date}:</strong> ${new Date(event.start).toLocaleDateString()}</p>
                <p><i class="bi bi-clock me-2" style="color: #0071ae;"></i><strong>${trans.time}:</strong> ${new Date(event.start).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})} - ${new Date(event.end).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</p>
                ${props.instructor ? `<p><i class="bi bi-person me-2" style="color: #0071ae;"></i><strong>${trans.instructor}:</strong> ${props.instructor}</p>` : ''}
                ${props.location ? `<p><i class="bi bi-geo-alt me-2" style="color: #0071ae;"></i><strong>${trans.location}:</strong> ${props.location}</p>` : ''}
                ${props.sessionType === 'online' && props.zoomUrl ? `
                    <div class="mt-3">
                        <a href="${props.zoomUrl}" target="_blank" class="btn btn-success">
                            <i class="bi bi-camera-video me-2"></i>${trans.join_meeting}
                        </a>
                        ${props.zoomId ? `<p class="mt-2 mb-0"><small>${trans.meeting_id}: <code>${props.zoomId}</code></small></p>` : ''}
                    </div>
                ` : ''}
                ${props.notes ? `<div class="alert alert-info mt-3"><strong><i class="bi bi-info-circle me-2"></i>${trans.notes}:</strong><p class="mb-0 mt-1">${props.notes}</p></div>` : ''}
            `;
        } else if (props.type === 'zoom') {
            bodyHTML += `
                <div class="mb-3">
                    <span class="badge bg-success">${trans.zoom_meeting}</span>
                </div>
                ${props.course ? `<p><i class="bi bi-book me-2 text-success"></i><strong>${trans.course}:</strong> ${props.course}</p>` : ''}
                ${props.agenda ? `<p><i class="bi bi-list-task me-2 text-success"></i><strong>${trans.agenda}:</strong> ${props.agenda}</p>` : ''}
                <p><i class="bi bi-calendar3 me-2 text-success"></i><strong>${trans.date}:</strong> ${new Date(event.start).toLocaleDateString()}</p>
                <p><i class="bi bi-clock me-2 text-success"></i><strong>${trans.time}:</strong> ${new Date(event.start).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</p>
                <p><i class="bi bi-hourglass-split me-2 text-success"></i><strong>${trans.duration}:</strong> ${props.duration} ${trans.minutes}</p>
                ${props.moderator ? `<p><i class="bi bi-person-badge me-2 text-success"></i><strong>${trans.moderator}:</strong> ${props.moderator}</p>` : ''}
                <div class="mt-3">
                    <a href="${props.joinUrl}" target="_blank" class="btn btn-success">
                        <i class="bi bi-camera-video me-2"></i>${trans.join_meeting}
                    </a>
                </div>
            `;
        } else {
            bodyHTML += `
                <div class="mb-3">
                    <span class="badge" style="background-color: #f59e0b;">${trans.scheduled_task}</span>
                </div>
                ${props.course ? `<p><i class="bi bi-book me-2" style="color: #f59e0b;"></i><strong>${trans.course}:</strong> ${props.course}</p>` : ''}
                ${props.description ? `<p><i class="bi bi-info-circle me-2" style="color: #f59e0b;"></i><strong>${trans.description}:</strong> ${props.description}</p>` : ''}
                <p><i class="bi bi-calendar3 me-2" style="color: #f59e0b;"></i><strong>${trans.date}:</strong> ${new Date(event.start).toLocaleDateString()}</p>
                <p><i class="bi bi-clock me-2" style="color: #f59e0b;"></i><strong>${trans.time}:</strong> ${new Date(event.start).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</p>
                ${props.status ? `<p><i class="bi bi-flag me-2" style="color: #f59e0b;"></i><strong>${trans.status}:</strong> ${props.status}</p>` : ''}
                ${props.priority ? `<p><i class="bi bi-exclamation-triangle me-2" style="color: #f59e0b;"></i><strong>${trans.priority}:</strong> ${props.priority}</p>` : ''}
                ${props.notes ? `<div class="alert alert-warning mt-3"><strong><i class="bi bi-journal-text me-2"></i>${trans.notes}:</strong><p class="mb-0 mt-1">${props.notes}</p></div>` : ''}
            `;
        }

        bodyHTML += '</div>';

        document.getElementById('eventModalBody').innerHTML = bodyHTML;
        modal.show();
    }

    // Render list view
    function renderListView() {
        const now = new Date();
        const upcomingEvents = events
            .filter(e => new Date(e.start) >= now)
            .sort((a, b) => new Date(a.start) - new Date(b.start));

        const listHTML = upcomingEvents.length > 0
            ? upcomingEvents.map(event => {
                const startDate = new Date(event.start);
                const isZoom = event.extendedProps.type === 'zoom';
                const isTask = event.extendedProps.type === 'task';
                return `
                    <div class="event-list-item ${isZoom ? 'zoom' : ''}" style="${isTask ? 'border-left-color:#f59e0b;background-color:rgba(245,158,11,0.08);' : ''}" onclick='showEventFromList(${JSON.stringify(event)})'>
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">${event.title}</h6>
                                <p class="mb-1 text-muted">
                                    <i class="bi bi-calendar3 me-1"></i>${startDate.toLocaleDateString()}
                                    <span class="mx-2">•</span>
                                    <i class="bi bi-clock me-1"></i>${startDate.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}
                                </p>
                                ${event.extendedProps.course ? `<p class="mb-0 text-muted"><small><i class="bi bi-book me-1"></i>${event.extendedProps.course}</small></p>` : ''}
                            </div>
                            <span class="badge ${isZoom ? 'bg-success' : ''}" style="${isTask ? 'background-color:#f59e0b;' : (!isZoom ? 'background-color: #0071ae;' : '')}">
                                <i class="bi bi-${isZoom ? 'camera-video' : (isTask ? 'list-task' : 'calendar-event')}"></i>
                            </span>
                        </div>
                    </div>
                `;
            }).join('')
            : `<div class="text-center py-5 text-muted">${trans.no_upcoming_events}</div>`;

        document.getElementById('upcomingEventsList').innerHTML = listHTML;
    }

    window.showEventFromList = function(eventData) {
        // Reconstruct event object for modal
        const event = {
            title: eventData.title,
            start: eventData.start,
            end: eventData.end,
            extendedProps: eventData.extendedProps
        };
        showEventDetails(event);
    };
});
</script>
@endpush
