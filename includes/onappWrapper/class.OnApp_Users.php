<?php


/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */

class NewOnApp_Users extends NewOnApp_Connection {

    protected $_id = null;
    protected $_api = null;

    public function __construct($id = null) {
        $this->_id = $id;
    }

    public function setID($id){
        $this->_id = $id;
    }
    
    public function getVMStats($params)
    {
        $params = array_merge(array('use_local_time' => '1'), $params);
        return $this->_api->sendGET('/users/'.$this->_id.'/vm_stats'.($params ? '?'.http_build_query($params) : ''));
    }
    
    public function edit($params){
        return $this->_api->sendPUT('/users/'.$this->_id,$params);
    }
    
    
}
