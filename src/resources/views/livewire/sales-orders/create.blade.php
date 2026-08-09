
<div class="w-full space-y-6">
    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('sales-orders.index') }}" wire:navigate class="text-gray-400 hover:text-gray-600 p-2 hover:bg-gray-100 rounded-lg transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">New Sales Order</h1>
            <p class="text-sm text-gray-500">Create a new sales order with line items</p>
        </div>
    </div>

    {{-- Form --}}
    <form wire:submit="save" class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 space-y-6">
            {{-- Order Information --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fas fa-file-invoice text-blue-500"></i> Order Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Order Number</label>
                        <input wire:model="order_number" type="text" readonly class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed outline-none">
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
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quotation</label>
                        <select wire:model="quotation_id" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white transition @error('quotation_id') border-red-500 @enderror">
                            <option value="">None</option>
                            @foreach($this->quotations as $quotation)
                            <option value="{{ $quotation->id }}">{{ $quotation->quote_number }}</option>
                            @endforeach
                        </select>
                        @error('quotation_id') <p class="text-xs text-red-500 mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <hr class="border-gray-100">

            {{-- Line Items --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fas fa-boxes text-blue-500"></i> Line Items
                </h3>
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-left">
                            <tr>
                                <th class="px-4 py-3 font-medium text-gray-500">Product</th>
                                <th class="px-4 py-3 font-medium text-gray-500">Description</th>
                                <th class="px-4 py-3 font-medium text-gray-500">Qty</th>
                                <th class="px-4 py-3 font-medium text-gray-500">Price</th>
                                <th class="px-4 py-3 font-medium text-gray-500">Total</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($items as $index => $item)
                            <tr>
                                <td class="px-4 py-3">
                                    <select wire:change="onProductSelect({{ $index }})" wire:model="items.{{ $index }}.product_id" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white transition">
                                        <option value="">Select product...</option>
                                        @foreach($this->products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-3">
                                    <input wire:model.live="items.{{ $index }}.description" type="text" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition @error('items.' . $index . '.description') border-red-500 @enderror" placeholder="Item description">
                                    @error('items.' . $index . '.description') <p class="text-xs text-red-500 mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                                </td>
                                <td class="px-4 py-3">
                                    <input wire:model.live="items.{{ $index }}.quantity" type="number" step="0.01" min="0.01" class="w-24 px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition @error('items.' . $index . '.quantity') border-red-500 @enderror">
                                    @error('items.' . $index . '.quantity') <p class="text-xs text-red-500 mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                                </td>
                                <td class="px-4 py-3">
                                    <input wire:model.live="items.{{ $index }}.unit_price" type="number" step="0.01" min="0" class="w-32 px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition @error('items.' . $index . '.unit_price') border-red-500 @enderror">
                                    @error('items.' . $index . '.unit_price') <p class="text-xs text-red-500 mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-900">${{ number_format((($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0)), 2) }}</td>
                                <td class="px-4 py-3 text-right">
                                    <button type="button" wire:click="removeItem({{ $index }})" class="p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition" title="Remove">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <button type="button" wire:click="addItem" class="mt-4 inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                    <i class="fas fa-plus"></i> Add Line Item
                </button>

                {{-- Summary --}}
                <div class="mt-6 flex justify-end">
                    <div class="w-full max-w-xs space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Subtotal</span>
                            <span class="font-medium text-gray-900">${{ number_format($this->subtotal, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Discount</span>
                            <input wire:model.live="discount" type="number" step="0.01" min="0" class="w-28 px-3 py-2 text-sm text-right border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition @error('discount') border-red-500 @enderror" placeholder="0.00">
                        </div>
                        @error('discount') <p class="text-xs text-red-500 text-right"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                        <div class="flex items-center justify-between text-sm border-t border-gray-100 pt-2">
                            <span class="font-medium text-gray-900">Total</span>
                            <span class="font-semibold text-gray-900">${{ number_format($this->total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="border-gray-100">

            {{-- Status & Delivery --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fas fa-truck text-blue-500"></i> Status & Delivery
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                        <select wire:model="status" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white transition @error('status') border-red-500 @enderror">
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        @error('status') <p class="text-xs text-red-500 mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Date</label>
                        <input wire:model="delivery_date" type="date" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition @error('delivery_date') border-red-500 @enderror">
                        @error('delivery_date') <p class="text-xs text-red-500 mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Shipping Address</label>
                        <textarea wire:model="shipping_address" rows="2" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition resize-none @error('shipping_address') border-red-500 @enderror" placeholder="Enter shipping address"></textarea>
                        @error('shipping_address') <p class="text-xs text-red-500 mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <hr class="border-gray-100">

            {{-- Notes --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fas fa-sticky-note text-blue-500"></i> Notes
                </h3>
                <textarea wire:model="notes" rows="3" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition resize-none @error('notes') border-red-500 @enderror" placeholder="Add any additional notes about this order..."></textarea>
                @error('notes') <p class="text-xs text-red-500 mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Actions --}}
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-3 rounded-b-xl">
            <a href="{{ route('sales-orders.index') }}" wire:navigate class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                <i class="fas fa-times mr-1"></i> Cancel
            </a>
            <button type="submit" wire:loading.attr="disabled" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50 transition shadow-sm">
                <span wire:loading.remove wire:target="save"><i class="fas fa-save mr-1"></i> Create Sales Order</span>
                <span wire:loading wire:target="save"><i class="fas fa-spinner fa-spin mr-1"></i> Saving...</span>
            </button>
        </div>
    </form>
</div>
