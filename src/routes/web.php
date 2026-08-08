<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Auth\Login as AuthLogin;
use App\Http\Livewire\Auth\Logout as AuthLogout;
use App\Http\Livewire\Dashboard;
use App\Http\Livewire\Customers\Index as CustomersIndex;
use App\Http\Livewire\Customers\Create as CustomersCreate;
use App\Http\Livewire\Customers\Edit as CustomersEdit;
use App\Http\Livewire\Contacts\Index as ContactsIndex;
use App\Http\Livewire\Leads\Index as LeadsIndex;
use App\Http\Livewire\Leads\Create as LeadsCreate;
use App\Http\Livewire\Pipeline\Index as PipelineIndex;
use App\Http\Livewire\Opportunities\Index as OpportunitiesIndex;
use App\Http\Livewire\Tasks\Index as TasksIndex;
use App\Http\Livewire\Activities\Index as ActivitiesIndex;
use App\Http\Livewire\Followups\Index as FollowupsIndex;
use App\Http\Livewire\Calendar\Index as CalendarIndex;
use App\Http\Livewire\Quotations\Index as QuotationsIndex;
use App\Http\Livewire\SalesOrders\Index as SalesOrdersIndex;
use App\Http\Livewire\Invoices\Index as InvoicesIndex;
use App\Http\Livewire\Payments\Index as PaymentsIndex;
use App\Http\Livewire\Products\Index as ProductsIndex;
use App\Http\Livewire\Chat\Index as ChatIndex;
use App\Http\Livewire\Email\Index as EmailIndex;
use App\Http\Livewire\Notes\Index as NotesIndex;
use App\Http\Livewire\Files\Index as FilesIndex;
use App\Http\Livewire\Timeline\Index as TimelineIndex;
use App\Http\Livewire\Tickets\Index as TicketsIndex;
use App\Http\Livewire\Users\Index as UsersIndex;
use App\Http\Livewire\Tags\Index as TagsIndex;
use App\Http\Livewire\Reports\Index as ReportsIndex;
use App\Http\Livewire\Settings\Index as SettingsIndex;
use App\Http\Livewire\AuditLogs\Index as AuditLogsIndex;
use App\Http\Livewire\Api\Index as ApiIndex;
use App\Http\Livewire\Notifications\Index as NotificationsIndex;
use App\Http\Livewire\Roles\Index as RolesIndex;
use App\Http\Livewire\Activities\Create as ActivitiesCreate;
use App\Http\Livewire\Activities\Edit as ActivitiesEdit;
use App\Http\Livewire\Activities\Show as ActivitiesShow;
use App\Http\Livewire\Calendar\Create as CalendarCreate;
use App\Http\Livewire\Calendar\Edit as CalendarEdit;
use App\Http\Livewire\Calendar\Show as CalendarShow;
use App\Http\Livewire\Contacts\Create as ContactsCreate;
use App\Http\Livewire\Contacts\Edit as ContactsEdit;
use App\Http\Livewire\Followups\Create as FollowupsCreate;
use App\Http\Livewire\Followups\Edit as FollowupsEdit;
use App\Http\Livewire\Followups\Show as FollowupsShow;
use App\Http\Livewire\Invoices\Create as InvoicesCreate;
use App\Http\Livewire\Invoices\Edit as InvoicesEdit;
use App\Http\Livewire\Invoices\Show as InvoicesShow;
use App\Http\Livewire\Leads\Edit as LeadsEdit;
use App\Http\Livewire\Opportunities\Create as OpportunitiesCreate;
use App\Http\Livewire\Opportunities\Edit as OpportunitiesEdit;
use App\Http\Livewire\Payments\Create as PaymentsCreate;
use App\Http\Livewire\Payments\Edit as PaymentsEdit;
use App\Http\Livewire\Payments\Show as PaymentsShow;
use App\Http\Livewire\Products\Create as ProductsCreate;
use App\Http\Livewire\Products\Edit as ProductsEdit;
use App\Http\Livewire\Products\Show as ProductsShow;
use App\Http\Livewire\Quotations\Create as QuotationsCreate;
use App\Http\Livewire\Quotations\Edit as QuotationsEdit;
use App\Http\Livewire\Quotations\Show as QuotationsShow;
use App\Http\Livewire\SalesOrders\Create as SalesOrdersCreate;
use App\Http\Livewire\SalesOrders\Edit as SalesOrdersEdit;
use App\Http\Livewire\SalesOrders\Show as SalesOrdersShow;
use App\Http\Livewire\Tasks\Create as TasksCreate;
use App\Http\Livewire\Tasks\Edit as TasksEdit;
use App\Http\Livewire\Tasks\Show as TasksShow;

