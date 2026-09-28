<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailTrackingController;
use App\Http\Controllers\AuthController;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\TrainingCaseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\TypeQuizController;
use App\Http\Controllers\ProductQuizController;
use App\Http\Controllers\ReponseQuestionController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ResultatQuizController;
use App\Http\Controllers\DiscussionController;
use App\Http\Controllers\CourseRatingController;
use App\Http\Controllers\NosPartenairesController;
use App\Http\Controllers\ResponseDiscussionController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CertifController;
use App\Http\Controllers\CarouselController;
use App\Http\Controllers\AvisAcceuilController;
use App\Http\Controllers\AboutAcceuilController;
use App\Http\Controllers\ProductAcceuilController;
use App\Http\Controllers\ImportStudentController;
use App\Http\Controllers\CourseStudentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentStageCourseController;
use App\Http\Controllers\StudentVideoExamController;
use App\Http\Controllers\StudentSuccessController;
use App\Http\Controllers\CertificateManagementController;
use App\Http\Controllers\SujetController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\EmailInboxController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\HistoriqueQuizController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\OrderSpecifiqueController;
use App\Http\Controllers\AchievementController;
use App\Http\Controllers\TeacherHomePageController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\AbandonedCartController;
use App\Http\Controllers\InternalMessageController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\PartnershipController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserManagementController;
// Routes d'authentification (non protégées)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public open-tracking pixel (Send Email logs); authenticated only by secret token length
Route::get('/email/track/{token}', [EmailTrackingController::class, 'pixel'])
    ->where('token', '[a-zA-Z0-9]{40}')
    ->middleware('throttle:120,1')
    ->name('email-tracking.pixel');

Route::get('/email/track/{token}/confirm', [EmailTrackingController::class, 'confirmRedirect'])
    ->where('token', '[a-zA-Z0-9]{40}')
    ->middleware('throttle:60,1')
    ->name('email-tracking.confirm');

Route::middleware('auth')->prefix('api/notifications')->group(function () {
    Route::get('/', [NotificationController::class, 'index']);
    Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
    Route::get('/counts-by-type', [NotificationController::class, 'getCountsByType']);
    Route::post('/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);
    Route::post('/mark-all-as-read', [NotificationController::class, 'markAllAsRead']);
    Route::post('/mark-as-read-by-type/{type}', [NotificationController::class, 'markAsReadByType']);
    Route::delete('/{id}', [NotificationController::class, 'destroy']);
});

// Redirection de la racine vers login si non connecté, sinon vers dashboard
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});
Route::resource('pages', PageController::class);
Route::post('pages/update-order', [PageController::class, 'updateOrder'])->name('pages.update-order');
// Dashboard
Route::get('/dashboard',[DashboardController::class,'index'])->name('dashboard');

// Routes pour les produits
Route::resource('products', ProductController::class);
Route::get('/products/{product}/duplicate', [ProductController::class, 'duplicate'])->name('products.duplicate');
Route::delete('/products/{product}/remove-study-resource', [ProductController::class, 'removeStudyResource'])->name('products.removeStudyResource');

// Routes pour les documents de stage
Route::post('/stage-documents/upload', [\App\Http\Controllers\Admin\StageDocumentController::class, 'upload'])->name('stage-documents.upload');

// Routes pour les examens pratiques
Route::get('/practical-exams', [\App\Http\Controllers\PracticalExamController::class, 'index'])->name('practical-exams.index');
Route::get('/practical-exams/{attempt}/grade', [\App\Http\Controllers\PracticalExamController::class, 'show'])->name('practical-exams.show');
Route::post('/practical-exams/{attempt}/grade', [\App\Http\Controllers\PracticalExamController::class, 'grade'])->name('practical-exams.grade');
Route::get('/api/practical-exams/stats', [\App\Http\Controllers\PracticalExamController::class, 'getStats'])->name('practical-exams.stats');
Route::get('/products/{product}/stage-documents', [\App\Http\Controllers\Admin\StageDocumentController::class, 'getProductDocuments'])->name('stage-documents.index');
Route::delete('/stage-documents/{document}', [\App\Http\Controllers\Admin\StageDocumentController::class, 'delete'])->name('stage-documents.delete');

// Routes pour les training cases (حالات تدريبية)
Route::get('/training-cases/list', [TrainingCaseController::class, 'list'])->name('training-cases.list');
Route::resource('training-cases', TrainingCaseController::class);
Route::post('/training-cases/{trainingCase}/toggle-status', [TrainingCaseController::class, 'toggleStatus'])->name('training-cases.toggle-status');
Route::delete('/training-case-files/{file}', [TrainingCaseController::class, 'deleteFile'])->name('training-case-files.delete');
Route::get('/training-case-files/{file}/download', [TrainingCaseController::class, 'downloadFile'])->name('training-case-files.download');

