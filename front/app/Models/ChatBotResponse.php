<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatBotResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'keyword',
        'question_en',
        'question_ar',
        'response_en',
        'response_ar',
        'category',
        'priority',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Méthodes utilitaires
    public function getQuestion($language = 'en')
    {
        return $language === 'ar' ? $this->question_ar : $this->question_en;
    }

    public function getResponse($language = 'en')
    {
        return $language === 'ar' ? $this->response_ar : $this->response_en;
    }

    // Scope pour les réponses actives
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope pour une catégorie spécifique
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Méthode pour trouver une réponse basée sur un mot-clé
    public static function findResponseByKeyword($keyword, $language = 'en')
    {
        return self::active()
            ->where('keyword', 'LIKE', "%{$keyword}%")
            ->orderBy('priority', 'desc')
            ->first();
    }

    // Méthode pour obtenir les questions fréquentes
    public static function getFrequentlyAskedQuestions($language = 'en', $limit = 5)
    {
        try {
            return self::active()
                ->where('category', 'faq')
                ->orderBy('priority', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($item) use ($language) {
                    return [
                        'question' => $item->getQuestion($language),
                        'response' => $item->getResponse($language)
                    ];
                });
        } catch (\Exception $e) {
            // Table may not exist yet
            return collect([]);
        }
    }
}
