<?php

/* * ********************************************************************
 * SolusVM_Extended_VPS_GIT product developed. (2017-04-06)
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
namespace onappWrapper\repositories;
use onappWrapper\models\User;

/**
 * Description of ClientRepository
 *
 * @author Pawel Kopec <pawelk@modulesgarden.com>
 * @version 1.0.0
 */
class UserRepository {
    
    private $filter;
    public function countForUserId($userId){
         return User::where('user_id',(int) $userId )
                     ->where('hosting_id',0)
                     ->count();
    }
    
    /**
     * 
     * @param int $userId
     * @return User
     */
    public function findForUserId($userId){
         return User::where('user_id',(int) $userId )
                          ->where('hosting_id',0)
                          ->first();
    }
    
    /**
     * 
     * @param int $hostingId
     * @return User
     */
    public function findForHostingId($hostingId){
         return User::where('hosting_id',(int) $hostingId)
                      ->first();
    }
    
    public function countForHostingId($hostingId){
         return User::where('hosting_id',(int) $hostingId)
                      ->count();
    }
    
    public function deleteForHostingId($hostingId){
         return User::where('hosting_id',(int) $hostingId)
                     ->delete();
    }
    
    public function deleteForUserId($userId){
         return User::where('user_id',(int) $userId )
                     ->where('hosting_id',0)
                     ->delete();
    }

    
    
}
