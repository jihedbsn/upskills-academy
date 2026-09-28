<?php

namespace App\Services;

use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ResourceService
{
    public function index(Request $request): array
    {
        $query = Resource::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name_ar', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%")
                  ->orWhere('file', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $orderBy  = $request->get('order_by', 'created_at');
        $orderDir = $request->get('order_dir', 'desc');
        $query->orderBy($orderBy, $orderDir);

        $perPage   = $request->get('per_page', 15);
        $resources = $query->paginate($perPage)->appends($request->except('page'));
        $types     = Resource::select('type')->distinct()->pluck('type');

        return compact('resources', 'types');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_ar'           => 'required|string|max:255',
            'name_en'           => 'required|string|max:255',
            'type'              => 'required|string|max:255',
            'file_ar'           => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,txt,jpg,jpeg,png,gif,mp4,avi,mov,mp3|max:512000',
            'file_en'           => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,txt,jpg,jpeg,png,gif,mp4,avi,mov,mp3|max:512000',
            'videos'            => 'nullable|array',
            'videos.*'          => 'nullable|string|max:255',
            'video_titles'      => 'nullable|array',
            'video_titles.*'    => 'nullable|string|max:255',
            'video_files'       => 'nullable|array',
            'video_files.*'     => 'nullable|file|mimes:mp4,avi,mov,mp3|max:512000',
            'ml_video_title_ar'   => 'nullable|array',
            'ml_video_title_ar.*' => 'nullable|string|max:255',
            'ml_video_title_en'   => 'nullable|array',
            'ml_video_title_en.*' => 'nullable|string|max:255',
            'ml_video_file_ar'    => 'nullable|array',
            'ml_video_file_ar.*'  => 'nullable|file|mimes:mp4,avi,mov,mp3|max:512000',
            'ml_video_file_en'    => 'nullable|array',
            'ml_video_file_en.*'  => 'nullable|file|mimes:mp4,avi,mov,mp3|max:512000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();

        if ($request->hasFile('file_ar')) {
            $file             = $request->file('file_ar');
            $fileName         = time() . '_ar_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/resources'), $fileName);
            $data['file_ar']  = $fileName;
        }

        if ($request->hasFile('file_en')) {
            $file             = $request->file('file_en');
            $fileName         = time() . '_en_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/resources'), $fileName);
            $data['file_en']  = $fileName;
        }

        if ($request->type !== 'video') {
            if (!$request->hasFile('file_ar') && !$request->hasFile('file_en')) {
                return redirect()->back()
                    ->withErrors(['file_ar' => 'At least one file (Arabic or English) is required.'])
                    ->withInput();
            }
        }

        if (!empty($data['file_ar'])) {
            $data['file'] = $data['file_ar'];
        } elseif (!empty($data['file_en'])) {
            $data['file'] = $data['file_en'];
        } elseif ($request->type === 'video') {
            $data['file'] = 'video_resource_' . time() . '.mp4';
        }

        if ($request->type === 'video') {
            $videos        = array_filter($request->input('videos', []));
            $data['videos'] = $videos;

            $videoTitles   = $request->input('video_titles', []);
            $videoFiles    = $request->file('video_files', []);
            $videoFilesData = [];

            foreach ($videoTitles as $index => $title) {
                if (!empty($title) && isset($videoFiles[$index]) && $videoFiles[$index]->isValid()) {
                    $videoFile     = $videoFiles[$index];
                    $videoFileName = time() . '_' . $index . '_' . $videoFile->getClientOriginalName();
                    $videoFile->move(public_path('uploads/resources/videos'), $videoFileName);
                    $videoFilesData[] = [
                        'title'       => $title,
                        'file'        => $videoFileName,
                        'uploaded_at' => now()->toISOString(),
                    ];
                }
            }

            $data['video_files'] = $videoFilesData;

            $mlTitlesAr  = $request->input('ml_video_title_ar', []);
            $mlTitlesEn  = $request->input('ml_video_title_en', []);
            $mlFilesAr   = $request->file('ml_video_file_ar', []);
            $mlFilesEn   = $request->file('ml_video_file_en', []);
            $videoFilesMl = [];

            $maxIndex = max(count($mlTitlesAr), count($mlTitlesEn));
            for ($i = 0; $i < $maxIndex; $i++) {
                $titleAr  = $mlTitlesAr[$i] ?? '';
                $titleEn  = $mlTitlesEn[$i] ?? '';
                $hasFileAr = isset($mlFilesAr[$i]) && $mlFilesAr[$i]->isValid();
                $hasFileEn = isset($mlFilesEn[$i]) && $mlFilesEn[$i]->isValid();

                if ((!empty($titleAr) || !empty($titleEn)) && ($hasFileAr || $hasFileEn)) {
                    $videoData = [
                        'title_ar'    => $titleAr,
                        'title_en'    => $titleEn,
                        'file_ar'     => null,
                        'file_en'     => null,
                        'uploaded_at' => now()->toISOString(),
                    ];

                    if ($hasFileAr) {
                        $fileAr       = $mlFilesAr[$i];
                        $fileNameAr   = time() . '_ml_' . $i . '_ar_' . $fileAr->getClientOriginalName();
                        $fileAr->move(public_path('uploads/resources/videos'), $fileNameAr);
                        $videoData['file_ar'] = $fileNameAr;
                    }

                    if ($hasFileEn) {
                        $fileEn       = $mlFilesEn[$i];
                        $fileNameEn   = time() . '_ml_' . $i . '_en_' . $fileEn->getClientOriginalName();
                        $fileEn->move(public_path('uploads/resources/videos'), $fileNameEn);
                        $videoData['file_en'] = $fileNameEn;
                    }

                    $videoFilesMl[] = $videoData;
                }
            }

            $data['video_files_multilingual'] = $videoFilesMl;
        }

        Resource::create($data);

        return redirect()->route('resources.index')->with('success', 'Ressource créée avec succès !');
    }

    public function update(Request $request, Resource $resource)
    {
        $validator = Validator::make($request->all(), [
            'name_ar'                  => 'required|string|max:255',
            'name_en'                  => 'required|string|max:255',
            'type'                     => 'required|string|max:255',
            'file_ar'                  => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,txt,jpg,jpeg,png,gif,mp4,avi,mov,mp3|max:512000',
            'file_en'                  => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,txt,jpg,jpeg,png,gif,mp4,avi,mov,mp3|max:512000',
            'delete_file_ar'           => 'nullable|boolean',
            'delete_file_en'           => 'nullable|boolean',
            'videos'                   => 'nullable|array',
            'videos.*'                 => 'nullable|string|max:255',
            'video_titles'             => 'nullable|array',
            'video_titles.*'           => 'nullable|string|max:255',
            'video_files'              => 'nullable|array',
            'video_files.*'            => 'nullable|file|mimes:mp4,avi,mov,mp3|max:512000',
            'existing_video_titles'    => 'nullable|array',
            'existing_video_titles.*'  => 'nullable|string|max:255',
            'existing_video_files'     => 'nullable|array',
            'existing_video_files.*'   => 'nullable|string',
            'replace_video_files'      => 'nullable|array',
            'replace_video_files.*'    => 'nullable|file|mimes:mp4,avi,mov,mp3|max:512000',
            'delete_video_files'       => 'nullable|array',
            'delete_video_files.*'     => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->except('file_ar', 'file_en', 'delete_file_ar', 'delete_file_en');

        if ($request->input('delete_file_ar') == '1') {
            if ($resource->file_ar && file_exists(public_path('uploads/resources/' . $resource->file_ar))) {
                unlink(public_path('uploads/resources/' . $resource->file_ar));
            }
            $data['file_ar'] = null;
        }

        if ($request->input('delete_file_en') == '1') {
            if ($resource->file_en && file_exists(public_path('uploads/resources/' . $resource->file_en))) {
                unlink(public_path('uploads/resources/' . $resource->file_en));
            }
            $data['file_en'] = null;
        }

        if ($request->hasFile('file_ar')) {
            if ($resource->file_ar && file_exists(public_path('uploads/resources/' . $resource->file_ar))) {
                unlink(public_path('uploads/resources/' . $resource->file_ar));
            }
            $file            = $request->file('file_ar');
            $fileName        = time() . '_ar_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/resources'), $fileName);
            $data['file_ar'] = $fileName;
        }

        if ($request->hasFile('file_en')) {
            if ($resource->file_en && file_exists(public_path('uploads/resources/' . $resource->file_en))) {
                unlink(public_path('uploads/resources/' . $resource->file_en));
            }
            $file            = $request->file('file_en');
            $fileName        = time() . '_en_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/resources'), $fileName);
            $data['file_en'] = $fileName;
        }

        $finalFileAr = $data['file_ar'] ?? $resource->file_ar;
        $finalFileEn = $data['file_en'] ?? $resource->file_en;
        if (!empty($finalFileAr)) {
            $data['file'] = $finalFileAr;
        } elseif (!empty($finalFileEn)) {
            $data['file'] = $finalFileEn;
        }

        if ($request->type === 'video') {
            $videos        = array_filter($request->input('videos', []));
            $data['videos'] = $videos;

            $videoFilesData         = [];
            $existingVideoTitles    = $request->input('existing_video_titles', []);
            $existingVideoFiles     = $request->input('existing_video_files', []);
            $replaceVideoFiles      = $request->file('replace_video_files', []);
            $deleteVideoFlags       = $request->input('delete_video_files', []);

            foreach ($existingVideoTitles as $index => $title) {
                if (isset($deleteVideoFlags[$index]) && $deleteVideoFlags[$index] == '1') {
                    if (isset($existingVideoFiles[$index]) && file_exists(public_path('uploads/resources/videos/' . $existingVideoFiles[$index]))) {
                        unlink(public_path('uploads/resources/videos/' . $existingVideoFiles[$index]));
                    }
                    continue;
                }

                if (isset($replaceVideoFiles[$index]) && $replaceVideoFiles[$index]->isValid()) {
                    if (isset($existingVideoFiles[$index]) && file_exists(public_path('uploads/resources/videos/' . $existingVideoFiles[$index]))) {
                        unlink(public_path('uploads/resources/videos/' . $existingVideoFiles[$index]));
                    }
                    $videoFile     = $replaceVideoFiles[$index];
                    $videoFileName = time() . '_' . $index . '_' . $videoFile->getClientOriginalName();
                    $videoFile->move(public_path('uploads/resources/videos'), $videoFileName);
                    $videoFilesData[] = [
                        'title'       => $title,
                        'file'        => $videoFileName,
                        'uploaded_at' => now()->toISOString(),
                    ];
                } else {
                    if (isset($existingVideoFiles[$index])) {
                        $videoFilesData[] = [
                            'title'       => $title,
                            'file'        => $existingVideoFiles[$index],
                            'uploaded_at' => $resource->video_files[$index]['uploaded_at'] ?? now()->toISOString(),
                        ];
                    }
                }
            }

            $videoTitles = $request->input('video_titles', []);
            $videoFiles  = $request->file('video_files', []);

            foreach ($videoTitles as $index => $title) {
                if (!empty($title) && isset($videoFiles[$index]) && $videoFiles[$index]->isValid()) {
                    $videoFile     = $videoFiles[$index];
                    $videoFileName = time() . '_' . $index . '_' . $videoFile->getClientOriginalName();
                    $videoFile->move(public_path('uploads/resources/videos'), $videoFileName);
                    $videoFilesData[] = [
                        'title'       => $title,
                        'file'        => $videoFileName,
                        'uploaded_at' => now()->toISOString(),
                    ];
                }
            }

            $data['video_files'] = $videoFilesData;

            $mlVideoFilesData  = [];
            $existingMlTitleAr = $request->input('existing_ml_video_title_ar', []);
            $existingMlTitleEn = $request->input('existing_ml_video_title_en', []);
            $existingMlFileAr  = $request->input('existing_ml_video_file_ar', []);
            $existingMlFileEn  = $request->input('existing_ml_video_file_en', []);
            $replaceMlFileAr   = $request->file('replace_ml_video_file_ar', []);
            $replaceMlFileEn   = $request->file('replace_ml_video_file_en', []);
            $deleteMlFlags     = $request->input('delete_ml_video', []);

            foreach ($existingMlTitleAr as $index => $titleAr) {
                if (isset($deleteMlFlags[$index]) && $deleteMlFlags[$index] == '1') {
                    if (isset($existingMlFileAr[$index]) && file_exists(public_path('uploads/resources/videos/' . $existingMlFileAr[$index]))) {
                        unlink(public_path('uploads/resources/videos/' . $existingMlFileAr[$index]));
                    }
                    if (isset($existingMlFileEn[$index]) && file_exists(public_path('uploads/resources/videos/' . $existingMlFileEn[$index]))) {
                        unlink(public_path('uploads/resources/videos/' . $existingMlFileEn[$index]));
                    }
                    continue;
                }

                $videoData = [
                    'title_ar'    => $titleAr,
                    'title_en'    => $existingMlTitleEn[$index] ?? '',
                    'file_ar'     => $existingMlFileAr[$index] ?? '',
                    'file_en'     => $existingMlFileEn[$index] ?? '',
                    'uploaded_at' => $resource->video_files_multilingual[$index]['uploaded_at'] ?? now()->toISOString(),
                ];

                if (isset($replaceMlFileAr[$index]) && $replaceMlFileAr[$index]->isValid()) {
                    if (!empty($existingMlFileAr[$index]) && file_exists(public_path('uploads/resources/videos/' . $existingMlFileAr[$index]))) {
                        unlink(public_path('uploads/resources/videos/' . $existingMlFileAr[$index]));
                    }
                    $file               = $replaceMlFileAr[$index];
                    $fileName           = time() . '_ar_' . $index . '_' . $file->getClientOriginalName();
                    $file->move(public_path('uploads/resources/videos'), $fileName);
                    $videoData['file_ar'] = $fileName;
                }

                if (isset($replaceMlFileEn[$index]) && $replaceMlFileEn[$index]->isValid()) {
                    if (!empty($existingMlFileEn[$index]) && file_exists(public_path('uploads/resources/videos/' . $existingMlFileEn[$index]))) {
                        unlink(public_path('uploads/resources/videos/' . $existingMlFileEn[$index]));
                    }
                    $file               = $replaceMlFileEn[$index];
                    $fileName           = time() . '_en_' . $index . '_' . $file->getClientOriginalName();
                    $file->move(public_path('uploads/resources/videos'), $fileName);
                    $videoData['file_en'] = $fileName;
                }

                $mlVideoFilesData[] = $videoData;
            }

            $newMlTitleAr = $request->input('ml_video_title_ar', []);
            $newMlTitleEn = $request->input('ml_video_title_en', []);
            $newMlFileAr  = $request->file('ml_video_file_ar', []);
            $newMlFileEn  = $request->file('ml_video_file_en', []);

            foreach ($newMlTitleAr as $index => $titleAr) {
                $titleEn = $newMlTitleEn[$index] ?? '';
                if (empty($titleAr) && empty($titleEn)) {
                    continue;
                }

                $videoData = [
                    'title_ar'    => $titleAr,
                    'title_en'    => $titleEn,
                    'file_ar'     => '',
                    'file_en'     => '',
                    'uploaded_at' => now()->toISOString(),
                ];

                if (isset($newMlFileAr[$index]) && $newMlFileAr[$index]->isValid()) {
                    $file               = $newMlFileAr[$index];
                    $fileName           = time() . '_ar_new_' . $index . '_' . $file->getClientOriginalName();
                    $file->move(public_path('uploads/resources/videos'), $fileName);
                    $videoData['file_ar'] = $fileName;
                }

                if (isset($newMlFileEn[$index]) && $newMlFileEn[$index]->isValid()) {
                    $file               = $newMlFileEn[$index];
                    $fileName           = time() . '_en_new_' . $index . '_' . $file->getClientOriginalName();
                    $file->move(public_path('uploads/resources/videos'), $fileName);
                    $videoData['file_en'] = $fileName;
                }

                if (!empty($videoData['file_ar']) || !empty($videoData['file_en'])) {
                    $mlVideoFilesData[] = $videoData;
                }
            }

            $data['video_files_multilingual'] = $mlVideoFilesData;
        } else {
            $data['videos']                   = null;
            $data['video_files']              = null;
            $data['video_files_multilingual'] = null;
        }

        $resource->update($data);

        return redirect()->route('resources.index')->with('success', 'Ressource mise à jour avec succès !');
    }

    public function destroy(Resource $resource)
    {
        try {
            Log::info('Destroying resource ID: ' . $resource->id);

            if ($resource->file && file_exists(public_path('uploads/resources/' . $resource->file))) {
                unlink(public_path('uploads/resources/' . $resource->file));
                Log::info('Deleted file: ' . $resource->file);
            }

            if ($resource->video_files) {
                foreach ($resource->video_files as $video) {
                    if (isset($video['file']) && file_exists(public_path('uploads/resources/videos/' . $video['file']))) {
                        unlink(public_path('uploads/resources/videos/' . $video['file']));
                        Log::info('Deleted video file: ' . $video['file']);
                    }
                }
            }

            $resource->delete();
            Log::info('Resource ID ' . $resource->id . ' deleted successfully from database');

            return redirect()->route('resources.index')->with('success', 'Ressource supprimée avec succès !');
        } catch (\Exception $e) {
            Log::error('Error deleting resource: ' . $e->getMessage());
            return redirect()->route('resources.index')
                ->with('error', 'Erreur lors de la suppression de la ressource : ' . $e->getMessage());
        }
    }

    public function download(Resource $resource)
    {
        $path = public_path('uploads/resources/' . $resource->file);

        if (file_exists($path)) {
            return response()->download($path);
        }

        return redirect()->back()->with('error', 'Fichier non trouvé !');
    }

    public function downloadVideo(Resource $resource, string $title)
    {
        $videoFile = $resource->getVideoFile($title);

        if (!$videoFile || !isset($videoFile['file'])) {
            return redirect()->back()->with('error', 'Fichier vidéo non trouvé !');
        }

        $path = public_path('uploads/resources/videos/' . $videoFile['file']);

        if (file_exists($path)) {
            return response()->download($path, $videoFile['title'] . '.' . pathinfo($videoFile['file'], PATHINFO_EXTENSION));
        }

        return redirect()->back()->with('error', 'Fichier vidéo non trouvé !');
    }

    public function addVideoFile(Request $request, Resource $resource)
    {
        $request->validate([
            'video_title' => 'required|string|max:255',
            'video_file'  => 'required|file|mimes:mp4,avi,mov,mp3|max:512000',
        ]);

        $videoFile = $request->file('video_file');
        $fileName  = time() . '_' . $videoFile->getClientOriginalName();
        $videoFile->move(public_path('uploads/resources/videos'), $fileName);

        $resource->addVideoFile($request->video_title, $fileName);

        return redirect()->back()->with('success', 'Vidéo ajoutée avec succès !');
    }

    public function removeVideoFile(Request $request, Resource $resource)
    {
        $request->validate([
            'video_title' => 'required|string|max:255',
        ]);

        $videoFile = $resource->getVideoFile($request->video_title);

        if ($videoFile && isset($videoFile['file'])) {
            if (file_exists(public_path('uploads/resources/videos/' . $videoFile['file']))) {
                unlink(public_path('uploads/resources/videos/' . $videoFile['file']));
            }
        }

        $resource->removeVideoFile($request->video_title);

        return redirect()->back()->with('success', 'Vidéo supprimée avec succès !');
    }

    public function addVideo(Request $request, Resource $resource)
    {
        $request->validate(['video_name' => 'required|string|max:255']);
        $resource->addVideo($request->video_name);
        return redirect()->back()->with('success', 'Vidéo ajoutée avec succès !');
    }

    public function removeVideo(Request $request, Resource $resource)
    {
        $request->validate(['video_name' => 'required|string|max:255']);
        $resource->removeVideo($request->video_name);
        return redirect()->back()->with('success', 'Vidéo supprimée avec succès !');
    }
}
