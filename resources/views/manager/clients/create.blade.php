<x-app-layout>
    <div class="min-h-screen bg-gray-50 py-10">
        <div class="max-w-4xl mx-auto px-6">
            
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-950 tracking-tight">Register New Client</h1>
                <p class="mt-1 text-sm text-gray-500">Add an international client entity to your enterprise portfolio.</p>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 border-t-4 border-t-indigo-600 shadow-sm overflow-hidden p-8">
                <form action="{{ route('manager.clients.store') }}" method="POST">
                    @csrf

                    @if ($errors->any())
                        <div class="mb-6 rounded-md bg-red-50 p-4 border border-red-200 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 gap-y-6 gap-x-8 md:grid-cols-2">
                        
                        <div class="md:col-span-2 border-b border-gray-100 pb-4 mb-2">
                            <h3 class="text-sm font-bold text-gray-800">Account Credentials</h3>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">System Username</label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition">
                            @error('name') <span class="text-red-500 text-xs font-medium mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Login Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition">
                            @error('email') <span class="text-red-500 text-xs font-medium mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2 md:w-1/2 md:pr-4">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Temporary Password</label>
                            <input type="password" name="password" required
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition">
                            @error('password') <span class="text-red-500 text-xs font-medium mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2 border-b border-gray-100 pb-4 mt-4 mb-2">
                            <h3 class="text-sm font-bold text-gray-800">Corporate Profile</h3>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Legal Name / Company</label>
                            <input type="text" name="legal_name" value="{{ old('legal_name') }}" required
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition">
                            @error('legal_name') <span class="text-red-500 text-xs font-medium mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Corporate Contact Email</label>
                            <input type="email" name="contact_email" value="{{ old('contact_email') }}" required
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition">
                            @error('contact_email') <span class="text-red-500 text-xs font-medium mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Tax ID (EIN, VAT, RIF)</label>
                            <input type="text" name="tax_id" value="{{ old('tax_id') }}"
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm font-mono text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition">
                            @error('tax_id') <span class="text-red-500 text-xs font-medium mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Contact Phone</label>
                            <input type="text" name="contact_phone" value="{{ old('contact_phone') }}"
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition">
                            @error('contact_phone') <span class="text-red-500 text-xs font-medium mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1.5">Country Code (ISO alpha-2)</label>
                            <input type="text" name="country_code" value="{{ old('country_code', 'US') }}" maxlength="2" required
                                class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm uppercase font-mono text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition">
                            @error('country_code') <span class="text-red-500 text-xs font-medium mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex items-center pt-6">
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <!-- Protección de variable ausente añadida -->
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} 
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
                            Save Client
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>