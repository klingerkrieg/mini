<?php


class RBModel {

    private $_tableName;
    //private $_rbObj = null;
    public $id = null;
    
    function __construct(){
        
        if (func_num_args() > 0){
            $obj = func_get_arg(0);

            #recupera os valores do objeto do RB
            $fields = get_object_vars($this);
            foreach( $fields as $field=>$value){
                //ignora as variaveis que começam com _
                if ($field[0] == "_"){
                    continue;
                }
                
                $this->$field = $obj->$field;
            }

        }
    
        $this->_tableName = strtolower(get_called_class());
    }
    
    public function save(){
        
        if ($this->id != null){
            $obj = R::load($this->_tableName, $this->id );
        } else {
            $obj = R::dispense( strtolower($this->_tableName));
        }
    
        $fields = get_object_vars($this);
        foreach( $fields as $field=>$value){
            //ignora as variaveis que começam com _
            if ($field[0] == "_" || $field == "id"){
                continue;
            }
            
            $obj->$field = $value;
        }

        
        R::store($obj);
    }
    
    
    public function delete(){
        if ($this->id != null){
            $obj = R::load($this->_tableName, $this->id );
            R::trash($obj);
        }
    }

    public static function deleteById($id){
        $obj = R::load(get_called_class(), $id );
        R::trash($obj);
    }

    public static function findById($id){
        $class = get_called_class();
        return new $class(R::load(get_called_class(),$id));
    }
    
    
    public static function first($sql){
    
        if (func_num_args() > 1){
            $arr = func_get_arg(1);
            $rbObj = R::findOne( get_called_class(),$sql,$arr);
        } else {
            $rbObj = R::findOne( get_called_class(),$sql);
        }
    
        if ($rbObj == null){
            return null;
        }
    
        $class = get_called_class();
        return new $class($rbObj);
    }
    
    public static function find($sql){
        
        if (func_num_args() > 1){
            $arr = func_get_arg(1);
            $list = R::find( get_called_class(),$sql,$arr);
        } else {
            $list = R::find( get_called_class(),$sql);
        }
    
        $class = get_called_class();
        $arr = [];
        foreach ($list as $it){
            array_push($arr, new $class($it));
        }
    
        return $arr;
    }
    
    public static function findAll(){
        
        $list = R::find( get_called_class());
    
        $class = get_called_class();
        $arr = [];
        foreach ($list as $it){
            array_push($arr, new $class($it));
        }
    
        return $arr;
    }

    public static function recreateTable($file){
        
        if (!file_exists("./models_checksum"))
            mkdir("./models_checksum");

        $md5 = md5_file("./models/$file.php");

        if (!file_exists("./models_checksum/$file")){
            file_put_contents("./models_checksum/$file",$md5);
            return true;
        }

        $md5s = file_get_contents("./models_checksum/$file");

        if ($md5 == $md5s){
            return false;
        }

        file_put_contents("./models_checksum/$file",$md5);
        return true;
        
    }
    
    public static function createTable(){
        #checa se a tabela foi modificada
        if (RBModel::recreateTable(get_called_class()) == false){
            return;
        }

        $bean = R::dispense( strtolower(get_called_class()));
        $fields = get_class_vars(get_called_class());
    
        foreach( $fields as $field=>$value){

            
            //ignora as variaveis que começam com _
            if ($field[0] == "_" || $field == "id"){
                continue;
            }

            $r = new ReflectionProperty(get_called_class(), $field);
            $comment = strtolower($r->getDocComment());

            if (strstr($comment,"@varchar")){
                $value = "";
            } else
            if (strstr($comment,"@int")){
                $value = 0;
            } else
            if (strstr($comment,"@date")){
                $value = "1990-01-01";
            } else
            if (strstr($comment,"@datetime")){
                $value = "1990-01-01 00:00:00";
            } else
            if (strstr($comment,"@double")){
                $value = 0.0;
            } else
            if (strstr($comment,"@bool")){
                $value = false;
            } else
            if (strstr($comment,"@money")){
                $value = "10.00";
            }
            
    
            $bean->$field = $value;
        }
    
        R::store($bean);
        R::trash($bean);
    }
    
    
}
/*
class RBModels_loader extends CI_Model {

    public function __construct(){

        parent::__construct();

        $model_files = scandir(__DIR__);

        foreach($model_files as $file){
            $ff = explode('.', $file);
            if(
               strtolower($ff[0]) !== strtolower(__CLASS__) &&
               strtolower($ff[1]) === 'php') {
                $this->load->model(strtolower(explode('.', $file)[0]));
                
                if (Rb::$createTables)
                    $ff[0]::createTable(); 
            }
        }
    }
}*/