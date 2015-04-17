<?php if ( ! defined("BASEPATH")) exit("No direct script access allowed");
class Email extends CI_Controller
{
    public function forgotpassword()
    {
        
        //set POST variables
        $email=$this->input->get_post('email');
        $userid=$this->user_model->getidbyemail($email);
//        echo "userid=".$userid."end";
        if($userid=="")
        {
            $data['message']="Not A Valid Email.";
            $this->load->view("json",$data);
        }
        else
        {
        $hashvalue=base64_encode ($userid."&powerforone");
        $link="<a href='http://www.powerforone.org/#/resetpswd/$hashvalue'>Click here </a>";
//        $normalbyhash=base64_decode ($hashvalue);
//        $returnvalue=explode("&",$normalbyhash);
//        print_r($returnvalue);
//        echo $returnvalue[0]."<br>";
//        echo $normalbyhash."<br>";
//        echo $userid;
//        echo $hashvalue;
//        echo $link;
        
        
        $url = base_url("email/forgetpasswordemail.php");
        $fields = array(
                                'userid' => urlencode($userid),
                                'email' => urlencode($email),
                                'link' => urlencode($link),
                                'hashvalue' => urlencode($hashvalue)
                        );

        //url-ify the data for the POST
        foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
        rtrim($fields_string, '&');

        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, count($fields));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

        //execute post
        $result = curl_exec($ch);
        $data['message']=$result;
        $this->load->view("json",$data);
        //close connection
        curl_close($ch);
    }
    }

    
    public function welcomemail()
    {
        
        $url = base_url("email/welcomeemail.php");
        $fields = array(
                        );

        //url-ify the data for the POST
        foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
        rtrim($fields_string, '&');

        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, count($fields));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

        //execute post
        $result = curl_exec($ch);

        //close connection
        curl_close($ch);
    }
    
    public function transactionsuccess()
    {
        
        $url = base_url("email/transactionsuccessemail.php");
        $fields = array(
                        );

        //url-ify the data for the POST
        foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
        rtrim($fields_string, '&');

        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, count($fields));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

        //execute post
        $result = curl_exec($ch);

        //close connection
        curl_close($ch);
    }
    
} 
?>
