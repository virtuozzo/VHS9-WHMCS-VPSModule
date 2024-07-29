<?php


/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */
class NewOnApp_LoadBalancer extends NewOnApp_Connection {
    

    protected $_api     = null;
    public    $_id      = null;
    
    public function __construct($id = null) {
        $this->_id = $id;
    }
    
    public function create($params){
         return $this->_api->sendPOST('/load_balancing_clusters',$params);
    }
    
    public function editCluster($id,$params){
        return $this->_api->sendPUT('/load_balancing_clusters/'.$id,$params);
    }
    
    public function getClusterDetails($id){
        return $this->_api->sendGET('/load_balancing_clusters/'.$id);
    }
    
    public function getDetails($id){
        return $this->_api->sendGET('/load_balancers/'.$id);
    }
    
    public function getList(){
        return $this->_api->sendGET('/load_balancing_clusters');
    }
    
    public function delete(){
        return $this->_api->sendDELETE('/load_balancers/'.$this->_id);
    }
    
    public function start(){
        return $this->_api->sendPOST('/load_balancers/'.$this->_id.'/startup');
    }
    
    public function shutdown(){
        return $this->_api->sendPOST('/load_balancers/'.$this->_id.'/shutdown');
    }
    
    public function stop(){
        return $this->_api->sendPOST('/load_balancers/'.$this->_id.'/stop');
    }
    
    public function suspend(){
        $details = $this->getDetails($this->_id);
        if($details['load_balancer']['suspended']==1)
            return true;
        else
            return $this->_api->sendPOST('/load_balancers/'.$this->_id.'/suspend');
    }
    
    public function unsuspend(){
        $details = $this->getDetails($this->_id);
        if($details['load_balancer']['suspended']==1)
            return $this->_api->sendPOST('/load_balancers/'.$this->_id.'/suspend');
        else 
            return true;
    }
    
    public function unlock(){
        return $this->_api->sendPOST('/load_balancers/'.$this->_id.'/unlock');
    }
    
    public function rebuild(){
        return $this->_api->sendPOST('/load_balancers/'.$this->_id.'/rebuild');
    }
    
    public function reboot(){
        return $this->_api->sendPOST('/load_balancers/'.$this->_id.'/reboot');
    }
    
    public function addNode($id,$params){
         return $this->_api->sendPUT('/load_balancing_clusters/'.$id,$params);
    }
    
    public function removeClusterNode($id,$params){
         return $this->_api->sendPUT('/load_balancing_clusters/'.$id,$params);
    }
    
    public function removeNode($node_id){
         return $this->_api->sendDELETE('/load_balancing_clusters/'.$this->_id.'/cluster_nodes/'.$node_id);
    }
    
    public function getVMStats($params = array())
    {
        $params = array_merge(array('use_local_time' => '1'), $params);
        return $this->_api->sendGET('/load_balancers/'.$this->_id.'/vm_stats'.($params ? '?'.http_build_query($params) : ''));
    }
}