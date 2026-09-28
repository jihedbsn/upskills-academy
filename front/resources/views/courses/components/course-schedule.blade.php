@if(!auth()->check() || !isset($enrollment) || !$enrollment->is_active)
  <div class="alert alert-warning text-center py-40" style="background: linear-gradient(135deg, #fff3cd 0%, #fff8e1 100%); border: 2px solid #ffc107; border-radius: 16px;">
    <i class="icon-lock text-50 text-warning mb-20"></i>
    <h4 class="text-20 fw-700 mb-15">{{ __('messages.course_schedule_locked') }}</h4>
    <p class="text-15 mb-0">{{ __('messages.purchase_course_to_view_schedule') }}</p>
  </div>
@else
<div class="course-schedule-section">
  <!-- Calendar Widget - Always Show -->
  @if(isset($courseSessions) || isset($zoomMeetings))
    <div class="mb-50">
      @include('courses.components.course-calendar')
    </div>
  @endif

  @php
    $upcomingItems = collect();

    // Merge upcoming course sessions
    if(isset($upcomingSessions)) {
        foreach($upcomingSessions as $session) {
            $upcomingItems->push([
                'type' => 'session',
                'data' => $session,
                'datetime' => $session->session_date_time
            ]);
        }
    }

    // Merge upcoming zoom meetings
    if(isset($zoomMeetings)) {
        foreach($zoomMeetings as $meeting) {
            $startTime = \Carbon\Carbon::parse($meeting->start_time);
            if($startTime->isFuture() || $startTime->isToday()) {
                $upcomingItems->push([
                    'type' => 'zoom',
                    'data' => $meeting,
                    'datetime' => $meeting->start_time
                ]);
            }
        }
    }

    // Sort by datetime
    $upcomingItems = $upcomingItems->sortBy('datetime');
    $totalUpcoming = $upcomingItems->count();
  @endphp

  @if($totalUpcoming > 0 || (isset($courseSessions) && $courseSessions->count() > 0))
    <!-- Unified Schedule Header -->
    <div class="schedule-header mb-40" style="background: #0071ae; padding: 30px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0, 113, 174, 0.15);">
      <h3 class="text-24 fw-700 mb-10" style="color: #ffffff;">
        <i class="icon-calendar mr-10"></i>
        {{ __('messages.course_schedule') }}
      </h3>
      <p class="text-15 mb-0" style="color: rgba(255, 255, 255, 0.9);">
        {{ __('messages.all_scheduled_sessions_for_course') }}
      </p>
    </div>

    <!-- Upcoming Events (Sessions & Zoom Meetings) -->
    @if($totalUpcoming > 0)
    <div class="schedule-section mb-40">
      <div class="section-header d-flex items-center justify-between mb-30" style="padding: 20px; background: rgba(0, 113, 174, 0.05); border-left: 4px solid #0071ae; border-radius: 8px;">
        <h4 class="text-20 fw-700 mb-0" style="color: #0071ae;">
          <i class="icon-clock-2 mr-10"></i>
          {{ __('messages.upcoming_events') ?? 'Upcoming Events' }}
        </h4>
        <span class="badge text-white px-20 py-10" style="background: #0071ae; border-radius: 20px; box-shadow: 0 2px 8px rgba(0, 113, 174, 0.3); font-size: 14px; font-weight: 600;">
          {{ $totalUpcoming }} {{ __('messages.events') ?? 'Events' }}
        </span>
      </div>

      <div class="events-grid row y-gap-30">
        @foreach($upcomingItems as $item)
          @if($item['type'] === 'session')
            @php $session = $item['data']; @endphp
            <div class="col-12">
              <div class="event-card bg-white rounded-16" style="border: 2px solid rgba(0, 113, 174, 0.1); box-shadow: 0 4px 20px rgba(0, 113, 174, 0.08); transition: all 0.3s ease; overflow: hidden;">
                <div class="row g-0">
                  <!-- Left Side: Event Info -->
                  <div class="col-lg-8" style="padding: 30px;">
                    <!-- Event Type Badge & Title -->
                    <div class="d-flex items-center mb-15" style="gap: 12px; flex-wrap: wrap;">
                      <span class="badge text-white px-12 py-6" style="background: #0071ae; border-radius: 6px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap;">
                        <i class="icon-book mr-5"></i>{{ __('messages.session') ?? 'Session' }}
                      </span>
                      <span class="badge px-12 py-6" style="background: rgba(0, 113, 174, 0.1); color: #0071ae; border-radius: 6px; font-size: 11px; font-weight: 600; white-space: nowrap;">
                        {{ $session->getTypeLabel() }}
                      </span>
                      @if($session->isToday())
                      <span class="badge text-white px-12 py-6" style="background: linear-gradient(135deg, #FCD116 0%, #d4a300 100%); border-radius: 6px; font-size: 11px; font-weight: 600; box-shadow: 0 2px 8px rgba(252, 209, 22, 0.3); white-space: nowrap;">
                        <i class="icon-flash mr-5"></i>{{ __('messages.today') }}
                      </span>
                      @endif
                    </div>

                    <h5 class="text-20 fw-700 mb-15" style="color: #1f2937;">{{ $session->title }}</h5>

                    @if($session->description)
                    <p class="text-15 mb-20" style="color: #6b7280; line-height: 1.6;">{{ $session->description }}</p>
                    @endif

                    <!-- Date & Time -->
                    <div class="d-flex flex-wrap items-center mb-20" style="gap: 12px;">
                      <div class="d-flex items-center" style="background: rgba(0, 113, 174, 0.05); padding: 8px 12px; border-radius: 8px;">
                        <i class="icon-calendar text-16 mr-8" style="color: #0071ae;"></i>
                        <span class="text-14 fw-600" style="color: #1f2937;">{{ $session->formatted_date }}</span>
                      </div>
                      <div class="d-flex items-center" style="background: rgba(0, 113, 174, 0.05); padding: 8px 12px; border-radius: 8px;">
                        <i class="icon-clock text-16 mr-8" style="color: #0071ae;"></i>
                        <span class="text-14 fw-600" style="color: #1f2937;">{{ $session->formatted_time }}</span>
                      </div>
                    </div>

                    @if($session->instructor_name)
                    <div class="d-flex items-center" style="background: rgba(252, 209, 22, 0.08); padding: 10px 12px; border-radius: 8px; border-left: 3px solid #FCD116;">
                      <i class="icon-person text-16 mr-8" style="color: #0071ae;"></i>
                      <span class="text-14" style="color: #6b7280;">{{ __('messages.instructor') }}:
                        <span class="fw-600" style="color: #1f2937;">{{ $session->instructor_name }}</span>
                      </span>
                    </div>
                    @endif
                  </div>

                  <!-- Right Side: Location/Join -->
                  <div class="col-lg-4" style="background: rgba(0, 113, 174, 0.02); border-left: 2px solid rgba(0, 113, 174, 0.1); padding: 30px;">
                    <div class="h-100 d-flex flex-column justify-center">
                      @if($session->session_type === 'online' && $session->zoom_join_url)
                        <div class="text-center" style="background: linear-gradient(135deg, rgba(0, 113, 174, 0.08) 0%, rgba(0, 113, 174, 0.03) 100%); padding: 20px; border-radius: 12px; border: 2px solid rgba(0, 113, 174, 0.15);">
                          <div class="mb-15">
                            <div style="width: 50px; height: 50px; margin: 0 auto; background: #0071ae; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(0, 113, 174, 0.3);">
                              <i class="icon-calendar text-24 text-white"></i>
                            </div>
                          </div>
                          <h6 class="text-15 fw-700 mb-15" style="color: #1f2937;">{{ __('messages.online_session') }}</h6>
                          <a href="{{ $session->zoom_join_url }}"
                             target="_blank"
                             class="button -sm text-white w-100"
                             style="background: #0071ae; border: none; padding: 10px 16px; border-radius: 8px; font-weight: 600; font-size: 14px; box-shadow: 0 4px 12px rgba(0, 113, 174, 0.3); transition: all 0.3s ease; display: inline-block; text-decoration: none;">
                            <i class="icon-video-camera mr-6"></i>{{ __('messages.join_session') ?? 'Join Session' }}
                          </a>
                        </div>
                      @elseif($session->location)
                        <div class="text-center" style="background: rgba(252, 209, 22, 0.08); padding: 20px; border-radius: 12px; border: 2px solid rgba(252, 209, 22, 0.2);">
                          <div class="mb-15">
                            <div style="width: 50px; height: 50px; margin: 0 auto; background: linear-gradient(135deg, #FCD116 0%, #d4a300 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(252, 209, 22, 0.3);">
                              <i class="icon-location text-24 text-white"></i>
                            </div>
                          </div>
                          <h6 class="text-15 fw-700 mb-10" style="color: #1f2937;">{{ __('messages.in_person') }}</h6>
                          <p class="text-13 mb-0" style="color: #6b7280; line-height: 1.5;">{{ $session->location }}</p>
                        </div>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
            </div>

          @elseif($item['type'] === 'zoom')
            @php
              $meeting = $item['data'];
              $startTime = \Carbon\Carbon::parse($meeting->start_time);
              $isToday = $startTime->isToday();
            @endphp
            <div class="col-12">
              <div class="event-card bg-white rounded-16" style="border: 2px solid rgba(0, 113, 174, 0.1); box-shadow: 0 4px 20px rgba(0, 113, 174, 0.08); transition: all 0.3s ease; overflow: hidden;">
                <div class="row g-0">
                  <!-- Left Side: Meeting Info -->
                  <div class="col-lg-8" style="padding: 30px;">
                    <!-- Meeting Type Badge & Title -->
                    <div class="d-flex items-center mb-15" style="gap: 12px; flex-wrap: wrap;">
                      <span class="badge text-white px-12 py-6" style="background: #0071ae; border-radius: 6px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap;">
                        <i class="icon-video-camera mr-5"></i>{{ __('messages.zoom_meeting') ?? 'Zoom Meeting' }}
                      </span>
                      @if($isToday)
                      <span class="badge text-white px-12 py-6" style="background: linear-gradient(135deg, #FCD116 0%, #d4a300 100%); border-radius: 6px; font-size: 11px; font-weight: 600; box-shadow: 0 2px 8px rgba(252, 209, 22, 0.3); white-space: nowrap;">
                        <i class="icon-flash mr-5"></i>{{ __('messages.today') }}
                      </span>
                      @endif
                    </div>

                    <h5 class="text-20 fw-700 mb-15" style="color: #1f2937;">{{ $meeting->topic }}</h5>

                    @if($meeting->agenda)
                    <p class="text-15 mb-20" style="color: #6b7280; line-height: 1.6;">{{ $meeting->agenda }}</p>
                    @endif

                    <!-- Date, Time & Duration -->
                    <div class="d-flex flex-wrap items-center mb-20" style="gap: 12px;">
                      <div class="d-flex items-center" style="background: rgba(0, 113, 174, 0.05); padding: 8px 12px; border-radius: 8px;">
                        <i class="icon-calendar text-16 mr-8" style="color: #0071ae;"></i>
                        <span class="text-14 fw-600" style="color: #1f2937;">{{ $startTime->format('F d, Y') }}</span>
                      </div>
                      <div class="d-flex items-center" style="background: rgba(0, 113, 174, 0.05); padding: 8px 12px; border-radius: 8px;">
                        <i class="icon-clock text-16 mr-8" style="color: #0071ae;"></i>
                        <span class="text-14 fw-600" style="color: #1f2937;">{{ $startTime->format('g:i A') }}</span>
                      </div>
                      <div class="d-flex items-center" style="background: rgba(0, 113, 174, 0.05); padding: 8px 12px; border-radius: 8px;">
                        <i class="icon-hourglass-split text-16 mr-8" style="color: #0071ae;"></i>
                        <span class="text-14 fw-600" style="color: #1f2937;">{{ $meeting->duration }} {{ __('messages.minutes') }}</span>
                      </div>
                    </div>

                    @if($meeting->moderator_email)
                    <div class="d-flex items-center" style="background: rgba(252, 209, 22, 0.08); padding: 10px 12px; border-radius: 8px; border-left: 3px solid #FCD116;">
                      <i class="icon-person text-16 mr-8" style="color: #0071ae;"></i>
                      <span class="text-14" style="color: #6b7280;">{{ __('messages.moderator') }}:
                        <span class="fw-600" style="color: #1f2937;">{{ $meeting->moderator_email }}</span>
                      </span>
                    </div>
                    @endif
                  </div>

                  <!-- Right Side: Join Meeting -->
                  <div class="col-lg-4" style="background: rgba(0, 113, 174, 0.02); border-left: 2px solid rgba(0, 113, 174, 0.1); padding: 30px;">
                    <div class="h-100 d-flex flex-column justify-center">
                      <div class="text-center" style="background: linear-gradient(135deg, rgba(0, 113, 174, 0.08) 0%, rgba(0, 113, 174, 0.03) 100%); padding: 20px; border-radius: 12px; border: 2px solid rgba(0, 113, 174, 0.15);">
                        <div class="mb-15">
                          <div style="width: 50px; height: 50px; margin: 0 auto; background: #0071ae; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(0, 113, 174, 0.3);">
                            <i class="icon-video-camera text-24 text-white"></i>
                          </div>
                        </div>
                        <h6 class="text-15 fw-700 mb-15" style="color: #1f2937;">{{ __('messages.join_zoom_meeting') }}</h6>
                        <a href="{{ $meeting->join_url }}"
                           target="_blank"
                           class="button -sm text-white w-100"
                           style="background: #0071ae; border: none; padding: 10px 16px; border-radius: 8px; font-weight: 600; font-size: 14px; box-shadow: 0 4px 12px rgba(0, 113, 174, 0.3); transition: all 0.3s ease; display: inline-block; text-decoration: none;">
                          <i class="icon-video-camera mr-6"></i>{{ __('messages.join_meeting') }}
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          @endif
        @endforeach
      </div>
    </div>
    @endif

    <!-- Past Sessions -->
    @if(isset($pastSessions) && $pastSessions->count() > 0)
    <div class="schedule-section">
      <div class="section-header d-flex items-center justify-between mb-25">
        <h4 class="text-20 fw-600 text-dark-1 mb-0">
          {{ __('messages.past_sessions') }}
        </h4>
        <span class="badge bg-secondary text-white px-20 py-8">
          {{ $pastSessions->count() }} {{ __('messages.sessions') }}
        </span>
      </div>

      <div class="sessions-list">
        @foreach($pastSessions as $session)
        <div class="past-session-item bg-light-6 rounded-12 p-20 mb-15">
          <div class="row">
            <div class="col-lg-8">
              <!-- Title & Status -->
              <div class="d-flex items-center justify-between mb-10">
                <h6 class="text-16 fw-500 text-dark-1 mb-0">{{ $session->title }}</h6>
                <span class="badge bg-{{ $session->getStatusBadgeColor() }} text-white px-12 py-5">
                  {{ $session->getStatusLabel() }}
                </span>
              </div>

              <!-- Date & Time -->
              <div class="d-flex flex-wrap items-center" style="gap: 15px;">
                <div class="d-flex items-center">
                  <i class="icon-calendar text-14 text-light-1 mr-6"></i>
                  <span class="text-13 text-light-1">{{ $session->formatted_date }}</span>
                </div>
                <div class="d-flex items-center">
                  <i class="icon-clock text-14 text-light-1 mr-6"></i>
                  <span class="text-13 text-light-1">{{ $session->formatted_time }}</span>
                </div>
                <span class="badge badge-sm bg-{{ $session->getTypeBadgeColor() }} text-white">
                  {{ $session->getTypeLabel() }}
                </span>
              </div>
            </div>

            <div class="col-lg-4">
              @if($session->instructor_name)
              <div class="d-flex items-center justify-end">
                <i class="icon-person text-14 text-light-1 mr-6"></i>
                <span class="text-13 text-light-1">{{ $session->instructor_name }}</span>
              </div>
              @endif
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
    @endif
  @endif

  @if((!isset($courseSessions) || $courseSessions->count() == 0) && (!isset($zoomMeetings) || $zoomMeetings->count() == 0))
    <!-- No Sessions Message -->
    <div class="no-sessions-message text-center py-60">
      <div class="mb-20">
        <i class="icon-calendar text-80 text-light-3"></i>
      </div>
      <h4 class="text-20 fw-600 text-dark-1 mb-10">{{ __('messages.no_sessions_scheduled') }}</h4>
      <p class="text-15 text-light-1 mb-0">
        {{ __('messages.no_sessions_available_for_course') }}
      </p>
    </div>
  @endif
