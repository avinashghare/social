<?php
if ( !defined( "BASEPATH" ) )
exit( "No direct script access allowed" );
class clientprovider_model extends CI_Model
{
public function create($client,$appkey,$secretkey,$provider)
{
$data=array("client" => $client,"appkey" => $appkey,"secretkey" => $secretkey,"provider" => $provider);
$query=$this->db->insert( "social_clientprovider", $data );
$id=$this->db->insert_id();
if(!$query)
return  0;
else
return  $id;
}
public function beforeedit($id)
{
$this->db->where("id",$id);
$query=$this->db->get("social_clientprovider")->row();
return $query;
}
function getsingleclientprovider($id){
$this->db->where("id",$id);
$query=$this->db->get("social_clientprovider")->row();
return $query;
}
public function edit($id,$client,$appkey,$secretkey,$provider)
{
$data=array("client" => $client,"appkey" => $appkey,"secretkey" => $secretkey,"provider" => $provider);
$this->db->where( "id", $id );
$query=$this->db->update( "social_clientprovider", $data );
return 1;
}
public function delete($id)
{
$query=$this->db->query("DELETE FROM `social_clientprovider` WHERE `id`='$id'");
return $query;
}
}
?>