// Routes pour la gestion des étudiants
Route::get('/students/{student}/search-courses-for-block', [StudentController::class, 'searchCoursesForBlock'])->name('students.search-courses-for-block');
Route::post('/students/{student}/toggle-block-course/{courseId}', [StudentController::class, 'toggleBlockCourse'])->name('students.toggle-block-course');
Route::post('/students/{student}/adjust-points', [StudentController::class, 'adjustPoints'])->name('students.adjust-points');
Route::post('/students/{student}/block', [StudentController::class, 'block'])->name('students.block');
Route::post('/students/{student}/unblock', [StudentController::class, 'unblock'])->name('students.unblock');
Route::resource('students', StudentController::class);

// Routes pour la gestion des rôles et permissions
Route::resource('roles', RoleController::class);
Route::post('/roles/assign-to-admin', [RoleController::class, 'assignToAdmin'])->name('roles.assign-to-admin');

// Routes pour la gestion des utilisateurs
Route::resource('user-management', UserManagementController::class)->parameters([
    'user-management' => 'user'
]);
Route::post('/user-management/{user}/assign-role', [UserManagementController::class, 'assignRole'])->name('user-management.assign-role');
Route::post('/user-management/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('user-management.toggle-status');

// Routes pour l'importation des étudiants
Route::get('/import-students', [ImportStudentController::class, 'index'])->name('import-students.index');
Route::post('/import-students', [ImportStudentController::class, 'import'])->name('import-students.import');
Route::post('/import-students/add-manual', [ImportStudentController::class, 'addManual'])->name('import-students.add-manual');

// Routes pour les cours et étudiants
Route::get('/course-students', [CourseStudentController::class, 'index'])->name('course-students.index');
Route::get('/course-students/{id}', [CourseStudentController::class, 'show'])->name('course-students.show');
Route::post('/course-students/{id}/add-student', [CourseStudentController::class, 'addStudent'])->name('course-students.add-student');
Route::get('/course-students/{id}/available-students', [CourseStudentController::class, 'getAvailableStudents'])->name('course-students.available-students');
Route::delete('/course-students/{id}/remove', [CourseStudentController::class, 'removeEnrollment'])->name('course-students.remove');
Route::post('/course-students/{courseId}/toggle-block/{studentId}', [CourseStudentController::class, 'toggleBlockStudent'])->name('course-students.toggle-block');

// Routes pour les paniers abandonnés
Route::get('/abandoned-carts', [AbandonedCartController::class, 'index'])->name('abandoned-carts.index');
Route::get('/abandoned-carts/{id}', [AbandonedCartController::class, 'show'])->name('abandoned-carts.show');
Route::get('/abandoned-carts-export', [AbandonedCartController::class, 'export'])->name('abandoned-carts.export');
Route::post('/abandoned-carts/{id}/send-reminder', [AbandonedCartController::class, 'sendReminder'])->name('abandoned-carts.send-reminder');

// Routes pour les messages internes
Route::get('/internal-messages', [InternalMessageController::class, 'index'])->name('internal-messages.index');
Route::get('/internal-messages/create', [InternalMessageController::class, 'create'])->name('internal-messages.create');
Route::post('/internal-messages', [InternalMessageController::class, 'store'])->name('internal-messages.store');
Route::get('/internal-messages/download/{filename}', [InternalMessageController::class, 'downloadAttachment'])->name('internal-messages.download')->where('filename', '.*');
Route::get('/internal-messages/api/unread-summary', [InternalMessageController::class, 'unreadSummary'])->name('internal-messages.unread-summary');
Route::post('/internal-messages/{id}/mark-read', [InternalMessageController::class, 'markRead'])->name('internal-messages.mark-read');
Route::get('/internal-messages/{id}', [InternalMessageController::class, 'show'])->name('internal-messages.show')->whereNumber('id');
Route::post('/internal-messages/response/{responseId}', [InternalMessageController::class, 'storeAdminResponse'])->name('internal-messages.respond');
Route::get('/api/search-students', [InternalMessageController::class, 'searchStudents'])->name('api.search-students');

// Routes pour les messages de contact
Route::get('/contact-messages', [ContactMessageController::class, 'index'])->name('contact-messages.index');
Route::get('/contact-messages/{contactMessage}', [ContactMessageController::class, 'show'])->name('contact-messages.show');
Route::post('/contact-messages/{contactMessage}/respond', [ContactMessageController::class, 'respond'])->name('contact-messages.respond');
Route::post('/contact-messages/{contactMessage}/mark-read', [ContactMessageController::class, 'markAsRead'])->name('contact-messages.mark-read');
Route::post('/contact-messages/{contactMessage}/mark-unread', [ContactMessageController::class, 'markAsUnread'])->name('contact-messages.mark-unread');
Route::delete('/contact-messages/{contactMessage}', [ContactMessageController::class, 'destroy'])->name('contact-messages.destroy');
Route::post('/contact-messages/bulk/mark-read', [ContactMessageController::class, 'bulkMarkAsRead'])->name('contact-messages.bulk-mark-read');
Route::post('/contact-messages/bulk/delete', [ContactMessageController::class, 'bulkDelete'])->name('contact-messages.bulk-delete');
Route::get('/api/contact-messages/unread-count', [ContactMessageController::class, 'getUnreadCount'])->name('contact-messages.unread-count');

// Routes pour les demandes de partenariat
Route::get('/partnerships', [PartnershipController::class, 'index'])->name('partnerships.index');
Route::get('/partnerships/mark-all-read', [PartnershipController::class, 'markAllAsRead'])->name('partnerships.mark-all-read');
Route::get('/partnerships/{partnership}', [PartnershipController::class, 'show'])->name('partnerships.show');
Route::patch('/partnerships/{partnership}/status', [PartnershipController::class, 'updateStatus'])->name('partnerships.update-status');
Route::delete('/partnerships/{partnership}', [PartnershipController::class, 'destroy'])->name('partnerships.destroy');
Route::get('/partnerships/{partnership}/download', [PartnershipController::class, 'downloadFile'])->name('partnerships.download');
Route::post('/partnerships/{partnership}/mark-read', [PartnershipController::class, 'markAsRead'])->name('partnerships.mark-read');

// Routes pour les soumissions de stage
Route::resource('student-stage-courses', StudentStageCourseController::class);
Route::get('/student-stage-courses-by-product', [StudentStageCourseController::class, 'byProduct'])->name('student-stage-courses.by-product');
Route::get('/student-stage-courses/{studentStageCourse}/download-file/{fileNumber}', [StudentStageCourseController::class, 'downloadFile'])->name('student-stage-courses.download-file');
Route::put('/student-stage-courses/{studentStageCourse}/validate', [StudentStageCourseController::class, 'validate'])->name('student-stage-courses.validate');
Route::put('/student-stage-courses/{studentStageCourse}/reject', [StudentStageCourseController::class, 'reject'])->name('student-stage-courses.reject');

// Routes pour les examens vidéo
Route::get('/student-video-exams-by-product', [StudentVideoExamController::class, 'byProduct'])->name('student-video-exams.by-product');
Route::put('/student-video-exams/{studentVideoExam}/validate', [StudentVideoExamController::class, 'approve'])->name('student-video-exams.validate');
Route::put('/student-video-exams/{studentVideoExam}/reject', [StudentVideoExamController::class, 'reject'])->name('student-video-exams.reject');
Route::get('/student-video-exams/{studentVideoExam}/open-video', [StudentVideoExamController::class, 'openVideo'])->name('student-video-exams.open-video');
Route::resource('student-video-exams', StudentVideoExamController::class);

// Routes pour le contenu public des produits
Route::get('/products/public/arabic', [ProductController::class, 'publicArabic'])->name('products.public.arabic');
Route::get('/products/public/english', [ProductController::class, 'publicEnglish'])->name('products.public.english');

// Routes pour les catégories
Route::resource('categories', CategoryController::class);
Route::post('/categories/update-order', [CategoryController::class, 'updateOrder'])->name('categories.update-order');
// Forcer uniquement la vue de création en anglais
Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create')->middleware('en.locale');

// Routes pour les enseignants
Route::resource('teachers', TeacherController::class);

// Routes pour les informations du site
Route::get('/information', [App\Http\Controllers\InformationController::class, 'index'])->name('information.index');
Route::put('/information', [App\Http\Controllers\InformationController::class, 'update'])->name('information.update');

// Routes pour les pays
Route::resource('countries', CountryController::class);

// Routes pour les ressources
Route::resource('resources', ResourceController::class);
Route::get('/resources/{resource}/download', [ResourceController::class, 'download'])->name('resources.download');
Route::get('/resources/{resource}/manage-videos', [ResourceController::class, 'manageVideos'])->name('resources.manage-videos');
Route::post('/resources/{resource}/add-video', [ResourceController::class, 'addVideo'])->name('resources.add-video');
Route::delete('/resources/{resource}/remove-video', [ResourceController::class, 'removeVideo'])->name('resources.remove-video');
Route::post('/resources/{resource}/add-video-file', [ResourceController::class, 'addVideoFile'])->name('resources.add-video-file');
Route::delete('/resources/{resource}/remove-video-file', [ResourceController::class, 'removeVideoFile'])->name('resources.remove-video-file');
Route::get('/resources/{resource}/download-video/{title}', [ResourceController::class, 'downloadVideo'])->name('resources.download-video');

// Routes pour la gestion des quiz
Route::resource('quizzes', QuizController::class);
Route::post('/quizzes/{quiz}/questions', [QuizController::class, 'addQuestion'])->name('quizzes.add-question');
Route::get('/import-quiz', [QuizController::class, 'importQuiz'])->name('import-quiz');
Route::resource('type-quizzes', TypeQuizController::class);

// Routes pour les réponses aux questions
Route::resource('reponse-questions', ReponseQuestionController::class);
Route::get('/questions/{questionId}/reponses', [ReponseQuestionController::class, 'getByQuestion'])->name('questions.reponses');

// Routes pour les quiz des produits
Route::get('/products/{product}/quizzes', [ProductQuizController::class, 'index'])->name('products.quizzes.index');
Route::post('/products/{product}/quizzes', [ProductQuizController::class, 'store'])->name('products.quizzes.store');
Route::delete('/products/{product}/quizzes/{quiz}', [ProductQuizController::class, 'destroy'])->name('products.quizzes.destroy');
Route::put('/products/{product}/quizzes/{quiz}/installment-month', [ProductQuizController::class, 'updateInstallmentMonth'])->name('products.quizzes.update-installment-month');
Route::get('/quizzes/search', [ProductQuizController::class, 'search'])->name('quizzes.search');

// Routes pour les livres
Route::resource('books', BookController::class);

// Routes pour les pages
Route::resource('pages', PageController::class);

// Routes pour les blogs
Route::resource('blogs', BlogController::class);

// Routes pour les articles
Route::resource('articles', ArticleController::class);

// Routes pour les certificats
Route::resource('certifs', CertifController::class);
Route::resource('nos-partenaires', NosPartenairesController::class)->parameters([
    'nos-partenaires' => 'nosPartenaires'
]);
Route::post('nos-partenaires/update-order', [NosPartenairesController::class, 'updateOrder'])->name('nos-partenaires.update-order');
Route::get('/certifs/{certif}/download', [CertifController::class, 'download'])->name('certifs.download');
Route::post('/certifs/{certif}/update-template', [CertifController::class, 'updateTemplate'])->name('certifs.update-template');
Route::get('/certifs/{certif}/template-data', [CertifController::class, 'getTemplateData'])->name('certifs.template-data');
Route::put('/certifs/{certif}/template-data', [CertifController::class, 'updateTemplateData'])->name('certifs.update-template-data');
Route::get('/certifs/{certif}/edit-click', [CertifController::class, 'editClick'])->name('certifs.edit-click');

// Routes pour la gestion des certificats générés
Route::get('/certificate-management', [CertificateManagementController::class, 'index'])->name('certificate-management.index');
Route::get('/certificate-management/create', [CertificateManagementController::class, 'create'])->name('certificate-management.create');
Route::post('/certificate-management/manual-generate', [CertificateManagementController::class, 'manualGenerate'])->name('certificate-management.manual-generate');
Route::get('/certificate-management/get-students/{courseId}', [CertificateManagementController::class, 'getStudentsByCourse'])->name('certificate-management.get-students');
Route::get('/certificate-management/{studentSuccess}', [CertificateManagementController::class, 'show'])->name('certificate-management.show');
Route::get('/certificate-management/{studentSuccess}/download', [CertificateManagementController::class, 'download'])->name('certificate-management.download');
Route::post('/certificate-management/{studentSuccess}/generate', [CertificateManagementController::class, 'generate'])->name('certificate-management.generate');
Route::post('/certificate-management/bulk-generate', [CertificateManagementController::class, 'bulkGenerate'])->name('certificate-management.bulk-generate');
Route::put('/certificate-management/certificate/{certificate}/update-date', [CertificateManagementController::class, 'updateDate'])->name('certificate-management.update-date');
Route::post('/certificate-management/certificate/{certificate}/regenerate', [CertificateManagementController::class, 'regenerate'])->name('certificate-management.regenerate');
Route::delete('/certificate-management/generated-certificate/{certificate}', [CertificateManagementController::class, 'deleteGeneratedCertificate'])->name('certificate-management.delete-generated');
Route::delete('/certificate-management/certificate/{id}/delete', [CertificateManagementController::class, 'delete'])->name('certificate-management.delete');

// Route publique pour afficher le certificat via QR code
Route::get('/certificate/{serialNumber}', [CertificateManagementController::class, 'viewPublic'])->name('certificate.public');

// Routes pour les sujets
Route::resource('sujets', SujetController::class);

// Routes pour l'envoi d'emails
Route::get('/emails', [EmailController::class, 'index'])->name('emails.index');
Route::post('/emails/send', [EmailController::class, 'send'])->name('emails.send');
Route::get('/emails/inbox', [EmailInboxController::class, 'index'])->name('emails.inbox.index');
Route::get('/emails/inbox/{thread}', [EmailInboxController::class, 'show'])->name('emails.inbox.show');
Route::post('/emails/inbox/{thread}/reply', [EmailInboxController::class, 'reply'])->name('emails.inbox.reply');
Route::post('/emails/inbox/sync', [EmailInboxController::class, 'sync'])->name('emails.inbox.sync');
Route::post('/emails/inbox/{thread}/close', [EmailInboxController::class, 'close'])->name('emails.inbox.close');
Route::post('/emails/inbox/{thread}/reopen', [EmailInboxController::class, 'reopen'])->name('emails.inbox.reopen');

// Routes pour les logs d'emails
Route::get('/email-logs', [App\Http\Controllers\EmailLogController::class, 'index'])->name('email-logs.index');

// Routes pour les extensions de cours
Route::get('/course-extensions', [App\Http\Controllers\CourseExtensionController::class, 'index'])->name('course-extensions.index');
Route::post('/course-extensions/{extensionOrder}/approve', [App\Http\Controllers\CourseExtensionController::class, 'approve'])->name('course-extensions.approve');
Route::post('/course-extensions/{extensionOrder}/reject', [App\Http\Controllers\CourseExtensionController::class, 'reject'])->name('course-extensions.reject');
Route::get('/course-extensions/{extensionOrder}/receipt', [App\Http\Controllers\CourseExtensionController::class, 'downloadReceipt'])->name('course-extensions.receipt');

// Routes pour la gestion des templates d'email
Route::resource('email-templates', EmailTemplateController::class);
Route::get('/email-templates/{emailTemplate}/preview', [EmailTemplateController::class, 'preview'])->name('email-templates.preview');
Route::patch('/email-templates/{emailTemplate}/toggle-status', [EmailTemplateController::class, 'toggleStatus'])->name('email-templates.toggle-status');

// Routes pour l'historique des quiz
Route::get('/historique-quiz', [HistoriqueQuizController::class, 'index'])->name('historique-quiz.index');
Route::get('/historique-quiz/{historiqueQuiz}', [HistoriqueQuizController::class, 'show'])->name('historique-quiz.show');
Route::get('/historique-quiz/student/{studentId}', [HistoriqueQuizController::class, 'byStudent'])->name('historique-quiz.by-student');
Route::get('/historique-quiz/course/{courseId}', [HistoriqueQuizController::class, 'byCourse'])->name('historique-quiz.by-course');
Route::get('/historique-quiz-statistics', [HistoriqueQuizController::class, 'statistics'])->name('historique-quiz.statistics');

// Routes pour le calendrier
Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
Route::get('/calendar/events', [CalendarController::class, 'getEvents'])->name('calendar.events');
Route::post('/calendar/events', [CalendarController::class, 'store'])->name('calendar.store');
Route::put('/calendar/events/{id}', [CalendarController::class, 'update'])->name('calendar.update');
Route::delete('/calendar/events/{id}', [CalendarController::class, 'destroy'])->name('calendar.destroy');
Route::patch('/calendar/events/{id}/complete', [CalendarController::class, 'markCompleted'])->name('calendar.complete');
Route::get('/calendar/statistics', [CalendarController::class, 'getStatistics'])->name('calendar.statistics');

// Routes pour les réunions Zoom
Route::get('zoom-meetings/add-recording/form', [App\Http\Controllers\ZoomMeetingController::class, 'addRecordingForm'])->name('zoom-meetings.add-recording');
Route::post('zoom-meetings/add-recording/store', [App\Http\Controllers\ZoomMeetingController::class, 'storeRecording'])->name('zoom-meetings.store-recording');
Route::resource('zoom-meetings', App\Http\Controllers\ZoomMeetingController::class);

// Routes pour les sessions de cours
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('course-sessions', App\Http\Controllers\Admin\CourseSessionController::class);
    Route::get('course-sessions/by-course/{productId}', [App\Http\Controllers\Admin\CourseSessionController::class, 'getByCourse'])->name('course-sessions.by-course');
    Route::post('course-sessions/bulk-update-status', [App\Http\Controllers\Admin\CourseSessionController::class, 'bulkUpdateStatus'])->name('course-sessions.bulk-update-status');
});

