<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class HAuth extends CI_Controller {

	public function index()
	{
		$this->load->view('hauth/home');
	}

	public function login()
	{
        $provider=$this->input->get('provider');
        $clientid=$this->input->get('clientid');
		log_message('debug', "controllers.HAuth.login($provider) called");

        
		try
		{
			log_message('debug', 'controllers.HAuth.login: loading HybridAuthLib');
			$this->load->library('HybridAuthLib');
            if($provider=="Facebook")
            {
                $providerid=1;
            }
            else if($provider=="Google")
            {
                $providerid=2;
            }
            $querycount=$this->db->query("SELECT * FROM `social_clientprovider` WHERE `client`='$clientid' AND `provider`='$providerid'");
            $queryrow=$this->db->query("SELECT * FROM `social_clientprovider` WHERE `client`='$clientid' AND `provider`='$providerid'")->row();
            //$query=$this->db->query("SELECT * FROM `social_clientprovider` WHERE `client`='$clientid' AND `provider`='$providerid'");
            $this->config->load("hybridauthlib");
            $newconfig=$this->config->item("providers");
            //print_r($queryrow);
            //echo $querycount;
            //return 0;
            if($querycount->num_rows() == 0)
            {
            }
            else
            {
                //$queryrow=$query->row();
                $appid=$queryrow->appkey;
                $secretid=$queryrow->secretkey;
                if($providerid==1)
                {
                    $newconfig=$this->config->item("providers");
                    $newconfig["Facebook"]["keys"]["id"]="$appid";
                    $newconfig["Facebook"]["keys"]["secret"]="$secretid";
                    $this->config->set_item("providers",$newconfig);
                }
                else if($providerid==2)
                {
                    $newconfig=$this->config->item("providers");
                    $newconfig["Google"]["keys"]["id"]="$appid";
                    $newconfig["Google"]["keys"]["secret"]="$secretid";
                    $this->config->set_item("providers",$newconfig);
                }
            }
            //print_r($newconfig);
            //return 0;
            
			if ($this->hybridauthlib->providerEnabled($provider))
			{
				log_message('debug', "controllers.HAuth.login: service $provider enabled, trying to authenticate.");
				$service = $this->hybridauthlib->authenticate($provider);

				if ($service->isUserConnected())
				{
					log_message('debug', 'controller.HAuth.login: user authenticated.');

					$user_profile = $service->getUserProfile();
                    $clientquery=$this->db->query("SELECT * FROM `social_client` WHERE `id`='$clientid'")->row();
                    $curlurl=$clientquery->curlurl;
                    $redirecturl=$clientquery->redirecturl;

                    $endurl=$clientquery->endpoint;
$endurl="http://".$endurl;
                    $url = $curlurl;

//                    $url = base_url("email/forgetpasswordemail.php");
                    
                    
                    
                    $fields = array(
                                            'displayName'=> urlencode($user_profile->displayName),
                                            'email'=> urlencode($user_profile->email),
                                            'photoURL'=> urlencode($user_profile->photoURL),
                                            'identifier'=> urlencode($user_profile->identifier),
                                            'birthYear'=> urlencode($user_profile->birthYear),
                                            'birthMonth'=> urlencode($user_profile->birthMonth),
                                            'birthDay'=> urlencode($user_profile->birthDay),
                                            'address'=> urlencode($user_profile->address),
                                            'region'=> urlencode($user_profile->region),
                                            'city'=> urlencode($user_profile->city),
                                            'country'=> urlencode($user_profile->country),
                                            'provider' => urlencode($provider)
                                    );

                    //url-ify the data for the POST
                    foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
                    rtrim($fields_string, '&');
//echo $url;
                    //open connection
                    $ch = curl_init();

                    //set the url, number of POST vars, POST data
                    curl_setopt($ch,CURLOPT_URL, $url);
                    curl_setopt($ch,CURLOPT_POST, count($fields));
                    curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    //execute post
                    $userid = curl_exec($ch);
                    //echo $userid;
//                    $sociallogin=$this->user_model->sociallogin($user_profile,$provider);
                    $endurl=urlencode($endurl);
                    $redirecturl=$redirecturl."?id=".$userid."&endurl=".$endurl;
//print_r($newconfig);
            //return 0;
                    //echo $redirecturl;
                    redirect($redirecturl);

					// $data['message'] = $sociallogin;

					// $this->load->view('json',$data);
				}
				else // Cannot authenticate user
				{
					show_error('Cannot authenticate user');
				}
			}
			else // This service is not enabled.
			{
				log_message('error', 'controllers.HAuth.login: This provider is not enabled ('.$provider.')');
				show_404($_SERVER['REQUEST_URI']);
			}
		}
		catch(Exception $e)
		{
			$error = 'Unexpected error';
			switch($e->getCode())
			{
				case 0 : $error = 'Unspecified error.'; break;
				case 1 : $error = 'Hybriauth configuration error.'; break;
				case 2 : $error = 'Provider not properly configured.'; break;
				case 3 : $error = 'Unknown or disabled provider.'; break;
				case 4 : $error = 'Missing provider application credentials.'; break;
				case 5 : log_message('debug', 'controllers.HAuth.login: Authentification failed. The user has canceled the authentication or the provider refused the connection.');
				         //redirect();
				         if (isset($service))
				         {
				         	log_message('debug', 'controllers.HAuth.login: logging out from service.');
				         	$service->logout();
				         }
				         show_error('User has cancelled the authentication or the provider refused the connection.');
				         break;
				case 6 : $error = 'User profile request failed. Most likely the user is not connected to the provider and he should to authenticate again.';
				         break;
				case 7 : $error = 'User not connected to the provider.';
				         break;
			}

			if (isset($service))
			{
				$service->logout();
			}

			log_message('error', 'controllers.HAuth.login: '.$error);
			show_error('Error authenticating user.');
		}
	}

	public function endpoint()
	{

		log_message('debug', 'controllers.HAuth.endpoint called.');
		log_message('info', 'controllers.HAuth.endpoint: $_REQUEST: '.print_r($_REQUEST, TRUE));

		if ($_SERVER['REQUEST_METHOD'] === 'GET')
		{
			log_message('debug', 'controllers.HAuth.endpoint: the request method is GET, copying REQUEST array into GET array.');
			$_GET = $_REQUEST;
		}

		log_message('debug', 'controllers.HAuth.endpoint: loading the original HybridAuth endpoint script.');
		require_once APPPATH.'/third_party/hybridauth/index.php';

	}
	public function posttweet()
    {
        $twitter = $this->hybridauthlib->authenticate("Twitter");
        $message=$this->input->get_post("message");
        $post=$this->input->get('id');
        $project=$this->input->get('project');
        $twitterid = $twitter->getUserProfile();
        $twitterid = $twitterid->identifier;



        $data["message"]=$twitter->api()->post("statuses/update.json?status=$message");
        if(isset($data["message"]->id_str))
        {
            // $this->userpost_model->addpostid($data["message"]->id_str,$post);
            $this->user_model->updatetweet($data["message"]->id_str,$project,$twitterid);
            redirect($this->input->get_post("returnurl"));
            $this->load->view("json",$data);
        }
        else
        {
            redirect($this->input->get_post("returnurl"));
		  $this->load->view("json",$data);
        }

    }
    public function postfb()
    {
        $facebook = $this->hybridauthlib->authenticate("Facebook");
        $message=$this->input->get_post("message");
        $image=$this->input->get_post("image");
        $link=$this->input->get_post("link");
        $project=$this->input->get_post("project");
//        echo "out".$message;
        $facebookid = $facebook->getUserProfile();
        $facebookid = $facebookid->identifier;



        if($image=="")
        {
            $data["message"]=$facebook->api()->api("v2.2/me/feed", "post", array(
                "message" => "$message",
                "link"=>"$link"
            ));

            if(isset($data["message"]['id']))
            {
//                echo "hauth".$project;
                $this->user_model->updatepost($data["message"]['id'],$project,$facebookid);
                redirect($this->input->get_post("returnurl"));
//							$this->load->view("json",$data);
            }
            else
            {
                redirect($this->input->get_post("returnurl"));
//							$this->load->view("json",$data);
            }
        }
        else
        {
            $data["message"]=$facebook->api()->api("v2.2/me/feed", "post", array(
                "message" => "$message",
                "picture"=> "$image",
                "link"=>"$link"
            ));

//            print_r($data['message']["id"]);

            if(isset($data["message"]["id"]))
            {
                
                redirect($this->input->get_post("returnurl"));

            $this->load->view("json",$data);
            }
            else
            {

                
                redirect($this->input->get_post("returnurl"));
                $this->load->view("json",$data);
            }
        }

    }

}

/* End of file hauth.php */
/* Location: ./application/controllers/hauth.php */