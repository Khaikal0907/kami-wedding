<?php
declare(strict_types=1);

function e(?string $value): string { return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8'); }

function supabaseRequest(string $method,string $path,?array $body=null,?string $accessToken=null):array{
 $ch=curl_init(SUPABASE_URL.$path); $headers=['apikey: '.RUNTIME_SUPABASE_KEY,'Content-Type: application/json']; if($accessToken)$headers[]='Authorization: Bearer '.$accessToken;
 curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_CUSTOMREQUEST=>$method,CURLOPT_HTTPHEADER=>$headers,CURLOPT_TIMEOUT=>20]); if($body!==null)curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($body,JSON_UNESCAPED_SLASHES));
 $raw=curl_exec($ch);$error=curl_error($ch);$status=(int)curl_getinfo($ch,CURLINFO_HTTP_CODE);unset($ch);if($error)return['status'=>0,'data'=>null,'error'=>$error];$data=json_decode($raw?:'null',true);return['status'=>$status,'data'=>$data,'error'=>$status>=400?($data['message']??$data['error_description']??'Request failed'):null];
}

function authCookieOptions(int $expires):array{
 return ['expires'=>$expires,'path'=>'/','secure'=>(!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off'),'httponly'=>true,'samesite'=>'Lax'];
}

function setAuthCookies(string $accessToken,string $refreshToken=''):void{
 setcookie('kami_access_token',$accessToken,authCookieOptions(time()+3600));
 if($refreshToken!=='') setcookie('kami_refresh_token',$refreshToken,authCookieOptions(time()+60*60*24*30));
}

function clearAuthCookies():void{
 setcookie('kami_access_token','',authCookieOptions(time()-3600));
 setcookie('kami_refresh_token','',authCookieOptions(time()-3600));
}

function jwtPayload(string $token):array{
 $parts=explode('.',$token); if(count($parts)!==3)return[];
 $raw=strtr($parts[1],'-_','+/'); $raw.=str_repeat('=',(4-strlen($raw)%4)%4); $data=json_decode(base64_decode($raw)?:'',true); return is_array($data)?$data:[];
}

function restoreAuthFromCookies():bool{
 if(!empty($_SESSION['access_token'])&&!empty($_SESSION['username'])) return true;
 $access=$_COOKIE['kami_access_token']??''; $refresh=$_COOKIE['kami_refresh_token']??'';
 if($access!==''){
  $payload=jwtPayload($access); $email=strtolower((string)($payload['email']??'')); $exp=(int)($payload['exp']??0);
  foreach(USERS as $username=>$user) if(strtolower($user['email'])===$email){
   if($exp>time()+60){$_SESSION['access_token']=$access;$_SESSION['user_id']=(string)($payload['sub']??'');$_SESSION['username']=$username;return true;}
   break;
  }
 }
 if($refresh!==''){
  $r=supabaseRequest('POST','/auth/v1/token?grant_type=refresh_token',['refresh_token'=>$refresh]);
  if(!$r['error']&&!empty($r['data']['access_token'])){
   $newAccess=$r['data']['access_token']; $newRefresh=$r['data']['refresh_token']??$refresh; $payload=jwtPayload($newAccess); $email=strtolower((string)($payload['email']??($r['data']['user']['email']??'')));
   foreach(USERS as $username=>$user) if(strtolower($user['email'])===$email){
    $_SESSION['access_token']=$newAccess;$_SESSION['user_id']=(string)($payload['sub']??($r['data']['user']['id']??''));$_SESSION['username']=$username;setAuthCookies($newAccess,$newRefresh);return true;
   }
  }
 }
 return false;
}

function requireLogin():void{if(!restoreAuthFromCookies()){clearAuthCookies();header('Location: index.php');exit;}}
function currentUser():string{return $_SESSION['username']??'';}
function isKhaikal():bool{return currentUser()==='khaikal';}
function authToken():string{return $_SESSION['access_token']??'';}
function inviteLink(string $name,string $base):string{return rtrim($base?:BASE_INVITE,'/').'/?to='.rawurlencode(trim($name));}
function whatsappUrl(string $phone,string $message):string{$n=preg_replace('/\D+/','',$phone);if(str_starts_with($n,'0'))$n='62'.substr($n,1);elseif(str_starts_with($n,'8'))$n='62'.$n;return'https://wa.me/'.$n.'?text='.rawurlencode($message);}
function csrf():string{if(empty($_SESSION['csrf']))$_SESSION['csrf']=bin2hex(random_bytes(24));return$_SESSION['csrf'];}
function checkCsrf():void{if(!hash_equals($_SESSION['csrf']??'',$_POST['csrf']??'')){http_response_code(419);exit('Invalid CSRF token');}}
