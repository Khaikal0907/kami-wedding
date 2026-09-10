<?php
declare(strict_types=1);
function e(?string $value): string { return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8'); }
function supabaseRequest(string $method,string $path,?array $body=null,?string $accessToken=null):array{
 $ch=curl_init(SUPABASE_URL.$path); $headers=['apikey: '.RUNTIME_SUPABASE_KEY,'Content-Type: application/json']; if($accessToken)$headers[]='Authorization: Bearer '.$accessToken;
 curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_CUSTOMREQUEST=>$method,CURLOPT_HTTPHEADER=>$headers,CURLOPT_TIMEOUT=>20]); if($body!==null)curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($body,JSON_UNESCAPED_SLASHES));
 $raw=curl_exec($ch);$error=curl_error($ch);$status=(int)curl_getinfo($ch,CURLINFO_HTTP_CODE);unset($ch);if($error)return['status'=>0,'data'=>null,'error'=>$error];$data=json_decode($raw?:'null',true);return['status'=>$status,'data'=>$data,'error'=>$status>=400?($data['message']??$data['error_description']??'Request failed'):null];
}
function requireLogin():void{if(empty($_SESSION['access_token'])||empty($_SESSION['username'])){header('Location: index.php');exit;}}
function currentUser():string{return $_SESSION['username']??'';} function isKhaikal():bool{return currentUser()==='khaikal';} function authToken():string{return $_SESSION['access_token']??'';}
function inviteLink(string $name,string $base):string{return rtrim($base?:BASE_INVITE,'/').'/?to='.rawurlencode(trim($name));}
function whatsappUrl(string $phone,string $message):string{$n=preg_replace('/\D+/','',$phone);if(str_starts_with($n,'0'))$n='62'.substr($n,1);elseif(str_starts_with($n,'8'))$n='62'.$n;return'https://wa.me/'.$n.'?text='.rawurlencode($message);}
function csrf():string{if(empty($_SESSION['csrf']))$_SESSION['csrf']=bin2hex(random_bytes(24));return$_SESSION['csrf'];}
function checkCsrf():void{if(!hash_equals($_SESSION['csrf']??'',$_POST['csrf']??'')){http_response_code(419);exit('Invalid CSRF token');}}
