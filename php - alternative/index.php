<?php
$url = (isset($_GET['url'])) ? $_GET['url']:'home';

$url = array_filter(explode('/',$url));
if ($url[0] == "index") $url[0] = "home";
$file = $url[0].'.php';

if(is_file($file)){
	include $file;
}else{
	include '404.html';
}
?>