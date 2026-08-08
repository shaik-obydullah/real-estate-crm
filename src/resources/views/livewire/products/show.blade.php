

<div class="max-w-4xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('products.index') }}" wire:navigate class="text-gray-400 hover:text-gray-600 p-2 hover:bg-gray-100 rounded-lg transition">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h1>
                <p class="text-sm text-gray-500">Product details</p>
            </div>
        </div>
        <a href="{{ route('products.edit', $product) }}" wire:navigate class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition shadow-sm">
            <i class="fas fa-pen"></i> Edit Product
        </a>
    </div>

    @if (session('success'))
    <div class="p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700 flex items-center gap-2">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    {{-- Details --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-box text-blue-500"></i> Product Information
            </h3>
        </div>
        <div class="divide-y divide-gray-50">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                <div class="px-6 py-4 bg-gray-50/50 text-sm font-medium text-gray-500">Name</div>
                <div class="px-6 py-4 text-sm text-gray-900 sm:col-span-2">{{ $product->name }}</div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                <div class="px-6 py-4 bg-gray-50/50 text-sm font-medium text-gray-500">SKU</div>
                <div class="px-6 py-4 text-sm text-gray-900 sm:col-span-2 font-mono text-xs">{{ $product->sku }}</div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                <div class="px-6 py-4 bg-gray-50/50 text-sm font-medium text-gray-500">Category</div>
                <div class="px-6 py-4 text-sm text-gray-900 sm:col-span-2">{{ ucfirst($product->category ?? '-') }}</div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                <div class="px-6 py-4 bg-gray-50/50 text-sm font-medium text-gray-500">Status</div>
                <div class="px-6 py-4 sm:col-span-2">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">{{ ucfirst($product->status) }}</span>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                <div class="px-6 py-4 bg-gray-50/50 text-sm font-medium text-gray-500">Price</div>
                <div class="px-6 py-4 text-sm text-gray-900 sm:col-span-2 font-medium">${{ number_format($product->price, 2) }}</div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                <div class="px-6 py-4 bg-gray-50/50 text-sm font-medium text-gray-500">Cost</div>
                <div class="px-6 py-4 text-sm text-gray-900 sm:col-span-2">{{ $product->cost !== null ? '$' . number_format($product->cost, 2) : '-' }}</div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-1">
                <div class="px-6 py-4 bg-gray-50/50 text-sm font-medium text-gray-500">Stock</div>
                <div class="px-6 py-4 text-sm text-gray-900 sm:col-span-2">{{ $product->stock }}</div>
            </div>
        </div>
    </div>

    {{-- Description --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-align-left text-blue-500"></i> Description
            </h3>
        </div>
        <div class="px-6 py-5">
            <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $product->description ?: 'No description provided.' }}</p>
        </div>
    </div>
</div>
