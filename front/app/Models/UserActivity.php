<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    protected $fillable = [
        'user_type',
        'user_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'url',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    // Relations
    public function user()
    {
        return $this->morphTo();
    }

    public function model()
    {
        return $this->morphTo('model');
    }

    // Accesseurs
    public function getUserNameAttribute()
    {
        if ($this->user) {
            return $this->user->full_name ?? $this->user->email;
        }
        return 'Utilisateur supprimé';
    }

    public function getModelNameAttribute()
    {
        if ($this->model_type && $this->model_id) {
            $model = $this->model_type::find($this->model_id);
            if ($model) {
                return class_basename($this->model_type) . ' #' . $this->model_id;
            }
        }
        return $this->model_type ? class_basename($this->model_type) : null;
    }

    public function getActionLabelAttribute()
    {
        $labels = [
            'create' => 'Création',
            'update' => 'Modification',
            'delete' => 'Suppression',
            'login' => 'Connexion',
            'logout' => 'Déconnexion',
            'view' => 'Consultation',
            'export' => 'Export',
            'import' => 'Import',
        ];

        return $labels[$this->action] ?? ucfirst($this->action);
    }

    // Scopes
    public function scopeByUser($query, $user)
    {
        return $query->where('user_type', get_class($user))
                    ->where('user_id', $user->id);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByModel($query, $modelType, $modelId = null)
    {
        $query = $query->where('model_type', $modelType);
        
        if ($modelId) {
            $query->where('model_id', $modelId);
        }
        
        return $query;
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Méthodes statiques pour créer des activités
    public static function logLogin($user)
    {
        return static::create([
            'user_type' => get_class($user),
            'user_id' => $user->id,
            'action' => 'login',
            'description' => 'Connexion au système',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
        ]);
    }

    public static function logLogout($user)
    {
        return static::create([
            'user_type' => get_class($user),
            'user_id' => $user->id,
            'action' => 'logout',
            'description' => 'Déconnexion du système',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
        ]);
    }

    public static function logModelAction($user, $action, $model, $description = null, $oldValues = null, $newValues = null)
    {
        return static::create([
            'user_type' => get_class($user),
            'user_id' => $user->id,
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
        ]);
    }
}
