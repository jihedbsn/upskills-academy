<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccueilController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SignupController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\StudyController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ResultatQuizController;
use App\Http\Controllers\DiscussionController;
use App\Http\Controllers\CourseRatingController;
use App\Http\Controllers\ResponseDiscussionController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PartnershipController;
use App\Http\Controllers\PartnershipCourseController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ChatBotController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StudentStageCourseController;
use App\Http\Controllers\PracticalExamController;
use App\Http\Controllers\StudentSuccessController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\StudentVideoExamController;
use App\Http\Controllers\StageDocumentController;
use App\Http\Controllers\Admin\ChatBotAdminController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\GraduateController;
use App\Http\Controllers\HistoriqueQuizController;
use App\Http\Controllers\ForgotPasswordController;

// Redirect root to default locale (Arabic)
Route::get('/', function () {
    return redirect('en');
});

// Stripe Webhook (must be outside CSRF protection)
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])->name('stripe.webhook');

// Facebook/Instagram/Messenger Webhook (must be outside CSRF protection)
Route::get('/webhook/facebook', [\App\Http\Controllers\FacebookWebhookController::class, 'verify'])->name('facebook.webhook.verify');
Route::post('/webhook/facebook', [\App\Http\Controllers\FacebookWebhookController::class, 'handle'])->name('facebook.webhook.handle');

// Facebook data deletion callback
Route::get('/facebook-data-deletion', function () {
    $userId = request('signed_request');
    // TODO: find and delete data corresponding to $userId

    return view('confirmation-suppression');
})->name('facebook.data-deletion');

Route::get('/confirmation-suppression', function () {
    return view('confirmation-suppression');
})->name('facebook.data-deletion.confirmation');

