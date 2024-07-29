<?php


/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */


class NewOnApp_VMBackup extends NewOnApp_Connection {
    
    protected $_api     = null;
        
    public function getList($vm_id){
        return $this->_api->sendGET('/virtual_machines/'.$vm_id.'/backups');
    }
    
    public function getDiskBackup($vm_id,$disk_id){
        return $this->_api->sendGET('/virtual_machines/'.$vm_id.'/disks/'.$disk_id.'/backups');
    }
    
    public function getBackupServer($server_id){     
        return $this->_api->sendGET('/settings/backup_servers/'.$server_id);
    }
       
    public function create($disk_id, $options){
        return $this->_api->sendPOST('/settings/disks/'.$disk_id.'/backups',$options);
    }
    
    public function convert($backup_id,$params){
        return $this->_api->sendPOST('/backups/'.$backup_id.'/convert',$params);   
    }
    
    public function delete($id){
        return $this->_api->sendDELETE('/backups/'.$id);
    }    
     
    public function restore($backup_id){
        return $this->_api->sendPOST('/backups/'.$backup_id.'/restore');
    }
    
    public function addIncrementralBackup($vmid, $note = ''){      
        $options['backup']['note'] = $note;
        return $this->_api->sendPOST('/virtual_machines/'.$vmid.'/backups', $options);
    }
    
    public function getServerList(){
        return $this->_api->sendGET('/settings/backup_servers');
      
    }
    
    public function addSchedule($disk_id,$params){
        return $this->_api->sendPOST('/settings/disks/'.$disk_id.'/schedules',$params);
    }

}