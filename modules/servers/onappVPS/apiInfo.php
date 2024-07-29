<?php

/**
 * @author Pawel Kopec <pawelk@modulesgarden.com>
 */
if($vars['disallow_action']['api_info']==1){
    ob_clean();
    header ("Location: clientarea.php?action=productdetails&id=".$params['serviceid']);
    die();
}
  try {
         
         onappVPS_Load();
         $actionController = new \OnAppVps\Controllers\Admin\ActionController($params); 
         if($_POST['regenerate']){
               $actionController->regenerateApiKey();
               $vars['msg_success'] = $vars['lang']['regenerateMsg'];
         }
         $dbUser = $actionController->getUser();
         $vars['userDetails'] = $user_details = ['username' => $dbUser->getEmailAttribute(),  'password' => $dbUser->getApiKeyAttribute()];

  } catch (Exception $ex) {
        $vars['msg_error'] = $ex->getMessage();
  }