<?php

/**
 * @author Damian Bzdon <damian.bz@modulesgarden.com>
 */
class NewOnApp_Configuration extends NewOnApp_Connection {
    
    protected $_api     = null;

    public function getDetails(){
        return $this->_api->sendGET('/settings/configuration');
    }
    
    public function edit($params){
        return $this->_api->sendPUT('/settings/configuration',$params);
    }
    
}