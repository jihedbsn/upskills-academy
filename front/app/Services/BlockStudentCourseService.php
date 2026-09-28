<?php

namespace App\Services;

use App\Models\BlockStudentCourse;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BlockStudentCourseService
{
    public function isBlocked(int $courseId, ?int $studentId = null): bool
    {
        $studentId ??= Auth::id();

        if (!$studentId) {
            return false;
        }

        return BlockStudentCourse::isBlocked($courseId, $studentId);
    }

    public function blockedView(string $locale, int $courseId): View
    {
        \Illuminate\Support\Facades\URL::defaults(['locale' => $locale]);

        $course = Product::with('variations')->findOrFail($courseId);

        return view('courses.blocked', compact('course', 'locale'));
    }

    public function blockedRedirect(string $locale, int $courseId): RedirectResponse
    {
        return redirect()
            ->route('courses.show', ['locale' => $locale, 'product' => $courseId])
            ->with('error', __('messages.course_access_blocked_message'));
    }

    public function denyIfBlocked(int $courseId, string $locale, bool $asView = true): View|RedirectResponse|null
    {
        if (!$this->isBlocked($courseId)) {
            return null;
        }

        return $asView
            ? $this->blockedView($locale, $courseId)
            : $this->blockedRedirect($locale, $courseId);
    }
}
