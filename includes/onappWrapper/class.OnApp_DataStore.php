<?php


/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */
class NewOnApp_DataStore extends NewOnApp_Connection
{
    
    protected $_id  = null;
    protected $_api = null;

    public function __construct($id = null)
    {
        $this->_id = $id;
    }

    public function getList()
    {
        return $this->_api->sendGET('/settings/data_stores');
    }
    
    public function getDetails($id = null)
    {
        return $this->_api->sendGET('/settings/data_stores/' . ($id > 0 ? $id : $this->_id));
    }
       
  
}