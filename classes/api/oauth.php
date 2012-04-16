<?php
require_once('twitteroauth/twitteroauth.php');

class Api_Oauth{
    protected static $_instance;
    protected function __construct(){
        
    }
    public static function getInstance()
    {
        if(self::$_instance == FALSE) self::$_instance = new self;
        return self::$_instance;
    }
    
    const VKONTAKTE = 'vkontakte';
    const FACEBOOK = 'facebook';
    const ODNOKLASSNIKI = 'odnoklassniki';
    const TWITTER = 'twitter';
    
    const FIELD_ID = 'id';
    const FIELD_SECRET = 'secret';
    const FIELD_OAUTH = 'oauth';
    const FIELD_SCOPE = 'scope';
    const FIELD_PUBLIC = 'public';
    const FIELD_TOKEN = 'token';
    const FIELD_USER = 'user';
    
    public $social = array(
        self::VKONTAKTE=>array(
            self::FIELD_ID=>'',
            self::FIELD_PUBLIC=>'',
            self::FIELD_SECRET=>'',
            self::FIELD_OAUTH=>'http://api.vkontakte.ru/oauth/authorize/?client_id={APPID}&redirect_uri={REDIRECT}&response_type=code',
            self::FIELD_TOKEN=>'https://api.vkontakte.ru/oauth/access_token?code={CODE}&client_id={APPID}&client_secret={SECRET}',
            self::FIELD_SCOPE=>'id'
        ),
        self::FACEBOOK=>array(
            self::FIELD_ID=>'',
            self::FIELD_PUBLIC=>'',
            self::FIELD_SECRET=>'',
            self::FIELD_OAUTH=>'https://www.facebook.com/dialog/oauth?client_id={APPID}&redirect_uri={REDIRECT}',
            self::FIELD_TOKEN=>'https://graph.facebook.com/oauth/access_token?client_id={APPID}&client_secret={SECRET}&code={CODE}&redirect_uri={REDIRECT}',
            self::FIELD_SCOPE=>'id',
            self::FIELD_USER=>'https://graph.facebook.com/me?access_token={TOKEN}&fields=id',
        ),
        self::ODNOKLASSNIKI=>array(
            self::FIELD_ID=>'',
            self::FIELD_PUBLIC=>'',
            self::FIELD_SECRET=>'',
            self::FIELD_OAUTH=>'http://www.odnoklassniki.ru/oauth/authorize?client_id={APPID}&response_type=code&redirect_uri={REDIRECT}&scope=VALUABLE ACCESS;',
            self::FIELD_TOKEN=>'http://api.odnoklassniki.ru/oauth/token.do',
            self::FIELD_USER=>'http://api.odnoklassniki.ru/api/users/getCurrentUser',
            self::FIELD_SCOPE=>'id'
        ),
        self::TWITTER=>array(
            self::FIELD_ID=>'',
            self::FIELD_PUBLIC=>'',
            self::FIELD_SECRET=>'',
            self::FIELD_OAUTH=>'https://api.twitter.com/oauth/authorize?consumer_key={APPID}&oauth_callback={REDIRECT}',
            self::FIELD_SCOPE=>'id'
        ),
    );
    
    protected function getAppId($network)
    {
        return $this->social[$network][self::FIELD_ID];
    }
    
    protected function getAppSecret($network)
    {
        return $this->social[$network][self::FIELD_SECRET];
    }
    
    protected function getOauthLink($network)
    {
        return $this->social[$network][self::FIELD_OAUTH];
    }
    
    protected function getScope($network)
    {
        return $this->social[$network][self::FIELD_SCOPE];
    }
    
    public function getFullOAuthLink($network,$redirect_url)
    {
        $storage = Storage::getInstance();
        $storage->setSession('redirect',$network,$redirect_url);
        
        $oauthLink = $this->getOauthLink($network);
        $params = array(
            '{APPID}'=>$this->getAppId($network),
            '{REDIRECT}'=>$redirect_url,
        );
        $url = str_replace(array_keys($params), $params,$oauthLink);
        return $url;
    }
    
    protected function getTokenLink($network)
    {
        return $this->social[$network][self::FIELD_TOKEN];
    }
    
    public function getFullTokenLink($network,$code)
    {
        $storage = Storage::getInstance();
        $link = $this->getTokenLink($network);
        $params = array(
            '{APPID}'=>urlencode($this->getAppId($network)),
            '{SECRET}'=>urlencode($this->getAppSecret($network)),
            '{CODE}'=>urlencode($code),
            '{REDIRECT}'=>urlencode($storage->getSession('redirect',$network)),
        );
        $url = str_replace(array_keys($params),$params,$link);
        
        return $url;
    }
    
