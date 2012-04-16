<?php
  
class View{
    protected $_mData = array();
    protected $_mViewFileName = null;
    protected $_mRoot = 'visual';
    
    public function __construct($viewName = false, $viewData = array()){
        $this->setViewFilename($viewName);
        $this->putData($viewData);
    }
    
    public function setRoot($path){
        $this->_mRoot = $path;
    }
    
    public function putData($data){
        $this->_mData = $data;
    }
    
    public static function factory($viewName, $viewData = array()){
        $view = new View($viewName, $viewData);
        
        return $view;
    }
    
    public function setViewFilename($filename){
        $this->_mViewFileName = $filename;
    }
    
    public function set($key,$value){
        $this->_mData[$key] = $value;
        return $this;
    }
    
    public function get($key){
        return $this->_mData[$key];
    }
    
    public function __set($key,$value){
        $this->set($key,$value);
    }
    
    public function __get($key){
        return $this->get($key);
    }
    
    public function render($viewFileName = false){
        if($viewFileName == false){ $viewFileName = $this->_mViewFileName; }
        
        extract($this->_mData, EXTR_SKIP);
        
        $storage = Storage::getInstance();
        $messager = Messenger::getInstance();
        
        ob_start();
        
        try
        {
            $root = ROOT_PATH . $this->_mRoot;
            include realpath($root) . DIRECTORY_SEPARATOR . $viewFileName.'.php';
        }
        catch (Exception $e)
        {            
            ob_end_clean();
            
            throw $e;
        }
        
        return ob_get_clean();
                
    }
    
    public function __toString(){
        return $this->render();
    }
}
