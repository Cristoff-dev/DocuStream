<x-app-layout :companies="$companies" :all-companies="$allCompanies" :managers="$managers">
@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator<\App\Models\Company> $companies */
    /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Company> $allCompanies */
    /** @var \Illuminate\Pagination\LengthAwarePaginator<\App\Models\User> $managers */
@endphp

<div class="min-h-screen bg-gray-50">
    <div class="max-w-screen-xl mx-auto px-6 py-10">
        
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Company & Operations Control</h1>
            <p class="mt-1 text-sm text-gray-500">Provision corporate accounts and branch managers globally.</p>
        </div>

        @if (session('success'))
        <div class="mb-6 flex items-center gap-2 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
            <svg class="h-4 w-4 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
            </svg>
            {{ session('success') }}
        </div>
        @endif

        <div class="flex gap-6 border-b border-gray-200 mb-8">
            <button id="btn-companies" onclick="switchTab('companies')" class="border-b-2 border-indigo-600 pb-3 text-sm font-semibold text-indigo-600 bg-transparent outline-none cursor-pointer">
                Companies
            </button>
            <button id="btn-managers" onclick="switchTab('managers')" class="border-b-2 border-transparent pb-3 text-sm font-medium text-gray-400 bg-transparent outline-none cursor-pointer">
                Branch Managers
            </button>
        </div>

        {{-- Companies Tab --}}
        <div id="tab-companies" class="flex flex-wrap gap-8 items-start">
            <div class="w-72 shrink-0 bg-white rounded-xl border border-gray-200 border-t-4 border-t-indigo-600 shadow-sm p-6">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-5">Provision New Company</p>
                <form method="POST" action="{{ route('admin.companies.store') }}" class="flex flex-col gap-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Company Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition @error('name') border-red-400 @enderror">
                        @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tax ID (EIN / RIF)</label>
                        <input type="text" name="registration_number" value="{{ old('registration_number') }}" required
                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition @error('registration_number') border-red-400 @enderror">
                        @error('registration_number')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="w-full rounded-md bg-indigo-600 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition shadow-sm">
                        Register Company
                    </button>
                </form>
            </div>

            <div class="flex-1 min-w-0 bg-white rounded-xl border border-gray-200 border-t-4 border-t-indigo-600 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <p class="text-sm font-semibold text-gray-900">Registered Companies</p>
                    <span class="rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-semibold text-indigo-700">
                        {{ $companies->total() }} total
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Company</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Tax ID</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Registered</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($companies as $company)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="text-sm font-semibold text-gray-900">{{ $company->name }}</p>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="font-mono text-sm text-gray-500">{{ $company->registration_number ?? '—' }}</span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    @if ($company->is_active)
                                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Active</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">Suspended</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-400">
                                    {{ $company->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="button" title="Edit company" onclick="toggleEditCompany('{{ $company->id }}')" class="inline-flex items-center justify-center h-8 w-8 rounded-md bg-blue-50 text-blue-600 hover:bg-blue-100 transition cursor-pointer">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
                                            </svg>
                                        </button>

                                        <form action="{{ route('admin.companies.update', $company) }}" method="POST" class="inline">
                                            @csrf @method('PUT')
                                            @if ($company->is_active)
                                                <input type="hidden" name="action" value="suspend">
                                                <button type="submit" title="Suspend company" class="inline-flex items-center justify-center h-8 w-8 rounded-md bg-amber-50 text-amber-600 hover:bg-amber-100 transition cursor-pointer">
                                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                    </svg>
                                                </button>
                                            @else
                                                <input type="hidden" name="action" value="activate">
                                                <button type="submit" title="Activate company" class="inline-flex items-center justify-center h-8 w-8 rounded-md bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition cursor-pointer">
                                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                </button>
                                            @endif
                                        </form>

                                        <form action="{{ route('admin.companies.destroy', $company) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this company?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" title="Delete company" class="inline-flex items-center justify-center h-8 w-8 rounded-md bg-red-50 text-red-600 hover:bg-red-100 transition cursor-pointer">
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <tr id="edit-row-company-{{ $company->id }}" class="hidden bg-gray-50 border-b border-gray-200">
                                <td colspan="5" class="px-6 py-4">
                                    <form action="{{ route('admin.companies.update', $company) }}" method="POST" class="flex items-center gap-3">
                                        @csrf @method('PUT')
                                        <input type="text" name="name" value="{{ old('name', $company->name) }}" required placeholder="Company Name"
                                            class="rounded-md border border-gray-300 px-3 py-1.5 text-sm bg-white outline-none focus:border-indigo-500">
                                        <input type="text" name="registration_number" value="{{ old('registration_number', $company->registration_number) }}" required placeholder="Tax ID"
                                            class="rounded-md border border-gray-300 px-3 py-1.5 text-sm bg-white outline-none focus:border-indigo-500">
                                        <button type="submit" class="rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-700 cursor-pointer">Save</button>
                                        <button type="button" onclick="toggleEditCompany('{{ $company->id }}')" class="rounded-md border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-600 bg-white cursor-pointer">Cancel</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center text-sm text-gray-400">
                                    No companies registered yet.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-100 bg-gray-50 px-6 py-3">
                    {{ $companies->appends(request()->except('page'))->links() }}
                </div>
            </div>
        </div>

        {{-- Managers Tab --}}
        <div id="tab-managers" class="hidden flex flex-wrap gap-8 items-start">
            <div class="w-72 shrink-0 bg-white rounded-xl border border-gray-200 border-t-4 border-t-violet-600 shadow-sm p-6">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-5">Provision Branch Manager</p>
                <form method="POST" action="{{ route('admin.managers.store') }}" class="flex flex-col gap-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Assign to Company</label>
                        <select name="company_id" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition @error('company_id') border-red-400 @enderror">
                            <option value="">Select…</option>
                            @foreach ($allCompanies as $c)
                            <option value="{{ $c->id }}" {{ old('company_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                        @error('company_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Full Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition @error('name') border-red-400 @enderror">
                        @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition @error('email') border-red-400 @enderror">
                        @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Temp Password</label>
                        <input type="password" name="password" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition @error('password') border-red-400 @enderror">
                        @error('password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-3">
                        <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-gray-500">Permissions</p>
                        <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer mb-1.5">
                            <input type="checkbox" name="permissions[]" value="request_heavy_audits" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            Request Heavy Audits
                        </label>
                        <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="delete_reports" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            Delete Branch Reports
                        </label>
                    </div>
                    <button type="submit" class="w-full rounded-md bg-violet-600 py-2 text-sm font-semibold text-white hover:bg-violet-700 transition shadow-sm cursor-pointer">
                        Create Manager
                    </button>
                </form>
            </div>

            <div class="flex-1 min-w-0 bg-white rounded-xl border border-gray-200 border-t-4 border-t-violet-600 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <p class="text-sm font-semibold text-gray-900">Branch Managers</p>
                    <span class="rounded-full bg-violet-50 px-2.5 py-0.5 text-xs font-semibold text-violet-700">
                        {{ $managers->total() }} total
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">User</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Company</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Scope</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($managers as $manager)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="text-sm font-semibold text-gray-900">{{ $manager->name }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $manager->email }}</p>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-600">
                                    {{ $manager->company->name ?? '—' }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    @if ($manager->is_active)
                                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Active</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">Suspended</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse($manager->permissions ?? [] as $perm)
                                            <span class="rounded bg-gray-100 border border-gray-200 px-1.5 py-0.5 text-[0.68rem] font-semibold text-gray-600">
                                                {{ str_replace('_', ' ', $perm) }}
                                            </span>
                                        @empty
                                            <span class="text-xs italic text-gray-400">Standard</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="button" title="Edit manager" onclick="toggleEditManager('{{ $manager->id }}')" class="inline-flex items-center justify-center h-8 w-8 rounded-md bg-blue-50 text-blue-600 hover:bg-blue-100 transition cursor-pointer">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
                                            </svg>
                                        </button>

                                        <form action="{{ route('admin.managers.update', $manager) }}" method="POST" class="inline">
                                            @csrf @method('PUT')
                                            @if ($manager->is_active)
                                                <input type="hidden" name="action" value="suspend">
                                                <button type="submit" title="Suspend manager" class="inline-flex items-center justify-center h-8 w-8 rounded-md bg-amber-50 text-amber-600 hover:bg-amber-100 transition cursor-pointer">
                                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                    </svg>
                                                </button>
                                            @else
                                                <input type="hidden" name="action" value="activate">
                                                <button type="submit" title="Activate manager" class="inline-flex items-center justify-center h-8 w-8 rounded-md bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition cursor-pointer">
                                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                </button>
                                            @endif
                                        </form>

                                        <form action="{{ route('admin.managers.destroy', $manager) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this manager?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" title="Delete manager" class="inline-flex items-center justify-center h-8 w-8 rounded-md bg-red-50 text-red-600 hover:bg-red-100 transition cursor-pointer">
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <tr id="edit-row-manager-{{ $manager->id }}" class="hidden bg-gray-50 border-b border-gray-200">
                                <td colspan="5" class="px-6 py-4">
                                    <form action="{{ route('admin.managers.update', $manager) }}" method="POST" class="flex flex-wrap items-center gap-3">
                                        @csrf @method('PUT')
                                        <input type="text" name="name" value="{{ $manager->name }}" required placeholder="Full Name"
                                            class="rounded-md border border-gray-300 px-3 py-1.5 text-sm bg-white outline-none focus:border-indigo-500">
                                        <input type="email" name="email" value="{{ $manager->email }}" required placeholder="Email"
                                            class="rounded-md border border-gray-300 px-3 py-1.5 text-sm bg-white outline-none focus:border-indigo-500">
                                        <input type="password" name="password" placeholder="New Password (optional)"
                                            class="rounded-md border border-gray-300 px-3 py-1.5 text-sm bg-white outline-none focus:border-indigo-500">
                                        <select name="company_id" required class="rounded-md border border-gray-300 px-3 py-1.5 text-sm bg-white outline-none focus:border-indigo-500">
                                            @foreach ($allCompanies as $c)
                                                <option value="{{ $c->id }}" {{ $manager->company_id == $c->id ? 'selected' : '' }}>
                                                    {{ $c->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-700 cursor-pointer">Save</button>
                                        <button type="button" onclick="toggleEditManager('{{ $manager->id }}')" class="rounded-md border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-600 bg-white cursor-pointer">Cancel</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center text-sm text-gray-400">
                                    No managers provisioned yet.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-100 bg-gray-50 px-6 py-3">
                    {{ $managers->appends(request()->except('managers_page'))->links() }}
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    function switchTab(tab) {
        ['companies', 'managers'].forEach(t => {
            document.getElementById('tab-' + t).classList.toggle('hidden', t !== tab);
            const btn = document.getElementById('btn-' + t);
            if (t === tab) {
                btn.classList.add('border-indigo-600', 'text-indigo-600', 'font-semibold');
                btn.classList.remove('border-transparent', 'text-gray-400', 'font-medium');
            } else {
                btn.classList.remove('border-indigo-600', 'text-indigo-600', 'font-semibold');
                btn.classList.add('border-transparent', 'text-gray-400', 'font-medium');
            }
        });
    }

    function toggleEditCompany(id) {
        document.getElementById('edit-row-company-' + id).classList.toggle('hidden');
    }

    function toggleEditManager(id) {
        document.getElementById('edit-row-manager-' + id).classList.toggle('hidden');
    }
</script>

@if(request()->has('managers_page') || old('company_id'))
<script>switchTab('managers');</script>
@endif

</x-app-layout>