/**
 * SiTiTi — Custom JavaScript
 * assets/js/app.js
 */

document.addEventListener('DOMContentLoaded', function () {

    /* ── Auto-dismiss alert setelah 5 detik ── */
    document.querySelectorAll('.alert.alert-dismissible').forEach(function (alert) {
        setTimeout(function () {
            var bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            bsAlert.close();
        }, 5000);
    });

    /* ── Upload area drag & drop ── */
    var uploadArea = document.getElementById('uploadArea');
    var fileInput  = document.getElementById('file_proposal');

    if (uploadArea && fileInput) {
        uploadArea.addEventListener('click', function () { fileInput.click(); });

        uploadArea.addEventListener('dragover', function (e) {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });
        uploadArea.addEventListener('dragleave', function () {
            uploadArea.classList.remove('dragover');
        });
        uploadArea.addEventListener('drop', function (e) {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                updateFileLabel(e.dataTransfer.files[0]);
            }
        });
        fileInput.addEventListener('change', function () {
            if (this.files.length) updateFileLabel(this.files[0]);
        });

        function updateFileLabel(file) {
            var label = uploadArea.querySelector('.upload-label');
            if (label) {
                label.innerHTML = '<i class="bi bi-file-earmark-pdf text-danger fs-1 d-block mb-2"></i>' +
                    '<strong>' + escapeHtml(file.name) + '</strong>' +
                    '<br><small class="text-muted">' + formatBytes(file.size) + '</small>';
            }
        }
    }

    /* ── Konfirmasi hapus/tolak ── */
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            if (!confirm(this.dataset.confirm)) {
                e.preventDefault();
            }
        });
    });

    /* ── Aktifkan tooltip Bootstrap ── */
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
        new bootstrap.Tooltip(el);
    });

    /* ── Aktifkan popover Bootstrap ── */
    document.querySelectorAll('[data-bs-toggle="popover"]').forEach(function (el) {
        new bootstrap.Popover(el);
    });

    /* ── Preview karakter textarea ── */
    document.querySelectorAll('textarea[data-max]').forEach(function (ta) {
        var max     = parseInt(ta.dataset.max);
        var counter = document.getElementById(ta.id + '_counter');
        function updateCounter() {
            var left = max - ta.value.length;
            if (counter) {
                counter.textContent = left + ' karakter tersisa';
                counter.className   = left < 50 ? 'form-text text-danger' : 'form-text text-muted';
            }
        }
        ta.addEventListener('input', updateCounter);
        updateCounter();
    });

    /* ── Sidebar toggle (mobile) ── */
    var sidebarToggle = document.getElementById('sidebarToggle');
    var sidebar       = document.getElementById('sidebar');
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('show');
        });
    }

    /* ── Helpers ── */
    function escapeHtml(text) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }
    function formatBytes(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(1) + ' MB';
    }
});
