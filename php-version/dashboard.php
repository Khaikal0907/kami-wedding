<?php
declare(strict_types=1);

ob_start();
require __DIR__ . '/dashboard_base.php';
$html = ob_get_clean();

$script = <<<'HTML'
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAllBtn = document.getElementById('selectAllBtn');
    const blastBtn = document.getElementById('blastBtn');
    if (!selectAllBtn || !blastBtn || document.getElementById('deleteSelectedBtn')) return;

    const btn = document.createElement('button');
    btn.type = 'button';
    btn.id = 'deleteSelectedBtn';
    btn.className = 'btn btn-outline-danger';
    btn.innerHTML = '<i class="bi bi-trash3 me-1"></i>Hapus Terpilih <span id="deleteSelectedCount" class="badge text-bg-danger ms-1">0</span>';
    blastBtn.parentElement.insertBefore(btn, blastBtn);

    function updateDeleteButton() {
        const checked = document.querySelectorAll('#guestTable .guest-check:checked');
        const n = checked.length;
        btn.disabled = n === 0;
        const count = document.getElementById('deleteSelectedCount');
        if (count) count.textContent = n;
    }

    document.addEventListener('change', function (e) {
        if (e.target.matches('#guestTable .guest-check, #headerCheck')) updateDeleteButton();
    });

    btn.addEventListener('click', async function () {
        const checked = [...document.querySelectorAll('#guestTable .guest-check:checked')];
        if (!checked.length) return;
        if (!confirm('Hapus ' + checked.length + ' data tamu yang dipilih?')) return;

        const csrf = document.querySelector('input[name="csrf"]')?.value || '';
        if (!csrf) {
            alert('CSRF token tidak ditemukan. Silakan refresh halaman.');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Menghapus...';

        let failed = 0;
        for (const checkbox of checked) {
            const fd = new FormData();
            fd.append('csrf', csrf);
            fd.append('action', 'delete_guest');
            fd.append('id', checkbox.value);
            try {
                const response = await fetch('dashboard.php?page=guests', {
                    method: 'POST',
                    body: fd,
                    credentials: 'same-origin',
                    redirect: 'manual'
                });
                if (!(response.type === 'opaqueredirect' || response.status === 0 || response.ok || response.status === 302 || response.status === 303)) failed++;
            } catch (_) {
                failed++;
            }
        }

        if (failed) alert('Ada ' + failed + ' data yang gagal dihapus.');
        window.location.href = 'dashboard.php?page=guests';
    });

    updateDeleteButton();
});
</script>
HTML;

if (strpos($html, '</body>') !== false) {
    $html = str_replace('</body>', $script . '</body>', $html);
} else {
    $html .= $script;
}

echo $html;
