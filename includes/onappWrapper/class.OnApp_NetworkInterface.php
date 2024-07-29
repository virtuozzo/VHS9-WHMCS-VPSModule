<?php


/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */
class NewOnApp_NetworkInterface extends NewOnApp_Connection {
    
    protected $_id      = null;
    protected $_api     = null;

    public function __construct($id = null) {
        $this->_id = $id;
    }
            
    public function getList(){
        return $this->_api->sendGET('/virtual_machines/'.$this->_id.'/network_interfaces');
    }
    
    public function getDetails($id){
        return $this->_api->sendGET('/virtual_machines/'.$this->_id.'/network_interfaces/'.$id);
    }
    
    public function addNetwork($params){
        return $this->_api->sendPOST('/virtual_machines/'.$this->_id.'/network_interfaces',$params);
    }
    
    public function save($id,$params){
        return $this->_api->sendPUT('/virtual_machines/'.$this->_id.'/network_interfaces/'.$id,$params);
    }
    
    public function delete($id){
        return $this->_api->sendDELETE('/virtual_machines/'.$this->_id.'/network_interfaces/'.$id);
    }
    
    public function setID($id){
        $this->_id = $id;
    }
    
    public function getUsageChart($id){
        $this->_api->_unsetJSON();
   
        $result = $this->_api->sendGETWithoutJSON('/virtual_machines/' . $this->_id . '/network_interfaces/'.$id.'/usage.chart?use_local_time=1');
        
        $this->_api->_setJSON();
        
        
        return $result;
    }

}