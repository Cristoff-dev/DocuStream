<x-app-layout>
<div
    x-data="{
        deleteOpen: false,
        deleteForm: null,
        openDelete(form) {
            this.deleteForm = form;
            this.deleteOpen = true;
        }
    }"
    class="min-h-screen bg-gray-50"
>

    <div class="max-w-screen-xl mx-auto px-6 py-10">

        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-950 tracking-tight">Client Management</h1>
                <p class="mt-1 text-sm text-gray-500">Manage all client accounts within your tenant.</p>
            </div>
            <a href="{{ route('manager.clients.create') }}"
                class="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition shadow-sm font-semibold text-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Add New Client
            </a>
        </div>

        @if (session('success'))
        <div class="mb-6 flex items-center gap-2 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
            <svg class="h-4 w-4 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
            </svg>
            {{ session('success') }}
        </div>
        @endif

        <div class="bg-white rounded-xl border border-gray-200 border-t-4 border-t-indigo-600 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <p class="text-sm font-semibold text-gray-900">Registered Clients</p>
                <span class="rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-semibold text-indigo-700">
                    {{ $clients->total() }} total
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Legal Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Email</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Tax ID</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Country</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($clients as $client)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                {{ $client->clientProfile?->legal_name ?? $client->name }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $client->clientProfile?->contact_email ?? $client->email }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-mono text-gray-500">
                                {{ $client->clientProfile?->tax_id ?? '—' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 uppercase font-medium">
                                {{ $client->clientProfile?->country_code ?? 'US' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                @if($client->is_active)
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Active</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">Inactive</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('manager.clients.edit', $client) }}" title="Edit client"
                                        class="inline-flex items-center justify-center h-8 w-8 rounded-md bg-blue-50 text-blue-600 hover:bg-blue-100 transition">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
                                        </svg>
                                    </a>

                                    <form action="{{ route('manager.clients.destroy', $client) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="button" title="Delete client"
                                            @click.prevent="openDelete($event.target.closest('form'))"
                                            class="inline-flex items-center justify-center h-8 w-8 rounded-md bg-red-50 text-red-600 hover:bg-red-100 transition">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-sm text-gray-400">
                                No clients registered yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-gray-100 bg-gray-50 px-6 py-3">
                {{ $clients->links() }}
            </div>
        </div>

    </div>

    <template x-teleport="body">
    <div x-show="deleteOpen" style="display:none" class="relative z-50" role="dialog" aria-modal="true">
        <div x-show="deleteOpen" x-transition.opacity class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
        <div class="fixed inset-0 z-10 flex items-center justify-center p-4 overflow-y-auto">
            <div x-show="deleteOpen"
                @click.away="deleteOpen = false"
                x-transition.enter="ease-out duration-200"
                x-transition.enter-start="opacity-0 scale-95"
                x-transition.enter-end="opacity-100 scale-100"
                x-transition.leave="ease-in duration-150"
                x-transition.leave-start="opacity-100 scale-100"
                x-transition.leave-end="opacity-0 scale-95"
                class="w-full max-w-sm bg-white rounded-xl shadow-2xl ring-1 ring-black/5 overflow-hidden">

                <div class="p-6">
                    <div class="flex gap-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-100">
                            <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">Confirm Deletion</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                This client and all associated data will be permanently removed.
                                This action <strong class="text-gray-700">cannot be undone</strong>.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-row-reverse gap-3 border-t border-gray-100 bg-gray-50 px-6 py-4">
                    <button type="button"
                        @click="deleteForm && deleteForm.submit()"
                        class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 transition">
                        Delete Permanently
                    </button>
                    <button type="button" @click="deleteOpen = false"
                        class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
    </template>
</div>
</x-app-layout>