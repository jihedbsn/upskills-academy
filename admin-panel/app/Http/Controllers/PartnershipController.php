<?php

namespace App\Http\Controllers;

use App\Models\Partnership;
use App\Services\PartnershipService;
use Illuminate\Http\Request;

class PartnershipController extends Controller
{
    protected PartnershipService $partnershipService;

    public function __construct(PartnershipService $partnershipService)
    {
        $this->partnershipService = $partnershipService;
    }

    public function index(Request $request)
    {
        return $this->partnershipService->index($request);
    }

    public function show(Partnership $partnership)
    {
        return $this->partnershipService->show($partnership);
    }

    public function updateStatus(Request $request, Partnership $partnership)
    {
        return $this->partnershipService->updateStatus($request, $partnership);
    }

    public function destroy(Partnership $partnership)
    {
        return $this->partnershipService->destroy($partnership);
    }

    public function downloadFile(Partnership $partnership)
    {
        return $this->partnershipService->downloadFile($partnership);
    }

    public function markAsRead(Partnership $partnership)
    {
        return $this->partnershipService->markAsRead($partnership);
    }

    public function markAllAsRead()
    {
        return $this->partnershipService->markAllAsRead();
    }
}
