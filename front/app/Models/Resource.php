<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'name_en',
        'type',
        'file',
        'file_ar',
        'file_en',
        'duration',
        'video_files',
        'video_files_multilingual',
    ];

    protected $casts = [
        'video_files' => 'array',
        'video_files_multilingual' => 'array',
    ];

    // Accesseur pour le nom multilingue
    public function getNameAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->name_ar : $this->name_en;
    }

    /**
     * Get the appropriate file based on current locale
     * Falls back to the other language if current locale file is not available
     */
    public function getLocalizedFileAttribute()
    {
        $locale = app()->getLocale();

        // Get raw attribute values from database using getRawOriginal
        $fileAr = $this->getRawOriginal('file_ar');
        $fileEn = $this->getRawOriginal('file_en');
        $fileLegacy = $this->getRawOriginal('file');

        // Try to get the file for current locale
        if ($locale === 'ar') {
            // Arabic: try file_ar first, then file_en, then legacy file
            if (!empty($fileAr)) {
                return $fileAr;
            }
            if (!empty($fileEn)) {
                return $fileEn;
            }
        } else {
            // English/French: try file_en first, then file_ar, then legacy file
            if (!empty($fileEn)) {
                return $fileEn;
            }
            if (!empty($fileAr)) {
                return $fileAr;
            }
        }

        // Fallback to legacy file field
        return $fileLegacy;
    }

    /**
     * Check if resource has file for specific language
     */
    public function hasFileForLanguage(string $lang): bool
    {
        $field = 'file_' . $lang;
        return !empty($this->$field);
    }

    /**
     * Get file for specific language (with fallback)
     */
    public function getFileForLanguage(string $lang): ?string
    {
        $field = 'file_' . $lang;

        // Try requested language first
        if (!empty($this->$field)) {
            return $this->$field;
        }

        // Fallback to other language
        $fallbackField = $lang === 'ar' ? 'file_en' : 'file_ar';
        if (!empty($this->$fallbackField)) {
            return $this->$fallbackField;
        }

        // Fallback to legacy file
        return $this->file;
    }

    // Accesseur pour obtenir les vidéos décodées
    public function getVideosAttribute()
    {
        // First priority: Return ALL localized videos (multilingual + legacy)
        $allVideos = $this->getAllLocalizedVideos();
        if (!empty($allVideos)) {
            return $allVideos;
        }

        // Fallback: Si video_files existe, l'utiliser (legacy)
        if ($this->video_files) {
            if (is_string($this->video_files)) {
                $decoded = json_decode($this->video_files, true);
                return $decoded ?: [];
            }
            if (is_array($this->video_files)) {
                return $this->video_files;
            }
        }

        // Sinon, si c'est une vidéo simple, créer un tableau avec une seule vidéo
        // Use localized_file to get the correct language version
        $localizedFile = $this->localized_file;
        if ($localizedFile && in_array($this->type, ['mp4', 'video'])) {
            return [
                [
                    'title' => $this->name ?? 'Video',
                    'file' => $localizedFile,
                    'uploaded_at' => $this->created_at ?? now(),
                    'is_single' => true, // Flag pour identifier une vidéo simple
                ]
            ];
        }

        return [];
    }

    // Méthode pour obtenir l'URL complète d'une vidéo
    public function getVideoUrl($videoFile, $isSingle = false)
    {
        $baseUrl = config('app.file_url');

        // Si c'est une vidéo simple, elle est dans uploads/resources/
        if ($isSingle) {
            return $baseUrl . 'uploads/resources/' . $videoFile;
        }

        // Si c'est une vidéo multiple, elle est dans uploads/resources/videos/ (avec "s")
        return $baseUrl . 'uploads/resources/videos/' . $videoFile;
    }

    // Méthode pour vérifier si la ressource a des vidéos multiples
    public function hasMultipleVideos()
    {
        // Check multilingual videos first
        if ($this->video_files_multilingual && count($this->video_files_multilingual) > 0) {
            return true;
        }

        // Si video_files existe et n'est pas vide, c'est une playlist multiple
        if ($this->video_files) {
            $videos = $this->getVideosAttribute();
            return is_array($videos) && count($videos) > 0;
        }

        // Si c'est juste le champ file, ce n'est pas multiple
        return false;
    }

    /**
     * Get multilingual videos filtered by current locale
     * Returns videos with title and file for the current language
     */
    public function getLocalizedVideoFilesAttribute()
    {
        $locale = app()->getLocale();
        $videos = $this->video_files_multilingual ?? [];

        return collect($videos)->map(function($video) use ($locale) {
            // Support ar, en, and fr locales
            $titleField = 'title_' . $locale;
            $fileField = 'file_' . $locale;

            // Fallback logic: Try current locale -> English -> Arabic
            $fallbackTitleField = 'title_en';
            $fallbackFileField = 'file_en';
            if ($locale === 'en') {
                $fallbackTitleField = 'title_ar';
                $fallbackFileField = 'file_ar';
            }

            return [
                'title' => $video[$titleField] ?? $video[$fallbackTitleField] ?? $video['title_ar'] ?? $video['title_en'] ?? $video['title'] ?? '',
                'file' => $video[$fileField] ?? $video[$fallbackFileField] ?? $video['file_ar'] ?? $video['file_en'] ?? $video['file'] ?? '',
                'title_ar' => $video['title_ar'] ?? '',
                'title_en' => $video['title_en'] ?? '',
                'file_ar' => $video['file_ar'] ?? '',
                'file_en' => $video['file_en'] ?? '',
                'uploaded_at' => $video['uploaded_at'] ?? null,
            ];
        })->filter(function($video) {
            // Only return videos that have at least a file (in any language)
            return !empty($video['file']);
        })->values()->toArray();
    }

    /**
     * Get all videos (multilingual + legacy) filtered by locale
     */
    public function getAllLocalizedVideos()
    {
        $locale = app()->getLocale();
        $videos = [];

        // First add multilingual videos (filtered by locale)
        $mlVideos = $this->localized_video_files;
        foreach ($mlVideos as $video) {
            $videos[] = [
                'title' => $video['title'],
                'file' => $video['file'],
                'uploaded_at' => $video['uploaded_at'],
                'is_multilingual' => true,
            ];
        }

        // Then add legacy videos (shown to all languages)
        if ($this->video_files) {
            foreach ($this->video_files as $video) {
                $videos[] = [
                    'title' => $video['title'] ?? '',
                    'file' => $video['file'] ?? '',
                    'uploaded_at' => $video['uploaded_at'] ?? null,
                    'is_multilingual' => false,
                ];
            }
        }

        return $videos;
    }
}
