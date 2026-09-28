<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Services\PageService;
use Illuminate\Http\Request;

class PageController extends Controller
{
    protected PageService $pageService;

    public function __construct(PageService $pageService)
    {
        $this->pageService = $pageService;
    }

    public function index(Request $request)
    {
        return $this->pageService->index($request);
    }

    public function create()
    {
        return $this->pageService->create();
    }

    public function store(Request $request)
    {
        return $this->pageService->store($request);
    }

    public function show(Page $page)
    {
        return $this->pageService->show($page);
    }

    public function edit(Page $page)
    {
        return $this->pageService->edit($page);
    }

    public function update(Request $request, Page $page)
    {
        return $this->pageService->update($request, $page);
    }

    public function destroy(Page $page)
    {
        return $this->pageService->destroy($page);
    }

    public function updateOrder(Request $request)
    {
        return $this->pageService->updateOrder($request);
    }
}
