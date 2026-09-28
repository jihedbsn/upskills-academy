<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'iscach',
        'categories_id',
        'period',
        'point',
        'video',
        'image',
        'promo_points',
        'langue',
        'statut',
        'online',
        'classroom',
        'teacher_id',
        'country_id',
        'certif_id',
        'certificate_generation_mode',
        'type_course',
        'goverrnement',
        'date_debut',
        'date_fin',
        'prix',
        'validity_months',
        'is_exam_video',
        'is_stage',
        'is_classroom',
        'is_zoom',
        'is_online',
        'breuillant',
        'max_exam_attempts',
        'renewal_price',
        'installment_allowed',
        'has_theoretical_exam',
        'has_practical_exam',
        'practical_exam_type',
        'is_listed',
    ];

    protected $casts = [
        'is_exam_video' => 'boolean',
        'is_stage' => 'boolean',
        'is_classroom' => 'boolean',
        'is_zoom' => 'boolean',
        'is_online' => 'boolean',
        'breuillant' => 'boolean',
        'prix' => 'decimal:2',
        'max_exam_attempts' => 'integer',
        'renewal_price' => 'decimal:2',
        'has_theoretical_exam' => 'boolean',
        'has_practical_exam' => 'boolean',
        'is_listed' => 'boolean',
        'installment_allowed' => 'boolean',
    ];

    // Relations vers tables parentes
    public function category()
    {
        return $this->belongsTo(Category::class, 'categories_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function certif()
    {
        return $this->belongsTo(Certif::class, 'certif_id');
    }

    // Relations vers tables enfants
    public function studies()
    {
        return $this->hasMany(ProductStudy::class, 'products_id')->orderBy('order');
    }

    public function variations()
    {
        return $this->hasMany(ProductVariation::class, 'products_id');
    }

    public function types()
    {
        return $this->hasMany(ProductType::class, 'products_id');
    }

    // Relation many-to-many avec les quiz
    public function quizzes()
    {
        return $this->belongsToMany(Quiz::class, 'quiz_products', 'product_id', 'quiz_id')
                    ->withPivot('nb_question_affiche', 'score_success', 'use_own_questions', 'installment_month', 'opens_after_purchase_days');
    }

    // Nouvelles relations pour le système de gestion des cours
    public function students()
    {
        return $this->belongsToMany(User::class, 'product_students', 'product_id', 'student_id')
                    ->withPivot('date', 'is_active', 'access_granted_at')
                    ->withTimestamps();
    }

    public function productStudents()
    {
        return $this->hasMany(ProductStudent::class);
    }

    /**
     * Get the content milestones for drip content.
     */
    public function contentMilestones()
    {
        return $this->hasMany(ContentMilestone::class)->orderBy('milestone_month');
    }

    /**
     * Get installment orders for this product.
     */
    public function installmentOrders()
    {
        return $this->hasMany(OrderSpecifique::class);
    }

    /**
     * Check if this product supports installment payments.
     */
    public function allowsInstallments(): bool
    {
        return $this->installment_allowed && $this->validity_months > 0;
    }

    /**
     * Get the number of installments based on course duration.
     */
    public function getInstallmentCountAttribute(): int
    {
        return $this->validity_months ?? 1;
    }

    /**
     * Get the price per installment.
     */
    public function getInstallmentPriceAttribute(): float
    {
        $count = $this->installment_count;
        return $count > 0 ? round((float) $this->prix / $count, 2) : (float) $this->prix;
    }

    /**
     * Check if a student can access the final exam for this course
     * considering installment payment status.
     */
    public function canStudentAccessFinalExamWithInstallment(int $studentId): bool
    {
        // Check for an active installment order
        $installmentOrder = $this->installmentOrders()
            ->where('student_id', $studentId)
            ->first();

        // If no installment order exists, use normal access rules
        if (!$installmentOrder) {
            return true;
        }

        // Must have Total_Due = 0
        return $installmentOrder->canAccessFinalExam();
    }

    /**
     * Check if a student can receive a certificate for this course
     * considering installment payment status.
     */
    public function canStudentReceiveCertificateWithInstallment(int $studentId): bool
    {
        $installmentOrder = $this->installmentOrders()
            ->where('student_id', $studentId)
            ->first();

        if (!$installmentOrder) {
            return true;
        }

        return $installmentOrder->canReceiveCertificate();
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function resultatQuizzes()
    {
        return $this->hasMany(ResultatQuiz::class);
    }

    public function discussions()
    {
        return $this->hasMany(Discussion::class);
    }

    public function courseRatings()
    {
        return $this->hasMany(CourseRating::class);
    }

    public function responseQuizzes()
    {
        return $this->hasMany(ResponseQuiz::class);
    }

    public function zoomMeetings()
    {
        return $this->hasMany(ZoomMeeting::class);
    }

    public function courseSessions()
    {
        return $this->hasMany(CourseSession::class);
    }

    public function stageDocuments()
    {
        return $this->hasMany(StageDocument::class);
    }

    public function trainingCases()
    {
        return $this->belongsToMany(TrainingCase::class, 'product_training_cases');
    }

    public function practicalExamAttempts()
    {
        return $this->hasMany(PracticalExamAttempt::class);
    }

    // Méthodes pour gérer les types de cours
    public static function getCourseTypeOptions()
    {
        return [
            'fa' => 'Foundation English',
            'fi' => 'Fluency & Interaction',
            'pt' => 'Proficiency Track',
            'autres' => 'Autres'
        ];
    }

    public function getCourseTypeLabelAttribute()
    {
        $options = self::getCourseTypeOptions();
        return $options[$this->type_course] ?? $this->type_course;
    }

    // Méthodes pour filtrer les quiz par type
    public function quizQuizzes()
    {
        return $this->quizzes()->whereHas('type', function($query) {
            $query->where('titre', 'like', '%quiz%');
        });
    }

    public function examQuizzes()
    {
        return $this->quizzes()->whereHas('type', function($query) {
            $query->where('titre', 'like', '%exam%');
        });
    }

    // Accesseurs pour les champs multilingues
    public function getTitreAttribute()
    {
        $variation = $this->variations()->byLanguage()->first();
        return $variation ? $variation->name : 'Course Title';
    }

    public function getDescriptionShortAttribute()
    {
        $variation = $this->variations()->byLanguage()->first();
        return $variation ? $variation->short_description : '';
    }

    public function getDescriptionAttribute()
    {
        $variation = $this->variations()->byLanguage()->first();
        return $variation ? $variation->description : '';
    }

    // Accesseur pour le prix formaté
    public function getPrixFormattedAttribute()
    {
        if ($this->prix == 0) {
            return __('messages.free');
        }
        return '$' . number_format($this->prix, 2);
    }

    // Accesseur pour l'image
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return config('app.file_url') . 'uploads/products/images/' . $this->image;
        }
        return asset('assets/img/courses/1.jpg'); // Image par défaut
    }

    // Accesseur pour le type
    public function getTypeAttribute()
    {
        if ($this->online) return 'internet';
        if ($this->classroom) return 'classroom';
        return 'zoom'; // Par défaut
    }

    // Accesseur pour le niveau
    public function getNiveauAttribute()
    {
        // Logique pour déterminer le niveau basé sur les points ou autres critères
        if ($this->point <= 10) return 'beginner';
        if ($this->point <= 20) return 'intermediate';
        if ($this->point <= 30) return 'advanced';
        return 'expert';
    }

    // Accesseur pour le nombre de leçons
    public function getLessonsCountAttribute()
    {
        return $this->studies()->count();
    }

    // Accesseur pour la durée
    public function getDurationAttribute()
    {
        // Calculer la durée totale depuis les études
        $totalDuration = 0;
        $studies = $this->studies()->with('resource')->get();

        foreach ($studies as $study) {
            if ($study->resource && $study->resource->duration) {
                $totalDuration += (int)$study->resource->duration;
            }
        }

        if ($totalDuration > 0) {
            return $totalDuration . ' min';
        }

        // Fallback sur le champ period si pas d'études
        if ($this->period) {
            return $this->period . 'h';
        }

        return null;
    }

    /**
     * Get the variation for the current language
     */
    public function getCurrentVariationAttribute()
    {
        return $this->variations()->byLanguage()->first();
    }

    /**
     * Get the title from the current variation
     */
    public function getVariationTitleAttribute()
    {
        $variation = $this->getCurrentVariationAttribute();
        return $variation ? $variation->name : 'Titre non disponible';
    }

    // Méthodes pour la gestion des accès étudiants
    public function hasStudentAccess($studentId)
    {
        return $this->productStudents()
                    ->where('student_id', $studentId)
                    ->where('is_active', true)
                    ->exists();
    }

    public function grantStudentAccess($studentId)
    {
        return $this->productStudents()->updateOrCreate(
            ['student_id' => $studentId],
            [
                'date' => now()->toDateString(),
                'is_active' => true,
                'access_granted_at' => now()
            ]
        );
    }

    public function revokeStudentAccess($studentId)
    {
        $productStudent = $this->productStudents()
                               ->where('student_id', $studentId)
                               ->first();

        if ($productStudent) {
            $productStudent->revokeAccess();
        }
    }

    // Méthodes pour les quiz
    public function getStudentQuizResult($studentId, $quizId)
    {
        return $this->resultatQuizzes()
                    ->where('student_id', $studentId)
                    ->where('quiz_id', $quizId)
                    ->first();
    }

    public function canStudentTakeQuiz($studentId, $quizId)
    {
        $result = $this->getStudentQuizResult($studentId, $quizId);

        if (!$result) {
            return true; // Première tentative
        }

        return $result->canRetake();
    }

    public function isStudentBlockedFromQuiz($studentId, $quizId)
    {
        $result = $this->getStudentQuizResult($studentId, $quizId);

        if (!$result) {
            return false;
        }

        return $result->isBlocked();
    }

    // Méthodes pour les examens vidéo
    public function requiresVideoExam()
    {
        return $this->is_exam_video;
    }

    // Méthodes pour les stages
    public function isStage()
    {
        return $this->is_stage;
    }

    public function requiresStage()
    {
        return $this->is_stage;
    }

    // Méthodes pour les types de formation
    public function isClassroom()
    {
        return $this->is_classroom;
    }

    public function isZoom()
    {
        return $this->is_zoom;
    }

    public function isOnline()
    {
        return $this->is_online;
    }

    public function hasClassroomTraining()
    {
        return $this->is_classroom;
    }

    public function hasZoomTraining()
    {
        return $this->is_zoom;
    }

    public function hasOnlineTraining()
    {
        return $this->is_online;
    }

    public function canStudentTakeVideoExam($studentId)
    {
        if (!$this->requiresVideoExam()) {
            return false;
        }

        // Vérifier que tous les quiz sont réussis
        $quizResults = $this->resultatQuizzes()
                           ->where('student_id', $studentId)
                           ->where('success', false)
                           ->count();

        return $quizResults === 0;
    }

    // Méthodes pour les discussions et ratings
    public function getApprovedDiscussions()
    {
        return $this->discussions()->approved()->with('student', 'responses.admin');
    }

    public function getAllDiscussions()
    {
        return $this->discussions()->with('student', 'responses.admin')->orderBy('created_at', 'desc');
    }

    public function getApprovedRatings()
    {
        return $this->courseRatings()->approved()->with('student');
    }

    public function getAverageRating()
    {
        return $this->courseRatings()->approved()->avg('rating') ?? 0;
    }

    public function getRatingsCount()
    {
        return $this->courseRatings()->approved()->count();
    }

    /**
     * Vérifier si un étudiant a complété le cours
     */
    public function hasStudentCompleted($studentId)
    {
        // Vérifier que l'étudiant a accès au cours
        if (!$this->hasStudentAccess($studentId)) {
            return false;
        }

        // Récupérer tous les quiz du cours
        $quizzes = $this->quizzes;

        if ($quizzes->isEmpty()) {
            return true; // Pas de quiz = cours complété
        }

        // Vérifier que tous les quiz sont réussis
        foreach ($quizzes as $quiz) {
            $result = $this->getStudentQuizResult($studentId, $quiz->id);
            if (!$result || !$result->success) {
                return false;
            }
        }

        return true;
    }

    /**
     * Vérifier si un étudiant peut noter le cours
     */
    public function canStudentRate($studentId)
    {
        // Vérifier que l'étudiant a complété le cours
        if (!$this->hasStudentCompleted($studentId)) {
            return false;
        }

        // Vérifier qu'il n'a pas déjà noté le cours
        $existingRating = $this->courseRatings()
                              ->where('student_id', $studentId)
                              ->first();

        return !$existingRating;
    }

    /**
     * Vérifier si un étudiant a déjà noté le cours
     */
    public function hasStudentRated($studentId)
    {
        return $this->courseRatings()
                   ->where('student_id', $studentId)
                   ->exists();
    }

    /**
     * Obtenir la note d'un étudiant pour ce cours
     */
    public function getStudentRating($studentId)
    {
        return $this->courseRatings()
                   ->where('student_id', $studentId)
                   ->first();
    }

    /**
     * Vérifier si un étudiant peut passer un examen spécifique
     */
    public function canStudentTakeExam($studentId, $examId)
    {
        // Vérifier que l'étudiant a accès au cours
        if (!$this->hasStudentAccess($studentId)) {
            return false;
        }

        // Récupérer l'examen
        $exam = $this->quizzes()->find($examId);
        if (!$exam) {
            return false;
        }

        // Vérifier que c'est bien un examen (type_id = 1)
        if ($exam->type_id != 1) {
            return false;
        }

        // Vérifier que tous les quiz (type_id = 2) sont réussis
        $quizQuizzes = $this->quizzes()->where('type_id', 2)->get();

        foreach ($quizQuizzes as $quiz) {
            $result = $this->getStudentQuizResult($studentId, $quiz->id);
            if (!$result || !$result->success) {
                return false;
            }
        }

        return true;
    }

    /**
     * Obtenir le pourcentage de progression d'un étudiant
     */
    public function getStudentProgress($studentId)
    {
        if (!$this->hasStudentAccess($studentId)) {
            return 0;
        }

        $quizzes = $this->quizzes;
        if ($quizzes->isEmpty()) {
            return 100; // Pas de quiz = 100% de progression
        }

        $completedQuizzes = 0;
        foreach ($quizzes as $quiz) {
            $result = $this->getStudentQuizResult($studentId, $quiz->id);
            if ($result && $result->success) {
                $completedQuizzes++;
            }
        }

        return round(($completedQuizzes / $quizzes->count()) * 100);
    }



    /**
     * Obtenir les statistiques de réussite pour ce cours
     */
    public function getSuccessStatistics()
    {
        $totalStudents = $this->productStudents()->where('is_active', true)->count();

        if ($totalStudents === 0) {
            return [
                'total_students' => 0,
                'completed_students' => 0,
                'success_rate' => 0
            ];
        }

        $completedStudents = 0;
        $students = $this->productStudents()->where('is_active', true)->get();

        foreach ($students as $student) {
            if ($this->hasStudentCompleted($student->student_id)) {
                $completedStudents++;
            }
        }

        return [
            'total_students' => $totalStudents,
            'completed_students' => $completedStudents,
            'success_rate' => round(($completedStudents / $totalStudents) * 100, 1)
        ];
    }
}