// Routes pour les emails de tâches planifiées
Route::get('/admin/scheduled-task-emails', [App\Http\Controllers\ScheduledTaskEmailController::class, 'index'])->name('scheduled-task-emails.index');
Route::post('/admin/scheduled-task-emails/send', [App\Http\Controllers\ScheduledTaskEmailController::class, 'sendEmails'])->name('scheduled-task-emails.send');
Route::post('/admin/scheduled-task-emails/test', [App\Http\Controllers\ScheduledTaskEmailController::class, 'sendTestEmail'])->name('scheduled-task-emails.test');

// Routes pour les coupons (Marketing)
Route::get('/coupons', [App\Http\Controllers\CouponController::class, 'index'])->name('coupons.index');
Route::get('/coupons/create', [App\Http\Controllers\CouponController::class, 'create'])->name('coupons.create');
Route::post('/coupons', [App\Http\Controllers\CouponController::class, 'store'])->name('coupons.store');
Route::get('/coupons/{coupon}', [App\Http\Controllers\CouponController::class, 'show'])->name('coupons.show');
Route::get('/coupons/{coupon}/edit', [App\Http\Controllers\CouponController::class, 'edit'])->name('coupons.edit');
Route::put('/coupons/{coupon}', [App\Http\Controllers\CouponController::class, 'update'])->name('coupons.update');
Route::patch('/coupons/{coupon}', [App\Http\Controllers\CouponController::class, 'update']);
Route::delete('/coupons/{coupon}', [App\Http\Controllers\CouponController::class, 'destroy'])->name('coupons.destroy');
Route::patch('/coupons/{coupon}/toggle', [App\Http\Controllers\CouponController::class, 'toggle'])->name('coupons.toggle');
Route::post('/coupons/{coupon}/duplicate', [App\Http\Controllers\CouponController::class, 'duplicate'])->name('coupons.duplicate');
Route::get('/coupons-statistics', [App\Http\Controllers\CouponController::class, 'statistics'])->name('coupons.statistics');
Route::post('/coupons/validate-name', [App\Http\Controllers\CouponController::class, 'validateName'])->name('coupons.validate-name');
Route::post('/coupons/validate-code', [App\Http\Controllers\CouponController::class, 'validateCode'])->name('coupons.validate-code');

