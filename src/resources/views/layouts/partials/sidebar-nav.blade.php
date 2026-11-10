@props(['mobile' => false])

@can('permission', 'dashboard.view')
<a href="{{ route('dashboard') }}" wire:navigate @if($mobile)@click="sidebarOpen = false"@endif class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('dashboard') ? 'text-white bg-blue-600' : 'text-gray-600 hover:bg-gray-100' }}">
    <i class="fas fa-th-large w-5 text-center"></i> Dashboard
</a>
@endcan

@can('permission', 'notifications.view')
<a href="{{ route('notifications.index') }}" wire:navigate @if($mobile)@click="sidebarOpen = false"@endif class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('notifications.*') ? 'text-white bg-blue-600' : 'text-gray-600 hover:bg-gray-100' }}">
    <i class="fas fa-bell w-5 text-center"></i> Notifications
</a>
@endcan

@if(auth()->user()->hasAnyPermission(['customers.view', 'contacts.view', 'leads.view', 'pipeline.view', 'opportunities.view']))
    <div class="border-t border-gray-200 my-3"></div>

    {{-- Section: CRM --}}
    <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">CRM</p>
@endif

@can('permission', 'customers.view')
<a href="{{ route('customers.index') }}" wire:navigate @if($mobile)@click="sidebarOpen = false"@endif class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('customers.*') ? 'text-white bg-blue-600' : 'text-gray-600 hover:bg-gray-100' }}">
    <i class="fas fa-users w-5 text-center"></i> Customers
</a>
@endcan
@can('permission', 'contacts.view')
<a href="{{ route('contacts.index') }}" wire:navigate @if($mobile)@click="sidebarOpen = false"@endif class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('contacts.*') ? 'text-white bg-blue-600' : 'text-gray-600 hover:bg-gray-100' }}">
    <i class="fas fa-address-book w-5 text-center"></i> Contacts
</a>
@endcan
@can('permission', 'leads.view')
<a href="{{ route('leads.index') }}" wire:navigate @if($mobile)@click="sidebarOpen = false"@endif class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('leads.*') ? 'text-white bg-blue-600' : 'text-gray-600 hover:bg-gray-100' }}">
    <i class="fas fa-funnel-dollar w-5 text-center"></i> Leads
</a>
@endcan
@can('permission', 'pipeline.view')
<a href="{{ route('pipeline.index') }}" wire:navigate @if($mobile)@click="sidebarOpen = false"@endif class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('pipeline.*') ? 'text-white bg-blue-600' : 'text-gray-600 hover:bg-gray-100' }}">
    <i class="fas fa-filter w-5 text-center"></i> Pipeline
</a>
@endcan
@can('permission', 'opportunities.view')
<a href="{{ route('opportunities.index') }}" wire:navigate @if($mobile)@click="sidebarOpen = false"@endif class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('opportunities.*') ? 'text-white bg-blue-600' : 'text-gray-600 hover:bg-gray-100' }}">
    <i class="fas fa-handshake w-5 text-center"></i> Opportunities
</a>
@endcan

@if(auth()->user()->hasAnyPermission(['tasks.view', 'activities.view', 'followups.view', 'calendar.view']))
    <div class="border-t border-gray-200 my-3"></div>

    {{-- Section: Activities --}}
    <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Activities</p>
@endif

@can('permission', 'tasks.view')
<a href="{{ route('tasks.index') }}" wire:navigate @if($mobile)@click="sidebarOpen = false"@endif class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('tasks.*') ? 'text-white bg-blue-600' : 'text-gray-600 hover:bg-gray-100' }}">
    <i class="fas fa-check-square w-5 text-center"></i> Tasks
</a>
@endcan
@can('permission', 'activities.view')
<a href="{{ route('activities.index') }}" wire:navigate @if($mobile)@click="sidebarOpen = false"@endif class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('activities.*') ? 'text-white bg-blue-600' : 'text-gray-600 hover:bg-gray-100' }}">
    <i class="fas fa-clipboard-list w-5 text-center"></i> Activities
