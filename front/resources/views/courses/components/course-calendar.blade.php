<div class="course-calendar-widget">
  <div class="calendar-header mb-30">
    <h3 class="text-24 fw-600 text-dark-1 mb-15">
      <i class="icon-calendar mr-10"></i>
      {{ __('messages.course_schedule') }} & {{ __('messages.zoom_meetings') }}
    </h3>
    <p class="text-15 text-light-1">
      View all your sessions and meetings for this course
    </p>
  </div>

  <div class="calendar-container bg-white rounded-16 shadow-2 border-light p-30">
    <div id="courseCalendar"></div>

    <!-- Legend -->
    <div class="calendar-legend d-flex flex-wrap mt-20" style="gap: 20px;">
      <div class="d-flex items-center">
        <span class="d-inline-block rounded" style="background-color: #0071ae; width: 16px; height: 16px; margin-right: 8px;"></span>
        <small>{{ __('messages.course_sessions') }}</small>
      </div>
      <div class="d-flex items-center">
        <span class="d-inline-block rounded" style="background-color: #28a745; width: 16px; height: 16px; margin-right: 8px;"></span>
        <small>{{ __('messages.zoom_meetings') }}</small>
      </div>
    </div>
  </div>
</div>

<!-- Event Details Modal -->
<div class="modal fade" id="courseEventModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #0071ae; color: white;">
        <h5 class="modal-title" id="courseEventModalTitle"></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="courseEventModalBody"></div>
    </div>
  </div>
</div>

<style>
#courseCalendar {
  min-height: 650px;
  height: 650px;
}

.course-calendar-widget {
  margin-bottom: 40px;
}

.calendar-container {
  width: 100%;
  overflow: visible;
  background: #ffffff;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0, 113, 174, 0.08);
  border: 1px solid rgba(0, 113, 174, 0.1);
}

.calendar-header h3 {
  color: #0071ae;
  display: flex;
  align-items: center;
  gap: 10px;
}

.calendar-header p {
  color: #6b7280;
}

/* FullCalendar Event Styling */
.fc-event {
  cursor: pointer;
  border: none !important;
  padding: 6px 10px;
  margin: 2px 0;
  border-radius: 6px;
  font-size: 0.875rem;
  font-weight: 500;
  transition: all 0.2s ease;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.fc-event:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 113, 174, 0.25);
}

/* Header Styling */
.fc .fc-toolbar {
  padding: 20px;
  background: linear-gradient(135deg, #0071ae 0%, #005a8d 100%);
  border-radius: 16px 16px 0 0;
  margin-bottom: 0 !important;
}

.fc .fc-toolbar-title {
  color: #ffffff !important;
  font-size: 1.5rem !important;
  font-weight: 700 !important;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.fc .fc-button {
  background-color: rgba(255, 255, 255, 0.15) !important;
  border: 1px solid rgba(255, 255, 255, 0.3) !important;
  color: #ffffff !important;
  padding: 8px 16px !important;
  border-radius: 8px !important;
  font-weight: 600 !important;
  transition: all 0.2s ease !important;
  text-transform: capitalize !important;
}

.fc .fc-button:hover {
  background-color: rgba(255, 255, 255, 0.25) !important;
  border-color: rgba(255, 255, 255, 0.5) !important;
  transform: translateY(-1px);
}

.fc .fc-button:active,
.fc .fc-button-active {
  background-color: rgba(255, 255, 255, 0.3) !important;
  border-color: rgba(255, 255, 255, 0.6) !important;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15) !important;
}

.fc .fc-button:disabled {
  opacity: 0.5 !important;
  cursor: not-allowed !important;
}

/* Day Number Styling */
.fc-daygrid-day-number {
  color: #0071ae !important;
  font-weight: 700 !important;
  font-size: 1rem !important;
  padding: 10px !important;
  transition: all 0.2s ease;
}

.fc-daygrid-day-frame:hover .fc-daygrid-day-number {
  color: #005a8d !important;
  transform: scale(1.1);
}

/* Column Header Styling */
.fc-col-header {
  background: linear-gradient(180deg, rgba(0, 113, 174, 0.08) 0%, rgba(0, 113, 174, 0.03) 100%);
}

.fc-col-header-cell {
  padding: 15px 0 !important;
  border-color: rgba(0, 113, 174, 0.1) !important;
}

.fc-col-header-cell-cushion {
  color: #0071ae !important;
  font-weight: 700 !important;
  text-transform: uppercase !important;
  font-size: 0.875rem !important;
  letter-spacing: 0.5px;
}

/* Today Highlighting */
.fc-day-today {
  background-color: rgba(252, 209, 22, 0.12) !important;
}

.fc-day-today .fc-daygrid-day-number {
  background: linear-gradient(135deg, #FCD116 0%, #f5c400 100%);
  color: #1f2937 !important;
  border-radius: 50%;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 3px 8px rgba(252, 209, 22, 0.4);
  font-weight: 800 !important;
}

/* Grid Styling */
.fc-daygrid-day {
  transition: background-color 0.2s ease;
}

.fc-daygrid-day:hover {
  background-color: rgba(0, 113, 174, 0.02);
}

.fc-scrollgrid {
  border-color: rgba(0, 113, 174, 0.1) !important;
  border-radius: 0 0 16px 16px;
  overflow: hidden;
}

.fc-theme-standard td,
.fc-theme-standard th {
  border-color: rgba(0, 113, 174, 0.1) !important;
}

/* Legend Styling */
.calendar-legend {
  padding: 15px 20px;
  background: rgba(0, 113, 174, 0.03);
  border-radius: 0 0 16px 16px;
  border-top: 2px solid rgba(0, 113, 174, 0.1);
}

.calendar-legend small {
  color: #4b5563;
  font-weight: 500;
  font-size: 0.875rem;
}

.calendar-legend .d-inline-block {
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* List View Styling */
.fc-list {
  border-color: rgba(0, 113, 174, 0.1) !important;
}

.fc-list-event:hover td {
  background-color: rgba(0, 113, 174, 0.05) !important;
}

.fc-list-event-dot {
  border-color: #0071ae !important;
}

.fc-list-day-cushion {
  background-color: rgba(0, 113, 174, 0.08) !important;
  color: #0071ae !important;
  font-weight: 600 !important;
}

/* Modal Styling */
#courseEventModal .modal-header {
  background: linear-gradient(135deg, #0071ae 0%, #005a8d 100%) !important;
  border-bottom: none;
  padding: 20px 25px;
}

#courseEventModal .modal-title {
  font-weight: 700;
  font-size: 1.25rem;
}

#courseEventModal .modal-body {
  padding: 25px;
}

#courseEventModal .btn {
  border-radius: 8px;
  padding: 10px 20px;
  font-weight: 600;
  transition: all 0.2s ease;
}

