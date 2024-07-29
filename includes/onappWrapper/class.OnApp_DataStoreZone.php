<?php


/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */
class NewOnApp_DataStoreZone extends NewOnApp_Connection {
    
    protected $_id      = null;
    protected $_api     = null;

    public function __construct($id = null) {
        $this->_id = $id;
    }

    public function getList(){
        return $this->_api->sendGET('/settings/data_store_zones');
    }
    
    public function getDetails(){
        return $this->_api->sendGET('/settings/data_store_zones/'.$this->_id);
    }
    
    public function create($params){
        return $this->_api->sendPOST('/settings/data_store_zones',$params);
    }
    
    public function edit($params){
        return $this->_api->sendPUT('/settings/data_store_zones/'.$this->_id,$params);
    }
    
    public function delete(){
        return $this->_api->sendDELETE('/settings/data_store_zones/'.$this->id);
    }    

}