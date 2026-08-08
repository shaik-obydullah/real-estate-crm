

<div class="max-w-5xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('invoices.index') }}" wire:navigate class="text-gray-400 hover:text-gray-600 p-2 hover:bg-gray-100 rounded-lg transition">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-gray-900">Invoice {{ $invoice->invoice_number }}</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getStatusColor($invoice->status) }}">{{ ucfirst($invoice->status) }}</span>
                </div>
                <p class="text-sm text-gray-500">{{ $invoice->customer->name ?? '-' }}</p>
            </div>
        </div>
        <a href="{{ route('invoices.edit', $invoice) }}" wire:navigate class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition shadow-sm">
            <i class="fas fa-pen"></i> Edit Invoice
        </a>
    </div>

    @if (session('success'))
    <div class="p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700 flex items-center gap-2">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    {{-- Customer & Sales Order --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-building text-blue-500"></i> Customer
                </h3>
            </div>
            <div class="divide-y divide-gray-50">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                    <div class="px-6 py-4 bg-gray-50/50 text-sm font-medium text-gray-500">Name</div>
                    <div class="px-6 py-4 text-sm text-gray-900 sm:col-span-2">{{ $invoice->customer->name ?? '-' }}</div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                    <div class="px-6 py-4 bg-gray-50/50 text-sm font-medium text-gray-500">Email</div>
                    <div class="px-6 py-4 text-sm text-gray-900 sm:col-span-2">{{ $invoice->customer->email ?? '-' }}</div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                    <div class="px-6 py-4 bg-gray-50/50 text-sm font-medium text-gray-500">Phone</div>
                    <div class="px-6 py-4 text-sm text-gray-900 sm:col-span-2">{{ $invoice->customer->phone ?? '-' }}</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-truck text-blue-500"></i> Sales Order
                </h3>
            </div>
            <div class="divide-y divide-gray-50">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                    <div class="px-6 py-4 bg-gray-50/50 text-sm font-medium text-gray-500">Order #</div>
                    <div class="px-6 py-4 text-sm text-gray-900 sm:col-span-2">{{ $invoice->salesOrder?->order_number ?? '-' }}</div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                    <div class="px-6 py-4 bg-gray-50/50 text-sm font-medium text-gray-500">Status</div>
                    <div class="px-6 py-4 text-sm text-gray-900 sm:col-span-2">{{ $invoice->salesOrder?->status ?? '-' }}</div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                    <div class="px-6 py-4 bg-gray-50/50 text-sm font-medium text-gray-500">Total</div>
                    <div class="px-6 py-4 text-sm text-gray-900 sm:col-span-2">{{ $invoice->salesOrder ? '$' . number_format($invoice->salesOrder->total, 2) : '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Line Items --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-list text-blue-500"></i> Line Items
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
                        <th class="px-6 py-3 font-medium text-gray-500 text-right">Tax %</th>
                        <th class="px-6 py-3 font-medium text-gray-500 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($invoice->items as $item)
                    <tr>
                        <td class="px-6 py-4 text-gray-500">{{ $item->product->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-900">{{ $item->description }}</td>
                        <td class="px-6 py-4 text-gray-500 text-right">{{ number_format($item->quantity, 2) }}</td>
                        <td class="px-6 py-4 text-gray-500 text-right">${{ number_format($item->unit_price, 2) }}</td>
                        <td class="px-6 py-4 text-gray-500 text-right">{{ number_format($item->tax_rate, 2) }}%</td>
                        <td class="px-6 py-4 font-medium text-gray-900 text-right">${{ number_format($item->total, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">No line items on this invoice.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-5 border-t border-gray-100">
            <div class="ml-auto w-full lg:w-80 space-y-3">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Subtotal</span>
                    <span class="font-medium text-gray-900">${{ number_format($invoice->subtotal, 2) }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Tax</span>
                    <span class="font-medium text-gray-900">${{ number_format($invoice->tax_amount, 2) }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Discount</span>
                    <span class="font-medium text-gray-900">${{ number_format($invoice->discount, 2) }}</span>
                </div>
                <div class="border-t border-gray-100 pt-3 flex items-center justify-between">
                    <span class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Total</span>
                    <span class="text-lg font-bold text-gray-900">${{ number_format($invoice->total, 2) }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Paid</span>
                    <span class="font-medium text-green-600">${{ number_format($invoice->paid_amount, 2) }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Balance Due</span>
                    <span class="font-medium {{ $invoice->total - $invoice->paid_amount > 0 ? 'text-red-600' : 'text-gray-900' }}">${{ number_format($invoice->total - $invoice->paid_amount, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Details --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-info-circle text-blue-500"></i> Details
            </h3>
        </div>
        <div class="divide-y divide-gray-50">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                <div class="px-6 py-4 bg-gray-50/50 text-sm font-medium text-gray-500">Due Date</div>
                <div class="px-6 py-4 text-sm text-gray-900 sm:col-span-2">{{ $invoice->due_date ? \App\Support\AppSettings::formatDate($invoice->due_date) : '-' }}</div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                <div class="px-6 py-4 bg-gray-50/50 text-sm font-medium text-gray-500">Paid Date</div>
                <div class="px-6 py-4 text-sm text-gray-900 sm:col-span-2">{{ $invoice->paid_date ? \App\Support\AppSettings::formatDate($invoice->paid_date) : '-' }}</div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                <div class="px-6 py-4 bg-gray-50/50 text-sm font-medium text-gray-500">Created By</div>
                <div class="px-6 py-4 text-sm text-gray-900 sm:col-span-2">{{ $invoice->creator->name ?? '-' }}</div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                <div class="px-6 py-4 bg-gray-50/50 text-sm font-medium text-gray-500">Created At</div>
                <div class="px-6 py-4 text-sm text-gray-900 sm:col-span-2">{{ $invoice->created_at ? \App\Support\AppSettings::formatDateTime($invoice->created_at) : '-' }}</div>
            </div>
        </div>
    </div>

    {{-- Payments --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-credit-card text-blue-500"></i> Payments
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="px-6 py-3 font-medium text-gray-500">Payment #</th>
                        <th class="px-6 py-3 font-medium text-gray-500">Date</th>
                        <th class="px-6 py-3 font-medium text-gray-500">Method</th>
                        <th class="px-6 py-3 font-medium text-gray-500">Status</th>
                        <th class="px-6 py-3 font-medium text-gray-500 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($invoice->payments as $payment)
                    <tr>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $payment->payment_number }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ $payment->payment_date ? \App\Support\AppSettings::formatDate($payment->payment_date) : '-' }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ ucfirst(str_replace('_', ' ', $payment->method)) }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $payment->status === 'completed' ? 'bg-green-100 text-green-700' : ($payment->status === 'failed' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700') }}">{{ ucfirst($payment->status) }}</span>
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900 text-right">${{ number_format($payment->amount, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">No payments recorded yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Notes --}}
    @if($invoice->notes)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-sticky-note text-blue-500"></i> Notes
            </h3>
        </div>
        <div class="px-6 py-5">
            <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $invoice->notes }}</p>
        </div>
    </div>
    @endif
</div>
