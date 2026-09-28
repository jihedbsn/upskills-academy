<?php

namespace App\Http\Controllers;

use App\Services\AchievementService;
use Illuminate\Http\Request;

class AchievementController extends Controller
{
    protected AchievementService $achievementService;

    public function __construct(AchievementService $achievementService)
    {
        $this->achievementService = $achievementService;
    }

    /**
     * Display the achievements page.
     */
    public function index()
    {
        return $this->achievementService->index();
    }

    /**
     * Show the form for editing the achievements.
     */
    public function edit()
    {
        return $this->achievementService->edit();
    }

    /**
     * Update the achievements in storage.
     */
    public function update(Request $request)
    {
        return $this->achievementService->update($request);
    }
}
