<?php


/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */

$publishing       = new NewOnApp_PublishingRule($params['customfields']['vmid']);
$publishing       ->setconnection($params);
$netInterface   = new NewOnApp_NetworkInterface($params['customfields']['vmid']);;
$netInterface   ->setconnection($params);

if(isset($_POST['do'])){
    switch($_POST['do']){
        case 'addRule':
            $data   = array('publication' => array(
                'port'               => $_POST['rule']['port'],
                'protocol'           => $_POST['rule']['protocol'],
                'use_customer_network_address'  => $_POST['rule']['customer_network']

            ));

            $publishing->create($data);
            if($publishing->isSuccess())
                $vars['msg_success'] = $vars['lang']['rule_added'];
            else
                $vars['msg_error']   = $publishing->error();
        break;
        case 'removeRule':
            if($_POST['rule']>0){
                $publishing->delete($_POST['rule']);       
                if($publishing->isSuccess())
                    echo 'success';
                else
                    echo $publishing->error();
                die();
            }
        break;    
        default: die();
    }
}


//get rules
$rules                      = $publishing->getList();
$vars['interfaces']         = $netInterface->getList();

if(count($vars['interfaces'])==0 && $netInterface->error()){
    $vars['msg_error']      = $netInterface->error();
    $vars['block_form']     = 1;
} else {

    $vars['list_interfaces']    = array();
    $vars['rules']              = array();

    foreach($vars['interfaces'] as $key=>$val)
        $vars['list_interfaces'][$val['network_interface']['id']] = $val['network_interface']['label'];

    foreach($rules as $key=>$val){
        $val['publishing_rule']['interface_label'] = $vars['list_interfaces'][$val['publishing_rule']['network_interface_id']];
        $vars['rules'][$key]    = $val;
    }
}

if(isset($_SESSION['ajax_msg_status'])){
    $vars['msg_success'] = $_SESSION['ajax_msg_status'];
    unset($_SESSION['ajax_msg_status']);
}
