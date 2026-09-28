@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
  <!-- Statistiques -->
  <div class="stats-grid">
    <div class="stat-card position-relative">
      <div class="stat-card__icon" style="background: var(--primary-gradient);">
        <i class="icon-play-button"></i>
      </div>
      <div class="stat-card__number">{{ $stats['total_courses'] ?? 12 }}</div>
      <div class="stat-card__label">{{ __('messages.total_courses') }}</div>
    </div>

    <div class="stat-card position-relative">
      <div class="stat-card__icon" style="background: var(--accent-color);">
        <i class="icon-award"></i>
      </div>
      <div class="stat-card__number">{{ $stats['completed_courses'] ?? 8 }}</div>
      <div class="stat-card__label">{{ __('messages.completed_courses') }}</div>
    </div>

    <div class="stat-card position-relative">
      <div class="stat-card__icon" style="background: var(--warning-color);">
        <i class="icon-award"></i>
      </div>
      <div class="stat-card__number">{{ $stats['total_certificates'] ?? 5 }}</div>
      <div class="stat-card__label">{{ __('messages.total_certificates') }}</div>
    </div>

    <div class="stat-card position-relative">
      <div class="stat-card__icon" style="background: var(--danger-color);">
        <i class="icon-bookmark"></i>
      </div>
      <div class="stat-card__number">{{ $stats['total_books'] ?? 15 }}</div>
      <div class="stat-card__label">{{ __('messages.total_books') }}</div>
    </div>
  </div>

  <!-- Cartes principales -->
  <div class="row y-gap-30">
    <!-- Activité récente -->
    <div class="col-lg-8">
      <div class="dashboard-card">
        <div class="dashboard-card__header">
          <h4 class="dashboard-card__title">{{ __('messages.recent_activity') }}</h4>
          <a href="#" class="btn-modern btn-secondary">{{ __('messages.view_all') }}</a>
        </div>

        <div class="activity-list">
          <div class="activity-item d-flex items-center py-15 border-bottom-light">
            <div class="activity-icon bg-purple-1 rounded-full size-40 d-flex items-center justify-center mr-15">
              <i class="icon-play-button text-white text-14"></i>
            </div>
            <div class="flex-grow-1">
              <div class="text-15 fw-500 text-dark-1">{{ __('messages.course_started') }}</div>
              <div class="text-14 text-light-1">{{ __('messages.complete_python_bootcamp') }}</div>
            </div>
            <div class="text-12 text-light-1">{{ __('messages.2_hours_ago') }}</div>
          </div>

          <div class="activity-item d-flex items-center py-15 border-bottom-light">
            <div class="activity-icon bg-green-1 rounded-full size-40 d-flex items-center justify-center mr-15">
              <i class="icon-award text-white text-14"></i>
            </div>
            <div class="flex-grow-1">
              <div class="text-15 fw-500 text-dark-1">{{ __('messages.certificate_earned') }}</div>
              <div class="text-14 text-light-1">{{ __('messages.sports_coaching_basic') }}</div>
            </div>
            <div class="text-12 text-light-1">{{ __('messages.1_day_ago') }}</div>
          </div>

          <div class="activity-item d-flex items-center py-15 border-bottom-light">
            <div class="activity-icon bg-blue-1 rounded-full size-40 d-flex items-center justify-center mr-15">
              <i class="icon-bookmark text-white text-14"></i>
            </div>
            <div class="flex-grow-1">
              <div class="text-15 fw-500 text-dark-1">{{ __('messages.book_added') }}</div>
              <div class="text-14 text-light-1">{{ __('messages.sports_nutrition_guide') }}</div>
            </div>
            <div class="text-12 text-light-1">{{ __('messages.3_days_ago') }}</div>
          </div>

          <div class="activity-item d-flex items-center py-15">
            <div class="activity-icon bg-orange-1 rounded-full size-40 d-flex items-center justify-center mr-15">
              <i class="icon-credit-card text-white text-14"></i>
            </div>
            <div class="flex-grow-1">
              <div class="text-15 fw-500 text-dark-1">{{ __('messages.payment_completed') }}</div>
              <div class="text-14 text-light-1">{{ __('messages.advanced_sports_psychology') }}</div>
            </div>
            <div class="text-12 text-light-1">{{ __('messages.1_week_ago') }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Actions rapides -->
    <div class="col-lg-4">
      <div class="dashboard-card">
        <div class="dashboard-card__header">
          <h4 class="dashboard-card__title">{{ __('messages.quick_actions') }}</h4>
        </div>

        <div class="quick-actions">
          <a href="{{ route('dashboard.courses') }}" class="btn-modern btn-primary w-100 mb-15">
            <i class="icon-play-button"></i>
            {{ __('messages.browse_courses') }}
          </a>

          <a href="{{ route('dashboard.books') }}" class="btn-modern btn-secondary w-100 mb-15">
            <i class="icon-bookmark"></i>
            {{ __('messages.access_books') }}
          </a>

          <a href="{{ route('dashboard.certificates') }}" class="btn-modern btn-success w-100 mb-15">
            <i class="icon-award"></i>
            {{ __('messages.view_certificates') }}
          </a>

          <a href="{{ route('dashboard.tickets') }}" class="btn-modern btn-warning w-100">
            <i class="icon-message"></i>
            {{ __('messages.get_support') }}
          </div>
        </div>
      </div>

      <!-- Upcoming Course Sessions -->
      @if(isset($upcomingCourseSessions) && $upcomingCourseSessions->count() > 0)
      <div class="dashboard-card mt-30">
        <div class="dashboard-card__header">
          <h4 class="dashboard-card__title">
            <i class="icon-calendar mr-10"></i>
            {{ __('messages.upcoming_sessions') }}
          </h4>
          <span class="badge bg-purple-1 text-white">{{ $upcomingCourseSessions->count() }}</span>
        </div>

        <div class="session-list">
          @foreach($upcomingCourseSessions as $session)
          <div class="session-item border-bottom-light py-15">
            <!-- Session Header -->
            <div class="d-flex justify-between items-center mb-10">
              <h6 class="text-15 fw-500 text-dark-1 mb-0">{{ $session->title }}</h6>
              <span class="badge badge-sm bg-{{ $session->getTypeBadgeColor() }} text-white">
                {{ $session->getTypeLabel() }}
              </span>
            </div>

            <!-- Course Name -->
            @if($session->product)
            <div class="text-13 text-dark-2 mb-8">
              <i class="icon-book-open mr-5"></i>
              {{ $session->product->titre }}
            </div>
            @endif

            <!-- Date & Time -->
            <div class="d-flex items-center mb-8">
              <i class="icon-calendar text-14 text-light-1 mr-8"></i>
              <span class="text-13 text-light-1">{{ $session->formatted_date }}</span>
              <span class="mx-10 text-light-1">•</span>
              <i class="icon-clock text-14 text-light-1 mr-8"></i>
              <span class="text-13 text-light-1">{{ $session->formatted_time }}</span>
            </div>

            <!-- Location/Zoom -->
            @if($session->session_type === 'online' && $session->zoom_join_url)
            <div class="d-flex items-center mb-10">
              <i class="icon-video-camera text-14 text-success-1 mr-8"></i>
              <a href="{{ $session->zoom_join_url }}" target="_blank"
                 class="text-13 text-purple-1 fw-500">
                {{ __('messages.join_zoom_meeting') }}
              </a>
            </div>
            @elseif($session->location)
            <div class="d-flex items-center mb-10">
              <i class="icon-location text-14 text-light-1 mr-8"></i>
              <span class="text-13 text-light-1">{{ $session->location }}</span>
            </div>
            @endif

            <!-- Instructor -->
            @if($session->instructor_name)
            <div class="d-flex items-center">
              <i class="icon-person text-14 text-light-1 mr-8"></i>
              <span class="text-13 text-light-1">{{ $session->instructor_name }}</span>
            </div>
            @endif
          </div>
          @endforeach
        </div>

        <!-- View All Link -->
        @if($upcomingCourseSessions->count() >= 5)
        <div class="pt-15 text-center">
          <a href="{{ route('dashboard.courses') }}" class="text-14 text-purple-1 fw-500">
            {{ __('messages.view_all_sessions') }} <i class="icon-arrow-right ml-5"></i>
          </a>
        </div>
        @endif
      </div>
      @endif

      <!-- Progression -->
      <div class="dashboard-card mt-30">
        <div class="dashboard-card__header">
          <h4 class="dashboard-card__title">{{ __('messages.progress_overview') }}</h4>
        </div>

        <div class="progress-stats">
          <div class="progress-item mb-20">
            <div class="d-flex justify-between items-center mb-10">
              <span class="text-14 fw-500">{{ __('messages.courses_completion') }}</span>
              <span class="text-14 text-light-1">67%</span>
            </div>
            <div class="progress-bar bg-light-3 rounded-full h-8">
              <div class="progress-fill bg-purple-1 rounded-full h-8" style="width: 67%"></div>
            </div>
          </div>

          <div class="progress-item mb-20">
            <div class="d-flex justify-between items-center mb-10">
              <span class="text-14 fw-500">{{ __('messages.certificates_earned') }}</span>
              <span class="text-14 text-light-1">42%</span>
            </div>
            <div class="progress-bar bg-light-3 rounded-full h-8">
              <div class="progress-fill bg-green-1 rounded-full h-8" style="width: 42%"></div>
            </div>
          </div>

          <div class="progress-item">
            <div class="d-flex justify-between items-center mb-10">
              <span class="text-14 fw-500">{{ __('messages.books_read') }}</span>
              <span class="text-14 text-light-1">80%</span>
            </div>
            <div class="progress-bar bg-light-3 rounded-full h-8">
              <div class="progress-fill bg-blue-1 rounded-full h-8" style="width: 80%"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Cours en cours -->
  <div class="dashboard-card mt-30">
    <div class="dashboard-card__header">
      <h4 class="dashboard-card__title">{{ __('messages.current_courses') }}</h4>
      <a href="{{ route('dashboard.courses') }}" class="btn-modern btn-secondary">{{ __('messages.view_all_courses') }}</a>
    </div>

    <div class="row y-gap-30">
      <div class="col-lg-4 col-md-6">
        <div class="course-card shadow-hover">
          <div class="course-card__image">
            <img src="{{ asset('assets/img/courses/1.jpg') }}" alt="Course" class="w-100 rounded-8">
            <div class="course-card__overlay">
              <div class="course-card__progress">
                <div class="progress-circle" data-progress="75">
                  <span class="progress-text">75%</span>
                </div>
              </div>
            </div>
          </div>
          <div class="course-card__content p-20">
            <h5 class="course-card__title text-16 fw-500 mb-10">{{ __('messages.sports_coaching_advanced') }}</h5>
            <p class="course-card__description text-14 text-light-1 mb-15">{{ __('messages.advanced_coaching_techniques') }}</p>
            <div class="course-card__meta d-flex justify-between items-center">
              <span class="text-12 text-light-1">{{ __('messages.12_lessons') }}</span>
              <a href="#" class="btn-modern btn-primary btn-sm">{{ __('messages.continue') }}</a>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4 col-md-6">
        <div class="course-card shadow-hover">
          <div class="course-card__image">
            <img src="{{ asset('assets/img/courses/2.jpg') }}" alt="Course" class="w-100 rounded-8">
            <div class="course-card__overlay">
              <div class="course-card__progress">
                <div class="progress-circle" data-progress="45">
                  <span class="progress-text">45%</span>
                </div>
              </div>
            </div>
          </div>
          <div class="course-card__content p-20">
            <h5 class="course-card__title text-16 fw-500 mb-10">{{ __('messages.sports_nutrition') }}</h5>
            <p class="course-card__description text-14 text-light-1 mb-15">{{ __('messages.nutrition_for_athletes') }}</p>
            <div class="course-card__meta d-flex justify-between items-center">
              <span class="text-12 text-light-1">{{ __('messages.8_lessons') }}</span>
              <a href="#" class="btn-modern btn-primary btn-sm">{{ __('messages.continue') }}</a>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4 col-md-6">
        <div class="course-card shadow-hover">
          <div class="course-card__image">
            <img src="{{ asset('assets/img/courses/3.jpg') }}" alt="Course" class="w-100 rounded-8">
            <div class="course-card__overlay">
              <div class="course-card__progress">
                <div class="progress-circle" data-progress="90">
                  <span class="progress-text">90%</span>
                </div>
              </div>
            </div>
          </div>
          <div class="course-card__content p-20">
            <h5 class="course-card__title text-16 fw-500 mb-10">{{ __('messages.sports_psychology') }}</h5>
            <p class="course-card__description text-14 text-light-1 mb-15">{{ __('messages.mental_training') }}</p>
            <div class="course-card__meta d-flex justify-between items-center">
              <span class="text-12 text-light-1">{{ __('messages.15_lessons') }}</span>
              <a href="#" class="btn-modern btn-success btn-sm">{{ __('messages.finish') }}</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
/* Styles supplémentaires pour les cartes de cours */
.course-card {
  background: white;
  border-radius: var(--border-radius);
  overflow: hidden;
  transition: var(--transition);
}

.course-card:hover {
  transform: translateY(-5px);
}

.course-card__image {
  position: relative;
  height: 200px;
  overflow: hidden;
}

.course-card__image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.course-card__overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
}

.progress-circle {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: conic-gradient(var(--primary-color) 0deg, transparent 0deg);
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}

.progress-circle::before {
  content: '';
  position: absolute;
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: white;
}

.progress-text {
  position: relative;
  z-index: 1;
  font-size: 12px;
  font-weight: 600;
  color: var(--primary-color);
}

/* Styles pour les barres de progression */
.progress-bar {
  position: relative;
  overflow: hidden;
}

.progress-fill {
  transition: width 0.3s ease;
}

/* Styles pour les activités */
.activity-item {
  transition: var(--transition);
}

.activity-item:hover {
  background: var(--secondary-color);
  border-radius: var(--border-radius);
  padding: 15px;
  margin: 0 -15px;
}

.activity-icon {
  flex-shrink: 0;
}

/* Responsive */
@media (max-width: 768px) {
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .course-card__image {
    height: 150px;
  }
}

@media (max-width: 576px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }
}
</style>
@endsection
