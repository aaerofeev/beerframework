<?php

class Database{
    protected static $instance = null;
    protected $_mPdo = null;
    protected $_mOptions = null;
    protected $_mPdoStat = null;
    protected $_mFetchStyle = null;
    
    protected $_mDefaultOptions = array(
        'host'  =>  'localhost',
        'dbname'    =>  'dbname',
        'username'  =>  'username',
        'password'  =>  'password',
        'fetchStyle'=>  PDO::FETCH_OBJ,
    );
    
    public static function getInstance(){
        if(self::$instance == null){
            self::$instance = new Database();
        }
        
        return self::$instance;
    }
    
    protected function __construct(){        
    }
    
    public function connect($options){
        $this->_mOptions = $options;
        
        $this->_mOptions = Arr::merge($this->_mOptions, $this->_mDefaultOptions);
        
        try {
            $this->_mPdo = new PDO('mysql:host='.$this->_mOptions['host'].';dbname='.$this->_mOptions['dbname'], $this->_mOptions['username'], $this->_mOptions['password']);
            $this->_mPdo->exec('SET CHARACTER SET utf8');            
            $this->_mFetchStyle = $this->_mOptions['fetchStyle'];
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    public function query($query, $params = array()){
        $this->_mPdoStat = $this->_mPdo->prepare($query);
        
        $this->_mPdoStat->execute($params);
        
        return $this;
    }
    
    public function fetch(){
        return $this->_mPdoStat->fetch($this->_mFetchStyle);
    }
    
    public function fetchAll(){
        return $this->_mPdoStat->fetchAll($this->_mFetchStyle);
    }
}
