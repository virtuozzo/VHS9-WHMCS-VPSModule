<?php


/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */
class NewOnApp_UserGroup extends NewOnApp_Connection {

    protected $_api = null;
    protected $_id  = null;


    public function __construct($id = null) {
        $this->_id  = $id;
    }

    public function getList() {
        return $this->_api->sendGET('/user_groups');
    }
    
    public function getDetails() {
        return $this->_api->sendGET('/user_groups/'.$this->_id);
    }

    public function create($params) {
        return $this->_api->sendPOST('/user_groups',$params);
    }
    
    public function edit($params) {
        return $this->_api->sendPUT('/user_groups/'.$this->_id,$params);
    }
    
    public function delete() {
        return $this->_api->sendDELETE('/user_groups/'.$this->_id);
    }

}