</a>
@endcan
@can('permission', 'followups.view')
<a href="{{ route('followups.index') }}" wire:navigate @if($mobile)@click="sidebarOpen = false"@endif class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('followups.*') ? 'text-white bg-blue-600' : 'text-gray-600 hover:bg-gray-100' }}">
    <i class="fas fa-phone-volume w-5 text-center"></i> Follow-ups
</a>
@endcan
@can('permission', 'calendar.view')
<a href="{{ route('calendar.index') }}" wire:navigate @if($mobile)@click="sidebarOpen = false"@endif class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('calendar.*') ? 'text-white bg-blue-600' : 'text-gray-600 hover:bg-gray-100' }}">
    <i class="fas fa-calendar-alt w-5 text-center"></i> Calendar
</a>
@endcan

@if(auth()->user()->hasAnyPermission(['quotations.view', 'sales-orders.view', 'invoices.view', 'payments.view', 'products.view']))
    <div class="border-t border-gray-200 my-3"></div>

    {{-- Section: Sales --}}
    <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Sales</p>
@endif

@can('permission', 'quotations.view')
<a href="{{ route('quotations.index') }}" wire:navigate @if($mobile)@click="sidebarOpen = false"@endif class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('quotations.*') ? 'text-white bg-blue-600' : 'text-gray-600 hover:bg-gray-100' }}">
    <i class="fas fa-file-invoice-dollar w-5 text-center"></i> Quotations
</a>
@endcan
@can('permission', 'sales-orders.view')
<a href="{{ route('sales-orders.index') }}" wire:navigate @if($mobile)@click="sidebarOpen = false"@endif class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('sales-orders.*') ? 'text-white bg-blue-600' : 'text-gray-600 hover:bg-gray-100' }}">
    <i class="fas fa-shopping-cart w-5 text-center"></i> Sales Orders
</a>
@endcan
@can('permission', 'invoices.view')
<a href="{{ route('invoices.index') }}" wire:navigate @if($mobile)@click="sidebarOpen = false"@endif class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('invoices.*') ? 'text-white bg-blue-600' : 'text-gray-600 hover:bg-gray-100' }}">
    <i class="fas fa-file-invoice w-5 text-center"></i> Invoices
</a>
@endcan
@can('permission', 'payments.view')
<a href="{{ route('payments.index') }}" wire:navigate @if($mobile)@click="sidebarOpen = false"@endif class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('payments.*') ? 'text-white bg-blue-600' : 'text-gray-600 hover:bg-gray-100' }}">
    <i class="fas fa-credit-card w-5 text-center"></i> Payments
</a>
@endcan
@can('permission', 'products.view')
<a href="{{ route('products.index') }}" wire:navigate @if($mobile)@click="sidebarOpen = false"@endif class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('products.*') ? 'text-white bg-blue-600' : 'text-gray-600 hover:bg-gray-100' }}">
    <i class="fas fa-box w-5 text-center"></i> Products
</a>
@endcan

@if(auth()->user()->hasAnyPermission(['chat.view', 'email.view', 'notes.view', 'files.view', 'timeline.view']))
    <div class="border-t border-gray-200 my-3"></div>

    {{-- Section: Communication --}}
    <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Communication</p>
@endif

@can('permission', 'chat.view')
<a href="{{ route('chat.index') }}" wire:navigate @if($mobile)@click="sidebarOpen = false"@endif class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('chat.*') ? 'text-white bg-blue-600' : 'text-gray-600 hover:bg-gray-100' }}">
    <i class="fas fa-comments w-5 text-center"></i> Chat
</a>
@endcan
@can('permission', 'email.view')
<a href="{{ route('email.index') }}" wire:navigate @if($mobile)@click="sidebarOpen = false"@endif class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('email.*') ? 'text-white bg-blue-600' : 'text-gray-600 hover:bg-gray-100' }}">
    <i class="fas fa-envelope w-5 text-center"></i> Email
</a>
@endcan
@can('permission', 'notes.view')
<a href="{{ route('notes.index') }}" wire:navigate @if($mobile)@click="sidebarOpen = false"@endif class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('notes.*') ? 'text-white bg-blue-600' : 'text-gray-600 hover:bg-gray-100' }}">
    <i class="fas fa-sticky-note w-5 text-center"></i> Notes