// Social Login Routes (Keep outside to preserve callback URLs)
Route::get('/login/facebook', [LoginController::class, 'redirectToFacebook'])->name('login.facebook');
Route::get('/login/facebook/callback', [LoginController::class, 'handleFacebookCallback'])->name('login.facebook.callback');
Route::get('/login/google', [LoginController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/auth/google/callback', [LoginController::class, 'handleGoogleCallback'])->name('login.google.callback');

// Certificate Redirect Route (for QR codes pointing to new site)
Route::get('/certificates/{serialnumber}', function ($serialnumber) {
    return redirect()->away('https://www.old.swedish-academy.se/certificates/' . $serialnumber);
})->name('certificate.redirect');

// Notification API Routes (Keep outside to avoid breaking AJAX calls)
Route::middleware('auth')->prefix('api/notifications')->group(function () {
    $controller = \App\Http\Controllers\Api\NotificationController::class;

    Route::get('/', [$controller, 'index']);
    Route::get('/unread-count', [$controller, 'unreadCount']);
    Route::get('/counts-by-type', [$controller, 'getCountsByType']);
    Route::post('/{id}/mark-as-read', [$controller, 'markAsRead']);
    Route::post('/mark-all-as-read', [$controller, 'markAllAsRead']);
    Route::delete('/{id}', [$controller, 'destroy']);
});

// Social Authentication Callback Routes (outside locale group for OAuth compatibility)
Route::get('/login/facebook/callback', [App\Http\Controllers\SocialAuthController::class, 'handleFacebookCallback'])->name('login.facebook.callback.no-locale');
Route::get('/login/google/callback', [App\Http\Controllers\SocialAuthController::class, 'handleGoogleCallback'])->name('login.google.callback.no-locale');

// Main Application Routes with Locale Prefix
Route::group([
    'prefix' => '{locale}',
    'where' => ['locale' => 'en|ar|fr']
], function() {

    Route::get('/', [AccueilController::class, 'index'])->name('accueil.index');

    // Authentication routes
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Social Authentication routes
    Route::get('/login/facebook', [App\Http\Controllers\SocialAuthController::class, 'redirectToFacebook'])->name('login.facebook');
    Route::get('/login/facebook/callback', [App\Http\Controllers\SocialAuthController::class, 'handleFacebookCallback'])->name('login.facebook.callback');
    Route::get('/login/google', [App\Http\Controllers\SocialAuthController::class, 'redirectToGoogle'])->name('login.google');
    Route::get('/login/google/callback', [App\Http\Controllers\SocialAuthController::class, 'handleGoogleCallback'])->name('login.google.callback');

    // Signup routes
    Route::get('/signup', [SignupController::class, 'index'])->name('signup');
    Route::post('/signup', [SignupController::class, 'signup'])->name('signup.post');

    // Forgot Password routes
    Route::get('/forgot-password', [ForgotPasswordController::class, 'index'])->name('forgot-password');
    Route::post('/send-code', [ForgotPasswordController::class, 'sendCode'])->name('send-code');
    Route::get('/verify-code/{email}', [ForgotPasswordController::class, 'verifyCodePage'])->name('verify-code')->where('email', '[A-Za-z0-9+/=]+');
    Route::post('/verify-code', [ForgotPasswordController::class, 'verifyCode'])->name('verify-code-post');
    Route::get('/reset-password/{email}/{code}', [ForgotPasswordController::class, 'resetPasswordPage'])->name('reset-password')->where(['email' => '[A-Za-z0-9+/=]+', 'code' => '[0-9]+']);
    Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('reset-password-post');

    // Contact routes
    Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
    Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit')->middleware('throttle:5,1'); // 5 requests per minute

    // Newsletter subscription
    Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

    // Graduates routes
    Route::get('/graduates', [GraduateController::class, 'index'])->name('graduates.index');
    Route::get('/graduates/year/{year}', [GraduateController::class, 'year'])->name('graduates.year');
    Route::get('/graduates/certificate/{serialnumber}', [GraduateController::class, 'certificate'])->name('graduates.certificate');
    Route::get('/graduates/api/data', [GraduateController::class, 'apiData'])->name('graduates.api.data');

    // Books routes
    Route::get('/books', [BookController::class, 'index'])->name('books.index');
    Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');

    // Study videos routes
    Route::get('/study/{study}/videos', [StudyController::class, 'showVideos'])->name('study.videos');
    Route::get('/study/{study}/videos-template', [StudyController::class, 'showVideosTemplate'])->name('study.videos.template');

    // Cart routes
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{book}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/add-course/{product}', [CartController::class, 'addCourse'])->name('cart.add-course');
    Route::post('/cart/buy-now-course/{product}', [CartController::class, 'buyNowCourse'])->name('cart.buy-now-course');
    Route::post('/cart/add-package/{package}', [CartController::class, 'addPackage'])->name('cart.add-package');
    Route::put('/cart/update/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::get('/cart/count', [CartController::class, 'getCartCount'])->name('cart.count');

    // Coupon routes
    Route::get('/coupons', [CouponController::class, 'index'])->name('coupons.index');
    Route::get('/coupons/{coupon}', [CouponController::class, 'show'])->name('coupons.show');
    Route::post('/cart/apply-coupon', [CartController::class, 'applyCoupon'])->name('cart.apply-coupon');
    Route::post('/cart/auto-apply-best-coupon', [CartController::class, 'autoApplyBestCoupon'])->name('cart.auto-apply-best-coupon');
    Route::delete('/cart/remove-coupon', [CartController::class, 'removeCoupon'])->name('cart.remove-coupon');
    Route::post('/cart/coupon-details', [CartController::class, 'getCouponDetails'])->name('cart.coupon-details');

    // Checkout routes
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::post('/checkout/apply-points', [CheckoutController::class, 'applyPoints'])->name('checkout.apply-points');
    Route::post('/checkout/remove-points', [CheckoutController::class, 'removePoints'])->name('checkout.remove-points');
    Route::get('/checkout/stripe/success', [CheckoutController::class, 'stripeSuccess'])->name('checkout.stripe.success');
    Route::get('/checkout/stripe/cancel', [CheckoutController::class, 'stripeCancel'])->name('checkout.stripe.cancel');

    // Filter routes
    Route::get('/filter', [FilterController::class, 'index'])->name('filter.index');
    Route::get('/courses', [FilterController::class, 'index'])->name('courses.index');
    Route::get('/courses/{product}', [FilterController::class, 'show'])->name('courses.show');

    // Package routes
    Route::get('/packages', [PackageController::class, 'index'])->name('packages.index');
    Route::get('/packages/{package:id}', [PackageController::class, 'show'])->name('packages.show');

    // Study routes
    Route::get('/study/{study}', [StudyController::class, 'show'])->name('study.show');
    Route::get('/study/{study}/pdf', [StudyController::class, 'showPdf'])->name('study.pdf');

    // Quiz routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/courses/{product}/quiz/{quiz}/start', [QuizController::class, 'start'])->name('quiz.start');
        Route::post('/courses/{product}/quiz/{quiz}/submit', [QuizController::class, 'submit'])->name('quiz.submit');
        Route::get('/courses/{product}/quiz/{quiz}/result', [QuizController::class, 'result'])->name('quiz.result');
    });

    // Nouvelles routes pour la gestion des cours
    Route::middleware(['auth'])->group(function () {
        // Routes pour les commandes
        Route::post('/orders/add-to-cart', [OrderController::class, 'addToCart'])->name('orders.add-to-cart');
        Route::post('/orders/checkout', [OrderController::class, 'checkout'])->name('orders.checkout');

        // Routes pour les résultats de quiz
        Route::post('/quiz/{quiz}/start-quiz', [ResultatQuizController::class, 'startQuiz'])->name('quiz.start-quiz');
        Route::post('/quiz/{quiz}/submit-quiz', [ResultatQuizController::class, 'submitQuiz'])->name('quiz.submit-quiz');

        // Routes pour les discussions
        Route::get('/courses/{product}/discussions', [DiscussionController::class, 'showProductDiscussions'])->name('discussions.show');
        Route::post('/discussions/store', [DiscussionController::class, 'storeStudentComment'])->name('discussions.store');

        // Routes pour les ratings
        Route::get('/courses/{product}/ratings', [CourseRatingController::class, 'showProductRatings'])->name('ratings.show');
        Route::post('/ratings/store', [CourseRatingController::class, 'storeStudentRating'])->name('ratings.store');

        // Routes pour les réponses aux discussions
        Route::post('/response-discussions/store', [ResponseDiscussionController::class, 'store'])->name('response-discussions.store');
        Route::put('/response-discussions/{responseDiscussion}', [ResponseDiscussionController::class, 'update'])->name('response-discussions.update');
        Route::delete('/response-discussions/{responseDiscussion}', [ResponseDiscussionController::class, 'destroy'])->name('response-discussions.destroy');

        // Routes pour les soumissions de stage
        Route::post('/student-stage-courses', [StudentStageCourseController::class, 'store'])->name('student-stage-courses.store');
        Route::get('/student-stage-courses/student-submission/{productId}', [StudentStageCourseController::class, 'getStudentSubmission'])->name('student-stage-courses.student-submission');

        // Routes pour les examens pratiques
        Route::get('/student-dashboard/practical-exams', [PracticalExamController::class, 'index'])->name('student.practical-exam.index');
        Route::get('/student-dashboard/courses/{product}/practical-exam', [PracticalExamController::class, 'show'])->name('student.practical-exam.show');
        Route::post('/student-dashboard/courses/{product}/practical-exam/start', [PracticalExamController::class, 'start'])->name('student.practical-exam.start');
        Route::post('/student-dashboard/courses/{product}/practical-exam/submit', [PracticalExamController::class, 'submit'])->name('student.practical-exam.submit');
        Route::post('/student-dashboard/courses/{product}/practical-exam/mark-completed', [PracticalExamController::class, 'markCompleted'])->name('student.practical-exam.mark-completed');
        Route::get('/student-dashboard/practical-exam/download-case/{attempt}', [PracticalExamController::class, 'downloadCaseFiles'])->name('student.practical-exam.download-case');

        // Routes pour les messages internes
        Route::prefix('student-dashboard/messages')->group(function () {
            $controller = \App\Http\Controllers\StudentMessageController::class;
            Route::get('/', [$controller, 'inbox'])->name('student.messages.inbox');
            Route::get('/api/unread-count', [$controller, 'getUnreadCount'])->name('student.messages.unread-count');
            Route::get('/api/sync-status', [$controller, 'syncStatus'])->name('student.messages.sync-status');
            Route::get('/{id}', [$controller, 'show'])->name('student.messages.show')->whereNumber('id');
            Route::post('/{id}/mark-read', [$controller, 'markAsRead'])->name('student.messages.mark-read');
            Route::post('/{id}/mark-unread', [$controller, 'markAsUnread'])->name('student.messages.mark-unread');
            Route::post('/{id}/respond', [$controller, 'storeResponse'])->name('student.messages.respond');
        });

        // Routes pour les documents de stage
        Route::get('/stage-documents/product/{productId}', [StageDocumentController::class, 'getCourseDocuments'])->name('stage-documents.index');
        Route::get('/stage-documents/{document}/download', [StageDocumentController::class, 'download'])->name('stage-documents.download');

        // Routes pour les soumissions de succès
        Route::post('/student-success/{productId}/submit', [StudentSuccessController::class, 'submit'])->name('student-success.submit');
        Route::get('/student-success/{productId}/status', [StudentSuccessController::class, 'getStatus'])->name('student-success.status');

        // Routes pour l'historique des quiz
        Route::get('/historique-quiz', [HistoriqueQuizController::class, 'index'])->name('historique-quiz.index');
        Route::get('/historique-quiz/product/{productId}', [HistoriqueQuizController::class, 'index'])->name('historique-quiz.product');
        Route::get('/historique-quiz/{id}', [HistoriqueQuizController::class, 'show'])->name('historique-quiz.show');

        // Routes API pour l'historique des quiz
        Route::get('/api/student/{studentId}/historique', [HistoriqueQuizController::class, 'getStudentHistory']);
        Route::get('/api/student/{studentId}/stats', [HistoriqueQuizController::class, 'getStudentStats']);
        Route::get('/api/student/{studentId}/quiz/{quizId}/history', [HistoriqueQuizController::class, 'getQuizHistory']);
        Route::get('/api/product/{productId}/stats', [HistoriqueQuizController::class, 'getProductStats']);
        Route::get('/api/global-stats', [HistoriqueQuizController::class, 'getGlobalStats']);

        // Routes pour les soumissions d'examens vidéo
        Route::post('/student-video-exams', [StudentVideoExamController::class, 'store'])->name('student-video-exams.store');
        Route::get('/student-video-exams/student-submission/{productId}', [StudentVideoExamController::class, 'getStudentSubmission'])->name('student-video-exams.student-submission');
    });

    // Routes du chatbot
    Route::prefix('chatbot')->name('chatbot.')->group(function () {
        Route::get('/', [ChatBotController::class, 'index'])->name('index');
        Route::post('/start', [ChatBotController::class, 'startConversation'])->name('start');
        Route::post('/send', [ChatBotController::class, 'sendMessage'])->name('send');
        Route::get('/messages', [ChatBotController::class, 'getMessages'])->name('messages');
        Route::post('/close', [ChatBotController::class, 'closeConversation'])->name('close');
        Route::post('/send-to-admin', [ChatBotController::class, 'sendToAdmin'])->name('send-to-admin');
        Route::get('/faq', [ChatBotController::class, 'getFaq'])->name('faq');
        Route::get('/stats', [ChatBotController::class, 'getStats'])->name('stats');
    });

    // Routes admin (protégées par middleware admin)
    Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
        // Gestion des produits/cours
        Route::resource('products', ProductController::class);

        // Gestion des commandes
        Route::resource('orders', OrderController::class);

        // Gestion des résultats de quiz
        Route::resource('resultat-quizzes', ResultatQuizController::class);
        Route::put('/resultat-quizzes/{resultatQuiz}/update-success', [ResultatQuizController::class, 'adminUpdateSuccess'])->name('resultat-quizzes.update-success');

        // Gestion du chatbot
        Route::prefix('chatbot')->name('chatbot.')->group(function () {
            Route::get('/', [ChatBotAdminController::class, 'index'])->name('index');
            Route::get('/responses', [ChatBotAdminController::class, 'responses'])->name('responses');
            Route::get('/responses/create', [ChatBotAdminController::class, 'create'])->name('responses.create');
            Route::post('/responses', [ChatBotAdminController::class, 'store'])->name('responses.store');
            Route::get('/responses/{response}/edit', [ChatBotAdminController::class, 'edit'])->name('responses.edit');
            Route::put('/responses/{response}', [ChatBotAdminController::class, 'update'])->name('responses.update');
            Route::delete('/responses/{response}', [ChatBotAdminController::class, 'destroy'])->name('responses.destroy');
            Route::get('/conversations', [ChatBotAdminController::class, 'conversations'])->name('conversations');
            Route::get('/conversations/{conversation}', [ChatBotAdminController::class, 'showConversation'])->name('conversations.show');
            Route::get('/stats', [ChatBotAdminController::class, 'getStats'])->name('stats');
        });
        Route::put('/resultat-quizzes/{resultatQuiz}/reset-attempts', [ResultatQuizController::class, 'resetAttempts'])->name('resultat-quizzes.reset-attempts');

        // Gestion des discussions
        Route::resource('discussions', DiscussionController::class);
        Route::put('/discussions/{discussion}/approve', [DiscussionController::class, 'approve'])->name('discussions.approve');
        Route::put('/discussions/{discussion}/disapprove', [DiscussionController::class, 'disapprove'])->name('discussions.disapprove');
        Route::post('/discussions/bulk-approve', [DiscussionController::class, 'bulkApprove'])->name('discussions.bulk-approve');

        // Gestion des ratings
        Route::resource('course-ratings', CourseRatingController::class);
        Route::put('/course-ratings/{courseRating}/approve', [CourseRatingController::class, 'approve'])->name('course-ratings.approve');
        Route::put('/course-ratings/{courseRating}/disapprove', [CourseRatingController::class, 'disapprove'])->name('course-ratings.disapprove');
        Route::post('/course-ratings/bulk-approve', [CourseRatingController::class, 'bulkApprove'])->name('course-ratings.bulkApprove');

        // Gestion des réponses aux discussions
        Route::put('/response-discussions/{responseDiscussion}/approve', [ResponseDiscussionController::class, 'approve'])->name('response-discussions.approve');
        Route::put('/response-discussions/{responseDiscussion}/disapprove', [ResponseDiscussionController::class, 'disapprove'])->name('response-discussions.disapprove');

        // Gestion des soumissions de stage
        Route::get('/student-stage-courses/{studentStageCourse}', [StudentStageCourseController::class, 'show'])->name('student-stage-courses.show');
        Route::put('/student-stage-courses/{studentStageCourse}/validation', [StudentStageCourseController::class, 'updateValidation'])->name('student-stage-courses.update-validation');
        Route::get('/student-stage-courses/product/{productId}/submissions', [StudentStageCourseController::class, 'getSubmissionsForProduct'])->name('student-stage-courses.product-submissions');
        Route::get('/student-stage-courses/{studentStageCourse}/download/{fileNumber}', [StudentStageCourseController::class, 'downloadFile'])->name('student-stage-courses.download-file');

        // Gestion des soumissions de succès
        Route::get('/student-success/{productId}/submissions', [StudentSuccessController::class, 'getSubmissions'])->name('student-success.submissions');
        Route::post('/student-success/{productId}/validate/{studentId}', [StudentSuccessController::class, 'validate'])->name('student-success.validate');
        Route::get('/student-success/{productId}/statistics', [StudentSuccessController::class, 'getStatistics'])->name('student-success.statistics');

        // Gestion des soumissions d'examens vidéo
        Route::get('/student-video-exams', [StudentVideoExamController::class, 'index'])->name('student-video-exams.index');
        Route::put('/student-video-exams/{id}/status', [StudentVideoExamController::class, 'updateStatus'])->name('student-video-exams.update-status');
    });

    // Page routes
    Route::get('/pages', [PageController::class, 'index'])->name('pages.index');
    Route::get('/pages/{slug}', [PageController::class, 'show'])->name('pages.show');

    // Blog routes
    Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
    Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

    // Article routes
    Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
    Route::get('/articles/{slug}', [ArticleController::class, 'show'])->name('articles.show');

    // Partnership routes
    Route::get('/partnership', [PartnershipController::class, 'index'])->name('partnership.index');
    Route::post('/partnership', [PartnershipController::class, 'store'])->name('partnership.store')->middleware('throttle:5,1'); // 5 requests per minute

    // Partnership Courses routes
    Route::get('/partnership-courses', [PartnershipCourseController::class, 'index'])->name('partnership-courses.index');
    Route::post('/partnership-courses', [PartnershipCourseController::class, 'store'])->name('partnership-courses.store');

    // Include Student Dashboard Routes
    require __DIR__.'/student.php';

});

