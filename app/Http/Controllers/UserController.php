<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Manager\StoreUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', User::class);

        $users = User::where('company_id', $request->user()->company_id)
            ->with('role')
            ->latest()
            ->paginate(10);
            
        return view('manager.users.index', compact('users'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        Gate::authorize('create', User::class);

        $roleId = Role::where('slug', 'client')->value('id'); 

        User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
            'role_id' => $roleId,
            'company_id' => $request->user()->company_id,
            'is_active' => true,
        ]);

        return back()->with('success', 'Employee account created successfully.');
    }
}