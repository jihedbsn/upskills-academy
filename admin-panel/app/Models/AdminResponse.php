<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminResponse extends Model
{
    protected $fillable = [
        'message_response_id',
        'admin_id',
        'response_body',
        'response_attachments'
    ];

    protected $casts = [
        'response_attachments' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the student response this admin response belongs to
     */
    public function messageResponse()
    {
        return $this->belongsTo(MessageResponse::class, 'message_response_id');
    }

    /**
     * Get the admin who sent this response
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
