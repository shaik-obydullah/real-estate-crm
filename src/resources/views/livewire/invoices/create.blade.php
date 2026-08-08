

<div class="max-w-5xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('invoices.index') }}" wire:navigate class="text-gray-400 hover:text-gray-600 p-2 hover:bg-gray-100 rounded-lg transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">New Invoice</h1>
            <p class="text-sm text-gray-500">Create an invoice and add line items</p>
        </div>
    </div>

    {{-- Form --}}
    <form wire:submit="save" class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 space-y-6">
            {{-- Invoice Information --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fas fa-file-invoice-dollar text-blue-500"></i> Invoice Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Invoice Number</label>
                        <input wire:model="invoice_number" type="text" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition bg-gray-50 text-gray-500 cursor-not-allowed" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Customer <span class="text-red-500">*</span></label>
                        <select wire:model="customer_id" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white transition @error('customer_id') border-red-500 @enderror">
                            <option value="">Select customer...</option>
                            @foreach($this->customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                        @error('customer_id') <p class="text-xs text-red-500 mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sales Order</label>
                        <select wire:model="sales_order_id" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white transition">
                            <option value="">None</option>
                            @foreach($this->salesOrders as $salesOrder)
                            <option value="{{ $salesOrder->id }}">{{ $salesOrder->order_number }} - {{ $salesOrder->customer->name ?? '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                        <select wire:model="status" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white transition">
                            <option value="draft">Draft</option>
                            <option value="sent">Sent</option>
                            <option value="paid">Paid</option>
                            <option value="partial">Partial</option>
                            <option value="overdue">Overdue</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Due Date <span class="text-red-500">*</span></label>
                        <input wire:model="due_date" type="date" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition @error('due_date') border-red-500 @enderror">
                        @error('due_date') <p class="text-xs text-red-500 mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Paid Date</label>
                        <input wire:model="paid_date" type="date" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition @error('paid_date') border-red-500 @enderror">
                        @error('paid_date') <p class="text-xs text-red-500 mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <hr class="border-gray-100">

            {{-- Line Items --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fas fa-list text-blue-500"></i> Line Items
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-left">
                            <tr>
                                <th class="px-4 py-3 font-medium text-gray-500 w-48">Product</th>
                                <th class="px-4 py-3 font-medium text-gray-500">Description <span class="text-red-500">*</span></th>
                                <th class="px-4 py-3 font-medium text-gray-500 w-24">Qty</th>
                                <th class="px-4 py-3 font-medium text-gray-500 w-28">Price</th>
                                <th class="px-4 py-3 font-medium text-gray-500 w-24">Tax %</th>
                                <th class="px-4 py-3 font-medium text-gray-500 w-28 text-right">Total</th>
                                <th class="px-4 py-3 font-medium text-gray-500 w-12"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($items as $index => $item)
                            <tr>
                                <td class="px-4 py-3">
                                    <select wire:change="onProductSelect({{ $index }})" wire:model="items.{{ $index }}.product_id" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white transition">
                                        <option value="">Select...</option>
                                        @foreach($this->products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-3">
                                    <input wire:model.live="items.{{ $index }}.description" type="text" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition @error('items.{{ $index }}.description') border-red-500 @enderror" placeholder="Item description">
                                    @error('items.{{ $index }}.description') <p class="text-xs text-red-500 mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                                </td>
                                <td class="px-4 py-3">
                                    <input wire:model.live="items.{{ $index }}.quantity" type="number" step="0.01" min="0.01" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition @error('items.{{ $index }}.quantity') border-red-500 @enderror">
                                    @error('items.{{ $index }}.quantity') <p class="text-xs text-red-500 mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                                </td>
                                <td class="px-4 py-3">
                                    <input wire:model.live="items.{{ $index }}.unit_price" type="number" step="0.01" min="0" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition @error('items.{{ $index }}.unit_price') border-red-500 @enderror">
                                    @error('items.{{ $index }}.unit_price') <p class="text-xs text-red-500 mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                                </td>
                                <td class="px-4 py-3">
                                    <input wire:model.live="items.{{ $index }}.tax_rate" type="number" step="0.01" min="0" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition" placeholder="0">
                                </td>
                                <td class="px-4 py-3 text-right font-medium text-gray-900 whitespace-nowrap">
                                    ${{ number_format((($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0)) * (1 + (($item['tax_rate'] ?? 0) / 100)), 2) }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button" wire:click="removeItem({{ $index }})" class="p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition" title="Remove line item">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                    <div>
                        <button type="button" wire:click="addItem" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                            <i class="fas fa-plus"></i> Add Line Item
                        </button>
                    </div>

                    {{-- Summary --}}
                    <div class="w-full lg:w-80 space-y-3">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Subtotal</span>
                            <span class="font-medium text-gray-900">${{ number_format($this->subtotal, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Tax</span>
                            <span class="font-medium text-gray-900">${{ number_format($this->taxAmount, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Discount</span>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                                <input wire:model.live="discount" type="number" step="0.01" min="0" class="w-32 pl-8 pr-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition text-right @error('discount') border-red-500 @enderror" placeholder="0.00">
                            </div>
                        </div>
                        @error('discount') <p class="text-xs text-red-500 mt-1 text-right"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                        <div class="border-t border-gray-100 pt-3 flex items-center justify-between">
                            <span class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Total</span>
                            <span class="text-lg font-bold text-gray-900">${{ number_format($this->total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="border-gray-100">

            {{-- Notes --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fas fa-sticky-note text-blue-500"></i> Notes
                </h3>
                <textarea wire:model="notes" rows="3" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition resize-none" placeholder="Add any additional notes about this invoice..."></textarea>
            </div>
        </div>

        {{-- Actions --}}
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-3 rounded-b-xl">
            <a href="{{ route('invoices.index') }}" wire:navigate class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                <i class="fas fa-times mr-1"></i> Cancel
            </a>
            <button type="submit" wire:loading.attr="disabled" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50 transition shadow-sm">
                <span wire:loading.remove wire:target="save"><i class="fas fa-save mr-1"></i> Create Invoice</span>
                <span wire:loading wire:target="save"><i class="fas fa-spinner fa-spin mr-1"></i> Saving...</span>
            </button>
        </div>
    </form>
</div>
