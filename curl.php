class Curl{

    public $result;
    public function __construct(){

    }

    public function request($url, $data="", $method="get", $headers=""){
        $ch = curl_init();
        // this is autologiM USING CURL POST
        // avoiding the redirect to gotos site where it asks for email and password and redirects back to the URL with a code
        curl_setopt($ch, CURLOPT_URL, $url); 
        if($method == "post"){
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HEADER, true);
        }
        if($headers)
            curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);          

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $this->result =  (string) curl_exec($ch);
        curl_close($ch);
        return $this->result;
    }

    public function __destruct(){

    }
}
