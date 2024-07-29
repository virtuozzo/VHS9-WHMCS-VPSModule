<?php

/* * ********************************************************************
 * onapp product developed. (2016-11-17)
 * *
 *
 *  CREATED BY MODULESGARDEN       ->       http://modulesgarden.com
 *  CONTACT                        ->       contact@modulesgarden.com
 *
 *
 * This software is furnished under a license and may be used and copied
 * only  in  accordance  with  the  terms  of such  license and with the
 * inclusion of the above copyright notice.  This software  or any other
 * copies thereof may not be provided or otherwise made available to any
 * other person.  No title to and  ownership of the  software is  hereby
 * transferred.
 *
 *
 * ******************************************************************** */

namespace OnAppVps\Reposiotry;
use OnAppVps\Database\Upgrade;
use onappWrapper\PdoWrapper;

/**
 * Description of Repository
 *
 * @author Pawel Kopec <pawelk@modulesgarden.com>
 * @version 1.0.0
 */
class UpgradeReposiotry {
    
    public function isPending($hostingId){
        
        $query  = Upgrade::where('relid',(int)$hostingId)
                         ->where('status',"Pending")
                         ->where('type',"configoptions")
                         ->where("date" ,'=', date('Y-m-d', strtotime("now")));

        return (int)  $query->count();
                
        
    }
    
    
    public function getOptionId($optionName, $productId){

      
        $query = PdoWrapper::query("SELECT po.id
                                               FROM  tblproductconfiggroups pg 
                                               LEFT JOIN tblproductconfiglinks pl ON (pg.id = pl.gid) 
                                               LEFT JOIN tblproductconfigoptions po ON (po.gid=pg.id) 
                                           WHERE po.optionname LIKE :optionname 
                                                 AND pl.pid = :pid", array(":optionname" => $optionName."%", ":pid" => $productId));
        $row =  PdoWrapper::fetchAssoc($query);
        
        return $row['id'];
        
    }
    
    public function hasNewValue($optionId, $serviceId){
        
        $query = PdoWrapper::query("SELECT * FROM `tblupgrades` WHERE relid=? AND `status`= ? AND originalvalue LIKE ?", 
                                   array($serviceId, "Pending","{$optionId}=>%"));               
        return (boolean) PdoWrapper::numRows($query);
                                                               
    }

}
