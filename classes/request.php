<?php
class Request{
    protected function __construct($initial=false, $requestString=false)
    {
        $this->_mInitial=$initial;
        $xReqWith = Storage::getInstance()->getServer('HTTP_X_REQUESTED_WITH');
        if($xReqWith != FALSE AND strtolower($xReqWith) === 'xmlhttprequest'){
            $this->_mIsAjax = true;
        }else{
            $this->_mIsAjax = false;
        }
        
        if($requestString!=FALSE) $this->_mRequestString = $requestString;
        else $this->_mRequestString = Router::detect_uri();
    }
    protected static $_instance;
    public static function getInstance($requestString)
    {
        if(self::$_instance==FALSE) self::$_instance = new self(true,$requestString);
        return self::$_instance;
    }
    
    public static function factory($requestString)
    {
        return new Request(false,$requestString);
    }
    
    protected $_mInitial;
    protected $_mIsAjax;

    /**
     * @var Router
     */
    protected $_mRouter;
    protected $_mResponse;
    protected $_mRequestString;
    
    public function isAjax()
    {
        return $this->_mIsAjax;
    }
    
    public function execute()
    {
        $this->_mResponse = new Response();
        
        $this->_mRouter = new Router($this->_mRequestString);
        $this->_mRouter->addRoute('rights','rights',array('controller'=>'index','action'=>'rights'));
        $this->_mRouter->execute();
                
        
        $controller = Controller::factory($this->_mRouter->getController(),$this);        
        $controller->before();
        $controller->execute($this->_mRouter->getAction());
        $controller->after();
                
        return $this->getBody();
    }
    
    public function acceptHeaders()
    {
        $this->_mResponse->acceptHeaders();
    }
    
    public function getBody()
    {
        return $this->_mResponse->getBody();
    }

    /**
     * @return Router
     */
    public function getRouter()
    {
        return $this->_mRouter;
    }
    
    public function getResponse()
    {
        return $this->_mResponse;
    }
    
    public function isInitial()
    {
        return $this->_mInitial;
    }
    
    public function isPost()
    {
        return strtolower($_SERVER['REQUEST_METHOD']) == 'post';
    }
    
    public function isGet()
    {
        return strtolower($_SERVER['REQUEST_METHOD']) == 'get';
    }
    
    public function redirect($url)
    {
        $this->_mResponse->addHeader('Location: '.$url);
        $this->acceptHeaders();
        exit;
    }
    
    public function refresh()
    {
        $queryString = Storage::getInstance()->getServer('QUERY_STRING');
        $url = Url::site($this->_mRouter->getQuery().$queryString);
        $this->redirect($url);
    }
    
    public function __toString()
    {
        return $this->getBody();
    }
}