<?php
class Response{
    protected $_body;
    protected $_headers;
    
    public function __construct(){
        $this->_body = null;
        $this->_headers = array();
    }
    
    public function setBody($body){
        $this->_body = $body;
    }
    
    public function getBody(){
        return $this->_body;        
    }
    
    public function setHeaders($headers){
        $this->_headers = $headers;        
    }
    
    public function getHeaders(){
        return $this->_headers;
    }
    
    public function addBody($block){
        $this->_body.=$block;        
    }
    
    public function addHeader($header){
        $this->_headers[] = $header;
    }
    
    public function acceptHeaders(){
        foreach($this->getHeaders() as $header)
            header($header);
    }
    
    public function getResponse($acceptHeaders=false,$display=false){
        if($acceptHeaders) $this->acceptHeaders();        
        $body = $this->getBody();
        if($display) echo $body;        
        return $body;
    }
}
