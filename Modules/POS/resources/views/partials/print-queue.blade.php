{{-- Print Queue Component - Include this in POS layout for automatic printing --}}
<div id="print-queue-indicator" class="position-fixed" style="bottom: 20px; left: 20px; z-index: 1050; display: none;">
    <div class="card shadow-lg" style="width: 300px;">
        <div class="card-header bg-primary text-white py-2 d-flex justify-content-between align-items-center">
            <span><i class="fas fa-print me-2"></i>{{ __('Print Queue') }}</span>
            <span class="badge bg-light text-dark" id="print-queue-count">0</span>
        </div>
        <div class="card-body py-2" id="print-queue-list" style="max-height: 200px; overflow-y: auto;">
            <div class="text-center text-muted small py-2">{{ __('No pending print jobs') }}</div>
        </div>
        <div class="card-footer py-2">
            <button class="btn btn-sm btn-success w-100" onclick="processPrintQueue()">
                <i class="fas fa-play me-1"></i>{{ __('Print All') }}
            </button>
        </div>
    </div>
</div>

{{-- Hidden iframe for printing --}}
<iframe id="print-frame" style="display: none;"></iframe>

<script>
(function() {
    let printQueue = [];
    let isProcessing = false;
    let pollInterval = null;

    // Start polling for print jobs
    function startPrintQueuePolling() {
        pollInterval = setInterval(fetchPendingJobs, 5000); // Poll every 5 seconds
        fetchPendingJobs(); // Initial fetch
    }

    // Fetch pending print jobs
    function fetchPendingJobs() {
        $.ajax({
            url: "{{ route('admin.pos.print.pending-jobs') }}",
            method: 'GET',
            success: function(jobs) {
                printQueue = jobs;
                updatePrintQueueUI();
            },
            error: function(xhr) {
                console.error('Failed to fetch print jobs:', xhr);
            }
        });
    }

    // Update the print queue UI
    function updatePrintQueueUI() {
        const indicator = document.getElementById('print-queue-indicator');
        const countBadge = document.getElementById('print-queue-count');
        const listContainer = document.getElementById('print-queue-list');

        if (printQueue.length === 0) {
            indicator.style.display = 'none';
            return;
        }

        indicator.style.display = 'block';
        countBadge.textContent = printQueue.length;

        let html = '';
        printQueue.forEach(function(job) {
            const printerIcon = job.printer.type === 'kitchen' ? 'fa-utensils' : 'fa-cash-register';
            const statusClass = job.status === 'failed' ? 'text-danger' : 'text-warning';

            html += `
                <div class="d-flex justify-content-between align-items-center mb-2 small">
                    <div>
                        <i class="fas ${printerIcon} me-1"></i>
                        <span>${job.printer.name}</span>
                        <br>
                        <small class="text-muted">Order #${job.sale_id}</small>
                    </div>
                    <div>
                        <span class="badge ${job.status === 'failed' ? 'bg-danger' : 'bg-warning'}">
                            ${job.status}
                        </span>
                        <button class="btn btn-xs btn-outline-primary ms-1" onclick="printSingleJob(${job.id})">
                            <i class="fas fa-print"></i>
                        </button>
                    </div>
                </div>
            `;
        });

        listContainer.innerHTML = html;
    }

    // Process entire print queue
    window.processPrintQueue = function() {
        if (isProcessing || printQueue.length === 0) return;

        isProcessing = true;
        processNextJob(0);
    };

    // Process jobs sequentially
    function processNextJob(index) {
        if (index >= printQueue.length) {
            isProcessing = false;
            fetchPendingJobs();
            return;
        }

        const job = printQueue[index];
        printJob(job, function() {
            setTimeout(function() {
                processNextJob(index + 1);
            }, 1000); // Wait 1 second between prints
        });
    }

    // Print a single job
    window.printSingleJob = function(jobId) {
        const job = printQueue.find(j => j.id === jobId);
        if (job) {
            printJob(job, fetchPendingJobs);
        }
    };

    // Execute print job
    function printJob(job, callback) {
        // Fetch job content and print
        $.ajax({
            url: "{{ url('admin/pos/print/job') }}/" + job.id + "/content",
            method: 'GET',
            success: function(content) {
                // Open print window
                const printWindow = window.open('', '_blank', 'width=400,height=600');

                if (printWindow) {
                    printWindow.document.write(content);
                    printWindow.document.close();

                    // Wait for content to load then print
                    printWindow.onload = function() {
                        printWindow.focus();
                        printWindow.print();

                        // Mark as printed after a delay
                        setTimeout(function() {
                            markJobAsPrinted(job.id);
                            printWindow.close();
                            if (callback) callback();
                        }, 2000);
                    };
                } else {
                    // Popup blocked - use iframe fallback
                    const iframe = document.getElementById('print-frame');
                    iframe.contentDocument.open();
                    iframe.contentDocument.write(content);
                    iframe.contentDocument.close();

                    setTimeout(function() {
                        iframe.contentWindow.print();
                        markJobAsPrinted(job.id);
                        if (callback) callback();
                    }, 500);
                }
            },
            error: function(xhr) {
                console.error('Failed to get print content:', xhr);
                markJobAsFailed(job.id, 'Failed to fetch content');
                if (callback) callback();
            }
        });
    }

    // Mark job as printed
    function markJobAsPrinted(jobId) {
        $.ajax({
            url: "{{ url('admin/pos/print/job') }}/" + jobId + "/mark-printed",
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function() {
                console.log('Job ' + jobId + ' marked as printed');
            },
            error: function(xhr) {
                console.error('Failed to mark job as printed:', xhr);
            }
        });
    }

    // Mark job as failed
    function markJobAsFailed(jobId, errorMessage) {
        $.ajax({
            url: "{{ url('admin/pos/print/job') }}/" + jobId + "/mark-failed",
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: { error_message: errorMessage },
            error: function(xhr) {
                console.error('Failed to mark job as failed:', xhr);
            }
        });
    }

    // Initialize when DOM is ready
    $(document).ready(function() {
        startPrintQueuePolling();
    });
})();
</script>
