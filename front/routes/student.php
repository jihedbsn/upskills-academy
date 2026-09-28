<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\InstallmentController;
use App\Http\Controllers\CourseExtensionController;
use App\Http\Controllers\ReferralController;

/*
|--------------------------------------------------------------------------
| Student Dashboard Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for the student dashboard.
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group.
|
*/

// Student Dashboard routes - protected by auth middleware
Route::middleware(['auth', 'installment.suspension'])->prefix('student-dashboard')->name('student-dashboard.')->group(function () {
    // Dashboard index
    Route::get('/', [StudentDashboardController::class, 'index'])->name('index');

    // Courses
    Route::get('/courses', [StudentDashboardController::class, 'coursesIndex'])->name('courses.index');
    Route::get('/courses/{id}', [StudentDashboardController::class, 'courseShow'])->name('courses.show');

    // History
    Route::get('/history', [StudentDashboardController::class, 'historyIndex'])->name('history.index');

    // Zoom Meetings
    Route::get('/zoom-meetings', [StudentDashboardController::class, 'zoomMeetingsIndex'])->name('zoom-meetings.index');

    // Books
    Route::get('/books', [StudentDashboardController::class, 'booksIndex'])->name('books.index');
    Route::get('/books/{id}', [StudentDashboardController::class, 'bookShow'])->name('books.show');

    // Certificates
    Route::get('/certificates', [StudentDashboardController::class, 'certificatesIndex'])->name('certificates.index');
    Route::get('/certificates/{id}', [StudentDashboardController::class, 'certificateShow'])->name('certificates.show');
    Route::get('/old-certificates', [StudentDashboardController::class, 'oldCertificate'])->name('old-certificates.index');

    // Calendar
    Route::get('/calendar', [StudentDashboardController::class, 'calendar'])->name('calendar');

    // Course Schedule
    Route::get('/course-schedule', [StudentDashboardController::class, 'courseSchedule'])->name('course-schedule');

    // Payment
    Route::get('/payment', [StudentDashboardController::class, 'payment'])->name('payment.index');
    Route::get('/payment/history', [StudentDashboardController::class, 'paymentHistory'])->name('payment.history');

    // Orders
    Route::get('/orders', [StudentDashboardController::class, 'orders'])->name('orders.index');

    // Special Offers / Coupons
    Route::get('/special-offers', [StudentDashboardController::class, 'specialOffers'])->name('special-offers.index');

    // Support
    Route::get('/support', [StudentDashboardController::class, 'support'])->name('support.index');
    Route::get('/support/tickets', [StudentDashboardController::class, 'tickets'])->name('support.tickets');
    Route::get('/support/tickets/create', [StudentDashboardController::class, 'createTicket'])->name('support.tickets.create');
    Route::post('/support/tickets', [StudentDashboardController::class, 'storeTicket'])->name('support.tickets.store');
    Route::post('/support', [StudentDashboardController::class, 'storeTicket'])->name('support.store');
    Route::post('/support/tickets/{ticket}/reply', [StudentDashboardController::class, 'storeTicketReply'])->name('support.tickets.reply');
    Route::get('/support/tickets/{ticket}/responses', [StudentDashboardController::class, 'getTicketResponses'])->name('support.tickets.responses');
    Route::post('/support/tickets/{ticket}/close', [StudentDashboardController::class, 'closeTicket'])->name('support.tickets.close');

    // Profile
    Route::get('/profile', [StudentDashboardController::class, 'profile'])->name('profile');
    Route::post('/profile/update-photo', [StudentDashboardController::class, 'updatePhoto'])->name('profile.update-photo');
    Route::post('/profile/update', [StudentDashboardController::class, 'updateProfile'])->name('profile.update');

    // Delete Account
    Route::post('/profile/delete-account', [StudentDashboardController::class, 'deleteAccount'])->name('profile.delete-account');

    // Test route for debugging
    Route::get('/profile/test-photo', function() {
        return response()->json([
            'success' => true,
            'message' => 'Test route working',
            'csrf_token' => csrf_token(),
            'student' => Auth::user() ? Auth::user()->toArray() : null
        ]);
    })->name('profile.test-photo');

    // Referrals
    Route::get('/referrals',           [ReferralController::class, 'index'])        ->name('referrals.index');
    Route::post('/referrals/preference',[ReferralController::class, 'setPreference'])->name('referrals.preference');
    Route::post('/referrals/claim',    [ReferralController::class, 'claimReward'])  ->name('referrals.claim');

    // Settings
    Route::get('/settings/language', [StudentDashboardController::class, 'settingsLanguage'])->name('settings.language');

    // Payment details
    Route::get('/payment/transaction/{transaction}', [StudentDashboardController::class, 'getTransactionDetails'])->name('payment.transaction.details');
    Route::get('/payment/receipt/{transaction}', [StudentDashboardController::class, 'downloadReceipt'])->name('payment.receipt.download');

    // =============================================
    // Course Extension Payment Routes
    // =============================================
    Route::prefix('course-extensions')->name('course-extensions.')->group(function () {
        Route::get('/', [CourseExtensionController::class, 'index'])->name('index');
        Route::get('/checkout/{product}', [CourseExtensionController::class, 'checkout'])->name('checkout');
        Route::post('/checkout/{product}/process', [CourseExtensionController::class, 'processPayment'])->name('process');
        Route::get('/stripe/success', [CourseExtensionController::class, 'stripeSuccess'])->name('stripe-success');
        Route::get('/stripe/cancel', [CourseExtensionController::class, 'stripeCancel'])->name('stripe-cancel');
    });

    // =============================================
    // Installment Payment System Routes
    // =============================================
    Route::prefix('installments')->name('installments.')->group(function () {
        // Dashboard - list all installment orders
        Route::get('/', [InstallmentController::class, 'index'])->name('index');

        // Suspended account page
        Route::get('/suspended', [InstallmentController::class, 'suspended'])->name('suspended');

        // Checkout - start installment purchase for a product
        Route::get('/checkout/{product}', [InstallmentController::class, 'checkout'])->name('checkout');

        // Create installment order then continue with chosen method (card, cash, or bank details page)
        Route::post('/checkout/{product}/start-payment', [InstallmentController::class, 'checkoutStartPayment'])->name('checkout-start-payment');

        // Legacy: create order and go to payment-options only (kept for old bookmarks / forms)
        Route::post('/checkout/{product}/process', [InstallmentController::class, 'processCheckout'])->name('process-checkout');

        // Stripe payment callbacks
        Route::get('/payment/success', [InstallmentController::class, 'paymentSuccess'])->name('payment-success');
        Route::get('/payment/cancel', [InstallmentController::class, 'paymentCancel'])->name('payment-cancel');

        // Pay next installment (POST for form submission, GET redirects to show page)
        Route::post('/{orderSpecifique}/pay', [InstallmentController::class, 'payInstallment'])->name('pay');
        Route::get('/{orderSpecifique}/pay', function($locale, $orderSpecifique) {
            return redirect()->route('student-dashboard.installments.show', ['locale' => $locale, 'orderSpecifique' => $orderSpecifique]);
        });

        // Payment options page (must be before wildcard show route)
        Route::get('/{orderSpecifique}/payment-options', [InstallmentController::class, 'paymentOptions'])->name('payment-options');

        // Show specific installment order details (wildcard route must be LAST)
        Route::get('/{orderSpecifique}', [InstallmentController::class, 'show'])->name('show');
    });
});