// Routes pour les partenaires affiliés (Marketing)
Route::resource('affiliate-partners', App\Http\Controllers\AffiliatePartnerController::class);
Route::patch('/affiliate-partners/{affiliate_partner}/approve', [App\Http\Controllers\AffiliatePartnerController::class, 'approve'])->name('affiliate-partners.approve');
Route::patch('/affiliate-partners/{affiliate_partner}/suspend', [App\Http\Controllers\AffiliatePartnerController::class, 'suspend'])->name('affiliate-partners.suspend');
Route::patch('/affiliate-partners/{affiliate_partner}/reactivate', [App\Http\Controllers\AffiliatePartnerController::class, 'reactivate'])->name('affiliate-partners.reactivate');
Route::get('/affiliate-partners-report', [App\Http\Controllers\AffiliatePartnerController::class, 'report'])->name('affiliate-partners.report');
Route::post('/affiliate-partners/{affiliate_partner}/process-payout', [App\Http\Controllers\AffiliatePartnerController::class, 'processPayout'])->name('affiliate-partners.process-payout');
Route::get('/affiliate-partners-export', [App\Http\Controllers\AffiliatePartnerController::class, 'export'])->name('affiliate-partners.export');

// Routes pour les packages (Marketing)
Route::resource('packages', App\Http\Controllers\PackageController::class);
Route::patch('/packages/{package}/toggle', [App\Http\Controllers\PackageController::class, 'toggle'])->name('packages.toggle');
Route::get('/packages-statistics', [App\Http\Controllers\PackageController::class, 'statistics'])->name('packages.statistics');

