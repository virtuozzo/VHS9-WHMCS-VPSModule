<?php


/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */
class NewOnApp_IPAddressJoin extends NewOnApp_Connection {
    
    protected $_id      = null;
    protected $_api     = null;

    public function __construct($id = null) {
        $this->_id = $id;
    }
        
    public function getList(){
        return $this->_api->sendGET('/virtual_machines/'.$this->_id.'/ip_addresses');
    }
       
    public function assign($params){
        return $this->_api->sendPOST('/virtual_machines/'.$this->_id.'/ip_addresses',$params);
    }
    
    public function delete($ip_id){
        return $this->_api->sendDELETE('/virtual_machines/'.$this->_id.'/ip_addresses/'.$ip_id);
    }

}