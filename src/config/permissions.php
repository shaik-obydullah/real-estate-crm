<?php

return [

    /*
    |--------------------------------------------------------------------------
    | User Roles
    |--------------------------------------------------------------------------
    |
    | Roles are stored as a string on the `users.role` column. Permissions are
    | assigned to roles via the `role_permissions` pivot table.
    |
    */

    'roles' => ['admin', 'manager', 'sales', 'support'],

    'role_labels' => [
        'admin' => 'Administrator',
        'manager' => 'Manager',
        'sales' => 'Sales',
        'support' => 'Support',
    ],

    /*
    |--------------------------------------------------------------------------
    | Permission Catalog
    |--------------------------------------------------------------------------
    |
    | Every permission in the system, grouped by module for display in the
    | Roles & Permissions admin screen. Permission names follow the
    | `{module}.{action}` convention.
    |
    */

    'groups' => [
        'CRM' => [
            'customers.view', 'customers.create', 'customers.edit', 'customers.delete',
            'contacts.view', 'contacts.create', 'contacts.edit', 'contacts.delete',
            'leads.view', 'leads.create', 'leads.edit', 'leads.delete',
            'pipeline.view',
            'opportunities.view', 'opportunities.create', 'opportunities.edit', 'opportunities.delete',
        ],
        'Activities' => [
            'tasks.view', 'tasks.create', 'tasks.edit', 'tasks.delete',
            'activities.view', 'activities.create', 'activities.edit', 'activities.delete',
            'followups.view', 'followups.create', 'followups.edit', 'followups.delete',
            'calendar.view', 'calendar.create', 'calendar.edit', 'calendar.delete',
        ],
        'Sales' => [
            'quotations.view', 'quotations.create', 'quotations.edit', 'quotations.delete',
            'sales-orders.view', 'sales-orders.create', 'sales-orders.edit', 'sales-orders.delete',
            'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.delete',
            'payments.view', 'payments.create', 'payments.edit', 'payments.delete',
            'products.view', 'products.create', 'products.edit', 'products.delete',
        ],
        'Communication' => [
            'chat.view', 'email.view', 'notes.view', 'files.view', 'timeline.view',
        ],
        'Support' => [
            'tickets.view', 'tickets.create', 'tickets.edit', 'tickets.delete',
        ],
        'Administration' => [
            'dashboard.view',
            'notifications.view',
            'reports.view',
            'users.view', 'users.manage',
            'roles.manage',
            'tags.manage',
            'settings.manage',
            'audit-logs.view',
            'api.manage',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Role Permissions
    |--------------------------------------------------------------------------
    |
    | Seeded into `role_permissions` by RolePermissionSeeder. `*` grants the
    | role every permission (the admin role also bypasses all checks).
    |
    */

    'defaults' => [
        'admin' => ['*'],

        'manager' => [
            'customers.view', 'customers.create', 'customers.edit', 'customers.delete',
            'contacts.view', 'contacts.create', 'contacts.edit', 'contacts.delete',
            'leads.view', 'leads.create', 'leads.edit', 'leads.delete',
            'pipeline.view',
            'opportunities.view', 'opportunities.create', 'opportunities.edit', 'opportunities.delete',
            'tasks.view', 'tasks.create', 'tasks.edit', 'tasks.delete',
            'activities.view', 'activities.create', 'activities.edit', 'activities.delete',
            'followups.view', 'followups.create', 'followups.edit', 'followups.delete',
            'calendar.view', 'calendar.create', 'calendar.edit', 'calendar.delete',
            'quotations.view', 'quotations.create', 'quotations.edit', 'quotations.delete',
            'sales-orders.view', 'sales-orders.create', 'sales-orders.edit', 'sales-orders.delete',
            'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.delete',
            'payments.view', 'payments.create', 'payments.edit', 'payments.delete',
            'products.view', 'products.create', 'products.edit', 'products.delete',
            'chat.view', 'email.view', 'notes.view', 'files.view', 'timeline.view',
            'tickets.view', 'tickets.create', 'tickets.edit', 'tickets.delete',
            'dashboard.view', 'notifications.view', 'reports.view',
        ],

        'sales' => [
            'customers.view', 'customers.create', 'customers.edit',
            'contacts.view', 'contacts.create', 'contacts.edit',
            'leads.view', 'leads.create', 'leads.edit',
            'pipeline.view',
            'opportunities.view', 'opportunities.create', 'opportunities.edit',
            'tasks.view', 'tasks.create', 'tasks.edit',
            'activities.view', 'activities.create', 'activities.edit',
            'followups.view', 'followups.create', 'followups.edit',
            'calendar.view', 'calendar.create', 'calendar.edit',
            'quotations.view', 'quotations.create', 'quotations.edit',
            'sales-orders.view', 'sales-orders.create',
            'invoices.view', 'invoices.create', 'invoices.edit',
            'payments.view', 'payments.create', 'payments.edit',
            'products.view', 'products.create', 'products.edit',
            'chat.view', 'email.view', 'notes.view', 'files.view', 'timeline.view',
            'tickets.view', 'tickets.create', 'tickets.edit',
            'dashboard.view', 'notifications.view', 'reports.view',
        ],

        'support' => [
            'customers.view',
            'tasks.view', 'tasks.create', 'tasks.edit',
            'activities.view',
            'notes.view', 'chat.view',
            'tickets.view', 'tickets.create', 'tickets.edit', 'tickets.delete',
            'dashboard.view', 'notifications.view',
        ],
    ],
];
