<div class="space-y-6" x-data="{ activeTab: @entangle('activeTab') }">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Settings</h1>
        <p class="text-sm text-gray-500">Configure your application settings</p>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('info'))
        <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg text-sm">
            {{ session('info') }}
        </div>
    @endif

    <!-- Tabs -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="border-b border-gray-200">
            <nav class="flex overflow-x-auto -mb-px px-4">
                @php
                    $tabs = [
                        'general' => ['icon' => 'fa-cogs', 'label' => 'General'],
                        'company' => ['icon' => 'fa-building', 'label' => 'Company'],
                        'email' => ['icon' => 'fa-envelope', 'label' => 'Email'],
                        'localization' => ['icon' => 'fa-globe', 'label' => 'Localization'],
                        'theme' => ['icon' => 'fa-palette', 'label' => 'Theme'],
                        'import_export' => ['icon' => 'fa-exchange-alt', 'label' => 'Import/Export'],
                    ];
                @endphp
                @foreach($tabs as $key => $tab)
                    <button @click="activeTab = '{{ $key }}'" :class="activeTab === '{{ $key }}' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="flex items-center gap-2 px-4 py-3 text-sm font-medium border-b-2 whitespace-nowrap transition">
                        <i class="fas {{ $tab['icon'] }}"></i> {{ $tab['label'] }}
                    </button>
                @endforeach
            </nav>
        </div>

        <div class="p-6">
            <!-- General Tab -->
            <div x-show="activeTab === 'general'" x-cloak>
                <form wire:submit="saveGeneral" class="space-y-6">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-4">Application</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Application Name</label>
                                <input wire:model="companyName" type="text" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                                @error('companyName') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900">Maintenance Mode</h3>
                                <p class="text-xs text-gray-500">Temporarily disable the application for everyone except administrators and whitelisted IPs</p>
                            </div>
                            <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $maintenanceEnabled ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700' }}">
                                {{ $maintenanceEnabled ? 'Maintenance ON' : 'Operational' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg mb-4">
                            <div>
                                <div class="text-sm font-medium text-gray-900">Enable Maintenance Mode</div>
                                <div class="text-xs text-gray-500">Non-admin users will see a maintenance page</div>
                            </div>
                            <button type="button" wire:click="$toggle('maintenanceEnabled')" :class="$wire.maintenanceEnabled ? 'bg-yellow-500' : 'bg-gray-300'" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2">
                                <span :class="$wire.maintenanceEnabled ? 'translate-x-6' : 'translate-x-1'" class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"></span>
                            </button>
                        </div>

                        <div x-show="$wire.maintenanceEnabled" x-cloak class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Maintenance Message</label>
                                <textarea wire:model="maintenanceMessage" rows="2" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Allowed IP Addresses</label>
                                <input wire:model="maintenanceAllowedIps" type="text" placeholder="127.0.0.1, 203.0.113.10" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                                <p class="text-xs text-gray-500 mt-1">Comma-separated. Your current IP is <code class="text-gray-700">{{ $clientIp }}</code>. Administrators always have access.</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" wire:loading.attr="disabled" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition disabled:opacity-50">
                            <span wire:loading.remove wire:target="saveGeneral">Save General Settings</span>
                            <span wire:loading wire:target="saveGeneral">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Company Tab -->
            <div x-show="activeTab === 'company'" x-cloak>
                <form wire:submit="saveCompany" class="space-y-6">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-4">Company Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Company Name</label>
                                <input wire:model="companyName" type="text" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                                <input wire:model="companyWebsite" type="url" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                <input wire:model="companyPhone" type="text" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input wire:model="companyEmail" type="email" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                <input wire:model="companyAddress" type="text" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                                <input wire:model="companyCity" type="text" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">State</label>
                                <input wire:model="companyState" type="text" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                                <input wire:model="companyCountry" type="text" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                                <input type="file" accept="image/*" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-sm file:bg-blue-50 file:text-blue-600">
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" wire:loading.attr="disabled" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition disabled:opacity-50">
                            <span wire:loading.remove wire:target="saveCompany">Save Company</span>
                            <span wire:loading wire:target="saveCompany">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Email Tab -->
            <div x-show="activeTab === 'email'" x-cloak>
                <form wire:submit="saveEmail" class="space-y-6">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-4">SMTP Configuration</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Host</label>
                                <input wire:model="smtpHost" type="text" placeholder="smtp.gmail.com" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Port</label>
                                <input wire:model="smtpPort" type="number" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                                <input wire:model="smtpUsername" type="text" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                <input wire:model="smtpPassword" type="password" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Encryption</label>
                                <select wire:model="smtpEncryption" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                                    <option value="tls">TLS</option>
                                    <option value="ssl">SSL</option>
                                    <option value="">None</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-4">From Address</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">From Email</label>
                                <input wire:model="fromAddress" type="email" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">From Name</label>
                                <input wire:model="fromName" type="text" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">Save Email Settings</button>
                    </div>
                </form>
            </div>

            <!-- Localization Tab -->
            <div x-show="activeTab === 'localization'" x-cloak>
                <form wire:submit="saveLocalization" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Language</label>
                            <select wire:model="locale" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                                <option value="en">English</option>
                                <option value="es">Spanish</option>
                                <option value="fr">French</option>
                                <option value="de">German</option>
                                <option value="ar">Arabic</option>
                                <option value="zh">Chinese</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Timezone</label>
                            <select wire:model.live="timezone" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                                @foreach($timezoneGroups as $group => $timezones)
                                    <optgroup label="{{ $group }}">
                                        @foreach($timezones as $tz)
                                            <option value="{{ $tz }}">{{ $tz }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date Format</label>
                            <select wire:model.live="dateFormat" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                                <option value="Y-m-d">YYYY-MM-DD (2026-08-08)</option>
                                <option value="d/m/Y">DD/MM/YYYY (08/08/2026)</option>
                                <option value="m/d/Y">MM/DD/YYYY (08/08/2026)</option>
                                <option value="d-m-Y">DD-MM-YYYY (08-08-2026)</option>
                                <option value="d M Y">DD Mon YYYY (08 Aug 2026)</option>
                                <option value="M j, Y">Mon DD, YYYY (Aug 8, 2026)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Time Format</label>
                            <select wire:model.live="timeFormat" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                                <option value="g:i A">12-hour (4:05 PM)</option>
                                <option value="H:i">24-hour (16:05)</option>
                                <option value="g:i a">12-hour lowercase (4:05 pm)</option>
                            </select>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 flex items-center justify-between">
                        <div>
                            <div class="text-sm font-medium text-gray-900">Preview</div>
                            <div class="text-xs text-gray-500">How dates and times will appear</div>
                        </div>
                        <div class="text-sm font-mono text-blue-700">{{ $this->previewDateTime() }}</div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">Save Localization</button>
                    </div>
                </form>
            </div>

            <!-- Theme Tab -->
            <div x-show="activeTab === 'theme'" x-cloak>
                <form wire:submit="saveTheme" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Primary Color</label>
                        <div class="flex items-center gap-3">
                            <input wire:model="primaryColor" type="color" class="w-10 h-10 rounded-lg cursor-pointer border-0">
                            <input wire:model="primaryColor" type="text" class="w-32 px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-mono">
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <div class="text-sm font-medium text-gray-900">Dark Mode</div>
                            <div class="text-xs text-gray-500">Enable dark mode for the application</div>
                        </div>
                        <button type="button" wire:click="$toggle('darkMode')" :class="$wire.darkMode ? 'bg-blue-600' : 'bg-gray-300'" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <span :class="$wire.darkMode ? 'translate-x-6' : 'translate-x-1'" class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"></span>
                        </button>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">Save Theme</button>
                    </div>
                </form>
            </div>

            <!-- Import/Export Tab -->
            <div x-show="activeTab === 'import_export'" x-cloak>
                <div class="space-y-6">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-4">Import Data</h3>
                        <div class="bg-gray-50 rounded-lg p-6 border-2 border-dashed border-gray-200 text-center">
                            <i class="fas fa-cloud-upload-alt text-3xl text-gray-300 mb-3"></i>
                            <p class="text-sm text-gray-600 mb-2">Drop your CSV file here or click to upload</p>
                            <input type="file" accept=".csv" class="block mx-auto text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100">
                        </div>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-4">Export Data</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <button wire:click="exportData('customers')" class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition text-left">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center"><i class="fas fa-users text-blue-600"></i></div>
                                <div><div class="text-sm font-medium text-gray-900">Customers</div><div class="text-xs text-gray-500">Export customer data</div></div>
                            </button>
                            <button wire:click="exportData('leads')" class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition text-left">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center"><i class="fas fa-funnel-dollar text-green-600"></i></div>
                                <div><div class="text-sm font-medium text-gray-900">Leads</div><div class="text-xs text-gray-500">Export lead data</div></div>
                            </button>
                            <button wire:click="exportData('invoices')" class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition text-left">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center"><i class="fas fa-file-invoice text-purple-600"></i></div>
                                <div><div class="text-sm font-medium text-gray-900">Invoices</div><div class="text-xs text-gray-500">Export invoice data</div></div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
