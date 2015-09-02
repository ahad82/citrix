<?php
define ("API_KEY", "");
define ("REDIRECT_URL","");
define ("AUTH_AUTOLOGIN_URL","https://developer.citrixonline.com/oauth/g2t/authorize.php");
define ("AUTH_EXCHANGE_URL", "https://api.citrixonline.com/oauth/access_token?grant_type=authorization_code&code=<CODE>&client_id=" . API_KEY);
define ("MANAGE_TRAINING_URL","https://api.citrixonline.com/G2T/rest/organizers/<ORGANIZERKEY>/trainings");

class GotoTraining extends OAuth{

    public $manage_training_url;
    public $training_result;
    public $error_list = array("AuthFailure", "AccessDenied", "ExpiredToken", "InternalError", "InvalidRequest", "InvalidMethod", "MissingToken", "NoSuchTraining", "InvalidToken");

    public function __construct($url = MANAGE_TRAINING_URL){
        $this->manage_training_url = $url;
        parent::__construct();
    }
    
    /**
    Arguement List for goto CreateTraining service
    [name] => Representational State Transfer 101
    [description] => The REST-ful way to APIs.
    [timeZone] => America/Los_Angeles
    [times] => Array
        (
            [0] => stdClass Object
                (
                    [startDate] => 2011-09-08T18:25:00Z
                    [endDate] => 2011-09-08T19:25:00Z
                )

            [1] => stdClass Object
                (
                    [startDate] => 2011-09-09T18:25:00Z
                    [endDate] => 2011-09-09T19:25:00Z
                )

        )

    [registrationSettings] => stdClass Object
        (
            [disableWebRegistration] => false
            [disableConfirmationEmail] => false
        )

    [organizers] => Array
        (
            [0] => 6512477
            [1] => 38712
            [2] => 9876466
        ) 
    */
    public function createTraining($name, $desc, $times){

        $registrationSettings["disableWebRegistration"] = "false";
        $registrationSettings["disableConfirmationEmail"] = "false";

        $json["name"] = $name;
        $json["description"] = $desc;
        $json["timeZone"] = "Australia/Sydney";
        $json["times"] = $times;//array for startDate, endDate
        $json["registrationSettings"] = $registrationSettings;
        $json["organizers"][0] =  $this->auth_result->organizer_key;

        $this->manage_training_url = str_replace("<ORGANIZERKEY>", $this->auth_result->organizer_key, $this->manage_training_url);
        $json = json_encode($json);
        //$post_data[] = "Authorization:OAuth oauth_token=" . $this->auth_result->access_token;
        //$this->manage_training_url = $this->manage_training_url . "?oauth_token=" . $this->auth_result->access_token;

        $headers =  array(
               'Accept: application/json',
              'Content-Type: application/json', 
              'Authorization: OAuth oauth_token=' . $this->auth_result->access_token
              );

        //$this->manage_training_url = $this->manage_training_url . "?oauth_token=" . $this->auth_result->access_token;
        $curl = new Curl();
        $this->training_result = $curl->request($this->manage_training_url, $json, "post", $headers);
        $arr = explode("\n", $this->training_result);
        $this->webCode = trim($arr[count($arr)-1], '"');

        $this->checkError();

        return $this->webCode;
    }

    public function checkError(){
    
        foreach($this->error_list as $val)
        {
            if(strstr($this->training_result, $val))
                $this->webCode = $val;
        }
        return 0;
    }
}
?>
