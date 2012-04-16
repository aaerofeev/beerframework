<?php
class Pavon_Date{
    protected $_date =  array('day'=>0,'month'=>0,'year'=>0);
    protected $_originDate;
    protected $_months = array(
        1=>'Января',
        2=>'Февраля',
        3=>'Марта',
        4=>'Апреля',
        5=>'Мая',
        6=>'Июня',
        7=>'Июля',
        8=>'Августа',
        9=>'Сентября',
        10=>'Октября',
        11=>'Ноября',
        12=>'Декабря',
    );
    
    protected $_days = array(
        1=>'Понедельник',
        2=>'Вторник',
        3=>'Среда',
        4=>'Четверг',
        5=>'Пятница',
        6=>'Суббота',
        7=>'Воскресенье',
    );
    
    public function __construct($date = null){
        if($date == null) $date = date('d.m.Y');
        $this->_originDate = $date;
        $this->parseDate($date);
    }
    
    public function parseDate($date){
        $matches = array();
        preg_match('#(?P<day>\d{1,2}).(?P<month>\d{1,2}).(?P<year>\d{4})#',$date,$matches);
        $this->setDay($matches['day']);
        $this->setMonth($matches['month']);
        $this->setYear($matches['year']);
               
    }
    
    public function setDay($day){
        $this->_date['day'] = (int)$day;
    }
    
    public function setMonth($month){
        $this->_date['month'] = (int)$month;
    }
    
    public function setYear($year){
        $this->_date['year'] = (int)$year;
    }
    
    public function getDay(){
        return $this->_date['day'];
    }
    
    public function getMonth(){
        return $this->_date['month'];
    }
    
    public function getYear(){
        return $this->_date['year'];
    }
    
    public function getArray(){
        return $this->_date;
    }
    
    public function humanizeMonth($month,$lower=false,$short=false){
        $month = $this->_months[$month];
        if($short) $month = mb_substr($month,0,3,'utf-8');
        if($lower) $month = mb_strtolower($month,'utf-8');
        return $month;
    }
    
    public function humanizeYear($year,$short=false){
        $postfix = 'год';
        if($short) $postfix = 'г.';
        return $year.' '.$postfix;
    }
    
    public function humanizeDayOfWeek($day,$lower=false,$short=false){
        $day = $this->_days[$day];
        if($short) $day = mb_substr($day,0,3,'utf-8');
        if($lower) $day = mb_strtolower($day,'utf-8');
        return $day;        
    }
    
    public function getDayOfWeek($date){
        $day = (int)date('N',strtotime($date));
        return $day;
    }
    
    public function getOriginDate(){
        return $this->_originDate;
    }
    
    public function humanize($weekDay=false,$short=false){
        $day = '';
        if($weekDay) $day = $this->humanizeDayOfWeek($this->getDayOfWeek($this->getOriginDate()),true,$short).' ';
        return $day.$this->getDay().' '.$this->humanizeMonth($this->getMonth(),true,$short).' '.$this->humanizeYear($this->getYear(),$short);
    }
}
