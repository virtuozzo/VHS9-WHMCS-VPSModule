<?php


/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */
class NewOnApp_VM extends NewOnApp_Connection {

    protected $_id = null;
    protected $_api = null;

    private $details;
    private $acceleratorPresence;
    
    public function __construct($id = null) {
        $this->_id = $id;
    }

    public function setID($id){
        $this->_id = $id;
    }
    
    public function getAutoScalling(){
        return $this->_api->sendGET('/virtual_machines/' . $this->_id.'/auto_scaling');
    }
    
    public function setAutoScalling($params){
    
         return $this->_api->sendPOST('/virtual_machines/' .$this->_id . '/auto_scaling' , $params);
    }
    
    public function deleteAutoScalling(){
         return $this->_api->sendDELETE('/virtual_machines/' .$this->_id . '/auto_scaling');
    }

    public function getDetails($force=false) {
        if($force || empty($this->details)){
            $result  = $this->_api->sendGET('/virtual_machines/' . $this->_id);
            $modifed = array();
            foreach($result as $key=>$value){
                foreach($value as $k =>$val){
                    if($k=='created_at' || $k=='updated_at'){
                        $result['virtual_machine'][$k] = date('Y-m-d H:i:s',  strtotime($val));
                    }
                }
            }
            $this->details = $result;
        }
        return $this->details;
    }

    public function getLists() {
        return $this->_api->sendGET('/virtual_machines');
    }

    public function create($params) {
        $result = $this->_api->sendPOST('/virtual_machines', $params);
        
        if($this->_api->getError())
        {
            throw new Exception($this->_api->getError());
        }
        
        if($this->isSuccess() && empty($result['virtual_machine']['id']))
            throw new Exception("API Error: Virtual Machine ('id') is empty ");
        return $result;
    }

    public function viewPassword() {
        return $this->_api->sendGET('/virtual_machines/' . $this->_id . '/with_decrypted_password', array(), array('initial_root_password_encryption_key' => 'encryptionkey'));
    }

    public function rebuild($params) {
        return $this->_api->sendPOST('/virtual_machines/' . $this->_id . '/build', $params);
    }

    public function modify($params) {
        return $this->_api->sendPUT('/virtual_machines/' . $this->_id, $params);
    }

    public function changeOwner($params) {
        return $this->_api->sendPOST('/virtual_machines/' . $this->_id . '/change_owner', $params);
    }

    public function changePassword($params) {
        return $this->_api->sendPOST('/virtual_machines/' . $this->_id . '/reset_password', $params);
    }

    public function setSSH() {
        return $this->_api->sendPOST('/virtual_machines/' . $this->_id . '/set_ssh_keys');
    }

    public function migrate($params) {
        return $this->_api->sendPOST('/virtual_machines/' . $this->_id . '/migrate', $params);
    }

    public function setVip() {
        return $this->_api->sendPOST('/virtual_machines/' . $this->_id . '/set_vip');
    }

    public function delete() {
        return $this->_api->sendDELETE('/virtual_machines/' . $this->_id);
    }

    public function resize($params) {
        return $this->_api->sendPOST('/virtual_machines/' . $this->_id . '/resize', $params);
    }

    public function start() {
        return $this->_api->sendPOST('/virtual_machines/' . $this->_id . '/startup');
    }

    public function segregate() {
        return $this->_api->sendPOST('/virtual_machines/' . $this->_id . '/strict_vm');
    }

    public function reboot() {
        return $this->_api->sendPOST('/virtual_machines/' . $this->_id . '/reboot');
    }

    public function recovery() {
        return $this->_api->sendPOST('/virtual_machines/' . $this->_id . '/reboot', array(), array('mode' => 'recovery'));
    }

    public function suspend() {
       $status  = $this->getDetails();
       if($status['virtual_machine']['suspended']==1)
            return true;
       return $this->_api->sendPOST('/virtual_machines/' . $this->_id . '/suspend');
    }

