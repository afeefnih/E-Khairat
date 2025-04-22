<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DependentEditRequest extends Model
{
    protected $fillable = [
        'user_id',
        'dependent_id',
        'full_name',
        'relationship',
        'age',
        'ic_number',
        'status',
        'request_type',
        'admin_comments',
        'processed_by',
        'processed_at'
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dependent()
    {
        return $this->belongsTo(Dependent::class, 'dependent_id');
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
