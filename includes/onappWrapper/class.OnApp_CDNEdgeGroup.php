<?php


/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */
class NewOnApp_CDNEdgeGroup extends NewOnApp_Connection {
    
    protected $_id      = null;
    protected $_api     = null;


    public function availableList(){
        return $this->_api->sendGET('/cdn_resources/available_edge_groups');
    }
    
    public function getList(){
        return $this->_api->sendGET('/edge_groups');
    }
    
    public function getDetails($id){
        return $this->_api->sendGET('/edge_groups/'.$id);
        
    }
  
}