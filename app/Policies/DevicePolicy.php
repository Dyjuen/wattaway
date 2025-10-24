<?php

namespace App\Policies;

use App\Models\Account;
use App\Models\Device;
use Illuminate\Auth\Access\Response;

class DevicePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Account $account): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Account $account, Device $device): bool
    {
        return $account->id === $device->account_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Account $account): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Account $account, Device $device): bool
    {
        return $account->id === $device->account_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Account $account, Device $device): bool
    {
        return $account->id === $device->account_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Account $account, Device $device): bool
    {
        return $account->id === $device->account_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Account $account, Device $device): bool
    {
        return $account->id === $device->account_id;
    }

    /**
     * Determine whether the user can view the API token.
     */
    public function viewApiToken(Account $account, Device $device): bool
    {
        return $account->id === $device->account_id;
    }
}
