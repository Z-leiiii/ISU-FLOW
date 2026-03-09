<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'code',
        'description',
        'earns_vacation_leave',
        'earns_sick_leave',
        'vacation_leave_rate',
        'sick_leave_rate',
        'is_active',
    ];

    protected $casts = [
        'earns_vacation_leave' => 'boolean',
        'earns_sick_leave' => 'boolean',
        'vacation_leave_rate' => 'decimal:2',
        'sick_leave_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the users for the designation.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the designation documents for the designation.
     */
    public function designationDocuments()
    {
        return $this->hasMany(DesignationDocument::class);
    }
}
