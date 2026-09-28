<?php

namespace App\Services;

use App\Models\PartnershipCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PartnershipCourseService
{
    public function index()
    {
        return view('partnership-courses.index');
    }

    public function store(Request $request, $locale)
    {
        $validator = Validator::make($request->all(), [
            'institution_name' => 'required|string|max:255',
            'institution_address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'course_title_en' => 'required|string|max:255',
            'course_type' => 'required|in:training_course,workshop',
            'delivery_method' => 'required|in:classroom,online',
            'evaluation_method' => 'required|in:self_assessment,theoretical_test,practical_test,theoretical_practical_test',
            'course_description' => 'required|string|max:2000',
            'website_url' => 'nullable|url|max:255',
        ], [
            'institution_name.required' => __('messages.institution_name_required'),
            'institution_address.required' => __('messages.institution_address_required'),
            'phone.required' => __('messages.phone_required'),
            'email.required' => __('messages.email_required'),
            'email.email' => __('messages.email_invalid'),
            'course_title_en.required' => __('messages.course_title_required'),
            'course_type.required' => __('messages.course_type_required'),
            'delivery_method.required' => __('messages.delivery_method_required'),
            'evaluation_method.required' => __('messages.evaluation_method_required'),
            'course_description.required' => __('messages.course_description_required'),
            'website_url.url' => __('messages.website_invalid'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $partnershipCourse = PartnershipCourse::create([
                'institution_name' => $request->institution_name,
                'institution_address' => $request->institution_address,
                'phone' => $request->phone,
                'email' => $request->email,
                'course_title_en' => $request->course_title_en,
                'course_type' => $request->course_type,
                'delivery_method' => $request->delivery_method,
                'evaluation_method' => $request->evaluation_method,
                'course_description' => $request->course_description,
                'website_url' => $request->website_url,
                'submitted_at' => now(),
            ]);

            Log::info('Partnership course created successfully', ['partnership_course_id' => $partnershipCourse->id]);

            return redirect()->route('partnership-courses.index', ['success' => '1']);
        } catch (\Exception $e) {
            return redirect()->route('partnership-courses.index', ['error' => urlencode($e->getMessage())]);
        }
    }

    public function admin()
    {
        $partnershipCourses = PartnershipCourse::orderBy('created_at', 'desc')->paginate(20);

        return view('partnership-courses.admin', compact('partnershipCourses'));
    }

    public function updateStatus(Request $request, PartnershipCourse $partnershipCourse)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'notes' => 'nullable|string|max:1000',
        ]);

        $partnershipCourse->update([
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return redirect()->back()
            ->with('success', __('messages.partnership_course_status_updated'));
    }
}