    public function unlock() {
        return $this->_api->sendPOST('/virtual_machines/' . $this->_id . '/unlock');
    }

    public function unsuspend() {
       $status  = $this->getDetails();
       if($status['virtual_machine']['suspended']==null)
            return true;
        return $this->_api->sendPOST('/virtual_machines/' . $this->_id . '/suspend');
    }

    public function shutdown() {
        return $this->_api->sendPOST('/virtual_machines/' . $this->_id . '/shutdown');
    }

    public function stop() {
        return $this->_api->sendPOST('/virtual_machines/' . $this->_id . '/stop');
    }

    public function getConsoleKey() {
        return $this->_api->sendGET('/virtual_machines/' . $this->_id . '/console');
    }
    
    public function getConsolePopUp() {
        $this->_api->_unsetJSON();
        $result = $this->_api->sendGET('/virtual_machines/' . $this->_id . '/console_popup');
        $this->_api->_setJSON();
        return $result;
    }

    public function getStats($params = array()) 
    {
        $params = array_merge(array('use_local_time' => '1'), $params);
        return $this->_api->sendGET('/virtual_machines/' . $this->_id . '/vm_stats', $params);
    }

    public function search($params) {
        return $this->_api->sendGET('/virtual_machines', array(), $params);
    }

    public function getUserVMs($user_id){
        return $this->_api->sendGET('/users/' . $user_id . '/virtual_machines');
    }
    
    public function getDisks(){
        return $this->_api->sendGET('/virtual_machines/' . $this->_id . '/disks');
    } 
    
    public function getDiskBackups($disk_id){
        return $this->_api->sendGET('/virtual_machines/' . $this->_id  . '/disks/'.$disk_id.'/backups');
    } 
    
    public function getUsageCPU() {
        return $this->_api->sendGET('/virtual_machines/' . $this->_id . '/cpu_usage');
    }
    
    public function getSchedules() {
        return $this->_api->sendGET('/virtual_machines/' . $this->_id  . '/schedules');
    }
    
    public function addSchedule($params) {
        return $this->_api->sendPOST('/virtual_machines/' . $this->_id  . '/schedules', $params);
    }
    
    public function editSchedule($id, $params) {
        return $this->_api->sendPUT('/virtual_machines/' . $this->_id  . '/schedules/'.$id, $params);
    }
    
    public function deleteSchedule($id){
         return $this->_api->sendDELETE('/virtual_machines/' .$this->_id . '/schedules/'.$id);
    }
    
    public function searchLog($key){
        return $this->_api->sendGET('/logs',array('q'=>$key));
    }
    
    public function getUsageChart(){
        $this->_api->_unsetJSON();
        $result = $this->_api->sendGETWithoutJSON('/virtual_machines/' . $this->_id . '/auto_scaling.chart');
        $this->_api->_setJSON();
        return $result;
    }

    public function getUsageCPUChart() {
        $this->_api->_unsetJSON();
        return $this->_api->sendGETWithoutJSON('/virtual_machines/' . $this->_id . '/cpu_usage.chart?use_local_time=1');
    }
    
    public function getNetworkInterfaces() {
        return $this->_api->sendGET('/virtual_machines/' . $this->_id . '/network_interfaces');
    }

    public function getNetworkInterface($id) {
        $id = (int)$id;
        return $this->_api->sendGET('/virtual_machines/' . $this->_id . '/network_interfaces/'.$id);
    }
    public function available($user_id){
        $result = $this->getDetails();
      
        if($this->isSuccess() && $result['virtual_machine']['user_id']==$user_id){
            $this->details = $result;
            return true;
        } else return false;
    }
    
    public function getLogs()
    {
        $data = $this->_api->sendGET('/virtual_machines/' . $this->_id . '/logs');
        foreach($data as $k=> $value){
            $data[$k]['log_item']['status'] = strtolower($value['log_item']['status']);
            $data[$k]['log_item']['created_at'] = date('Y-m-d H:i:s',  strtotime($value['log_item']['created_at']));
        }
        return $data;
    }
    
