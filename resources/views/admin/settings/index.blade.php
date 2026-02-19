@extends('adminlte::page')

@section('title', 'System Settings')

@section('content_header')
    <h1>System Settings</h1>
@stop

@section('content')
    @if(session('success'))
        <x-adminlte-alert theme="success" title="Success" dismissable>
            {{ session('success') }}
        </x-adminlte-alert>
    @endif

    @if($errors->any())
        <x-adminlte-alert theme="danger" title="Validation Error" dismissable>
            <ul class="mb-0 pl-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-adminlte-alert>
    @endif

    <x-adminlte-card title="Notification Settings" theme="primary" icon="fas fa-bell">
        @if(app()->environment('production'))
            <x-adminlte-alert theme="warning" title="Production Environment" dismissable>
                You are updating live system and environment configuration. Saving may immediately affect running services.
            </x-adminlte-alert>
        @endif

        <form id="system-settings-form" method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="active_tab" id="active_tab" value="pane-notifications">
            <input type="hidden" name="active_tab_label" id="active_tab_label" value="Notifications">

            @php
                $envSettingsWithDefaults = $envSettings;
                $filesystemKeys = [
                    'FILESYSTEM_DISK',
                    'DO_SPACES_KEY',
                    'DO_SPACES_SECRET',
                    'DO_SPACES_REGION',
                    'DO_SPACES_BUCKET',
                    'DO_SPACES_ENDPOINT',
                    'DO_SPACES_URL',
                    'DO_SPACES_ROOT',
                    'DO_SPACES_VISIBILITY',
                    'AWS_ACCESS_KEY_ID',
                    'AWS_SECRET_ACCESS_KEY',
                    'AWS_DEFAULT_REGION',
                    'AWS_BUCKET',
                    'AWS_ENDPOINT',
                    'AWS_URL',
                    'AWS_USE_PATH_STYLE_ENDPOINT',
                ];

                foreach ($filesystemKeys as $filesystemKey) {
                    if (!array_key_exists($filesystemKey, $envSettingsWithDefaults)) {
                        $envSettingsWithDefaults[$filesystemKey] = '';
                    }
                }

                $availabilityValue = function (string $key) use ($envSettingsWithDefaults) {
                    $oldValue = old('env.' . $key);
                    $currentValue = (string) ($envSettingsWithDefaults[$key] ?? '');

                    if ($oldValue === null) {
                        return $currentValue;
                    }

                    $incoming = (string) $oldValue;
                    $isSecret = preg_match('/(SECRET|PASSWORD|TOKEN|^APP_KEY$)/', $key) === 1;

                    if ($isSecret && trim($incoming) === '') {
                        return $currentValue;
                    }

                    return $incoming;
                };

                $doRequiredKeys = ['DO_SPACES_KEY', 'DO_SPACES_SECRET', 'DO_SPACES_REGION', 'DO_SPACES_BUCKET', 'DO_SPACES_ENDPOINT'];
                $awsRequiredKeys = ['AWS_ACCESS_KEY_ID', 'AWS_SECRET_ACCESS_KEY', 'AWS_DEFAULT_REGION', 'AWS_BUCKET'];

                $doProviderReady = collect($doRequiredKeys)->every(fn ($key) => trim((string) $availabilityValue($key)) !== '');
                $awsProviderReady = collect($awsRequiredKeys)->every(fn ($key) => trim((string) $availabilityValue($key)) !== '');
                $selectedFilesystemDisk = (string) old('env.FILESYSTEM_DISK', (string) ($envSettingsWithDefaults['FILESYSTEM_DISK'] ?? ''));

                $doOptionEnabled = $doProviderReady || $selectedFilesystemDisk === 'do_spaces';
                $awsOptionEnabled = $awsProviderReady || $selectedFilesystemDisk === 's3';

                $groupDefinitions = [
                    'Application' => ['APP_'],
                    'Logging' => ['LOG_'],
                    'Database' => ['DB_'],
                    'Session' => ['SESSION_'],
                    'Cache' => ['CACHE_', 'MEMCACHED_'],
                    'Redis' => ['REDIS_'],
                    'Queue & Broadcast' => ['QUEUE_', 'BROADCAST_'],
                    'Filesystem' => ['FILESYSTEM_', 'DO_SPACES_', 'AWS_'],
                    'Telescope' => ['TELESCOPE_'],
                    'Frontend / Vite' => ['VITE_'],
                ];

                $remainingEnv = collect($envSettingsWithDefaults)
                    ->reject(function ($value, $key) {
                        return str_starts_with((string) $key, 'MAIL_');
                    });
                $envGroups = [];

                foreach ($groupDefinitions as $groupName => $prefixes) {
                    $groupItems = $remainingEnv->filter(function ($value, $key) use ($prefixes) {
                        foreach ($prefixes as $prefix) {
                            if (str_starts_with($key, $prefix)) {
                                return true;
                            }
                        }

                        return false;
                    });

                    if ($groupItems->isNotEmpty()) {
                        $envGroups[$groupName] = $groupItems->all();
                        $remainingEnv = $remainingEnv->except(array_keys($groupItems->all()));
                    }
                }

                if ($remainingEnv->isNotEmpty()) {
                    $envGroups['Other'] = $remainingEnv->all();
                }
            @endphp

            <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="tab-notifications" data-toggle="tab" href="#pane-notifications" role="tab" aria-controls="pane-notifications" aria-selected="true">Notifications</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-smtp" data-toggle="tab" href="#pane-smtp" role="tab" aria-controls="pane-smtp" aria-selected="false">Mailer Configuration</a>
                </li>
                @foreach($envGroups as $groupName => $groupItems)
                    @php
                        $groupId = 'pane-env-group-' . preg_replace('/[^a-z0-9]+/', '-', strtolower($groupName)) . '-' . $loop->index;
                        $groupTabId = 'tab-env-group-' . preg_replace('/[^a-z0-9]+/', '-', strtolower($groupName)) . '-' . $loop->index;
                    @endphp
                    <li class="nav-item">
                        <a class="nav-link" id="{{ $groupTabId }}" data-toggle="tab" href="#{{ $groupId }}" role="tab" aria-controls="{{ $groupId }}" aria-selected="false">{{ $groupName }}</a>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content border border-top-0 p-3" id="settingsTabContent">
                <div class="tab-pane fade show active" id="pane-notifications" role="tabpanel" aria-labelledby="tab-notifications">
                    <div class="form-group form-check">
                        <input type="hidden" name="notification_in_app_enabled" value="0">
                        <input type="checkbox" class="form-check-input" id="notification_in_app_enabled" name="notification_in_app_enabled" value="1" {{ old('notification_in_app_enabled', $settings['notification_in_app_enabled']) ? 'checked' : '' }}>
                        <label class="form-check-label" for="notification_in_app_enabled">Enable in-app notifications</label>
                    </div>

                    <div class="form-group form-check mb-0">
                        <input type="hidden" name="notification_email_enabled" value="0">
                        <input type="checkbox" class="form-check-input" id="notification_email_enabled" name="notification_email_enabled" value="1" {{ old('notification_email_enabled', $settings['notification_email_enabled']) ? 'checked' : '' }}>
                        <label class="form-check-label" for="notification_email_enabled">Enable email notifications</label>
                    </div>

                    <button type="submit" name="save_section" value="notifications" class="btn btn-outline-primary mt-3">
                        <i class="fas fa-save mr-1"></i> Save This Section
                    </button>
                </div>

                <div class="tab-pane fade" id="pane-smtp" role="tabpanel" aria-labelledby="tab-smtp">
                    <h5>Mailer Configuration</h5>
                    @php
                        $mailerCurrent = (string) old('mail_mailer', (string) $settings['mail_mailer']);
                        $smtpFieldValue = function (string $field, bool $isSecret = false) use ($settings) {
                            $oldValue = old($field);
                            $currentValue = (string) ($settings[$field] ?? '');

                            if ($oldValue === null) {
                                return $currentValue;
                            }

                            $incoming = (string) $oldValue;
                            if ($isSecret && trim($incoming) === '') {
                                return $currentValue;
                            }

                            return $incoming;
                        };

                        $smtpReady = collect([
                            trim((string) $smtpFieldValue('mail_host')),
                            trim((string) $smtpFieldValue('mail_port')),
                            trim((string) $smtpFieldValue('mail_username')),
                            trim((string) $smtpFieldValue('mail_password', true)),
                            trim((string) $smtpFieldValue('mail_from_address')),
                        ])->every(fn ($value) => $value !== '');

                        $smtpOptionEnabled = $smtpReady || $mailerCurrent === 'smtp';
                    @endphp

                    <x-adminlte-alert theme="info" title="Active Mailer" dismissable>
                        Choose your active mailer. SMTP requires full SMTP credentials, while Sendmail uses a sendmail path and Log requires no SMTP credentials.
                    </x-adminlte-alert>

                    <div class="form-group">
                        <label for="mail_mailer">Mailer</label>
                        <select class="form-control" id="mail_mailer" name="mail_mailer">
                            <option value="">Select mailer</option>
                            <option value="smtp" {{ $mailerCurrent === 'smtp' ? 'selected' : '' }} {{ $smtpOptionEnabled ? '' : 'disabled' }}>SMTP</option>
                            <option value="log" {{ $mailerCurrent === 'log' ? 'selected' : '' }}>Log</option>
                            <option value="sendmail" {{ $mailerCurrent === 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                        </select>
                        <small id="mailer-driver-hint" class="form-text text-muted">
                            {{ $smtpReady ? 'SMTP is ready to use.' : 'Complete SMTP required fields to enable SMTP selection.' }}
                        </small>
                        <small id="mailer-mode-hint" class="form-text text-muted">
                            For <strong>Sendmail</strong>, configure only <code>MAIL_SENDMAIL_PATH</code>. For <strong>Log</strong>, SMTP fields are optional.
                        </small>
                    </div>

                    <div class="card card-outline card-secondary mb-3">
                        <div class="card-header py-2">
                            <strong>SMTP Credential Checklist</strong>
                        </div>
                        <div class="card-body py-2">
                            <ul class="list-unstyled mb-0 small" id="smtp-credential-checklist">
                                <li data-mail-check-key="mail_host"><span class="badge badge-secondary">Pending</span> SMTP Host</li>
                                <li data-mail-check-key="mail_port"><span class="badge badge-secondary">Pending</span> SMTP Port</li>
                                <li data-mail-check-key="mail_username"><span class="badge badge-secondary">Pending</span> SMTP Username</li>
                                <li data-mail-check-key="mail_password"><span class="badge badge-secondary">Pending</span> SMTP Password</li>
                                <li data-mail-check-key="mail_from_address"><span class="badge badge-secondary">Pending</span> From Address</li>
                            </ul>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card card-outline card-primary h-100">
                                <div class="card-header py-2"><strong>SMTP Connection</strong></div>
                                <div class="card-body py-2">
                                    <div class="form-group mb-2">
                                        <label for="mail_host">SMTP Host</label>
                                        <input type="text" class="form-control" id="mail_host" name="mail_host" value="{{ old('mail_host', $settings['mail_host']) }}" placeholder="smtp.mailgun.org">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label for="mail_port">SMTP Port</label>
                                        <input type="number" class="form-control" id="mail_port" name="mail_port" value="{{ old('mail_port', $settings['mail_port']) }}" placeholder="587">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label for="mail_username">SMTP Username</label>
                                        <input type="text" class="form-control" id="mail_username" name="mail_username" value="{{ old('mail_username', $settings['mail_username']) }}">
                                    </div>
                                    <div class="form-group mb-2">
                                        <label for="mail_password">SMTP Password</label>
                                        <div class="env-secret-wrapper d-flex align-items-center">
                                            <input type="password" class="form-control env-secret-input" id="mail_password" name="mail_password" data-current-filled="{{ empty($settings['mail_password']) ? '0' : '1' }}" placeholder="Leave blank to keep existing">
                                            <button type="button" class="btn btn-xs btn-outline-secondary ml-2 env-secret-toggle">View</button>
                                        </div>
                                    </div>
                                    <div class="form-group mb-0">
                                        <label for="mail_encryption">Encryption</label>
                                        <input type="text" class="form-control" id="mail_encryption" name="mail_encryption" value="{{ old('mail_encryption', $settings['mail_encryption']) }}" placeholder="tls">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card card-outline card-primary h-100">
                                <div class="card-header py-2"><strong>Mailer-specific Options</strong></div>
                                <div class="card-body py-2">
                                    <div class="form-group mb-0">
                                        <label for="mail_sendmail_path">Sendmail Path</label>
                                        <input type="text" class="form-control" id="mail_sendmail_path" name="mail_sendmail_path" value="{{ old('mail_sendmail_path', $settings['mail_sendmail_path'] ?? '') }}" placeholder="/usr/sbin/sendmail -bs -i">
                                        <small class="form-text text-muted">Used when Mailer is set to Sendmail.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card card-outline card-secondary mt-3 mb-0">
                        <div class="card-header py-2"><strong>Global Sender Details</strong></div>
                        <div class="card-body py-2">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-0">
                                        <label for="mail_from_address">From Address</label>
                                        <input type="email" class="form-control" id="mail_from_address" name="mail_from_address" value="{{ old('mail_from_address', $settings['mail_from_address']) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-0">
                                        <label for="mail_from_name">From Name</label>
                                        <input type="text" class="form-control" id="mail_from_name" name="mail_from_name" value="{{ old('mail_from_name', $settings['mail_from_name']) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" name="save_section" value="smtp" class="btn btn-outline-primary mt-3">
                        <i class="fas fa-save mr-1"></i> Save This Section
                    </button>
                </div>

                @foreach($envGroups as $groupName => $groupItems)
                    @php
                        $groupId = 'pane-env-group-' . preg_replace('/[^a-z0-9]+/', '-', strtolower($groupName)) . '-' . $loop->index;
                        $groupTabId = 'tab-env-group-' . preg_replace('/[^a-z0-9]+/', '-', strtolower($groupName)) . '-' . $loop->index;
                        $displayItems = $groupItems;
                        if ($groupName === 'Filesystem' && array_key_exists('FILESYSTEM_DISK', $groupItems)) {
                            $displayItems = ['FILESYSTEM_DISK' => $groupItems['FILESYSTEM_DISK']] + collect($groupItems)->except('FILESYSTEM_DISK')->all();
                        }
                    @endphp
                    <div class="tab-pane fade" id="{{ $groupId }}" role="tabpanel" aria-labelledby="{{ $groupTabId }}">
                        <h5>{{ $groupName }} (.env)</h5>
                        <p class="text-muted mb-3">
                            Update environment values for this section.
                        </p>
                        @if($groupName === 'Filesystem')
                            <x-adminlte-alert theme="info" title="Active Filesystem Driver" dismissable>
                                Select <strong>DigitalOcean Spaces</strong> or <strong>AWS S3</strong> using <code>FILESYSTEM_DISK</code>. Keep only one active at a time.
                            </x-adminlte-alert>

                            <div class="card card-outline card-secondary mb-3">
                                <div class="card-header py-2">
                                    <strong>Credential Checklist</strong>
                                </div>
                                <div class="card-body py-2">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="mb-2">DigitalOcean Spaces</h6>
                                            <ul class="list-unstyled mb-0 small" id="do-credential-checklist">
                                                <li data-check-key="DO_SPACES_KEY" data-check-label="DO_SPACES_KEY"><span class="badge badge-secondary">Pending</span> Access Key</li>
                                                <li data-check-key="DO_SPACES_SECRET" data-check-label="DO_SPACES_SECRET"><span class="badge badge-secondary">Pending</span> Secret Key</li>
                                                <li data-check-key="DO_SPACES_REGION" data-check-label="DO_SPACES_REGION"><span class="badge badge-secondary">Pending</span> Region</li>
                                                <li data-check-key="DO_SPACES_BUCKET" data-check-label="DO_SPACES_BUCKET"><span class="badge badge-secondary">Pending</span> Bucket</li>
                                                <li data-check-key="DO_SPACES_ENDPOINT" data-check-label="DO_SPACES_ENDPOINT"><span class="badge badge-secondary">Pending</span> Endpoint</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="mb-2">AWS S3</h6>
                                            <ul class="list-unstyled mb-0 small" id="aws-credential-checklist">
                                                <li data-check-key="AWS_ACCESS_KEY_ID" data-check-label="AWS_ACCESS_KEY_ID"><span class="badge badge-secondary">Pending</span> Access Key ID</li>
                                                <li data-check-key="AWS_SECRET_ACCESS_KEY" data-check-label="AWS_SECRET_ACCESS_KEY"><span class="badge badge-secondary">Pending</span> Secret Access Key</li>
                                                <li data-check-key="AWS_DEFAULT_REGION" data-check-label="AWS_DEFAULT_REGION"><span class="badge badge-secondary">Pending</span> Default Region</li>
                                                <li data-check-key="AWS_BUCKET" data-check-label="AWS_BUCKET"><span class="badge badge-secondary">Pending</span> Bucket</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($groupName === 'Filesystem')
                            @php
                                $diskValue = old('env.FILESYSTEM_DISK', (string) ($groupItems['FILESYSTEM_DISK'] ?? ''));
                            @endphp

                            <div class="form-group">
                                <label><code>FILESYSTEM_DISK</code></label>
                                <select class="form-control form-control-sm" name="env[FILESYSTEM_DISK]">
                                    <option value="">Select driver</option>
                                    <option value="do_spaces" {{ $diskValue === 'do_spaces' ? 'selected' : '' }} {{ $doOptionEnabled ? '' : 'disabled' }}>DigitalOcean Spaces (do_spaces)</option>
                                    <option value="s3" {{ $diskValue === 's3' ? 'selected' : '' }} {{ $awsOptionEnabled ? '' : 'disabled' }}>AWS S3 (s3)</option>
                                    <option value="local" {{ $diskValue === 'local' ? 'selected' : '' }}>Local (local)</option>
                                    <option value="public" {{ $diskValue === 'public' ? 'selected' : '' }}>Public (public)</option>
                                </select>
                                <small id="filesystem-driver-hint" class="form-text text-muted">
                                    @if(!$doProviderReady && !$awsProviderReady)
                                        Complete DigitalOcean or AWS required credentials to enable that provider.
                                    @elseif(!$doProviderReady)
                                        DigitalOcean option is disabled until required credentials are filled.
                                    @elseif(!$awsProviderReady)
                                        AWS option is disabled until required credentials are filled.
                                    @else
                                        Both providers are ready. Select the one you want to use.
                                    @endif
                                </small>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card card-outline card-primary h-100">
                                        <div class="card-header py-2"><strong>DigitalOcean Spaces</strong></div>
                                        <div class="card-body py-2">
                                            @php
                                                $doFields = [
                                                    ['key' => 'DO_SPACES_KEY', 'label' => 'Access Key'],
                                                    ['key' => 'DO_SPACES_SECRET', 'label' => 'Secret Key', 'secret' => true],
                                                    ['key' => 'DO_SPACES_REGION', 'label' => 'Region'],
                                                    ['key' => 'DO_SPACES_BUCKET', 'label' => 'Bucket'],
                                                    ['key' => 'DO_SPACES_ENDPOINT', 'label' => 'Endpoint URL'],
                                                    ['key' => 'DO_SPACES_URL', 'label' => 'Public URL'],
                                                    ['key' => 'DO_SPACES_ROOT', 'label' => 'Root Path'],
                                                    ['key' => 'DO_SPACES_VISIBILITY', 'label' => 'Visibility'],
                                                ];
                                            @endphp
                                            @foreach($doFields as $field)
                                                @php
                                                    $fieldKey = $field['key'];
                                                    $isSecret = $field['secret'] ?? false;
                                                    $currentValue = (string) ($groupItems[$fieldKey] ?? '');
                                                    $fieldValue = old('env.' . $fieldKey, $isSecret ? '' : $currentValue);
                                                @endphp
                                                <div class="form-group mb-2">
                                                    <label class="mb-1"><code>{{ $fieldKey }}</code> <span class="text-muted">({{ $field['label'] }})</span></label>
                                                    @if($isSecret)
                                                        <div class="env-secret-wrapper d-flex align-items-center">
                                                            <input type="password" class="form-control form-control-sm env-secret-input" name="env[{{ $fieldKey }}]" value="{{ $fieldValue }}" data-current-filled="{{ $currentValue !== '' ? '1' : '0' }}" placeholder="•••••••• (leave blank to keep current)">
                                                            <button type="button" class="btn btn-xs btn-outline-secondary ml-2 env-secret-toggle">View</button>
                                                        </div>
                                                    @else
                                                        <input type="text" class="form-control form-control-sm" name="env[{{ $fieldKey }}]" value="{{ $fieldValue }}">
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card card-outline card-primary h-100">
                                        <div class="card-header py-2"><strong>AWS S3</strong></div>
                                        <div class="card-body py-2">
                                            @php
                                                $awsFields = [
                                                    ['key' => 'AWS_ACCESS_KEY_ID', 'label' => 'Access Key ID'],
                                                    ['key' => 'AWS_SECRET_ACCESS_KEY', 'label' => 'Secret Access Key', 'secret' => true],
                                                    ['key' => 'AWS_DEFAULT_REGION', 'label' => 'Default Region'],
                                                    ['key' => 'AWS_BUCKET', 'label' => 'Bucket'],
                                                    ['key' => 'AWS_ENDPOINT', 'label' => 'Endpoint URL'],
                                                    ['key' => 'AWS_URL', 'label' => 'Public URL'],
                                                ];
                                                $awsPathStyle = old('env.AWS_USE_PATH_STYLE_ENDPOINT', (string) ($groupItems['AWS_USE_PATH_STYLE_ENDPOINT'] ?? ''));
                                            @endphp
                                            @foreach($awsFields as $field)
                                                @php
                                                    $fieldKey = $field['key'];
                                                    $isSecret = $field['secret'] ?? false;
                                                    $currentValue = (string) ($groupItems[$fieldKey] ?? '');
                                                    $fieldValue = old('env.' . $fieldKey, $isSecret ? '' : $currentValue);
                                                @endphp
                                                <div class="form-group mb-2">
                                                    <label class="mb-1"><code>{{ $fieldKey }}</code> <span class="text-muted">({{ $field['label'] }})</span></label>
                                                    @if($isSecret)
                                                        <div class="env-secret-wrapper d-flex align-items-center">
                                                            <input type="password" class="form-control form-control-sm env-secret-input" name="env[{{ $fieldKey }}]" value="{{ $fieldValue }}" data-current-filled="{{ $currentValue !== '' ? '1' : '0' }}" placeholder="•••••••• (leave blank to keep current)">
                                                            <button type="button" class="btn btn-xs btn-outline-secondary ml-2 env-secret-toggle">View</button>
                                                        </div>
                                                    @else
                                                        <input type="text" class="form-control form-control-sm" name="env[{{ $fieldKey }}]" value="{{ $fieldValue }}">
                                                    @endif
                                                </div>
                                            @endforeach

                                            <div class="form-group mb-0">
                                                <label class="mb-1"><code>AWS_USE_PATH_STYLE_ENDPOINT</code> <span class="text-muted">(Path Style Endpoint)</span></label>
                                                <select class="form-control form-control-sm" name="env[AWS_USE_PATH_STYLE_ENDPOINT]">
                                                    <option value="" {{ $awsPathStyle === '' ? 'selected' : '' }}>Empty</option>
                                                    <option value="true" {{ strtolower($awsPathStyle) === 'true' || $awsPathStyle === '1' ? 'selected' : '' }}>true</option>
                                                    <option value="false" {{ strtolower($awsPathStyle) === 'false' || $awsPathStyle === '0' ? 'selected' : '' }}>false</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width: 35%">Key</th>
                                            <th>Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($displayItems as $envKey => $envValue)
                                            @php
                                                $isSecret = collect($secretEnvPatterns)->contains(fn($pattern) => preg_match($pattern, $envKey));
                                                $resolvedValue = is_null($envValue) || $envValue === '' ? '—' : (string) $envValue;
                                                $inputName = 'env[' . $envKey . ']';
                                                $inputValue = old('env.' . $envKey, $isSecret ? '' : (string) ($envValue ?? ''));
                                            @endphp
                                            <tr>
                                                <td><code>{{ $envKey }}</code></td>
                                                <td>
                                                    @if($isSecret)
                                                        <div class="env-secret-wrapper d-flex align-items-center justify-content-between">
                                                            <input
                                                                type="password"
                                                                class="form-control form-control-sm env-secret-input"
                                                                name="{{ $inputName }}"
                                                                value="{{ $inputValue }}"
                                                                data-current-filled="{{ empty($envValue) ? '0' : '1' }}"
                                                                placeholder="•••••••• (leave blank to keep current)"
                                                            >
                                                            <button type="button" class="btn btn-xs btn-outline-secondary ml-2 env-secret-toggle">View</button>
                                                        </div>
                                                    @else
                                                        <input
                                                            type="text"
                                                            class="form-control form-control-sm"
                                                            name="{{ $inputName }}"
                                                            value="{{ $inputValue }}"
                                                            placeholder="{{ $resolvedValue === '—' ? 'Empty value' : '' }}"
                                                        >
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        <button type="submit" name="save_section" value="env" class="btn btn-outline-primary mt-3">
                            <i class="fas fa-save mr-1"></i> Save This Section
                        </button>
                    </div>
                @endforeach
            </div>

            <button type="submit" name="save_section" value="all" class="btn btn-primary mt-3">
                <i class="fas fa-save mr-1"></i> Save All Configuration
            </button>
        </form>
    </x-adminlte-card>
@stop

@section('js')
    <script>
        (function () {
            const form = document.getElementById('system-settings-form');
            if (!form) return;

            const activeTabInput = document.getElementById('active_tab');
            const activeTabLabelInput = document.getElementById('active_tab_label');
            const initialTabId = @json(old('active_tab', session('active_tab', 'pane-notifications')));

            const settingsTabLinks = form.querySelectorAll('#settingsTabs .nav-link[data-toggle="tab"]');
            const updateActiveTabState = (tabLink) => {
                if (!tabLink || !activeTabInput || !activeTabLabelInput) return;

                const href = tabLink.getAttribute('href') || '';
                if (!href.startsWith('#')) return;

                activeTabInput.value = href.substring(1);
                activeTabLabelInput.value = (tabLink.textContent || '').trim();
            };

            settingsTabLinks.forEach((tabLink) => {
                tabLink.addEventListener('click', function () {
                    updateActiveTabState(this);
                });
            });

            if (initialTabId) {
                const initialLink = form.querySelector(`#settingsTabs .nav-link[href="#${initialTabId}"]`);
                if (initialLink) {
                    if (window.jQuery && typeof window.jQuery(initialLink).tab === 'function') {
                        window.jQuery(initialLink).tab('show');
                    }
                    updateActiveTabState(initialLink);
                }
            }

            const activeLink = form.querySelector('#settingsTabs .nav-link.active[data-toggle="tab"]');
            if (activeLink) {
                updateActiveTabState(activeLink);
            }

            const toggleButtons = form.querySelectorAll('.env-secret-toggle');
            toggleButtons.forEach((button) => {
                button.addEventListener('click', function () {
                    const container = this.closest('.env-secret-wrapper') || this.closest('td');
                    if (!container) return;

                    const inputEl = container.querySelector('.env-secret-input');
                    if (!inputEl) return;

                    const isVisible = inputEl.getAttribute('type') === 'text';
                    if (isVisible) {
                        inputEl.setAttribute('type', 'password');
                        this.textContent = 'View';
                        return;
                    }

                    inputEl.setAttribute('type', 'text');
                    this.textContent = 'Hide';
                });
            });

            const filesystemDriverSelect = form.querySelector('select[name="env[FILESYSTEM_DISK]"]');
            if (filesystemDriverSelect) {
                const driverHint = form.querySelector('#filesystem-driver-hint');
                const doRequiredNames = [
                    'env[DO_SPACES_KEY]',
                    'env[DO_SPACES_SECRET]',
                    'env[DO_SPACES_REGION]',
                    'env[DO_SPACES_BUCKET]',
                    'env[DO_SPACES_ENDPOINT]'
                ];
                const awsRequiredNames = [
                    'env[AWS_ACCESS_KEY_ID]',
                    'env[AWS_SECRET_ACCESS_KEY]',
                    'env[AWS_DEFAULT_REGION]',
                    'env[AWS_BUCKET]'
                ];

                const isFilled = (input) => {
                    if (!input) return false;
                    const value = (input.value || '').trim();
                    if (value !== '') return true;

                    const hasCurrent = input.getAttribute('data-current-filled') === '1';
                    return hasCurrent;
                };

                const isProviderReady = (names) => names.every((name) => isFilled(form.querySelector(`[name="${name}"]`)));

                const updateChecklist = () => {
                    const checklistItems = form.querySelectorAll('[data-check-key]');
                    checklistItems.forEach((item) => {
                        const key = item.getAttribute('data-check-key');
                        if (!key) return;

                        const field = form.querySelector(`[name="env[${key}]"]`);
                        const badge = item.querySelector('.badge');
                        if (!badge) return;

                        const ready = isFilled(field);
                        badge.textContent = ready ? 'Ready' : 'Missing';
                        badge.classList.remove('badge-success', 'badge-danger', 'badge-secondary');
                        badge.classList.add(ready ? 'badge-success' : 'badge-danger');
                    });
                };

                const updateDriverOptions = () => {
                    const doReady = isProviderReady(doRequiredNames);
                    const awsReady = isProviderReady(awsRequiredNames);
                    const selectedValue = filesystemDriverSelect.value;

                    const doOption = filesystemDriverSelect.querySelector('option[value="do_spaces"]');
                    const awsOption = filesystemDriverSelect.querySelector('option[value="s3"]');

                    if (doOption) {
                        doOption.disabled = !doReady && selectedValue !== 'do_spaces';
                    }

                    if (awsOption) {
                        awsOption.disabled = !awsReady && selectedValue !== 's3';
                    }

                    if ((!doReady && selectedValue === 'do_spaces') || (!awsReady && selectedValue === 's3')) {
                        filesystemDriverSelect.value = '';
                    }

                    updateChecklist();

                    if (!driverHint) return;

                    if (!doReady && !awsReady) {
                        driverHint.textContent = 'Complete DigitalOcean or AWS required credentials to enable that provider.';
                        return;
                    }

                    if (!doReady) {
                        driverHint.textContent = 'DigitalOcean option is disabled until required credentials are filled.';
                        return;
                    }

                    if (!awsReady) {
                        driverHint.textContent = 'AWS option is disabled until required credentials are filled.';
                        return;
                    }

                    driverHint.textContent = 'Both providers are ready. Select the one you want to use.';
                };

                [...doRequiredNames, ...awsRequiredNames].forEach((name) => {
                    const input = form.querySelector(`[name="${name}"]`);
                    if (!input) return;
                    input.addEventListener('input', updateDriverOptions);
                    input.addEventListener('change', updateDriverOptions);
                });

                updateDriverOptions();
                updateChecklist();
            }

            const mailerSelect = form.querySelector('select[name="mail_mailer"]');
            if (mailerSelect) {
                const mailerHint = form.querySelector('#mailer-driver-hint');
                const mailerModeHint = form.querySelector('#mailer-mode-hint');
                const smtpRequiredNames = [
                    'mail_host',
                    'mail_port',
                    'mail_username',
                    'mail_password',
                    'mail_from_address'
                ];

                const mailInputByName = (name) => form.querySelector(`[name="${name}"]`);

                const isMailFieldFilled = (input) => {
                    if (!input) return false;
                    const value = (input.value || '').trim();
                    if (value !== '') return true;

                    return input.getAttribute('data-current-filled') === '1';
                };

                const isSmtpReady = () => smtpRequiredNames.every((name) => isMailFieldFilled(mailInputByName(name)));

                const updateMailChecklist = () => {
                    const items = form.querySelectorAll('[data-mail-check-key]');
                    items.forEach((item) => {
                        const key = item.getAttribute('data-mail-check-key');
                        if (!key) return;

                        const field = mailInputByName(key);
                        const badge = item.querySelector('.badge');
                        if (!badge) return;

                        const ready = isMailFieldFilled(field);
                        badge.textContent = ready ? 'Ready' : 'Missing';
                        badge.classList.remove('badge-success', 'badge-danger', 'badge-secondary');
                        badge.classList.add(ready ? 'badge-success' : 'badge-danger');
                    });
                };

                const updateMailerOptions = () => {
                    const smtpReady = isSmtpReady();
                    const smtpOption = mailerSelect.querySelector('option[value="smtp"]');
                    const selectedValue = mailerSelect.value;

                    if (smtpOption) {
                        smtpOption.disabled = !smtpReady && selectedValue !== 'smtp';
                    }

                    if (!smtpReady && selectedValue === 'smtp') {
                        mailerSelect.value = '';
                    }

                    if (mailerHint) {
                        mailerHint.textContent = smtpReady
                            ? 'SMTP is ready to use.'
                            : 'Complete SMTP required fields to enable SMTP selection.';
                    }

                    if (mailerModeHint) {
                        const selected = mailerSelect.value;
                        if (selected === 'sendmail') {
                            mailerModeHint.textContent = 'Sendmail selected: configure MAIL_SENDMAIL_PATH; SMTP fields are not required.';
                        } else if (selected === 'log') {
                            mailerModeHint.textContent = 'Log selected: no SMTP credentials required.';
                        } else if (selected === 'smtp') {
                            mailerModeHint.textContent = 'SMTP selected: host, port, username, password, and from address must be complete.';
                        } else {
                            mailerModeHint.textContent = 'For Sendmail, configure MAIL_SENDMAIL_PATH. For Log, SMTP fields are optional.';
                        }
                    }

                    updateMailChecklist();
                };

                smtpRequiredNames.forEach((name) => {
                    const input = mailInputByName(name);
                    if (!input) return;
                    input.addEventListener('input', updateMailerOptions);
                    input.addEventListener('change', updateMailerOptions);
                });

                updateMailerOptions();
                updateMailChecklist();
            }

            const isProduction = @json(app()->environment('production'));
            if (!isProduction) return;

            let productionSubmitConfirmed = false;

            form.addEventListener('submit', function (event) {
                const activeLinkOnSubmit = form.querySelector('#settingsTabs .nav-link.active[data-toggle="tab"]');
                if (activeLinkOnSubmit) {
                    updateActiveTabState(activeLinkOnSubmit);
                }

                if (productionSubmitConfirmed) {
                    productionSubmitConfirmed = false;
                    return;
                }

                event.preventDefault();

                const confirmationMessage = 'You are in PRODUCTION. This will update live configuration and clear cache. Do you want to continue?';

                if (!(window.Swal && typeof window.Swal.fire === 'function')) {
                    return;
                }

                window.Swal.fire({
                    title: 'Apply production settings?',
                    text: confirmationMessage,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, apply changes',
                    cancelButtonText: 'Cancel'
                }).then(function (result) {
                    if (!result.isConfirmed) {
                        return;
                    }

                    productionSubmitConfirmed = true;
                    if (typeof form.requestSubmit === 'function') {
                        form.requestSubmit();
                    } else {
                        form.submit();
                    }
                });
            });
        })();
    </script>
@stop
