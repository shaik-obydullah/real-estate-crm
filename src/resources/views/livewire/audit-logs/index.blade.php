
<div class="space-y-6" x-data="{ expandedLog: null }">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Audit Logs</h1>
        <p class="text-sm text-gray-500">Track all system changes and activity</p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <input wire:model.live="search" type="text" placeholder="Search by entity, action, or IP..." class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <select wire:model.live="userFilter" class="px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                <option value="">All Users</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                @endforeach
            </select>
            <select wire:model.live="actionFilter" class="px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                <option value="">All Actions</option>
                @foreach($actions as $action)
                    <option value="{{ $action }}">{{ ucfirst($action) }}</option>
                @endforeach
            </select>
            <input wire:model.live="dateFrom" type="date" class="px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            <input wire:model.live="dateTo" type="date" class="px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="px-6 py-3 font-medium text-gray-500 w-8"></th>
                        <th class="px-6 py-3 font-medium text-gray-500">User</th>
                        <th class="px-6 py-3 font-medium text-gray-500 hidden md:table-cell">Action</th>
                        <th class="px-6 py-3 font-medium text-gray-500 hidden md:table-cell">Entity</th>
                        <th class="px-6 py-3 font-medium text-gray-500 hidden lg:table-cell">IP Address</th>
                        <th class="px-6 py-3 font-medium text-gray-500">Timestamp</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($logs as $log)
                        @php
                            $actionColors = [
                                'create' => 'bg-green-100 text-green-700',
                                'update' => 'bg-blue-100 text-blue-700',
                                'delete' => 'bg-red-100 text-red-700',
                                'login' => 'bg-purple-100 text-purple-700',
                                'logout' => 'bg-gray-100 text-gray-700',
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50 cursor-pointer" @click="expandedLog = expandedLog === {{ $log->id }} ? null : {{ $log->id }}">
                            <td class="px-6 py-4">
                                <i class="fas fa-chevron-right text-gray-400 text-xs transition-transform" :class="expandedLog === {{ $log->id }} ? 'rotate-90' : ''"></i>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                        <span class="text-blue-600 font-semibold text-xs">{{ strtoupper(substr($log->user->name ?? 'S', 0, 1)) }}</span>
                                    </div>
                                    <span class="text-gray-900 font-medium">{{ $log->user->name ?? 'System' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $actionColors[$log->action] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ ucfirst($log->action) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-500 hidden md:table-cell">
                                {{ class_basename($log->entity_type) }} #{{ $log->entity_id }}
                            </td>
                            <td class="px-6 py-4 text-gray-500 hidden lg:table-cell font-mono text-xs">{{ $log->ip_address ?? '-' }}</td>
                            <td class="px-6 py-4 text-gray-500 text-xs whitespace-nowrap">{{ $log->created_at->diffForHumans() }}</td>
                        </tr>
                        <!-- Expanded Diff -->
                        <tr x-show="expandedLog === {{ $log->id }}" x-cloak x-transition>
                            <td colspan="6" class="px-6 py-4 bg-gray-50">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @if($log->old_values && count($log->old_values) > 0)
                                        <div>
                                            <h4 class="text-xs font-semibold text-red-600 mb-2">Old Values</h4>
                                            <div class="bg-white rounded-lg border border-red-200 p-3">
                                                @foreach($log->old_values as $key => $val)
                                                    <div class="text-xs mb-1"><span class="font-medium text-gray-700">{{ $key }}:</span> <span class="text-red-600">{{ is_array($val) ? json_encode($val) : $val }}</span></div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    @if($log->new_values && count($log->new_values) > 0)
                                        <div>
                                            <h4 class="text-xs font-semibold text-green-600 mb-2">New Values</h4>
                                            <div class="bg-white rounded-lg border border-green-200 p-3">
                                                @foreach($log->new_values as $key => $val)
                                                    <div class="text-xs mb-1"><span class="font-medium text-gray-700">{{ $key }}:</span> <span class="text-green-600">{{ is_array($val) ? json_encode($val) : $val }}</span></div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    @if((!$log->old_values || count($log->old_values) == 0) && (!$log->new_values || count($log->new_values) == 0))
                                        <p class="text-xs text-gray-400">No change details available for this log entry.</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-history text-3xl text-gray-300 mb-3"></i>
                                <p>No audit logs found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $logs->links() }}
        </div>
    </div>
</div>
