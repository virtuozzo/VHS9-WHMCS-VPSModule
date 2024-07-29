<?php

/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */
if ($vars['disallow_action']['ip'] == 1) {
    ob_clean();
    header("Location: clientarea.php?action=productdetails&id=" . $params['serviceid']);
    die();
}
$details = $vm->getDetails();
$network = new NewOnApp_Network($details['virtual_machine']['ip_addresses'][0]['ip_address']['network_id']);
$network->setconnection($params);
$network->setVM($params['customfields']['vmid']);

$ipAddress = new NewOnApp_IPAddressJoin($params['customfields']['vmid']);
$ipAddress->setconnection($params);
$vm_details = $details;
$vm_id = $details['virtual_machine']['id'];

if (isset($_POST['do'])) {
    switch ($_POST['do']) {
        case 'rebuildNetwork':
            $data = array('shutdown_type' => 'hard', 'required_startup' => 1, 'force' => 1);
            $network->rebuild($data);
            if ($network->isSuccess())
                $vars['msg_success'] = $vars['lang']['rebuilded'];
            else
                $vars['msg_error'] = $network->error();
            break;
    }
}

switch ($_REQUEST['doAction']) {

    case 'add_ip_address':
        if (isset($_POST['ip'])) {
            $ip_join = new NewOnApp_IPAddressJoin($vm_id);
            $ip_join->setapi($vm->getApi());
            
            $cn_ip = (int) $_POST['ip']['used_ip'];
            $i = 0;

            if ($i <= $cn_ip && $ip_join->getVersion() && version_compare($ip_join->getVersion(), "5.4.0", '>=')) { //5.4.0
                for ($j = 1; $j <= $cn_ip; $j++) {
                    if ($i <= $cn_ip) {
                        $data = array('ip_address' => array('ip_version' => '4', 'network_interface_id' => $_POST['ip']['network_interface_id']));
                        $ip_join->assign($data);
                        if ($ip_join->isSuccess()) {
                            $i++;
                        } else {
                            return $ip_join->error();
                        }
                    }
                }
            } else { //5.3.0
                for ($j = 1; $j <= $cn_ip; $j++) {
                    if ($i <= $cn_ip) {
                        $data = array('ip_address_join' => array('ip_version' => '4', 'network_interface_id' => $_POST['ip']['network_interface_id']));
                        $ip_join->assign($data);
                        if ($ip_join->isSuccess()) {
                            $i++;
                        } else {
                            $vars['msg_error'] = $ip_join->error();
                            break;
                        }
                    }
                }
            }

            if ($i < $cn_ip)
                $vars['msg_error'] = $vars['lang']['ip_error1'];
            else if ($ip_join->isSuccess())
                $vars['msg_success'] = $vars['lang']['ipadded'];
        }
        
        $vars['formAddIp'] = true;
        $vars['interfaces'] = $vm->getNetworkInterfaces();

        break;


    case 'removeIP':
        $ip_join = new NewOnApp_IPAddressJoin($vm_id);
        $ip_join->setapi($vm->getApi());
        ob_get_clean();
        $ip_join->delete($_POST['ip_id']);
        if ($ip_join->isSuccess())
            echo "success";
        else
            echo $ip_join->error();
        die();
        break;
}


$networks = $network->getList();
$ip_addresses = $ipAddress->getList();

$interfaces = array();
foreach ($vm->getNetworkInterfaces() as $int) {
    $interfaces[$int['network_interface']['id']] = $int['network_interface']['label'];
}

$networks_labels = array();
$vars['ip_addresses'] = array();

if (is_array($networks) && count($networks) > 0)
    foreach ($networks as $key => $value)
        $networks_labels[$value['network']['id']] = $value['network']['label'];

if (is_array($ip_addresses) && count($ip_addresses) > 0) {
    foreach ($ip_addresses as $key => $value) {
        $value['ip_address_join']['ip_address']['network_label'] = $networks_labels[$value['ip_address_join']['ip_address']['network_id']];
        $value['interface'] = $interfaces[$value['ip_address_join']['network_interface_id']];
        $vars['ip_addresses'][] = $value;
    }
  

}
$details = $vm->getDetails();
$hypervisor_id = $details['virtual_machine']['hypervisor_id'];
$hypervisor = new NewOnApp_Hypervisor($hypervisor_id);
$hypervisor->setconnection($params);
$res = $hypervisor->details();
$zone = new NewOnApp_HypervisorZone($res['hypervisor']['hypervisor_group_id']);
$zone->setconnection($params);
$zoneDetail = $zone->getZone();
$isFederation = $zoneDetail['hypervisor_group']['federation_enabled'] || !empty($zoneDetail['hypervisor_group']['federation_id']);
$vars['isFederation'] = $isFederation;
