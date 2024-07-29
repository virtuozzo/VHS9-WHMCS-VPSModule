<?php

/* * ********************************************************************
 * onapp product developed. (2017-01-09)
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
use OnAppVps\Database\LocationGroups;
/**
 * Description of FederatedTemplateRepository
 *
 * @author Pawel Kopec <pawelk@modulesgarden.com>
 * @version 1.0.0
 */
class FederatedTemplateRepository {
    
    public function findByLocation( LocationGroups $location ){
        
        $templates = new \OnAppVps\Database\FederatedTemplates();
        if($location->federated=="1"){
            return $templates->where('location_id', 'like', $location->location_id)
                             ->get();
        }else{
            return $templates->whereNull('location_id')
                             ->get();
        }
        
        
    }
    
    public function findByLocationVCenter()
    {
        $templates = new \OnAppVps\Database\FederatedTemplates();
        return $templates->where('group', 'vCenter')->where('location_id', '0')
                             ->get();
    }
}
