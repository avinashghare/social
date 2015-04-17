<?php
if ( !defined( "BASEPATH" ) )
exit( "No direct script access allowed" );
class provider_model extends CI_Model
{
    public function create($name)
    {
        $data=array("name" => $name);
        $query=$this->db->insert( "social_provider", $data );
        $id=$this->db->insert_id();
        if(!$query)
        return  0;
        else
        return  $id;
    }
    public function beforeedit($id)
    {
        $this->db->where("id",$id);
        $query=$this->db->get("social_provider")->row();
        return $query;
    }
    function getsingleprovider($id)
    {
        $this->db->where("id",$id);
        $query=$this->db->get("social_provider")->row();
        return $query;
    }
    public function edit($id,$name)
    {
        $data=array("name" => $name);
        $this->db->where( "id", $id );
        $query=$this->db->update( "social_provider", $data );
        return 1;
    }
    public function delete($id)
    {
        $query=$this->db->query("DELETE FROM `social_provider` WHERE `id`='$id'");
        return $query;
    }
    
    public function getproviderdropdown()
	{
		$query=$this->db->query("SELECT * FROM `social_provider` ORDER BY `id` ASC")->result();
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
