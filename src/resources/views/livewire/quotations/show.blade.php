

<div class="max-w-5xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('quotations.index') }}" wire:navigate class="text-gray-400 hover:text-gray-600 p-2 hover:bg-gray-100 rounded-lg transition">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Quotation {{ $quotation->quote_number }}</h1>
                <p class="text-sm text-gray-500 mt-1 flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getStatusColor($quotation->status) }}">{{ ucfirst($quotation->status) }}</span>
                    <span>Created by {{ $quotation->creator?->name ?? '—' }}</span>
                </p>
            </div>
        </div>
        <a href="{{ route('quotations.edit', $quotation) }}" wire:navigate class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition shadow-sm">
            <i class="fas fa-pen"></i> Edit
        </a>
    </div>

    @if (session('success'))
    <div class="p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700 flex items-center gap-2">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    {{-- Customer --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-building text-blue-500"></i> Customer
            </h3>
        </div>
        <div class="p-6">
            <p class="text-sm font-semibold text-gray-900">{{ $quotation->customer->name }}</p>
            <p class="text-sm text-gray-500 mt-1">
                {{ $quotation->customer->email ?: '-' }}@if($quotation->customer->phone) • {{ $quotation->customer->phone }}@endif
            </p>
            @if($quotation->opportunity)
            <p class="text-sm text-gray-500 mt-1">Opportunity: {{ $quotation->opportunity->name }}</p>
            @endif
        </div>
    </div>

    {{-- Line Items --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-cube text-blue-500"></i> Products / Services
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr class="text-gray-500 text-xs uppercase tracking-wider">
                        <th class="px-6 py-3 font-medium">Description</th>
                        <th class="px-6 py-3 font-medium text-right">Qty</th>
                        <th class="px-6 py-3 font-medium text-right">Price</th>
                        <th class="px-6 py-3 font-medium text-right">Tax %</th>
                        <th class="px-6 py-3 font-medium text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($quotation->items as $item)
                    <tr>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $item->description }}</td>
                        <td class="px-6 py-4 text-right text-gray-600">{{ $item->quantity }}</td>
                        <td class="px-6 py-4 text-right text-gray-600">${{ number_format($item->unit_price, 2) }}</td>
                        <td class="px-6 py-4 text-right text-gray-600">{{ $item->tax_rate }}%</td>
                        <td class="px-6 py-4 text-right font-medium text-gray-900">${{ number_format($item->total, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">No line items.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            <div class="max-w-xs ml-auto space-y-2 text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Subtotal</span>
                    <span class="font-medium text-gray-900">${{ number_format($quotation->subtotal, 2) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Tax</span>
                    <span class="font-medium text-gray-900">${{ number_format($quotation->tax_amount, 2) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Discount</span>
                    <span class="font-medium text-red-500">-${{ number_format($quotation->discount, 2) }}</span>
                </div>
                <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                    <span class="font-semibold text-gray-900">Total</span>
                    <span class="text-lg font-bold text-gray-900">${{ number_format($quotation->total, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Details --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-file-invoice text-blue-500"></i> Details
            </h3>
        </div>
        <div class="divide-y divide-gray-50">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                <div class="px-6 py-4 bg-gray-50/50 text-sm font-medium text-gray-500">Valid Until</div>
                <div class="px-6 py-4 text-sm text-gray-900 sm:col-span-2">{{ $quotation->valid_until?->format('M d, Y') ?? '-' }}</div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                <div class="px-6 py-4 bg-gray-50/50 text-sm font-medium text-gray-500">Payment Terms</div>
                <div class="px-6 py-4 text-sm text-gray-900 sm:col-span-2">{{ $quotation->payment_terms ?? '-' }}</div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                <div class="px-6 py-4 bg-gray-50/50 text-sm font-medium text-gray-500">Created By</div>
                <div class="px-6 py-4 text-sm text-gray-900 sm:col-span-2">{{ $quotation->creator?->name ?? '-' }}</div>
            </div>
        </div>
        @if($quotation->notes)
        <div class="px-6 py-5 border-t border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3 flex items-center gap-2">
                <i class="fas fa-sticky-note text-blue-500"></i> Notes
            </h3>
            <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $quotation->notes }}</p>
        </div>
        @endif
    </div>
</div>
