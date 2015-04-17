<?php if ( ! defined("BASEPATH")) exit("No direct script access allowed");
class Json extends CI_Controller 
{
    public function checksession()
    {
    $id=$this->session->userdata('id');
        echo "id=".$id."end";
    }
    function getallclient()
{
$elements=array();
$elements[0]=new stdClass();
$elements[0]->field="`social_client`.`id`";
$elements[0]->sort="1";
$elements[0]->header="ID";
$elements[0]->alias="id";

$elements=array();
$elements[1]=new stdClass();
$elements[1]->field="`social_client`.`name`";
$elements[1]->sort="1";
$elements[1]->header="Name";
$elements[1]->alias="name";

$elements=array();
$elements[2]=new stdClass();
$elements[2]->field="`social_client`.`endpoint`";
$elements[2]->sort="1";
$elements[2]->header="End Point";
$elements[2]->alias="endpoint";

$elements=array();
$elements[3]=new stdClass();
$elements[3]->field="`social_client`.`redirecturl`";
$elements[3]->sort="1";
$elements[3]->header="Redirect URL";
$elements[3]->alias="redirecturl";

$search=$this->input->get_post("search");
$pageno=$this->input->get_post("pageno");
$orderby=$this->input->get_post("orderby");
$orderorder=$this->input->get_post("orderorder");
$maxrow=$this->input->get_post("maxrow");
if($maxrow=="")
{
}
if($orderby=="")
{
$orderby="id";
$orderorder="ASC";
}
$data["message"]=$this->chintantable->query($pageno,$maxrow,$orderby,$orderorder,$search,$elements,"FROM `social_client`");
$this->load->view("json",$data);
}
public function getsingleclient()
{
$id=$this->input->get_post("id");
$data["message"]=$this->client_model->getsingleclient($id);
$this->load->view("json",$data);
}
function getallprovider()
{
$elements=array();
$elements[0]=new stdClass();
$elements[0]->field="`social_provider`.`id`";
$elements[0]->sort="1";
$elements[0]->header="ID";
$elements[0]->alias="id";

$elements=array();
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
}
if($orderby=="")
{
$orderby="id";
$orderorder="ASC";
}
$data["message"]=$this->chintantable->query($pageno,$maxrow,$orderby,$orderorder,$search,$elements,"FROM `social_provider`");
$this->load->view("json",$data);
}
public function getsingleprovider()
{
$id=$this->input->get_post("id");
$data["message"]=$this->provider_model->getsingleprovider($id);
$this->load->view("json",$data);
}
function getallclientprovider()
{
$elements=array();
$elements[0]=new stdClass();
$elements[0]->field="`social_clientprovider`.`id`";
$elements[0]->sort="1";
$elements[0]->header="ID";
$elements[0]->alias="id";

$elements=array();
$elements[1]=new stdClass();
$elements[1]->field="`social_clientprovider`.`client`";
$elements[1]->sort="1";
$elements[1]->header="Client";
$elements[1]->alias="client";

$elements=array();
$elements[2]=new stdClass();
$elements[2]->field="`social_clientprovider`.`appkey`";
$elements[2]->sort="1";
$elements[2]->header="App Key";
$elements[2]->alias="appkey";

$elements=array();
$elements[3]=new stdClass();
$elements[3]->field="`social_clientprovider`.`secretkey`";
$elements[3]->sort="1";
$elements[3]->header="Secret Key";
$elements[3]->alias="secretkey";

$elements=array();
$elements[4]=new stdClass();
$elements[4]->field="`social_clientprovider`.`provider`";
$elements[4]->sort="1";
$elements[4]->header="Provider";
$elements[4]->alias="provider";

$search=$this->input->get_post("search");
$pageno=$this->input->get_post("pageno");
$orderby=$this->input->get_post("orderby");
$orderorder=$this->input->get_post("orderorder");
$maxrow=$this->input->get_post("maxrow");
if($maxrow=="")
{
}
if($orderby=="")
{
$orderby="id";
$orderorder="ASC";
}
$data["message"]=$this->chintantable->query($pageno,$maxrow,$orderby,$orderorder,$search,$elements,"FROM `social_clientprovider`");
$this->load->view("json",$data);
}
public function getsingleclientprovider()
{
$id=$this->input->get_post("id");
$data["message"]=$this->clientprovider_model->getsingleclientprovider($id);
$this->load->view("json",$data);
}
} ?>