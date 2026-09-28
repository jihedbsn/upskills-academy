<?php

namespace App\Services;

use App\Mail\PartnershipResponse;
use App\Models\EmailLog;
use App\Models\Partnership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PartnershipService
{
    public function index(Request $request)
    {
        $query = Partnership::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('institution_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('email', 'like', '%' . $searchTerm . '%')
                    ->orWhere('phone', 'like', '%' . $searchTerm . '%');
            });
        }

        $partnerships = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('partnerships.index', compact('partnerships'));
    }

    public function show(Partnership $partnership)
    {
        if (!$partnership->is_read) {
            $partnership->update(['is_read' => true]);
        }

        return view('partnerships.show', compact('partnership'));
    }

    public function updateStatus(Request $request, Partnership $partnership)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'notes'  => 'nullable|string|max:1000',
        ]);

        $partnership->update([
            'status' => $request->status,
            'notes'  => $request->notes,
        ]);

        try {
            Mail::to($partnership->email)->send(new PartnershipResponse($partnership));

            EmailLog::logSent(
                $partnership->email,
                'partnership_response',
                'Partnership Status: ' . ucfirst($request->status),
                null,
                $partnership->institution_name ?? $partnership->contact_name ?? null,
                'Partnership',
                $partnership->id
            );
        } catch (\Exception $e) {
            Log::error('Failed to send partnership response email: ' . $e->getMessage());

            EmailLog::logFailed(
                $partnership->email,
                'partnership_response',
                'Partnership Status: ' . ucfirst($request->status),
                $e->getMessage(),
                null,
                $partnership->institution_name ?? $partnership->contact_name ?? null,
                'Partnership',
                $partnership->id
            );
        }

        return redirect()->route('partnerships.show', $partnership)
            ->with('success', 'Partnership status updated successfully!');
    }

    public function destroy(Partnership $partnership)
    {
        if ($partnership->profile_file) {
            $userStoragePath  = base_path('../user/storage/app/public/' . $partnership->profile_file);
            $adminStoragePath = storage_path('app/public/' . $partnership->profile_file);

            if (file_exists($userStoragePath)) {
                unlink($userStoragePath);
            } elseif (file_exists($adminStoragePath)) {
                unlink($adminStoragePath);
            }
        }

        $partnership->delete();

        return redirect()->route('partnerships.index')
            ->with('success', 'Partnership request deleted successfully!');
    }

    public function downloadFile(Partnership $partnership)
    {
        if (!$partnership->profile_file) {
            return redirect()->back()->with('error', 'No file available.');
        }

        $userStoragePath  = base_path('../user/storage/app/public/' . $partnership->profile_file);
        $adminStoragePath = storage_path('app/public/' . $partnership->profile_file);

        if (file_exists($userStoragePath)) {
            return response()->download($userStoragePath);
        } elseif (file_exists($adminStoragePath)) {
            return response()->download($adminStoragePath);
        }

        return redirect()->back()->with('error', 'File not found.');
    }

    public function markAsRead(Partnership $partnership)
    {
        $partnership->update(['is_read' => true]);

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back();
    }

    public function markAllAsRead()
    {
        Partnership::where('is_read', false)->update(['is_read' => true]);

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('partnerships.index')
            ->with('success', 'All partnerships marked as read.');
    }
}
