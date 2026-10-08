<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class ClientPolicy
{
    private function checkTenantIsolation(User $user, User $client): Response
    {
        return $user->company_id === $client->company_id
            ? Response::allow()
            : Response::denyAsNotFound('Client not found.');
    }

    public function viewAny(User $user): Response
    {
        return $user->hasRole('manager') ? Response::allow() : Response::deny();
    }

    public function view(User $user, User $client): Response
    {
        if (!$user->hasRole('manager')) {
            return Response::denyAsNotFound('Client not found.');
        }

        return $this->checkTenantIsolation($user, $client);
    }

    public function create(User $user): Response
    {
        return $user->hasRole('manager') ? Response::allow() : Response::deny('Unauthorized.');
    }

    public function update(User $user, User $client): Response
    {
        if (!$user->hasRole('manager')) {
            return Response::denyAsNotFound('Client not found.');
        }

        return $this->checkTenantIsolation($user, $client);
    }

    public function delete(User $user, User $client): Response
    {
        if (!$user->hasRole('manager')) {
            return Response::denyAsNotFound('Client not found.');
        }

        return $this->checkTenantIsolation($user, $client);
    }
}