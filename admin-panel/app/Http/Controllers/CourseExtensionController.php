<?php

namespace App\Http\Controllers;

use App\Models\CourseExtensionOrder;
use App\Services\CourseExtensionService;
use Illuminate\Http\Request;

class CourseExtensionController extends Controller
{
    protected CourseExtensionService $courseExtensionService;

    public function __construct(CourseExtensionService $courseExtensionService)
    {
        $this->courseExtensionService = $courseExtensionService;
    }

    /**
     * Display list of all extension orders
     */
    public function index(Request $request)
    {
        return $this->courseExtensionService->index($request);
    }

    /**
     * Approve an extension order
     */
    public function approve(CourseExtensionOrder $extensionOrder)
    {
        return $this->courseExtensionService->approve($extensionOrder);
    }

    /**
     * Reject an extension order
     */
    public function reject(Request $request, CourseExtensionOrder $extensionOrder)
    {
        return $this->courseExtensionService->reject($request, $extensionOrder);
    }

    /**
     * Download the payment receipt file
     */
    public function downloadReceipt(CourseExtensionOrder $extensionOrder)
    {
        return $this->courseExtensionService->downloadReceipt($extensionOrder);
    }
}
