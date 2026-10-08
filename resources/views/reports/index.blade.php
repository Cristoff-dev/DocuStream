<x-app-layout>
    <div x-data="{ showDeleteModal: false, deleteForm: null }" @open-delete-modal.window="showDeleteModal = true; deleteForm = $event.detail.form" class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">General Reports</h2>
            
            @can('create', App\Models\Report::class)
                <form action="{{ route('reports.store') }}" method="POST" id="standard-report-form">
                    @csrf
                    <input type="hidden" name="client_timezone" id="standard_client_timezone">
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition shadow-sm font-medium text-sm">
                        Generate Standard Report
                    </button>
                </form>
            @endcan
        </div>

        <div class="bg-white shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">Report Reference</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Requested At</th>
                        <th class="relative py-3.5 pl-3 pr-4 sm:pr-6 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse ($reports as $report)
                        <tr id="report-row-{{ $report->id }}" data-report-id="{{ $report->id }}" data-status="{{ $report->status }}">
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900">
                                {{ $report->name }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm status-cell">
                                @if($report->status === 'completed')
                                    <span class="text-green-700 bg-green-50 ring-green-600/20 inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset">Completed</span>
                                @elseif($report->status === 'processing')
                                    <span class="text-blue-700 bg-blue-50 ring-blue-600/20 inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset cursor-wait animate-pulse">Processing...</span>
                                @elseif($report->status === 'failed')
                                    <span class="text-red-700 bg-red-50 ring-red-600/10 inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset">Failed</span>
                                @else
                                    <span class="text-gray-600 bg-gray-50 ring-gray-500/10 inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset">Pending</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                <span class="local-date" data-utc="{{ $report->created_at->toIso8601String() }}">
                                    {{ $report->created_at->format('M d, Y') }}
                                </span>
                            </td>
                            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6 action-cell flex justify-end items-center gap-3">
                                @if($report->status === 'completed')
                                    @can('view', $report)
                                        <a href="{{ \Illuminate\Support\Facades\URL::signedRoute('reports.download', $report) }}" class="text-indigo-600 hover:text-indigo-900">Download</a>
                                    @endcan
                                    
                                    @can('delete', $report)
                                        <form action="{{ route('reports.destroy', $report) }}" method="POST" class="inline-block m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" @click="$dispatch('open-delete-modal', { form:$event.target.closest('form') })" class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    @endcan
                                @else
                                    <span class="text-gray-400 cursor-not-allowed">Wait</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-3 py-8 text-center text-sm text-gray-500">
                                No reports found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $reports->links() }}
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const standardForm = document.getElementById('standard-report-form');
            if (standardForm) {
                standardForm.addEventListener('submit', function() {
                    document.getElementById('standard_client_timezone').value = Intl.DateTimeFormat().resolvedOptions().timeZone;
                });
            }

            document.querySelectorAll('.local-date').forEach(el => {
                const utcString = el.getAttribute('data-utc');
                if (utcString) {
                    const date = new Date(utcString);
                    el.textContent = date.toLocaleDateString(undefined, { 
                        year: 'numeric', month: 'short', day: 'numeric', 
                        hour: '2-digit', minute:'2-digit' 
                    });
                }
            });

            const rows = document.querySelectorAll('tr[data-report-id]');
            rows.forEach(row => {
                const reportId = row.getAttribute('data-report-id');
                let status = row.getAttribute('data-status');

                if (status === 'completed' || status === 'failed') return;

                const interval = setInterval(async () => {
                    try {
                        const response = await fetch(`/reports/${reportId}/status`);
                        if (!response.ok) return;
                        const data = await response.json();

                        if (data.status !== status) {
                            status = data.status;
                            row.setAttribute('data-status', status);

                            const statusCell = row.querySelector('.status-cell');
                            const actionCell = row.querySelector('.action-cell');

                            if (status === 'completed') {
                                statusCell.innerHTML = `<span class="text-green-700 bg-green-50 ring-green-600/20 inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset">Completed</span>`;
                                
                                actionCell.innerHTML = '';
                                
                                if(data.download_url) {
                                    const link = document.createElement('a');
                                    link.href = data.download_url;
                                    link.className = 'text-indigo-600 hover:text-indigo-900';
                                    link.textContent = 'Download';
                                    actionCell.appendChild(link);
                                }

                                if(data.can_delete && data.delete_url) {
                                    const form = document.createElement('form');
                                    form.action = data.delete_url;
                                    form.method = 'POST';
                                    form.className = 'inline-block m-0 ml-3';
                                    
                                    form.innerHTML = `
                                        <input type="hidden" name="_token" value="${data.csrf_token}">
                                        <input type="hidden" name="_method" value="DELETE">
                                    `;
                                    
                                    const btn = document.createElement('button');
                                    btn.type = 'button';
                                    btn.className = 'text-red-600 hover:text-red-900';
                                    btn.textContent = 'Delete';
                                    btn.addEventListener('click', (e) => {
                                        document.dispatchEvent(new CustomEvent('open-delete-modal', { detail: { form: e.target.closest('form') } }));
                                    });
                                    
                                    form.appendChild(btn);
                                    actionCell.appendChild(form);
                                }

                                clearInterval(interval);
                            } else if (status === 'failed') {
                                statusCell.innerHTML = `<span class="text-red-700 bg-red-50 ring-red-600/10 inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset">Failed</span>`;
                                actionCell.innerHTML = '';
                                clearInterval(interval);
                            }
                        }
                    } catch (e) { console.error('Error polling report status', e); }
                }, 3000);
            });
        });
    </script>

    <div x-show="showDeleteModal" style="display: none;" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="showDeleteModal" x-transition.opacity class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="showDeleteModal" @click.away="showDeleteModal = false" class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <h3 class="text-base font-semibold leading-6 text-gray-900">Confirm Deletion</h3>
                                <p class="text-sm text-gray-500 mt-2">Are you sure you want to delete this report? This action cannot be undone.</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-3">
                        <button type="button" @click="if(deleteForm) deleteForm.submit()" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-500 sm:w-auto">Delete</button>
                        <button type="button" @click="showDeleteModal = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-50 sm:mt-0 sm:w-auto">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>