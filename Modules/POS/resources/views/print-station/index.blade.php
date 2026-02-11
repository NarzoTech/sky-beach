@extends('admin.layouts.master')
@section('title', __('Print Station'))

@section('content')
    <div class="card mb-3 page-title-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h4 class="section_title mb-0">{{ __('Print Station') }}</h4>
                <small class="text-muted">{{ __('Automatic Browser Printing') }}</small>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="connection-status" id="connectionStatus">
                    <span class="dot"></span>
                    <span>{{ __('Connected') }}</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="small">{{ __('Auto Print') }}</span>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" id="autoPrintToggle" checked>
                    </div>
                </div>
                <select class="form-control" id="printerSelect" style="width: 200px;">
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

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="fs-1 fw-bold text-warning" id="pendingCount">0</div>
                    <div class="text-muted">{{ __('Pending') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="fs-1 fw-bold text-info" id="printingCount">0</div>
                    <div class="text-muted">{{ __('Printing') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="fs-1 fw-bold text-success" id="printedCount">0</div>
                    <div class="text-muted">{{ __('Printed Today') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <div class="fs-1 fw-bold text-danger" id="failedCount">0</div>
                    <div class="text-muted">{{ __('Failed') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Print Queue -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class='bx bx-list-ul me-1'></i> {{ __('Print Queue') }}</h5>
                    <button class="btn btn-sm bg-label-primary" onclick="refreshQueue()">
                        <i class='bx bx-refresh'></i>
                    </button>
                </div>
                <div class="card-body print-queue" id="printQueue">
                    <div class="text-center py-4">
                        <i class='bx bx-check-circle bx-lg text-muted d-block mb-2'></i>
                        <p class="text-muted mb-0">{{ __('No pending print jobs') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Print Log -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class='bx bx-history me-1'></i> {{ __('Print Log') }}</h5>
                    <button class="btn btn-sm bg-label-danger" onclick="clearLog()">
                        <i class='bx bx-trash'></i> {{ __('Clear') }}
                    </button>
                </div>
                <div class="card-body print-log" id="printLog">
                    <div class="log-entry log-info">
                        <i class='bx bx-info-circle'></i> {{ __('Print station started') }} - {{ now()->format('H:i:s') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Settings -->
    <div class="card mt-3">
        <div class="card-header">
            <h5 class="mb-0"><i class='bx bx-cog me-1'></i> {{ __('Settings') }}</h5>
        </div>
        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ __('Poll Interval (seconds)') }}</label>
                        <input type="number" class="form-control" id="pollInterval" value="3" min="1" max="30">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ __('Auto-close print dialog') }}</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" id="autoCloseDialog" checked>
                            <label class="form-check-label">{{ __('Enabled') }}</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ __('Sound Notification') }}</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" id="soundEnabled" checked>
                            <label class="form-check-label">{{ __('Enabled') }}</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>{{ __('Actions') }}</label>
                        <div class="d-flex gap-2">
                            <button class="btn bg-label-warning btn-sm" onclick="retryAllFailed()">
                                <i class='bx bx-revision'></i> {{ __('Retry Failed') }}
                            </button>
                            <button class="btn bg-label-danger btn-sm" onclick="clearOldJobs()">
                                <i class='bx bx-trash'></i> {{ __('Clear Old') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden iframe for printing -->
    <iframe id="printFrame" name="printFrame" style="position:absolute;left:-9999px;top:-9999px;width:0;height:0;"></iframe>

    <!-- Audio for notifications -->
    <audio id="printSound" preload="auto">
        <source src="data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1sbHCAiIqGdWlncXuDhX92bG1ydXl3b2ttc3l8fHdycHN5fX9+e3l3eXx+f4GBgH9+fX5/gIGBgYGAgH+AgYKCgoKBgYGBgYKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKC" type="audio/wav">
    </audio>
@endsection

@push('css')
<style>
    .connection-status {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .connection-status .dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #71dd37;
        animation: blink 2s infinite;
    }
    .connection-status.disconnected .dot {
        background: #ff3e1d;
        animation: none;
    }
    @keyframes blink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    .print-queue {
        max-height: 400px;
        overflow-y: auto;
    }
    .print-log {
        max-height: 300px;
        overflow-y: auto;
    }

    .job-card {
        background: #f5f5f9;
        border-radius: 8px;
        padding: 12px 15px;
        margin-bottom: 8px;
        border-left: 4px solid #ddd;
        transition: all 0.3s ease;
    }
    .job-card.printing {
        border-left-color: #03c3ec;
        animation: pulse 1s infinite;
    }
    .job-card.kitchen { border-left-color: #ffab00; }
    .job-card.cash_counter { border-left-color: #71dd37; }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }

    .log-entry {
        padding: 8px 12px;
        border-radius: 6px;
        margin-bottom: 5px;
        font-size: 0.875rem;
        background: #f5f5f9;
    }
    .log-entry.log-success { border-left: 3px solid #71dd37; }
    .log-entry.log-error { border-left: 3px solid #ff3e1d; }
    .log-entry.log-info { border-left: 3px solid #03c3ec; }
</style>
@endpush

@push('js')
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
            const iframe = document.getElementById('printFrame');
            iframe.src = API.getJobContent(job.id);

            iframe.onload = async function() {
                try {
                    iframe.contentWindow.print();

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
                <div class="text-center py-4">
                    <i class='bx bx-check-circle bx-lg text-muted d-block mb-2'></i>
                    <p class="text-muted mb-0">{{ __('No pending print jobs') }}</p>
                </div>
            `;
            return;
        }

        container.innerHTML = jobs.map(job => `
            <div class="job-card ${job.printer_type} ${config.currentJobId === job.id ? 'printing' : ''}">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <strong>${job.invoice || 'Order #' + job.sale_id}</strong>
                        ${job.table_name ? `<span class="badge bg-label-secondary ms-2">${job.table_name}</span>` : ''}
                        <div class="small text-muted mt-1">
                            <i class='bx bx-printer'></i> ${job.printer_name}
                            <span class="ms-2"><i class='bx bx-time'></i> ${job.created_at}</span>
                        </div>
                    </div>
                    <div>
                        <span class="badge bg-label-${job.printer_type === 'kitchen' ? 'warning' : 'success'}">
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
        logEntry.className = `log-entry log-${type}`;
        logEntry.innerHTML = `<i class='bx ${icon}'></i> <span class="text-muted">[${time}]</span> ${message}`;

        container.insertBefore(logEntry, container.firstChild);

        while (container.children.length > 50) {
            container.removeChild(container.lastChild);
        }
    }

    function clearLog() {
        document.getElementById('printLog').innerHTML = `
            <div class="log-entry log-info">
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
@endpush
