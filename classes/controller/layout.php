<?php

class Controller_Layout extends Controller_Page {
    /**
     * @var string|Layout
     */
    protected $layout = 'index';

    /**
     * @var array Ошибки, отправляются в вид
     */
    protected $errors = array();

    /**
     * @var Layout
     */
    protected $content;

    public function getViewRoot()
    {        
        return 'visual' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR;
    }
    
    public function getDefaultViewName()
    {
        return $this->_router->getController() . DIRECTORY_SEPARATOR . $this->_router->getAction();
    }
    
    public function createView($file)
    {
        if(is_array($file)) $file = implode(DIRECTORY_SEPARATOR,$file);
        
        $viewRoot = $this->getViewRoot();
        $view = View::factory($file);
        $view->setRoot($viewRoot);
        return $view;
    }

    public function init()
    {
        parent::init();
                
        $this->layout = View::factory($this->layout);
        
        $this->layout->setRoot('visual' . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR);
                
        $viewName = $this->getDefaultViewName();
        
        $view = $this->createView($this->getDefaultViewName());        
        $this->layout->content = $view;
        $this->content = $view;
    }

    public function after()
    {
        parent::after();

        if($this->_request->isAjax() != FALSE || $this->_request->isInitial() == FALSE){
            $this->_response->setBody($this->layout->content);
        }
        else
        {
            $this->content->errors = $this->errors;
            $this->_response->setBody($this->layout->render());
        }
    }
}
