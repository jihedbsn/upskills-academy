<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class WebMaster extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'avatar',
        'role',
        'permissions',
        'is_active',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'permissions' => 'array',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    // Relations
    public function activities()
    {
        return $this->morphMany(UserActivity::class, 'user');
    }

    // Accesseurs
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('uploads/webmasters/' . $this->avatar);
        }
        return asset('assets/img/default-avatar.png');
    }

    // Méthodes pour les permissions
    public function hasPermission($permission)
    {
        if ($this->role === 'web_master') {
            return true;
        }

        $permissions = $this->permissions ?? [];
        return in_array($permission, $permissions);
    }

    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function isWebMaster()
    {
        return $this->role === 'web_master';
    }

    public function isContentManager()
    {
        return $this->role === 'content_manager';
    }

    public function isSupport()
    {
        return $this->role === 'support';
    }

    // Méthodes pour l'historique
    public function logActivity($action, $model = null, $description = null, $oldValues = null, $newValues = null)
    {
        return $this->activities()->create([
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->id : null,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
        ]);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }
}