    protected function getUserLink($network)
    {
        return $this->social[$network][self::FIELD_USER];
    }
    
    public function  getFullUserLink($network,$token)
    {
        $link = $this->getUserLink($network);
        $params = array(
            '{TOKEN}'=>urlencode($token),
        );
        $url = str_replace(array_keys($params),$params,$link);
        return $url;
    }
    
    public function setOauthId($network, $id)
    {
        $storage = Storage::getInstance();
        $storage->setSession('user','social', $network.'-'.$id);
        $storage->setSession('network_info',$network,$id);
        $storage->setSession('user','provider', $network);
    }
    
    public function getNetworkInfo($network)
    {
        $storage = Storage::getInstance();
        return $storage->getSession('network_info',$network);
    }
    
    public function getOauthId()
    {
        $storage = Storage::getInstance();
        return $storage->getSession('user','social');
    }
    
    public function getOauthProvider()
    {
        $storage = Storage::getInstance();
        return $storage->getSession('user','provider');
    }
    
    public function getToken($network, $code)
    {
        $token = null;
        if($network == self::ODNOKLASSNIKI)
        {
            $data = $this->getTokenOdnoklassniki($code);
        }else{
            $data = file_get_contents($this->getFullTokenLink($network,$code));
        }
        
        if($network==self::FACEBOOK)
        {
            list($token,$expires) = explode('&',$data,2);
            list($key,$value) = explode('=',$token,2);
            $data = $value;
        }else $data = json_decode($data);
                
        return $data;
    }
    
    public function getUserInfo($network, $token)
    {
        $link = $this->getFullUserLink($network,$token);
        
        if($network == self::ODNOKLASSNIKI){
            $params = array(
                'application_key='.$this->social[$network][self::FIELD_PUBLIC],
                //'method=users.getCurrentUser',
                //'access_token='.$token,
                'format=json',
            );
            sort($params);
            $sig = strtolower(md5(join('',$params).md5($token.$this->getAppSecret($network))));
            $link.='?sig='.$sig.'&'.join('&',$params).'&access_token='.$token;
        }
        
        $data = file_get_contents($link);
        $data = json_decode($data);
        return $data;
    }
    
    public function getTokenOdnoklassniki($code){
            $network = self::ODNOKLASSNIKI;
            
            $storage = Storage::getInstance();            
            $params = array(
                '{APPID}'=>urlencode($this->getAppId($network)),
                '{SECRET}'=>urlencode($this->getAppSecret($network)),
                '{CODE}'=>urlencode($code),
                '{REDIRECT}'=>urlencode($storage->getSession('redirect',$network)),
            );
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://api.odnoklassniki.ru/oauth/token.do?');
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch, CURLOPT_POST, 1);
            $fields = 'code={CODE}&redirect_uri={REDIRECT}&grant_type=authorization_code&client_id={APPID}&client_secret={SECRET}';
            $fields = str_replace(array_keys($params),$params,$fields);
            curl_setopt($ch, CURLOPT_POSTFIELDS,$fields);
            $data = curl_exec($ch);
            curl_close($ch);
            return $data;        
    }
    
    public function getTwitterRequestToken($redirect_url)
    {
        $storage = Storage::getInstance();
        $storage->setSession('redirect',self::TWITTER,$redirect_url);
        
        $twitter = new TwitterOAuth($this->getAppId(self::TWITTER),$this->getAppSecret(self::TWITTER));
        $request_token = $twitter->getRequestToken($redirect_url);
        
        $token = $request_token['oauth_token'];
        $storage->setSession('secret', self::TWITTER, $request_token['oauth_token_secret']);
        
        return  $twitter->getAuthorizeURL($token);
    }
    
    public function getTwitterUser($oauth_token,$oauth_verifier){
        $storage = Storage::getInstance();
        $secret = $storage->getSession('secret', self::TWITTER);
        $twitter = new TwitterOAuth($this->getAppId(self::TWITTER),$this->getAppSecret(self::TWITTER),$oauth_token,$secret);
        $content = $twitter->getAccessToken($oauth_verifier);
        
        if(isset($content['user_id'])==FALSE || $content['user_id']==FALSE) return FALSE;
        
        return $content['user_id'];
    }
}