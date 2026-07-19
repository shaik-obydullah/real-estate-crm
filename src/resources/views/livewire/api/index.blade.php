
<div class="space-y-6" x-data="{ open: false, showKeys: {} }" x-on:open-modal.window="open = true">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">API Keys</h1>
            <p class="text-sm text-gray-500">Manage API access keys for external integrations</p>
        </div>
        <button wire:click="$set('showModal', true)" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus"></i> Generate Key
        </button>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if($newKeyShown)
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg text-sm" x-data>
            <div class="flex items-start gap-3">
                <i class="fas fa-key mt-0.5"></i>
                <div class="flex-1">
                    <p class="font-medium">Your new API key</p>
                    <div class="mt-2 flex items-center gap-2">
                        <code class="bg-white px-3 py-1.5 rounded border border-yellow-300 text-xs font-mono break-all">{{ $newKeyShown }}</code>
                        <button @click="navigator.clipboard.writeText('{{ $newKeyShown }}')" class="px-2 py-1 text-xs bg-white border border-yellow-300 rounded hover:bg-yellow-50">Copy</button>
                    </div>
                    <p class="mt-2 text-xs text-yellow-700">Copy this key now. It will not be shown again.</p>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-4 border-b border-gray-100">
            <input wire:model.live="search" type="text" placeholder="Search API keys..." class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="px-6 py-3 font-medium text-gray-500">Name</th>
                        <th class="px-6 py-3 font-medium text-gray-500 hidden md:table-cell">Key</th>
                        <th class="px-6 py-3 font-medium text-gray-500 hidden md:table-cell">Permissions</th>
                        <th class="px-6 py-3 font-medium text-gray-500 hidden lg:table-cell">Last Used</th>
                        <th class="px-6 py-3 font-medium text-gray-500">Status</th>
                        <th class="px-6 py-3 font-medium text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($apiKeys as $apiKey)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-key text-gray-400"></i>
                                    <span class="font-medium text-gray-900">{{ $apiKey->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell" x-data="{ visible: false }">
                                <div class="flex items-center gap-2">
                                    <code class="text-xs font-mono text-gray-500" x-text="visible ? '{{ substr($apiKey->key, 0, 8) }}...{{ substr($apiKey->key, -4) }}' : 'sk_••••••••••••••••'"></code>
                                    <button @click="visible = !visible" class="text-gray-400 hover:text-gray-600">
                                        <i class="fas" :class="visible ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell">
                                <div class="flex gap-1 flex-wrap">
                                    @foreach($apiKey->permissions ?? [] as $perm)
                                        <span class="px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700">{{ $perm }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-500 text-xs hidden lg:table-cell">
                                {{ $apiKey->last_used_at ? $apiKey->last_used_at->diffForHumans() : 'Never' }}
                            </td>
                            <td class="px-6 py-4">
                                @if($apiKey->status === 'active')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Active</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Revoked</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1">
                                    @if($apiKey->status === 'active')
                                        <button wire:click="revokeKey({{ $apiKey->id }})" wire:confirm="Revoke this key?" class="p-1.5 text-gray-400 hover:text-yellow-600 rounded-lg hover:bg-yellow-50 transition" title="Revoke">
                                            <i class="fas fa-ban text-xs"></i>
                                        </button>
                                    @endif
                                    <button wire:click="deleteKey({{ $apiKey->id }})" wire:confirm="Permanently delete this key?" class="p-1.5 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition" title="Delete">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-key text-3xl text-gray-300 mb-3"></i>
                                <p>No API keys generated yet</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Generate Key Modal -->
    <div x-show="$wire.showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="$wire.showModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/50" @click="@this.set('showModal', false)"></div>
            <div x-show="$wire.showModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="relative w-full max-w-md bg-white rounded-xl shadow-xl p-6 text-left">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Generate API Key</h3>
                    <button @click="@this.set('showModal', false)" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
                </div>
                <form wire:submit="generateKey" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                        <input wire:model="formName" type="text" placeholder="e.g. Production API Key" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        @error('formName') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Permissions *</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach(['read', 'write', 'delete', 'admin'] as $perm)
                                <label class="inline-flex items-center gap-2 px-3 py-2 border rounded-lg cursor-pointer transition {{ in_array($perm, $formPermissions) ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:bg-gray-50' }}">
                                    <input wire:model="formPermissions" type="checkbox" value="{{ $perm }}" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm text-gray-700">{{ ucfirst($perm) }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('formPermissions') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="@this.set('showModal', false)" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">Cancel</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">Generate Key</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