</div>
@endif

<style>
/* Course Schedule Styles */
.course-schedule-section {
  padding: 30px 0;
}

.session-card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.session-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
}

.past-session-item {
  transition: background-color 0.3s ease;
}

.past-session-item:hover {
  background-color: #e9ecef;
}

.border-left-light {
  border-left: 1px solid #e5e7eb;
}

/* Session Card Hover Effects */
.session-card,
.zoom-meeting-card {
  transition: all 0.3s ease;
}

.session-card:hover,
.zoom-meeting-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 30px rgba(0, 113, 174, 0.15) !important;
  border-color: rgba(0, 113, 174, 0.3) !important;
}

.zoom-meeting-card:hover {
  box-shadow: 0 8px 30px rgba(40, 167, 69, 0.15) !important;
  border-color: rgba(40, 167, 69, 0.3) !important;
}

/* Button Hover Effects */
.session-card a.button:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4) !important;
}

/* Responsive Styles */
@media (max-width: 991px) {
  .col-lg-4 {
    border-left: none !important;
    border-top: 2px solid rgba(0, 113, 174, 0.1) !important;
    background: rgba(0, 113, 174, 0.02) !important;
  }
}

.badge-sm {
  font-size: 11px;
  padding: 3px 8px;
}

.no-sessions-message {
  background: #f8f9fa;
  border-radius: 16px;
}
</style>