// Routes pour les chiffres d'accueil (Homepage Statistics)
Route::resource('accueil-chiffres', App\Http\Controllers\AccueilChiffreController::class);
Route::patch('/accueil-chiffres/{accueilChiffre}/toggle', [App\Http\Controllers\AccueilChiffreController::class, 'toggle'])->name('accueil-chiffres.toggle');

Route::post('/certifs/{certif}/generate', [CertifController::class, 'generateCertificate'])->name('certifs.generate');
Route::post('/certifs/{certif}/test-generate', [CertifController::class, 'testGenerateCertificate'])->name('certifs.test-generate');
Route::get('/certifs/{certif}/download-test/{filename}', [CertifController::class, 'downloadTestCertificate'])->name('certifs.download-test-certificate');

// Routes pour les champs dynamiques des certificats
Route::post('/certifs/{certif}/add-dynamic-field', [CertifController::class, 'addDynamicField'])->name('certifs.add-dynamic-field');
Route::delete('/certifs/{certif}/remove-dynamic-field', [CertifController::class, 'removeDynamicField'])->name('certifs.remove-dynamic-field');
Route::get('/certifs/{certif}/dynamic-fields', [CertifController::class, 'getDynamicFields'])->name('certifs.dynamic-fields');

// Routes pour les carousels
Route::resource('carousels', CarouselController::class);

