<?php

/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */
class NewOnApp_StorageServer extends NewOnApp_Connection {
    

    protected $_api     = null;

    // Ref: https://docs.onapp.com/display/50API/Get+List+of+Available+Storage+Locations
    public function availableList($params){
        return $this->_api->sendGET('/cdn_resources/available_storage_server_locations', $params);
    }

    // Ref: https://docs.onapp.com/display/50API/Get+List+of+CDN+Storage+Servers 
    public function get($params){
         return $this->_api->sendGET('/storage_servers', $params);
    }
}
