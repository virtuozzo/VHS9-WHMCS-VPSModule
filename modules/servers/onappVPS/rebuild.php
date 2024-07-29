<?php

use onappWrapper\PdoWrapper;

/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */
if (isset($_REQUEST['do']) && $_REQUEST['do'] == 'rebuildVPS') {
    if (isset($_REQUEST['template']) && $_REQUEST['template'] > 0) {
        
        $r  = PdoWrapper::fetchAssoc(PdoWrapper::query("
        SELECT 
            h.billingcycle,
            ps.id as optionid,
            ps.configid,
            ps.optionname,
            pr.*
        FROM  tblproductconfiggroups pg 
        LEFT JOIN tblproductconfiglinks pl ON (pg.id = pl.gid) 
        LEFT JOIN tblproductconfigoptions po ON (po.gid=pg.id) 
        LEFT JOIN tblproductconfigoptionssub ps ON (ps.configid = po.id) 
        LEFT JOIN tblpricing pr ON (pr.relid = ps.id)
        LEFT JOIN tblhosting h ON (h.packageid = pl.pid)
        LEFT JOIN tblservers s ON (s.id = h.server)
        WHERE 
            po.optionname LIKE 'template_id|%' 
            AND h.id='" . ValidateVariable('id','int','request') . "' 
            AND pl.pid = h.packageid
            AND pr.type = 'configoptions'
            AND s.type = 'onappVPS'
            AND h.userid = '".(int)$_SESSION['uid']."'
            AND pr.relid = '".ValidateVariable('template','int','request')."'"));

        
        $replace  = array('monthly','monthly','quarterly','semiannually','annually','biennially','triennially');
        $search = array('One Time','Monthly','Quarterly','Semi-Annually','Annually','Biennially','Triennially');
        $price = $r[str_replace($search, $replace, $r['billingcycle'])];

        $checkTemplate = PdoWrapper::fetchAssoc(PdoWrapper::query("SELECT * FROM `tblhostingconfigoptions` WHERE `relid` = '{$_REQUEST['id']}' and `configid` = '{$r['configid']}'"));

        if($price =='0.00' || $r['billingcycle'] == 'Free Account' || (isset($checkTemplate['optionid']) && $checkTemplate['optionid'] == $_REQUEST['template'])){
            $template = explode('|',$r['optionname']);
            $postData = array(
                        'virtual_machine'               => 
                            array(
                                'template_id'           => $template[0], 
                                'licensing_type'        => $product->getConfig('licensing_type'),
                                'licensing_key'         => $product->getConfig('licensing_key'),
                                'licensing_server_id'   => $product->getConfig('licensing_server_id'),
                                'type_of_format'        => $product->getConfig('type_of_format'),
                                'required_startup' => (int) $_REQUEST['required_startup']
                            ),
            );
            $vm->rebuild($postData);

            if ($vm->isSuccess()) {
                PdoWrapper::query("UPDATE tblhostingconfigoptions SET `optionid`='".$r['optionid']."' WHERE `relid`='".ValidateVariable('id','int','request')."' AND `configid`='".(int)$r['configid']."' LIMIT 1");
                $_SESSION['msg_status'] = $lang['rebuild']['vps_rebuilded'];
                header("Location: clientarea.php?action=productdetails&id=" . $params['serviceid']);
                die();
            } else {
                if (!empty($vm->error()))
                    $vars['msg_error'] = $vm->error();
                else {
                    $error = $vm->getResponse();
                    $error = json_decode($error['response_body'], true);
                    $vars['msg_error'] = $error['base'][0];
                }
            }
        } else $vars['msg_error'] = $lang['rebuild']['select_template'];    
    }
    else
        $vars['msg_error'] = $lang['rebuild']['select_template'];
}
$config = PdoWrapper::fetchAssoc(PdoWrapper::query("
        SELECT po.id 
        FROM  tblproductconfiggroups pg 
        LEFT JOIN tblproductconfiglinks pl ON (pg.id = pl.gid) 
        LEFT JOIN tblproductconfigoptions po ON (po.gid=pg.id) 
        LEFT JOIN tblproductconfigoptionssub ps ON (ps.configid = po.id) 
        WHERE po.optionname LIKE 'template_id|%' AND pl.pid =" . (int) $params['pid']));

$vars['confid'] = $config['id'];

$q_template = PdoWrapper::query("
        SELECT 
            h.billingcycle,
            ps.id as optionid,
            ps.optionname,
            pr.*
        FROM  tblproductconfiggroups pg 
        LEFT JOIN tblproductconfiglinks pl ON (pg.id = pl.gid) 
        LEFT JOIN tblproductconfigoptions po ON (po.gid=pg.id) 
        LEFT JOIN tblproductconfigoptionssub ps ON (ps.configid = po.id) 
        LEFT JOIN tblpricing pr ON (pr.relid = ps.id)
        LEFT JOIN tblhosting h ON (h.packageid = pl.pid)
        WHERE 
            po.optionname LIKE 'template_id|%' 
            AND h.id='" . (int) $params['serviceid'] . "' 
            AND pl.pid =" . (int) $params['pid'] . "
            AND pr.type = 'configoptions'");
$rows = array();
while ($r = PdoWrapper::fetchAssoc($q_template)) {
    $name           = explode('|', $r['optionname']);
    $rows[$name[0]] = array('price' => price($r['billingcycle'], $r), 'id' => $r['optionid']);
}

$template   = new NewOnApp_Template(null);
$template   -> setconnection($params);
$vm->setconnection($params);
$details = $vm ->getDetails();
$hypervisor_id = $details['virtual_machine']['hypervisor_id'];
$hypervisor    = new NewOnApp_Hypervisor($hypervisor_id);
$hypervisor->setconnection($params);
$res = $hypervisor->details();
$zone = new NewOnApp_HypervisorZone($res['hypervisor']['hypervisor_group_id']);
$zone->setconnection($params);
$zoneDetail = $zone->getZone();
$isFederation = $zoneDetail['hypervisor_group']['federation_enabled'] || !empty($zoneDetail['hypervisor_group']['federation_id']);
if($isFederation){
    $templates = $template ->getTemplatePricing($res['hypervisor']['hypervisor_group_id']);
}
if(empty($templates)){
    $resTemp = $template ->getTemplatePricing();
    $temp=[];
    $templates=[];
    $templateGroup = $product->getConfig("template_group");
    foreach($resTemp as $tempGroup){
        if($tempGroup['hypervisor_group_id']==$res['hypervisor']['hypervisor_group_id'] || empty($tempGroup['hypervisor_group_id']) ){
            if($templateGroup && $templateGroup!='all'){
                foreach($tempGroup['relations'] as $tk => $tv){
                    if($tv['image_template']['operating_system']!==$templateGroup){
                        unset($tempGroup['relations'][$tk]);
                    }
                }
            }
            $temp = array_merge($temp, $tempGroup['relations']);
        }
    }
    $templates['relations']= $temp;
}
    

if (!$template->error())
    foreach ($templates['relations'] as $template) {
         $key = 'federated_' .$template['template_id'];

        if(!$product->getConfig($key) && $params['configoptions']['country'])
            continue;
        if ( array_key_exists($template['template_id'], $rows)) {
            $vars['templates'][ucfirst($template['image_template']['operating_system'])][$rows[$template['template_id']]['id']] = array('price' => onAppFormatCurrency($rows[$template['template_id']]['price']), 'label' => $template['image_template']['label']);
            asort($vars['templates'][ucfirst($template['image_template']['operating_system'])]);
        }
    }
$vars['vpsdata'] = (isset($vm_details['virtual_machine']) ? $vm_details['virtual_machine'] : array());

$query    = PdoWrapper::query("SELECT * FROM tblhostingconfigoptions WHERE `relid`='".(int)$params['serviceid']."'");
$confdata = array();
while($r  = PdoWrapper::fetchAssoc($query))
{
    if($r['configid'] != $vars['confid'])
        $confdata[$r['configid']] = array('option' => $r['optionid'],'qty' => $r['qty']);
   
}

$vars['confdata'] = $confdata;

function price($billingcycle, $pricing) {
    $cycles = array('Monthly', 'Quarterly', 'Annually', 'Biennially', 'Triennially');
    if ($billingcycle == 'One Time') {
        return $pricing['monthly'];
    } else if (in_array($billingcycle, $cycles)) {
        return $pricing[strtolower($billingcycle)];
    } else if ($billingcycle == 'Semi-Annually') {
        return $pricing['semiannually'];
    }
}