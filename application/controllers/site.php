<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Site extends CI_Controller 
{
	public function __construct( )
	{
		parent::__construct();
		
		$this->is_logged_in();
	}
	function is_logged_in( )
	{
		$is_logged_in = $this->session->userdata( 'logged_in' );
		if ( $is_logged_in !== 'true' || !isset( $is_logged_in ) ) {
			redirect( base_url() . 'index.php/login', 'refresh' );
		} //$is_logged_in !== 'true' || !isset( $is_logged_in )
	}
	function checkaccess($access)
	{
		$accesslevel=$this->session->userdata('accesslevel');
		if(!in_array($accesslevel,$access))
			redirect( base_url() . 'index.php/site?alerterror=You do not have access to this page. ', 'refresh' );
	}
	public function index()
	{
		$access = array("1","2");
		$this->checkaccess($access);
		$data[ 'page' ] = 'dashboard';
		$data[ 'title' ] = 'Welcome';
		$this->load->view( 'template', $data );	
	}
	public function createuser()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['accesslevel']=$this->user_model->getaccesslevels();
		$data[ 'status' ] =$this->user_model->getstatusdropdown();
		$data[ 'logintype' ] =$this->user_model->getlogintypedropdown();
//        $data['category']=$this->category_model->getcategorydropdown();
		$data[ 'page' ] = 'createuser';
		$data[ 'title' ] = 'Create User';
		$this->load->view( 'template', $data );	
	}
	function createusersubmit()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->form_validation->set_rules('name','Name','trim|required|max_length[30]');
		$this->form_validation->set_rules('email','Email','trim|required|valid_email|is_unique[clientuser.email]');
		$this->form_validation->set_rules('password','Password','trim|required|min_length[6]|max_length[30]');
		$this->form_validation->set_rules('confirmpassword','Confirm Password','trim|required|matches[password]');
		$this->form_validation->set_rules('accessslevel','Accessslevel','trim');
		$this->form_validation->set_rules('status','status','trim|');
		$this->form_validation->set_rules('socialid','Socialid','trim');
		$this->form_validation->set_rules('logintype','logintype','trim');
		$this->form_validation->set_rules('json','json','trim');
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
			$data['accesslevel']=$this->user_model->getaccesslevels();
            $data[ 'status' ] =$this->user_model->getstatusdropdown();
            $data[ 'logintype' ] =$this->user_model->getlogintypedropdown();
            $data['category']=$this->category_model->getcategorydropdown();
            $data[ 'page' ] = 'createuser';
            $data[ 'title' ] = 'Create User';
            $this->load->view( 'template', $data );	
		}
		else
		{
            $name=$this->input->post('name');
            $email=$this->input->post('email');
            $password=$this->input->post('password');
            $accesslevel=$this->input->post('accesslevel');
            $status=$this->input->post('status');
            $socialid=$this->input->post('socialid');
            $logintype=$this->input->post('logintype');
            $json=$this->input->post('json');
//            $category=$this->input->post('category');
            
            $config['upload_path'] = './uploads/';
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
			$this->load->library('upload', $config);
			$filename="image";
			$image="";
			if (  $this->upload->do_upload($filename))
			{
				$uploaddata = $this->upload->data();
				$image=$uploaddata['file_name'];
                
                $config_r['source_image']   = './uploads/' . $uploaddata['file_name'];
                $config_r['maintain_ratio'] = TRUE;
                $config_t['create_thumb'] = FALSE;///add this
                $config_r['width']   = 800;
                $config_r['height'] = 800;
                $config_r['quality']    = 100;
                //end of configs

                $this->load->library('image_lib', $config_r); 
                $this->image_lib->initialize($config_r);
                if(!$this->image_lib->resize())
                {
                    echo "Failed." . $this->image_lib->display_errors();
                    //return false;
                }  
                else
                {
                    //print_r($this->image_lib->dest_image);
                    //dest_image
                    $image=$this->image_lib->dest_image;
                    //return false;
                }
                
			}
            
			if($this->user_model->create($name,$email,$password,$accesslevel,$status,$socialid,$logintype,$image,$json)==0)
			$data['alerterror']="New user could not be created.";
			else
			$data['alertsuccess']="User created Successfully.";
			$data['redirect']="site/viewusers";
			$this->load->view("redirect",$data);
		}
	}
    function viewusers()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['page']='viewusers';
        $data['base_url'] = site_url("site/viewusersjson");
        
		$data['title']='View Users';
		$this->load->view('template',$data);
	} 
    function viewusersjson()
	{
		$access = array("1");
		$this->checkaccess($access);
        
        
        $elements=array();
        $elements[0]=new stdClass();
        $elements[0]->field="`clientuser`.`id`";
        $elements[0]->sort="1";
        $elements[0]->header="ID";
        $elements[0]->alias="id";
        
        
        $elements[1]=new stdClass();
        $elements[1]->field="`clientuser`.`name`";
        $elements[1]->sort="1";
        $elements[1]->header="Name";
        $elements[1]->alias="name";
        
        $elements[2]=new stdClass();
        $elements[2]->field="`clientuser`.`email`";
        $elements[2]->sort="1";
        $elements[2]->header="Email";
        $elements[2]->alias="email";
        
        $elements[3]=new stdClass();
        $elements[3]->field="`clientuser`.`socialid`";
        $elements[3]->sort="1";
        $elements[3]->header="SocialId";
        $elements[3]->alias="socialid";
        
        $elements[4]=new stdClass();
        $elements[4]->field="`logintype`.`name`";
        $elements[4]->sort="1";
        $elements[4]->header="Logintype";
        $elements[4]->alias="logintype";
        
        $elements[5]=new stdClass();
        $elements[5]->field="`clientuser`.`json`";
        $elements[5]->sort="1";
        $elements[5]->header="Json";
        $elements[5]->alias="json";
       
        $elements[6]=new stdClass();
        $elements[6]->field="`accesslevel`.`name`";
        $elements[6]->sort="1";
        $elements[6]->header="Accesslevel";
        $elements[6]->alias="accesslevelname";
       
        $elements[7]=new stdClass();
        $elements[7]->field="`statuses`.`name`";
        $elements[7]->sort="1";
        $elements[7]->header="Status";
        $elements[7]->alias="status";
       
        
        $search=$this->input->get_post("search");
        $pageno=$this->input->get_post("pageno");
        $orderby=$this->input->get_post("orderby");
        $orderorder=$this->input->get_post("orderorder");
        $maxrow=$this->input->get_post("maxrow");
        if($maxrow=="")
        {
            $maxrow=20;
        }
        
        if($orderby=="")
        {
            $orderby="id";
            $orderorder="ASC";
        }
       
        $data["message"]=$this->chintantable->query($pageno,$maxrow,$orderby,$orderorder,$search,$elements,"FROM `clientuser` LEFT OUTER JOIN `logintype` ON `logintype`.`id`=`clientuser`.`logintype` LEFT OUTER JOIN `accesslevel` ON `accesslevel`.`id`=`clientuser`.`accesslevel` LEFT OUTER JOIN `statuses` ON `statuses`.`id`=`clientuser`.`status`");
        
		$this->load->view("json",$data);
	} 
    
    
	function edituser()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data[ 'status' ] =$this->user_model->getstatusdropdown();
		$data['accesslevel']=$this->user_model->getaccesslevels();
		$data[ 'logintype' ] =$this->user_model->getlogintypedropdown();
		$data['before']=$this->user_model->beforeedit($this->input->get('id'));
		$data['page']='edituser';
		$data['page2']='block/userblock';
		$data['title']='Edit User';
		$this->load->view('template',$data);
	}
	function editusersubmit()
	{
		$access = array("1");
		$this->checkaccess($access);
		
		$this->form_validation->set_rules('name','Name','trim|required|max_length[30]');
		$this->form_validation->set_rules('email','Email','trim|required|valid_email');
		$this->form_validation->set_rules('password','Password','trim|min_length[6]|max_length[30]');
		$this->form_validation->set_rules('confirmpassword','Confirm Password','trim|matches[password]');
		$this->form_validation->set_rules('accessslevel','Accessslevel','trim');
		$this->form_validation->set_rules('status','status','trim|');
		$this->form_validation->set_rules('socialid','Socialid','trim');
		$this->form_validation->set_rules('logintype','logintype','trim');
		$this->form_validation->set_rules('json','json','trim');
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
			$data[ 'status' ] =$this->user_model->getstatusdropdown();
			$data['accesslevel']=$this->user_model->getaccesslevels();
            $data[ 'logintype' ] =$this->user_model->getlogintypedropdown();
			$data['before']=$this->user_model->beforeedit($this->input->post('id'));
			$data['page']='edituser';
//			$data['page2']='block/userblock';
			$data['title']='Edit User';
			$this->load->view('template',$data);
		}
		else
		{
            
            $id=$this->input->get_post('id');
            $name=$this->input->get_post('name');
            $email=$this->input->get_post('email');
            $password=$this->input->get_post('password');
            $accesslevel=$this->input->get_post('accesslevel');
            $status=$this->input->get_post('status');
            $socialid=$this->input->get_post('socialid');
            $logintype=$this->input->get_post('logintype');
            $json=$this->input->get_post('json');
//            $category=$this->input->get_post('category');
            
            $config['upload_path'] = './uploads/';
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
			$this->load->library('upload', $config);
			$filename="image";
			$image="";
			if (  $this->upload->do_upload($filename))
			{
				$uploaddata = $this->upload->data();
				$image=$uploaddata['file_name'];
                
                $config_r['source_image']   = './uploads/' . $uploaddata['file_name'];
                $config_r['maintain_ratio'] = TRUE;
                $config_t['create_thumb'] = FALSE;///add this
                $config_r['width']   = 800;
                $config_r['height'] = 800;
                $config_r['quality']    = 100;
                //end of configs

                $this->load->library('image_lib', $config_r); 
                $this->image_lib->initialize($config_r);
                if(!$this->image_lib->resize())
                {
                    echo "Failed." . $this->image_lib->display_errors();
                    //return false;
                }  
                else
                {
                    //print_r($this->image_lib->dest_image);
                    //dest_image
                    $image=$this->image_lib->dest_image;
                    //return false;
                }
                
			}
            
            if($image=="")
            {
            $image=$this->user_model->getuserimagebyid($id);
               // print_r($image);
                $image=$image->image;
            }
            
			if($this->user_model->edit($id,$name,$email,$password,$accesslevel,$status,$socialid,$logintype,$image,$json)==0)
			$data['alerterror']="User Editing was unsuccesful";
			else
			$data['alertsuccess']="User edited Successfully.";
			
			$data['redirect']="site/viewusers";
			//$data['other']="template=$template";
			$this->load->view("redirect",$data);
			
		}
	}
	
	function deleteuser()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->user_model->deleteuser($this->input->get('id'));
