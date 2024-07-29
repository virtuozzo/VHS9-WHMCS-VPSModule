<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OnApp_Buckets
 *
 * @author Marcin Piątek <marcin.pi@modulesgarden.com>
 */
class OnApp_Buckets extends NewOnApp_Connection {

    protected $_api = null;
    protected $_id  = null;

    public function __construct($id = NULL) {
        $this->_id = $id;
    }

    public function getBuckets(){ 
        return $this->_api->sendGET('/billing/buckets');
    }
    
    public function getDetails(){ 
        return $this->_api->sendGET('/billing/buckets/'.$this->_id);
    }

    
    public function create_copy(){
        return $this->_api->sendPOST('/billing/buckets/'.$this->_id.'/clone');
    }

    
     public function setBillingPlanID($id){
        $this->_id = $id;
    }
    
        public function edit($params){
        return $this->_api->sendPUT('/billing/buckets/'.$this->_id,$params);
    }
    
        public function getBaseResources() {
        return $this->_api->sendGET('/billing/buckets/'.$this->_id.'/access_controls');
    }
    
    public function getRateCards() {
        return $this->_api->sendGET('/billing/buckets/'.$this->_id.'/rate_cards');
    }
    
        public function add_base_resources($params){
        return $this->_api->sendPOST('/billing/buckets/'.$this->_id.'/access_controls',$params);
    }
    
       public function edit_base_resources($params){
        return $this->_api->sendPUT('/billing/buckets/'.$this->_id.'/access_controls',$params);
    }
    
      public function delete(){
        return $this->_api->sendDELETE('/billing/buckets/'.$this->_id);
    }
//    
//    public function create($params){
//        return $this->_api->sendPOST('/billing/user/plans',$params);

//    
//    public function delete_base_resource($id)
//    {
//        return $this->_api->sendDELETE('/billing/user/plans/'.$this->_id.'/base_resources/'.$id);
//    }
//    



    
}