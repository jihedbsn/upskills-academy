<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\QuizApiController;
use App\Http\Controllers\Api\QuizImportController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\AbandonedCartApiController;
use App\Http\Controllers\Api\ReferralApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Quiz API Routes
|--------------------------------------------------------------------------
|
| Routes pour gérer les quiz, questions et réponses via API externe
|
*/

// Routes pour les quiz
Route::prefix('quiz')->group(function () {
    // Créer un quiz complet avec questions et réponses
    Route::post('/', [QuizApiController::class, 'storeQuiz']);

    // Insérer plusieurs quiz en une seule fois (format tableau)
    Route::post('/multiple', [QuizApiController::class, 'storeMultipleQuizzes']);

    // Récupérer tous les quiz
    Route::get('/', [QuizApiController::class, 'getQuizzes']);

    // Récupérer un quiz spécifique
    Route::get('/{id}', [QuizApiController::class, 'getQuiz']);

    // Mettre à jour un quiz
    Route::put('/{id}', [QuizApiController::class, 'updateQuiz']);

    // Supprimer un quiz
    Route::delete('/{id}', [QuizApiController::class, 'deleteQuiz']);
});

// Routes pour les questions
Route::prefix('question')->group(function () {
    // Créer une question avec ses réponses
    Route::post('/', [QuizApiController::class, 'storeQuestion']);
});

// Routes pour les réponses
Route::prefix('reponse')->group(function () {
    // Créer une réponse pour une question
    Route::post('/', [QuizApiController::class, 'storeReponse']);
});

/*
|--------------------------------------------------------------------------
| Quiz Import API Routes
|--------------------------------------------------------------------------
|
| Routes pour importer les quiz depuis l'API externe
|
*/

// Routes pour l'importation des quiz
Route::prefix('import')->group(function () {
    // Importer tous les quiz depuis l'API externe
    Route::post('/all', [QuizImportController::class, 'importFromExternalApi']);

    // Importer un quiz spécifique
    Route::post('/quiz', [QuizImportController::class, 'importSpecificQuiz']);

    // Vérifier les quiz disponibles dans l'API externe
    Route::get('/check', [QuizImportController::class, 'checkExternalQuizzes']);

    // Synchroniser les quiz (mettre à jour les existants + créer les nouveaux)
    Route::post('/sync', [QuizImportController::class, 'syncQuizzes']);

    // Récupérer la liste des quiz disponibles depuis l'API externe
    Route::get('/available-quizzes', [QuizImportController::class, 'getAvailableQuizzes'])->name('api.quiz.available-quizzes');

    // Importer les questions pour un nouveau quiz exam
    Route::get('/questions', [QuizImportController::class, 'importQuestions'])->name('api.quiz.import-questions');
});

/*
|--------------------------------------------------------------------------
| Notification API Routes
|--------------------------------------------------------------------------
|
| Routes for managing in-app notifications
|
*/
Route::middleware('auth:sanctum')->prefix('notifications')->group(function () {
    // Get all notifications
    Route::get('/', [NotificationController::class, 'index']);

    // Get unread count
    Route::get('/unread-count', [NotificationController::class, 'unreadCount']);

    // Mark notification as read
    Route::post('/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);

    // Mark all as read
    Route::post('/mark-all-as-read', [NotificationController::class, 'markAllAsRead']);

    // Delete notification
    Route::delete('/{id}', [NotificationController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| Abandoned Cart API Routes
|--------------------------------------------------------------------------
*/
Route::post('/abandoned-carts/mark-converted', [AbandonedCartApiController::class, 'markConverted']);

/*
|--------------------------------------------------------------------------
| Referral API Routes
|--------------------------------------------------------------------------
*/
Route::prefix('referrals')->group(function () {
    Route::post('/generate-code',   [ReferralApiController::class, 'generateCode']);
    Route::post('/register',        [ReferralApiController::class, 'register']);
    Route::post('/process-reward',  [ReferralApiController::class, 'processReward']);
    Route::post('/set-preference',  [ReferralApiController::class, 'setPreference']);
    Route::post('/claim-reward',    [ReferralApiController::class, 'claimReward']);
    Route::get('/student/{studentId}/stats',   [ReferralApiController::class, 'studentStats']);
    Route::get('/student/{studentId}/history', [ReferralApiController::class, 'studentHistory']);
    Route::get('/student/{studentId}/discount',[ReferralApiController::class, 'checkDiscount']);
    Route::get('/student/{studentId}/credit-balance', [ReferralApiController::class, 'creditBalance']);
    Route::post('/consume-credit',  [ReferralApiController::class, 'consumeCredit']);
});

Route::post('/email-inbound', [\App\Http\Controllers\Api\EmailInboundController::class, 'webhook']);