//		$data['table']=$this->user_model->viewusers();
		$data['alertsuccess']="User Deleted Successfully";
		$data['redirect']="site/viewusers";
			//$data['other']="template=$template";
		$this->load->view("redirect",$data);
	}
	function changeuserstatus()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->user_model->changestatus($this->input->get('id'));
		$data['table']=$this->user_model->viewusers();
		$data['alertsuccess']="Status Changed Successfully";
		$data['redirect']="site/viewusers";
        $data['other']="template=$template";
        $this->load->view("redirect",$data);
	}
    
    
    
    public function viewclient()
    {
        $access=array("1");
        $this->checkaccess($access);
        $data["page"]="viewclient";
        $data["base_url"]=site_url("site/viewclientjson");
        $data["title"]="View client";
        $this->load->view("template",$data);
    }
    function viewclientjson()
    {
        $elements=array();
        $elements[0]=new stdClass();
        $elements[0]->field="`social_client`.`id`";
        $elements[0]->sort="1";
        $elements[0]->header="ID";
        $elements[0]->alias="id";
        
        $elements[1]=new stdClass();
        $elements[1]->field="`social_client`.`name`";
        $elements[1]->sort="1";
        $elements[1]->header="Name";
        $elements[1]->alias="name";
        
        $elements[2]=new stdClass();
        $elements[2]->field="`social_client`.`endpoint`";
        $elements[2]->sort="1";
        $elements[2]->header="End Point";
        $elements[2]->alias="endpoint";
        
        $elements[3]=new stdClass();
        $elements[3]->field="`social_client`.`redirecturl`";
        $elements[3]->sort="1";
        $elements[3]->header="Redirect URL";
        $elements[3]->alias="redirecturl";
        
        $elements[4]=new stdClass();
        $elements[4]->field="`social_client`.`curlurl`";
        $elements[4]->sort="1";
        $elements[4]->header="Curl URL";
        $elements[4]->alias="curlurl";
        
        $search=$this->input->get_post("search");
        $pageno=$this->input->get_post("pageno");
        $orderby=$this->input->get_post("orderby");
        $orderorder=$this->input->get_post("orderorder");
        $maxrow=$this->input->get_post("maxrow");
        if($maxrow=="")
        {
            $maxrow=20;
        }
        if($orderby=="")
        {
            $orderby="id";
            $orderorder="ASC";
        }
        $data["message"]=$this->chintantable->query($pageno,$maxrow,$orderby,$orderorder,$search,$elements,"FROM `social_client`");
        $this->load->view("json",$data);
    }

    public function createclient()
    {
        $access=array("1");
        $this->checkaccess($access);
        $data["page"]="createclient";
        $data["title"]="Create client";
        $this->load->view("template",$data);
    }
    public function createclientsubmit() 
    {
        $access=array("1");
        $this->checkaccess($access);
        $this->form_validation->set_rules("name","Name","trim");
        $this->form_validation->set_rules("endpoint","End Point","trim");
        $this->form_validation->set_rules("redirecturl","Redirect URL","trim");
        $this->form_validation->set_rules("curlurl","curl URL","trim");
        if($this->form_validation->run()==FALSE)
        {
        $data["alerterror"]=validation_errors();
        $data["page"]="createclient";
        $data["title"]="Create client";
        $this->load->view("template",$data);
        }
        else
        {
        $name=$this->input->get_post("name");
        $endpoint=$this->input->get_post("endpoint");
        $redirecturl=$this->input->get_post("redirecturl");
        $curlurl=$this->input->get_post("curlurl");
        if($this->client_model->create($name,$endpoint,$redirecturl,$curlurl)==0)
        $data["alerterror"]="New client could not be created.";
        else
        $data["alertsuccess"]="client created Successfully.";
        $data["redirect"]="site/viewclient";
        $this->load->view("redirect",$data);
        }
    }
    public function editclient()
    {
        $access=array("1");
        $this->checkaccess($access);
        $data["page"]="editclient";
        $data["title"]="Edit client";
        $data["before"]=$this->client_model->beforeedit($this->input->get("id"));
        $this->load->view("template",$data);
    }
    public function editclientsubmit()
    {
        $access=array("1");
        $this->checkaccess($access);
        $this->form_validation->set_rules("id","ID","trim");
        $this->form_validation->set_rules("name","Name","trim");
        $this->form_validation->set_rules("endpoint","End Point","trim");
        $this->form_validation->set_rules("redirecturl","Redirect URL","trim");
        $this->form_validation->set_rules("curlurl","curl URL","trim");
        if($this->form_validation->run()==FALSE)
        {
            $data["alerterror"]=validation_errors();
            $data["page"]="editclient";
            $data["title"]="Edit client";
            $data["before"]=$this->client_model->beforeedit($this->input->get("id"));
            $this->load->view("template",$data);
        }
        else
        {
            $id=$this->input->get_post("id");
            $name=$this->input->get_post("name");
            $endpoint=$this->input->get_post("endpoint");
            $redirecturl=$this->input->get_post("redirecturl");
            $curlurl=$this->input->get_post("curlurl");
            if($this->client_model->edit($id,$name,$endpoint,$redirecturl,$curlurl)==0)
                $data["alerterror"]="New client could not be Updated.";
            else
                $data["alertsuccess"]="client Updated Successfully.";
            $data["redirect"]="site/viewclient";
            $this->load->view("redirect",$data);
        }
    }
    public function deleteclient()
    {
        $access=array("1");
        $this->checkaccess($access);
        $this->client_model->delete($this->input->get("id"));
        $data["redirect"]="site/viewclient";
        $this->load->view("redirect",$data);
    }
    public function viewprovider()
    {
        $access=array("1");
        $this->checkaccess($access);
        $data["page"]="viewprovider";
        $data["base_url"]=site_url("site/viewproviderjson");
        $data["title"]="View provider";
        $this->load->view("template",$data);
    }
    function viewproviderjson()
    {
        $elements=array();
        $elements[0]=new stdClass();
        $elements[0]->field="`social_provider`.`id`";
        $elements[0]->sort="1";
        $elements[0]->header="ID";
        $elements[0]->alias="id";
        
        $elements[1]=new stdClass();
        $elements[1]->field="`social_provider`.`name`";
        $elements[1]->sort="1";
        $elements[1]->header="Name";
        $elements[1]->alias="name";
        
        $search=$this->input->get_post("search");
        $pageno=$this->input->get_post("pageno");
        $orderby=$this->input->get_post("orderby");
        $orderorder=$this->input->get_post("orderorder");
        $maxrow=$this->input->get_post("maxrow");
        if($maxrow=="")
        {
            $maxrow=20;
        }
        if($orderby=="")
        {
            $orderby="id";
            $orderorder="ASC";
        }
        $data["message"]=$this->chintantable->query($pageno,$maxrow,$orderby,$orderorder,$search,$elements,"FROM `social_provider`");
        $this->load->view("json",$data);
    }

    public function createprovider()
    {
        $access=array("1");
        $this->checkaccess($access);
        $data["page"]="createprovider";
        $data["title"]="Create provider";
        $this->load->view("template",$data);
    }
    public function createprovidersubmit() 
    {
        $access=array("1");
        $this->checkaccess($access);
        $this->form_validation->set_rules("name","Name","trim");
        if($this->form_validation->run()==FALSE)
        {
            $data["alerterror"]=validation_errors();
            $data["page"]="createprovider";
            $data["title"]="Create provider";
            $this->load->view("template",$data);
        }
        else
        {
            $name=$this->input->get_post("name");
            if($this->provider_model->create($name)==0)
                $data["alerterror"]="New provider could not be created.";
            else
                $data["alertsuccess"]="provider created Successfully.";
            $data["redirect"]="site/viewprovider";
            $this->load->view("redirect",$data);
        }
    }
    public function editprovider()
    {
        $access=array("1");
        $this->checkaccess($access);
        $data["page"]="editprovider";
        $data["title"]="Edit provider";
        $data["before"]=$this->provider_model->beforeedit($this->input->get("id"));
        $this->load->view("template",$data);
    }
    public function editprovidersubmit()
    {
        $access=array("1");
        $this->checkaccess($access);
        $this->form_validation->set_rules("id","ID","trim");
        $this->form_validation->set_rules("name","Name","trim");
        if($this->form_validation->run()==FALSE)
        {
            $data["alerterror"]=validation_errors();
            $data["page"]="editprovider";
            $data["title"]="Edit provider";
            $data["before"]=$this->provider_model->beforeedit($this->input->get("id"));
            $this->load->view("template",$data);
        }
        else
        {
            $id=$this->input->get_post("id");
            $name=$this->input->get_post("name");
            if($this->provider_model->edit($id,$name)==0)
                $data["alerterror"]="New provider could not be Updated.";
            else
                $data["alertsuccess"]="provider Updated Successfully.";
            $data["redirect"]="site/viewprovider";
            $this->load->view("redirect",$data);
        }
    }
    public function deleteprovider()
    {
        $access=array("1");
        $this->checkaccess($access);
        $this->provider_model->delete($this->input->get("id"));
        $data["redirect"]="site/viewprovider";
        $this->load->view("redirect",$data);
    }
    public function viewclientprovider()
    {
        $access=array("1");
        $this->checkaccess($access);
        $data["page"]="viewclientprovider";
        $data["base_url"]=site_url("site/viewclientproviderjson");
        $data["title"]="View clientprovider";
        $this->load->view("template",$data);
    }
    function viewclientproviderjson()
    {
        $elements=array();
        $elements[0]=new stdClass();
        $elements[0]->field="`social_clientprovider`.`id`";
        $elements[0]->sort="1";
        $elements[0]->header="ID";
        $elements[0]->alias="id";
        
        $elements[1]=new stdClass();
        $elements[1]->field="`social_clientprovider`.`client`";
        $elements[1]->sort="1";
        $elements[1]->header="Clientid";
        $elements[1]->alias="client";
        
        $elements[2]=new stdClass();
        $elements[2]->field="`social_clientprovider`.`appkey`";
        $elements[2]->sort="1";
        $elements[2]->header="App Key";
        $elements[2]->alias="appkey";
        
        $elements[3]=new stdClass();
        $elements[3]->field="`social_clientprovider`.`secretkey`";
        $elements[3]->sort="1";
        $elements[3]->header="Secret Key";
        $elements[3]->alias="secretkey";
        
        $elements[4]=new stdClass();
        $elements[4]->field="`social_clientprovider`.`provider`";
        $elements[4]->sort="1";
        $elements[4]->header="Provider";
        $elements[4]->alias="providerid";
        
        $elements[5]=new stdClass();
        $elements[5]->field="`social_client`.`name`";
        $elements[5]->sort="1";
        $elements[5]->header="Client";
        $elements[5]->alias="clientname";
        
        $elements[6]=new stdClass();
        $elements[6]->field="`social_provider`.`name`";
        $elements[6]->sort="1";
        $elements[6]->header="Provider";
        $elements[6]->alias="providername";
        
        $search=$this->input->get_post("search");
        $pageno=$this->input->get_post("pageno");
        $orderby=$this->input->get_post("orderby");
        $orderorder=$this->input->get_post("orderorder");
        $maxrow=$this->input->get_post("maxrow");
        if($maxrow=="")
        {
            $maxrow=20;
        }
        if($orderby=="")
        {
            $orderby="id";
            $orderorder="ASC";
        }
        $data["message"]=$this->chintantable->query($pageno,$maxrow,$orderby,$orderorder,$search,$elements,"FROM `social_clientprovider` LEFT OUTER JOIN `social_client` ON `social_client`.`id`=`social_clientprovider`.`client` LEFT OUTER JOIN `social_provider` ON `social_provider`.`id`=`social_clientprovider`.`provider`");
        $this->load->view("json",$data);
    }

    public function createclientprovider()
    {
        $access=array("1");
        $this->checkaccess($access);
        $data['client']=$this->client_model->getclientdropdown();
        $data['provider']=$this->provider_model->getproviderdropdown();
        $data["page"]="createclientprovider";
        $data["title"]="Create clientprovider";
        $this->load->view("template",$data);
    }
    public function createclientprovidersubmit() 
    {
        $access=array("1");
        $this->checkaccess($access);
        $this->form_validation->set_rules("client","Client","trim");
        $this->form_validation->set_rules("appkey","App Key","trim");
        $this->form_validation->set_rules("secretkey","Secret Key","trim");
        $this->form_validation->set_rules("provider","Provider","trim");
        if($this->form_validation->run()==FALSE)
        {
            $data["alerterror"]=validation_errors();
            $data['client']=$this->client_model->getclientdropdown();
            $data['provider']=$this->provider_model->getproviderdropdown();
            $data["page"]="createclientprovider";
            $data["title"]="Create clientprovider";
            $this->load->view("template",$data);
        }
        else
        {
            $client=$this->input->get_post("client");
            $appkey=$this->input->get_post("appkey");
            $secretkey=$this->input->get_post("secretkey");
            $provider=$this->input->get_post("provider");
            if($this->clientprovider_model->create($client,$appkey,$secretkey,$provider)==0)
                $data["alerterror"]="New clientprovider could not be created.";
            else
                $data["alertsuccess"]="clientprovider created Successfully.";
            $data["redirect"]="site/viewclientprovider";
            $this->load->view("redirect",$data);
        }
    }
    public function editclientprovider()
    {
        $access=array("1");
        $this->checkaccess($access);
        $data["page"]="editclientprovider";
        $data["title"]="Edit clientprovider";
        $data['client']=$this->client_model->getclientdropdown();
        $data['provider']=$this->provider_model->getproviderdropdown();
        $data["before"]=$this->clientprovider_model->beforeedit($this->input->get("id"));
        $this->load->view("template",$data);
    }
    public function editclientprovidersubmit()
    {
        $access=array("1");
        $this->checkaccess($access);
        $this->form_validation->set_rules("id","ID","trim");
        $this->form_validation->set_rules("client","Client","trim");
        $this->form_validation->set_rules("appkey","App Key","trim");
        $this->form_validation->set_rules("secretkey","Secret Key","trim");
        $this->form_validation->set_rules("provider","Provider","trim");
        if($this->form_validation->run()==FALSE)
        {
            $data["alerterror"]=validation_errors();
            $data["page"]="editclientprovider";
            $data["title"]="Edit clientprovider";
            $data['client']=$this->client_model->getclientdropdown();
            $data['provider']=$this->provider_model->getproviderdropdown();
            $data["before"]=$this->clientprovider_model->beforeedit($this->input->get("id"));
            $this->load->view("template",$data);
        }
        else
        {
            $id=$this->input->get_post("id");
            $client=$this->input->get_post("client");
            $appkey=$this->input->get_post("appkey");
            $secretkey=$this->input->get_post("secretkey");
            $provider=$this->input->get_post("provider");
            if($this->clientprovider_model->edit($id,$client,$appkey,$secretkey,$provider)==0)
            $data["alerterror"]="New clientprovider could not be Updated.";
            else
            $data["alertsuccess"]="clientprovider Updated Successfully.";
            $data["redirect"]="site/viewclientprovider";
            $this->load->view("redirect",$data);
        }
    }
    public function deleteclientprovider()
    {
        $access=array("1");
        $this->checkaccess($access);
        $this->clientprovider_model->delete($this->input->get("id"));
        $data["redirect"]="site/viewclientprovider";
        $this->load->view("redirect",$data);
    }

}
?>
