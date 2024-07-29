<?php



/**
 * @author Mariusz Miodowski <mariusz@modulesgarden.com>
 */

class NewOnApp_DNSZone extends NewOnApp_Connection
{
    private $_zone_id = null;
    
    public function __construct($dns_zone_id = null)
    {
        $this->_zone_id = $dns_zone_id;
    }
    
    public function getDNSZoneDetails()
    {
        return $this->_api->sendGET('/dns_zones/'.$this->_zone_id);
    }
    
    public function createDNSZone($name, $auto_populate = 0)
    {
        return $this->_api->sendPOST('/dns_zones', array
        (
            'dns_zone'      =>  array
            (
                'name'          =>  $name,
                'auto_populate' =>  $auto_populate
            )
        ));
    }
    
    public function deleteDNSZone()
    {
        return $this->_api->sendDELETE('/dns_zones/'.$this->_zone_id);
    }
    
    public function getNameServerList()
    {
        return $this->_api->sendGET('/settings/dns_zones/name-servers');
    }
    
    public function getDNSRecordsList()
    {
        return $this->_api->sendGET('/dns_zones/'.$this->_zone_id.'/records');
    }
    
    public function getDNSRecordDetails($record_id)
    {
        return $this->_api->sendGET('/dns_zones/'.$this->_zone_id.'/records/'.$record_id);
    }
    
    public function deleteDNSRecord($record_id)
    {
        return $this->_api->sendDELETE('/dns_zones/'.$this->_zone_id.'/records/'.$record_id);
    }
    
    public function createDNSRecord($details)
    {
        return $this->_api->sendPOST('/dns_zones/'.$this->_zone_id.'/records', array
        (
            'dns_record'    =>  $details
        ));
    }
    
    public function editDNSRecord($record_id, $details)
    {
        return $this->_api->sendPUT('/dns_zones/'.$this->_zone_id.'/records/'.$record_id, array
        (
            'dns_record'    =>  $details
        ));
    }
}