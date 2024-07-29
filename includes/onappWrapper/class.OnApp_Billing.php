<?php


/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */
class NewOnApp_Billing extends NewOnApp_Connection {

    protected $_api = null;
    protected $_id  = null;

    public function __construct($id = NULL) {
        $this->_id = $id;
    }

    public function getDetails(){ 
        return $this->_api->sendGET('/billing/user/plans/'.$this->_id);
    }

    public function getPlans() {
        return $this->_api->sendGET('/billing/user/plans');
    }
    
    public function create($params){
        return $this->_api->sendPOST('/billing/user/plans',$params);
    }
    
    public function create_copy(){
        return $this->_api->sendPOST('/billing/user/plans/'.$this->_id.'/create_copy');
    }
    
    public function delete(){
        
        return $this->_api->sendDELETE('/billing/user/plans/'.$this->_id);
    }
    
    public function edit($params){
        return $this->_api->sendPUT('/billing/user/plans/'.$this->_id,$params);
    }
    
    public function edit_base_resources($id,$params){
        return $this->_api->sendPUT('/billing/user/resources/'.$id,$params);
    }
    
    public function add_base_resources($params){
        return $this->_api->sendPOST('/billing/user/plans/'.$this->_id.'/resources',$params);
    }
    
    public function delete_base_resource($id)
    {
        return $this->_api->sendDELETE('/billing/user/plans/'.$this->_id.'/base_resources/'.$id);
    }
    
    public function setBillingPlanID($id){
        $this->_id = $id;
    }
    
    public function getBaseResources() {
        return $this->_api->sendGET('/billing/user/plans/'.$this->_id.'/resources');
    }
    
}