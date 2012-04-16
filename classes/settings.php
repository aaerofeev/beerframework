<?php
class Settings{
    const PHOTO_SRC_PATH = 'media/img/photos/source/';
    const PHOTO_STD_PATH = 'media/img/photos/normal/';
    const PHOTO_MIN_PATH = 'media/img/photos/minimal/';
    const PHOTO_MOB_PATH = 'media/img/photos/mobile/';
    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;
    const GENDER_ALL = 0;
    
    const STATE_NONE = 0;
    const STATE_ACTIVE = 1;
    const STATE_REJECTED = 2;
    
    const ACT_CURRENT = 'current';
    const ACT_FUTURE = 'future';
    const ACT_PAST = 'past';
    const ACT_NEVER = 'never';
    
    const ST_REGISTRATION  = 1;
    const ST_VOTE = 2;
    const ST_MOBILE = 3;
    const ST_FINAL = 4;
    
    const AJAX_OK='ok';
    const AJAX_BAD = 'error';
    
    protected static $range = array(        
        self::ST_FINAL=>array('start'=>'14-02-2012','end'=>'20-02-2012','controller'=>'final'),
        //self::ST_MOBILE=>array('start'=>'29-01-2012','end'=>'01-02-2012','controller'=>'mobile'),
        self::ST_VOTE=>array('start'=>'06-02-2012','end'=>'13-02-2012','controller'=>'vote'),
        self::ST_REGISTRATION=>array('start'=>'31-01-2012','end'=>'13-02-2012','controller'=>'registration'),
    );
    
    public static function getStage($stage)
    {
        return self::$range[$stage];
    }
    
    public static function getStageActivity($stage)
    {
        $date = self::$range[$stage];        
        if(strtotime($date['start'])<=time() AND time()<=strtotime($date['end'])) return self::ACT_CURRENT;
        if(strtotime($date['start']) > time()) return self::ACT_FUTURE;
        if(strtotime($date['end']) < time()) return self::ACT_PAST;
                
        return self::ACT_NEVER;
    }
    
    public static function getActiveStage()
    {
        foreach(self::$range as $key=>$date)
            if(strtotime($date['start']) <= time() AND strtotime($date['end']) >= time()) return $key;
        return 0;        
    }
    
    public static function isValidStage($stage)
    {
        return isset(self::$range[$stage]);
    }
}
