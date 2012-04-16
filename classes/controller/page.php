<?php
abstract class Controller_Page{
    /**
     * @var Request
     */
    protected $_request;

    /**
     * @var Router
     */
    protected $_router;

    /**
     * @var Response
     */
    protected $_response;

    /**
     * @var Storage
     */
    protected $_storage;

    /**
     * Pre-action
     */
    public function before(){}

    /**
     * Post-action
     */
    public function after(){}

    public function init(){}

    final public function __construct(Request $request){
        $this->_request = $request;
        $this->_storage = Storage::getInstance();
        $this->_response = $request->getResponse();
        $this->_router = $request->getRouter();
        $this->init();
    }
    
    public function execute($actionName){
        $action = 'action' . ucfirst($actionName);

        if(is_callable(array($this,$action)) == FALSE)
            throw new Exception_Framework('Action not found!');

        call_user_func(array($this,$action));
    }

    /**
     * Получить запрос
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->_request;
    }
}
