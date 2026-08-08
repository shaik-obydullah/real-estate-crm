
<div class="space-y-6">
    <div class="flex items-center justify-between"><div><h1 class="text-2xl font-bold text-gray-900">Tickets</h1><p class="text-sm text-gray-500">Support ticket management</p></div></div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-4 border-b border-gray-100 flex gap-3">
            <div class="flex-1"><input wire:model.live="search" type="text" placeholder="Search tickets..." class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"></div>
            <select wire:model.live="statusFilter" class="px-4 py-2.5 text-sm border border-gray-200 rounded-lg outline-none"><option value="">All Status</option><option value="open">Open</option><option value="in_progress">In Progress</option><option value="resolved">Resolved</option><option value="closed">Closed</option></select>
        </div>
        <div class="overflow-x-auto"><table class="w-full text-sm"><thead class="bg-gray-50 text-left"><tr>
            <th class="px-6 py-3 font-medium text-gray-500">Subject</th><th class="px-6 py-3 font-medium text-gray-500 hidden md:table-cell">Customer</th><th class="px-6 py-3 font-medium text-gray-500 hidden md:table-cell">Priority</th><th class="px-6 py-3 font-medium text-gray-500">Status</th>
        </tr></thead><tbody class="divide-y divide-gray-50">
            @forelse($tickets as $t)<tr class="hover:bg-gray-50">
                <td class="px-6 py-4 font-medium text-gray-900">{{ $t->subject }}</td>
                <td class="px-6 py-4 text-gray-500 hidden md:table-cell">{{ $t->customer->name ?? '-' }}</td>
                <td class="px-6 py-4 text-gray-500 hidden md:table-cell capitalize">{{ $t->priority }}</td>
                <td class="px-6 py-4"><span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">{{ ucfirst(str_replace('_', ' ', $t->status)) }}</span></td>
            </tr>@empty<tr><td colspan="4" class="px-6 py-12 text-center text-gray-500">No tickets found</td></tr>@endforelse
        </tbody></table></div>
        <div class="p-4 border-t border-gray-100">{{ $tickets->links() }}</div>
    </div>
</div>
