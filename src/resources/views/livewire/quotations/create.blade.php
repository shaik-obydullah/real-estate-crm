

<div class="max-w-5xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('quotations.index') }}" wire:navigate class="text-gray-400 hover:text-gray-600 p-2 hover:bg-gray-100 rounded-lg transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">New Quotation</h1>
            <p class="text-sm text-gray-500">{{ $quote_number }} (auto-generated)</p>
        </div>
    </div>

    {{-- Form --}}
    <form wire:submit="save" class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 space-y-6">
            {{-- Customer --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fas fa-building text-blue-500"></i> Customer
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                        <label class="block text-sm font-medium text-gray-700 mb-1">Opportunity</label>
                        <select wire:model="opportunity_id" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white transition">
                            <option value="">None</option>
                            @foreach($this->opportunities as $opportunity)
                            <option value="{{ $opportunity->id }}">{{ $opportunity->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <hr class="border-gray-100">

            {{-- Line Items --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fas fa-cube text-blue-500"></i> Products / Services
                </h3>
                <div class="overflow-x-auto border border-gray-100 rounded-lg">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-left">
                            <tr class="text-gray-500 text-xs uppercase tracking-wider">
                                <th class="px-4 py-3 font-medium">Product</th>
                                <th class="px-4 py-3 font-medium">Description</th>
                                <th class="px-4 py-3 font-medium">Qty</th>
                                <th class="px-4 py-3 font-medium">Price</th>
                                <th class="px-4 py-3 font-medium">Tax %</th>
                                <th class="px-4 py-3 font-medium">Discount</th>
                                <th class="px-4 py-3 font-medium text-right">Total</th>
                                <th class="px-2 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($items as $index => $item)
                            <tr>
                                <td class="px-4 py-3">
                                    <select wire:change="onProductSelect({{ $index }}, $event.target.value)" class="w-40 px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white transition">
                                        <option value="">Select product...</option>
                                        @foreach($this->products as $product)
                                        <option value="{{ $product->id }}" @if((string) $item['product_id'] === (string) $product->id) selected @endif>{{ $product->name }} ({{ $product->sku }})</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-3">
                                    <input wire:model.live="items.{{ $index }}.description" type="text" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition @error('items.' . $index . '.description') border-red-500 @enderror" placeholder="Item description">
                                    @error('items.' . $index . '.description') <p class="text-xs text-red-500 mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                                </td>
                                <td class="px-4 py-3">
                                    <input wire:model.live="items.{{ $index }}.quantity" type="number" step="0.01" min="0.01" class="w-20 px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                                </td>
                                <td class="px-4 py-3">
                                    <input wire:model.live="items.{{ $index }}.unit_price" type="number" step="0.01" min="0" class="w-28 px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                                </td>
                                <td class="px-4 py-3">
                                    <input wire:model.live="items.{{ $index }}.tax_rate" type="number" step="0.01" min="0" max="100" class="w-20 px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                                </td>
                                <td class="px-4 py-3">
                                    <input wire:model.live="items.{{ $index }}.discount" type="number" step="0.01" min="0" class="w-24 px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                                </td>
                                <td class="px-4 py-3 text-right font-medium text-gray-900 whitespace-nowrap">
                                    ${{ number_format(($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0) + (($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0)) * (($item['tax_rate'] ?? 0) / 100) - ($item['discount'] ?? 0), 2) }}
                                </td>
                                <td class="px-2 py-3 text-right">
                                    @if(count($items) > 1)
                                    <button type="button" wire:click="removeItem({{ $index }})" class="p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition" title="Remove">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <button type="button" wire:click="addItem" class="mt-3 inline-flex items-center gap-2 text-sm font-medium text-blue-600 hover:text-blue-700 transition">
                    <i class="fas fa-plus"></i> Add Line Item
                </button>

                {{-- Totals --}}
                <div class="mt-6 max-w-xs ml-auto space-y-2 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Subtotal</span>
                        <span class="font-medium text-gray-900">${{ number_format($this->subtotal, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Tax</span>
                        <span class="font-medium text-gray-900">${{ number_format($this->taxAmount, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Discount</span>
                        <span class="font-medium text-red-500">-${{ number_format((float) $discount, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                        <span class="font-semibold text-gray-900">Total</span>
                        <span class="text-lg font-bold text-gray-900">${{ number_format($this->total, 2) }}</span>
                    </div>
                </div>
            </div>

            <hr class="border-gray-100">

            {{-- Details --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fas fa-cog text-blue-500"></i> Details
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                        <select wire:model="status" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white transition">
                            <option value="draft">Draft</option>
                            <option value="sent">Sent</option>
                            <option value="accepted">Accepted</option>
                            <option value="rejected">Rejected</option>
                            <option value="expired">Expired</option>
                        </select>
                        @error('status') <p class="text-xs text-red-500 mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Valid Until <span class="text-red-500">*</span></label>
                        <input wire:model="valid_until" type="date" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition @error('valid_until') border-red-500 @enderror">
                        @error('valid_until') <p class="text-xs text-red-500 mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Terms</label>
                        <select wire:model="payment_terms" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white transition">
                            <option value="">Select terms...</option>
                            <option value="Net 15">Net 15</option>
                            <option value="Net 30">Net 30</option>
                            <option value="Net 45">Net 45</option>
                            <option value="Due on Receipt">Due on Receipt</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tax Rate (%)</label>
                        <input wire:model="tax_rate" type="number" step="0.01" min="0" max="100" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                        @error('tax_rate') <p class="text-xs text-red-500 mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Discount</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                            <input wire:model="discount" type="number" step="0.01" min="0" class="w-full pl-8 pr-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition @error('discount') border-red-500 @enderror">
                        </div>
                        @error('discount') <p class="text-xs text-red-500 mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <hr class="border-gray-100">

            {{-- Notes --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fas fa-sticky-note text-blue-500"></i> Notes
                </h3>
                <textarea wire:model="notes" rows="3" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition resize-none" placeholder="Add any additional notes about this quotation..."></textarea>
                @error('notes') <p class="text-xs text-red-500 mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Actions --}}
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-3 rounded-b-xl">
            <a href="{{ route('quotations.index') }}" wire:navigate class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                <i class="fas fa-times mr-1"></i> Cancel
            </a>
            <button type="submit" wire:loading.attr="disabled" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50 transition shadow-sm">
                <span wire:loading.remove wire:target="save"><i class="fas fa-save mr-1"></i> Create Quotation</span>
                <span wire:loading wire:target="save"><i class="fas fa-spinner fa-spin mr-1"></i> Saving...</span>
            </button>
        </div>
    </form>
</div>
