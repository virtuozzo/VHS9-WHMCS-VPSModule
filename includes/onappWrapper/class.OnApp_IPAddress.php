<?php


/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */
class NewOnApp_IPAddress extends NewOnApp_Connection {
    
    protected $_id      = null;
    protected $_api     = null;

    public function __construct($id = null) {
        $this->_id = $id;
    }
        
    public function getList(){
        return $this->_api->sendGET('/settings/networks/'.$this->_id.'/ip_addresses');
    }
    
    public function getFreeList($vm_id,$interface_id){
        return $this->_api->sendGET('/virtual_machines/'.$vm_id.'/network_interfaces/'.$interface_id.'/ip_addresses',array('used_ip'=>'no', 'show_only_user_ips' => 0));
    }
       
    public function create($params){
        return $this->_api->sendPOST('/settings/networks/'.$this->_id.'/ip_addresses',$params);
    }
    
    public function edit($ip_id,$params){
        return $this->_api->sendPUT('/settings/networks/'.$this->_id.'/ip_addresses/'.$ip_id,$params);
    }
    
    public function delete(){
        return $this->_api->sendDELETE('/settings/networks/'.$this->id.'/ip_addresses/'.$ip_id);
    }

}