// Auth
Route::get('/login', AuthLogin::class)->middleware('guest')->name('login');
Route::get('/logout', AuthLogout::class)->middleware('auth')->name('logout');

// Roles & Permissions (admin)
Route::get('/roles', RolesIndex::class)->middleware(['auth', 'permission:roles.manage'])->name('roles.index');

Route::middleware('auth')->group(function () {
    Route::get('/', Dashboard::class)->middleware('permission:dashboard.view')->name('dashboard');
    Route::get('/dashboard', Dashboard::class)->middleware('permission:dashboard.view');

    // CRM Core
    Route::get('/customers', CustomersIndex::class)->middleware('permission:customers.view')->name('customers.index');
    Route::get('/customers/create', CustomersCreate::class)->middleware('permission:customers.create')->name('customers.create');
    Route::get('/customers/{customer}/edit', CustomersEdit::class)->middleware('permission:customers.edit')->name('customers.edit');
    Route::get('/contacts', ContactsIndex::class)->middleware('permission:contacts.view')->name('contacts.index');
    Route::get('/leads', LeadsIndex::class)->middleware('permission:leads.view')->name('leads.index');
    Route::get('/leads/create', LeadsCreate::class)->middleware('permission:leads.create')->name('leads.create');
    Route::get('/pipeline', PipelineIndex::class)->middleware('permission:pipeline.view')->name('pipeline.index');
    Route::get('/opportunities', OpportunitiesIndex::class)->middleware('permission:opportunities.view')->name('opportunities.index');

    // Activities
    Route::get('/tasks', TasksIndex::class)->middleware('permission:tasks.view')->name('tasks.index');
    Route::get('/activities', ActivitiesIndex::class)->middleware('permission:activities.view')->name('activities.index');
    Route::get('/followups', FollowupsIndex::class)->middleware('permission:followups.view')->name('followups.index');
    Route::get('/calendar', CalendarIndex::class)->middleware('permission:calendar.view')->name('calendar.index');

    // Sales
    Route::get('/quotations', QuotationsIndex::class)->middleware('permission:quotations.view')->name('quotations.index');
    Route::get('/sales-orders', SalesOrdersIndex::class)->middleware('permission:sales-orders.view')->name('sales-orders.index');
    Route::get('/invoices', InvoicesIndex::class)->middleware('permission:invoices.view')->name('invoices.index');
    Route::get('/payments', PaymentsIndex::class)->middleware('permission:payments.view')->name('payments.index');
    Route::get('/products', ProductsIndex::class)->middleware('permission:products.view')->name('products.index');

    // Communication
    Route::get('/chat', ChatIndex::class)->middleware('permission:chat.view')->name('chat.index');
    Route::get('/email', EmailIndex::class)->middleware('permission:email.view')->name('email.index');
    Route::get('/notes', NotesIndex::class)->middleware('permission:notes.view')->name('notes.index');
    Route::get('/files', FilesIndex::class)->middleware('permission:files.view')->name('files.index');
    Route::get('/timeline', TimelineIndex::class)->middleware('permission:timeline.view')->name('timeline.index');

    // Support
    Route::get('/tickets', TicketsIndex::class)->middleware('permission:tickets.view')->name('tickets.index');

    // Admin
    Route::get('/users', UsersIndex::class)->middleware('permission:users.view')->name('users.index');
    Route::get('/tags', TagsIndex::class)->middleware('permission:tags.manage')->name('tags.index');
    Route::get('/reports', ReportsIndex::class)->middleware('permission:reports.view')->name('reports.index');
    Route::get('/settings', SettingsIndex::class)->middleware('permission:settings.manage')->name('settings.index');
    Route::get('/audit-logs', AuditLogsIndex::class)->middleware('permission:audit-logs.view')->name('audit-logs.index');
    Route::get('/api', ApiIndex::class)->middleware('permission:api.manage')->name('api.index');
    Route::get('/notifications', NotificationsIndex::class)->middleware('permission:notifications.view')->name('notifications.index');

    // Activities CRUD
    Route::get('/activities/create', ActivitiesCreate::class)->middleware('permission:activities.create')->name('activities.create');
    Route::get('/activities/{activity}', ActivitiesShow::class)->middleware('permission:activities.view')->name('activities.show');
    Route::get('/activities/{activity}/edit', ActivitiesEdit::class)->middleware('permission:activities.edit')->name('activities.edit');

    // Calendar CRUD
    Route::get('/calendar/create', CalendarCreate::class)->middleware('permission:calendar.create')->name('calendar.create');
    Route::get('/calendar/{event}', CalendarShow::class)->middleware('permission:calendar.view')->name('calendar.show');
    Route::get('/calendar/{event}/edit', CalendarEdit::class)->middleware('permission:calendar.edit')->name('calendar.edit');

    // Contacts CRUD
    Route::get('/contacts/create', ContactsCreate::class)->middleware('permission:contacts.create')->name('contacts.create');
    Route::get('/contacts/{contact}/edit', ContactsEdit::class)->middleware('permission:contacts.edit')->name('contacts.edit');

    // Followups CRUD
    Route::get('/followups/create', FollowupsCreate::class)->middleware('permission:followups.create')->name('followups.create');
    Route::get('/followups/{followup}', FollowupsShow::class)->middleware('permission:followups.view')->name('followups.show');
    Route::get('/followups/{followup}/edit', FollowupsEdit::class)->middleware('permission:followups.edit')->name('followups.edit');

    // Invoices CRUD
    Route::get('/invoices/create', InvoicesCreate::class)->middleware('permission:invoices.create')->name('invoices.create');
    Route::get('/invoices/{invoice}', InvoicesShow::class)->middleware('permission:invoices.view')->name('invoices.show');
    Route::get('/invoices/{invoice}/edit', InvoicesEdit::class)->middleware('permission:invoices.edit')->name('invoices.edit');

    // Leads CRUD
    Route::get('/leads/{lead}/edit', LeadsEdit::class)->middleware('permission:leads.edit')->name('leads.edit');

    // Opportunities CRUD
    Route::get('/opportunities/create', OpportunitiesCreate::class)->middleware('permission:opportunities.create')->name('opportunities.create');
    Route::get('/opportunities/{opportunity}/edit', OpportunitiesEdit::class)->middleware('permission:opportunities.edit')->name('opportunities.edit');

    // Payments CRUD
    Route::get('/payments/create', PaymentsCreate::class)->middleware('permission:payments.create')->name('payments.create');
    Route::get('/payments/{payment}', PaymentsShow::class)->middleware('permission:payments.view')->name('payments.show');
    Route::get('/payments/{payment}/edit', PaymentsEdit::class)->middleware('permission:payments.edit')->name('payments.edit');

    // Products CRUD
    Route::get('/products/create', ProductsCreate::class)->middleware('permission:products.create')->name('products.create');
    Route::get('/products/{product}', ProductsShow::class)->middleware('permission:products.view')->name('products.show');
    Route::get('/products/{product}/edit', ProductsEdit::class)->middleware('permission:products.edit')->name('products.edit');

    // Quotations CRUD
    Route::get('/quotations/create', QuotationsCreate::class)->middleware('permission:quotations.create')->name('quotations.create');
    Route::get('/quotations/{quotation}', QuotationsShow::class)->middleware('permission:quotations.view')->name('quotations.show');
    Route::get('/quotations/{quotation}/edit', QuotationsEdit::class)->middleware('permission:quotations.edit')->name('quotations.edit');

    // Sales Orders CRUD
    Route::get('/sales-orders/create', SalesOrdersCreate::class)->middleware('permission:sales-orders.create')->name('sales-orders.create');
    Route::get('/sales-orders/{salesOrder}', SalesOrdersShow::class)->middleware('permission:sales-orders.view')->name('sales-orders.show');
    Route::get('/sales-orders/{salesOrder}/edit', SalesOrdersEdit::class)->middleware('permission:sales-orders.edit')->name('sales-orders.edit');

    // Tasks CRUD
    Route::get('/tasks/create', TasksCreate::class)->middleware('permission:tasks.create')->name('tasks.create');
    Route::get('/tasks/{task}', TasksShow::class)->middleware('permission:tasks.view')->name('tasks.show');
    Route::get('/tasks/{task}/edit', TasksEdit::class)->middleware('permission:tasks.edit')->name('tasks.edit');
});
