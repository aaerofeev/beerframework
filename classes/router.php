<?php
class Router{
    const PARAMS_DELIMITER = '/';
    protected $_mQuery;
    protected $_mController;
    protected $_mAction;
    protected $_mParams;
    protected $_routes = array();
    
    public function __construct($query){
        // Reduce multiple slashes to a single slash
        $query = preg_replace('#//+#', '/', $query);
        // Remove all dot-paths from the URI, they are not valid
        $query = preg_replace('#\.[\s./]*/#', '', $query);
        $this->_mQuery = trim($query,self::PARAMS_DELIMITER);
    }
    
    public function getParam($key,$default=null)
    {
        $storage = Storage::getInstance();
        
        if(isset($this->_mParams[$key])!=FALSE) 
            return $this->_mParams[$key];
        
        $value = $storage->{$key};
        
        if($value!==null)
            return $value;
        
        return $default;
    }
    
    public function addRoute($name,$rule,$options){
        $this->_routes[$rule] = $options;
        $this->_routes[$rule]['name'] = $name;
    }
    
    public static function detect_uri(){
        $url = parse_url(Storage::getInstance()->getServer('REQUEST_URI'),PHP_URL_PATH);
        return $url;
    }
    
    protected function detectRoute(){
        $query = $this->getQuery();
        $route = Arr::get($this->_routes,$query,false);
        if($route == FALSE) return FALSE;
        
        $this->_mController=Arr::get($route,'controller','index');
        $this->_mAction=Arr::get($route,'action','index');
        $this->_mParams=Arr::get($route,'params',array());
        
        return TRUE;
    }
    
    public function execute(){
        $params = explode(self::PARAMS_DELIMITER,$this->getQuery());
        $this->_mController = 'index';
        $this->_mAction = 'index';
        
        if($this->detectRoute() == FALSE){
            if(isset($params[0]) && !empty($params[0])){
                $this->_mController = $params[0];
            }
            
            if(isset($params[1]) && !empty($params[1])){
                $this->_mAction = $params[1];
            }
        }
                
        for($i=2; $i<count($params); $i+=2){
            $value = null;
            
            if(isset($params[$i+1]) && !empty($params[$i+1]))
                $value = $params[$i+1];
                
            $this->_mParams[$params[$i]] =  $value;
        }
    }
    
    public function getQuery(){
        return $this->_mQuery;
    }
    
    public function getController(){
        return $this->_mController;        
    }
    
    public function getAction(){
        return $this->_mAction;
    }
    
    public function __toString(){
        return 'Controller: '.$this->getController().PHP_EOL.
                'Action: '.$this->getAction().PHP_EOL.
                'Params: '.print_r($this->_mParams,true);
    }
    
    public function serverUrl($url, $saveQuery=false, $queryParams=array()){        
        $bufGet = array();                
        if($saveQuery) $bufGet = $_GET;        
        if(count($queryParams)!=FALSE)
            foreach($queryParams as $key=>$value) $bufGet[$key] = $value;
        
        $currentParams = array();
        foreach($bufGet as $key=>$value)
            $currentParams[] = $key.'='.urlencode($value);
            
        $queryString = implode('&',$currentParams);
        
        if(strpos($url,'://')!==FALSE) return $url;
        
        if(strpos($url,'?')===FALSE AND !empty($queryString))
            $url = $url.'?'.$queryString;
        else
            $url = $url.$queryString;
        
        $host = $_SERVER['HTTP_HOST'];
        $url = trim($url,'/');
        return 'http://'.$host.'/'.$url;
    }
}