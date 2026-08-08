<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\CalendarEvent;
use App\Models\ChatMessage;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\Followup;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Note;
use App\Models\Notification;
use App\Models\Opportunity;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\SalesOrder;
use App\Models\Tag;
use App\Models\Task;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BulkDemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        $this->command?->info('Seeding bulk demo data...');

        $this->addUsers($faker);
        $users = User::all();
        $salesUsers = $users->whereIn('role', ['manager', 'sales'])->values();

        $customers = $this->addCustomers($faker, $salesUsers);
        $contacts = $this->addContacts($faker, $customers);
        $tags = Tag::all();
        $products = $this->addProducts($faker);

        $leads = $this->addLeads($faker, $salesUsers, $tags);
        $opportunities = $this->addOpportunities($faker, $salesUsers, $contacts, $leads, $tags);

        $quotations = $this->addQuotations($faker, $customers, $opportunities, $products, $salesUsers);
        $salesOrders = $this->addSalesOrders($faker, $customers, $quotations, $products, $salesUsers);
        $invoices = $this->addInvoices($faker, $customers, $salesOrders, $products, $salesUsers);
        $this->addPayments($faker, $invoices, $salesUsers);

        $this->addTasks($faker, $salesUsers, $customers, $opportunities, $leads, $tags);
        $this->addActivities($faker, $salesUsers, $customers, $contacts, $opportunities, $leads);
        $this->addFollowups($faker, $salesUsers, $customers, $contacts, $opportunities, $leads);
        $this->addTickets($faker, $customers, $contacts, $users, $salesUsers);
        $this->addNotes($faker, $customers, $contacts, $opportunities, $leads, $users, $tags);
        $this->addNotifications($faker, $users);
        $this->addCalendarEvents($faker, $users, $customers, $contacts, $opportunities);
        $this->addChatMessages($faker, $users);

        $this->command?->info('Bulk demo data seeded.');
    }

    private function addUsers(\Faker\Generator $faker): void
    {
        foreach (range(1, 10) as $i) {
            User::firstOrCreate(
                ['email' => "user{$i}@crm.com"],
                [
                    'name' => $faker->name,
                    'password' => Hash::make('password'),
                    'phone' => $faker->phoneNumber,
                    'department' => $faker->randomElement(['Sales', 'Marketing', 'Support']),
                    'role' => $faker->randomElement(['sales', 'sales', 'sales', 'manager']),
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );
        }
        $this->command?->info('  users: ' . User::count());
    }

    private function addCustomers(\Faker\Generator $faker, $salesUsers)
    {
        $customers = collect();
        foreach (range(1, 100) as $i) {
            $type = $faker->randomElement(['individual', 'company']);
            $customers->push(Customer::create([
                'name' => $type === 'company' ? $faker->company : $faker->name,
                'type' => $type,
                'email' => $type === 'company' ? $faker->companyEmail : $faker->safeEmail,
                'phone' => $faker->phoneNumber,
                'industry' => $type === 'company' ? $faker->randomElement(['Technology', 'Healthcare', 'Finance', 'Retail', 'Manufacturing', 'Real Estate', 'Education', 'Logistics']) : null,
                'website' => $type === 'company' ? 'https://' . $faker->domainName : null,
                'address' => $faker->streetAddress,
                'city' => $faker->city,
                'state' => $faker->stateAbbr,
                'country' => 'USA',
                'postal_code' => $faker->postcode,
                'credit_limit' => $faker->randomElement([5000, 10000, 25000, 50000, 100000, 200000]),
                'status' => $faker->randomElement(['active', 'active', 'active', 'inactive', 'archived']),
                'notes' => $faker->boolean(25) ? $faker->sentence(8) : null,
                'account_manager_id' => $salesUsers->random()->id,
            ]));
        }
        $this->command?->info('  customers: ' . $customers->count());

        return $customers;
    }

    private function addContacts(\Faker\Generator $faker, $customers)
    {
        $contacts = collect();
        foreach ($customers as $customer) {
            foreach (range(1, $faker->numberBetween(1, 3)) as $i) {
                $contacts->push(Contact::create([
                    'customer_id' => $customer->id,
                    'first_name' => $faker->firstName,
                    'last_name' => $faker->lastName,
                    'email' => $faker->unique()->safeEmail,
                    'phone' => $faker->phoneNumber,
                    'whatsapp' => $faker->boolean(30) ? $faker->phoneNumber : null,
                    'position' => $customer->type === 'company' ? $faker->randomElement(['CEO', 'CTO', 'CFO', 'Director', 'Manager', 'Procurement Lead']) : null,
                    'department' => $faker->boolean(40) ? $faker->randomElement(['Executive', 'IT', 'Finance', 'Operations', 'Sales']) : null,
                    'birthday' => $faker->boolean(30) ? $faker->date() : null,
                    'is_primary' => $i === 1,
                ]));
            }
        }
        $this->command?->info('  contacts: ' . $contacts->count());

        return $contacts;
    }

    private function addProducts(\Faker\Generator $faker)
    {
        $products = collect();
        $categories = ['Technology', 'Services', 'Hardware', 'Consulting', 'Infrastructure'];
        $names = [
            'Standard License', 'Premium License', 'Implementation Services', 'Consulting Package',
            'Server Hardware', 'Network Equipment', 'Data Backup Plan', 'Security Package',
            'Analytics Module', 'Automation Suite', 'Integration Service', 'Managed Services',
            'Workstation Bundle', 'Firewall Appliance', 'Phone System', 'Custom Development',
            'Quarterly Review', 'Cloud Storage Tier', 'Email Hosting', 'Dedicated Server',
        ];
        foreach (range(1, 30) as $i) {
            $products->push(Product::create([
                'name' => $names[$i % count($names)] . ' ' . $faker->randomElement(['', 'Pro', 'Plus', 'Elite', 'Standard', 'Basic']),
                'sku' => 'SKU-' . str_pad((string) $faker->unique()->numberBetween(100, 9999), 4, '0', STR_PAD_LEFT),
                'description' => $faker->sentence(10),
                'price' => $faker->numberBetween(500, 40000),
                'cost' => $faker->numberBetween(200, 20000),
                'category' => $faker->randomElement($categories),
                'stock' => $faker->numberBetween(10, 999),
                'status' => $faker->randomElement(['active', 'active', 'active', 'inactive']),
            ]));
        }
        $this->command?->info('  products: ' . $products->count());

        return $products;
    }

    private function addLeads(\Faker\Generator $faker, $salesUsers, $tags)
    {
        $leads = collect();
        foreach (range(1, 120) as $i) {
            $lead = Lead::create([
                'title' => $faker->randomElement([
                    'New Software Purchase', 'Cloud Migration', 'Security Assessment',
                    'Website Redesign', 'Mobile App Development', 'ERP Implementation',
                    'Data Analytics Project', 'Infrastructure Upgrade', 'Managed IT Services',
                    'Training Program', 'API Integration', 'Consulting Engagement',
                ]) . ' ' . $faker->numberBetween(1, 99),
                'company_name' => $faker->company,
                'contact_name' => $faker->name,
                'contact_email' => $faker->safeEmail,
                'contact_phone' => $faker->phoneNumber,
                'source' => $faker->randomElement(['website', 'referral', 'social_media', 'email_campaign', 'cold_call', 'partner', 'event', 'other']),
                'status' => $faker->randomElement(['new', 'new', 'contacted', 'qualified', 'proposal_sent', 'negotiation', 'won', 'lost']),
                'priority' => $faker->randomElement(['high', 'medium', 'low']),
                'value' => $faker->randomFloat(2, 5000, 200000),
                'expected_closing_date' => $faker->dateTimeBetween('-30 days', '+90 days')->format('Y-m-d'),
                'assigned_to' => $salesUsers->random()->id,
                'notes' => $faker->boolean(30) ? $faker->sentence(10) : null,
            ]);
            $lead->tags()->attach($tags->random($faker->numberBetween(1, 2))->pluck('id'));
            $leads->push($lead);
        }
        $this->command?->info('  leads: ' . $leads->count());

        return $leads;
    }

    private function addOpportunities(\Faker\Generator $faker, $salesUsers, $contacts, $leads, $tags)
    {
        $opportunities = collect();
        foreach (range(1, 120) as $i) {
            $stage = $faker->randomElement(['new', 'qualified', 'meeting', 'proposal', 'negotiation', 'won', 'lost']);
            $probabilityMap = [
                'new' => 10, 'qualified' => 25, 'meeting' => 40,
                'proposal' => 60, 'negotiation' => 75, 'won' => 100, 'lost' => 0,
            ];
            $opportunity = Opportunity::create([
                'name' => $faker->randomElement([
                    'Cloud Migration', 'Security Audit', 'ERP System', 'Mobile App',
                    'API Integration', 'Data Analytics', 'Consulting Retainer', 'Training Program',
                    'Hardware Upgrade', 'IT Support', 'Digital Transformation', 'Process Automation',
                ]),
                'company_name' => $faker->company,
                'contact_id' => $contacts->random()->id,
                'lead_id' => $leads->random()->id,
                'value' => $faker->randomFloat(2, 10000, 300000),
                'stage' => $stage,
                'probability' => $probabilityMap[$stage],
                'expected_closing_date' => $faker->dateTimeBetween('-20 days', '+90 days')->format('Y-m-d'),
                'assigned_to' => $salesUsers->random()->id,
                'notes' => $faker->boolean(25) ? $faker->sentence(10) : null,
            ]);
            $opportunity->tags()->attach($tags->random($faker->numberBetween(1, 2))->pluck('id'));
            $opportunities->push($opportunity);
        }
        $this->command?->info('  opportunities: ' . $opportunities->count());

        return $opportunities;
    }

    private function addQuotations(\Faker\Generator $faker, $customers, $opportunities, $products, $salesUsers)
    {
        $quotations = collect();
        foreach (range(1, 80) as $i) {
            $items = $products->random($faker->numberBetween(1, 4));
            $subtotal = 0;
            $lineItems = [];
            foreach ($items as $product) {
                $qty = $faker->numberBetween(1, 100);
                $lineTotal = round($product->price * $qty, 2);
                $subtotal += $lineTotal;
                $lineItems[] = [
                    'product_id' => $product->id,
                    'description' => $product->name,
                    'quantity' => $qty,
                    'unit_price' => $product->price,
                    'tax_rate' => 10,
                    'discount' => 0,
                    'total' => $lineTotal,
                ];
            }
            $taxRate = 10;
            $taxAmount = round($subtotal * $taxRate / 100, 2);
            $discount = $faker->boolean(20) ? $faker->numberBetween(100, 1000) : 0;
            $total = round($subtotal + $taxAmount - $discount, 2);

            $quotation = Quotation::create([
                'quote_number' => 'QUO-' . str_pad((string) $faker->unique()->numberBetween(1000, 9999), 4, '0', STR_PAD_LEFT),
                'customer_id' => $customers->random()->id,
                'opportunity_id' => $opportunities->random()->id,
                'subtotal' => $subtotal,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'discount' => $discount,
                'total' => $total,
                'status' => $faker->randomElement(['draft', 'sent', 'sent', 'accepted', 'rejected', 'expired']),
                'valid_until' => $faker->dateTimeBetween('+1 week', '+3 months')->format('Y-m-d'),
                'payment_terms' => $faker->randomElement(['Net 30 days', 'Net 45 days', 'Net 60 days', 'Due on receipt']),
                'notes' => $faker->boolean(25) ? $faker->sentence(8) : null,
                'created_by' => $salesUsers->random()->id,
            ]);
            foreach ($lineItems as $item) {
                $quotation->items()->create($item);
            }
            $quotations->push($quotation);
        }
        $this->command?->info('  quotations: ' . $quotations->count());

        return $quotations;
    }

    private function addSalesOrders(\Faker\Generator $faker, $customers, $quotations, $products, $salesUsers)
    {
        $salesOrders = collect();
        foreach (range(1, 60) as $i) {
            $items = $products->random($faker->numberBetween(1, 3));
            $subtotal = 0;
            $lineItems = [];
            foreach ($items as $product) {
                $qty = $faker->numberBetween(1, 50);
                $lineTotal = round($product->price * $qty, 2);
                $subtotal += $lineTotal;
                $lineItems[] = [
                    'product_id' => $product->id,
                    'description' => $product->name,
                    'quantity' => $qty,
                    'unit_price' => $product->price,
                    'total' => $lineTotal,
                ];
            }
            $taxAmount = round($subtotal * 10 / 100, 2);
            $discount = $faker->boolean(15) ? $faker->numberBetween(100, 800) : 0;
            $total = round($subtotal + $taxAmount - $discount, 2);

            $order = SalesOrder::create([
                'order_number' => 'SO-' . str_pad((string) $faker->unique()->numberBetween(1000, 9999), 4, '0', STR_PAD_LEFT),
                'customer_id' => $customers->random()->id,
                'quotation_id' => $faker->boolean(40) ? $quotations->random()->id : null,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount' => $discount,
                'total' => $total,
                'status' => $faker->randomElement(['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled']),
                'delivery_date' => $faker->dateTimeBetween('+1 week', '+2 months')->format('Y-m-d'),
                'shipping_address' => $faker->streetAddress . ', ' . $faker->city,
                'notes' => $faker->boolean(20) ? $faker->sentence(8) : null,
                'created_by' => $salesUsers->random()->id,
            ]);
            foreach ($lineItems as $item) {
                $order->items()->create($item);
            }
            $salesOrders->push($order);
        }
        $this->command?->info('  sales_orders: ' . $salesOrders->count());

        return $salesOrders;
    }

    private function addInvoices(\Faker\Generator $faker, $customers, $salesOrders, $products, $salesUsers)
    {
        $invoices = collect();
        foreach (range(1, 80) as $i) {
            $items = $products->random($faker->numberBetween(1, 3));
            $subtotal = 0;
            $lineItems = [];
            foreach ($items as $product) {
                $qty = $faker->numberBetween(1, 40);
                $lineTotal = round($product->price * $qty, 2);
                $subtotal += $lineTotal;
                $lineItems[] = [
                    'product_id' => $product->id,
                    'description' => $product->name,
                    'quantity' => $qty,
                    'unit_price' => $product->price,
                    'tax_rate' => 10,
                    'total' => $lineTotal,
                ];
            }
            $taxAmount = round($subtotal * 10 / 100, 2);
            $total = round($subtotal + $taxAmount, 2);
            $status = $faker->randomElement(['draft', 'sent', 'paid', 'paid', 'partial', 'overdue', 'cancelled']);
            $paidAmount = match ($status) {
                'paid' => $total,
                'partial' => round($total * $faker->numberBetween(30, 80) / 100, 2),
                default => 0,
            };

            $invoice = Invoice::create([
                'invoice_number' => 'INV-' . str_pad((string) $faker->unique()->numberBetween(1000, 9999), 4, '0', STR_PAD_LEFT),
                'customer_id' => $customers->random()->id,
                'sales_order_id' => $faker->boolean(40) ? $salesOrders->random()->id : null,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount' => 0,
                'total' => $total,
                'paid_amount' => $paidAmount,
                'status' => $status,
                'due_date' => $faker->dateTimeBetween('-30 days', '+60 days')->format('Y-m-d'),
                'paid_date' => in_array($status, ['paid', 'partial']) ? $faker->dateTimeBetween('-40 days', '-1 day')->format('Y-m-d') : null,
                'notes' => $faker->boolean(20) ? $faker->sentence(8) : null,
                'created_by' => $salesUsers->random()->id,
            ]);
            foreach ($lineItems as $item) {
                $invoice->items()->create($item);
            }
            $invoices->push($invoice);
        }
        $this->command?->info('  invoices: ' . $invoices->count());

        return $invoices;
    }

    private function addPayments(\Faker\Generator $faker, $invoices, $salesUsers)
    {
        $count = 0;
        foreach ($invoices->whereIn('status', ['paid', 'partial']) as $invoice) {
            Payment::create([
                'payment_number' => 'PAY-' . str_pad((string) $faker->unique()->numberBetween(1000, 9999), 4, '0', STR_PAD_LEFT),
                'invoice_id' => $invoice->id,
                'customer_id' => $invoice->customer_id,
                'amount' => $invoice->paid_amount,
                'method' => $faker->randomElement(['cash', 'bank_transfer', 'credit_card', 'check', 'other']),
                'reference_number' => $faker->boolean(50) ? $faker->bothify('REF-####-####') : null,
                'payment_date' => $invoice->paid_date ?? now(),
                'notes' => $faker->boolean(15) ? $faker->sentence(6) : null,
                'status' => $faker->randomElement(['completed', 'completed', 'completed', 'pending', 'failed']),
                'created_by' => $salesUsers->random()->id,
            ]);
            $count++;
        }
        $this->command?->info('  payments: ' . $count);
    }

    private function addTasks(\Faker\Generator $faker, $salesUsers, $customers, $opportunities, $leads, $tags)
    {
        $count = 0;
        foreach (range(1, 120) as $i) {
            $task = Task::create([
                'title' => $faker->randomElement([
                    'Call client for update', 'Send proposal', 'Schedule demo',
                    'Review contract', 'Prepare quote', 'Follow up on invoice',
                    'Update pipeline notes', 'Research prospect', 'Confirm meeting',
                    'Send onboarding docs', 'Renew subscription', 'Resolve billing issue',
                ]),
                'description' => $faker->sentence(10),
                'priority' => $faker->randomElement(['high', 'medium', 'low']),
                'status' => $faker->randomElement(['pending', 'pending', 'in_progress', 'completed', 'cancelled']),
                'due_date' => $faker->dateTimeBetween('-10 days', '+30 days')->format('Y-m-d'),
                'due_time' => $faker->boolean(60) ? $faker->time('H:i:s') : null,
                'assigned_to' => $salesUsers->random()->id,
                'related_customer_id' => $faker->boolean(70) ? $customers->random()->id : null,
                'related_opportunity_id' => $faker->boolean(50) ? $opportunities->random()->id : null,
                'related_lead_id' => $faker->boolean(40) ? $leads->random()->id : null,
            ]);
            $task->tags()->attach($tags->random($faker->numberBetween(1, 2))->pluck('id'));
            $count++;
        }
        $this->command?->info('  tasks: ' . $count);
    }

    private function addActivities(\Faker\Generator $faker, $salesUsers, $customers, $contacts, $opportunities, $leads)
    {
        $count = 0;
        foreach (range(1, 120) as $i) {
            Activity::create([
                'type' => $faker->randomElement(['call', 'email', 'meeting', 'note', 'task', 'other']),
                'title' => $faker->randomElement([
                    'Discovery call', 'Follow-up email', 'Product demo', 'Kickoff meeting',
                    'Negotiation call', 'Status update', 'Client visit', 'Review session',
                ]),
                'description' => $faker->sentence(8),
                'date' => $faker->dateTimeBetween('-30 days', '+14 days')->format('Y-m-d'),
                'time' => $faker->time('H:i'),
                'duration' => $faker->randomElement([15, 30, 30, 45, 60, 60, 90]),
                'outcome' => $faker->boolean(40) ? $faker->randomElement(['Positive', 'Negative', 'Follow-up needed', 'Completed']) : null,
                'contact_id' => $faker->boolean(50) ? $contacts->random()->id : null,
                'customer_id' => $faker->boolean(60) ? $customers->random()->id : null,
                'opportunity_id' => $faker->boolean(40) ? $opportunities->random()->id : null,
                'lead_id' => $faker->boolean(30) ? $leads->random()->id : null,
                'assigned_to' => $salesUsers->random()->id,
                'created_by' => $salesUsers->random()->id,
            ]);
            $count++;
        }
        $this->command?->info('  activities: ' . $count);
    }

    private function addFollowups(\Faker\Generator $faker, $salesUsers, $customers, $contacts, $opportunities, $leads)
    {
        $count = 0;
        foreach (range(1, 80) as $i) {
            Followup::create([
                'type' => $faker->randomElement(['call', 'meeting', 'email', 'other']),
                'title' => $faker->randomElement([
                    'Follow up on quote', 'Check-in call', 'Schedule next meeting',
                    'Send additional info', 'Negotiation follow-up', 'Renewal discussion',
                ]),
                'description' => $faker->sentence(8),
                'due_date' => $faker->dateTimeBetween('today', '+30 days')->format('Y-m-d'),
                'due_time' => $faker->time('H:i'),
                'priority' => $faker->randomElement(['high', 'medium', 'low']),
                'status' => $faker->randomElement(['pending', 'pending', 'completed', 'cancelled', 'overdue']),
                'contact_id' => $faker->boolean(50) ? $contacts->random()->id : null,
                'customer_id' => $faker->boolean(60) ? $customers->random()->id : null,
                'opportunity_id' => $faker->boolean(40) ? $opportunities->random()->id : null,
                'lead_id' => $faker->boolean(30) ? $leads->random()->id : null,
                'assigned_to' => $salesUsers->random()->id,
                'reminder_at' => $faker->boolean(50) ? $faker->dateTimeBetween('today', '+1 day') : null,
            ]);
            $count++;
        }
        $this->command?->info('  followups: ' . $count);
    }

    private function addTickets(\Faker\Generator $faker, $customers, $contacts, $users, $salesUsers)
    {
        $count = 0;
        foreach (range(1, 60) as $i) {
            Ticket::create([
                'ticket_number' => 'TKT-' . str_pad((string) $faker->unique()->numberBetween(1000, 9999), 4, '0', STR_PAD_LEFT),
                'subject' => $faker->randomElement([
                    'Cannot access account', 'Billing discrepancy', 'Feature request',
                    'Login issue', 'Data export request', 'Performance problem',
                    'Invoice not received', 'Configuration help', 'Bug report', 'Upgrade inquiry',
                ]),
                'description' => $faker->paragraph(2),
                'priority' => $faker->randomElement(['urgent', 'high', 'medium', 'low']),
                'status' => $faker->randomElement(['open', 'in_progress', 'waiting', 'resolved', 'closed']),
                'customer_id' => $faker->boolean(70) ? $customers->random()->id : null,
                'contact_id' => $faker->boolean(50) ? $contacts->random()->id : null,
                'assigned_to' => $faker->boolean(80) ? $salesUsers->random()->id : null,
                'created_by' => $users->random()->id,
            ]);
            $count++;
        }
        $this->command?->info('  tickets: ' . $count);
    }

    private function addNotes(\Faker\Generator $faker, $customers, $contacts, $opportunities, $leads, $users, $tags)
    {
        $count = 0;
        foreach (range(1, 100) as $i) {
            $note = Note::create([
                'title' => $faker->sentence(3),
                'content' => $faker->paragraph(3),
                'customer_id' => $faker->boolean(50) ? $customers->random()->id : null,
                'contact_id' => $faker->boolean(40) ? $contacts->random()->id : null,
                'lead_id' => $faker->boolean(30) ? $leads->random()->id : null,
                'opportunity_id' => $faker->boolean(30) ? $opportunities->random()->id : null,
                'created_by' => $users->random()->id,
                'is_pinned' => $faker->boolean(10),
            ]);
            $note->tags()->attach($tags->random($faker->numberBetween(1, 2))->pluck('id'));
            $count++;
        }
        $this->command?->info('  notes: ' . $count);
    }

    private function addNotifications(\Faker\Generator $faker, $users)
    {
        $count = 0;
        foreach ($users as $user) {
            foreach (range(1, 8) as $i) {
                Notification::create([
                    'user_id' => $user->id,
                    'title' => $faker->randomElement(['New lead assigned', 'Task due soon', 'Quote accepted', 'Payment received', 'Ticket resolved', 'Meeting reminder']),
                    'message' => $faker->sentence(8),
                    'type' => $faker->randomElement(['info', 'success', 'warning', 'error']),
                    'read_at' => $faker->boolean(50) ? $faker->dateTimeBetween('-7 days', 'now') : null,
                    'data' => ['source' => 'system'],
                ]);
                $count++;
            }
        }
        $this->command?->info('  notifications: ' . $count);
    }

    private function addCalendarEvents(\Faker\Generator $faker, $users, $customers, $contacts, $opportunities)
    {
        $count = 0;
        foreach (range(1, 100) as $i) {
            $start = $faker->dateTimeBetween('-30 days', '+45 days');
            $end = (clone $start)->modify('+' . $faker->numberBetween(30, 180) . ' minutes');
            CalendarEvent::create([
                'title' => $faker->randomElement([
                    'Client meeting', 'Product demo', 'Negotiation call', 'Kickoff meeting',
                    'Review session', 'Site visit', 'Quarterly review', 'Onboarding call',
                ]),
                'description' => $faker->boolean(40) ? $faker->sentence(8) : null,
                'start_time' => $start,
                'end_time' => $end,
                'location' => $faker->boolean(40) ? $faker->randomElement(['Office', 'Client site', 'Zoom', 'Google Meet', 'Phone']) : null,
                'type' => $faker->randomElement(['meeting', 'call', 'task', 'other']),
                'user_id' => $users->random()->id,
                'contact_id' => $faker->boolean(40) ? $contacts->random()->id : null,
                'customer_id' => $faker->boolean(50) ? $customers->random()->id : null,
                'opportunity_id' => $faker->boolean(30) ? $opportunities->random()->id : null,
            ]);
            $count++;
        }
        $this->command?->info('  calendar_events: ' . $count);
    }

    private function addChatMessages(\Faker\Generator $faker, $users)
    {
        $count = 0;
        foreach (range(1, 200) as $i) {
            $sender = $users->random();
            $receiver = $users->where('id', '!=', $sender->id)->random();
            ChatMessage::create([
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'message' => $faker->sentence(12),
                'is_read' => $faker->boolean(70),
            ]);
            $count++;
        }
        $this->command?->info('  chat_messages: ' . $count);
    }
}
