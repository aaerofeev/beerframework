<?php
class Storage{
    protected $_mPost;
    protected $_mGet;
    protected $_mSession;
    protected $_mServer;
    
    protected function __construct(){
        $this->_mPost = $_POST;
        $this->_mGet = $_GET;
        $this->_mSession = &$_SESSION;
        $this->_mServer = $_SERVER;
        $this->_mFiles = $_FILES;
    }

    /**
     * @var Storage
     */
    protected static $_msInstance;

    /**
     * @static
     * @return Storage
     */
    public static function getInstance(){
        if(self::$_msInstance==FALSE) self::$_msInstance = new self();
        return self::$_msInstance;
    }
    
    public function __get($key){
        if(isset($this->_mFiles[$key])) return $this->_mFiles($key);
        if(isset($this->_mPost[$key])) return $this->getPost($key);
        if(isset($this->_mGet[$key])) return $this->_mGet[$key];
        if(isset($this->_mSession[$key])) return $this->_mSession[$key];
        if(isset($this->_mServer[$key])) return $this->getServer($key);
        return null;
    }
    
    public function setSession($namespace,$key,$value){
        $this->_mSession[$namespace.'_namespace_'.$key] = $value;
    }
    
    public function getSession($namespace,$key){
        if(isset($this->_mSession[$namespace.'_namespace_'.$key]))
            return $this->_mSession[$namespace.'_namespace_'.$key];
    }
    
    public function deleteSession($namespace,$key){
        if(isset($this->_mSession[$namespace.'_namespace_'.$key]))
            unset($this->_mSession[$namespace.'_namespace_'.$key]);
    }
    
    public function getPost($key = FALSE){
        if($key != FALSE) return $this->getKey($this->_mPost,$key);
        
        return $this->_mPost;
    }
    
    public function getServer($key){
        return $this->getKey($this->_mServer,$key);
    }
    
    public function getKey($array,$key){
        return isset($array[$key]) ? $array[$key] : null;
    }
}