    public function getTransactions($page=false){
        $limit = 20;
        $data = $this->_api->sendGET('/virtual_machines/' . $this->_id . '/transactions');
        $new  = array();
        $i    = 0;
        foreach($data as $k=> $value){
            foreach($value['transaction'] as $key=>$val){
                if($key=='created_at'){
                    
                    $format = date('Y-m-d H:i:s',  strtotime($val));
                }
            }
            $new[$k] = $value;
            $new[$k]['transaction']['created_at']  = $format;
            $i++;
        }

        $list  = array();
        $backups_ids = array();
        $disks = $this->getDisks();
        if($this->isSuccess()){
            foreach ($disks as $val){
                $backups = $this->getDiskBackups($val['disk']['id']);
                if($this->isSuccess()){
                    foreach($backups as $b){
                        $backups_ids[] = $b['backup']['identifier'];
                    }
                }

            }
        }
        $offset = is_numeric($page)? $page : 0;
        $output = array_slice($backups_ids, $offset, $limit);
        foreach($output as $val){
            $logs = $this->searchLog($val);
            if($this->isSuccess()){
              foreach($logs as $l){
                  $list[$i]['transaction']['action']     = $l['log_item']['action'];
                  $list[$i]['transaction']['created_at'] = $format = date('Y-m-d H:i:s',  strtotime($l['log_item']['created_at']));
                  $list[$i]['transaction']['status']     = strtolower($l['log_item']['status']);
                  $i++;
              }  
            }

        }
               
        $data = array_merge($new,$list);
        usort($data, cmp);
        if($page===false)
            return array ('pages'=>null,'data'=>$data);
        else {
           return array('pages'=>ceil(count($data)/$limit),'data'=>array_slice($data, $page*$limit,$limit));
            
        }
        
        
    }
    

    
    public function assignIP($cn_ip,$network_id,$hypervisor_id = null, $hypervisor_zone = null) {
            
            $cn_ip = (int)$cn_ip;

            $interfaces = $this->getNetworkInterfaces();                
            $interface  = $interfaces[0]['network_interface']['id'];

            if (empty($interface))
                    return false;
            
            if(!$network_id && $hypervisor_zone){
                $hypervisor = new NewOnApp_HypervisorZone($hypervisor_zone);
                $hypervisor ->setapi($this->_api);
                $joins      = $hypervisor->networkJoins();
                foreach($joins as $key=>$value){
                    if($value['network_join']['id'] == $interfaces[0]['network_interface']['network_join_id']){
                        $network_id = $value['network_join']['network_id'];
                        break;
                    }
                }
            }
            if(!$network_id){
                $ip_join = new NewOnApp_IPAddressJoin($this->_id);
                $ip_join->setapi($this->getApi());
                foreach($ip_join->getList() as $key=>$value){
                    if($value['ip_address_join']['ip_address']['network_id'] == $interfaces[0]['network_interface']['network_join_id']){
                        $network_id = $value['ip_address_join']['ip_address']['network_id'] ;
                        break;
                    }
                }
            }

            $ip_join    = new NewOnApp_IPAddressJoin($this->_id);
            $ip_join    ->setapi($this->_api);
            $ip_joins   = $ip_join->getList();
            $i          = count($ip_joins);
            if ($i == $cn_ip)
                return true;
            else {
                if($i >= $cn_ip){
                    $rem = 1;
                    foreach($ip_joins as $key =>$value){
                      if($rem>$cn_ip){
                           $ip_join->delete($value['ip_address_join']['id']);
                           if(!$ip_join->isSuccess())
                               return $ip_join->error();
                      }
                      $rem++;
                    }
                    return;
            }

            $ip_address = new NewOnApp_IPAddress($network_id);
            $ip_address ->setapi($this->_api);
            $ip_pools   = $ip_address->getList();
                
            if($i <= $cn_ip &&  $this->getVersion() && version_compare( $this->getVersion(), "5.4.0", '>=')){ //5.4.0
                $usedHost=[];
                foreach($ip_pools as $ip){
                  $usedHost[]=  $ip['ip_address']['address'];
                }
                $freeIpAddresses=[];
                $limit = $cn_ip - $i;
                $result = $this->_api->sendGET(sprintf('/settings/networks/%s/ip_nets',$network_id));
                foreach($result as  $ipNet){
                    if ($ipNet['ip_net']['ipv4']) {
                        $netId =  $ipNet['ip_net']['id'];
                        $ipRanges = $this->_api->sendGET(sprintf('/settings/networks/%s/ip_nets/%s/ip_ranges', $network_id,$netId));
                        //IP ranges
                        foreach ($ipRanges as $ipRange) {
                            $range = $ipRange['ip_range'];
                            //IP Address
                            $poolIpv4 = new \onappWrapper\utility\PoolIpv4($ipNet['ip_net']['network_address'], $ipNet['ip_net']['network_mask']);
                            $poolIpv4->usedHosts($usedHost)
                                     ->setStartAddress($range['start_address'])
                                     ->setEndAddress($range['end_address'])
                                     ->limit($limit);
                            $freeIpAddresses = array_merge($freeIpAddresses, (array) $poolIpv4->compute()->getHosts());
                            if(count($freeIpAddresses)>=$limit){
                                break;
                            }
                        }
                    }
                }
                foreach($freeIpAddresses as $host){
                    $data = array('ip_address' => array('ip_version' => '4', 'network_interface_id' => $interface));
                    $ip_join->assign($data);
                    if(!$ip_join->isSuccess()){
                        return $ip_join->error();
                    } 
                    $i++;
                    if($i > $limit){
                        break;
                    }
                }
            }else{ //5.3.0
                if(isset($ip_pools) && is_array($ip_pools)){
                    for($j=1; $j<=$cn_ip; $j++){
                        foreach ($ip_pools as $key => $value) {
                                if ($i == $cn_ip)
                                    return;

                                if($i <= $cn_ip){
                                    if (isset($value['ip_address']['free']) && $value['ip_address']['free'] && empty($value['ip_address']['user_id'])) {
                                        $data = array('ip_address_join' => array('ip_version' => '4', 'network_interface_id' => $interface));
                                        $ip_join->assign($data);
                                        if($ip_join->isSuccess()){
                                            $i++;
                                        } else {
                                            return $ip_join->error();
                                        }
                                    }
                                    else
                                        continue;
                                } 
                        }
                    }
                }    
            }
            return true;
        }
    }
    
    public function acceleratorEnable() {
        return $this->_api->sendPUT('/virtual_machines/' . $this->_id . '/acceleration/enable');   // since version Onapp 6.0
    }
    
    public function acceleratorDisable() {
        return $this->_api->sendPUT('/virtual_machines/' . $this->_id . '/acceleration/disable');  // since version Onapp 6.0
    }
    
    public function acceleratorPresence($force = false) {
        if($force || is_null($this->acceleratorPresence)){
            $details = $this->getDetails();
            $this->acceleratorPresence = false;
            foreach($details['virtual_machine']['ip_addresses'] as $ip){
                $out = $this->_api->sendPOST('/virtual_machines/accelerator_presence',['ip_id'=>$ip['ip_address']['id']]);
                if($out['present']==true){
                    $this->acceleratorPresence = true;
                    break;
                }
            }
        }
        return $this->acceleratorPresence;
    }
}

    function cmp($a,$b)
    {
        if(strtotime($a['transaction']['created_at']) < strtotime($b['transaction']['created_at']))
            return 1;
        else if(strtotime($a['transaction']['created_at']) == strtotime($b['transaction']['created_at']))
            return 0;
        else
            return -1;
    }
