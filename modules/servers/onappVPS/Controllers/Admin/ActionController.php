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

namespace OnAppVps\Controllers\Admin;

use OnAppVps as main;
use onappWrapper\models\User;
use onappWrapper\repositories\UserRepository;

/**
 * Description of ActionController
 *
 * @author Pawel Kopec <pawelk@modulesgarden.com>
 * @version 1.0.0
 */
class ActionController extends main\Controllers\MainController {

    public function userValidation() {
        $userRepository = new UserRepository();
        if ($this->product->getConfig('userAccountPerVPS')) {
            return $userRepository->countForHostingId($this->params['serviceid']);
        } else {

            return $userRepository->countForUserId($this->params['clientsdetails']['id']);
        }
    }

    public function preCreateValidation() {

        if (!isset($this->params['configoptions']['template_id']) && empty($this->product()->getConfig('template_id'))) {
            throw new \Exception("OS Template - is empty");
        }
        if (empty($this->product()->getConfig('user_role'))) {
            throw new \Exception("User Role - is empty");
        }
        return $this;
    }

    public function userBuild(){
        if($this->product->getConfig('userAccountPerVPS')){//One user per vps
            //Prepare user For DB
            if (empty($this->params['username'])) {
                $this->params['username'] = uniqid();
            }
            //Is in DB
            $userRepository = new UserRepository();

            if ($userRepository->countForHostingId($this->params['serviceid'])) {
                //Valid Access
                $user = $userRepository->findForHostingId($this->params['serviceid']);
                $this->setUser($user);
                $this->onAppUser()->setDetails(['email' => $user->getEmailAttribute(), "key" => $user->getApiKeyAttribute()]);
                try {
                    $profile = $this->onAppUser()->getProfileDetails();
                } catch (\Exception $ex) {
                    $userRepository->deleteForHostingId($this->params['serviceid']);
                    $this->createUser($this->params['serviceid']);
                }
                return true;
            } else {
                $this->createUser($this->params['serviceid']);
            }
            return true;
        }else{//OneUser many VPS
            if(empty($this->params['username'])){
                $this->params['username'] = uniqid();
            }
            //Is in DB
            $userRepository = new UserRepository();
            if($userRepository ->countForUserId($this->params['clientsdetails']['id'])){
                //Valid Access
                $user = $userRepository->findForUserId($this->params['clientsdetails']['id'] );
                $this->setUser($user);
                $this->onAppUser()->setDetails(['email'=> $user->getEmailAttribute(), "key" => $user->getApiKeyAttribute()]);
                try{
                    $profile = $this->onAppUser()->getProfileDetails();
                    
                    if($profile === false)
                    {
                        $userRepository ->deleteForUserId($this->params['clientsdetails']['id']);
                        $this->createUser('0');
                    }
                    
                } catch (\Exception $ex) {
                    $userRepository ->deleteForUserId($this->params['clientsdetails']['id']);
                    $this->createUser('0');
                }
                return true;
            } else {
                $users = $this->onAppUser()->search($this->params['username']);

                if (count($users) > 0) {
                    $onAppUser = new \NewOnApp_User($users[0]['user']['id']);
                    $onAppUser->setconnection($this->params);
                    $key = $onAppUser->generateAPIKey();
                    $user = new User();
                    $user->setEmailAttribute($users[0]['user']['email'])
                            ->setUserIdAttribute($this->params['clientsdetails']['id'])
                            ->setApiKeyAttribute($key['user']['api_key'])
                            ->setUsernameAttribute($key['user']['login'])
                            ->setOnappIdAttribute($users[0]['user']['id'])
                            ->save();

                    $this->setUser($user);
                    $this->onAppUser()->setDetails(['email' => $user->getEmailAttribute(), "key" => $user->getApiKeyAttribute()]);
                    $profile = $this->onAppUser()->getProfileDetails();
                } else {
                    $this->createUser('0');
                }
            }
        }
    }
    
    public function createNewUser($hostingId) {
        
        $this->createUser($hostingId);
        
    }

    private function createUser($hostingId) {
        //Prepare user For DB
        $email = $_SERVER['HTTP_HOST'] == "192.168.56.101" ? uniqid() . '@dev.modulesgarden-demo.com' : uniqid() . '@' . $_SERVER['HTTP_HOST'];
        //Create user In OnApp
        $user_details = [
            'user' => [
                'login' => $this->product->getConfig('user_prefix') . $this->params['username'],
                'first_name' => $this->params['clientsdetails']['firstname'],
                'last_name' => $this->params['clientsdetails']['lastname'],
                'email' => $email,
                'password' => onapp_pass_generator(14),
                'role_ids' => $this->product()->getConfig('user_role'),
                'status' => 'active',
                'billing_plan_id' => $this->product()->getConfig('user_billing_plan'),
                'user_group_id' => $this->product()->getConfig('user_group'),
            ],
        ];
        $this->onAppUser()->setapi($this->getApi());
        $out = $this->onAppUser()->create($user_details);
        if (!isset($out['user']['id'])) {
            throw new \Exception("API Error: Create user failed. Empty user id");
        }
        //Generate API Key
        $keys = $this->onAppUser()->generateAPIKey();
        $version = $this->onAppUser()->getVersionOnApp();
 
        $apikey = '';
        if($version && version_compare($version, "6.5", '>=')){ //6.5
            $apikey = $keys['api_key'];
        }
        else
        {
            $apikey = $keys['user']['api_key']; 
        }
        
        $user = new User();
        $user->setUserIdAttribute($this->params['clientsdetails']['id'])
                ->setHostingIdAttribute($hostingId)
                ->setOnappIdAttribute($out['user']['id'])
                ->setUsernameAttribute($out['user']['login'])
                ->setEmailAttribute($out['user']['email'])
                ->setPasswordAttribute($user_details['user']['password'])
                ->setApiKeyAttribute($apikey)
                ->save();
        $this->setUser($user);
        return true;
    }

    public function userDelete($userid) {

        $this->onAppUser()->setapi($this->getApi());
        $this->onAppUser()->setUserID($userid);
        $list = $this->onAppUser()->getVMList();
        if (is_array($list) && count($list) <= 1) {
            if (count($list) == 1 && $list['0']['virtual_machine']['id'] != $this->params['customfields']['vmid']) {
                return;
            }
            $this->onAppUser()->delete(['force' => 1]);
            $this->getUser()->delete();
            logActivity(sprintf('OnApp VPS - Virtual Machine User ID:%s Deleted - Service ID: %s', $userid, $this->params['serviceid']));
        }
    }

    public function regenerateApiKey() {
        //Generate API Key
        $this->onAppUser()->setapi($this->getApi()); // as root
        $this->onAppUser()->setUserID($this->getOnAppUserId());
        $keys = $this->onAppUser()->generateAPIKey();
        $this->getUser()->setUsernameAttribute($keys['user']['login'])
                ->setEmailAttribute($keys['user']['email'])
                ->setApiKeyAttribute($keys['user']['api_key'])
                ->save();
    }

}
