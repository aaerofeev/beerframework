<?php
class Model{
    protected $_mMethods = array('query','fetch','fetchAll');
    protected $_mMessenger;
    protected $_mTable = null;
    
    public static function factory($modelName)
    {
        $className = 'Model_'.ucfirst($modelName);
        return new $className(Database::getInstance());
    }
    
    protected $_mDb;
    public function __construct($dataBase)
    {
        $this->_mDb = $dataBase;
        $this->_mMessenger = Messenger::getInstance();    
    }
    
    public function __call($name,$arguments)
    {
        if(in_array($name,$this->_mMethods)) return call_user_func_array(array($this->_mDb,$name),$arguments);
    }
    
    public function addError($key,$value)
    {
        $this->_mMessenger->addError($key,$value);
    }
    
    public function addMessage($key,$value)
    {
        $this->_mMessenger->addMessage($key,$value);
    }
    
    public function getSizeByte($value)
    {
        $regExp = '/(.*)(m|g|b|t)/ui';
        preg_match($regExp,$value,$match);
        
        $size = Arr::get($match,1);
        $type = strtolower(Arr::get($match,2));
        $mult = 0;
        
        switch($type){
            case 'm':$mult = 1048576;
                break;
            case 'g':$mult = 1073741824;
                break;
            case 't':$mult = 1099511627776;
                break;
            case 'b':$mult = 1;
            
        }
        
        return (int)($size*$mult);
    }
    
    public function isNotEmpty($value,$key,$error)
    {
        if(empty($value)){
            $this->addError($key,$error);
            return false;  
        } 
        return true;
    }
    
    public function isEmail($value,$key,$error)
    {
        $expression = '/^[-_a-z0-9\'+*$^&%=~!?{}]++(?:\.[-_a-z0-9\'+*$^&%=~!?{}]+)*+@(?:(?![-.])[-a-z0-9.]+(?<![-.])\.[a-z]{2,6}|\d{1,3}(?:\.\d{1,3}){3})(?::\d++)?$/iD';
        if(preg_match($expression, $value) == FALSE){ 
            $this->addError($key,$error);
            return false;  
        } 
        return true;
        
    }
    
    public function isValidFileType($key, $extensions, $error)
    {
        $file = Arr::get($_FILES, $key);
        $extension = strtolower(pathinfo(Arr::get($file,'name',NULL),PATHINFO_EXTENSION));
        
        if(in_array($extension,$extensions) == FALSE){
            $this->addError($key,$error);
            return false;
        }
        
        return true;
    }
    
    public function isFileNotEmpty($key,$error)
    {
        $file = Arr::get($_FILES, $key);
        
        if(is_uploaded_file(Arr::get($file,'tmp_name',false)) == FALSE){
            $this->addError($key,$error);
            return false;
        }
        
        return true;                
    }
    
    public function isValidFileSize($size,$key,$error)
    {
        $file = Arr::get($_FILES, $key);
        $fileSize = Arr::get($file,'size',0);
        
        if($fileSize > $this->getSizeByte($size)){
            $this->addError($key,$error);
            return false;
        }
        
        return true;
    }
    
    public function isValidLength($value,$length,$key,$error)
    {
        if(mb_strlen($value) != $length){ 
            $this->addError($key,$error);
            return false;  
        } 
        return true;
    }
    
    public function isNotGreatThen($value, $then, $key, $error)
    {
        $value = (int)$value;
        
        if($value > $then){
            $this->addError($key,$error);
            return false;
        } 
        return true;
    }
    
    public function isNumeric($value,$key,$error)
    {
        $match = array();
        if(preg_match('/[^0-9]/si',$value,$match) != FALSE){ 
            $this->addError($key,$error);
            return false;  
        } 
        return true;
    }
    
    public function isAlpha($value,$key,$error)
    {
        $match = array();
        if(preg_match('/[^-а-яА-Яa-zA-Z ]/u',$value,$match) != FALSE){
            $this->addError($key,$error);
            return false;
        }
        return true;
    }
    
    public function isUcFirst($value,$key,$error)
    { 
        $match = array();       
        if(preg_match('/^[А-ЯA-Z]/u',$value,$match) == FALSE){
            $this->addError($key,$error);
            return false;            
        }
        return true;
    }
    
    public function isUcOne($value,$key,$error)
    {
        $match = array();
        $numUppcase = preg_match_all('/[А-ЯA-Z]/u',$value,$match);
        if($numUppcase !== 1){
            $this->addError($key,$error);
            return false;            
        }
        return true;
    }
    
    public function isUniqColumn($value,$key,$table,$error){
        $parameters = array(
            ':value'    =>  $value
        );
        $result = Database::getInstance()->query('SELECT COUNT(*) as count FROM '.$table.' WHERE '.$key.' = :value LIMIT 1',$parameters)->fetch();
        
        if($result->count != 0){
            $this->addError($key,$error);
            return false;
        }
        
        return true;
    }
    
    public function uploadFile($key, $path)
    {
        $file = Arr::get($_FILES,$key);
        
        $tmp_name = Arr::get($file,'tmp_name');
        $extension = pathinfo(Arr::get($file,'name',NULL),PATHINFO_EXTENSION);
        $filepath = $path.'.'.$extension;
        
        if(move_uploaded_file($tmp_name,$filepath) == FALSE){
            return 'error'; 
        }
        
        return pathinfo($filepath,PATHINFO_BASENAME);
    }
    
    public function isValidSessionKey($value,$key,$error)
    {
        if(strtolower($value) != strtolower($_SESSION[$key])){
            $this->addError($key,$error);
            return false;
        }
        return true;
    }
        
    public function copyFile($source,$dest){
        return copy($source,$dest);
    }
}