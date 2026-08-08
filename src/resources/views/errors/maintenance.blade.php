<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'CRM') }} — Maintenance</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
</head>
<body class="h-full font-sans antialiased">
    <div class="min-h-full flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md text-center">
            <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-wrench text-white text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">We'll be right back</h1>
            <p class="text-sm text-gray-500 mb-8">{{ $message }}</p>
            <div class="flex items-center justify-center gap-3 text-xs text-gray-400">
                <i class="fas fa-circle-notch fa-spin"></i> System under maintenance
            </div>
        </div>
    </div>
</body>
</html>
