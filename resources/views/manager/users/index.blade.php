<x-app-layout>
    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-wrap gap-4 justify-between items-end">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Financial Audits</h2>
                <p class="text-sm text-gray-600">Manage client documents and generate massive asynchronous reports.</p>
            </div>

            @can('create', App\Models\Report::class)
            <form action="{{ route('manager.financial.store') }}" method="POST" class="flex items-end gap-3">
                @csrf
                <input type="hidden" name="client_timezone" id="client_timezone">
                
                <div>
                    <label for="client_id" class="block text-xs font-medium text-gray-700 mb-1">Assign to Client</label>
                    <select name="client_id" id="client_id" required class="block w-48 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="" disabled selected>Select a client...</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="bg-gray-900 text-white px-4 py-2 rounded-md hover:bg-gray-800 transition shadow-sm font-medium text-sm h-[38px]">
                    Generate Heavy Audit
                </button>
            </form>
            @endcan
        </div>

        @if (session('success'))
        <div class="rounded-md bg-green-50 p-4 mb-6 border border-green-200">
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
        @endif

        @if (session('error'))
        <div class="rounded-md bg-red-50 p-4 mb-6 border border-red-200">
            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
        </div>
        @endif

        <div class="bg-white shadow sm:rounded-lg overflow-hidden border border-gray-200">
            <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">Document Name</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Client</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Requested At</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                        <th class="relative py-3.5 pl-3 pr-4 sm:pr-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($audits as $audit)
                    <tr data-report-id="{{ $audit->id }}" data-status="{{ $audit->status }}">
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900">
                            {{ $audit->name }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            {{ $audit->user->name ?? 'Unassigned' }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            <span class="local-date" data-utc-date="{{ $audit->created_at->toIso8601String() }}">
                                {{ $audit->created_at->format('M d, Y H:i') }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm status-cell">
                            @if(in_array($audit->status, ['completed', 'approved']))
                                <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">{{ ucfirst($audit->status) }}</span>
                            @elseif($audit->status === 'processing')
                                <span class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-700 ring-1 ring-inset ring-yellow-600/20">Processing...</span>
                            @elseif($audit->status === 'pending_review')
                                <span class="inline-flex items-center rounded-md bg-orange-50 px-2 py-1 text-xs font-medium text-orange-700 ring-1 ring-inset ring-orange-600/20">Pending Review</span>
                            @elseif(in_array($audit->status, ['failed', 'rejected']))
                                <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20">{{ ucfirst($audit->status) }}</span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">Pending</span>
                            @endif
                        </td>
                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6 action-cell">
                            @if($audit->status === 'pending_review')
                                @can('update', $audit)
                                    <div class="flex justify-end items-center gap-2">
                                        <a href="{{ \Illuminate\Support\Facades\URL::signedRoute('reports.download', $audit) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1.5 rounded-md text-xs font-semibold">
                                            Review File
                                        </a>
                                        <form action="{{ route('reports.approve', $audit) }}" method="POST" class="inline-block m-0">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="bg-emerald-600 text-white hover:bg-emerald-700 px-3 py-1.5 rounded-md text-xs font-semibold transition-colors">Approve</button>
                                        </form>
                                        <form action="{{ route('reports.reject', $audit) }}" method="POST" class="inline-block m-0">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="bg-red-600 text-white hover:bg-red-700 px-3 py-1.5 rounded-md text-xs font-semibold transition-colors">Reject</button>
                                        </form>
                                    </div>
                                @endcan
                            @elseif(in_array($audit->status, ['completed', 'approved']))
                                @can('view', $audit)
                                    <a href="{{ \Illuminate\Support\Facades\URL::signedRoute('reports.download', $audit) }}" class="text-indigo-600 hover:text-indigo-900 px-2 py-1 text-xs font-semibold">
                                        Download
                                    </a>
                                @endcan
                                @can('delete', $audit)
                                    <form action="{{ route('reports.destroy', $audit) }}" method="POST" class="inline-block ml-2 m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 px-2 py-1 text-xs font-semibold" onclick="return confirm('Are you sure you want to delete this document?')">Delete</button>
                                    </form>
                                @endcan
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-3 py-8 text-center text-sm text-gray-500">
                            No financial audits generated or uploaded yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="px-4 py-3 border-t border-gray-200">
                {{ $audits->links() }}
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tzInput = document.getElementById('client_timezone');
            if (tzInput) {
                try {
                    tzInput.value = Intl.DateTimeFormat().resolvedOptions().timeZone;
                } catch (e) {
                    tzInput.value = 'UTC';
                }
            }
        });
    </script>
</x-app-layout>