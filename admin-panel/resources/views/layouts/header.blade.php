<style>
    /* Style pour le conteneur du loader */
    #loading {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.7);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    /* Style du spinner */
    .spinner-border {
        width: 3rem;
        height: 3rem;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    /* Animation de rotation */
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .navigation-main .nav-item .nav-link {
        border-radius: 6px;
        margin: 0 1rem 0.2rem 1rem;
        /* transition: all 0.3s ease; */
    }

    .navigation-main .nav-item .nav-link:hover {
        background-color: rgba(9, 49, 98, 0.08);
        color: #093162;
    }

    .navigation-main .nav-item .nav-link.active {
        background-color: #093162;
        color: #fff;
        box-shadow: 0 2px 4px 0 rgba(9, 49, 98, 0.4);
    }

    .navigation-main .nav-item .nav-link i {
        /* transition: all 0.3s ease; */
    }

    .navigation-main .nav-item .nav-link:hover i {
        /* transform: scale(1.1); */
    }

    .navigation-header {
        margin: 1.5rem 1rem 0.5rem 1rem;
        font-size: 0.857rem;
        font-weight: 600;
        color: #6e6b7b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .brand-text {
        font-weight: 600;
        color: #093162;
        margin-bottom: 0;
    }

    .brand-subtitle {
        font-size: 0.75rem;
        color: #6e6b7b;
        font-weight: 400;
        display: block;
        margin-top: -2px;
    }

    .navbar-brand {
        padding: 1.5rem 1rem;
    }

    /* Styles pour les icônes Font Awesome du menu */
    .navigation-main .nav-item i.fa {
        width: 16px;
        height: 16px;
        margin-right: 0.75rem;
        display: inline-block !important;
        vertical-align: middle;
        text-align: center;
        font-size: 16px;
        line-height: 1;
    }

    .navigation-header i.fa {
        width: 14px;
        height: 14px;
        margin-left: 0.5rem;
        display: inline-block !important;
        vertical-align: middle;
        text-align: center;
        font-size: 14px;
        line-height: 1;
    }

    /* Assurer que les icônes sont visibles et bien positionnées */
    .nav-item .d-flex.align-items-center i.fa {
        display: inline-block !important;
        vertical-align: middle;
        flex-shrink: 0;
    }

    /* Style pour les icônes actives */
    .navigation-main .nav-item.active i.fa {
        color: #fff !important;
    }

    /* Style pour les icônes au survol */
    .navigation-main .nav-item:hover i.fa {
        color: #093162 !important;
    }

    /* Animation de chargement des icônes */
    .navigation-main .nav-item i.fa {
        transition: all 0.3s ease;
    }

    /* Support RTL pour l'arabe */
    [dir="rtl"] .navigation-main .nav-item i.fa {
        margin-right: 0;
        margin-left: 0.75rem;
    }

    [dir="rtl"] .navigation-header i.fa {
        margin-left: 0;
        margin-right: 0.5rem;
    }
</style>

<div class="main-menu menu-fixed menu-dark menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item me-auto">
                <a class="navbar-brand" href="{{ route('dashboard') }}">



                </a>
            </li>
            <li class="nav-item nav-toggle">
                <a class="nav-link modern-nav-toggle pe-0" data-bs-toggle="collapse">
                    <i class="d-block d-xl-none text-primary toggle-icon font-medium-4 fa fa-times"></i>
                    <i class="d-none d-xl-block collapse-toggle-icon font-medium-4 text-primary fa fa-circle-o-notch" data-ticon="disc"></i>
                </a>
            </li>
        </ul>
    </div>
    <div class="shadow-bottom"></div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">

            <!-- Dashboard -->
            <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('dashboard') }}">
                    <i class="fa fa-home"></i>
                    <span class="menu-title text-truncate" data-i18n="Dashboard">Dashboard</span>
                </a>
            </li>

    <!-- Envoyer Email -->
    <li class="nav-item {{ request()->routeIs('emails.*') && !request()->routeIs('email-templates.*') ? 'active' : '' }}">
        <a class="d-flex align-items-center" href="{{ route('emails.inbox.index') }}">
            <i class="fa fa-envelope"></i>
            <span class="menu-title text-truncate" data-i18n="Send Email">Email & Inbox</span>
            @php
                $inboxUnreadNav = 0;
                if (\Illuminate\Support\Facades\Schema::hasTable('email_threads')) {
                    $inboxUnreadNav = \App\Models\EmailThread::where('unread_count', '>', 0)->sum('unread_count');
                }
            @endphp
            @if($inboxUnreadNav > 0)
                <span class="badge rounded-pill bg-danger ms-auto">{{ $inboxUnreadNav }}</span>
            @endif
        </a>
    </li>

    <!-- Gestion Templates Email -->
    <li class="nav-item {{ request()->routeIs('email-templates.*') ? 'active' : '' }}">
        <a class="d-flex align-items-center" href="{{ route('email-templates.index') }}">
            <i class="fa fa-envelope-o"></i>
            <span class="menu-title text-truncate" data-i18n="Email Templates">Email Templates Management</span>
        </a>
    </li>

    <!-- Email Logs -->
    <li class="nav-item {{ request()->routeIs('email-logs.*') ? 'active' : '' }}">
        <a class="d-flex align-items-center" href="{{ route('email-logs.index') }}" style="position: relative;">
            <i class="fa fa-list-alt"></i>
            <span class="menu-title text-truncate" data-i18n="Email Logs">Unified Email Log</span>
            @php
                $failedEmails = \App\Models\EmailLog::where('status', 'failed')->where('created_at', '>=', now()->subDays(7))->count();
            @endphp
            @if($failedEmails > 0)
                <span class="badge badge-danger badge-pill ml-auto" style="background: #ea5455; color: white; font-size: 10px; padding: 3px 6px; border-radius: 10px; margin-left: auto;">{{ $failedEmails }}</span>
            @endif
        </a>
    </li>

    <!-- Course Extensions -->
    <li class="nav-item {{ request()->routeIs('course-extensions.*') ? 'active' : '' }}">
        <a class="d-flex align-items-center" href="{{ route('course-extensions.index') }}" style="position: relative;">
            <i class="fa fa-refresh"></i>
            <span class="menu-title text-truncate" data-i18n="Course Extensions">Course Extensions</span>
            @php
                $pendingExtensions = \App\Models\CourseExtensionOrder::where('payment_status', 'pending')->count();
            @endphp
            @if($pendingExtensions > 0)
                <span class="badge badge-warning badge-pill ml-auto" style="background: #ff9f43; color: white; font-size: 10px; padding: 3px 6px; border-radius: 10px; margin-left: auto;">{{ $pendingExtensions }}</span>
            @endif
        </a>
    </li>

    <!-- Calendrier -->
    <li class="nav-item {{ request()->routeIs('calendar.*') ? 'active' : '' }}">
        <a class="d-flex align-items-center" href="{{ route('calendar.index') }}">
            <i class="fa fa-calendar"></i>
            <span class="menu-title text-truncate" data-i18n="Calendar">Calendar</span>
        </a>
    </li>

    <!-- Zoom Meetings -->
    <li class="nav-item {{ request()->routeIs('zoom-meetings.*') ? 'active' : '' }}">
        <a class="d-flex align-items-center" href="{{ route('zoom-meetings.index') }}">
            <i class="fa fa-video-camera"></i>
            <span class="menu-title text-truncate" data-i18n="Zoom Meetings">Zoom Meetings</span>
        </a>
    </li>

    <!-- Course Sessions -->
    <li class="nav-item {{ request()->routeIs('admin.course-sessions.*') ? 'active' : '' }}">
        <a class="d-flex align-items-center" href="{{ route('admin.course-sessions.index') }}">
            <i class="fa fa-calendar-check"></i>
            <span class="menu-title text-truncate" data-i18n="Course Sessions">Course Sessions</span>
        </a>
    </li>

    <!-- ===== MARKETING ===== -->
    <li class="navigation-header">
        <span data-i18n="Marketing">Marketing</span>
        <i class="fa fa-bullhorn"></i>
    </li>

    <!-- Coupons -->
    <li class="nav-item {{ request()->routeIs('coupons.*') ? 'active' : '' }}">
        <a class="d-flex align-items-center" href="{{ route('coupons.index') }}">
            <i class="fa fa-tags"></i>
            <span class="menu-title text-truncate" data-i18n="Coupons">Coupons</span>
        </a>
    </li>

    <!-- Packages -->
    <li class="nav-item {{ request()->routeIs('packages.*') ? 'active' : '' }}">
        <a class="d-flex align-items-center" href="{{ route('packages.index') }}">
            <i class="fa fa-cube"></i>
            <span class="menu-title text-truncate" data-i18n="Packages">Packages</span>
        </a>
    </li>

    <!-- Affiliate Partners -->
    <li class="nav-item {{ request()->routeIs('affiliate-partners.*') ? 'active' : '' }}">
        <a class="d-flex align-items-center" href="{{ route('affiliate-partners.index') }}">
            <i class="fa fa-users"></i>
            <span class="menu-title text-truncate" data-i18n="Affiliate Partners">Affiliate Partners</span>
        </a>
    </li>

    <!-- Referrals -->
    <li class="nav-item {{ request()->routeIs('admin.referrals.*') ? 'active' : '' }}">
        <a class="d-flex align-items-center" href="{{ route('admin.referrals.index') }}" style="position: relative;">
            <i class="fa fa-share-alt"></i>
            <span class="menu-title text-truncate" data-i18n="Referrals">Referrals</span>
            @php
                $pendingReferrals = \App\Models\Referral::where('status', 'pending')->count();
            @endphp
            @if($pendingReferrals > 0)
                <span class="badge badge-danger badge-pill ml-auto" style="background: #ea5455; color: white; font-size: 10px; padding: 3px 6px; border-radius: 10px; margin-left: auto;">{{ $pendingReferrals }}</span>
            @endif
        </a>
    </li>

    <!-- Abandoned Carts -->
    <li class="nav-item {{ request()->routeIs('abandoned-carts.*') ? 'active' : '' }}">
        <a class="d-flex align-items-center" href="{{ route('abandoned-carts.index') }}">
            <i class="fa fa-shopping-cart"></i>
            <span class="menu-title text-truncate" data-i18n="Abandoned Carts">Abandoned Carts</span>
        </a>
    </li>

    <!-- Internal Messages -->
    <li class="nav-item {{ request()->routeIs('internal-messages.*') ? 'active' : '' }}">
        <a class="d-flex align-items-center" href="{{ route('internal-messages.index') }}" style="position: relative;">
            <i class="fa fa-envelope-open-text"></i>
            <span class="menu-title text-truncate" data-i18n="Internal Messages">Internal Messages</span>
            <span class="sidebar-notification-badge" data-notification-type="student_message_response"></span>
        </a>
    </li>

    <!-- Contact Messages -->
    <li class="nav-item {{ request()->routeIs('contact-messages.*') ? 'active' : '' }}">
        <a class="d-flex align-items-center" href="{{ route('contact-messages.index') }}" style="position: relative;">
            <i class="fa fa-envelope"></i>
            <span class="menu-title text-truncate" data-i18n="Contact Messages">Contact Messages</span>
            @php
                $unreadContactMessages = \App\Models\ContactMessage::where('is_read', false)->count();
            @endphp
            @if($unreadContactMessages > 0)
                <span class="badge badge-danger badge-pill ml-auto" style="background: #ea5455; color: white; font-size: 10px; padding: 3px 6px; border-radius: 10px; margin-left: auto;">{{ $unreadContactMessages }}</span>
            @endif
        </a>
    </li>

    <!-- Partnerships -->
    <li class="nav-item {{ request()->routeIs('partnerships.*') ? 'active' : '' }}">
        <a class="d-flex align-items-center" href="{{ route('partnerships.index') }}" style="position: relative;">
            <i class="fa fa-handshake"></i>
            <span class="menu-title text-truncate" data-i18n="Partnerships">Partnerships</span>
            @php
                $unreadPartnerships = \App\Models\Partnership::where('is_read', false)->count();
            @endphp
            @if($unreadPartnerships > 0)
                <span class="badge badge-danger badge-pill ml-auto" style="background: #ea5455; color: white; font-size: 10px; padding: 3px 6px; border-radius: 10px; margin-left: auto;">{{ $unreadPartnerships }}</span>
            @endif
        </a>
    </li>

    <!-- Live Chat -->
    <li class="nav-item {{ request()->routeIs('admin.chat.*') ? 'active' : '' }}">
        <a class="d-flex align-items-center" href="{{ route('admin.chat.index') }}" style="position: relative;">
            <i class="fa fa-comments"></i>
            <span class="menu-title text-truncate" data-i18n="Live Chat">Live Chat</span>
            @php
                $unreadChats = \App\Models\ChatConversation::where('unread_admin_count', '>', 0)->count();
            @endphp
            @if($unreadChats > 0)
                <span class="badge badge-danger badge-pill ml-auto" style="background: #ea5455; color: white; font-size: 10px; padding: 3px 6px; border-radius: 10px; margin-left: auto;">{{ $unreadChats }}</span>
            @endif
        </a>
    </li>

            <!-- ===== CONTENT MANAGEMENT ===== -->




            <!-- ===== BLOGS & ARTICLES ===== -->
            <li class="navigation-header">
                <span data-i18n="Blogs & Articles">Courses</span>
                <i class="fa fa-file-text"></i>
            </li>

            <!-- Blog -->
        <li class="nav-item {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('categories.index') }}">
                    <i class="fa fa-folder"></i>
                    <span class="menu-title text-truncate" data-i18n="Categories">Categories</span>
                </a>
            </li>

            <!-- Courses -->
            <li class="nav-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('products.index') }}">
                    <i class="fa fa-book"></i>
                    <span class="menu-title text-truncate" data-i18n="Courses">Courses</span>
                </a>
            </li>

            <!-- Categories -->

            <!-- Certificats -->
            <li class="nav-item {{ request()->routeIs('certifs.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('certifs.index') }}">
                    <i class="fa fa-trophy"></i>
                    <span class="menu-title text-truncate" data-i18n="Certificats">Certificates</span>
                </a>
            </li>

            <!-- Practical Exams -->
            <li class="nav-item {{ request()->routeIs('practical-exams.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('practical-exams.index') }}" style="position: relative;">
                    <i class="fa fa-clipboard-check"></i>
                    <span class="menu-title text-truncate" data-i18n="Practical Exams">Practical Exams</span>
                    <span class="sidebar-notification-badge" data-notification-type="practical_exam_new_submission"></span>
                    @php
                        $pendingCount = \App\Models\PracticalExamAttempt::where('status', 'pending_review')->count();
                    @endphp
                    @if($pendingCount > 0)
                        <span class="badge badge-light-warning rounded-circle ms-auto" style="width: 8px; height: 8px; padding: 0; background-color: #ff9f43;"></span>
                    @endif
                </a>
            </li>

              <li class="nav-item {{ request()->routeIs('quizzes.*') || request()->routeIs('type-quizzes.*') ? 'active' : '' }}">

                <a class="d-flex align-items-center" href="{{ route('quizzes.index') }}">
                    <i class="fa fa-question-circle"></i>
                    <span class="menu-title text-truncate" data-i18n="Quizzes">Quizzes</span>
                </a>
            </li>

            <!-- Question Responses -->
            <li class="nav-item {{ request()->routeIs('reponse-questions.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('reponse-questions.index') }}">
                    <i class="fa fa-check-circle"></i>
                    <span class="menu-title text-truncate" data-i18n="Question Responses">Responses</span>
                    </a>
            </li>

            <!-- Sujets -->
            <li class="nav-item {{ request()->routeIs('sujets.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('sujets.index') }}">
                    <i class="fa fa-book"></i>
                    <span class="menu-title text-truncate" data-i18n="Sujets">Subjects</span>
                </a>
            </li>

            <!-- Books -->

 <li class="navigation-header">
                <span data-i18n="Gestion Résultat">Results Management</span>
                <i class="fa fa-chart-line"></i>
            </li>
             <li class="nav-item {{ request()->routeIs('course-students.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('course-students.index') }}">
                    <i class="fa fa-graduation-cap"></i>
                    <span class="menu-title text-truncate" data-i18n="Course Student">Course Student</span>
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('admin.resultat-quizzes.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.resultat-quizzes.index') }}" style="position: relative;">
                    <i class="fa fa-trophy"></i>
                    <span class="menu-title text-truncate" data-i18n="Quiz Results">Quiz Results</span>
                    <span class="sidebar-notification-badge" data-notification-type="evaluation"></span>
                </a>
            </li>


            <!-- Course Student -->


            <!-- Soumissions de Stage -->
            <li class="nav-item {{ request()->routeIs('student-stage-courses.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('student-stage-courses.by-product') }}">
                    <i class="fa fa-briefcase"></i>
                    <span class="menu-title text-truncate" data-i18n="Soumissions de Stage">Stage Submissions</span>
                    @php
                        $pendingStageCount = \App\Models\StudentStageCourse::where('is_valid', 0)->count();
                    @endphp
                    @if($pendingStageCount > 0)
                        <span class="badge bg-warning ms-auto">{{ $pendingStageCount }}</span>
                    @endif
                </a>
            </li>

            <!-- Examens Vidéo -->
            <li class="nav-item {{ request()->routeIs('student-video-exams.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('student-video-exams.by-product') }}">
                    <i class="fa fa-video-camera"></i>
                    <span class="menu-title text-truncate" data-i18n="Examens Vidéo">Video Exams</span>
                    @php
                        $pendingVideoCount = \App\Models\StudentVideoExam::where('is_valid', 0)->count();
                    @endphp
                    @if($pendingVideoCount > 0)
                        <span class="badge bg-warning ms-auto">{{ $pendingVideoCount }}</span>
                    @endif
                </a>
            </li>

            <!-- Succès Étudiants -->
            <li class="nav-item {{ request()->routeIs('student-successes.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('student-successes.by-product') }}">
                    <i class="fa fa-trophy"></i>
                    <span class="menu-title text-truncate" data-i18n="Succès Étudiants">Student Success</span>
                    <span class="sidebar-notification-badge" data-notification-type="student_success"></span>
                    <span class="sidebar-notification-badge" data-notification-type="manual_certificate"></span>
                </a>
            </li>
            <!-- ===== EDUCATIONAL MANAGEMENT ===== -->
            <li class="navigation-header">
                <span data-i18n="Educational Management">Educational Management</span>
                <i class="fa fa-graduation-cap"></i>
            </li>

            <!-- Teachers -->
            <li class="nav-item {{ request()->routeIs('teachers.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('teachers.index') }}">
                    <i class="fa fa-users"></i>
                    <span class="menu-title text-truncate" data-i18n="Teachers">Teachers</span>
                </a>
            </li>

            <!-- Roles & Permissions -->
            <li class="nav-item {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('roles.index') }}">
                    <i class="fa fa-shield"></i>
                    <span class="menu-title text-truncate" data-i18n="Roles & Permissions">Roles & Permissions</span>
                </a>
            </li>

            <!-- User Management -->
            <li class="nav-item {{ request()->routeIs('user-management.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('user-management.index') }}">
                    <i class="fa fa-users"></i>
                    <span class="menu-title text-truncate" data-i18n="Users">Users</span>
                </a>
            </li>

            <!-- Students -->
            <li class="nav-item {{ request()->routeIs('students.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('students.index') }}">
                    <i class="fa fa-graduation-cap"></i>
                    <span class="menu-title text-truncate" data-i18n="Students">Students</span>
                </a>
            </li>

            <!-- Books -->
            <li class="nav-item {{ request()->routeIs('books.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('books.index') }}">
                    <i class="fa fa-book"></i>
                    <span class="menu-title text-truncate" data-i18n="Books">Books</span>
                </a>
            </li>

            <!-- Resources -->
            <li class="nav-item {{ request()->routeIs('resources.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('resources.index') }}">
                    <i class="fa fa-download"></i>
                    <span class="menu-title text-truncate" data-i18n="Resources">Resources</span>
                </a>
            </li>

            <!-- Training Cases -->
            <li class="nav-item {{ request()->routeIs('training-cases.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('training-cases.index') }}">
                    <i class="fa fa-file-text"></i>
                    <span class="menu-title text-truncate" data-i18n="Training Cases">Training Cases</span>
                </a>
            </li>

            <!-- Certificate Management -->
            <li class="nav-item {{ request()->routeIs('certificate-management.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('certificate-management.index') }}">
                    <i class="fa fa-certificate"></i>
                    <span class="menu-title text-truncate" data-i18n="Certificate Management">Certificate Management</span>
                </a>
            </li>

            <!-- Quiz History -->
            <li class="nav-item {{ request()->routeIs('historique-quiz.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('historique-quiz.index') }}">
                    <i class="fa fa-history"></i>
                    <span class="menu-title text-truncate" data-i18n="Quiz History">Quiz History</span>
                </a>
            </li>

            <!-- Quizzes -->


            <!-- ===== GEOGRAPHIC MANAGEMENT ===== -->

             <li class="navigation-header">
                <span data-i18n="Geographic Management">Orders</span>
               <i class="fa fa-user-check"></i>
            </li>

              <li class="nav-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.orders.index') }}" style="position: relative;">
                    <i class="fa fa-shopping-cart"></i>
                    <span class="menu-title text-truncate" data-i18n="Orders">Orders</span>
                    <span class="sidebar-notification-badge" data-notification-type="purchase"></span>
                </a>
            </li>

            <!-- Installment Orders -->
            <li class="nav-item {{ request()->routeIs('admin.order-specifiques.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.order-specifiques.index') }}" style="position: relative;">
                    <i class="fa fa-credit-card"></i>
                    <span class="menu-title text-truncate" data-i18n="Installment Orders">Installment Orders</span>
                    <span class="sidebar-notification-badge" data-notification-type="installment"></span>
                </a>
            </li>
            <!-- ===== STUDENT MANAGEMENT ===== -->
             <li class="navigation-header">
                <span data-i18n="Geographic Management">Student</span>
              <i class="fa fa-user-check"></i>
            </li>


            <!-- Orders -->
           <li class="nav-item {{ request()->routeIs('import-students.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('import-students.index') }}">
                    <i class="fa fa-upload"></i>
                    <span class="menu-title text-truncate" data-i18n="Import Étudiant">Import Students</span>
                </a>
            </li>

            <!-- Quiz Results -->

            <!-- Discussions -->
            <li class="nav-item {{ request()->routeIs('admin.discussions.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.discussions.index') }}" style="position: relative;">
                    <i class="fa fa-comments"></i>
                    <span class="menu-title text-truncate" data-i18n="Discussions">Discussions</span>
                    <span class="sidebar-notification-badge" data-notification-type="comment"></span>
                </a>
            </li>

            <!-- Course Ratings -->
            <li class="nav-item {{ request()->routeIs('admin.course-ratings.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.course-ratings.index') }}" style="position: relative;">
                    <i class="fa fa-star"></i>
                    <span class="menu-title text-truncate" data-i18n="Course Ratings">Course Ratings</span>
                    <span class="sidebar-notification-badge" data-notification-type="rating"></span>
                </a>
            </li>

            <!-- Support Tickets -->
            <li class="nav-item {{ request()->routeIs('admin.support-tickets.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.support-tickets.index') }}" style="position: relative;">
                    <i class="fa fa-ticket"></i>
                    <span class="menu-title text-truncate" data-i18n="Support Tickets">Support Tickets</span>
                    <span class="sidebar-notification-badge" data-notification-type="ticket"></span>
                </a>
            </li>

            <!-- Import Étudiant -->



            <!-- ===== GESTION RÉSULTAT ===== -->

              <li class="navigation-header">
                <span data-i18n="Geographic Management">Geographic Management</span>
                <i class="fa fa-map-marker"></i>
            </li>

            <!-- Countries -->
            <li class="nav-item {{ request()->routeIs('countries.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('countries.index') }}">
                    <i class="fa fa-globe"></i>
                    <span class="menu-title text-truncate" data-i18n="Countries">Countries</span>
                </a>
            </li>
 <li class="navigation-header">
                <span data-i18n="Content Management">Content Management</span>
                <i class="fa fa-edit"></i>
            </li>

            <!-- Pages -->
            <li class="nav-item {{ request()->routeIs('pages.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('pages.index') }}">
                    <i class="fa fa-file-text"></i>
                    <span class="menu-title text-truncate" data-i18n="Pages">Pages</span>
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('blogs.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('blogs.index') }}">
                    <i class="fa fa-edit"></i>
                    <span class="menu-title text-truncate" data-i18n="Blog">Blog</span>
                </a>
            </li>

            <!-- Article -->
            <li class="nav-item {{ request()->routeIs('articles.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('articles.index') }}">
                    <i class="fa fa-file-text"></i>
                    <span class="menu-title text-truncate" data-i18n="Article">Article</span>
                </a>
            </li>

            <!-- Site Information -->
            <li class="nav-item {{ request()->routeIs('information.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('information.index') }}">
                    <i class="fa fa-info-circle"></i>
                    <span class="menu-title text-truncate" data-i18n="Information">Information</span>
                </a>
            </li>

            <!-- ===== GESTION DE PAGE D'ACCUEIL ===== -->
            <li class="navigation-header">
                <span data-i18n="Gestion de page d'accueil">Homepage Management</span>
                <i class="fa fa-home"></i>
            </li>

            <!-- Carousel -->
            <li class="nav-item {{ request()->routeIs('carousels.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('carousels.index') }}">
                    <i class="fa fa-image"></i>
                    <span class="menu-title text-truncate" data-i18n="Carousel">Carousel</span>
                </a>
            </li>

            <!-- Avis Clients -->
            <li class="nav-item {{ request()->routeIs('avis-acceuil.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('avis-acceuil.index') }}">
                    <i class="fa fa-star"></i>
                    <span class="menu-title text-truncate" data-i18n="Avis Clients">Customer Reviews</span>
                </a>
            </li>

            <!-- About Accueil -->
            <li class="nav-item {{ request()->routeIs('about-acceuil.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('about-acceuil.index') }}">
                    <i class="fa fa-info-circle"></i>
                    <span class="menu-title text-truncate" data-i18n="About Accueil">About Homepage</span>
                </a>
            </li>

            <!-- Products Accueil -->
            <li class="nav-item {{ request()->routeIs('products-acceuil.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('products-acceuil.index') }}">
                    <i class="fa fa-shopping-bag"></i>
                    <span class="menu-title text-truncate" data-i18n="Products Accueil">Homepage Products</span>
                </a>
            </li>

            <!-- Nos Partenaires -->
            <li class="nav-item {{ request()->routeIs('nos-partenaires.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('nos-partenaires.index') }}">
                    <i class="fa fa-users"></i>
                    <span class="menu-title text-truncate" data-i18n="Nos Partenaires">Our Partners</span>
                </a>
            </li>

            <!-- Achievements -->
            <li class="nav-item {{ request()->routeIs('admin.achievements.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.achievements.index') }}">
                    <i class="fa fa-trophy"></i>
                    <span class="menu-title text-truncate" data-i18n="Achievements">Achievements</span>
                </a>
            </li>

            <!-- Teachers Homepage -->
            <li class="nav-item {{ request()->routeIs('teacher-home-pages.*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('teacher-home-pages.index') }}">
                    <i class="fa fa-graduation-cap"></i>
                    <span class="menu-title text-truncate" data-i18n="Teachers Homepage">Homepage Teachers</span>
                </a>
            </li>

        </ul>
    </div>
</div>

<!-- Sidebar Notifications Component -->
@include('components.sidebar-notifications')

<!-- Les icônes Feather sont maintenant gérées par feather-icons-manager.js -->
