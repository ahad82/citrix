<?php
class OAuth{

    public $autologin_url;
    public $exchange_url;
    public $code;
    public $auth_result;
    //https://api.citrixonline.com/oauth/authorize?client_id= used this URL to get all the field names 
   public $login_data = array(
        'emailAddress' => '',
        'password' => '',
        'client_id' => '',
        'access_type'=> 'G2T',
        'app_name' => '',
        'redirect_uri' => '',
        'submitted' => 'form_submitted',
        );

   public function __construct($autologin_url = AUTH_AUTOLOGIN_URL, $exchange_url = AUTH_EXCHANGE_URL, $apikey=API_KEY){
       $this->autologin_url = $autologin_url;
       $this->exchange_url = $exchange_url;
       $this->login_data['client_id'] = $apikey;
   }
   
   public function authorize(){
       $this->getCode();
       $this->exchangeCodeForAccessToken();

   }
   
   public function getCode(){
       $curl = new Curl();
       $result = $curl->request($this->autologin_url, $this->login_data, "post");
       $arr = explode("\n", $result);
        foreach($arr as $k=>$v)
        {
                if(strstr($v,"Location: http:"))
                        $return_url = $v;
        }
        $query = trim(parse_url($return_url, PHP_URL_QUERY));// adds one unnecessary _ (underscore) at the end of the query string
        $this->code = substr($query, 5, (strlen($query) - 6));//starting from 5 get me ...number of chars
   }
   
   function exchangeCodeForAccessToken(){
       $this->exchange_url = str_replace("<CODE>", $this->code, $this->exchange_url);
       $curl = new Curl();
       $result = $curl->request($this->exchange_url);
       $this->auth_result = json_decode($result);
    }
    
   public function __destruct(){

   }
}
?>
