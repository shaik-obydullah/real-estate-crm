
<div class="w-full space-y-6">
    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('contacts.index') }}" wire:navigate class="text-gray-400 hover:text-gray-600 p-2 hover:bg-gray-100 rounded-lg transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Contact</h1>
            <p class="text-sm text-gray-500">Update {{ $contact->first_name }} {{ $contact->last_name }}'s information</p>
        </div>
    </div>

    {{-- Form --}}
    <form wire:submit="save" class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 space-y-6">
            {{-- Company --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fas fa-building text-blue-500"></i> Company
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Company <span class="text-red-500">*</span></label>
                        <select wire:model="customer_id" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white transition @error('customer_id') border-red-500 @enderror">
                            <option value="">Select company...</option>
                            @foreach($this->customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                        @error('customer_id') <p class="text-xs text-red-500 mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                    </div>
                    <div class="flex items-end">
                        <label class="inline-flex items-center gap-3 cursor-pointer pb-2.5">
                            <input wire:model="is_primary" type="checkbox" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="text-sm font-medium text-gray-700">Primary Contact</span>
                        </label>
                    </div>
                </div>
            </div>

            <hr class="border-gray-100">

            {{-- Contact Details --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fas fa-user text-blue-500"></i> Contact Details
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
                        <input wire:model="first_name" type="text" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition @error('first_name') border-red-500 @enderror" placeholder="Enter first name">
                        @error('first_name') <p class="text-xs text-red-500 mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
                        <input wire:model="last_name" type="text" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition @error('last_name') border-red-500 @enderror" placeholder="Enter last name">
                        @error('last_name') <p class="text-xs text-red-500 mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input wire:model="email" type="email" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition @error('email') border-red-500 @enderror" placeholder="email@example.com">
                        @error('email') <p class="text-xs text-red-500 mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input wire:model="phone" type="text" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition" placeholder="+1 (555) 000-0000">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp</label>
                        <input wire:model="whatsapp" type="text" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition" placeholder="+1 (555) 000-0000">
                    </div>
                </div>
            </div>

            <hr class="border-gray-100">

            {{-- Job Information --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fas fa-briefcase text-blue-500"></i> Job Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Position / Title</label>
                        <input wire:model="position" type="text" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition" placeholder="e.g. VP Engineering">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                        <input wire:model="department" type="text" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition" placeholder="e.g. Engineering">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Birthday</label>
                        <input wire:model="birthday" type="date" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition @error('birthday') border-red-500 @enderror">
                        @error('birthday') <p class="text-xs text-red-500 mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <hr class="border-gray-100">

            {{-- Notes --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fas fa-sticky-note text-blue-500"></i> Notes
                </h3>
                <textarea wire:model="notes" rows="3" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition resize-none" placeholder="Add any additional notes about this contact..."></textarea>
            </div>
        </div>

        {{-- Actions --}}
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-3 rounded-b-xl">
            <a href="{{ route('contacts.index') }}" wire:navigate class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                <i class="fas fa-times mr-1"></i> Cancel
            </a>
            <button type="submit" wire:loading.attr="disabled" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50 transition shadow-sm">
                <span wire:loading.remove wire:target="save"><i class="fas fa-save mr-1"></i> Update Contact</span>
                <span wire:loading wire:target="save"><i class="fas fa-spinner fa-spin mr-1"></i> Saving...</span>
            </button>
        </div>
    </form>
</div>
