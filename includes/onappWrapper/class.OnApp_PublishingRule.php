<?php


/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */
class NewOnApp_PublishingRule extends NewOnApp_Connection {
    
    protected $_id      = null;
    protected $_api     = null;

    public function __construct($id = null) {
        $this->_id = $id;
    }
        
    public function getList(){
        return $this->_api->sendGET('/virtual_machines/'.$this->_id.'/publications');
    }   
       
    public function create($params){
        return $this->_api->sendPOST('/virtual_machines/'.$this->_id.'/publications',$params);
    }
    
    public function delete($rule_id){
        return $this->_api->sendDELETE('/virtual_machines/'.$this->_id.'/publications/'.$rule_id);
    }

}