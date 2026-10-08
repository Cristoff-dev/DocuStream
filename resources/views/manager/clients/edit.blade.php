<x-app-layout>
    <div class="min-h-screen bg-gray-50 py-10">
        <div class="max-w-4xl mx-auto px-6">
            
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-950 tracking-tight">
                    Edit Client: {{ $client->clientProfile?->legal_name ?? $client->name }}
                </h1>
                <p class="mt-1 text-sm text-gray-500">Update account credentials and corporate profile information.</p>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 border-t-4 border-t-indigo-600 shadow-sm overflow-hidden p-8">
                <form action="{{ route('manager.clients.update', $client) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-y-6 gap-x-8 md:grid-cols-2">
                        
                        <div class="md:col-span-2 border-b border-gray-100 pb-4 mb-2">
                            <h3 class="text-sm font-bold text-gray-800">Account Credentials</h3>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Account / User Name</label>
                            <input type="text" name="name" value="{{ old('name', $client->name) }}" required
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition">
                            @error('name') <span class="text-red-500 text-xs font-medium mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Login Email</label>
                            <input type="email" name="email" value="{{ old('email', $client->email) }}" required
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition">
                            @error('email') <span class="text-red-500 text-xs font-medium mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2 md:w-1/2 md:pr-4">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">
                                New Password <span class="text-gray-400 font-normal lowercase">(leave blank to keep current)</span>
                            </label>
                            <input type="password" name="password"
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition">
                            @error('password') <span class="text-red-500 text-xs font-medium mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2 border-b border-gray-100 pb-4 mt-4 mb-2">
                            <h3 class="text-sm font-bold text-gray-800">Corporate Profile</h3>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Legal Name / Company</label>
                            <input type="text" name="legal_name" value="{{ old('legal_name', $client->clientProfile?->legal_name) }}" required
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition">
                            @error('legal_name') <span class="text-red-500 text-xs font-medium mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Billing / Contact Email</label>
                            <input type="email" name="contact_email" value="{{ old('contact_email', $client->clientProfile?->contact_email) }}" required
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition">
                            @error('contact_email') <span class="text-red-500 text-xs font-medium mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Tax ID (EIN, VAT, RIF)</label>
                            <input type="text" name="tax_id" value="{{ old('tax_id', $client->clientProfile?->tax_id) }}"
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm font-mono text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition">
                            @error('tax_id') <span class="text-red-500 text-xs font-medium mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Contact Phone</label>
                            <input type="text" name="contact_phone" value="{{ old('contact_phone', $client->clientProfile?->contact_phone) }}"
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition">
                            @error('contact_phone') <span class="text-red-500 text-xs font-medium mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">
                                Country Code <span class="text-gray-400 font-normal lowercase">(ISO alpha-2)</span>
                            </label>
                            <input type="text" name="country_code" value="{{ old('country_code', $client->clientProfile?->country_code ?? 'US') }}" maxlength="2" required
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm uppercase font-mono text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition">
                            @error('country_code') <span class="text-red-500 text-xs font-medium mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex items-center pt-6">
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $client->is_active) ? 'checked' : '' }} 
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 shadow-sm">
                                <span class="text-sm font-medium text-gray-700">Active Client Account</span>
                            </label>
                        </div>
                    </div>

                    <div class="mt-8 pt-5 border-t border-gray-100 flex justify-end gap-3">
                        <a href="{{ route('manager.clients.index') }}" 
                            class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                            Cancel
                        </a>
                        <button type="submit" 
                            class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition shadow-sm">
                            Save Client Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>