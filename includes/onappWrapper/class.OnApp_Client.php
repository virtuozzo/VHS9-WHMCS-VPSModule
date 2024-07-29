<?php

use onappWrapper\PdoWrapper;
/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */
class NewOnApp_Client {

    public $id;

    public function __construct($id = null) {
        $this->id = (int) $id;
    }

    public function isAbleToManageVserver($hostingid, $vserverid) {
        $q = PdoWrapper::query('
			SELECT h.id
			FROM tblhosting AS h
			JOIN tblcustomfieldsvalues	AS cfv	ON h.id = cfv.relid
			JOIN tblcustomfields		AS cf	ON cfv.fieldid = cf.id
			WHERE
			h.id = ?
			AND cf.fieldname = "vserverid"
			AND cfv.value = ?
			AND h.userid = ?', array(
            $hostingid,
            $vserverid,
            $this->id
        ));

        return (bool) PdoWrapper::numRows($q);
    }

    public function isAbleToManageHosting($hostingid) {
        $q = PdoWrapper::query('SELECT * FROM tblhosting WHERE id = ? AND userid = ?', array(
            $hostingid,
            $this->id
        ));

        return (bool) PdoWrapper::numRows($q);
    }
    
    public function getLastUsernameFromHostings($prod_id, array $stripHostingsIds = array()){
		$q = PdoWrapper::query('
			SELECT h.username
			FROM tblhosting h 
                        LEFT JOIN tblproducts p  ON (p.id = h.packageid)
			WHERE p.servertype="onappVPS" AND h.userid = ?  AND h.username != "" '.(empty($stripHostingsIds) ? '' : ' AND h.id NOT IN('.implode(',',$stripHostingsIds).')').'
			ORDER BY h.id DESC
			LIMIT 1
		', array(
			$this->id,
		));
                
		$row = PdoWrapper::fetchAssoc($q);
		return isset($row['username']) ? $row['username'] : null;
	}
        
    public function getLastPasswordFromHostings($prod_id, array $stripHostingsIds = array()){
		$q = PdoWrapper::query('
			SELECT h.password
			FROM tblhosting h
                        LEFT JOIN tblproducts p  ON (p.id = h.packageid)
			WHERE p.servertype="onappVPS" AND h.userid = ?  AND h.username != "" '.(empty($stripHostingsIds) ? '' : ' AND h.id NOT IN('.implode(',',$stripHostingsIds).')').'
			ORDER BY h.id DESC
			LIMIT 1
		', array(
			$this->id,
		));
                
		$row = PdoWrapper::fetchAssoc($q);
		return isset($row['password']) ? decrypt($row['password']) : null;
                
    }    

    public function updateHostingUsername($username,$service_id,$pid){
        PdoWrapper::query("UPDATE tblhosting SET `username`=? WHERE `id`=? AND `packageid`=?",array($username,$service_id,$pid));
    }
    
    public function updateHostingPassword($password,$service_id,$pid){
        PdoWrapper::query("UPDATE tblhosting SET `password`=? WHERE `id`=? AND `packageid`=?",array(encrypt($password),$service_id,$pid));
    }


    
    public static function isAdmin() {
        return isset($_SESSION['adminid']) && $_SESSION['adminid'];
    }

}