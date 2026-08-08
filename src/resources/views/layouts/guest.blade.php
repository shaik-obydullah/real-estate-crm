<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'CRM') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
</head>
<body class="h-full font-sans antialiased">
    <div class="min-h-full flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            <div class="flex items-center justify-center gap-3 mb-8">
                <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-building text-white"></i>
                </div>
                <span class="text-2xl font-bold text-gray-900">CRM</span>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                {{ $slot }}
            </div>
            <p class="mt-6 text-center text-xs text-gray-400">&copy; {{ date('Y') }} CRM. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
