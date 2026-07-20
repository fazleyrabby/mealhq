<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('customers.view');
    }

    public function view(User $user, User $model): bool
    {
        return $user->can('customers.view');
    }

    public function create(User $user): bool
    {
        return $user->can('customers.manage');
    }

    public function update(User $user, User $model): bool
    {
        return $user->can('customers.manage');
    }

    public function delete(User $user, User $model): bool
    {
        return $user->can('customers.manage') && $user->id !== $model->id;
    }
}
