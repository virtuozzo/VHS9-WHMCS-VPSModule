<?php


/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */
class NewOnApp_HypervisorZone extends NewOnApp_Connection {
    
    protected $_api     = null;
    protected $_id      = null;

    public function __construct($id = null) {
        $this->_id = $id;
    }
    
    public function getZone(){
        return $this->_api->sendGET('/settings/hypervisor_zones/'.$this->_id);
    }
    
    public function getZones(){
        return $this->_api->sendGET('/settings/hypervisor_zones');
    }
    
    public function create($params){
        return $this->_api->sendPOST('/settings/hypervisor_zones',$params);
    }
    
    public function edit($params){
        return $this->_api->sendPUT('/settings/hypervisor_zones/'.$this->_id,$params);
    }
    
    public function delete(){
        return $this->_api->sendDELETE('/settings/hypervisor_zones/'.$this->_id,$params);
    } 
    
    public function lisHPV(){
        return $this->_api->sendGET('/settings/hypervisor_zones/'.$this->_id.'/hypervisors');
    }
    
    public function attachHPV($hpv_id){
        return $this->_api->sendPOST('/settings/hypervisor_zones/'.$this->_id.'/hypervisors/'.$hpv_id.'/attach');
    }
    
    public function removeHPV($hpv_id){
        return $this->_api->sendDELETE('/settings/hypervisor_zones/'.$this->_id.'/hypervisors/'.$hpv_id.'/detach');
    }
    
    public function lisDataStore(){
        return $this->_api->sendGET('/settings/hypervisor_zones/'.$this->_id.'/data_store_joins');
    }
        
    public function addDataStore($params){
        return $this->_api->sendPOST('/settings/hypervisor_zones/'.$this->_id.'/data_store_joins',$params);
    } 
    
    public function removeDataStore($data_store_id){
        return $this->_api->sendDELETE('/settings/hypervisor_zones/'.$this->_id.'/data_store_joins/'.$data_store_id);
    }
     
    public function networkJoins(){
        return $this->_api->sendGET('/settings/hypervisor_zones/'.$this->_id.'/network_joins');
    }
    
    public function attachNetwork($params){
        return $this->_api->sendPOST('/settings/hypervisor_zones/'.$this->_id.'/network_joins',$params);
    }
    
    public function removeNetwork($network_id){
        return $this->_api->sendDELETE('/settings/hypervisor_zones/'.$this->_id.'/network_joins/'.$network_id);
    }
    
    public function getBestHypervisor()
    {
        $res = $this->_api->sendGET('/settings/hypervisor_zones/' . $this->_id . '/hypervisors');
        $list = [];
        $prev = 0;
        foreach($res as $val)
        {
            if($val['hypervisor']['enabled'] == true)
            {
                if($prev < $val['hypervisor']['used_cpu_resources'])
                {
                    array_push($list, $val['hypervisor']['id']);
                }
                else 
                {
                    array_unshift($list, $val['hypervisor']['id']);
                }
                $prev = $val['hypervisor']['used_cpu_resources'];
                $list = array_values($list);
            }
            
        }

        return array_shift($list);
       
    }
    
    /**
     * Federated stuff
     * @marcin.do@modulesgarden.com modifications
     */

    public function getFederatedList()
    {
        return $this->_api->sendGET('/federation/hypervisor_zones/unsubscribed');
    }

    public function getDataStores(){
        return $this->_api->sendGET('/settings/hypervisor_zones/'.$this->_id.'/data_stores');
    }

    public function getId() {
        return $this->_id;
    }

    public function setId($id) {
        $this->_id = $id;
        return $this;
    }


}