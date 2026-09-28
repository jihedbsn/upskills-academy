<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Services\ResourceService;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    public function __construct(private ResourceService $service) {}

    public function index(Request $request)
    {
        $data = $this->service->index($request);
        return view('resources.index', $data);
    }

    public function create()
    {
        return view('resources.create');
    }

    public function store(Request $request)
    {
        return $this->service->store($request);
    }

    public function show(Resource $resource)
    {
        return view('resources.show', compact('resource'));
    }

    public function edit(Resource $resource)
    {
        return view('resources.edit', compact('resource'));
    }

    public function update(Request $request, Resource $resource)
    {
        return $this->service->update($request, $resource);
    }

    public function destroy(Resource $resource)
    {
        return $this->service->destroy($resource);
    }

    public function download(Resource $resource)
    {
        return $this->service->download($resource);
    }

    public function downloadVideo(Resource $resource, string $title)
    {
        return $this->service->downloadVideo($resource, $title);
    }

    public function manageVideos(Resource $resource)
    {
        return view('resources.manage-videos', compact('resource'));
    }

    public function addVideoFile(Request $request, Resource $resource)
    {
        return $this->service->addVideoFile($request, $resource);
    }

    public function removeVideoFile(Request $request, Resource $resource)
    {
        return $this->service->removeVideoFile($request, $resource);
    }

    public function addVideo(Request $request, Resource $resource)
    {
        return $this->service->addVideo($request, $resource);
    }

    public function removeVideo(Request $request, Resource $resource)
    {
        return $this->service->removeVideo($request, $resource);
    }
}
