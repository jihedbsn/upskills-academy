<?php

namespace App\Http\View\Composers;

use App\Models\StudentSuccess;
use App\Models\StudentStageCourse;
use App\Models\StudentVideoExam;
use Illuminate\View\View;

class HeaderComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view)
    {
        // Compter les éléments en attente
        $pendingStudentSuccessesCount = StudentSuccess::where('success', 0)->count();
        $pendingStageSubmissionsCount = StudentStageCourse::where('is_valid', 0)->count();
        $pendingVideoExamsCount = StudentVideoExam::where('is_valid', 0)->count();
        
        // Passer les données à la vue
        $view->with([
            'pendingStudentSuccessesCount' => $pendingStudentSuccessesCount,
            'pendingStageSubmissionsCount' => $pendingStageSubmissionsCount,
            'pendingVideoExamsCount' => $pendingVideoExamsCount,
        ]);
    }
}
