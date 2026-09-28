<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AbandonedCartService;
use Illuminate\Http\Request;

class AbandonedCartApiController extends Controller
{
    protected $abandonedCartService;

    public function __construct(AbandonedCartService $abandonedCartService)
    {
        $this->abandonedCartService = $abandonedCartService;
    }

    /**
     * Mark abandoned cart as converted.
     */
    public function markConverted(Request $request)
    {
        $request->validate([
            'student_id' => 'required|integer'
        ]);

        $success = $this->abandonedCartService->markAsConverted($request->student_id);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Cart marked as converted' : 'No abandoned cart found'
        ]);
    }
}
