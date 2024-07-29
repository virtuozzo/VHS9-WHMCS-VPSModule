<?php


/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */
class NewOnApp_User extends NewOnApp_Connection {

    protected $_api = null;
    protected $_id  = null;
    
    public function __construct($id = null) {
        $this->_id = $id;
    }

    public function getDetails() {
        return $this->_api->sendGET('/users/' . $this->_id);
    }
    
    public function getProfileDetails()
    {
        return $this->_api->sendGET('/profile');
    }

    public function isUserExists($username,$email) {
        return $this->_api->sendPOST('/users/validate_login',array(),array('login' => $username,'email'=>$email));
    }
    
    public function isEmailExists($email) {
        return $this->_api->sendPOST('/users/validate_login',array(),array('email' => $email));
    }
    
    public function isExists($data) {
        return $this->_api->sendPOST('/users/validate_login',array(),$data);
    }
    
    public function create($params) {
        $out = $this->_api->sendPOST('/users',$params);
        if($out['user']['id']){
            $this->setUserID($out['user']['id']);
        }
        return $out;
    }
    
    public function edit($params) {
        return $this->_api->sendPUT('/users/'.$this->_id,$params);
    }
    
    public function suspend() {
        return $this->_api->sendPOST('/users/'.$this->_id.'/suspend');
    }
    
    public function active() {
        return $this->_api->sendPOST('/users/'.$this->_id.'/activate'); 
    }
    
    public function unlock($params) {
        return $this->_api->sendPOST('/users/'.$this->_id.'/unlock_account',$params);
    }
    
    public function delete($params=array()) {
        return $this->_api->sendDELETE('/users/'.$this->_id,$params);
    }
    
    public function search($username) {
        return $this->_api->sendGET('/users',array('q'=>$username));
    }
    
    public function getVMList(){
        if(is_null($this->_api)){
            throw new \Exception("Api instance is not defined");
        }
        return $this->_api->sendGET('/users/'.$this->_id.'/virtual_machines');
    }
    
    public function getLimits(){
        return $this->_api->sendGET('/users/'.$this->_id.'/limits');
    }
    
    public function getHVLimits(){
        return $this->_api->sendGET('/users/'.$this->_id.'/hv_limits');
    }
    
    public function getHVfree($vmware = false, $hypervisorIDs= array())
    {
        $res  = $this->getHVLimits();
        $prev = 0; 
        $list = array();
        foreach($res as $hv)
        {
            
            if($hv['hypervisor']['hypervisor_type'] != 'vcenter')
            {
            
                if($vmware && $hv['hypervisor']['hypervisor_type'] != 'vmware'){
                      continue;
                }else if(!$vmware  && $hv['hypervisor']['hypervisor_type'] == 'vmware')
                      continue;
                // only hypervisor belong for the zone
                if(!empty($hypervisorIDs) && !in_array($hv['hypervisor']['id'], $hypervisorIDs))
                      continue;
            
            }
            
            if($prev >   $hv['hypervisor']['free_memory'])
            {
                // Push one or more elements onto the end of array
                array_push($list,$hv['hypervisor']['id']);
            } else
            {
                array_unshift($list, $hv['hypervisor']['id']);
            }
            $prev =  $hv['hypervisor']['free_memory'];    
        }
        return array_shift($list);
    }
    
    public function setUserID($id){
        $this->_id = $id;    
    }
    
    public function addCustomerNetwork($params)
    {
        return $this->_api->sendPOST('/users/'.$this->_id.'/customer_networks',$params);
    }
    
    public function deleteCustomerNetwork($params)
    {
        return $this->_api->sendDELETE('/customer_networks/'.$this->_id);
    } 
    
    public function listCustomerNetworks()
    {
        return $this->_api->sendGET('/users/'.$this->_id.'/customer_networks');
    }
    
    public function list_address_pool($id){
        return $this->_api->sendGET('/customer_networks/available_pools',array('hypervisor_id' => $id));
    }  
    
    public function addRange()
    {
        return $this->_api->sendPOST('/settings/customer_vlans',array('customer_vlan_range'=>array('vlan_starts'=>1000,'vlan_ends'=>2000)));
    }
    
    public function getVersionOnApp()
    {
        $version = $this->getVersion();
        $version = preg_replace("/[^0-9.]/", "", $version);
        return $version;
    }
    
    public function generateAPIKey()
    {
        $version = $this->getVersion();
        $version = preg_replace("/[^0-9.]/", "", $version);
        
        if($version && version_compare($version, "6.5", '>=')){ //6.5
            return $this->_api->sendPOST('/users/'.$this->_id.'/user_api_keys/generate');
        }
        return $this->_api->sendPOST('/users/'.$this->_id.'/make_new_api_key');
    }
    
    public function getID(){
          return $this->_id;
    }
        
      
}