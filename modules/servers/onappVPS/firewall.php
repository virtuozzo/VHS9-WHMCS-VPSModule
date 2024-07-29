<?php


/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */
if($vars['disallow_action']['firewall']==1){
    ob_clean();
    header ("Location: clientarea.php?action=productdetails&id=".$params['serviceid']);
    die();
}
$firewall       = new NewOnApp_FirewallRule($params['customfields']['vmid']);
$firewall       ->setconnection($params);
$netInterface   = new NewOnApp_NetworkInterface($params['customfields']['vmid']);;
$netInterface   ->setconnection($params);
$deltedIds=[];
if(isset($_REQUEST['do'])){
    switch($_REQUEST['do']){
        case 'addRule':
            $data   = array('firewall_rule' => array(
                'address'               => $_POST['rule']['address'],
                'command'               => $_POST['rule']['command'],
                'port'                  => $_POST['rule']['port'],
                'protocol'              => $_POST['rule']['protocol'],
                'network_interface_id'  => $_POST['rule']['interface']

            ));

            $firewall->create($data);
            if($firewall->isSuccess())
                $vars['msg_success'] = $vars['lang']['rule_added'];
            else
                $vars['msg_error']   = $firewall->error();
        break;
        case 'removeRule':
            if($_POST['rule']>0){
                
                $firewall->delete($_POST['rule']);       
                if($firewall->isSuccess())
                    echo 'success';
                else
                    echo $firewall->error();
                $deltedIds[]=(int)$_POST['rule'];
                die();
            }
        break;    
        case 'removeSelected':
            $vars['msg_success'] = null;
            $vars['msg_error']   = null;
            if(isset($_POST['rule_id']) && count($_POST['rule_id'])>0){
                foreach($_POST['rule_id'] as $key=>$value){
                    $firewall->delete($value);
                    if($firewall->isSuccess())
                        $vars['msg_success'] .= $vars['lang']['rule_removed']."<br />";
                    else
                        $vars['msg_error']   .= $firewall->error()."<br />";
                    $deltedIds[]=(int)$value;
                }
            }
        break; 
        case 'apply':
            $firewall->apply();
            if($firewall->isSuccess())
                $vars['msg_success'] .= $vars['lang']['rule_updated']."<br />";
            else
                $vars['msg_error']   .= $firewall->error()."<br />";
        break;
        case 'defaultRule':
             if(isset($_POST['defaultRule']) && count($_POST['defaultRule'])>0){
                  $vars['msg_success'] = null;
                  $vars['msg_error']   = null;
                  foreach($_POST['defaultRule'] as $key=>$value){
                       $data['network_interfaces'][$key]['default_firewall_rule'] = $value['command'];
                       $firewall->setDefault($data);
                       if($firewall->isSuccess())
                            $vars['msg_success'] .= $vars['lang']['default_rule_updated']."<br />";
                       else
                            $vars['msg_error']   .= $firewall->error()."<br />";
                  }
             }
        break;
        case 'saveRule':
            if(isset($_POST['rule']) && $_POST['rule']>0){
                $data   = array('firewall_rule'=>array(
                    'address'               => $_POST['address'],
                    'command'               => $_POST['command'],
                    'port'                  => $_POST['port'],
                    'protocol'              => $_POST['protocol'],
                    'network_interface_id'  => $_POST['interface']
                ));
                
                $firewall->save($_POST['rule'],$data);
                if($firewall->isSuccess()){
                    $_SESSION['ajax_msg_status'] = $vars['lang']['success'];
                    die('success');
                }    
                else
                    die($firewall->error());
            }    
            die();
        break;
        case 'pos':
            if(isset($_GET['rule']) && $_GET['rule']>0){
                if($_GET['pos']=='up')
                    $firewall->move($_GET['rule'],'up');
                elseif($_GET['pos']=='down')
                    $firewall->move($_GET['rule'],'down');
                
                if($firewall->isSuccess()){
                    $_SESSION['ajax_msg_status'] = $vars['lang']['success'];
                }    
                else
                    $_SESSION['msg_status_error']= $firewall->error();
                header("Location: clientarea.php?action=productdetails&id=".$_GET['id']."&modop=custom&a=management&page=firewall");
                die();
            }    
            break;
        default: die();
    }
}


//get rules
$rules                      = $firewall->getList();
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
        if(in_array($val['firewall_rule']['id'], $deltedIds))
                continue;
        $val['firewall_rule']['interface_label'] = $vars['list_interfaces'][$val['firewall_rule']['network_interface_id']];
        $vars['rules'][$key]    = $val;
    }
}

if(isset($_SESSION['ajax_msg_status'])){
    $vars['msg_success'] = $_SESSION['ajax_msg_status'];
    unset($_SESSION['ajax_msg_status']);
}
if(isset($_SESSION['msg_status_error'])){
    $vars['msg_error'] = $_SESSION['msg_status_error'];
    unset($_SESSION['msg_status_error']);
}