// Routes pour les avis clients
Route::resource('avis-acceuil', AvisAcceuilController::class);

// Routes pour la section About
Route::resource('about-acceuil', AboutAcceuilController::class);

// Routes pour les produits d'accueil
Route::resource('products-acceuil', ProductAcceuilController::class);

// Routes pour les Achievements
Route::get('/achievements', [AchievementController::class, 'index'])->name('admin.achievements.index');
Route::get('/achievements/edit', [AchievementController::class, 'edit'])->name('admin.achievements.edit');
Route::put('/achievements', [AchievementController::class, 'update'])->name('admin.achievements.update');

// Routes pour les Teachers Homepage
Route::resource('teacher-home-pages', TeacherHomePageController::class);

// Routes pour StudentStageCourse
Route::resource('student-stage-courses', StudentStageCourseController::class);
Route::get('student-stage-courses/{studentStageCourse}/download-file/{fileNumber}', [StudentStageCourseController::class, 'downloadFile'])->name('student-stage-courses.download-file');
Route::put('student-stage-courses/{studentStageCourse}/validate', [StudentStageCourseController::class, 'validate'])->name('student-stage-courses.validate');
Route::put('student-stage-courses/{studentStageCourse}/reject', [StudentStageCourseController::class, 'reject'])->name('student-stage-courses.reject');
Route::get('student-stage-courses-by-product', [StudentStageCourseController::class, 'byProduct'])->name('student-stage-courses.by-product');

