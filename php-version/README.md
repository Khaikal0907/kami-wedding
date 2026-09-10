# KaMi Wedding PHP + Bootstrap

Versi PHP server-side dengan Bootstrap 5 dan Bootstrap Icons.

File aplikasi: `index.php`, `dashboard.php`, `logout.php`, `config.php`, `helpers.php`.

PHP 8.1+ dan extension cURL diperlukan.

Set environment variable di hosting PHP:
- `KAMI_SUPABASE_KEY`
- `KAMI_SHARED_PASSWORD`

Versi ini sengaja berada di branch `php-version` agar deployment Vercel yang sekarang di branch `main` tetap aman. Vercel yang sekarang tidak menjalankan PHP server-side seperti hosting PHP biasa.
