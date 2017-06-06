<?php

class Rb extends R {

    public static function tbl($tblName){
        return R::dispense($tblName);
    }

    public static function save($obj){
        return R::store($obj);
    }

    public static function delete($tblName,$id){
        $obj = R::load($tblName,$id);
        return R::trash($obj);
    }

}