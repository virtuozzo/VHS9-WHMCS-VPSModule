<?php

/* * ********************************************************************
 * onapp product developed. (2017-04-10)
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
namespace OnAppVps\Controllers;
use onappWrapper\migrations\TableSchema;
use onappWrapper\repositories\UserRepository;
/**
 * Description of MainController
 *
 * @author Pawel Kopec <pawelk@modulesgarden.com>
 * @version 1.0.0
 */
abstract class MainController {
    
    protected $params;
    /**
     *
     * @var \onappVPS_Product
     */
    protected $product;
    /**
     *
     * @var  \NewOnApp_User
     */
    protected $onAppUser;
    /**
     *
     * @var  \NewOnApp_Connection
     */
    protected $api;
    protected $user;
    
    public function __construct($params) {
        $this->params = $params;
        \NewOnApp_WrapperAPI::throwException();
        $tableShema = new TableSchema();
        $tableShema->up()
                    ->upgrade();
    }



    public function product() {
        if(empty($this->product)){
            $this->product = new \onappVPS_Product($this->params['pid']);
        }
        return $this->product;
    }

    public function setParams($params) {
        $this->params = $params;
        return $this;
    }

    public function setProduct(\onappVPS_Product $product) {
        $this->product = $product;
        return $this;
    }
    /**
     * 
     * @return \NewOnApp_User
     */
    public function onAppUser() {
        if(empty($this->onAppUser)){
            $this->onAppUser = new \NewOnApp_User();
            $this->onAppUser->setconnection($this->params);
        }
        return $this->onAppUser;
    }

    public function getOnAppUser() {
        
        return $this->onAppUser;
    }

    /**
     * 
     * @return \onappWrapper\models\User
     */
    public function getUser() {
        if(empty($this->user)){
            $userRepository = new UserRepository();
            if($userRepository->countForHostingId($this->params['serviceid'])){
                 $this->user  = $userRepository->findForHostingId($this->params['serviceid']);
            }else {
                 $this->user  = $userRepository->findForUserId($this->params['clientsdetails']['id']);
            }
        }
        return $this->user;
    }

    public function setOnAppUser(\NewOnApp_User $onAppUser) {
        $this->onAppUser = $onAppUser;
        return $this;
    }

    public function getOnAppUserId(){
        $onappUserId = $this->getUser()->getOnappIdAttribute();
        if($onappUserId =="0"){
             $this->onAppUser()->setDetails(['email'=> $this->getUser()->getEmailAttribute(), "key" => $this->getUser()->getApiKeyAttribute()]);
             try{
                 $profile = $this->onAppUser()->getProfileDetails();
                 $onappUserId = $profile['user']['id'];
                 $this->getUser()->setOnappIdAttribute( $onappUserId ) ; 
             } catch (\Exception $ex) {
                 //Generate API Key
                $this->onAppUser()->setapi($this->getApi());// as root
                $onappUserId = $this->params['customfields']['userid'];
                $this->onAppUser()->setUserID($onappUserId);
                $keys = $this->onAppUser()->generateAPIKey();
                $this->getUser()->setUsernameAttribute($keys['user']['login'])
                                ->setEmailAttribute($keys['user']['email'])
                                ->setApiKeyAttribute( $keys['user']['api_key'])
                                ->setOnappIdAttribute($onappUserId)
                                ->save();
                
             }
        }
        return $onappUserId;
    }
    
    public function setUser($user) {
        $this->user = $user;
        return $this;
    }

    public function getApi() {
        if(empty($this->api)){
            $this->api = new \NewOnApp_WrapperAPI(\NewOnApp_WrapperAPI::getHostname($this->params), $this->params['serverusername'], $this->params['serverpassword']);
        }
        return $this->api;
    }

    public function setApi($api) {
        $this->api = $api;
        return $this;
    }





}
