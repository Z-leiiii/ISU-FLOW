<?php

namespace App\Policies;

use App\Models\LeaveApplication;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LeaveApplicationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LeaveApplication $leaveApplication): bool
    {
        // Users can view their own applications
        // HR and Admin can view all applications
        return $user->id === $leaveApplication->user_id || 
               $user->hasRole('hr') || 
               $user->hasRole('admin');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only regular employees can create leave applications
        return !$user->hasRole('hr') && !$user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LeaveApplication $leaveApplication): bool
    {
        // Users can update their own pending applications
        return $user->id === $leaveApplication->user_id && 
               $leaveApplication->status === 'pending';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LeaveApplication $leaveApplication): bool
    {
        // Users can delete their own pending applications
        return $user->id === $leaveApplication->user_id && 
               $leaveApplication->status === 'pending';
    }

    /**
     * Determine whether the user can approve the model.
     */
    public function approve(User $user, LeaveApplication $leaveApplication): bool
    {
        // Only HR and Admin can approve applications
        return $user->hasRole('hr') || $user->hasRole('admin');
    }
}
