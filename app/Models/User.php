<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'employee_id',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'password',
        'department_id',
        'designation_id',
        'date_hired',
        'salary',
        'contact_number',
        'address',
        'is_active',
    ];

    /**
     * Hidden attributes
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casting
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_hired' => 'date',
            'salary' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Department relationship
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Designation relationship
     */
    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    /**
     * Leave credits
     */
    public function leaveCredits()
    {
        return $this->hasMany(LeaveCredit::class);
    }

    /**
     * Leave applications
     */
    public function leaveApplications()
    {
        return $this->hasMany(LeaveApplication::class);
    }

    /**
     * Designation documents
     */
    public function designationDocuments()
    {
        return $this->hasMany(DesignationDocument::class);
    }

    /**
     * Notifications
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Compensatory Time Off records
     */
    public function compensatoryTimeOffs()
    {
        return $this->hasMany(CompensatoryTimeOff::class);
    }

    /**
     * Full name accessor
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
