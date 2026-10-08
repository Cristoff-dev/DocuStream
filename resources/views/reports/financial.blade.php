<x-app-layout>
<div class="min-h-screen bg-gray-50 py-10">
    <div class="max-w-screen-xl mx-auto px-6">

        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Financial Audits</h2>
            <p class="mt-1 text-sm text-gray-500">Queue and monitor heavy corporate financial audit reports.</p>
        </div>

        @can('create', App\Models\Report::class)
        <div class="mb-6 bg-white rounded-xl border border-gray-200 border-t-4 border-t-indigo-600 shadow-sm px-6 py-5">
            <form action="{{ route('manager.financial.store') }}" method="POST" class="flex flex-wrap items-end gap-4">
                @csrf
                <input type="hidden" name="client_timezone" id="client_timezone">

                <div class="min-w-0 w-full sm:w-72">
                    <label for="client_id" class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">
                        Target Client
                    </label>
                    <select name="client_id" id="client_id" required class="block w-full rounded-md border border-gray-300 py-2 pl-3 pr-8 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 hover:border-gray-400 transition-colors">
                        <option value="" disabled selected>Select a client…</option>
                        <?php 
                        if (isset($clients)) {
                            foreach ($clients as $client) {
                                echo '<option value="' . $client->id . '">' . htmlspecialchars($client->name) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <button type="submit" class="inline-flex shrink-0 items-center gap-2 rounded-md bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 transition whitespace-nowrap">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    Generate Audit
                </button>
            </form>
        </div>
        @endcan

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

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Audit Reference</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Assigned To</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Generated</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Rows</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($audits->isEmpty()): ?>
                        <tr>
                            <td colspan="6" class="py-16 text-center">
                                <p class="text-sm font-medium text-gray-500">No financial audits generated yet.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($audits as $audit): ?>
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors" data-report-id="{{ $audit->id }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="text-sm font-semibold text-gray-900">{{ $audit->name }}</p>
                                    <p class="text-xs text-gray-400 font-mono mt-0.5">#<?php echo substr($audit->id, 0, 8); ?></p>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-600">
                                    {{ $audit->user->name ?? '—' }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <p class="text-sm text-gray-700"><?php echo $audit->created_at->format('M d, Y'); ?></p>
                                    <p class="text-xs text-gray-400"><?php echo $audit->created_at->format('H:i'); ?></p>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap audit-status-cell">
                                    <?php if (in_array($audit->status, ['completed', 'approved'])): ?>
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 text-xs font-medium text-emerald-700">
                                            <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-emerald-500"></span><?php echo ucfirst($audit->status); ?>
                                        </span>
                                    <?php elseif ($audit->status === 'processing'): ?>
                                        <span class="inline-flex items-center rounded-full bg-amber-50 border border-amber-200 px-2.5 py-0.5 text-xs font-medium text-amber-700 animate-pulse">
                                            <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-amber-400"></span>Processing…
                                        </span>
                                    <?php elseif ($audit->status === 'pending_review'): ?>
                                        <span class="inline-flex items-center rounded-full bg-orange-50 border border-orange-200 px-2.5 py-0.5 text-xs font-medium text-orange-700">
                                            <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-orange-500"></span>Pending Review
                                        </span>
                                    <?php elseif (in_array($audit->status, ['failed', 'rejected'])): ?>
                                        <span class="inline-flex items-center rounded-full bg-red-50 border border-red-200 px-2.5 py-0.5 text-xs font-medium text-red-700">
                                            <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-red-500"></span><?php echo ucfirst($audit->status); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center rounded-full bg-gray-50 border border-gray-200 px-2.5 py-0.5 text-xs font-medium text-gray-600 animate-pulse">
                                            <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-gray-400"></span>Pending
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-500 tabular-nums">
                                    {{ number_format($audit->processed_rows) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right audit-actions-cell">
                                    <?php if ($audit->status === 'pending_review'): ?>
                                        @can('update', $audit)
                                            <div class="flex justify-end items-center gap-2">
                                                <a href="{{ \Illuminate\Support\Facades\URL::signedRoute('reports.download', $audit) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1.5 rounded-md text-xs font-semibold">
                                                    Review
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
                                    <?php elseif (in_array($audit->status, ['completed', 'approved']) && $audit->file_path): ?>
                                        <a href="{{ \Illuminate\Support\Facades\URL::signedRoute('reports.download', ['report' => $audit->id]) }}" title="Download PDF" class="inline-flex items-center gap-1 p-2 text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-md transition-colors">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                                            </svg>
                                        </a>
                                    <?php elseif (in_array($audit->status, ['failed', 'rejected'])): ?>
                                        <span class="text-xs italic text-red-400">Action Required</span>
                                    <?php else: ?>
                                        <span class="text-xs italic text-gray-400">Processing…</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">{{ $audits->links() }}</div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const tz = document.getElementById('client_timezone');
    if (tz) {
        try { tz.value = Intl.DateTimeFormat().resolvedOptions().timeZone; }
        catch { tz.value = 'UTC'; }
    }
});
</script>
</x-app-layout>