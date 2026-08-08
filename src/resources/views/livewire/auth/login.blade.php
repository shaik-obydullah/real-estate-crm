<div>
    @if (session()->has('status'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">{{ session('status') }}</div>
    @endif

    <h2 class="text-xl font-bold text-gray-900">Welcome back</h2>
    <p class="text-sm text-gray-500 mt-1 mb-6">Sign in to your account to continue</p>

    <form wire:submit="login" class="space-y-4">
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input wire:model="email" id="email" type="email" autocomplete="email" required
                   class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                   placeholder="you@example.com">
            @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input wire:model="password" id="password" type="password" autocomplete="current-password" required
                   class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                   placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;">
            @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>
        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-sm text-gray-600">
                <input wire:model="remember" type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                Remember me
            </label>
        </div>
        <button type="submit" wire:loading.attr="disabled"
                class="w-full px-4 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition disabled:opacity-50">
            <span wire:loading.remove wire:target="login">Sign in</span>
            <span wire:loading wire:target="login">Signing in...</span>
        </button>
    </form>

    <p class="mt-6 text-xs text-center text-gray-400">
        Demo users: admin@crm.com, jane@crm.com, john@crm.com, sarah@crm.com &mdash; password: password
    </p>
</div>
