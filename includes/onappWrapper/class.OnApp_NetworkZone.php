<?php


/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */
class NewOnApp_NetworkZone extends NewOnApp_Connection {
    
    protected $_id      = null;
    protected $_api     = null;

    public function __construct($id = null) {
        $this->_id = $id;
    }

    public function getList(){
        return $this->_api->sendGET('/settings/network_zones');
    }
    
    public function getDetails(){
        return $this->_api->sendGET('/settings/network_zones/'.$this->_id);
    }
    
    public function create($params){
        return $this->_api->sendPOST('/settings/network_zones',$params);
    }
    
    public function edit($params){
        return $this->_api->sendPUT('/settings/network_zones/'.$this->_id,$params);
    }
    
    public function delete(){
        return $this->_api->sendDELETE('/settings/network_zones/'.$this->id);
    }
    
    public function attachNetwork($network_id){
        return $this->_api->sendPOST('/settings/network_zones/'.$this->id.'/networks/'.$network_id.'/attach');
    }
    
    public function removeNetwork($network_id){
        return $this->_api->sendPOST('/settings/network_zones/'.$this->id.'/networks/'.$network_id.'/detach');
    }
    
}