// Currency Routes (outside locale prefix to avoid language conflicts)
Route::prefix('currency')->name('currency.')->group(function () {
    Route::get('/', [\App\Http\Controllers\CurrencyController::class, 'index'])->name('index');
    Route::post('/set', [\App\Http\Controllers\CurrencyController::class, 'set'])->name('set');
    Route::get('/detect', [\App\Http\Controllers\CurrencyController::class, 'detect'])->name('detect');
    Route::post('/convert', [\App\Http\Controllers\CurrencyController::class, 'convert'])->name('convert');
    Route::get('/location', [\App\Http\Controllers\CurrencyController::class, 'location'])->name('location');
    Route::post('/redetect', [\App\Http\Controllers\CurrencyController::class, 'redetect'])->name('redetect');
});

// AI Chatbot Routes
Route::prefix('ai-chat')->name('ai-chat.')->group(function () {
    Route::get('/init', [\App\Http\Controllers\AIChatbotController::class, 'init'])->name('init');
    Route::post('/send', [\App\Http\Controllers\AIChatbotController::class, 'sendMessage'])->name('send');
    Route::post('/clear', [\App\Http\Controllers\AIChatbotController::class, 'clearHistory'])->name('clear');
    Route::get('/history', [\App\Http\Controllers\AIChatbotController::class, 'getHistory'])->name('history');
    Route::get('/poll', [\App\Http\Controllers\AIChatbotController::class, 'pollMessages'])->name('poll');
});
