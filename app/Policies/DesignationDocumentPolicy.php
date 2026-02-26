<?php

namespace App\Policies;

use App\Models\DesignationDocument;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DesignationDocumentPolicy
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
    public function view(User $user, DesignationDocument $designationDocument): bool
    {
        // Users can view their own documents
        // HR and Admin can view all documents
        return $user->id === $designationDocument->user_id || 
               $user->hasRole('hr') || 
               $user->hasRole('admin');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only regular employees can create designation documents
        return !$user->hasRole('hr') && !$user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DesignationDocument $designationDocument): bool
    {
        // Users can update their own pending documents
        return $user->id === $designationDocument->user_id && 
               $designationDocument->status === 'pending';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DesignationDocument $designationDocument): bool
    {
        // Users can delete their own pending documents
        return $user->id === $designationDocument->user_id && 
               $designationDocument->status === 'pending';
    }

    /**
     * Determine whether the user can approve the model.
     */
    public function approve(User $user, DesignationDocument $designationDocument): bool
    {
        // Only HR and Admin can approve documents
        return $user->hasRole('hr') || $user->hasRole('admin');
    }
}
