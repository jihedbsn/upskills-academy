<?php

namespace App\Services;

use App\Models\Achievement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AchievementService
{
    public function index()
    {
        try {
            $achievement = Achievement::getInstance();

            return view('achievements.index', compact('achievement'));
        } catch (\Exception $e) {
            Log::error('Error loading achievements: ' . $e->getMessage());
            return back()->with('error', 'Error loading achievements data.');
        }
    }

    public function edit()
    {
        try {
            $achievement = Achievement::getInstance();

            return view('achievements.edit', compact('achievement'));
        } catch (\Exception $e) {
            Log::error('Error loading achievements edit form: ' . $e->getMessage());
            return back()->with('error', 'Error loading edit form.');
        }
    }

    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'training_programs' => 'required|integer|min:0',
                'registered_students' => 'required|integer|min:0',
                'academy_books' => 'required|integer|min:0',
                'ready_instructors' => 'required|integer|min:0',
            ]);

            $achievement = Achievement::getInstance();
            $achievement->update($validated);

            return redirect()->route('admin.achievements.index')
                ->with('success', 'Achievements updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating achievements: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Error updating achievements: ' . $e->getMessage());
        }
    }
}
