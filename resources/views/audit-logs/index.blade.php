<x-app-layout>
@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator<\App\Models\AuditLog> $logs */
@endphp

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Security & Compliance Audit Trail</h2>
            <p class="text-sm text-gray-500">Immutable record of system activities.</p>
        </div>

        <div class="bg-white shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">Timestamp</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Actor & IP</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Event</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Target Model</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Metadata</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    <?php if ($logs->isEmpty()): ?>
                    <tr>
                        <td colspan="5" class="py-8 text-center text-sm text-gray-500">
                            No audit logs found. The system is clean.
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($logs as$log): ?>
                        <tr>
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-500">
                                <span data-utc-date="{{ $log->created_at->toIso8601String() }}">
                                    {{ $log->created_at->format('Y-m-d H:i:s') }}
                                </span>
                            </td>
                            <td class="px-3 py-4 text-sm text-gray-900">
                                <div class="font-medium">{{ $log->user->name ?? 'System/Unknown' }}</div>
                                <div class="text-gray-500 text-xs">{{ $log->ip_address }}</div>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm">
                                @if($log->action === 'created')
                                    <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Created</span>
                                @elseif($log->action === 'updated')
                                    <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">Updated</span>
                                @elseif($log->action === 'deleted')
                                    <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10">Deleted</span>
                                @else
                                    <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">{{ ucfirst($log->action) }}</span>
                                @endif
                            </td>
                            <td class="px-3 py-4 text-sm text-gray-500">
                                {{ class_basename($log->model_type) }} #{{ $log->model_id }}
                            </td>
                            <td class="px-3 py-4 text-sm text-gray-500">
                                <button 
                                    type="button"
                                    data-payload="{{ json_encode($log->metadata, JSON_HEX_QUOT | JSON_HEX_APOS | JSON_HEX_TAG | JSON_HEX_AMP) }}" 
                                    onclick="showPayloadModal(this.dataset.payload)" 
                                    class="text-indigo-600 hover:text-indigo-900 hover:underline font-medium"
                                >
                                    View Payload
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            
            @if($logs->hasPages())
                <div class="border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>

    <dialog id="payloadModal" class="p-0 rounded-lg shadow-xl backdrop:bg-gray-900/50 w-full max-w-3xl border-0">
        <div class="bg-white">
            <div class="px-6 py-5 border-b border-gray-200 flex justify-between items-center bg-gray-50 rounded-t-lg">
                <h3 class="text-lg leading-6 font-semibold text-gray-900">Event Metadata</h3>
                <button onclick="document.getElementById('payloadModal').close()" class="text-gray-400 hover:text-gray-500 transition-colors">
                    <span class="text-2xl leading-none">&times;</span>
                </button>
            </div>
            <div class="px-6 py-5" id="payloadContent"></div>
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-lg flex justify-end">
                <button type="button" onclick="document.getElementById('payloadModal').close()" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </dialog>

    <script>
        function showPayloadModal(payloadString) {
            const modal = document.getElementById('payloadModal');
            const content = document.getElementById('payloadContent');
            
            try {
                const parsed = JSON.parse(payloadString);
                
                if (!parsed || (Object.keys(parsed).length === 0 && parsed.constructor === Object)) {
                    content.innerHTML = `
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-semibold text-gray-900">No Metadata Available</h3>
                            <p class="mt-1 text-sm text-gray-500">No metadata changes were recorded for this event.</p>
                        </div>
                    `;
                    modal.showModal();
                    return;
                }
                
                if (parsed.old_values || parsed.new_values) {
                    let html = '<div class="space-y-6">';
                    
                    if (parsed.user_agent) {
                        html += `
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-2">User Agent</h4>
                                <div class="bg-gray-100 p-3 rounded-md text-xs text-gray-700 break-all font-mono border border-gray-200">
                                    ${parsed.user_agent}
                                </div>
                            </div>
                        `;
                    }

                    let allKeys = new Set([
                        ...Object.keys(parsed.old_values || {}),
                        ...Object.keys(parsed.new_values || {})
                    ]);
                    
                    const ignoredFields = ['remember_token', 'email_verified_at', 'password'];
                    ignoredFields.forEach(field => allKeys.delete(field));

                    if (allKeys.size > 0) {
                        html += `
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Data Changes</h4>
                                <div class="border border-gray-200 rounded-md overflow-hidden">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-1/4">Field</th>
                                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-3/8">Previous Value</th>
                                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-3/8">New Value</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                        `;

                        for (const key of allKeys) {
                            const oldVal = parsed.old_values ? parsed.old_values[key] : undefined;
                            const newVal = parsed.new_values ? parsed.new_values[key] : undefined;
                            
                            const escapeHtml = (unsafe) => {
                                return String(unsafe)
                                     .replace(/&/g, "&amp;")
                                     .replace(/</g, "&lt;")
                                     .replace(/>/g, "&gt;")
                                     .replace(/"/g, "&quot;")
                                     .replace(/'/g, "&#039;");
                            };

                            const oldStr = oldVal !== undefined && oldVal !== null ? escapeHtml(oldVal) : '<em>null</em>';
                            const newStr = newVal !== undefined && newVal !== null ? escapeHtml(newVal) : '<em>null</em>';
                            
                            const changed = oldVal !== newVal;
                            
                            html += `
                                <tr class="${changed ? 'bg-amber-50/10' : ''} hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 whitespace-nowrap">${key}</td>
                                    <td class="px-4 py-3 text-sm font-mono break-all ${changed && oldVal !== undefined ? 'text-red-700 bg-red-50/50' : 'text-gray-500'}">${oldVal !== undefined ? oldStr : '-'}</td>
                                    <td class="px-4 py-3 text-sm font-mono break-all ${changed && newVal !== undefined ? 'text-green-700 bg-green-50/50' : 'text-gray-500'}">${newVal !== undefined ? newStr : '-'}</td>
                                </tr>
                            `;
                        }

                        html += `
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        `;
                    }
                    
                    html += '</div>';
                    content.innerHTML = html;
                } else {
                    content.innerHTML = `<pre class="text-sm text-gray-800 overflow-x-auto whitespace-pre-wrap font-mono p-4 bg-gray-50 rounded-md border border-gray-200">${JSON.stringify(parsed, null, 2)}</pre>`;
                }
            } catch (e) {
                content.innerHTML = `<pre class="text-sm text-gray-800 p-4 font-mono break-all">${payloadString}</pre>`;
            }
            
            modal.showModal();
        }
    </script>
</x-app-layout>