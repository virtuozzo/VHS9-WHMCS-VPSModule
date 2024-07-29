<?php

/* * ********************************************************************
 * SolusVM_Extended_VPS_GIT product developed. (2017-04-05)
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

namespace onappWrapper\models;
use Illuminate\Database\Eloquent\Model;
/**
 * Description of Client
 *
 * @author Pawel Kopec <pawelk@modulesgarden.com>
 * @version 1.0.0
 */
class User extends Model{
    

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'id','user_id','hosting_id', 'onapp_id','username', 'email', 'password','api_key'
    ];
    
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'int',
        'hosting_id' => 'int',
    ];
    
    protected $table = 'mg_onapp_users';
    
    public function setIdAttribute($id) {
        $this->attributes['id'] = $id;
        return $this;
    }
    
    public function setUserIdAttribute($userId) {
        $this->attributes['user_id'] = $userId;
        return $this;
    }
    
    public function setHostingIdAttribute($hostingId) {
        $this->attributes['hosting_id'] = $hostingId;
        return $this;
    }
        
    public function setUsernameAttribute($username) {
        $this->attributes['username'] = $username;
        return $this;
    }
    
    public function setEmailAttribute($email) {
        $this->attributes['email'] = $email;
        return $this;
    }
    
    public function setPasswordAttribute($password) {
        $this->attributes['password'] = encrypt($password);
        return $this;
    }
    
    public function setOnappIdAttribute($id) {
        $this->attributes['onapp_id'] = $id;
        return $this;
    }
    
    public function setApiKeyAttribute($apiKey) {
        $this->attributes['api_key'] = encrypt($apiKey);
        return $this;
    }
    
    public function getIdAttribute() {
        return $this->attributes['id'];
    }
    
    public function getUserIdAttribute() {
        return $this->attributes['user_id'];
    }
    
    public function getHostingIdAttribute() {
        return $this->attributes['hosting_id'];
    }
        
    public function getUsernameAttribute() {
        return $this->attributes['username'];
    }
    
    public function getEmailAttribute() {
        return $this->attributes['email'];
    }
    
    public function getPasswordAttribute() {
        return decrypt($this->attributes['password']);
    }
    
    public function getApiKeyAttribute() {
        return decrypt($this->attributes['api_key']);
    }
    public function getOnappIdAttribute() {
        return $this->attributes['onapp_id'] ;
    }
    
}
