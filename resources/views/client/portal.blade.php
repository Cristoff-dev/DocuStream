<x-app-layout>
@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator<\App\Models\Report> $reports */
@endphp

<div class="min-h-screen bg-gray-50 py-10">
    <div class="max-w-screen-xl mx-auto px-6" x-data="{ showDeleteModal: false, deleteForm: null }" @open-delete-modal.window="showDeleteModal = true; deleteForm = $event.detail.form">

        <div class="mb-6 flex flex-wrap justify-between items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Corporate Documents Portal</h2>
                <p class="mt-1 text-sm text-gray-500">Access your secure financial audits and reports.</p>
            </div>

            @can('create', App\Models\Report::class)
                @if(auth()->user()->hasRole('manager'))
                <form action="{{ route('reports.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="client_timezone" id="client_timezone">
                    <button type="submit" class="inline-flex shrink-0 items-center gap-2 rounded-md bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 transition whitespace-nowrap">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        Generate Standard Report
                    </button>
                </form>
                @endif
            @endcan
        </div>

        @if (session('success'))
        <div class="mb-6 flex items-center gap-2 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
            <svg class="h-4 w-4 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
            </svg>
            {{ session('success') }}
        </div>
        @endif

        @if (session('error'))
        <div class="mb-6 flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">
            <svg class="h-4 w-4 shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            {{ session('error') }}
        </div>
        @endif

        @if(auth()->user()->hasRole('client'))
        <div class="mb-8 bg-white rounded-xl border border-dashed border-gray-300 p-6 shadow-sm">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Upload Document for Review</h3>
            <form action="{{ route('reports.upload') }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-4 items-start sm:items-end">
                @csrf
                <div class="w-full sm:w-1/3">
                    <label for="name" class="block text-sm font-medium text-gray-700">Document Title</label>
                    <input type="text" name="name" id="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @error('name')<span class="text-xs text-red-500 mt-1">{{ $message }}</span>@enderror
                </div>
                <div class="w-full sm:w-1/3">
                    <label for="document" class="block text-sm font-medium text-gray-700">File (PDF, Excel, Word)</label>
                    <input type="file" name="document" id="document" accept=".pdf,.xlsx,.xls,.doc,.docx" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    @error('document')<span class="text-xs text-red-500 mt-1">{{ $message }}</span>@enderror
                </div>
                <div class="w-full sm:w-auto">
                    <button type="submit" class="w-full sm:w-auto inline-flex justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                        Secure Upload
                    </button>
                </div>
            </form>
        </div>
        @endif

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Document Name</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Upload Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Rows</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse ($reports as $report)
                    <tr data-report-id="{{ $report->id }}">
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900">
                            {{ $report->name }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            <span>{{ $report->created_at->format('M d, Y H:i') }}</span>
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm report-status-cell">
                            @if(in_array($report->status, ['completed', 'approved']))
                                <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">{{ ucfirst($report->status) }}</span>
                            @elseif($report->status === 'processing')
                                <span class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-700 ring-1 ring-inset ring-yellow-600/20">Processing...</span>
                            @elseif($report->status === 'pending_review')
                                <span class="inline-flex items-center rounded-md bg-orange-50 px-2 py-1 text-xs font-medium text-orange-700 ring-1 ring-inset ring-orange-600/20">Pending Review</span>
                            @elseif(in_array($report->status, ['failed', 'rejected']))
                                <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20">{{ ucfirst($report->status) }}</span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">Pending</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            {{ number_format($report->processed_rows) }}
                        </td>
                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6 report-actions-cell">
                            @if(in_array($report->status, ['completed', 'approved', 'pending_review']))
                                @can('view', $report)
                                <a href="{{ \Illuminate\Support\Facades\URL::signedRoute('reports.download', ['report' => $report->id]) }}" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1.5 rounded-md text-xs font-semibold mr-2">
                                    Download Securely
                                </a>
                                @can('delete', $report)
                                <form action="{{ route('reports.destroy', $report) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" @click="$dispatch('open-delete-modal', { form:$event.target.closest('form') })" class="text-red-600 hover:text-red-900 text-xs font-semibold">Delete</button>
                                </form>
                                @endcan
                                @endcan
                            @elseif($report->status === 'rejected')
                                <span class="text-xs text-red-500 italic">Action Required</span>
                            @else
                                <span class="text-xs text-gray-400 italic">Not ready</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-3 py-8 text-center text-sm text-gray-500">
                            No documents are currently available for your account.
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

    <div id="toast-container" class="fixed bottom-5 right-5 z-50 space-y-2"></div>

    <div x-show="showDeleteModal" style="display: none;" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="showDeleteModal" x-transition.opacity class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="showDeleteModal" @click.away="showDeleteModal = false" x-transition.enter="ease-out duration-300" x-transition.enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition.enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition.leave="ease-in duration-200" x-transition.leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition.leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">Confirm Deletion</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">Are you sure you want to permanently delete this report? This action cannot be undone and will be permanently removed from the server.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-3">
                        <button type="button" @click="if(deleteForm) deleteForm.submit()" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:w-auto">Delete Permanently</button>
                        <button type="button" @click="showDeleteModal = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Cancel</button>
                    </div>
                </div>
            </div>
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

    const showToast = (message, type = 'success') => {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const toast = document.createElement('div');
        const bgColor = type === 'success' ? 'bg-slate-900' : 'bg-red-600';

        toast.className = `${bgColor} text-white px-4 py-3 rounded-lg shadow-lg text-sm font-medium transition-all duration-300 transform translate-y-2 opacity-0 flex items-center space-x-2`;
        
        const textSpan = document.createElement('span');
        textSpan.textContent = message;
        toast.appendChild(textSpan);

        container.appendChild(toast);

        setTimeout(() => toast.classList.remove('translate-y-2', 'opacity-0'), 50);
        setTimeout(() => {
            toast.classList.add('translate-y-2', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    };

    const rows = document.querySelectorAll('tr[data-report-id]');

    rows.forEach(row => {
        const reportId = row.getAttribute('data-report-id');
        const statusCell = row.querySelector('.report-status-cell');
        const actionsCell = row.querySelector('.report-actions-cell');

        if (!statusCell || !actionsCell) return;

        let pollingInterval;

        const checkReportStatus = async () => {
            try {
                const response = await fetch(`/reports/${reportId}/status`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) return;

                const data = await response.json();

                if (data.is_completed) {
                    clearInterval(pollingInterval);
                    
                    const statusText = data.status.charAt(0).toUpperCase() + data.status.slice(1);
                    
                    statusCell.innerHTML = `
                        <span class="inline-flex items-center rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 text-xs font-medium text-emerald-700">
                            <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-emerald-500"></span>${statusText}
                        </span>`;

                    actionsCell.innerHTML = '';

                    const downloadBtn = document.createElement('a');
                    downloadBtn.href = data.download_url;
                    downloadBtn.className = 'text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1.5 rounded-md text-xs font-semibold mr-2';
                    downloadBtn.textContent = 'Download Securely';
                    actionsCell.appendChild(downloadBtn);

                    if (data.can_delete) {
                        const form = document.createElement('form');
                        form.action = data.delete_url;
                        form.method = 'POST';
                        form.className = 'inline-block';
                        
                        form.innerHTML = `
                            <input type="hidden" name="_token" value="${data.csrf_token}">
                            <input type="hidden" name="_method" value="DELETE">
                        `;
                        
                        const deleteBtn = document.createElement('button');
                        deleteBtn.type = 'button';
                        deleteBtn.className = 'text-red-600 hover:text-red-900 text-xs font-semibold';
                        deleteBtn.textContent = 'Delete';
                        deleteBtn.addEventListener('click', (e) => {
                            document.dispatchEvent(new CustomEvent('open-delete-modal', { detail: { form: e.target.closest('form') } }));
                        });

                        form.appendChild(deleteBtn);
                        actionsCell.appendChild(form);
                    }

                    showToast('Your corporate document is ready for download.');
                } else if (data.status === 'failed') {
                    clearInterval(pollingInterval);
                    
                    statusCell.innerHTML = `
                        <span class="inline-flex items-center rounded-full bg-red-50 border border-red-200 px-2.5 py-0.5 text-xs font-medium text-red-700">
                            <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-red-500"></span>Failed
                        </span>`;
                    
                    actionsCell.innerHTML = '';
                    const errorSpan = document.createElement('span');
                    errorSpan.className = 'text-xs italic text-red-400';
                    errorSpan.textContent = 'Error processing';
                    actionsCell.appendChild(errorSpan);
                    
                    showToast('Report generation failed. Please try again.', 'error');
                }
            } catch (error) {
                console.error('Error polling report status:', error);
            }
        };

        const initialStatus = statusCell.textContent.trim();
        if (initialStatus === 'Processing...' || initialStatus === 'Pending') {
            pollingInterval = setInterval(checkReportStatus, 4000);
        }
    });
});
</script>
</x-app-layout>