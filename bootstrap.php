<?php
session_name('songvault');
session_set_cookie_params(['path'=>'/','secure'=>true,'httponly'=>true,'samesite'=>'Lax']);
session_start();
header('Content-Security-Policy: default-src \'self\'; object-src \'none\'; base-uri \'self\'; frame-ancestors \'none\'');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store');
require dirname(__DIR__,2).'/vendor/autoload.php';
$config = [
    'host' => 'localhost',
    'database' => 'db2439648',
    'username' => '2439648',
    'password' => '39Kathmandu@'
];
$db=new PDO('mysql:host='.$config['host'].';dbname='.$config['database'].';charset=utf8mb4',$config['username'],$config['password'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]);
$twig=new Twig\Environment(new Twig\Loader\FilesystemLoader(dirname(__DIR__).'/templates'),['autoescape'=>'html']);
function input_filter(array $source,string $key): string {return is_string($source[$key]??null)?trim($source[$key]):'';}
function token(): string {return $_SESSION['csrf']??=bin2hex(random_bytes(32));}
function csrf(): void {if(!hash_equals(token(),input_filter($_POST,'csrf'))){http_response_code(403);exit('Invalid request');}}
function logged(): void {if(empty($_SESSION['uid'])){header('Location: login.php');exit;}}
function render(string $page,array $data=[]): void {global $twig;echo $twig->render($page,$data+['csrf'=>token(),'user'=>$_SESSION['username']??null]);}
function redirect(string $url): never {header('Location: '.$url);exit;}
function song_id(): int {$id=filter_var($_GET['id']??null,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);if(!$id){http_response_code(400);exit('Invalid ID');}return $id;}
function get_song(int $id): array {global $db;$s=$db->prepare('SELECT * FROM songs WHERE id=?');$s->execute([$id]);$r=$s->fetch();if(!$r){http_response_code(404);exit('Song not found');}return $r;}
function search_songs(array $source): array {global $db;$where=[];$params=[];foreach(['title','artist','album','genre','release_year'] as $k){$v=input_filter($source,$k);if($v==='')continue;if(in_array($k,['genre','release_year'],true)){$where[]="$k = ?";$params[]=$v;}else{$where[]="$k LIKE ?";$params[]='%'.str_replace(['!','%','_'],['!!','!%','!_'],$v).'%';$where[count($where)-1].=" ESCAPE '!'";}}$sort=['title'=>'title','artist'=>'artist','year'=>'release_year DESC','duration'=>'duration'][input_filter($source,'sort')]??'title';$s=$db->prepare('SELECT id,title,artist,album,genre,release_year,duration FROM songs'.($where?' WHERE '.implode(' AND ',$where):'').' ORDER BY '.$sort.', id');$s->execute($params);return $s->fetchAll();}
