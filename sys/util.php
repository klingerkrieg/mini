<?php


/**
* AmericanDate to PtBrDate
*/
function dateToBr($date = ""){
    if ($date == ""){
        return "";
    }
    
    $date = date_create_from_format('Y-m-d',$date);
    return date_format($date, 'd/m/Y');
}

/**
* AmericanDate to PtBrDate
*/
function dateToEUA($date = ""){
    if ($date == ""){
        return "";
    }
    
    $date = date_create_from_format('d/m/Y', $date);
    return date_format($date, 'Y/m/d');
}