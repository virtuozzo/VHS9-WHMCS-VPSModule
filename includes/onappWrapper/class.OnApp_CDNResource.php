<?php

/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */
class NewOnApp_CDNResource extends NewOnApp_Connection {
    

    protected $_api     = null;
    
    public function create($params){
         return $this->_api->sendPOST('/cdn_resources',$params);
    }
    
    public function getDetails($id){
        return $this->_api->sendGET('/cdn_resources/'.$id);
    }
    
    public function edit($id,$params){
        return $this->_api->sendPUT('/cdn_resources/'.$id,$params);
    }
    
    public function getAdvancedDetails($id){
        return $this->_api->sendGET('/cdn_resources/'.$id.'/advanced');
    }
    
    public function getReporting($id){
        return $this->_api->sendGET('/cdn_resources/'.$id.'/advanced_reporting');
    }
    
    public function getList(){
        return $this->_api->sendGET('/cdn_resources');
    }
    
    public function delete($id){
        return $this->_api->sendDELETE('/cdn_resources/'.$id);
    }
    
    public function prefetch($id,$params){
        return $this->_api->sendPOST('/cdn_resources/'.$id.'/prefetch',$params);
    }
    
    public function purge($id,$params){
        return $this->_api->sendPOST('/cdn_resources/'.$id.'/purge',$params);
    }
    
    public function purge_all($id){
        return $this->_api->sendPOST('/cdn_resources/'.$id.'/purge_all');
    }
    
    public function getChart($name, $data = array()){
        $this->_api->_unsetJson();
        return $this->_api->sendGET('/cdn/reports/'.$name, $data);
    }
    
    public function billing($id, $params)
    {
        return $this->_api->sendGET('/cdn_resources/'.$id.'/billing'.($params ? '?'.http_build_query($params) : ''));
    } 
    
    public function suspend($id){
        return $this->_api->sendPUT('/cdn_resources/'.$id.'/suspend');
    }

    public function resume($id){
        return $this->_api->sendPUT('/cdn_resources/'.$id.'/resume');
    }
    
    public function bandwidth($params)
    { 
        return $this->_api->sendGET('/cdn_resources/bandwidth', $params);
    }
    
    public function getInstructions($id)
    {
        return $this->_api->sendGET('/cdn_resources/'.$id.'/instructions');
    }
    
    public function getLocations()
    {
        return $this->_api->sendGET('/settings/cdn_locations');
    }
}