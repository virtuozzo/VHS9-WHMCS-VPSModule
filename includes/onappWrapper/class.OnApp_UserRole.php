<?php


/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */
class NewOnApp_UserRole extends NewOnApp_Connection {

    protected $_api = null;
    protected $_id  = null;


    public function __construct($id = null) {
        $this->_id  = $id;
    }

    public function getList() {
        return $this->_api->sendGET('/roles');
    }
    
    public function getDetails() {
        return $this->_api->sendGET('/roles/'.$this->_id);
    }

    public function create($params) {
        return $this->_api->sendPOST('/roles',$params);
    }
    
    public function edit($params) {
        return $this->_api->sendPUT('/roles/'.$this->_id,$params);
    }
    
    public function delete() {
        return $this->_api->sendDELETE('/roles/'.$this->_id);
    }
      
}