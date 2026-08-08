const fs = require('fs');
const path = require('path');

const templatesDir = path.join(__dirname, '..', 'templates');

const pageMap = {
  'dashboard.html':     'Dashboard',
  'notifications.html': 'Notifications',
  'customers.html':     'Customers',
  'customer-form.html': 'Customers',
  'contacts.html':      'Contacts',
  'contact-form.html':  'Contacts',
  'leads.html':         'Leads',
  'lead-form.html':     'Leads',
  'pipeline.html':      'Pipeline',
  'opportunities.html': 'Opportunities',
  'opportunity-form.html':'Opportunities',
  'tasks.html':         'Tasks',
  'task-form.html':     'Tasks',
  'activities.html':    'Activities',
  'activity-form.html': 'Activities',
  'followups.html':     'Follow-ups',
  'followup-form.html': 'Follow-ups',
  'calendar.html':      'Calendar',
  'quotations.html':    'Quotations',
  'quotation-form.html':'Quotations',
  'sales-orders.html':  'Sales Orders',
  'invoices.html':      'Invoices',
  'payments.html':      'Payments',
  'products.html':      'Products',
  'chat.html':          'Chat',
  'email.html':         'Email',
  'notes.html':         'Notes',
  'files.html':         'Files',
  'timeline.html':      'Timeline',
  'tickets.html':       'Tickets',
  'users.html':         'Users',
  'tags.html':          'Tags',
  'reports.html':       'Reports',
  'settings.html':      'Settings',
  'audit-logs.html':    'Audit Logs',
  'api.html':           'API',
  'index.html':         'Dashboard',
};

function makeLink(label, href, active) {
  const cls = active
    ? 'text-white bg-blue-600'
    : 'text-gray-600 hover:bg-gray-100';
  const h = active ? '#' : href;
  return `<a href="${h}" class="flex items-center gap-3 px-3 py-2 text-sm rounded-lg ${cls}">`;
}

function canonicalSidebar(activePage) {
  const a = (label, href) => makeLink(label, href, label === activePage);

  return `<aside class="w-64 bg-white shadow-lg hidden md:block shrink-0">
  <div class="p-5 border-b">
    <h1 class="text-xl font-bold text-gray-800"><i class="fas fa-cubes mr-2"></i>CRM</h1>
  </div>
  <nav class="p-3 space-y-0.5 overflow-y-auto max-h-[calc(100vh-65px)]">
    ${a('Dashboard', 'dashboard.html')}<i class="fas fa-th-large w-4 text-center"></i> Dashboard</a>
    ${a('Notifications', 'notifications.html')}<i class="fas fa-bell w-4 text-center"></i> Notifications</a>
    <hr class="my-1.5 border-gray-100">
    ${a('Customers', 'customers.html')}<i class="fas fa-users w-4 text-center"></i> Customers</a>
    ${a('Contacts', 'contacts.html')}<i class="fas fa-address-book w-4 text-center"></i> Contacts</a>
    ${a('Leads', 'leads.html')}<i class="fas fa-filter w-4 text-center"></i> Leads</a>
    ${a('Pipeline', 'pipeline.html')}<i class="fas fa-columns w-4 text-center"></i> Pipeline</a>
    ${a('Opportunities', 'opportunities.html')}<i class="fas fa-handshake w-4 text-center"></i> Opportunities</a>
    <hr class="my-1.5 border-gray-100">
    ${a('Tasks', 'tasks.html')}<i class="fas fa-check-circle w-4 text-center"></i> Tasks</a>
    ${a('Activities', 'activities.html')}<i class="fas fa-running w-4 text-center"></i> Activities</a>
    ${a('Follow-ups', 'followups.html')}<i class="fas fa-redo w-4 text-center"></i> Follow-ups</a>
    ${a('Calendar', 'calendar.html')}<i class="fas fa-calendar w-4 text-center"></i> Calendar</a>
    <hr class="my-1.5 border-gray-100">
    ${a('Quotations', 'quotations.html')}<i class="fas fa-file-invoice w-4 text-center"></i> Quotations</a>
    ${a('Sales Orders', 'sales-orders.html')}<i class="fas fa-truck w-4 text-center"></i> Sales Orders</a>
    ${a('Invoices', 'invoices.html')}<i class="fas fa-receipt w-4 text-center"></i> Invoices</a>
    ${a('Payments', 'payments.html')}<i class="fas fa-credit-card w-4 text-center"></i> Payments</a>
    ${a('Products', 'products.html')}<i class="fas fa-box w-4 text-center"></i> Products</a>
    <hr class="my-1.5 border-gray-100">
    ${a('Chat', 'chat.html')}<i class="fas fa-comment-dots w-4 text-center"></i> Chat</a>
    ${a('Email', 'email.html')}<i class="fas fa-envelope w-4 text-center"></i> Email</a>
    ${a('Notes', 'notes.html')}<i class="fas fa-sticky-note w-4 text-center"></i> Notes</a>
    ${a('Files', 'files.html')}<i class="fas fa-folder w-4 text-center"></i> Files</a>
    ${a('Timeline', 'timeline.html')}<i class="fas fa-stream w-4 text-center"></i> Timeline</a>
    <hr class="my-1.5 border-gray-100">
    ${a('Tickets', 'tickets.html')}<i class="fas fa-headset w-4 text-center"></i> Tickets</a>
    <hr class="my-1.5 border-gray-100">
    ${a('Users', 'users.html')}<i class="fas fa-user-shield w-4 text-center"></i> Users</a>
    ${a('Tags', 'tags.html')}<i class="fas fa-tags w-4 text-center"></i> Tags</a>
    ${a('Reports', 'reports.html')}<i class="fas fa-chart-bar w-4 text-center"></i> Reports</a>
    ${a('Settings', 'settings.html')}<i class="fas fa-cog w-4 text-center"></i> Settings</a>
    ${a('Audit Logs', 'audit-logs.html')}<i class="fas fa-history w-4 text-center"></i> Audit Logs</a>
    ${a('API', 'api.html')}<i class="fas fa-code w-4 text-center"></i> API</a>
  </nav>
</aside>`;
}

const faLink = '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">';

const files = fs.readdirSync(templatesDir).filter(f => f.endsWith('.html'));

let modified = 0;

for (const file of files) {
  const filePath = path.join(templatesDir, file);
  let html = fs.readFileSync(filePath, 'utf-8');

  // 1. Add Font Awesome if missing
  if (!html.includes('font-awesome') && !html.includes('fontawesome')) {
    html = html.replace('</head>', `  ${faLink}\n</head>`);
  }

  // 2. Replace the sidebar
  const activePage = pageMap[file] || 'Dashboard';
  const newSidebar = canonicalSidebar(activePage);

  // Find the first <aside tag and its closing </aside>
  const asideStart = html.indexOf('<aside');
  if (asideStart === -1) {
    console.log(`  SKIP ${file}: no <aside> found`);
    continue;
  }
  const asideEnd = html.indexOf('</aside>', asideStart);
  if (asideEnd === -1) {
    console.log(`  SKIP ${file}: no </aside> found`);
    continue;
  }

  const before = html.slice(0, asideStart);
  const after = html.slice(asideEnd + 8); // 8 = length of '</aside>'
  html = before + newSidebar + after;

  fs.writeFileSync(filePath, html, 'utf-8');
  console.log(`  OK   ${file}  [active: ${activePage}]`);
  modified++;
}

console.log(`\nDone! ${modified} files updated.`);
