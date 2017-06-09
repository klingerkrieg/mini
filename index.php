<?php
/**
* Framework simples MVC
* Autor:Alan Klinger 05/06/2017
*/
require 'sys/config.php';
require 'sys/rb.php';
require 'sys/rb-mod.php';
require 'sys/util.php';

R::setup("mysql:host=$host;dbname=$dbname", $user,$pass);

#caso seja para congelar o banco
if ($freezeDb){
	R::freeze();
}

$server_url = "http://".$_SERVER['SERVER_NAME'] . explode("index.php",$_SERVER['SCRIPT_NAME'])[0];

/**
*Retorna o endereco da url ate a pasta principal do projeto
*/
function serverUrl(){
	global $server_url;
	return $server_url;
}

/**
* Funcao para pegar um valor de um array de forma segura
* sem dar erro caso nao exista
* $arr $key
*/
function _v($arr,$val){
	if (isset($arr[$val])){
		return $arr[$val];	
	} else {
		return "";	
	}
}

/**
* Redireciona para outra pagina
*/
function redirect($url){
	Header("Location: $url");
}

/**
*
*/
function view($name, $data=array()){
	global $server_url;
	foreach($data as $k=>$var){
		$$k = $var;
	}
	include './views/'.$name.'.php';
}

function model($name){
	include './models/'.$name.'.php';
	return new $name();
}


if (_v($_SERVER,'PATH_INFO') != ""){
	$parts = explode("/", trim($_SERVER['PATH_INFO'],"/") );
} else {
	$parts = array();
}


//carrega a classe controle
if (_v($parts,0) != ""){
	$class = ucwords(strtolower($parts[0]));
} else {
	$class = "Principal";
}
	include './controllers/'.$class.'.php';


//carrega o metodo
if (_v($parts,1) != ""){
	$metodo = $parts[1];		
} else {
	$metodo = "index";
}

$params = array_slice($parts,2);

$obj = new $class();
//$obj->$metodo();
call_user_func_array(array($obj, $metodo), $params);

R::close();
