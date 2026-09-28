<?php

namespace App\Models;

trait ProductSuccessMethods
{
    // ===== NOUVELLES MÉTHODES POUR LA GESTION DES SUCCÈS =====

    /**
     * Vérifier si un étudiant peut soumettre un stage
     * Note: Quiz completion is no longer required - only purchase access
     */
    public function canStudentSubmitStage($studentId)
    {
        // Vérifier que l'étudiant a accès au cours
        if (!$this->hasStudentAccess($studentId)) {
            return false;
        }

        // Vérifier que le cours a un stage
        if (!$this->is_stage) {
            return false;
        }

        // Check if student already has a submission (pending or approved)
        $existingSubmission = StudentStageCourse::where('student_id', $studentId)
            ->where('product_id', $this->id)
            ->where('is_valid', '!=', -1) // Allow resubmission only if rejected
            ->exists();

        if ($existingSubmission) {
            return false; // Student already has an active submission
        }

        // Quiz completion is no longer required - students can submit stage anytime after purchase
        return true;
    }

    /**
     * Vérifier si un étudiant peut soumettre un examen vidéo
     * Note: Quiz/exam completion is no longer required - only purchase and stage validation (if applicable)
     */
    public function canStudentSubmitVideoExam($studentId)
    {
        // Vérifier que l'étudiant a accès au cours
        if (!$this->hasStudentAccess($studentId)) {
            return false;
        }

        // Vérifier que le cours a un examen vidéo
        if (!$this->is_exam_video) {
            return false;
        }

        // Vérifier que le stage est validé si le cours en a un
        if ($this->is_stage) {
            $stageSubmission = $this->studentStageCourses()
                                 ->where('student_id', $studentId)
                                 ->where('is_valid', 1)
                                 ->first();

            if (!$stageSubmission) {
                return false;
            }
        }

        return true;
    }

    /**
     * Vérifier si un étudiant peut soumettre sa réussite finale
     */
    public function canStudentSubmitSuccess($studentId)
    {
        // Vérifier que l'étudiant a accès au cours
        if (!$this->hasStudentAccess($studentId)) {
            return false;
        }

        // Vérifier que tous les quiz sont terminés avec succès
        $quizQuizzes = $this->quizzes()->where('type_id', 2)->get();
        foreach ($quizQuizzes as $quiz) {
            $result = $this->getStudentQuizResult($studentId, $quiz->id);
            if (!$result || !$result->success) {
                return false;
            }
        }

        // Vérifier que tous les examens sont terminés avec succès
        $examQuizzes = $this->quizzes()->where('type_id', 1)->get();
        foreach ($examQuizzes as $exam) {
            $result = $this->getStudentQuizResult($studentId, $exam->id);
            if (!$result || !$result->success) {
                return false;
            }
        }

        // Si le cours a un stage, vérifier qu'il est validé
        if ($this->is_stage) {
            $stageSubmission = $this->studentStageCourses()
                                 ->where('student_id', $studentId)
                                 ->where('is_valid', 1)
                                 ->first();

            if (!$stageSubmission) {
                return false;
            }
        }

        // Si le cours a un examen vidéo, vérifier qu'il est soumis
        if ($this->is_exam_video) {
            $videoSubmission = $this->studentSuccesses()
                                 ->where('student_id', $studentId)
                                 ->whereNotNull('lien_video')
                                 ->first();

            if (!$videoSubmission) {
                return false;
            }
        }

        return true;
    }

    /**
     * Créer ou mettre à jour la soumission de succès d'un étudiant
     */
    public function submitStudentSuccess($studentId, $lienVideo = null)
    {
        // Vérifier que l'étudiant peut soumettre sa réussite
        if (!$this->canStudentSubmitSuccess($studentId)) {
            return false;
        }

        // Si le cours a un examen vidéo, la soumission nécessite un lien vidéo
        if ($this->is_exam_video && !$lienVideo) {
            return false;
        }

        // Créer ou mettre à jour la soumission
        $success = $this->studentSuccesses()->updateOrCreate(
            ['student_id' => $studentId],
            [
                'lien_video' => $lienVideo,
                'submitted_at' => now(),
                'success' => 0 // En attente de validation admin
            ]
        );

        return $success;
    }

    /**
     * Obtenir la soumission de succès d'un étudiant
     */
    public function getStudentSuccess($studentId)
    {
        return $this->studentSuccesses()
                   ->where('student_id', $studentId)
                   ->first();
    }

    /**
     * Vérifier si un étudiant a déjà soumis sa réussite
     */
    public function hasStudentSubmittedSuccess($studentId)
    {
        return $this->studentSuccesses()
                   ->where('student_id', $studentId)
                   ->whereNotNull('submitted_at')
                   ->exists();
    }

    /**
     * Vérifier si un étudiant a réussi le cours
     */
    public function hasStudentSucceeded($studentId)
    {
        $success = $this->getStudentSuccess($studentId);
        return $success && $success->isApproved();
    }

    /**
     * Obtenir le statut de réussite d'un étudiant
     */
    public function getStudentSuccessStatus($studentId)
    {
        $success = $this->getStudentSuccess($studentId);

        if (!$success) {
            return 'not_submitted';
        }

        if ($success->isPending()) {
            return 'pending';
        }

        if ($success->isApproved()) {
            return 'approved';
        }

        if ($success->isRejected()) {
            return 'rejected';
        }

        return 'unknown';
    }

    /**
     * Valider ou rejeter la soumission d'un étudiant
     */
    public function validateStudentSuccess($studentId, $approved = true, $adminNotes = null)
    {
        $success = $this->getStudentSuccess($studentId);

        if (!$success) {
            return false;
        }

        if ($approved) {
            $success->approve($adminNotes);
        } else {
            $success->reject($adminNotes);
        }

        return $success;
    }
}
