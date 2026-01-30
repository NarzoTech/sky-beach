<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Print Station') }} - {{ $setting->name ?? config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #1a1a2e;
            color: #eee;
            min-height: 100vh;
        }
        .print-station-header {
            background: linear-gradient(135deg, #16213e 0%, #1a1a2e 100%);
            padding: 20px;
            border-bottom: 1px solid #0f3460;
        }
        .status-card {
            background: #16213e;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            border: 1px solid #0f3460;
        }
        .status-card .number {
            font-size: 2.5rem;
            font-weight: bold;
        }
        .status-card.pending .number { color: #ffc107; }
        .status-card.printing .number { color: #17a2b8; }
        .status-card.printed .number { color: #28a745; }
        .status-card.failed .number { color: #dc3545; }

        .job-card {
            background: #16213e;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
            border-left: 4px solid #0f3460;
            transition: all 0.3s ease;
        }
        .job-card.printing {
            border-left-color: #17a2b8;
            animation: pulse 1s infinite;
        }
        .job-card.kitchen { border-left-color: #fd7e14; }
        .job-card.cash { border-left-color: #28a745; }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .auto-print-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .auto-print-toggle .form-check-input {
            width: 50px;
            height: 25px;
        }
        .auto-print-toggle .form-check-input:checked {
            background-color: #28a745;
            border-color: #28a745;
        }

        .connection-status {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .connection-status .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #28a745;
            animation: blink 2s infinite;
        }
        .connection-status.disconnected .dot {
            background: #dc3545;
            animation: none;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .log-entry {
            padding: 8px 12px;
            border-radius: 5px;
            margin-bottom: 5px;
            font-size: 0.875rem;
            background: #0f3460;
        }
        .log-entry.success { border-left: 3px solid #28a745; }
        .log-entry.error { border-left: 3px solid #dc3545; }
        .log-entry.info { border-left: 3px solid #17a2b8; }

        .print-queue {
            max-height: 400px;
            overflow-y: auto;
        }
        .print-log {
            max-height: 300px;
            overflow-y: auto;
        }

        #printFrame {
            position: absolute;
            left: -9999px;
            top: -9999px;
            width: 0;
            height: 0;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="print-station-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <h4 class="mb-0">
                        <i class='bx bx-printer'></i> {{ __('Print Station') }}
                    </h4>
                    <small class="text-muted">{{ __('Automatic Browser Printing') }}</small>
                </div>
                <div class="col-md-4 text-center">
                    <div class="connection-status" id="connectionStatus">
                        <span class="dot"></span>
                        <span>{{ __('Connected') }}</span>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="auto-print-toggle">
                        <span>{{ __('Auto Print') }}</span>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="autoPrintToggle" checked>
                        </div>
                        <select class="form-select form-select-sm" id="printerSelect" style="width: 200px;">
                            <option value="">{{ __('All Printers') }}</option>
                            @foreach($printers as $printer)
                                <option value="{{ $printer->id }}" {{ $selectedPrinterId == $printer->id ? 'selected' : '' }}>
                                    {{ $printer->name }} ({{ ucfirst(str_replace('_', ' ', $printer->type)) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid py-4">
        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="status-card pending">
                    <div class="number" id="pendingCount">0</div>
                    <div>{{ __('Pending') }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="status-card printing">
                    <div class="number" id="printingCount">0</div>
                    <div>{{ __('Printing') }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="status-card printed">
                    <div class="number" id="printedCount">0</div>
                    <div>{{ __('Printed Today') }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="status-card failed">
                    <div class="number" id="failedCount">0</div>
                    <div>{{ __('Failed') }}</div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Print Queue -->
            <div class="col-md-6">
                <div class="card bg-dark border-secondary">
                    <div class="card-header bg-transparent border-secondary d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class='bx bx-list-ul'></i> {{ __('Print Queue') }}</h5>
                        <button class="btn btn-sm btn-outline-light" onclick="refreshQueue()">
                            <i class='bx bx-refresh'></i>
                        </button>
                    </div>
                    <div class="card-body print-queue" id="printQueue">
                        <div class="empty-state">
                            <i class='bx bx-check-circle'></i>
                            <p>{{ __('No pending print jobs') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Print Log -->
            <div class="col-md-6">
                <div class="card bg-dark border-secondary">
                    <div class="card-header bg-transparent border-secondary d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class='bx bx-history'></i> {{ __('Print Log') }}</h5>
                        <button class="btn btn-sm btn-outline-light" onclick="clearLog()">
                            <i class='bx bx-trash'></i> {{ __('Clear') }}
                        </button>
                    </div>
                    <div class="card-body print-log" id="printLog">
                        <div class="log-entry info">
                            <i class='bx bx-info-circle'></i> {{ __('Print station started') }} - {{ now()->format('H:i:s') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Settings -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card bg-dark border-secondary">
                    <div class="card-header bg-transparent border-secondary">
                        <h5 class="mb-0"><i class='bx bx-cog'></i> {{ __('Settings') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">{{ __('Poll Interval (seconds)') }}</label>
                                <input type="number" class="form-control bg-dark text-light border-secondary"
                                       id="pollInterval" value="3" min="1" max="30">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ __('Auto-close print dialog') }}</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="autoCloseDialog" checked>
                                    <label class="form-check-label">{{ __('Enabled') }}</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ __('Sound Notification') }}</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="soundEnabled" checked>
                                    <label class="form-check-label">{{ __('Enabled') }}</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ __('Actions') }}</label>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-warning btn-sm" onclick="retryAllFailed()">
                                        <i class='bx bx-revision'></i> {{ __('Retry Failed') }}
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" onclick="clearOldJobs()">
                                        <i class='bx bx-trash'></i> {{ __('Clear Old') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden iframe for printing -->
    <iframe id="printFrame" name="printFrame"></iframe>

    <!-- Audio for notifications -->
    <audio id="printSound" preload="auto">
        <source src="data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1sbHCAiIqGdWlncXuDhX92bG1ydXl3b2ttc3l8fHdycHN5fX9+e3l3eXx+f4GBgH9+fX5/gIGBgYGAgH+AgYKCgoKBgYGBgYKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKC" type="audio/wav">
    </audio>

    <script>
        // Configuration
        let config = {
            autoPrint: true,
            pollInterval: 3000,
            autoCloseDialog: true,
            soundEnabled: true,
            selectedPrinter: '{{ $selectedPrinterId ?? '' }}',
            isProcessing: false,
            currentJobId: null,
        };

        // API Endpoints
        const API = {
            getPendingJobs: '{{ route("admin.pos.print-station.pending-jobs") }}',
            getJobContent: (id) => `{{ url("admin/pos/print-station/job") }}/${id}/content`,
            markPrinted: (id) => `{{ url("admin/pos/print-station/job") }}/${id}/printed`,
            markFailed: (id) => `{{ url("admin/pos/print-station/job") }}/${id}/failed`,
            retryJob: (id) => `{{ url("admin/pos/print-station/job") }}/${id}/retry`,
            getStats: '{{ route("admin.pos.print-station.stats") }}',
            clearOldJobs: '{{ route("admin.pos.print-station.clear-old") }}',
        };

        // CSRF Token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            // Load saved settings
            loadSettings();

            // Bind events
            document.getElementById('autoPrintToggle').addEventListener('change', function() {
                config.autoPrint = this.checked;
                saveSettings();
                addLog(this.checked ? 'Auto-print enabled' : 'Auto-print disabled', 'info');
            });

            document.getElementById('printerSelect').addEventListener('change', function() {
                config.selectedPrinter = this.value;
                saveSettings();
                refreshQueue();
            });

            document.getElementById('pollInterval').addEventListener('change', function() {
                config.pollInterval = parseInt(this.value) * 1000;
                saveSettings();
                restartPolling();
            });

            document.getElementById('autoCloseDialog').addEventListener('change', function() {
                config.autoCloseDialog = this.checked;
                saveSettings();
            });

            document.getElementById('soundEnabled').addEventListener('change', function() {
                config.soundEnabled = this.checked;
                saveSettings();
            });

            // Start polling
            startPolling();
            updateStats();

            // Update stats every 30 seconds
            setInterval(updateStats, 30000);
        });

        let pollTimer = null;

        function startPolling() {
            pollTimer = setInterval(checkForJobs, config.pollInterval);
            addLog('Polling started', 'info');
        }

        function restartPolling() {
            if (pollTimer) clearInterval(pollTimer);
            startPolling();
        }

        async function checkForJobs() {
            if (!config.autoPrint || config.isProcessing) return;

            try {
                let url = API.getPendingJobs;
                if (config.selectedPrinter) {
                    url += `?printer_id=${config.selectedPrinter}`;
                }

                const response = await fetch(url, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await response.json();

                if (data.success && data.jobs.length > 0) {
                    updateQueueDisplay(data.jobs);
                    // Process first job
                    processJob(data.jobs[0]);
                } else {
                    updateQueueDisplay([]);
                }
            } catch (error) {
                console.error('Error checking for jobs:', error);
                setConnectionStatus(false);
            }
        }

        async function processJob(job) {
            if (config.isProcessing) return;

            config.isProcessing = true;
            config.currentJobId = job.id;

            addLog(`Processing: ${job.invoice || 'Order'} (${job.printer_name})`, 'info');
            playSound();

            try {
                // Load content into iframe
                const iframe = document.getElementById('printFrame');
                iframe.src = API.getJobContent(job.id);

                // Wait for iframe to load
                iframe.onload = async function() {
                    try {
                        // Trigger print
                        iframe.contentWindow.print();

                        // Mark as printed (assuming user printed)
                        // In production, you might want to detect actual print completion
                        setTimeout(async () => {
                            await markJobPrinted(job.id);
                            addLog(`Printed: ${job.invoice || 'Order'} (${job.printer_name})`, 'success');
                            config.isProcessing = false;
                            config.currentJobId = null;
                            updateStats();
                        }, 1000);

                    } catch (printError) {
                        await markJobFailed(job.id, printError.message);
                        addLog(`Failed: ${job.invoice || 'Order'} - ${printError.message}`, 'error');
                        config.isProcessing = false;
                        config.currentJobId = null;
                    }
                };

                iframe.onerror = async function() {
                    await markJobFailed(job.id, 'Failed to load print content');
                    addLog(`Failed to load: ${job.invoice || 'Order'}`, 'error');
                    config.isProcessing = false;
                    config.currentJobId = null;
                };

            } catch (error) {
                await markJobFailed(job.id, error.message);
                addLog(`Error: ${error.message}`, 'error');
                config.isProcessing = false;
                config.currentJobId = null;
            }
        }

        async function markJobPrinted(jobId) {
            await fetch(API.markPrinted(jobId), {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                }
            });
        }

        async function markJobFailed(jobId, error) {
            await fetch(API.markFailed(jobId), {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ error: error })
            });
        }

        async function retryJob(jobId) {
            try {
                await fetch(API.retryJob(jobId), {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    }
                });
                addLog('Job requeued for printing', 'info');
                refreshQueue();
                updateStats();
            } catch (error) {
                addLog(`Retry failed: ${error.message}`, 'error');
            }
        }

        async function retryAllFailed() {
            // Get failed jobs and retry each
            try {
                const response = await fetch('{{ route("admin.pos.print-station.failed-jobs") }}');
                const data = await response.json();

                for (const job of data.jobs) {
                    await retryJob(job.id);
                }

                addLog(`Retried ${data.jobs.length} failed jobs`, 'info');
            } catch (error) {
                addLog(`Error retrying jobs: ${error.message}`, 'error');
            }
        }

        async function clearOldJobs() {
            if (!confirm('Clear all printed jobs older than 24 hours?')) return;

            try {
                const response = await fetch(API.clearOldJobs, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    }
                });
                const data = await response.json();
                addLog(data.message, 'success');
                updateStats();
            } catch (error) {
                addLog(`Error: ${error.message}`, 'error');
            }
        }

        async function updateStats() {
            try {
                const response = await fetch(API.getStats);
                const data = await response.json();

                document.getElementById('pendingCount').textContent = data.stats.pending;
                document.getElementById('printingCount').textContent = data.stats.printing;
                document.getElementById('printedCount').textContent = data.stats.printed_today;
                document.getElementById('failedCount').textContent = data.stats.failed;

                setConnectionStatus(true);
            } catch (error) {
                setConnectionStatus(false);
            }
        }

        function updateQueueDisplay(jobs) {
            const container = document.getElementById('printQueue');

            if (jobs.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class='bx bx-check-circle'></i>
                        <p>{{ __('No pending print jobs') }}</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = jobs.map(job => `
                <div class="job-card ${job.printer_type} ${config.currentJobId === job.id ? 'printing' : ''}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <strong>${job.invoice || 'Order #' + job.sale_id}</strong>
                            ${job.table_name ? `<span class="badge bg-secondary ms-2">${job.table_name}</span>` : ''}
                            <div class="small text-muted mt-1">
                                <i class='bx bx-printer'></i> ${job.printer_name}
                                <span class="ms-2"><i class='bx bx-time'></i> ${job.created_at}</span>
                            </div>
                        </div>
                        <div>
                            <span class="badge bg-${job.printer_type === 'kitchen' ? 'warning' : 'success'}">
                                ${job.printer_type === 'kitchen' ? 'Kitchen' : 'Cash'}
                            </span>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function refreshQueue() {
            checkForJobs();
        }

        function setConnectionStatus(connected) {
            const status = document.getElementById('connectionStatus');
            if (connected) {
                status.classList.remove('disconnected');
                status.innerHTML = '<span class="dot"></span><span>{{ __("Connected") }}</span>';
            } else {
                status.classList.add('disconnected');
                status.innerHTML = '<span class="dot"></span><span>{{ __("Disconnected") }}</span>';
            }
        }

        function addLog(message, type = 'info') {
            const container = document.getElementById('printLog');
            const time = new Date().toLocaleTimeString();
            const icon = type === 'success' ? 'bx-check' : (type === 'error' ? 'bx-x' : 'bx-info-circle');

            const logEntry = document.createElement('div');
            logEntry.className = `log-entry ${type}`;
            logEntry.innerHTML = `<i class='bx ${icon}'></i> <span class="text-muted">[${time}]</span> ${message}`;

            container.insertBefore(logEntry, container.firstChild);

            // Keep only last 50 entries
            while (container.children.length > 50) {
                container.removeChild(container.lastChild);
            }
        }

        function clearLog() {
            document.getElementById('printLog').innerHTML = `
                <div class="log-entry info">
                    <i class='bx bx-info-circle'></i> Log cleared - ${new Date().toLocaleTimeString()}
                </div>
            `;
        }

        function playSound() {
            if (config.soundEnabled) {
                document.getElementById('printSound').play().catch(() => {});
            }
        }

        function saveSettings() {
            localStorage.setItem('printStationConfig', JSON.stringify({
                autoPrint: config.autoPrint,
                pollInterval: config.pollInterval,
                autoCloseDialog: config.autoCloseDialog,
                soundEnabled: config.soundEnabled,
                selectedPrinter: config.selectedPrinter,
            }));
        }

        function loadSettings() {
            const saved = localStorage.getItem('printStationConfig');
            if (saved) {
                const settings = JSON.parse(saved);
                config = { ...config, ...settings };

                document.getElementById('autoPrintToggle').checked = config.autoPrint;
                document.getElementById('pollInterval').value = config.pollInterval / 1000;
                document.getElementById('autoCloseDialog').checked = config.autoCloseDialog;
                document.getElementById('soundEnabled').checked = config.soundEnabled;
                if (config.selectedPrinter) {
                    document.getElementById('printerSelect').value = config.selectedPrinter;
                }
            }
        }

        // Handle visibility change - pause/resume polling
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                if (pollTimer) clearInterval(pollTimer);
            } else {
                startPolling();
            }
        });
    </script>
</body>
</html>
