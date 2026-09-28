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
        'videos', // Ancien champ pour compatibilité
        'video_files', // Nouveau champ pour titre + fichier (legacy - all languages)
        'video_files_multilingual', // Multilingual video files with ar/en support
    ];

    protected $casts = [
        'videos' => 'array', // Cast automatique en array
        'video_files' => 'array', // Cast automatique en array
        'video_files_multilingual' => 'array', // Cast for multilingual video files
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

    // Accesseur pour vérifier si c'est une ressource vidéo
    public function getIsVideoAttribute()
    {
        return $this->type === 'video';
    }

    // Accesseur pour obtenir la liste des vidéos (ancien format)
    public function getVideosListAttribute()
    {
        return $this->videos ?? [];
    }

    // Accesseur pour obtenir la liste des fichiers vidéo (nouveau format)
    public function getVideoFilesListAttribute()
    {
        return $this->video_files ?? [];
    }

    // Get multilingual video files
    public function getVideoFilesMultilingualListAttribute()
    {
        return $this->video_files_multilingual ?? [];
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
            $titleField = 'title_' . $locale;
            $fileField = 'file_' . $locale;
            $fallbackTitleField = $locale === 'ar' ? 'title_en' : 'title_ar';
            $fallbackFileField = $locale === 'ar' ? 'file_en' : 'file_ar';

            return [
                'title' => $video[$titleField] ?? $video[$fallbackTitleField] ?? $video['title'] ?? '',
                'file' => $video[$fileField] ?? $video[$fallbackFileField] ?? $video['file'] ?? '',
                'title_ar' => $video['title_ar'] ?? '',
                'title_en' => $video['title_en'] ?? '',
                'file_ar' => $video['file_ar'] ?? '',
                'file_en' => $video['file_en'] ?? '',
                'uploaded_at' => $video['uploaded_at'] ?? null,
            ];
        })->filter(function($video) {
            // Only return videos that have a file for the current language
            return !empty($video['file']);
        })->values()->toArray();
    }

    // Méthode pour ajouter une vidéo avec fichier
    public function addVideoFile($title, $fileName)
    {
        $videoFiles = $this->video_files ?? [];
        $videoFiles[] = [
            'title' => $title,
            'file' => $fileName,
            'uploaded_at' => now()->toISOString()
        ];
        $this->video_files = $videoFiles;
        $this->save();
    }

    // Méthode pour supprimer une vidéo par titre
    public function removeVideoFile($title)
    {
        $videoFiles = $this->video_files ?? [];
        $videoFiles = array_filter($videoFiles, function($video) use ($title) {
            return $video['title'] !== $title;
        });
        $this->video_files = array_values($videoFiles);
        $this->save();
    }

    // Méthode pour obtenir une vidéo par titre
    public function getVideoFile($title)
    {
        $videoFiles = $this->video_files ?? [];
        foreach ($videoFiles as $video) {
            if ($video['title'] === $title) {
                return $video;
            }
        }
        return null;
    }

    // Méthodes d'ancienne compatibilité (à supprimer progressivement)
    public function addVideo($videoName)
    {
        $videos = $this->videos ?? [];
        $videos[] = $videoName;
        $this->videos = $videos;
        $this->save();
    }

    public function removeVideo($videoName)
    {
        $videos = $this->videos ?? [];
        $videos = array_filter($videos, function($video) use ($videoName) {
            return $video !== $videoName;
        });
        $this->videos = array_values($videos);
        $this->save();
    }
}
