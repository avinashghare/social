<?php
if ( !defined( "BASEPATH" ) )
exit( "No direct script access allowed" );
class client_model extends CI_Model
{
    public function create($name,$endpoint,$redirecturl,$curlurl)
    {
        $data=array(
            "name" => $name,
            "endpoint" => $endpoint,
            "redirecturl" => $redirecturl,
            "curlurl" => $curlurl
        );
        $query=$this->db->insert( "social_client", $data );
        $id=$this->db->insert_id();
        if(!$query)
        return  0;
        else
        return  $id;
    }
    public function beforeedit($id)
    {
        $this->db->where("id",$id);
        $query=$this->db->get("social_client")->row();
        return $query;
    }
    function getsingleclient($id)
    {
        $this->db->where("id",$id);
        $query=$this->db->get("social_client")->row();
        return $query;
    }
    public function edit($id,$name,$endpoint,$redirecturl,$curlurl)
    {
        $data=array(
            "name" => $name,
            "endpoint" => $endpoint,
            "redirecturl" => $redirecturl,
            "curlurl" => $curlurl
        );
        $this->db->where( "id", $id );
        $query=$this->db->update( "social_client", $data );
        return 1;
    }
    public function delete($id)
    {
        $query=$this->db->query("DELETE FROM `social_client` WHERE `id`='$id'");
        return $query;
    }
    
    public function getclientdropdown()
	{
		$query=$this->db->query("SELECT * FROM `social_client` ORDER BY `id` ASC")->result();
		$return=array(
		);
		foreach($query as $row)
		{
			$return[$row->id]=$row->name;
		}
		
		return $return;
	}
    
}
?>