</a>
@endcan
@can('permission', 'files.view')
<a href="{{ route('files.index') }}" wire:navigate @if($mobile)@click="sidebarOpen = false"@endif class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('files.*') ? 'text-white bg-blue-600' : 'text-gray-600 hover:bg-gray-100' }}">
    <i class="fas fa-folder-open w-5 text-center"></i> Files
</a>
@endcan
@can('permission', 'timeline.view')
<a href="{{ route('timeline.index') }}" wire:navigate @if($mobile)@click="sidebarOpen = false"@endif class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('timeline.*') ? 'text-white bg-blue-600' : 'text-gray-600 hover:bg-gray-100' }}">
    <i class="fas fa-stream w-5 text-center"></i> Timeline
</a>
@endcan

@can('permission', 'tickets.view')
<div class="border-t border-gray-200 my-3"></div>

{{-- Section: Support --}}
<p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Support</p>
<a href="{{ route('tickets.index') }}" wire:navigate @if($mobile)@click="sidebarOpen = false"@endif class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('tickets.*') ? 'text-white bg-blue-600' : 'text-gray-600 hover:bg-gray-100' }}">
    <i class="fas fa-ticket-alt w-5 text-center"></i> Tickets
</a>
@endcan

@if(auth()->user()->hasAnyPermission(['users.view', 'tags.manage', 'reports.view', 'settings.manage', 'audit-logs.view', 'api.manage', 'roles.manage']))
    <div class="border-t border-gray-200 my-3"></div>

    {{-- Section: Admin --}}
    <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Administration</p>
@endif

@can('permission', 'roles.manage')
<a href="{{ route('roles.index') }}" wire:navigate @if($mobile)@click="sidebarOpen = false"@endif class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('roles.*') ? 'text-white bg-blue-600' : 'text-gray-600 hover:bg-gray-100' }}">
    <i class="fas fa-user-shield w-5 text-center"></i> Roles &amp; Permissions
</a>
@endcan
@can('permission', 'users.view')
<a href="{{ route('users.index') }}" wire:navigate @if($mobile)@click="sidebarOpen = false"@endif class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('users.*') ? 'text-white bg-blue-600' : 'text-gray-600 hover:bg-gray-100' }}">
    <i class="fas fa-user-cog w-5 text-center"></i> Users
</a>
@endcan
@can('permission', 'tags.manage')
<a href="{{ route('tags.index') }}" wire:navigate @if($mobile)@click="sidebarOpen = false"@endif class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('tags.*') ? 'text-white bg-blue-600' : 'text-gray-600 hover:bg-gray-100' }}">
    <i class="fas fa-tags w-5 text-center"></i> Tags
</a>
@endcan
@can('permission', 'reports.view')
<a href="{{ route('reports.index') }}" wire:navigate @if($mobile)@click="sidebarOpen = false"@endif class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('reports.*') ? 'text-white bg-blue-600' : 'text-gray-600 hover:bg-gray-100' }}">
    <i class="fas fa-chart-bar w-5 text-center"></i> Reports
</a>
@endcan
@can('permission', 'settings.manage')
<a href="{{ route('settings.index') }}" wire:navigate @if($mobile)@click="sidebarOpen = false"@endif class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('settings.*') ? 'text-white bg-blue-600' : 'text-gray-600 hover:bg-gray-100' }}">
    <i class="fas fa-cog w-5 text-center"></i> Settings
</a>
@endcan
@can('permission', 'audit-logs.view')
<a href="{{ route('audit-logs.index') }}" wire:navigate @if($mobile)@click="sidebarOpen = false"@endif class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('audit-logs.*') ? 'text-white bg-blue-600' : 'text-gray-600 hover:bg-gray-100' }}">
    <i class="fas fa-history w-5 text-center"></i> Audit Logs
</a>
@endcan
@can('permission', 'api.manage')
<a href="{{ route('api.index') }}" wire:navigate @if($mobile)@click="sidebarOpen = false"@endif class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('api.*') ? 'text-white bg-blue-600' : 'text-gray-600 hover:bg-gray-100' }}">
    <i class="fas fa-plug w-5 text-center"></i> API
</a>
@endcan
