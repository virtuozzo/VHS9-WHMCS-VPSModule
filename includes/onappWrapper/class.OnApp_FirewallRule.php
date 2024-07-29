<?php


/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */
class NewOnApp_FirewallRule extends NewOnApp_Connection {
    
    protected $_id      = null;
    protected $_api     = null;

    public function __construct($id = null) {
        $this->_id = $id;
    }
        
    public function getList(){
        return $this->_api->sendGET('/virtual_machines/'.$this->_id.'/firewall_rules');
    }
    
    public function apply(){
        return $this->_api->sendPOST('/virtual_machines/'.$this->_id.'/update_firewall_rules');
    }
       
    public function create($params){
        return $this->_api->sendPOST('/virtual_machines/'.$this->_id.'/firewall_rules',$params);
    }
    
    public function save($rule_id,$params){
        return $this->_api->sendPUT('/virtual_machines/'.$this->_id.'/firewall_rules/'.$rule_id,$params);
    }
    
    public function delete($rule_id){
        return $this->_api->sendDELETE('/virtual_machines/'.$this->_id.'/firewall_rules/'.$rule_id);
    }
    
    public function move($rule_id,$pos){
        return $this->_api->sendGET('/virtual_machines/'.$this->_id.'/firewall_rules/'.$rule_id.'/move',array('position'=>$pos)); 
    }
    
    public function setDefault($params){
        return $this->_api->sendPUT('/virtual_machines/'.$this->_id.'/firewall_rules/update_defaults',$params);
    }

}