<div class="max-w-4xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex items-start gap-3">
        <a href="{{ route('payments.index') }}" wire:navigate class="text-gray-400 hover:text-gray-600 p-2 hover:bg-gray-100 rounded-lg transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">Payment {{ $payment->payment_number }}</h1>
            <p class="text-sm text-gray-500 mt-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getStatusColor($payment->status) }}">
                    {{ ucfirst($payment->status) }}
                </span>
            </p>
        </div>
        <a href="{{ route('payments.edit', $payment) }}" wire:navigate class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition shadow-sm">
            <i class="fas fa-pen mr-1"></i> Edit
        </a>
    </div>

    {{-- Details --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 space-y-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                <i class="fas fa-credit-card text-blue-500"></i> Payment Details
            </h3>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Invoice</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if($payment->invoice)
                        <a href="{{ route('invoices.show', $payment->invoice) }}" wire:navigate class="text-blue-600 hover:text-blue-700">{{ $payment->invoice->invoice_number }}</a>
                        @else
                        —
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Customer</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $payment->customer?->name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Amount</dt>
                    <dd class="mt-1 text-sm text-gray-900">${{ number_format($payment->amount, 2) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Method</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $this->getMethodLabel($payment->method) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Reference Number</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $payment->reference_number ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Payment Date</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ \App\Support\AppSettings::formatDate($payment->payment_date) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($payment->status) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Created By</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $payment->creator?->name ?? '—' }}</dd>
                </div>
            </dl>

            @if($payment->notes)
            <hr class="border-gray-100">

            <div>
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fas fa-sticky-note text-blue-500"></i> Notes
                </h3>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $payment->notes }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
