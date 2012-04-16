<?php

class Arr{
    public static function get($array, $key, $default = null){
        return isset($array[$key]) ? $array[$key] : $default;
    }
    
    public static function extract($array, $keys, $default = null){
        $extract = array();
        
        foreach($keys as $k){
            $extract[$k] = self::get($array,$k,$default);
        }
        
        return $extract;
    }
    
    public static function merge($array, $defArray){
        foreach($defArray as $key => $elem){
            if(isset($array[$key])){ $defArray[$key] = $array[$key]; }
        }
        
        return $defArray;
    }
}