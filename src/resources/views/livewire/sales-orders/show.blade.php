
<div class="max-w-5xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('sales-orders.index') }}" wire:navigate class="text-gray-400 hover:text-gray-600 p-2 hover:bg-gray-100 rounded-lg transition">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-gray-900">Sales Order {{ $salesOrder->order_number }}</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getStatusColor($salesOrder->status) }}">{{ ucfirst($salesOrder->status) }}</span>
                </div>
                <p class="text-sm text-gray-500">Created {{ $salesOrder->created_at->format('M d, Y') }} by {{ $salesOrder->creator->name ?? '-' }}</p>
            </div>
        </div>
        <a href="{{ route('sales-orders.edit', $salesOrder) }}" wire:navigate class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition shadow-sm">
            <i class="fas fa-pen"></i> Edit
        </a>
    </div>

    @if (session('success'))
    <div class="p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700 flex items-center gap-2">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    {{-- Customer & Quotation --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                <i class="fas fa-building text-blue-500"></i> Customer
            </h3>
            <div class="space-y-2 text-sm">
                <p class="font-medium text-gray-900">{{ $salesOrder->customer->name ?? '-' }}</p>
                @if($salesOrder->customer?->email)
                <p class="text-gray-500"><i class="fas fa-envelope w-4 text-gray-400 mr-1"></i>{{ $salesOrder->customer->email }}</p>
                @endif
                @if($salesOrder->customer?->phone)
                <p class="text-gray-500"><i class="fas fa-phone w-4 text-gray-400 mr-1"></i>{{ $salesOrder->customer->phone }}</p>
                @endif
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                <i class="fas fa-file-invoice text-blue-500"></i> Quotation
            </h3>
            @if($salesOrder->quotation)
            <div class="space-y-2 text-sm">
                <p class="font-medium text-gray-900">{{ $salesOrder->quotation->quote_number }}</p>
                <p class="text-gray-500"><i class="fas fa-calendar w-4 text-gray-400 mr-1"></i>Valid until {{ $salesOrder->quotation->valid_until?->format('M d, Y') ?? '-' }}</p>
                <p class="text-gray-500"><i class="fas fa-tag w-4 text-gray-400 mr-1"></i>{{ ucfirst($salesOrder->quotation->status) }}</p>
            </div>
            @else
            <p class="text-sm text-gray-400">No quotation linked</p>
            @endif
        </div>
    </div>

    {{-- Line Items --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 pt-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                <i class="fas fa-boxes text-blue-500"></i> Line Items
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="px-6 py-3 font-medium text-gray-500">Product</th>
                        <th class="px-6 py-3 font-medium text-gray-500">Description</th>
                        <th class="px-6 py-3 font-medium text-gray-500 text-right">Qty</th>
                        <th class="px-6 py-3 font-medium text-gray-500 text-right">Price</th>
                        <th class="px-6 py-3 font-medium text-gray-500 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($salesOrder->items as $item)
                    <tr>
                        <td class="px-6 py-4 text-gray-700">{{ $item->product->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $item->description }}</td>
                        <td class="px-6 py-4 text-right text-gray-500">{{ number_format($item->quantity, 2) }}</td>
                        <td class="px-6 py-4 text-right text-gray-500">${{ number_format($item->unit_price, 2) }}</td>
                        <td class="px-6 py-4 text-right font-medium text-gray-900">${{ number_format($item->total, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-400">No line items on this order.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
            <div class="w-full max-w-xs space-y-2 text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Subtotal</span>
                    <span class="font-medium text-gray-900">${{ number_format($salesOrder->subtotal, 2) }}</span>
                </div>
                @if((float) $salesOrder->tax_amount > 0)
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Tax</span>
                    <span class="font-medium text-gray-900">${{ number_format($salesOrder->tax_amount, 2) }}</span>
                </div>
                @endif
                @if((float) $salesOrder->discount > 0)
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Discount</span>
                    <span class="font-medium text-gray-900">-${{ number_format($salesOrder->discount, 2) }}</span>
                </div>
                @endif
                <div class="flex items-center justify-between border-t border-gray-200 pt-2">
                    <span class="font-semibold text-gray-900">Total</span>
                    <span class="font-bold text-gray-900">${{ number_format($salesOrder->total, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Delivery & Notes --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                <i class="fas fa-truck text-blue-500"></i> Delivery
            </h3>
            <div class="space-y-4 text-sm">
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase mb-1">Delivery Date</p>
                    <p class="text-gray-700">{{ $salesOrder->delivery_date?->format('M d, Y') ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase mb-1">Shipping Address</p>
                    <p class="text-gray-700 whitespace-pre-line">{{ $salesOrder->shipping_address ?? '-' }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                <i class="fas fa-sticky-note text-blue-500"></i> Notes
            </h3>
            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $salesOrder->notes ?? '-' }}</p>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-xs font-medium text-gray-400 uppercase mb-1">Created By</p>
                <p class="text-sm text-gray-700">{{ $salesOrder->creator->name ?? '-' }}</p>
            </div>
        </div>
    </div>
</div>
