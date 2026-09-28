<?php

namespace App\Http\Controllers;

use App\Services\SupportTicketService;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    public function __construct(private SupportTicketService $service) {}

    public function index(Request $request)
    {
        $data = $this->service->index($request);
        return view('tickets.index', $data);
    }

    public function show($id)
    {
        $ticket = $this->service->show($id);
        return view('tickets.show', compact('ticket'));
    }

    public function respond(Request $request, $id)
    {
        return $this->service->respond($request, $id);
    }

    public function toggleStatus($id)
    {
        return $this->service->toggleStatus($id);
    }

    public function destroy($id)
    {
        return $this->service->destroy($id);
    }
}
