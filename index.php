<?php
/**
* Framework simples MVC
* Autor:Alan Klinger 05/06/2017
*/
require 'sys/config.php';
require 'sys/rb.php';
require 'sys/RbLib.php';
require 'sys/util.php';
require 'sys/Pagination.php';

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

function _url($url){

	$arr = explode("/",$url);

	$urlBack = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	
	if (strstr($url,".")){
		if (!file_exists($url)){
			Header("Location: ../errors/url.php?url=$url&back=$urlBack");
		}
	} else
	if (file_exists('./controllers/'.$arr[0].'.php')){
		include_once './controllers/'.$arr[0].'.php';
		$methods = get_class_methods($arr[0]);
		if ( !in_array($arr[1],$methods) ){
			Header("Location: ../errors/url.php?url=$url&back=$urlBack");
		}
	} else {
		Header("Location: ../errors/url.php?url=$url&back=$urlBack");
	}




	$local = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
	$local = substr($local,0,strpos($local,"index.php")) . $url;
	return "http://".$local;
}

/**
* Redireciona para outra pagina
*/
function redirect($url){

	$local = _url($url);
	
	Header("Location: $local");
}

/**
* Chama a view através do Twig
*/
function render($name){
	global $twig;

	
	$data = [];
	if ( func_get_args() > 1 ){
		$data = func_get_arg(1);
	}



	if (file_exists("./views/$name")){
		print $twig->render($name,$data);
	} else
	if (file_exists("./views/$name.html")){
		print $twig->render($name.".html",$data);
	} else 
	if (file_exists("./views/$name.twig")){
		print $twig->render($name.".twig",$data);
	}
}

function model($name){
	include './models/'.$name.'.php';
}

function all_models(){
	global $createTables;
	$model_files = scandir("./models/");

	foreach($model_files as $file){
		$ff = explode('.', $file);
		if(
			strtolower($ff[0]) !== strtolower(__CLASS__) &&
			strtolower($ff[1]) === 'php') {
			require_once("./models/".$file);
			
			
			if ($createTables)
				$ff[0]::createTable(); 
		}
	}
}


#Twig
require_once './sys/Twig/Autoloader.php';
Twig_Autoloader::register();
$loader = new Twig_Loader_Filesystem('./views');
$twig = new Twig_Environment($loader, [
]);


$function = new Twig_SimpleFunction('url', function ($url) {
    return _url($url);
});
$twig->addFunction($function);



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






#carrega os models
all_models();


#carrega o controller
$controller = new $class();


$params_to_controller = array();

#Converte o request para objetos
$request = $_REQUEST;
$r = new ReflectionMethod( $controller, $metodo );
$params = $r->getParameters();
if ( !empty( $params ) ) {
	$param_names = array();
	foreach ( $params as $param ) {
		$obj = null;
		$paramName = $param->getName();
		//Para parametros primitivos
		if ($param->getClass() == null){

			foreach($request as $key=>$req ){
				if ($key == $paramName){
					if ($_REQUEST[$key] == ""){
						$obj = null;
					} else {
						$obj = $_REQUEST[$key];
					}
					unset($request[$key]);
				}
			}
			

		} else {
			//Para parametros não primitivos
			$className = $param->getClass()->getName();
						
			foreach($request as $key=>$req ){
				if (strstr($key,$paramName)){
					if ($obj == null){
						$obj = new $className();
					}

					$attribute = str_replace($paramName."_","",$key);
					$obj->$attribute = $_REQUEST[$key];
					unset($request[$key]);
				}
			}
		}

		array_push($params_to_controller, $obj);
	}
}


if ( count($parts) > 2 ){

	for ($i = 0; $i < count($params_to_controller); $i++){
		if ($params_to_controller[$i] == null ){
			$params_to_controller[$i] = $parts[2+$i];
		}
	}

}


//$obj->$metodo();
call_user_func_array(array($controller, $metodo), $params_to_controller);

R::close();
