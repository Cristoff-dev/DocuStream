<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Admin\StoreCompanyRequest;
use App\Http\Requests\Admin\StoreManagerRequest;
use App\Http\Requests\Admin\UpdateCompanyRequest;
use App\Http\Requests\Admin\UpdateManagerRequest;
use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Company::class);

        return view('admin.dashboard', [
            'companies'    => Company::latest()->paginate(10),
            'allCompanies' => Company::orderBy('name')->get(['id', 'name']),
            'managers'     => User::whereRelation('role', 'slug', 'manager')
                                  ->with('company')
                                  ->latest()
                                  ->paginate(10, ['*'], 'managers_page'),
        ]);
    }

    public function store(StoreCompanyRequest $request): RedirectResponse
    {
        Gate::authorize('create', Company::class);

        Company::create([...$request->validated(), 'is_active' => true]);

        return back()->with('success', 'Company provisioned successfully.');
    }

    public function update(UpdateCompanyRequest $request, Company $company): RedirectResponse
    {
        Gate::authorize('update', $company);

        if ($request->has('action')) {
            $company->update(['is_active' => $request->input('action') === 'activate']);
            return back()->with('success', 'Company status updated.');
        }

        $data = $request->validated();
        $company->update([
            'name'                => $data['name'],
            'registration_number' => $data['registration_number'],
            'is_active'           => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Company updated successfully.');
    }

    public function destroy(Company $company): RedirectResponse
    {
        Gate::authorize('delete', $company);
        $company->delete();

        return back()->with('success', 'Company deleted.');
    }

    public function storeManager(StoreManagerRequest $request): RedirectResponse
    {
        Gate::authorize('create', User::class);

        $data        = $request->validated();
        $managerRole = Role::where('slug', 'manager')->firstOrFail();

        $existingUser = User::withTrashed()->where('email', $data['email'])->first();

        if ($existingUser) {
            if ($existingUser->trashed()) {
                $existingUser->restore();
                $existingUser->update([
                    'name'        => $data['name'],
                    'company_id'  => $data['company_id'],
                    'password'    => Hash::make($data['password']),
                    'role_id'     => $managerRole->id,
                    'permissions' => $data['permissions'] ?? [],
                    'is_active'   => true,
                ]);

                return back()->with('success', 'Branch Manager restored and assigned successfully.');
            }

            return back()->withErrors(['email' => 'This email address is already in use.'])->withInput();
        }

        User::create([
            'name'        => $data['name'],
            'email'       => $data['email'],
            'password'    => Hash::make($data['password']),
            'role_id'     => $managerRole->id,
            'company_id'  => $data['company_id'],
            'permissions' => $data['permissions'] ?? [],
            'is_active'   => true,
        ]);

        return back()->with('success', 'Branch Manager provisioned and assigned successfully.');
    }

    public function updateManager(UpdateManagerRequest $request, User $user): RedirectResponse
    {
        Gate::authorize('update', $user);

        if ($request->has('action')) {
            $user->update(['is_active' => $request->input('action') === 'activate']);
            return back()->with('success', 'Manager status updated.');
        }

        $data = $request->validated();

        $user->update([
            'name'        => $data['name'],
            'email'       => $data['email'],
            'company_id'  => $data['company_id'],
            'permissions' => $data['permissions'] ?? [],
        ]);

        if (filled($data['password'] ?? null)) {
            $user->update(['password' => Hash::make($data['password'])]);
        }

        return back()->with('success', 'Manager updated successfully.');
    }

    public function destroyManager(User $user): RedirectResponse
    {
        Gate::authorize('delete', $user);
        $user->delete();

        return back()->with('success', 'Manager removed.');
    }
}