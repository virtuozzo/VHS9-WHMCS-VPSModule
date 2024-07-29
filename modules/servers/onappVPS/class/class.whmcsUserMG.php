<?php

use onappWrapper\PdoWrapper;

/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */

class whmcsUserMG extends NewOnApp_User 
{
    private $userid ;
    private $auth = array();
    
    function __construct($user_id) {
        $this->userid = $user_id;
        if(!empty($this->userid)){
            $this->auth         = PdoWrapper::fetchAssoc(PdoWrapper::query("SELECT `email`,`key`,`username` FROM `".onappVPS_Product::$_tableAuth."` WHERE `user_id` = ?", array($this->userid)));
            if(!empty($this->auth)){
                $this->auth['key']  = decrypt($this->auth['key']);
            }
        }
    }   
    
    public function isUserExistsInWHMCS()
    {
        $row = PdoWrapper::fetchAssoc(PdoWrapper::query("SELECT COUNT(`user_id`) as cn FROM `".onappVPS_Product::$_tableAuth."` WHERE `user_id` = ?", array($this->userid)));
        return $row['cn'] == 0 ? false : true;
    }
    
    public function getIDbyEmail()
    {
        if(!empty($this->auth['username']))
        {
            $res = $this->search($this->auth['username']);
            if($this->isSuccess())
            {
                $this->setUserID($res[0]['user']['id']);
            } else
            {
                throw new Exception($this->error());
            }
        } else 
        {
            throw new Exception('Username not found');
        }
    }
    
    public function isUserExistsInOnApp($email = false)
    {
        if(!$email)
        {
            $email = $this->auth['email'];
        }
        
        if(empty($email))
            return false;
        
        $res = $this->isEmailExists($email);
        return isset($res['valid']) && $res['valid'] != '1';
    }
    
    public function getAccountAccess()
    {
        $this->auth         = PdoWrapper::fetchAssoc(PdoWrapper::query("SELECT `email`,`key`,`username` FROM `".onappVPS_Product::$_tableAuth."` WHERE `user_id` = ?", array($this->userid)));
        $this->auth['key']  = decrypt($this->auth['key']);
        if(empty($this->auth['email']) || empty($this->auth['key']))
        {
            throw new Exception("Unable to get account details");
        }        
        return array (
            'username' => $this->auth['email'],
            'password' => $this->auth['key']
        );
    }
    
    public function isValidAccess()
    {
        $this->getAccountAccess();
        $this->setDetails($this->auth);
        $res = $this->getProfileDetails();
        if($this->isSuccess()) 
        {
            $this->setUserID($res['user']['id']);
            return true;
        }
        else
        {
            throw new Exception($this->error());
            return false;
        }
            
    }
    
    public function createUser($params)
    {
        if(!$this->isUserExistsInOnApp($params['user']['email']))
        {
            $res = $this->create($params);
            if($this->isSuccess())
            {
                $this->setUserID($res['user']['id']);
                return true;
                
            } else 
            {
                throw new Exception($this->error());
            }
        } else 
        {
            throw new Exception("Email address already exists in onapp");
        }
    }
    
    public function addAPIKey()
    {
        $res = $this->generateAPIKey();
        if($this->isSuccess())
        {
            $this->auth['email']    = $res['user']['email'];
            $this->auth['key']      = encrypt($res['user']['api_key']);
            $this->auth['username'] = $res['user']['login'];
            $this->addAccessDetailsToDB();
        } else
        {
            throw new Exception($this->error());
        }
    }
    
    private function addAccessDetailsToDB()
    {
        PdoWrapper::query("REPLACE INTO `".onappVPS_Product::$_tableAuth."` VALUES(?,?,?,?)", array($this->userid, $this->auth['email'], $this->auth['key'], $this->auth['username']));
    }
    
    public function removeFromDB()
    {
        PdoWrapper::query("DELETE FROM `".onappVPS_Product::$_tableAuth."` WHERE `user_id`= ?",array($this->userid));
    }
   
    
}