#courseEventModal .btn-success {
  background: linear-gradient(135deg, #28a745 0%, #20803a 100%);
  border: none;
}

#courseEventModal .btn-success:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
}
</style>

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const courseSessions = @json($courseSessions ?? []);
  const zoomMeetings = @json($zoomMeetings ?? []);

  const trans = {
    course_session: '{{ __('messages.course_session') }}',
    zoom_meeting: '{{ __('messages.zoom_meeting') }}',
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
    password: '{{ __('messages.password') }}'
  };

  const events = [];

  // Add course sessions
  courseSessions.forEach(session => {
    let startDateTime, endDateTime;

    if (session.session_date && session.start_time) {
      if (session.start_time.includes('T') || session.start_time.includes(' ')) {
        startDateTime = session.start_time.replace(' ', 'T');
        endDateTime = session.end_time.replace(' ', 'T');
      } else {
        startDateTime = session.session_date + 'T' + session.start_time;
        endDateTime = session.session_date + 'T' + session.end_time;
      }
    } else {
      return;
    }

    events.push({
      id: 'session-' + session.id,
      title: '📚 ' + session.title,
      start: startDateTime,
      end: endDateTime,
      backgroundColor: '#0071ae',
      borderColor: '#0071ae',
      extendedProps: {
        type: 'course',
        description: session.description,
        instructor: session.instructor_name,
        location: session.location,
        notes: session.notes,
        sessionType: session.session_type,
        zoomUrl: session.zoom_join_url,
        zoomId: session.zoom_meeting_id
      }
    });
  });

  // Add zoom meetings
  zoomMeetings.forEach(meeting => {
    const startTime = new Date(meeting.start_time);
    const endTime = new Date(startTime.getTime() + meeting.duration * 60000);

    events.push({
      id: 'zoom-' + meeting.id,
      title: '🎥 ' + meeting.topic,
      start: meeting.start_time,
      end: endTime.toISOString(),
      backgroundColor: '#28a745',
      borderColor: '#28a745',
      extendedProps: {
        type: 'zoom',
        agenda: meeting.agenda,
        joinUrl: meeting.join_url,
        duration: meeting.duration,
        moderator: meeting.moderator_email
      }
    });
  });


  const calendarEl = document.getElementById('courseCalendar');
  if (calendarEl) {
    const calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,listMonth'
      },
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
      height: 600,
      contentHeight: 550,
      firstDay: 1,
      buttonText: {
        today: 'Today',
        month: 'Month',
        list: 'List'
      }
    });

    calendar.render();

    // Re-render calendar when tab becomes visible
    const tabButton = document.querySelector('button[data-tab="course-schedule"]');
    if (tabButton) {
      tabButton.addEventListener('click', function () {
        setTimeout(() => {
          calendar.updateSize();
          calendar.render();
        }, 150);
      });
    }

    // Also check if the tab is already active on page load
    const courseScheduleTab = document.getElementById('course-schedule');
    if (courseScheduleTab && courseScheduleTab.classList.contains('active')) {
      setTimeout(() => {
        calendar.updateSize();
        calendar.render();
      }, 200);
    }
  }

  function showEventDetails(event) {
    const modal = new bootstrap.Modal(document.getElementById('courseEventModal'));
    const props = event.extendedProps;

    document.getElementById('courseEventModalTitle').textContent = event.title;

    let bodyHTML = '<div class="event-details">';

    if (props.type === 'course') {
      bodyHTML += `
        <div class="mb-3">
          <span class="badge" style="background-color: #0071ae;">${trans.course_session}</span>
        </div>
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
    } else {
      bodyHTML += `
        <div class="mb-3">
          <span class="badge bg-success">${trans.zoom_meeting}</span>
        </div>
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
    }

    bodyHTML += '</div>';

    document.getElementById('courseEventModalBody').innerHTML = bodyHTML;
    modal.show();
  }
});
</script>
