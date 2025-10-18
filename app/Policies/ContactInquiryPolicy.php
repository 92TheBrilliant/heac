<?php

namespace App\Policies;

use App\Models\ContactInquiry;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ContactInquiryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_contact_inquiries');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ContactInquiry $contactInquiry): bool
    {
        return $user->can('view_contact_inquiries');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Contact inquiries are created by public users, not admin users
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ContactInquiry $contactInquiry): bool
    {
        return $user->can('edit_contact_inquiries');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ContactInquiry $contactInquiry): bool
    {
        return $user->can('delete_contact_inquiries');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ContactInquiry $contactInquiry): bool
    {
        return $user->can('delete_contact_inquiries');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ContactInquiry $contactInquiry): bool
    {
        return $user->can('delete_contact_inquiries');
    }
}
