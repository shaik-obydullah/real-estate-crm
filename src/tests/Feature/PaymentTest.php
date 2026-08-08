<?php

namespace Tests\Feature;

use App\Http\Livewire\Payments\Create as PaymentsCreate;
use App\Http\Livewire\Payments\Edit as PaymentsEdit;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function seedPermissions(): void
    {
        $this->seed(PermissionSeeder::class);
        $this->seed(RolePermissionSeeder::class);
    }

    protected function createInvoice(User $user, array $overrides = []): Invoice
    {
        $customer = Customer::create(['name' => 'Acme Corp', 'type' => 'company']);

        return Invoice::create(array_merge([
            'invoice_number' => 'INV-' . Str::upper(Str::random(6)),
            'customer_id' => $customer->id,
            'subtotal' => 1000.00,
            'tax_amount' => 0,
            'discount' => 0,
            'total' => 1000.00,
            'paid_amount' => 0,
            'status' => 'sent',
            'due_date' => now()->addDays(30)->toDateString(),
            'created_by' => $user->id,
        ], $overrides));
    }

    protected function createPayment(User $user, array $overrides = []): Payment
    {
        $invoice = $this->createInvoice($user);

        return Payment::create(array_merge([
            'payment_number' => 'PAY-' . Str::upper(Str::random(6)),
            'invoice_id' => $invoice->id,
            'customer_id' => $invoice->customer_id,
            'amount' => 500.00,
            'method' => 'bank_transfer',
            'reference_number' => 'REF-123',
            'payment_date' => now()->toDateString(),
            'status' => 'completed',
            'created_by' => $user->id,
        ], $overrides));
    }

    public function test_guest_is_redirected_from_payment_create(): void
    {
        $this->get('/payments/create')->assertRedirect('/login');
    }

    public function test_admin_can_view_payment_create_page(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/payments/create')->assertOk();
    }

    public function test_admin_can_view_payment_show_page(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        $payment = $this->createPayment($admin);

        $this->actingAs($admin)->get('/payments/' . $payment->id)->assertOk();
    }

    public function test_admin_can_view_payment_edit_page(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        $payment = $this->createPayment($admin);

        $this->actingAs($admin)->get('/payments/' . $payment->id . '/edit')->assertOk();
    }

    public function test_support_role_cannot_access_payment_create(): void
    {
        $this->seedPermissions();

        $support = User::factory()->create(['role' => 'support']);

        $this->actingAs($support)->get('/payments/create')->assertForbidden();
    }

    public function test_support_role_cannot_access_payment_show(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        $payment = $this->createPayment($admin);
        $support = User::factory()->create(['role' => 'support']);

        $this->actingAs($support)->get('/payments/' . $payment->id)->assertForbidden();
    }

    public function test_support_role_cannot_access_payment_edit(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        $payment = $this->createPayment($admin);
        $support = User::factory()->create(['role' => 'support']);

        $this->actingAs($support)->get('/payments/' . $payment->id . '/edit')->assertForbidden();
    }

    public function test_admin_can_create_payment(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        $invoice = $this->createInvoice($admin);
        $customer = $invoice->customer;

        Livewire::actingAs($admin)->test(PaymentsCreate::class)
            ->set('invoice_id', $invoice->id)
            ->set('customer_id', $customer->id)
            ->set('amount', 750.00)
            ->set('method', 'credit_card')
            ->set('reference_number', 'TXN-991')
            ->set('payment_date', '2026-08-08')
            ->set('status', 'completed')
            ->call('save')
            ->assertRedirect(route('payments.index'));

        $this->assertDatabaseHas('payments', [
            'payment_number' => 'PAY-0001',
            'invoice_id' => $invoice->id,
            'customer_id' => $customer->id,
            'amount' => 750.00,
            'method' => 'credit_card',
            'reference_number' => 'TXN-991',
            'status' => 'completed',
            'created_by' => $admin->id,
        ]);

        $this->assertSame('2026-08-08', Payment::first()->payment_date->format('Y-m-d'));
    }

    public function test_create_payment_requires_invoice_amount_method_and_date(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)->test(PaymentsCreate::class)
            ->set('invoice_id', null)
            ->set('amount', null)
            ->set('method', '')
            ->set('payment_date', null)
            ->call('save')
            ->assertHasErrors(['invoice_id', 'amount', 'method', 'payment_date']);
    }

    public function test_invoice_change_autofills_customer_and_amount(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        $invoice = $this->createInvoice($admin, ['paid_amount' => 250.00]);

        Livewire::actingAs($admin)->test(PaymentsCreate::class)
            ->set('invoice_id', $invoice->id)
            ->call('onInvoiceChange')
            ->assertSet('customer_id', $invoice->customer_id)
            ->assertSet('amount', 750.0);
    }

    public function test_admin_can_edit_payment(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['role' => 'admin']);
        $payment = $this->createPayment($admin);
        $invoice = $this->createInvoice($admin, ['paid_amount' => 300.00]);

        Livewire::actingAs($admin)->test(PaymentsEdit::class, ['payment' => $payment])
            ->set('invoice_id', $invoice->id)
            ->set('customer_id', $invoice->customer_id)
            ->set('amount', 700.00)
            ->set('status', 'refunded')
            ->call('save')
            ->assertRedirect(route('payments.index'));

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'invoice_id' => $invoice->id,
            'customer_id' => $invoice->customer_id,
            'amount' => 700.00,
            'status' => 'refunded',
        ]);
    }
}
