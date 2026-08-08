<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Roles &amp; Permissions</h1>
            <p class="text-sm text-gray-500">Grant or revoke access per role</p>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        @foreach($roles as $role)
            <div class="p-6 border-b border-gray-100 last:border-0 {{ $loop->last ? '' : '' }}">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                            <i class="fas fa-user-shield text-blue-600"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">{{ $roleLabels[$role] }}</h2>
                            <p class="text-xs text-gray-500">
                                Role: <code class="text-gray-700">{{ $role }}</code>
                                @if($role === 'admin')
                                    &mdash; administrators always have access to everything
                                @endif
                            </p>
                        </div>
                    </div>
                    <button wire:click="saveRole('{{ $role }}')" wire:loading.attr="disabled"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition disabled:opacity-50">
                        <i class="fas fa-save"></i> Save {{ ucfirst($role) }}
                    </button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($groups as $group => $perms)
                        <div class="border border-gray-100 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ $group }}</h3>
                                <div class="flex items-center gap-2">
                                    <button wire:click="toggleGroup('{{ $role }}', '{{ $group }}', true)"
                                            class="text-xs text-blue-600 hover:text-blue-700">All</button>
                                    <span class="text-gray-300">|</span>
                                    <button wire:click="toggleGroup('{{ $role }}', '{{ $group }}', false)"
                                            class="text-xs text-gray-400 hover:text-gray-600">None</button>
                                </div>
                            </div>
                            <div class="space-y-1.5">
                                @foreach($perms as $permission)
                                    <label class="flex items-center gap-2 cursor-pointer select-none">
                                        <input type="checkbox" wire:change="toggle('{{ $role }}', '{{ $permission }}')"
                                               {{ ! empty($matrix[$role][$permission]) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="text-sm text-gray-600">{{ Str::headline(str_replace('.', ' ', $permission)) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
