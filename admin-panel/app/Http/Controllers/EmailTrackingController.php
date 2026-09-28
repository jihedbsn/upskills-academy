<?php

namespace App\Http\Controllers;

use App\Models\EmailLog;
use App\Support\StudentFrontendUrl;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class EmailTrackingController extends Controller
{
    /**
     * 1×1 tracking pixel (many clients block remote images until "Display images").
     */
    public function pixel(string $token): Response
    {
        $this->recordOpen($token);

        $gif = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');

        return response($gif, 200, [
            'Content-Type' => 'image/gif',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
        ]);
    }

    /**
     * Link-based open confirmation: records read then redirects (works when images are blocked).
     */
    public function confirmRedirect(string $token): RedirectResponse
    {
        $this->recordOpen($token);

        $landing = StudentFrontendUrl::localized('en', '');

        return redirect()->away($landing);
    }

    private function recordOpen(string $token): void
    {
        if (! preg_match('/^[a-zA-Z0-9]{40}$/', $token)) {
            return;
        }

        EmailLog::query()
            ->where('tracking_token', $token)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}
