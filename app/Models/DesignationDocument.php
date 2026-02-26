<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesignationDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'designation_id',
        'document_title',
        'file_path',
        'status',
        'hr_remarks',
        'approved_at',
        'rejected_at',
        'approved_by',
        'rejected_by',
        'is_current',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'is_current' => 'boolean',
    ];

    /**
     * Get the user that owns the designation document.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the designation that owns the designation document.
     */
    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    /**
     * Get the user who approved the designation document.
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who rejected the designation document.
     */
    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }
}
