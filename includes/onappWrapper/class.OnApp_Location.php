<?php


/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */
class NewOnApp_Location extends NewOnApp_Connection
{

    protected $_id  = null;
    protected $_api = null;

    public function __construct($id = null)
    {
        $this->_id = $id;
    }

    public function getLocationGroups()
    {
        return $this->_api->sendGET('/settings/location_groups');
    }
}