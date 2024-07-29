<?php


/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */
class NewOnApp_Hypervisor extends NewOnApp_Connection {
    
    protected $_id      = null;
    protected $_api     = null;
    protected $_zoneid  = null;

    public function __construct($id = null) {
        $this->_id = $id;
    }
        
    public function getHypervisors(){
        return $this->_api->sendGET('/settings/hypervisors');
    }
    
    public function getUnsignedHypervisors(){
        return $this->_api->sendGET('/hypervisors/not_grouped');
    }
    
    public function details(){
        return $this->_api->sendGET('/settings/hypervisors/'.$this->_id);
    }
    
    public function create($params){
        return $this->_api->sendPOST('/settings/hypervisors',$params);
    }
    
    public function createVMware($params){
        return $this->_api->sendPOST('/settings/hypervisors',$params);
    }
    
    public function edit($params){
        return $this->_api->sendPUT('/settings/hypervisors/'.$this->_id,$params);
    }
    
    public function editVMware($params){
        return $this->_api->sendPUT('/settings/hypervisors',$params);
    }
    
    public function reboot(){
        return $this->_api->sendPOST('/settings/hypervisors/'.$this->_id.'/reboot');
    }
    
    public function delete(){
        return $this->_api->sendDELETE('/settings/hypervisors/'.$this->_id);
    }
    
    public function listRunningHPV(){
        return $this->_api->sendGET('/hypervisors/'.$this->_id.'/virtual_machines');
    }
    
    public function joinesDataStore(){
        return $this->_api->sendGET('/settings/hypervisors/'.$this->_id.'/data_store_joins');
    }
    
    public function attachedDataStore($zone_id){
        return $this->_api->sendGET('/settings/hypervisor_zones/'.$zoneid.'/data_stores');
    }
    
    public function addDataStore($params){
        return $this->_api->sendPOST('/settings/hypervisors/'.$this->_id.'/data_store_joins',$params);
    }
    
    public function removeDataStore($data_store_id){
        return $this->_api->sendDELETE('/settings/hypervisors/'.$this->_id.'/data_store_joins/'.$data_store_id);
    }
    
    public function networkJoins(){
        return $this->_api->sendGET('/settings/hypervisors/'.$this->_id.'/network_joins');
    }
    
    public function addNetwork($params){
        return $this->_api->sendPOST('/settings/hypervisors/'.$this->_id.'/network_joins',$params);
    }
    
    public function removeNetwork($network_id){
        return $this->_api->sendDELETE('/settings/hypervisors/'.$this->_id.'/network_joins/'.$network_id);
    }
    
}