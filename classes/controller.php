<?php
class Controller{
    public static function factory($controller,Request $request){
        $cntrl = 'Controller_'.$controller;
        if(class_exists($cntrl,true)==FALSE) throw new Exception_Framework('Controller not found!');
        return new $cntrl($request);
    }
}
