<?php

function validate($obj,$paramName){

    $errors = [];

    $r = new ReflectionObject($obj);

    $props = $r->getProperties();

    foreach($props as $p){
        $doc = strtolower($p->getDocComment());
        $name = $paramName.".".$p->getName();
        $pName = $p->getName();
        $val = $obj->$pName;
        
        if (strstr($doc,"@required") && $val == ""){
            $errors[$name] = ["error"=>"required","value"=>$val];
        }

        #caso o campo venha vazio ignora
        if ($val == ""){
            continue;
        }

        #validacoes de acordo com o tipo
        if (strstr($doc,"@int") && !is_numeric($val)){
            $errors[$name] = ["error"=>"int","value"=>$val];
        } else
        if (strstr($doc,"@datetime") && !validateDate($val)){
            $errors[$name] = ["error"=>"datetime","value"=>$val];
        } else
        if (strstr($doc,"@date") && !validateDate($val, "d/m/Y")){
            $errors[$name] = ["error"=>"date","value"=>$val];
        } else
        if ( (strstr($doc,"@money") || strstr($doc,"@double")) && !is_numeric($val)){
            $errors[$name] = ["error"=>"double","value"=>$val];
        }

        

    }

    return $errors;

}


function validateDate($date, $format = 'd/m/Y H:i:s') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}



function fixJSON($s) {
    $s = str_replace(
        array("'"),
        array('"'),
        $s
    );
    $s = preg_replace('/(\w+):/i', '"\1":', $s);
    return sprintf('%s', $s);
}