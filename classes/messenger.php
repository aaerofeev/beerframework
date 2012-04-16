<?php
class Messenger{
    protected static $_instance;
    public static function getInstance(){
        if(self::$_instance==FALSE) self::$_instance = new self;
        return self::$_instance;        
    }
    
    protected $_mErrors;
    protected $_mMessages;
    
    protected function __construct()
    {
        $this->clearErrors();
        $this->clearMessages();
        $sessionMessages = Storage::getInstance()->getSession('messenger','messages');
        if($sessionMessages != FALSE) {
            $this->_mMessages = $sessionMessages;            
        }
    }
        
    public function addError($key,$value)
    {
        if(isset($this->_mErrors[$key]) == FALSE)
            $this->_mErrors[$key] = $value;
    }
    
    public function addMessage($key,$value)
    {
        if(isset($this->_mMessages[$key]) == FALSE){
            $this->_mMessages[$key] = $value;
            Storage::getInstance()->setSession('messenger','messages',$this->_mMessages);
        }
    }
    
    public function addErrors($errors)
    {        
        $this->_mErrors = $errors;
    }
    
    public function addMessages($messages)
    {
        $this->_mMessages = $messages;
        Storage::getInstance()->setSession('messenger','messages',$this->_mMessages);
    }
    
    public function clearMessages()
    {
        $this->_mMessages = array();
    }
    
    public function clearErrors()
    {
        $this->_mErrors = array();
    }
    
    public function getError($key)
    {
        $sess = Storage::getInstance()->getSession('messenger','messages');
        if(isset($sess[$key])){
            unset($sess[$key]);
            Storage::getInstance()->setSession('messenger','messages',$sess);
        }
        return Arr::get($this->_mErrors,$key,null);
    }
    
    public function getMessage($key)
    {
        return Arr::get($this->_mMessages,$key,null);
    }
    
    public function getErrors()
    {
        return $this->_mErrors;
    }
    
    public function getMessages()
    {
        Storage::getInstance()->deleteSession('messenger','messages'); 
        return $this->_mMessages;
    }
    
    public function deleteError($key)
    {
        if(isset($this->_mErrors[$key]))
            unset($this->_mErrors[$key]);
    }
    
    public function deleteMessage($key)
    {
        if(isset($this->_mMessages[$key]))
            unset($this->_mMessages[$key]);
    }
    
    public function isErrors()
    {
        return (count($this->_mErrors)!=FALSE);
    }
    
    public function isMessages()
    {
        return (count($this->_mMessages)!=FALSE);
    }
    
    public function isError($key)
    {
        return isset($this->_mErrors[$key]);
    }
    
    public function isMessage($key)
    {
        return isset($this->_mMessages[$key]);
    }
}
