<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Manager\StoreClientRequest;
use App\Http\Requests\Manager\UpdateClientRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', User::class);

        $clients = User::whereRelation('role', 'slug', 'client')
            ->with('clientProfile')
            ->latest()
            ->paginate(10);
            
        return view('manager.clients.index', compact('clients'));
    }

    public function create(): View
    {
        Gate::authorize('create', User::class);
        return view('manager.clients.create');
    }

    public function store(StoreClientRequest $request): RedirectResponse
    {
        Gate::authorize('create', User::class);

        $roleId = Role::where('slug', 'client')->value('id');

        DB::transaction(function () use ($request, $roleId) {
            $user = User::create([
                'name' => $request->validated('name'),
                'email' => $request->validated('email'),
                'password' => $request->validated('password'),
                'role_id' => $roleId,
                'company_id' => $request->user()->company_id,
                'is_active' => $request->boolean('is_active', true),
            ]);

            $user->clientProfile()->create([
                'legal_name' => $request->validated('legal_name'),
                'contact_email' => $request->validated('contact_email'),
                'tax_id' => $request->validated('tax_id'),
                'contact_phone' => $request->validated('contact_phone'),
                'country_code' => strtoupper($request->validated('country_code')),
            ]);
        });

        return redirect()->route('manager.clients.index')
            ->with('success', 'Client and corporate profile provisioned successfully.');
    }

    public function edit(User $client): View
    {
        Gate::authorize('update', $client);
        
        $client->load('clientProfile');
        
        return view('manager.clients.edit', compact('client'));
    }

    public function update(UpdateClientRequest $request, User $client): RedirectResponse
    {
        Gate::authorize('update', $client);

        DB::transaction(function () use ($request, $client) {
            $userData = [
                'name' => $request->validated('name'),
                'email' => $request->validated('email'),
                'is_active' => $request->boolean('is_active', false),
            ];

            if ($request->filled('password')) {
                $userData['password'] = $request->validated('password');
            }

            $client->update($userData);

            $client->clientProfile()->updateOrCreate(
                ['user_id' => $client->id],
                [
                    'legal_name' => $request->validated('legal_name'),
                    'contact_email' => $request->validated('contact_email'),
                    'tax_id' => $request->validated('tax_id'),
                    'contact_phone' => $request->validated('contact_phone'),
                    'country_code' => strtoupper($request->validated('country_code')),
                ]
            );
        });

        return redirect()->route('manager.clients.index')
            ->with('success', 'Client profile updated successfully.');
    }

    public function destroy(User $client): RedirectResponse
    {
        Gate::authorize('delete', $client);
        $client->delete();

        return back()->with('success', 'Client access and profile permanently removed.');
    }
}