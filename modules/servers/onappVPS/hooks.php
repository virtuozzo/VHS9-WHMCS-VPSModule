<?php
use onappWrapper\PdoWrapper;

/**
 * @author Grzegorz Draganik <grzegorz@modulesgarden.com>
 */
if (!defined('DS'))
    define('DS', DIRECTORY_SEPARATOR);
require ROOTDIR . DS . 'includes' . DS . 'onappWrapper' . DS .'plugins'.DS.'autoload.php';
if(basename($_SERVER["SCRIPT_FILENAME"], '.php') == 'upgrade' && $_POST['vm_action']=='rebuild'){
    include_once ROOTDIR . DS . 'includes' . DS . 'onappWrapper' . DS . 'utility.php';
    onapp_loadCLass();
    if(PdoWrapper::numRows(PdoWrapper::query("
        SELECT 
            h.id 
        FROM 
            tblhosting h 
        JOIN 
            tblproducts p ON(h.packageid = p.id) 
        WHERE 
            h.id        ='".(int)$_REQUEST['id']."'  AND 
            h.userid    ='".(int)$_SESSION['uid']."' AND 
            p.servertype='onappVPS'
    "))>0)
    {
    $keys = $_REQUEST['configoption'];
    $keys = array_keys($keys);
    $key   = (int)end($keys);
    $keys  =$_REQUEST['configoption'];
    $value = (int)end($keys);

    $r  = PdoWrapper::fetchAssoc(PdoWrapper::query("
        SELECT 
            h.billingcycle,
            ps.id as optionid,
            ps.optionname,
            pr.*,
            ho.optionid
        FROM  tblproductconfiggroups pg 
        LEFT JOIN tblproductconfiglinks pl ON (pg.id = pl.gid) 
        LEFT JOIN tblproductconfigoptions po ON (po.gid=pg.id) 
        LEFT JOIN tblproductconfigoptionssub ps ON (ps.configid = po.id) 
        LEFT JOIN tblpricing pr ON (pr.relid = ps.id)
        LEFT JOIN tblhosting h ON (h.packageid = pl.pid)
        LEFT JOIN tblservers s ON (s.id = h.server)
        LEFT JOIN tblhostingconfigoptions ho ON (ho.relid = h.id)
        WHERE 
            po.optionname LIKE 'template_id|%' 
            AND h.id        = '" . ValidateVariable('id','int','request') . "' 
            AND pl.pid      = h.packageid
            AND pr.type     = 'configoptions'
            AND s.type      = 'onappVPS'
            AND h.userid    = '".(int)$_SESSION['uid']."'
            AND ho.configid = '".(int)$key."'
            AND ps.id       = '".(int)$value."'    
            "));


    $replace  = array('monthly','monthly','quarterly','semiannually','annually','biennially','triennially');
    $search = array('One Time','Monthly','Quarterly','Semi-Annually','Annually','Biennially','Triennially');
    $price = $r[str_replace($search, $replace, $r['billingcycle'])];
    $checkTemplate = PdoWrapper::fetchAssoc(PdoWrapper::query("SELECT * FROM `tblhostingconfigoptions` WHERE `relid` = '{$_REQUEST['id']}' and `configid` = '{$key}'"));

    if($price =='0.00' || $r['billingcycle'] == 'Free Account' || (isset($checkTemplate['optionid']) && $checkTemplate['optionid'] == $value)){
                 ob_clean();
            header("Location: clientarea.php?action=productdetails&id=".ValidateVariable('id','int','request')."&modop=custom&a=management&page=rebuild&template=".$value."&do=rebuildVPS&required_startup={$_REQUEST['required_startup']}");
            exit;
    }
    }
}

if(basename($_SERVER["SCRIPT_FILENAME"], '.php') == 'upgrade' && $_POST['step']=="2" && !empty( $_SESSION['onappvps']['configoptions'])){
    //Federated support
    foreach( $_SESSION['onappvps']['configoptions'] as $temp){
        $_REQUEST['configoption'][$temp['id']] = $temp['selectedvalue'];
        $_POST['configoption'][$temp['id']] = $temp['selectedvalue'];
    }
    unset($_SESSION['onappvps']['configoptions']);
}


if (!defined('DS'))
	define('DS', DIRECTORY_SEPARATOR);

// save product configuration

if (isset($_SESSION['adminid'])  && isset($_REQUEST['action'])  && isset($_REQUEST['id']) && isset($_POST['packageconfigoption']) && !isset($_POST['servertype'])){
      include_once ROOTDIR . DS . 'includes' . DS . 'onappWrapper' . DS . 'utility.php';
      onapp_loadCLass();
      $product = PdoWrapper::fetchAssoc(PdoWrapper::query("select servertype from  `tblproducts` where id=?",array($_REQUEST['id'])));
      $_POST['servertype'] = $product['servertype'];
}

if (isset($_SESSION['adminid'])  && isset($_REQUEST['action'])  && isset($_REQUEST['id']) && isset($_POST['packageconfigoption']) && isset($_POST['servertype'])&& $_POST['servertype']=='onappVPS'){

    include_once ROOTDIR . DS . 'includes' . DS . 'onappWrapper' . DS . 'utility.php';
    onapp_loadCLass();
    include_once dirname(__FILE__)         . DS . 'class'        . DS . 'class.Product.php';
    $product     = new onappVPS_Product($_REQUEST['id']);
    $params      = $product->getParams();
    switch($_REQUEST['action']){
        case 'save':
            if(isset($_POST['servertype']) && $_POST['servertype']=='onappVPS'){
                $module = PdoWrapper::fetchAssoc(PdoWrapper::query("SELECT `servertype` FROM `tblproducts` WHERE `id`=?",array($_REQUEST['id'])));
                if($module['servertype']=='onapp'){

                    $role = PdoWrapper::fetchAssoc(PdoWrapper::query("SELECT `configoption21` FROM `tblproducts` WHERE `id`=?",array($_REQUEST['id'])));

                    $role = htmlspecialchars_decode($role['configoption21']);
                    $role = json_decode($role,true);

                    if(isset($_POST['packageconfigoption'][3]))
                        $product->saveConfig('memory',                          (int)$_POST['packageconfigoption'][3]);

                    if(isset($_POST['packageconfigoption'][5]))
                        $product->saveConfig('cpus',                            (int)$_POST['packageconfigoption'][5]);

                    if(isset($_POST['packageconfigoption'][75]))
                        $product->saveConfig('cpu_shares',                      (int)$_POST['packageconfigoption'][7]);

                    if(isset($_POST['packageconfigoption'][11]))
                        $product->saveConfig('primary_disk_size',               (int)$_POST['packageconfigoption'][11]);

                    if(isset($_POST['packageconfigoption'][9]))
                        $product->saveConfig('swap_disk_size',                  (int)$_POST['packageconfigoption'][9]);

                    if(isset($_POST['packageconfigoption'][18]))
                        $product->saveConfig('ip_addresses',                    (int)$_POST['packageconfigoption'][18]);

                    if(isset($_POST['ds_zones_primary']))
                        $product->saveConfig('data_store_group_primary_id',     (int)$_POST['ds_zones_primary']);

                    if(isset($_POST['ds_zones_swap']))
                        $product->saveConfig('data_store_group_swap_id',        (int)$_POST['ds_zones_swap']);

                    if(isset($_POST['billing_plan']))
                        $product->saveConfig('user_billing_plan',               (int)$_POST['billing_plan']);

                    if(isset($_POST['hvzones']))
                        $product->saveConfig('hypervisor_zone',                 (int)$_POST['hvzones']);

                    if(isset($_POST['packageconfigoption'][4]))
                        $product->saveConfig('hypervisor_id',                   (int)$_POST['packageconfigoption'][4]);

                    if(isset($_POST['role_ids']))
                        $product->saveConfig('user_role',                       json_encode($role['role_ids']));

                    if(isset($_POST['user_group']))
                        $product->saveConfig('user_group',                      $_POST['user_group']);

                    if(isset($_POST['packageconfigoption'][6]))
                        $product->saveConfig('primary_network_id',              $_POST['packageconfigoption'][6]);

                    if(isset($_POST['packageconfigoption'][8]))
                        $product->saveConfig('rate_limit',                      $_POST['packageconfigoption'][8]);

                    if(isset($_POST['autobackups']))
                        $product->saveConfig('required_automatic_backup',       (isset($_POST['autobackups']) ? 1 : ''));
                }

            }

            break;
        case 'gethypervisors':
            if(isset($_POST['zone']) && $_POST['zone'] > 0)
            {
                $hypervisor  = new NewOnApp_HypervisorZone($_POST['zone']);
                $hypervisor -> setconnection($params);
                $hypervisors = $hypervisor->lisHPV();
                echo "<option value='0'>Auto</option>";
                if($hypervisor->isSuccess())
                {
                    foreach($hypervisors as $key => $val)
                    {
                        if($val['hypervisor']['online'] == 1)
                            echo "<option value='".$val['hypervisor']['id']."'>".$val['hypervisor']['label']."</option>";
                    }
                }
            }
            die();
            break;
        case 'gettemplates':
            if(isset($_POST['group'])){
                $template    = new NewOnApp_Template(null);
                $template    ->setconnection($params);
                $templates   = $template->getSystemTemplates();
                foreach ($templates as $template){
                    if($template['image_template']['operating_system']==$_POST['group'] || $_POST['group']=='all')
                        echo  '<option value="'.$template['image_template']['id'].'">'.$template['image_template']['label'].'</option>';
                }
            }
            die();
            break;
        case 'onappVPS_setup_configurable_options':
            if ($product->hasConfigurableOptions()){
                die(json_encode(array("success" => 0, "result" => 'Product has already configurable options assigned.')));
            }
            else if ($product->setupDefaultConfigurableOptions()){
                $params = $product->getParams();
                $federated = new \OnAppVps\OnApp\OnAppFederated($params, $product->id);
                $federated->synchronize();
                   echo json_encode(array("success" => 1, "result" =>  'Default Configurable options have been created.'));
            }
            die();
            break;
        case 'onappVPS_setup_custom_fields':
            if(onapp_customFieldExists($_POST['productid'],'hostname') || onapp_customFieldExists($_POST['productid'],'vmid') || onapp_customFieldExists($_POST['productid'],'userid')){
                       die(json_encode(array("success" => 0, "result" => 'Product has already custom fields assigned.')));
            }
            if ($product->setupDefaultCustomFields()){
                       die(json_encode(array("success" => 1, "result" => 'Default custom fields have been created.')));
            }
            break;
        case 'onappVPS_synchronize_templates':


            if($_POST['replace']==1)
                $replace = true;
            else
                $replace = false;
            $result = $product->synchronize($replace);
            $federated = new \OnAppVps\OnApp\OnAppFederated($params, $product->id);
            $federated->synchronize();
            die($result );

        break;
        default:
            die();
        break;

    }

}

if (isset($_SESSION['adminid']) && isset($_POST['servertype']) && $_POST['servertype'] == 'onappVPS' && isset($_POST['customconfigoption']) && isset($_REQUEST['id'])){
        include_once ROOTDIR . DS . 'includes' . DS . 'onappWrapper' . DS . 'utility.php';
        onapp_loadCLass();
        include_once dirname(__FILE__)         . DS . 'class'        . DS . 'class.Product.php';
	$product = new onappVPS_Product($_REQUEST['id']);
	$product->clearConfig();
	foreach ($_POST['customconfigoption'] as $k => $v){
		$product->saveConfig($k, $v);
	}

}



if(!function_exists('hide_pass_in_clientarea')){
    function hide_pass_in_clientarea ($vars){
        //hide pass in client area
        if(!empty($vars['pid'])){
            include_once ROOTDIR . DS . 'includes' . DS . 'onappWrapper' . DS . 'utility.php';
            onapp_loadCLass();
            include_once dirname(__FILE__)         . DS . 'class'        . DS . 'class.Product.php';
            $product = new onappVPS_Product($vars['pid']);
            if($product->getConfig('show_user_details') == 1)
            {
                global $smarty;
                if(method_exists ( $smarty,'assign')){
                    $smarty->assign('password', null);
                    $smarty->assign('username', null);
                }
            }
        }

        if($vars['filename']=="clientarea" && $_GET['action']=="productdetails" && $vars['configurableoptions']){

            //Federated support
            $isFederated = false;
            $federateGroup = null;
            foreach($vars['configurableoptions'] as $key=>$value){
                if($value['optionname']=="City"){
                    $query = PdoWrapper::query("SELECT `federated`, `location_id`  FROM `onappVPS_LocationGroups` WHERE `city`=?", array($value['selectedoption']));
                    $row = PdoWrapper::fetchAssoc($query);
                    $isFederated = isset($row['federated']) && $row['federated']=="1";
                    $federateGroup = $row['location_id'];
                    break;
                }
            }
            $toRemove = array("Country","City","Hypervisor Zone","Network Group","Primary Data Store","Swap Data Store");
            if($isFederated){
                foreach($vars['configurableoptions'] as $key=>$value){
                    if(in_array($value['optionname'],  $toRemove)){
                      unset($vars['configurableoptions'][$key]);
                    }
                }
            }
            global $smarty;
            if(method_exists ( $smarty,'assign')){
                $smarty->assign('configurableoptions', $vars['configurableoptions']);
            }

        }
    }
}

if(!function_exists('remove_configoptions_from_upgrade')){
    function remove_configoptions_from_upgrade ($vars){


        if($vars['filename']!="upgrade")
            return;

        global $smarty;
        $tplVars = array();
        if(method_exists ( $smarty,'getTemplateVars')){
            $tplVars = $smarty->getTemplateVars();
        }else if(isset($smarty->_tpl_vars)){
            $tplVars = $smarty->_tpl_vars;
        }
        if(empty($tplVars))
            return;

        include_once ROOTDIR . DS . 'includes' . DS . 'onappWrapper' . DS . 'utility.php';
        onapp_loadCLass();

        if(PdoWrapper::numRows(PdoWrapper::query("SELECT h.id FROM tblhosting h JOIN tblproducts p ON(h.packageid = p.id) WHERE h.id=? AND p.servertype='onappVPS'",array($tplVars['id'])))==0)
                return;
        //primary
        foreach($tplVars['configoptions'] as $key=>$value){
            $fieldname = PdoWrapper::fetchAssoc(PdoWrapper::query("SELECT count(id) AS count FROM  `tblproductconfigoptions` WHERE `id`=? AND `optionname` LIKE 'primary_disk_size|%'",array($value['id'])));
            if($fieldname['count']>0){
                $ids = array();

                $selected      = PdoWrapper::fetchAssoc(PdoWrapper::query("SELECT id,optionname FROM  `tblproductconfigoptionssub` WHERE `id` = ?",array($value['selectedvalue'])));
                $selected_disk = explode('|',$selected['optionname']);
                if($selected_disk[0]>0)
                   $selected = $selected_disk[0];
                else
                   $selected = $selected['optionname'];

                foreach($value['options'] as $k =>$val){
                    $ids[]= (int)$val['id'];
                }
                if(empty($ids))
                    continue;
                $ip_values = PdoWrapper::query("SELECT id,optionname FROM  `tblproductconfigoptionssub` WHERE `id` IN (".implode(",", $ids).")");
                $remove = array();
                while($r = PdoWrapper::fetchAssoc($ip_values)){
                        $disk = explode('|',$r['optionname']);
                        if($disk[0]>0)
                            $new_value = $disk[0];
                        else
                            $new_value = $r['optionname'];

                        if($selected>$new_value){
                            $remove[]  = $r['id'];
                        }
                }
                foreach($value['options'] as $k =>$val){
                        if(in_array($val['id'],$remove)){
                        unset($tplVars['configoptions'][$key]['options'][$k]);}
                }

                if(count($tplVars['configoptions'][$key]['options'])==0 || strpos(strtolower($os),  strtolower('FreeBSD')>0)){
                        unset($tplVars['configoptions'][$key]);
                }

            }
        }

         //swap
        foreach($tplVars['configoptions'] as $key=>$value){
            //unset($tplVars['configoptions'][$key]);
            $fieldname = PdoWrapper::fetchAssoc(PdoWrapper::query("SELECT count(id) AS count FROM  `tblproductconfigoptions` WHERE `id`=? AND `optionname` LIKE 'swap_disk_size|%'",array($value['id'])));
            if($fieldname['count']>0){
                $ids = null;

                $selected       = PdoWrapper::fetchAssoc(PdoWrapper::query("SELECT id,optionname FROM  `tblproductconfigoptionssub` WHERE `id` = ?",array($value['selectedvalue'])));
                $selected_disk  = explode('|',$selected['optionname']);
                if($selected_disk[0]>0)
                   $selected = $selected_disk[0];
                else
                   $selected = $selected['optionname'];
                foreach($value['options'] as $k =>$val){
                    $ids .=$val['id'].",";
                }

                $ip_values = PdoWrapper::query("SELECT id,optionname FROM  `tblproductconfigoptionssub` WHERE `id` IN (".addslashes(substr($ids,0,-1)).")");
                $remove = array();
                while($r = PdoWrapper::fetchAssoc($ip_values)){
                        $disk = explode('|',$r['optionname']);
                        if($disk[0]>0)
                            $new_value = $disk[0];
                        else
                            $new_value = $r['optionname'];

                        if($selected>$new_value){
                            $remove[] = $r['id'];
                        }
                }
                foreach($value['options'] as $k =>$val){
                        if(in_array($val['id'],$remove)){
                        unset($tplVars['configoptions'][$key]['options'][$k]);}
                }

                if(count($tplVars['configoptions'][$key]['options'])==0 ){
                        unset($tplVars['configoptions'][$key]);
                }

            }
        }



        //Federated support
        $isFederated = false;
        $federateGroup = null;
        foreach($tplVars['configoptions'] as $key=>$value){
            if($value['optionname']=="City"){
                $query = PdoWrapper::query("SELECT `federated`, `location_id`  FROM `onappVPS_LocationGroups` WHERE `city`=?", array($value['selectedoption']));
                $row = PdoWrapper::fetchAssoc($query);
                $isFederated = isset($row['federated']) && $row['federated']=="1";
                $federateGroup = $row['location_id'];
                break;
            }
        }
        $toRemove = array("Country","City","Hypervisor Zone","Network Group","Primary Data Store","Swap Data Store");

        if($isFederated){
             $_SESSION['onappvps']['configoptions'] = array();
            foreach($tplVars['configoptions'] as $key=>$value){
                if(in_array($value['optionname'],  $toRemove)){
                  $_SESSION['onappvps']['configoptions'][$key]=$tplVars['configoptions'][$key];
                  unset($tplVars['configoptions'][$key]);
                }

                if($value['optionname']=="OS Template" && $federateGroup){
                    foreach($value['options'] as $k2  => $v2){
                        $template = PdoWrapper::query("SELECT optionname FROM tblproductconfigoptionssub WHERE id=?", array( $v2['id']));
                        $template = PdoWrapper::fetchAssoc($template);
                        list($templateId, $templateName) = explode("|", $template['optionname']);
                        $count = PdoWrapper::query("SELECT COUNT(`id`) AS count FROM `onappVPS_FederatedTemplates` "
                                . "                   WHERE `onapp_id` = ? AND `location_id`=? ", array($templateId, $federateGroup));
                        $count = PdoWrapper::fetchAssoc($count);
                        if($count['count']=="0"){
                           unset($tplVars['configoptions'][$key]['options'][$k2]);
                        }

                    }
                }
            }
        }

        if(method_exists ( $smarty,'assign')){
            $smarty->assign('configoptions', $tplVars['configoptions']);
        }
        unset($tplVars);

    }
}


add_hook('ClientAreaHeadOutput',10,'hide_pass_in_clientarea');
add_hook('ClientAreaHeadOutput',10,'remove_configoptions_from_upgrade');

add_hook('ClientAreaHeaderOutput', 999999, function($vars) {
    if($vars['filename'] == 'cart' && $_GET['a'] == 'confproduct')
    {
        $pid = $vars['productinfo']['pid'];
        $product = new onappVPS_Product($pid);
        $config = $product->loadConfig();

        $result = PdoWrapper::query("SELECT * FROM `tblproducts` WHERE `id` = ? LIMIT 1", array($pid));
        $row = PdoWrapper::fetchAssoc($result);

        if($row['servertype'] == 'onappVPS' && isset($vars['configurableoptions']) && !empty($vars['configurableoptions']))
        {

            $templates = array();
            $accelerator = array();
            $hypervisorZone = array();
            $memoryOption = array();
            $diskOption = array();

            foreach($vars['configurableoptions'] as $configurable)
            {
                if($configurable['optionname'] == 'OS Template')
                {
                    $templates = $configurable;
                }
                else if($configurable['optionname'] == 'Accelerator')
                {
                    $accelerator = $configurable;
                }
                else if($configurable['optionname'] == 'Hypervisor Zone')
                {
                    $hypervisorZone = $configurable;
                }
                else if($configurable['optionname'] == 'Memory')
                {
                    $memoryOption = $configurable;
                }
                else if($configurable['optionname'] == 'Primary Disk Size')
                {
                    $diskOption = $configurable;
                }
            }

            $product = new onappVPS_Product($pid);
            $accelerator_config = $product->getConfig('accelerator');

            $hpvZoneAPI = new NewOnApp_HypervisorZone(null);
            $templateAPI = new NewOnApp_Template(null);

            $params = $product->getParams();

            $templateAPI->setconnection($params);
            $hpvZoneAPI->setconnection($params);
            $hasFederationSupport = $templateAPI->hasFederationSupport();

            $fieldid = $templates['id'];
            $removeValues = '';
            $templatesOption = $templates['options'];

            $allowTemplateJSON = [];
            $allowTemplateJSON['removeOptions'] = [];

            foreach($templatesOption as $template)
            {
                if($hasFederationSupport && (!isset($config['federated_'.$template['required']]) || $config['federated_'.$template['required']] != '1'))
                {
                    $removeValues.= <<< JS
                        $('select[name="configoption[{$fieldid}]"]').find('option[value={$template['id']}]').remove();
JS;
                    $allowTemplateJSON['removeOptions'][] = $template['id'];
                }
                if($accelerator_config == '1')
                {
                    $removeValues.= <<< JS
                        $('input[name="configoption[{$accelerator['id']}]"]').parents('.form-group').remove();
JS;
                }
            }

            $allowTemplate = '';
            $hypervisorZoneSelectedID = $hypervisorZone['selectedvalue'];

            if($_GET['ajax_hpvZone'])
            {
                $hypervisorZoneSelectedID = $_GET['ajax_hpvZone'];
            }

            $activeHpvZone = [];
            foreach($hypervisorZone['options'] as $hpvZoneOption)
            {
                if($hpvZoneOption['id'] == $hypervisorZoneSelectedID)
                {
                    $activeHpvZone  = $hpvZoneOption;
                }
            }

            $allTemplates = [];
            $allHypervisorZones = [];


            $templatesAPI = $templateAPI->getSystemTemplates();
            $hypervisor_zonesAPI = $hpvZoneAPI->getZones();
            foreach($hypervisor_zonesAPI as $hpvZoneData)
            {
                $allHypervisorZones[$hpvZoneData['hypervisor_group']['id']] = $hpvZoneData['hypervisor_group'];
            }
            foreach($templatesAPI as $templateData)
            {
                $allTemplates[$templateData['image_template']['id']] = $templateData['image_template'];
            }

            if($product->getConfig('showvCentertemplates')) {
                $vcentertemplates = $templateAPI->getSystemTemplatesVcenter();
                foreach($vcentertemplates as $templateData)
                {
                    $allTemplates[$templateData['vcenter_image_template']['id']] = $templateData['vcenter_image_template'];
                }
            }

            $allowTemplateJSON['fieldid'] = $fieldid;
            $federation_id = $allHypervisorZones[$activeHpvZone['required']]['federation_id'];
            $templateMinValuesJSON = [];

            foreach($templatesOption as $templateData)
            {
                $remoteTemplateInfo = $allTemplates[$templateData['required']];

                if (isset($remoteTemplateInfo)) {
                    $templateMinValuesJSON[$templateData['id']] = [];
                    $templateMinValuesJSON[$templateData['id']]['min_disk_size'] = $remoteTemplateInfo['min_disk_size'];
                    $templateMinValuesJSON[$templateData['id']]['min_memory_size'] = $remoteTemplateInfo['min_memory_size'];
                }

                if ($hasFederationSupport) {
                    $remote_id = $remoteTemplateInfo['remote_id'];

                    if ($remote_id == $federation_id) {
                        $allowTemplate .= "$('select[name=\"configoption[{$fieldid}]\"]').append('<option value=\"{$templateData['id']}\">{$templateData['name']}</option>');";
                        $allowTemplateJSON['options'][] = ['id' => $templateData['id'], 'name' => $templateData['name']];
                    } elseif (strpos($remote_id, $federation_id) !== false) {
                        $allowTemplate .= "$('select[name=\"configoption[{$fieldid}]\"]').append('<option value=\"{$templateData['id']}\">{$templateData['name']}</option>');";
                        $allowTemplateJSON['options'][] = ['id' => $templateData['id'], 'name' => $templateData['name']];
                    }
                } else {
                    $allowTemplate .= "$('select[name=\"configoption[{$fieldid}]\"]').append('<option value=\"{$templateData['id']}\">{$templateData['name']}</option>');";
                    $allowTemplateJSON['options'][] = ['id' => $templateData['id'], 'name' => $templateData['name']];
                }
            }

            if($_GET['ajax_hpvZone'])
            {
                die(json_encode($allowTemplateJSON));
            }

            $templateMinValuesJSON = json_encode($templateMinValuesJSON);

            $customjs = <<< JS
                <script>
                    $(document).ready(function(){
                        var templateMinValues = JSON.parse('{$templateMinValuesJSON}');
                        
                        function setMinValue(el, minValue) {
                            if (el) {
                                var currentValue = parseInt(el.val(), 10);
                                
                                el.data("ionRangeSlider").update({
                                    min: minValue,
                                    from: Math.max(currentValue, minValue) 
                                });    
                            }
                        }
                        
                        function applyMinTemplateValues(templateId) {
                            var minValues = templateMinValues[templateId] || {};
                            
                            if (typeof minValues.min_memory_size !== 'undefined') {
                                setMinValue(jQuery("#inputConfigOption{$memoryOption['id']}"), minValues.min_memory_size);
                            }
                            
                            if (typeof minValues.min_disk_size !== 'undefined') {
                                setMinValue(jQuery("#inputConfigOption{$diskOption['id']}"), minValues.min_disk_size);
                            }
                        }
                        
                        $('body').on('change', '#inputConfigOption{$templates['id']}', function () {
                            applyMinTemplateValues(this.value);
                        });
                                                
                        $('body').on('change', '#inputConfigOption{$hypervisorZone['id']}', function() {
                            
                            $.ajax({
                                data : { ajax_hpvZone : $(this).val() },
                                type : 'GET',
                                dataType : 'json',
                                beforeSend: function(){
                                    $('select[name="configoption[{$fieldid}]"] option').remove();
                                    $('select[name="configoption[{$fieldid}]"]').append('<option value="">Loading...</option>');
                                    $('#btnCompleteProductConfig').prop('disabled', true);
                                },
                                success : function(response) {
                                    $('select[name="configoption['+response.fieldid+']"] option').remove();
                                    if (typeof response.options !== 'undefined' && response.options.length > 0) {
                                        for (index = 0; index < response.options.length; ++index) {
                                            $('select[name="configoption['+response.fieldid+']"]').append('<option value="'+response.options[index].id+'">'+response.options[index].name+'</option>');
                                        }
                                    }
                                    
                                    if (typeof response.removeOptions !== 'undefined' && response.removeOptions.length > 0) {
                                        for (index = 0; index < response.removeOptions.length; ++index) {
                                            $('select[name="configoption['+response.fieldid+']"]').find('option[value='+response.removeOptions[index]+']').remove();
                                        }
                                    }
                                    $('#btnCompleteProductConfig').prop('disabled', false);
                                    $('#orderSummaryLoader').hide();
                                }
                            });
                        
                        });
                        
                        $('select[name="configoption[{$fieldid}]"] option').remove();
                        {$allowTemplate}
                        {$removeValues}
                        
                        applyMinTemplateValues($('select[name="configoption[{$templates['id']}]"]').val());
                    });
                </script>
JS;

            return $customjs;
        }

    }
});
