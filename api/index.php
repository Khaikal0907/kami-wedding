<?php
declare(strict_types=1);
$path = rtrim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/', '/') ?: '/';

if ($path === '/favicon.svg') {
    $file = __DIR__ . '/../favicon.svg';
    if (is_file($file)) { header('Content-Type: image/svg+xml'); header('Cache-Control: public, max-age=86400'); readfile($file); exit; }
    http_response_code(404); exit;
}
if ($path === '/' || $path === '/index.php') { require __DIR__ . '/../index.php'; exit; }
if ($path === '/logout.php') { require __DIR__ . '/../php-version/logout.php'; exit; }

if ($path === '/dashboard.php') {
    ob_start();
    require __DIR__ . '/../php-version/dashboard.php';
    $html = ob_get_clean();

    $style = <<<'CSS'
<style>
.mobile-menu-toggle,.mobile-menu-backdrop{display:none}
@media(max-width:768px){
 body{overflow-x:hidden}
 .sidebar{position:fixed!important;z-index:1050;left:0;top:0;width:250px!important;height:100vh;min-height:100vh;transform:translateX(-100%);transition:transform .22s ease;box-shadow:10px 0 30px rgba(0,0,0,.18);padding:18px 14px!important}
 .sidebar .brand{display:flex!important;align-items:center!important;gap:10px!important;padding:10px 8px!important;margin-bottom:24px!important;white-space:nowrap}
 .sidebar .brand i{display:block!important;width:22px!important;min-width:22px!important;margin:0!important;text-align:center!important;line-height:1}
 .sidebar .brand-text,.sidebar .nav-text,.sidebar .logout-text{display:inline!important}
 .sidebar nav{width:100%}
 .sidebar .nav-link{display:flex!important;align-items:center!important;justify-content:flex-start!important;gap:10px!important;padding:11px 12px!important;min-height:44px;margin:4px 0!important;white-space:nowrap}
 .sidebar .nav-link i{display:block!important;width:22px!important;min-width:22px!important;margin:0!important;text-align:center!important;line-height:1}
 .sidebar .nav-link span{display:block!important;line-height:1.2!important}
 .sidebar .logout-link{display:flex!important;align-items:center!important;justify-content:flex-start!important;gap:10px!important;padding:11px 12px!important;white-space:nowrap}
 .sidebar .logout-link i{width:22px!important;min-width:22px!important;text-align:center!important}
 body.mobile-menu-open .sidebar{transform:translateX(0)}
 .mobile-menu-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.42);z-index:1040}
 body.mobile-menu-open .mobile-menu-backdrop{display:block}
 .mobile-menu-toggle{display:inline-flex;align-items:center;justify-content:center;width:42px;height:42px;border:1px solid #e5ddd2;border-radius:12px;background:#fff;color:#514332;font-size:21px;flex:0 0 auto}
 .content{width:100%!important;margin:0!important}
 .container-fluid{padding:16px 14px!important}
 .container-fluid>.d-flex.justify-content-between.align-items-center.mb-4{display:none!important}
 .mobile-header{display:flex;align-items:center;gap:10px;margin-bottom:16px}
 .mobile-header .mobile-title{font-weight:800;font-size:18px;line-height:1.1}
 .mobile-header .mobile-subtitle{font-size:12px;color:#7c7369}
 #search{max-width:none!important;min-width:0}
 #selectAllBtn{white-space:nowrap}
 #guestTable thead{display:none}
 #guestTable tbody{display:block;padding:8px}
 #guestTable tbody tr{display:grid;grid-template-columns:30px 1fr;gap:0 8px;margin-bottom:10px;padding:12px 10px;border:1px solid #e5ddd2;border-radius:15px;background:#fff;box-shadow:0 2px 8px rgba(60,45,30,.04)}
 #guestTable tbody tr:last-child{margin-bottom:0}
 #guestTable tbody td{border:0!important;padding:2px 0!important;min-width:0}
 #guestTable tbody td:nth-child(1){grid-column:1;grid-row:1 / span 4;padding-top:5px!important}
 #guestTable tbody td:nth-child(2){grid-column:2;grid-row:1;font-size:15px}
 #guestTable tbody td:nth-child(3){grid-column:2;grid-row:2;color:#665e55;font-size:13px}
 #guestTable tbody td:nth-child(4){grid-column:2;grid-row:3}
 #guestTable tbody td:nth-child(5){grid-column:2;grid-row:4;margin-top:7px}
 #guestTable tbody td:nth-child(5)>div{justify-content:flex-start!important}
 #guestTable tbody td:nth-child(5) .icon-btn{width:40px;height:40px}
 #guestTable tbody tr>td:nth-child(3)::before{content:"Phone: ";font-weight:600;color:#8a8177}
 #guestTable tbody tr>td:nth-child(4)::before{content:"Status: ";font-weight:600;color:#8a8177}
 #guestTable tbody tr>td:nth-child(5)::before{content:"Aksi";display:block;font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:#9a9187;margin-bottom:4px;font-weight:700}
 #guestTable tbody tr>td[colspan]{grid-column:1 / -1;text-align:center}
 .d-flex.flex-column.flex-md-row.justify-content-between.gap-2.mb-3{gap:8px!important}
 .d-flex.flex-column.flex-md-row.justify-content-between.gap-2.mb-3>div:first-child{width:100%}
 .d-flex.flex-column.flex-md-row.justify-content-between.gap-2.mb-3>div:last-child{display:grid!important;grid-template-columns:1fr 1fr;width:100%}
 .d-flex.flex-column.flex-md-row.justify-content-between.gap-2.mb-3>div:last-child .btn{width:100%}
}
@media(min-width:769px){.mobile-header{display:none!important}.d-flex.justify-content-end.gap-1>form:first-of-type{order:-1}}
</style>
CSS;
    $html = str_replace('</head>', '<link rel="icon" type="image/svg+xml" href="/favicon.svg">' . $style . '</head>', $html);

    $mobileHeader = <<<'HTML'
<div class="mobile-menu-backdrop" id="mobileMenuBackdrop"></div>
<div class="mobile-header px-3 pt-3">
 <button type="button" class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Buka menu"><i class="bi bi-list"></i></button>
 <div><div class="mobile-title">KaMi Wedding</div><div class="mobile-subtitle">Invitation Admin</div></div>
</div>
HTML;
    $html = str_replace('<body>', '<body>' . $mobileHeader, $html);

    $script = <<<'HTML'
<script>
document.addEventListener('click', async function(e){
 const menu=e.target.closest('#mobileMenuToggle');
 const back=e.target.closest('#mobileMenuBackdrop');
 if(menu){document.body.classList.toggle('mobile-menu-open');const i=menu.querySelector('i');if(i)i.className=document.body.classList.contains('mobile-menu-open')?'bi bi-x-lg':'bi bi-list';return;}
 if(back){document.body.classList.remove('mobile-menu-open');const i=document.querySelector('#mobileMenuToggle i');if(i)i.className='bi bi-list';return;}
 const btn=e.target.closest('.copy-message-btn');if(!btn)return;e.preventDefault();e.stopImmediatePropagation();
 const message=btn.getAttribute('data-message')||'';let copied=false;
 try{if(navigator.clipboard&&window.isSecureContext){await navigator.clipboard.writeText(message);copied=true;}}catch(_){ }
 if(!copied){const ta=document.createElement('textarea');ta.value=message;ta.setAttribute('readonly','');ta.style.position='fixed';ta.style.opacity='0';document.body.appendChild(ta);ta.select();try{copied=document.execCommand('copy');}catch(_){ }ta.remove();}
 const icon=btn.querySelector('i'),old=icon?icon.className:'',title=btn.title;if(copied){if(icon)icon.className='bi bi-check2';btn.title='Tersalin!';setTimeout(()=>{if(icon)icon.className=old;btn.title=title;},1200);}
},true);
</script>
HTML;
    echo str_replace('</body>', $script . '</body>', $html);
    exit;
}
http_response_code(404);
echo 'Not Found';
