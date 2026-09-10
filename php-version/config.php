<?php
declare(strict_types=1);

const SUPABASE_URL = 'https://hcyziqzfbplowikqpdiv.supabase.co';
const SUPABASE_KEY = '';
const SHARED_PASSWORD = '';
const BASE_INVITE = 'https://waktutemu.id/java-heritage/';

const USERS = [
    'khaikal' => ['email'=>'khaikal@kami-wedding.local','label'=>'Khaikal'],
    'mimi' => ['email'=>'mimi@kami-wedding.local','label'=>'Mimi'],
];

const DEFAULT_TEMPLATE = <<<'TXT'
Kepada Yth.
Bapak/Ibu/Saudara/i
{{nama}}
_______

Assalamualaikum Warahmatullahi Wabarakatuh

Tanpa mengurangi rasa hormat, perkenankan kami mengundang Bapak/Ibu/Saudara/i, teman sekaligus sahabat, untuk menghadiri acara pernikahan kami.

Berikut link undangan kami, untuk info lengkap dari acara, bisa kunjungi :

{{link}}

Merupakan suatu kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan untuk hadir dan memberikan doa restu.

Wassalamualaikum Warahmatullahi Wabarakatuh

Terima kasih banyak atas perhatian dan doanya.

Khaikal & Mimi
TXT;

// Set these on your PHP hosting as environment variables:
// KAMI_SUPABASE_KEY and KAMI_SHARED_PASSWORD
if (($key = getenv('KAMI_SUPABASE_KEY')) !== false && $key !== '') define('RUNTIME_SUPABASE_KEY', $key); else define('RUNTIME_SUPABASE_KEY', SUPABASE_KEY);
if (($password = getenv('KAMI_SHARED_PASSWORD')) !== false && $password !== '') define('RUNTIME_SHARED_PASSWORD', $password); else define('RUNTIME_SHARED_PASSWORD', SHARED_PASSWORD);
