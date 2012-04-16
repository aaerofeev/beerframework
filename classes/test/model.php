<?php
class Test_Model extends Model{
    public function runTest(){
            
            $this->testIsNumeric('123','test','err',true);
            $this->testIsNumeric('123a','test','err',false);
            $this->testIsNumeric('asa','test','err',false);
                                      
            $this->testIsAlpha('Друзь','test','err',true);
            $this->testIsAlpha('Дру-ь','test','err',true);
            $this->testIsAlpha('Дру2зь','test','err',false);
            
            $this->testIsUcFirst('Друзь','lastname','Херня всё',true);
            $this->testIsUcFirst('друзь','lastname','Херня всё',false);
            $this->testIsUcFirst('дрУзь','lastname','Херня всё',false);
            
            $this->testIsUcOne('Друзь','lastname','Херня всё',true);
            $this->testIsUcOne('друзь','lastname','Херня всё',false);
            $this->testIsUcOne('дРУзь','lastname','Херня всё',false);
            
            $this->testIsUniqColumn('morg@mail.ru','email','users','Херня всё',false);
            $this->testIsUniqColumn('morg1@mail.ru','email','users','Херня всё',true);
    }
    
    public function __call($name,$arguments){        
        $match = array();
        $regexp = '|test(.*)|si';
        preg_match($regexp,$name,$match);
        
        if(!empty($match[1])){
            $processResult = array_pop($arguments);
            $result = call_user_func_array(array($this,$match[1]),$arguments);
            
            $wResult = $result;
            
            echo '<pre>';
            echo 'Call: '.$match[1].PHP_EOL;
            echo 'Input: '.implode(', ',$arguments).PHP_EOL;
            echo 'Waiting: '.print_r($processResult,true).PHP_EOL;
            if(is_bool($wResult)) $wResult = (int)$result;
            echo 'Output: '.print_r($wResult,true).PHP_EOL;
            
            if($result == $processResult) { echo 'Result: Ok'.PHP_EOL; }
            else{ echo '<b style="color:red">Result: Fall</b>'.PHP_EOL; }
            echo '</pre>';
        }
    }
    
    
}
