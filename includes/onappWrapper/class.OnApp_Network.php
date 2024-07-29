<?php


/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */
class NewOnApp_Network extends NewOnApp_Connection {
    
    protected $_id      = null;
    protected $_vmid    = null;
    protected $_api     = null;

    public function __construct($id = null) {
        $this->_id = $id;
    }

    public function getList(){
        return $this->_api->sendGET('/settings/networks');
    }
    
    public function getDetails(){
        return $this->_api->sendGET('/settings/networks/'.$this->_id);
    }
    
    public function create($params){
        return $this->_api->sendPOST('/settings/networks',$params);
    }
    
    public function edit($params){
        return $this->_api->sendPUT('/settings/networks/'.$this->_id,$params);
    }
    
    public function rebuild($params){
        return $this->_api->sendPOST('/virtual_machines/'.$this->_vmid.'/rebuild_network',array(),$params);
    }
    
    public function delete(){
        return $this->_api->sendDELETE('/settings/networks/'.$this->id);
    }

    public function setVM($id){
        $this->_vmid = $id;
    }
    public function getId() {
        return $this->_id;
    }

    public function setId($id) {
        $this->_id = $id;
        return $this;
    }


    
}