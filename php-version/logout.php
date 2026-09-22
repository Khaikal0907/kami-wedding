<?php
session_start();require __DIR__.'/../config.php';require __DIR__.'/../helpers.php';
$_SESSION=[];
if(ini_get('session.use_cookies')){$p=session_get_cookie_params();setcookie(session_name(),' ',time()-42000,$p['path'],$p['domain'],$p['secure'],$p['httponly']);}
clearAuthCookies();
session_destroy();
header('Location: index.php');exit;
