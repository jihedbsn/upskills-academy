<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResponseDiscussion extends Model
{
    use HasFactory;

    protected $fillable = [
        'discussion_id',
        'admin_id',
        'reponse',
        'is_approved'
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    // Relations
    public function discussion()
    {
        return $this->belongsTo(Discussion::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeByDiscussion($query, $discussionId)
    {
        return $query->where('discussion_id', $discussionId);
    }

    public function scopeByAdmin($query, $adminId)
    {
        return $query->where('admin_id', $adminId);
    }

    // Méthodes
    public function approve()
    {
        $this->update(['is_approved' => true]);
    }

    public function disapprove()
    {
        $this->update(['is_approved' => false]);
    }
}
