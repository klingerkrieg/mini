<?php
session_start();
/**
* Framework simples MVC
* Autor:Alan Klinger 05/06/2017
*/
require 'sys/config.php';
require 'sys/rb.php';
require 'sys/RbLib.php';
require 'sys/util.php';
require 'sys/Pagination.php';
require 'sys/validate.php';
require 'sys/messages.php';


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
		if (!file_exists($url) && !file_exists(".".$url)){
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

	if (!strstr($url,"http")){
		$local = _url($url);
	} else {
		$local = $url;
	}
	
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


$function = new Twig_SimpleFunction('formMessage', function () {
	
	if (isset($_SESSION['_flash_success'])){
		print "<div class='flash_success'>{$_SESSION['_flash_success']}</div>";
		unset($_SESSION['_flash_success']);
	} else
	if (isset($_SESSION['_flash_error'])){
		print "<div class='flash_error'>{$_SESSION['_flash_error']}</div>";
		unset($_SESSION['_flash_error']);
	}

});
$twig->addFunction($function);


$function = new Twig_SimpleFunction('input', function ($name, $value, $props = []) {

	


	if (isset($props['type'])){
		$type = $props['type'];
	} else {
		$type = "text";
	}

	if (isset($props['class'])){
		$class = $props['class'];
	} else {
		$class = "";
	}

	$html = "";

	$lab = false;
	if (isset($props['label'])){
		$html .= "<label>". $props['label'];
		$lab = true;
	}


	if ($value != null){
		$vProp = substr($name,strpos($name,".")+1);
		$val = $value->$vProp;
	} else {
		$val = "";
	}


	if (isset($_SESSION['_errors']) && isset($_SESSION['_errors'][$name])){
		global $_MESSAGES;
		$errmsg = $_SESSION['_errors'][$name]['error'];
		$errorSpan = "<span class='error'>{$_MESSAGES[$errmsg]}</span>";
		unset($_SESSION['_errors'][$name]);
	}

	if (isset($_SESSION['_has_errors']) && $_SESSION['_has_errors'] == true){
		if (isset($_SESSION['_request'][str_replace(".","_",$name)]))
			$val = $_SESSION['_request'][str_replace(".","_",$name)];
	}
	

	if ($type == "radio"){

		foreach($props['values'] as $key=>$opt){

			$checked = "";
			if ($val === $key){
				$checked = "checked";
			}

			$html .= "<label>";
			$html .= "<input type='radio' $checked name='$name' value='$key' />";
			$html .= " $opt</label>";
		}


	} else
	if ($type == "select"){

		$html .= "<select name='$name' >";

		foreach($props['values'] as $key=>$opt){

			$selected = "";
			if ((string)$val === (string)$key){
				$selected = "selected";
			}
			#sinal de - é o mesmo que vazio
			if ($key === "-"){
				$key = "";
			} else {
				$key = (int)$key;
			}

			$html .= "<option value='$key' $selected>$opt</option>";
		}

		$html .= "</select>";


	} else
	if ($type == "checkbox"){

		if (isset($props['value'])){
			$inputValue = $props['value'];
		} else {
			$inputValue = 1;
		}

		$checked = "";
		if ($val == $inputValue){
			$checked = "checked='checked'";
		}

		$html .= "<input type='$type' $checked name='$name' value='$inputValue' />";

	} else {
		$html .= "<input type='$type' name='$name' value='$val' />";
	}

	

	if ($lab){
		$html .= "</label>";
	}

	if (isset($errorSpan))
		$html .= $errorSpan;
	

	print $html;

});
$twig->addFunction($function);



function create_list_link($btn, $id){
	#provisorio
	$link = $btn['link'];
	$link = str_replace("{id}","",$link);
	$url = _url($link);
	$link = $url . $id;
	return "<span class='col'><a href='$link'> {$btn['text']} </a></span>";
}


$function = new Twig_SimpleFunction('list', function ($list, $headers = null) {

	
	//recupera os links e botoes dos parametros
	$args = func_get_args();
	$c = count($args);
	$btns = [];
	for($i = 2; $i < $c; $i++){
		$btns[$args[$i]["pos"]] = $args[$i];
	}

	$headers = explode(",",$headers);


	
	$html = "<div class='table'>";
	foreach ($list as $it){
		$html .= "<div class='row'>";
		#botoes anteriores
		for ($i = 0; $i < count($btns); $i++){
			$btn = $btns[$i];
			if (isset($btn['final']) && $btn['final'] == true){
				continue;
			}
			if (isset($btn['link'])){
				$html .= create_list_link($btn, $it->id);
			}
			
		}

		#dados
		foreach($headers as $h){
			$html .= "<span class='col'>";
			$html .= $it->$h;
			$html .= "</span>";
		}

		#botoes posteriores
		for ($i = 0; $i < count($btns); $i++){
			$btn = $btns[$i];
			if (isset($btn['final']) && $btn['final'] == true){
				if (isset($btn['link'])){
					$html .= create_list_link($btn, $it->id);
				}
			}
			
		}

		$html .= "</div>";
	}
	$html .= "</div>";

	print $html;
	$list->controls();
    //return $html;
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
$methodDoc = strtolower($r->getDocComment());
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


			#realiza a validacao
			if (strstr($methodDoc,"@valid") ){
				$errors = validate($obj,$paramName);

				#recupera as mensagens caso existam
				$methodDoc = $r->getDocComment();
				$annotations = [];
				$r = new ReflectionClass($class);
				preg_match('/\@Valid(\{.*?\})/i', $methodDoc, $annotations);
				
				if (isset($annotations[1])){
					$annotJson = $annotations[1];
					$jsobj = json_decode(fixJSON($annotJson));
				}
				
				
				#redireciona de volta caso possua erros
				$url = $_SERVER['HTTP_REFERER'];
				if (count($errors) > 0){
					#salva os erros
					$_SESSION["_has_errors"] = true;
					$_SESSION["_errors"] = $errors;
					$_SESSION["_request"] = $_REQUEST;
					if ($jsobj != null && isset($jsobj->error))
						$_SESSION["_flash_error"] = $jsobj->error;

					
					redirect($url);
					die();
				}

				$_SESSION["_has_errors"] = false;
				if ($jsobj != null && isset($jsobj->success))
					$_SESSION["_flash_success"] = $jsobj->success;
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
