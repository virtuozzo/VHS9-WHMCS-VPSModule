<?php


/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */


class NewOnApp_Disk extends NewOnApp_Connection {
    
    protected $_api     = null;
    public    $_id      = null;

    public function __construct($id = null) {
        $this->_id = $id;
    }

    public function getList($vm_id){
        return $this->_api->sendGET('/virtual_machines/'.$vm_id.'/disks');
    }
    
    public function addDisk($vm_id,$params){
        return $this->_api->sendPOST('/virtual_machines/'.$vm_id.'/disks',$params);
    }
    
    public function edit($params){
        return $this->_api->sendPUT('/settings/disks/'.$this->_id,$params);
    }
    
    public function deleteDisk($vm_id,$id, $params=array()){
        return $this->_api->sendDELETE('/virtual_machines/'.$vm_id.'/disks/'.$id, $params);
    }
    
    public function getScheduleList(){
        return $this->_api->sendGET('/settings/disks/'.$this->_id.'/schedules');
    }
    
        public function getBackupList($id){
        return $this->_api->sendGET('/settings/disks/'.$id.'/backups');
    }
    
    public function getScheduleDetails($id){
        return $this->_api->sendGET('/settings/disks/'.$this->_id.'/schedules/'.$id);
    }
    
    public function addSchedule($params){
        return $this->_api->sendPOST('/settings/disks/'.$this->_id.'/schedules',$params);
    }
    
    public function deleteSchedule($id){
        return $this->_api->sendDELETE('/settings/disks/'.$this->_id.'/schedules/'.$id);
    }
    
    public function editSchedule($id,$params){
        return $this->_api->sendPUT('/settings/disks/'.$this->_id.'/schedules/'.$id,$params);
    }
    
    public function setID($id){
        $this->_id = $id;
    }
    
    public function detail(){
        return $this->_api->sendGET('/settings/disks/'.$this->_id);
    }
    
    
    

}