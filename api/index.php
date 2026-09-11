<?php
declare(strict_types=1);
// Vercel PHP runtime entrypoint.
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$path = rtrim($path, '/') ?: '/';

if ($path === '/' || $path === '/index.php') {
    require __DIR__ . '/../index.php';
    exit;
}
if ($path === '/dashboard.php') {
    ob_start();
    require __DIR__ . '/../php-version/dashboard.php';
    $html = ob_get_clean();
    $copyScript = <<<'HTML'
<script>
document.addEventListener('click', async function(e) {
    const btn = e.target.closest('.copy-message-btn');
    if (!btn) return;
    e.preventDefault();
    e.stopImmediatePropagation();
    const message = btn.getAttribute('data-message') || '';
    let copied = false;
    try {
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(message);
            copied = true;
        }
    } catch (_) {}
    if (!copied) {
        const ta = document.createElement('textarea');
        ta.value = message;
        ta.setAttribute('readonly', '');
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.select();
        try { copied = document.execCommand('copy'); } catch (_) {}
        ta.remove();
    }
    const icon = btn.querySelector('i');
    const oldIcon = icon ? icon.className : '';
    const oldTitle = btn.title;
    if (copied) {
        if (icon) icon.className = 'bi bi-check2';
        btn.title = 'Tersalin!';
        setTimeout(() => {
            if (icon) icon.className = oldIcon;
            btn.title = oldTitle;
        }, 1200);
    }
}, true);
</script>
HTML;
    echo str_replace('</body>', $copyScript . '</body>', $html);
    exit;
}
if ($path === '/logout.php') {
    require __DIR__ . '/../php-version/logout.php';
    exit;
}
http_response_code(404);
echo 'Not Found';
