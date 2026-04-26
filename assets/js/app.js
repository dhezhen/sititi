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

    /* ── SweetAlert Global Config ── */
    window.SwalToast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    /* ── Handle SweetAlert Confirmation ── */
    document.body.addEventListener('click', function (e) {
        let confirmBtn = e.target.closest('[data-confirm]');
        if (confirmBtn) {
            e.preventDefault();
            const message = confirmBtn.dataset.confirm || "Apakah Anda yakin?";
            
            // Check if Swal is available
            if (typeof Swal !== 'undefined') {
                const type = confirmBtn.dataset.type || "warning";
                const confirmText = confirmBtn.dataset.confirmText || "Ya, Lanjutkan";

                Swal.fire({
                    title: 'Konfirmasi',
                    text: message,
                    icon: type,
                    showCancelButton: true,
                    confirmButtonColor: '#4f46e5',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: confirmText,
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        executeConfirmAction(confirmBtn);
                    }
                });
            } else {
                // Fallback to standard confirm if Swal is not loaded
                if (confirm(message)) {
                    executeConfirmAction(confirmBtn);
                }
            }
        }
    });

    function jsLog(msg) {
        fetch('scratch/js_log.php?msg=' + encodeURIComponent(msg));
    }

    function executeConfirmAction(btn) {
        if (btn.tagName === 'A') {
            window.location.href = btn.href;
        } else if (btn.closest('form')) {
            // Hapus data-confirm agar tidak loop, lalu klik ulang button-nya
            // Ini cara paling aman agar nilai button (name/value) tetap terkirim secara native
            delete btn.dataset.confirm;
            btn.click();
        }
    }






    /* ── Aktifkan tooltip Bootstrap ── */
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
        new bootstrap.Tooltip(el);
    });

    /* ── Aktifkan popover Bootstrap ── */
    document.querySelectorAll('[data-bs-toggle="popover"]').forEach(function (el) {
        new bootstrap.Popover(el);
    });

    /* ── Quill Rich Text Editor ── */
    document.querySelectorAll('.editor').forEach(function (el) {
        var quill = new Quill(el, {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['clean']
                ]
            }
        });

        // Sinkronisasi dengan hidden input jika ada dalam form
        var form = el.closest('form');
        if (form) {
            form.addEventListener('submit', function () {
                var inputId = el.dataset.input;
                if (inputId) {
                    var input = document.getElementById(inputId);
                    if (input) input.value = quill.root.innerHTML;
                }
            });
        }
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