// Routes pour StudentSuccess
Route::resource('student-successes', StudentSuccessController::class);
Route::get('student-successes/{studentSuccess}/open-video', [StudentSuccessController::class, 'openVideo'])->name('student-successes.open-video');
Route::put('student-successes/{studentSuccess}/validate', [StudentSuccessController::class, 'validate'])->name('student-successes.validate');
Route::put('student-successes/{studentSuccess}/reject', [StudentSuccessController::class, 'reject'])->name('student-successes.reject');
Route::get('student-successes/{studentSuccess}/download-certificate', [StudentSuccessController::class, 'downloadCertificate'])->name('student-successes.download-certificate');
Route::put('student-successes/{studentSuccess}/generate-certificate-direct', [StudentSuccessController::class, 'generateCertificateDirect'])->name('student-successes.generate-certificate-direct');
Route::get('student-successes-by-product', [StudentSuccessController::class, 'byProduct'])->name('student-successes.by-product');

// Route pour servir les fichiers PDF
Route::get('/serve-pdf/{filename}', function ($filename) {
    $path = storage_path('app/public/certifs/' . $filename);

    if (file_exists($path)) {
        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ]);
    }

    abort(404, 'Fichier non trouvé');
})->where('filename', '.*')->name('serve-pdf');

// Routes Admin pour la Gestion des Cours
Route::prefix('admin')->name('admin.')->group(function () {
    // Routes pour les commandes
    Route::resource('orders', OrderController::class);
    Route::post('/orders/{order}/toggle-payment', [OrderController::class, 'togglePayment'])->name('orders.toggle-payment');
    Route::post('/orders/{order}/approve-payment', [OrderController::class, 'approvePayment'])->name('orders.approve-payment');
    Route::post('/orders/{order}/reject-payment', [OrderController::class, 'rejectPayment'])->name('orders.reject-payment');
    Route::get('/orders/{order}/download-receipt', [OrderController::class, 'downloadReceipt'])->name('orders.download-receipt');

    // Routes pour les commandes par tranches
    Route::resource('order-specifiques', OrderSpecifiqueController::class);
    Route::post('/order-specifiques/{orderSpecifique}/add-payment', [OrderSpecifiqueController::class, 'addPayment'])->name('order-specifiques.add-payment');
    Route::get('/order-specifiques/product-variations', [OrderSpecifiqueController::class, 'getProductVariations'])->name('order-specifiques.product-variations');

    // Routes pour la gestion individuelle des échéances
    Route::get('/order-specifiques/installments/{installment}/detail', [OrderSpecifiqueController::class, 'showInstallment'])->name('order-specifiques.installment-detail');
    Route::get('/order-specifiques/installments/{installment}/download-receipt', [OrderSpecifiqueController::class, 'downloadInstallmentReceipt'])->name('order-specifiques.download-installment-receipt');
    Route::post('/order-specifiques/installments/{installment}/mark-paid', [OrderSpecifiqueController::class, 'markInstallmentPaid'])->name('order-specifiques.mark-installment-paid');
    Route::put('/order-specifiques/installments/{installment}/mark-pending', [OrderSpecifiqueController::class, 'markInstallmentPending'])->name('order-specifiques.mark-installment-pending');
    Route::put('/order-specifiques/installments/{installment}/due-date', [OrderSpecifiqueController::class, 'updateInstallmentDueDate'])->name('order-specifiques.update-installment-due-date');
    Route::put('/order-specifiques/installments/{installment}/paid-date', [OrderSpecifiqueController::class, 'updateInstallmentPaidDate'])->name('order-specifiques.update-installment-paid-date');

    // Routes pour les résultats de quiz
    Route::resource('resultat-quizzes', ResultatQuizController::class);
    Route::put('/resultat-quizzes/{resultatQuiz}/update-success', [ResultatQuizController::class, 'adminUpdateSuccess'])->name('resultat-quizzes.update-success');
    Route::put('/resultat-quizzes/{resultatQuiz}/reset-attempts', [ResultatQuizController::class, 'resetAttempts'])->name('resultat-quizzes.reset-attempts');
    Route::put('/resultat-quizzes/{resultatQuiz}/mark-success', [ResultatQuizController::class, 'markAsSuccess'])->name('resultat-quizzes.mark-success');

    // Routes pour les discussions
    Route::post('/discussions/bulk-approve', [DiscussionController::class, 'bulkApprove'])->name('discussions.bulk-approve');
    Route::post('/discussions/bulk-delete', [DiscussionController::class, 'bulkDelete'])->name('discussions.bulk-delete');
    Route::put('/discussions/{discussion}/approve', [DiscussionController::class, 'approve'])->name('discussions.approve');
    Route::put('/discussions/{discussion}/disapprove', [DiscussionController::class, 'disapprove'])->name('discussions.disapprove');
    Route::resource('discussions', DiscussionController::class);

    // Routes pour les évaluations de cours
    Route::resource('course-ratings', CourseRatingController::class);
    Route::put('/course-ratings/{courseRating}/approve', [CourseRatingController::class, 'approve'])->name('course-ratings.approve');
    Route::put('/course-ratings/{courseRating}/disapprove', [CourseRatingController::class, 'disapprove'])->name('course-ratings.disapprove');
    Route::post('/course-ratings/{courseRating}/respond', [CourseRatingController::class, 'respond'])->name('course-ratings.respond');
    Route::post('/course-ratings/bulk-approve', [CourseRatingController::class, 'bulkApprove'])->name('course-ratings.bulk-approve');

    // Routes pour les réponses aux discussions
    Route::resource('response-discussions', ResponseDiscussionController::class);
    Route::put('/response-discussions/{responseDiscussion}/approve', [ResponseDiscussionController::class, 'approve'])->name('response-discussions.approve');
    Route::put('/response-discussions/{responseDiscussion}/disapprove', [ResponseDiscussionController::class, 'disapprove'])->name('response-discussions.disapprove');

    // Routes pour les tickets de support
    Route::get('/support-tickets', [SupportTicketController::class, 'index'])->name('support-tickets.index');
    Route::get('/support-tickets/{id}', [SupportTicketController::class, 'show'])->name('support-tickets.show');
    Route::post('/support-tickets/{id}/respond', [SupportTicketController::class, 'respond'])->name('support-tickets.respond');
    Route::put('/support-tickets/{id}/toggle-status', [SupportTicketController::class, 'toggleStatus'])->name('support-tickets.toggle-status');
    Route::delete('/support-tickets/{id}', [SupportTicketController::class, 'destroy'])->name('support-tickets.destroy');

    // Routes pour le chat (conversations chatbot)
    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ChatConversationController::class, 'index'])->name('index');
        Route::get('/unread', [\App\Http\Controllers\ChatConversationController::class, 'getUnreadConversations'])->name('unread');
        Route::get('/{conversation}', [\App\Http\Controllers\ChatConversationController::class, 'show'])->name('show');
        Route::post('/{conversation}/take-over', [\App\Http\Controllers\ChatConversationController::class, 'takeOver'])->name('take-over');
        Route::post('/{conversation}/release', [\App\Http\Controllers\ChatConversationController::class, 'release'])->name('release');
        Route::post('/{conversation}/close', [\App\Http\Controllers\ChatConversationController::class, 'close'])->name('close');
        Route::post('/{conversation}/send', [\App\Http\Controllers\ChatConversationController::class, 'sendMessage'])->name('send');
        Route::get('/{conversation}/messages', [\App\Http\Controllers\ChatConversationController::class, 'getMessages'])->name('messages');
        Route::get('/{conversation}/status', [\App\Http\Controllers\ChatConversationController::class, 'getStatus'])->name('status');
        Route::delete('/{conversation}', [\App\Http\Controllers\ChatConversationController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-delete', [\App\Http\Controllers\ChatConversationController::class, 'bulkDelete'])->name('bulk-delete');
    });

    // Referral Management
    Route::prefix('referrals')->name('referrals.')->group(function () {
        Route::get('/',                         [\App\Http\Controllers\ReferralController::class, 'index'])    ->name('index');
        Route::patch('/{id}/override',          [\App\Http\Controllers\ReferralController::class, 'override']) ->name('override');
        Route::delete('/{id}',                  [\App\Http\Controllers\ReferralController::class, 'destroy'])  ->name('destroy');
    });
    });
