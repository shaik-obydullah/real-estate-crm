<?php

use Illuminate\Support\Facades\Route;
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

Route::get('/', Dashboard::class)->name('dashboard');
Route::get('/dashboard', Dashboard::class);

// CRM Core
Route::get('/customers', CustomersIndex::class)->name('customers.index');
Route::get('/customers/create', CustomersCreate::class)->name('customers.create');
Route::get('/customers/{customer}/edit', CustomersEdit::class)->name('customers.edit');
Route::get('/contacts', ContactsIndex::class)->name('contacts.index');
Route::get('/leads', LeadsIndex::class)->name('leads.index');
Route::get('/leads/create', LeadsCreate::class)->name('leads.create');
Route::get('/pipeline', PipelineIndex::class)->name('pipeline.index');
Route::get('/opportunities', OpportunitiesIndex::class)->name('opportunities.index');

// Activities
Route::get('/tasks', TasksIndex::class)->name('tasks.index');
Route::get('/activities', ActivitiesIndex::class)->name('activities.index');
Route::get('/followups', FollowupsIndex::class)->name('followups.index');
Route::get('/calendar', CalendarIndex::class)->name('calendar.index');

// Sales
Route::get('/quotations', QuotationsIndex::class)->name('quotations.index');
Route::get('/sales-orders', SalesOrdersIndex::class)->name('sales-orders.index');
Route::get('/invoices', InvoicesIndex::class)->name('invoices.index');
Route::get('/payments', PaymentsIndex::class)->name('payments.index');
Route::get('/products', ProductsIndex::class)->name('products.index');

// Communication
Route::get('/chat', ChatIndex::class)->name('chat.index');
Route::get('/email', EmailIndex::class)->name('email.index');
Route::get('/notes', NotesIndex::class)->name('notes.index');
Route::get('/files', FilesIndex::class)->name('files.index');
Route::get('/timeline', TimelineIndex::class)->name('timeline.index');

// Support
Route::get('/tickets', TicketsIndex::class)->name('tickets.index');

// Admin
Route::get('/users', UsersIndex::class)->name('users.index');
Route::get('/tags', TagsIndex::class)->name('tags.index');
Route::get('/reports', ReportsIndex::class)->name('reports.index');
Route::get('/settings', SettingsIndex::class)->name('settings.index');
Route::get('/audit-logs', AuditLogsIndex::class)->name('audit-logs.index');
Route::get('/api', ApiIndex::class)->name('api.index');
Route::get('/notifications', NotificationsIndex::class)->name('notifications.index');

// Activities CRUD
Route::get('/activities/create', ActivitiesCreate::class)->name('activities.create');
Route::get('/activities/{activity}', ActivitiesShow::class)->name('activities.show');
Route::get('/activities/{activity}/edit', ActivitiesEdit::class)->name('activities.edit');

// Calendar CRUD
Route::get('/calendar/create', CalendarCreate::class)->name('calendar.create');
Route::get('/calendar/{event}', CalendarShow::class)->name('calendar.show');
Route::get('/calendar/{event}/edit', CalendarEdit::class)->name('calendar.edit');

// Contacts CRUD
Route::get('/contacts/create', ContactsCreate::class)->name('contacts.create');
Route::get('/contacts/{contact}/edit', ContactsEdit::class)->name('contacts.edit');

// Followups CRUD
Route::get('/followups/create', FollowupsCreate::class)->name('followups.create');
Route::get('/followups/{followup}', FollowupsShow::class)->name('followups.show');
Route::get('/followups/{followup}/edit', FollowupsEdit::class)->name('followups.edit');

// Invoices CRUD
Route::get('/invoices/create', InvoicesCreate::class)->name('invoices.create');
Route::get('/invoices/{invoice}', InvoicesShow::class)->name('invoices.show');
Route::get('/invoices/{invoice}/edit', InvoicesEdit::class)->name('invoices.edit');

// Leads CRUD
Route::get('/leads/{lead}/edit', LeadsEdit::class)->name('leads.edit');

// Opportunities CRUD
Route::get('/opportunities/create', OpportunitiesCreate::class)->name('opportunities.create');
Route::get('/opportunities/{opportunity}/edit', OpportunitiesEdit::class)->name('opportunities.edit');

// Payments CRUD
Route::get('/payments/create', PaymentsCreate::class)->name('payments.create');
Route::get('/payments/{payment}', PaymentsShow::class)->name('payments.show');
Route::get('/payments/{payment}/edit', PaymentsEdit::class)->name('payments.edit');

// Products CRUD
Route::get('/products/create', ProductsCreate::class)->name('products.create');
Route::get('/products/{product}', ProductsShow::class)->name('products.show');
Route::get('/products/{product}/edit', ProductsEdit::class)->name('products.edit');

// Quotations CRUD
Route::get('/quotations/create', QuotationsCreate::class)->name('quotations.create');
Route::get('/quotations/{quotation}', QuotationsShow::class)->name('quotations.show');
Route::get('/quotations/{quotation}/edit', QuotationsEdit::class)->name('quotations.edit');

// Sales Orders CRUD
Route::get('/sales-orders/create', SalesOrdersCreate::class)->name('sales-orders.create');
Route::get('/sales-orders/{salesOrder}', SalesOrdersShow::class)->name('sales-orders.show');
Route::get('/sales-orders/{salesOrder}/edit', SalesOrdersEdit::class)->name('sales-orders.edit');

// Tasks CRUD
Route::get('/tasks/create', TasksCreate::class)->name('tasks.create');
Route::get('/tasks/{task}', TasksShow::class)->name('tasks.show');
Route::get('/tasks/{task}/edit', TasksEdit::class)->name('tasks.edit');
