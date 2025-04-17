<?php

use onappWrapper\PdoWrapper;

/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */
if (!defined('DS'))
{
    define('DS', DIRECTORY_SEPARATOR);
}

require_once ROOTDIR . DS . 'includes' . DS . 'onappWrapper' . DS . 'utility.php';
onapp_loadCLass();
require_once dirname(__FILE__) . DS . 'class' . DS . 'class.Product.php';
require_once dirname(__FILE__) . DS . 'class' . DS . 'class.whmcsUserMG.php';

function onappVPS_Load()
{
    static $runing;
    if (!$runing)
    {
        require ROOTDIR . DS . 'includes' . DS . 'onappWrapper' . DS . 'plugins' . DS . 'autoload.php';
        $runing = true;
    }
}
if (!function_exists('onappVPS_ConfigOptions'))
{

    function onappVPS_ConfigOptions($prameters = array())
    {

        $moduleVersion = '1.10.4';
        $ex            = explode(DS, $_SERVER['SCRIPT_FILENAME']);
        if ($_REQUEST['action'] != 'save' && end($ex) == 'configproducts.php')
        {//v6
            $data            = array();
            $data['mode']    = "advanced";
            $data['content'] = "";
            // setup params
            $product         = new onappVPS_Product($_REQUEST['id']);
            $params          = $product->getParams();

            try
            {

                if (empty($params))
                {
                    throw new \Exception('Please setup onappVPS server first');
                }
                $product->installDb();

                //  $billing = new NewOnApp_Billing();
                $bucket = new OnApp_Buckets();




                $hpv           = new NewOnApp_Hypervisor(null);
                //hypervisor_zone
                $hypervisor    = new NewOnApp_HypervisorZone($product->getConfig('hypervisor_zone'));
                $hpvZone       = new NewOnApp_HypervisorZone(null);
                $template      = new NewOnApp_Template(null);
                $networkZone   = new NewOnApp_NetworkZone(null);
                $dataStoreZone = new NewOnApp_DataStoreZone(null);
                $userGroup     = new NewOnApp_UserGroup(null);
                $userRole      = new NewOnApp_UserRole(null);

                $hpv->setconnection($params);
                $hpvZone->setconnection($params);
                $template->setconnection($params);
                $networkZone->setconnection($params);
                $dataStoreZone->setconnection($params);
                $userGroup->setconnection($params);
                $userRole->setconnection($params);
                //$billing->setconnection($params);
                $bucket->setconnection($params);   //zmiana 
                $hypervisor->setconnection($params);


                $api = $hpv->getApi();
                if (!$api->testConnection() && $api->getError())
                    throw new \Exception($api->getError());

                $hypervisors = $hypervisor->lisHPV();
                if ($hypervisor->isSuccess())
                {
                    foreach ($hypervisors as $key => $val)
                    {
                        if ($val['hypervisor']['online'] == 1)
                        {
                            $product->defaultConfig['hypervisor_id']['options'][$val['hypervisor']['id']] = $val['hypervisor']['label'];
                        }
                    }
                }

                $hypervisor_zones = $hpvZone->getZones();
                if (!$hpvZone->error())
                {
                    foreach ($hypervisor_zones as $key => $value)
                    {
                        $product->defaultConfig['hypervisor_zone']['options'][$value['hypervisor_group']['id']] = $value['hypervisor_group']['label'];
                    }
                }

                $templates = $template->getSystemTemplates();
                if($product->getConfig('showvCentertemplates')) {
                    $vcentertemplates = $template->getSystemTemplatesVcenter();
                }
                
                
                if (!$template->error())
                {
                    foreach ($templates as $template)
                    {
                        $product->defaultConfig['template_id']['options'][$template['image_template']['id']]                  = $template['image_template']['label'];
                        $product->defaultConfig['template_group']['options'][$template['image_template']['operating_system']] = $template['image_template']['operating_system'];
                    }
                    
                    if($product->getConfig('showvCentertemplates')) {
                        foreach ($vcentertemplates as $template)
                        {
                            $product->defaultConfig['template_id']['options'][$template['vcenter_image_template']['id']]                  = $template['vcenter_image_template']['label'];
                            $product->defaultConfig['template_group']['options'][$template['vcenter_image_template']['operating_system']] = $template['vcenter_image_template']['operating_system'];
                        }
                    }
                    
                }
                asort($product->defaultConfig['template_id']['options']);


                $networks = $networkZone->getList();
                if (!$networkZone->error())
                {
                    foreach ($networks as $network)
                    {
                        $product->defaultConfig['primary_network_id']['options'][$network['network_group']['id']] = $network['network_group']['label'];
                    }
                }

                $data_zones = $dataStoreZone->getList();
                if (!$dataStoreZone->error())
                {
                    foreach ($data_zones as $zone)
                    {
                        $product->defaultConfig['data_store_group_primary_id']['options'][$zone['data_store_group']['id']] = $zone['data_store_group']['label'];
                        $product->defaultConfig['data_store_group_swap_id']['options'][$zone['data_store_group']['id']]    = $zone['data_store_group']['label'];
                    }
                }

                $user_groups = $userGroup->getList();
                if (!$userGroup->error())
                {
                    foreach ($user_groups as $group)
                    {
                        $product->defaultConfig['user_group']['options'][$group['user_group']['id']] = $group['user_group']['label'];
                    }
                }

                $user_roles = $userRole->getList();
                if (!$userRole->error())
                {
                    foreach ($user_roles as $role)
                    {
                        $product->defaultConfig['user_role']['options'][$role['role']['id']] = $role['role']['label'];
                    }
                }


                // $billing_plans = $billing->getPlans();
//            if (!$billing->error())
//            {
//                if (count($billing_plans) > 500 || count($billing_plans) == 0)
//                {
//                    $product->defaultConfig['user_billing_plan']['type'] = 'text';
//                }
//                else
//                {
//                    foreach ($billing_plans as $plan)
//                    {
//                        $product->defaultConfig['user_billing_plan']['options'][$plan['user_plan']['id']] = '#' . $plan['user_plan']['id'] . ' ' . $plan['user_plan']['label'];
//                    }
//                    natcasesort($product->defaultConfig['user_billing_plan']['options']);
//                }
//            }
//            else
//            {
//              $product->defaultConfig['user_billing_plan']['type'] = 'text';
//            }
                /*                 * *************************************************************** */
//          
                $bucket_plans = $bucket->getBuckets();

                if (!$bucket->error())
                {
                    if (count($bucket_plans) == 0)
                    {
                        $product->defaultConfig['user_bucket_plan']['type'] = 'text';
                    }
                    else
                    {
                        foreach ($bucket_plans as $plan)
                        {
                            $product->defaultConfig['user_billing_plan']['options'][$plan['bucket']['id']] = '#' . $plan['bucket']['id'] . ' ' . $plan['bucket']['label'];
                        }
                        natcasesort($product->defaultConfig['user_billing_plan']['options']);
                    }
                }
                else
                {
                    $product->defaultConfig['user_billing_plan']['type'] = 'text';
                }



                $memory_unit  = $product->getConfig('memory_unit') ? $product->getConfig('memory_unit') : "MB";
                $primary_unit = $product->getConfig('primary_unit') ? $product->getConfig('primary_unit') : "GB";
                $swap_unit    = $product->getConfig('swap_unit') ? $product->getConfig('swap_unit') : "GB";
                $scripts      = '<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="https://code.jquery.com/ui/1.11.1/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css" /><script type="text/javascript">
				jQuery(document).ready(function(){
                                        jQuery("select[name=\'customconfigoption[memory_unit]\']").val("' . $memory_unit . '");
                                        jQuery("select[name=\'customconfigoption[primary_unit]\']").val("' . $primary_unit . '");
                                        jQuery("select[name=\'customconfigoption[swap_unit]\']").val("' . $swap_unit . '");
                                            
					jQuery("select[name=\'customconfigoption[template_group]\']").change(function(){
                                                jQuery("select[name=\'customconfigoption[template_id]\']").attr("disabled",true);
						var group = jQuery(this).val();
						jQuery.post(window.location.href, {action: \'gettemplates\',group:group, "packageconfigoption":null},function(data){
                                                        jQuery("select[name=\'customconfigoption[template_id]\']").attr("disabled",false);
							if(data!="")
								jQuery("select[name=\'customconfigoption[template_id]\']").html(data);
						});
					});
                                        
                                        jQuery("select[name=\'customconfigoption[hypervisor_zone]\']").change(function(){
                                            if(jQuery(this).val() != "-- not specified --")
                                            {
                                                jQuery("select[name=\'customconfigoption[hypervisor_id]\']").attr("disabled",true);
                                                jQuery.post(window.location.href, {action: \'gethypervisors\',zone: jQuery(this).val(),"packageconfigoption":null},function(data){
                                                        jQuery("select[name=\'customconfigoption[hypervisor_id]\']").attr("disabled",false);
                                                        jQuery("select[name=\'customconfigoption[hypervisor_id]\']").html(data);
						});
                                            }

                                        });

				});
			</script>';
                $scripts .= '<script type="text/javascript">
                        jQuery(document).ready(function(){
                                jQuery("#onappVPS_configurable_options").click(function(){
                                        jQuery("#config_options_spinner").remove();
                                        jQuery("#onappVPS_configurable_options").next("a").after("  <i id=\'config_options_spinner\' class=\'fa fa-spinner fa-spin\'></i>");
                                        jQuery.post(window.location.href, {"action":"onappVPS_setup_configurable_options", "productid":' . (int) $_REQUEST['id'] . ',"packageconfigoption":null}, function(res){
                                                alert(res.result);
                                                window.location.href = "configproducts.php?action=edit&id=' . (int) $_REQUEST['id'] . '&tab=5";
                                        }, "json")
                                        .done(function(){
                                            jQuery("#config_options_spinner").remove();
                                        });
                                        return false;
                                });

                                jQuery("#onappVPS_custom_fields").click(function(){
                                        jQuery.post(window.location.href, {"action":"onappVPS_setup_custom_fields", "productid":' . (int) $_REQUEST['id'] . ',"packageconfigoption":null}, function(res){
                                                alert(res.result);
                                                window.location.href = "configproducts.php?action=edit&id=' . (int) $_REQUEST['id'] . '&tab=4";
                                        }, "json");
                                        return false;
                                });
                                
                                jQuery("#onappVPS_synchronize_templates").click(function(){
                                     jQuery.post(window.location.href, {"action":"onappVPS_synchronize_templates", "productid":' . (int) $_REQUEST['id'] . ',"packageconfigoption":null}, function(res){
                                            jQuery("#dialog_template").html(res);
                                            jQuery( "#dialog_template" ).dialog({width:700,modal: true, buttons: {
                                                "Synchronie all items": function() {
                                                  jQuery.post(window.location.href, {"action":"onappVPS_synchronize_templates", "productid":' . (int) $_REQUEST['id'] . ',"packageconfigoption":null,replace:1}, function(res){
                                                        if(res=="success"){
                                                            alert("Synchronize process: success");
                                                            window.location.href = "configproducts.php?action=edit&id=' . (int) $_REQUEST['id'] . '&tab=4";
                                                        }
                                                    });
                                                    jQuery( this ).dialog( "close" );
                                                },
                                                Cancel: function() {
                                                  jQuery( this ).dialog( "close" );
                                                }
                                          }});
                                        });
                                        return false;
                                });
                        });
                        </script>';
                $data['content'] .= '<table style="width: 100%;"><tr>
                    <td class="fieldlabel mg">Configurable Options</td>
                    <td class="fieldarea mg"><a href="" id="onappVPS_configurable_options">Generate default</a> <a href="" class="so_popup"><img src="../modules/servers/onappVPS/img/info.png" title="This button will create Configurable Options for your product that optionally can be enabled. Your clients will be able to choose resources and server options during Create/Upgrade Process." /></a></td>
                    <td class="fieldlabel mg">Custom Fields</td>
                    <td class="fieldarea mg"><a href="" id="onappVPS_custom_fields">Generate default</a> <a href="" class="so_popup"><img src="../modules/servers/onappVPS/img/info.png" title="This button will create Custom Fields for your product that must be enabled." /></a></td>
               </tr>
               <tr>
                    <td class="fieldlabel mg">Templates</td>
                    <td class="fieldarea mg"><a href="" id="onappVPS_synchronize_templates">Synchronize</a> <a href="" class="so_popup"><img src="../modules/servers/onappVPS/img/info.png" title="This button will sychronize templates from OnApp." /></a></td>
                    <td></td>
                    <td></td>
               </tr>';


                $data['content'].= '<style type="text/css">
                        .mgContact {
                                float: right;
                                margin: 0;
                                background-color: #1c4b8c;
                                -moz-border-radius: 5px;
                                -webkit-border-radius: 5px;
                                -o-border-radius: 5px;
                                border-radius: 5px;
                                position: relative;
                                top: -5px;
                                width: 400px;
                                height: 30px;
                                padding: 3px;
                                text-align: center;
                                position: relative;
                                -webkit-border-radius: 5px;
                                -moz-border-radius: 5px;
                                border-radius: 5px;
                                background-color: #1c4b8c;
                        }
                        #whmcsdevbanner {
                        display: none!important;
                        }
                        #lightbox{
                        display: none!important;
                        }
                </style>
                <tr><td colspan="4" id="created-by-todelete"></td></tr>
                <script type="text/javascript">
            $(document).ready(function() 
            {
                // Created By
                if(!$("#created-by-mg").size())
                    $("#created-by-todelete").closest(".form").before(\'<p id="created-by-mg" style="text-align: left; margin-bottom: 5px; margin-top: 0;"><small>Version ' . $moduleVersion . '</small></p>\');

                                        // Now remove this unused row
                $("#created-by-todelete").remove();
           });
                </script>';

                $scripts .= '<div id="dialog_template" title="Synchronize templates" style="display:none;"></div>';

                //remove auto scalling    
                if ($product->getConfig('vmware') == 'Yes')
                {
                    $product->removeGroupConfig('gr7', 'gr8');
                }

                $data['content'].= $product->renderConfigOptions($scripts);
                $data['content'].= '</table>';
            }
            catch (Exception $ex)
            {
                $data['content'].= '<div class="errorbox"><strong><span class="title">' . $ex->getMessage() . '</span></strong></div>';
                $data['ex'] = print_r($ex, true);
            }
            if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == "simple")
            {//v7
                //ob_clean();
                header('Content-Type: application/json');
                echo json_encode($data);
                die();
            }
            else
            { //v6
                echo $data['content'];
            }
            return array();
        }
    }
}

/**
 * FUNCTION onappVPS_CreateAccount
 * Create user & VM
 *
 * @params array
 * @return string
 */
function onappVPS_CreateAccount($params, $double = false)
{

    try
    {
        onappVPS_Load();
        \NewOnApp_WrapperAPI::throwException();
        $actionController = new \OnAppVps\Controllers\Admin\ActionController($params);
        $product          = new onappVPS_Product($params['pid']);
        $actionController->setProduct($product);
        $actionController->preCreateValidation()
                ->userBuild();
        $dbUser           = $actionController->getUser();
        $userOnapp                = new NewOnApp_User($dbUser->onapp_id);
        $userOnapp->setconnection($params);
        $userOnappDetails = $userOnapp->getDetails();
           
        if(!isset($userOnappDetails['user']['email']) || !isset($dbUser->email) || $userOnappDetails['user']['email'] != $dbUser->email || !isset($userOnappDetails['user']['login']) || !isset($dbUser->username) || $userOnappDetails['user']['login'] != $dbUser->username)
        {
            $actionController->createNewUser($params['serviceid']);
            $dbUser = $actionController->getUser();
        }
        
        $product          = new onappVPS_Product($params['pid'], $params['serviceid']);
        if (!onapp_customFieldExists($params['pid'], 'vmid'))
        {
            $product->setupDefaultCustomFields();
        }
        $params_connection = $params;
        $vm                = new NewOnApp_VM();
        $vm->setconnection($params);
        $hpvZone           = new NewOnApp_HypervisorZone(null);
        $hpvZone->setconnection($params);

        if (!onapp_customFieldExists($params['pid'], 'vmid'))
        {
            return 'Custom fields dosen\'t exists.';
        }
        if (!onapp_customFieldExists($params['pid'], 'userid'))
        {
            return 'Custom fields dosen\'t exists.';
        }
        if (!empty($params['customfields']['vmid']))
        {
            try
            {
                $vm->setID($params['customfields']['vmid']);
                $exists = $vm->getDetails();
                return 'VM already exists, please remove it and try again.';
            }
            catch (\Exception $ex)
            {
                if ($ex->getCode() != 0)
                {
                    throw $ex;
                }
            }
        }
        $product->installDb();

        //check template
        $swap     = 1;
        $template = PdoWrapper::fetchAssoc(PdoWrapper::query("SELECT ps.optionname FROM  tblproductconfiggroups pg LEFT JOIN tblproductconfiglinks pl ON (pg.id = pl.gid) LEFT JOIN tblproductconfigoptions po ON (po.gid=pg.id) LEFT JOIN tblproductconfigoptionssub ps ON (ps.configid = po.id) WHERE po.optionname LIKE 'template_id|%' AND ps.optionname LIKE '" . (isset($params['configoptions']['template_id']) ? (int) $params['configoptions']['template_id'] : (int) $product->getConfig('template_id')) . "|%'"));
        if (strpos(strtolower($template['optionname']), 'windows') !== false)
        {
            $swap    = 0;
            $vm_pass = onapp_pass_generator(14, 0);
        }
        else
        {
            $vm_pass = onapp_pass_generator(14);
        }

        if (!empty($params['password']))
        {
            $vm_pass = $params['password'];
        }


        $params = array_merge($params, ['username' => $dbUser->getEmailAttribute(), 'password' => $dbUser->getApiKeyAttribute()]);
        $vm->setconnection($params, true);


        $hypervisor_id   = $product->getConfig('hypervisor_id');
        $hypervisor_zone = $product->getConfig('hypervisor_zone') == "-- not specified --" ? null : $product->getConfig('hypervisor_zone');
        if (isset($params['configoptions']['hypervisor_zone']))
        {
            $hypervisor_zone = $params['configoptions']['hypervisor_zone'];
        }

        $network_id = isset($params['configoptions']['network_group']) ? $params['configoptions']['network_group'] : $product->getConfig('primary_network_id');
	$primary_ds_id = isset($params['configoptions']['data_store']) ? $params['configoptions']['data_store'] : $product->getConfig('data_store_group_primary_id');
	$swap_ds_id = isset($params['configoptions']['swap_store']) ? $params['configoptions']['swap_store'] : $product->getConfig('data_store_group_swap_id');

        $templateID      = (isset($params['configoptions']['template_id']) ? $params['configoptions']['template_id'] : $product->getConfig('template_id'));
        //create VM
        $virtual_machine = [
            'virtual_machine' => [
                'label'                              => (!empty($params['customfields']['label']) ? $params['customfields']['label'] : $product->getConfig('label')),
                'memory'                             => $product->getValueWithUnit(isset($params['configoptions']['memory']) ? $params['configoptions']['memory'] : $product->getConfig('memory'), $product->getConfig('memory_unit')),
                'cpu_shares'                         => (isset($params['configoptions']['cpu_shares']) ? $params['configoptions']['cpu_shares'] : $product->getConfig('cpu_shares')),
                'hostname'                           => $params['customfields']['hostname'],
                'cpus'                               => (isset($params['configoptions']['cpus']) ? $params['configoptions']['cpus'] : $product->getConfig('cpus')),
                'primary_disk_size'                  => $product->getValueWithUnit(isset($params['configoptions']['primary_disk_size']) ? $params['configoptions']['primary_disk_size'] : $product->getConfig('primary_disk_size'), $product->getConfig('primary_unit'), 'GB'),
                'swap_disk_size'                     => ($swap === 0 ? null : $product->getValueWithUnit(isset($params['configoptions']['swap_disk_size']) ? $params['configoptions']['swap_disk_size'] : $product->getConfig('swap_disk_size'), $product->getConfig('swap_unit'), 'GB')),
                'template_id'                        => $templateID,
                'initial_root_password'              => $vm_pass,
                'hypervisor_group_id'                => $hypervisor_zone,
                'type_of_format'                     => $product->getConfig('type_of_format'),
                'rate_limit'                         => (isset($params['configoptions']['rate_limit']) ? $params['configoptions']['rate_limit'] : ($product->getConfig('rate_limit') != "" ? $product->getConfig('rate_limit') : 0)),
                'licensing_key'                      => (!empty($params['customfields']['licensing_key']) ? $params['customfields']['licensing_key'] : $product->getConfig('licensing_key')),
                'licensing_type'                     => $product->getConfig('licensing_type'),
                'licensing_server_id'                => $product->getConfig('licensing_server_id'),
                'required_virtual_machine_build'     => 1,
                'required_ip_address_assignment'     => 1,
                'required_virtual_machine_startup'   => 1,
                'initial_root_password_confirmation' => $vm_pass,
                'required_automatic_backup'          => $product->getConfig('required_automatic_backup'),
                'primary_disk_min_iops'              => $product->getConfig('primary_disk_min_iops'),
                'swap_disk_min_iops'                 => $product->getConfig('swap_disk_min_iops'),
		'primary_network_group_id'           => $network_id,
		'data_store_group_primary_id'        => $primary_ds_id,
		'data_store_group_swap_id'           => $swap_ds_id
            ],
        ];

# This code block would only set network and datastore zones if specified as a configuration option in WHMCS. 
/*    
        if(isset($params['configoptions']['network_group']) && !empty($params['configoptions']['network_group']))
        {
            $virtual_machine['primary_network_group_id'] = $params['configoptions']['network_group'];
        }
        if(isset($params['configoptions']['data_store']) && !empty($params['configoptions']['data_store']))
        {
            $virtual_machine['data_store_group_primary_id'] = $params['configoptions']['data_store'];
        }
        if(isset($params['configoptions']['swap_store']) && !empty($params['configoptions']['swap_store']))
        {
            $virtual_machine['data_store_group_swap_id'] = $params['configoptions']['swap_store'];
        }
*/
	    
        if($product->getConfig('showvCentertemplates'))
        {
            unset($virtual_machine['virtual_machine']['hypervisor_group_id']);
            
            $templatevc  = new NewOnApp_Template(null);
            $templatevc->setconnection($params_connection);
            $resources = $templatevc->getResourcePoolsVcenter();
            foreach($resources as $resource)
            {
                if($product->getConfig('vcenter_resource_pool_id') == $resource['vcenter_resource_pool']['label'])
                {
                    $virtual_machine['virtual_machine']['vcenter_resource_pool_id'] = $resource['vcenter_resource_pool']['id'];
                }
            }
        }
        
        $virtual_machine['virtual_machine']['domain'] = isset($params['domain']) && !empty($params['domain']) ? $params['domain'] : $params['customfields']['domain'];

        $service         = new \OnAppVps\Service\OnappService($params);
        $virtual_machine = $service->filterVMArrayByFederated($virtual_machine);

        if (!$product->getConfig('licensing_server_id') && $product->getConfig('licensing_type') == 'kms')
        {
            $templateOnApp                                = new NewOnApp_Template($templateID);
            $templateOnApp->setconnection($params_connection);
            $templateDetails                              = $templateOnApp->getTemplateDetails();
        
            $api              = $vm->getApi();
            $templatesDetails = $api->sendGET('/template_store');
            $templateGroupID  = null;
            foreach ($templatesDetails as $det)
            {
                foreach ($det['children'] as $children)
                {
                    foreach ($children['relations'] as $relation)
                    {
                        if ($relation['template_id'] == (string) $templateID)
                        {
                            $templateGroupID = $det['id'];
                            break;
                        }
                    }
                    if ($templateGroupID)
                    {
                        break;
                    }
                }
                if ($templateGroupID)
                {
                    break;
                }
            }
            $virtual_machine['virtual_machine']['licensing_server_id'] = $templateGroupID;
        }
        try
        {
            $result = $vm->create($virtual_machine);
        }
        catch (\Exception $ex)
        {
            if ($product->getConfig('userAccountPerVPS'))
            {
                $actionController->userDelete($onappUserId);
            }
            throw $ex;
        }

        if ($vm->isSuccess())
        {
            $vm->setconnection($params);
            onapp_addCustomFieldValue('vmid', $params['pid'], $params['serviceid'], $result['virtual_machine']['id']);
            onapp_addCustomFieldValue('userid', $params['pid'], $params['serviceid'], $result['virtual_machine']['user_id']);
            $vm->setID($result['virtual_machine']['id']);
            
            if (isset($params['configoptions']['accelerator']) && $params['configoptions']['accelerator'] == '1')
            {
                $vm->acceleratorEnable();
            }
            
            $cn_ip = (isset($params['configoptions']['ip_addresses']) ? $params['configoptions']['ip_addresses'] : $product->getConfig('ip_addresses'));
            $vm->assignIP($cn_ip, $result['virtual_machine']['ip_addresses'][0]['ip_address']['network_id'], $result['virtual_machine']['hypervisor_id'], $hypervisor_zone);

            $result  = $vm->getDetails();
            $ip_list = [];
            foreach ($result['virtual_machine']['ip_addresses'] as $ip)
            {
                $ip_list[] = $ip['ip_address']['address'];
            }
            PdoWrapper::query("UPDATE tblhosting SET `dedicatedip`=?,`assignedips`=? , `password`=? WHERE `id`=?", [($cn_ip > 0 ? array_shift($ip_list) : null), implode("\n", $ip_list), encrypt($vm_pass), $params['serviceid']]);
            if (!$vm->isSuccess())
            {
                return $vm->error();
            }

            return 'success';
        }
    }
    catch (\Exception $ex)
    {
        return $ex->getMessage();
    }
}

/**
 * FUNCTION onappVPS_TerminateAccount
 * Remove VM
 *
 * @params array
 * @return string
 */
function onappVPS_TerminateAccount($params)
{
    try
    {
        if (empty($params['customfields']['vmid']))
        {
            return 'VM not found!';
        }
        onappVPS_Load();

        $vm = new NewOnApp_VM($params['customfields']['vmid']);
        $vm->setconnection($params);

        $detailsVM = $vm->getDetails();
        $userid    = $detailsVM['virtual_machine']['user_id'];

        $actionController = new \OnAppVps\Controllers\Admin\ActionController($params);
        $user             = new NewOnApp_User($userid);
        $user->setconnection($params);
        $list             = $user->getVMList();
        if (!$user->isSuccess())
            return $user->error();

        if (is_array($list) && count($list) <= 1)
        {
            $actionController->userDelete($userid);
            onapp_addCustomFieldValue('vmid', $params['pid'], $params['serviceid'], '');
        }
        else
        {
            $vm->unsuspend();
            $vm->delete();
            logActivity(sprintf('OnApp VPS - Virtual Machine ID:%s Deleted - Service ID: %s', $params['customfields']['vmid'], $params['serviceid']));
            if (!$vm->isSuccess())
            {
                return $vm->error();
            }
            onapp_addCustomFieldValue('vmid', $params['pid'], $params['serviceid'], '');
        }
        return "success";
    }
    catch (\Exception $ex)
    {
        $error = $ex->getMessage() . "\r\n" . $ex->getTraceAsString();
        logModuleCall(
                "OnAppVPS", __FUNCTION__, print_r(), //request
                '', $error, //response
                array()
        );
        return $ex->getMessage();
    }
}
/**
 * FUNCTION onappVPS_SuspendAccount
 * Disable VM
 *
 * @params array
 * @return string
 */
if (!function_exists('onappVPS_SuspendAccount'))
{

    function onappVPS_SuspendAccount($params)
    {
        if (empty($params['customfields']['vmid']))
        {
            return 'VM not found!';
        }

        $vm = new NewOnApp_VM($params['customfields']['vmid']);
        $vm->setconnection($params);

        $vm->suspend();
        if ($vm->isSuccess())
        {
            return 'success';
        }
        else
        {
            return $vm->error();
        }
    }
}


/**
 * FUNCTION onappVPS_UnsuspendAccount
 * Enable VM
 *
 * @params array
 * @return string
 */
if (!function_exists('onappVPS_UnspendAccount'))
{

    function onappVPS_UnsuspendAccount($params)
    {
        if (empty($params['customfields']['vmid']))
        {
            return 'VM not found!';
        }

        $vm = new NewOnApp_VM($params['customfields']['vmid']);
        $vm->setconnection($params);

        $vm->unsuspend();
        if ($vm->isSuccess())
        {
            $vm->start();

            return 'success';
        }
        else
        {
            return $vm->error();
        }
    }
}


/**
 * FUNCTION onappVPS_ChangePackage
 * Modify VM
 *
 * @params array
 * @return string
 */
if (!function_exists('onappVPS_ChangePackage'))
{

    function onappVPS_ChangePackage($params)
    {

        onappVPS_Load();
        $upgradeRepository     = new OnAppVps\Reposiotry\UpgradeReposiotry();
        $isClientChangePackage = $upgradeRepository->isPending($params['serviceid']);
        $isAdminArea           = (boolean) preg_match("/clientsservices.php/", $_SERVER['REQUEST_URI']);

        $product = new onappVPS_Product($params['pid']);
        $vm      = new NewOnApp_VM($params['customfields']['vmid']);
        $vm->setconnection($params);
        $details = $vm->getDetails();

        try
        {
            if ($product->getConfig('user_billing_plan') && $det['user']['billing_plan_id'] != $product->getConfig('user_billing_plan'))
            {

                $user = new whmcsUserMG($params['clientsdetails']['id']);
                $user->setconnection($params);
                $user->isValidAccess();
                $det  = $user->getDetails();
                $user->setconnection($params);

                $user->edit(['user' => ['billing_plan_id' => $product->getConfig('user_billing_plan')]]);
                if (!$user->isSuccess())
                {
                    throw new Exception($user->error());
                }
            }
        }
        catch (Exception $e)
        {
            $error = $e->getMessage();
        }

        if (
                $details['virtual_machine']['label'] != (!empty($params['customfields']['label']) ? $params['customfields']['label'] : $product->getConfig('label')) ||
                $details['virtual_machine']['memory'] != (isset($params['configoptions']['memory']) ? $params['configoptions']['memory'] : $product->getConfig('memory')) ||
                $details['virtual_machine']['cpu_shares'] != (isset($params['configoptions']['cpu_shares']) ? $params['configoptions']['cpu_shares'] : $product->getConfig('cpu_shares')) ||
                $details['virtual_machine']['cpus'] != (isset($params['configoptions']['cpus']) ? $params['configoptions']['cpus'] : $product->getConfig('cpus'))
        )
        {
            $data = [
                'virtual_machine' => [
                    'label'                 => (!empty($params['customfields']['label']) ? $params['customfields']['label'] : $product->getConfig('label')),
                    'memory'                => $product->getValueWithUnit(isset($params['configoptions']['memory']) ? $params['configoptions']['memory'] : $product->getConfig('memory'), $product->getConfig('memory_unit'), 'MB'),
                    'cpu_shares'            => (isset($params['configoptions']['cpu_shares']) ? $params['configoptions']['cpu_shares'] : $product->getConfig('cpu_shares')),
                    'cpus'                  => (isset($params['configoptions']['cpus']) ? $params['configoptions']['cpus'] : $product->getConfig('cpus')),
                    'primary_disk_min_iops' => $product->getConfig('primary_disk_min_iops'),
                    'swap_disk_min_iops'    => $product->getConfig('swap_disk_min_iops'),
                ],
            ];

            foreach ($data['virtual_machine'] as $k => $v)
            {
                if (isset($details['virtual_machine'][$k]) && $details['virtual_machine'][$k] == $v)
                {
                    unset($data['virtual_machine'][$k]);
                }
            }
            if (!empty($data['virtual_machine']))
            {
                $vm->modify($data);
                if (!$vm->isSuccess())
                {
                    return $vm->error();
                }
            }
        }


        $clientChangePackage = (boolean) PdoWrapper::numRows(PdoWrapper::query("SELECT * FROM `tblupgrades` WHERE relid=? AND `status`= ?", array($params['serviceid'], "Pending")));
        $isRrebuild          = false;
        if ($clientChangePackage)
        {
            $q = PdoWrapper::query("SELECT po.id, po.optionname
                                               FROM  tblproductconfiggroups pg 
                                               LEFT JOIN tblproductconfiglinks pl ON (pg.id = pl.gid) 
                                               LEFT JOIN tblproductconfigoptions po ON (po.gid=pg.id) 
                                           WHERE po.optionname LIKE ? 
                                                 AND pl.pid = ?", array('template_id|%', $params['pid']));

            $templateOptionId = PdoWrapper::fetchAssoc($q);
            $templateOptionId = isset($templateOptionId['id']) ? (int) $templateOptionId['id'] : null;
            if ($templateOptionId)
            {

                $isRrebuild = (boolean) PdoWrapper::numRows(PdoWrapper::query("SELECT * FROM `tblupgrades` WHERE relid=? AND `status`= ? AND originalvalue LIKE ? ", array($params['serviceid'], "Pending", "{$templateOptionId}=>%")));
            }
        }
        if ($isRrebuild === true && isset($params['configoptions']['template_id']) && isset($details['virtual_machine']['template_id']) && $details['virtual_machine']['template_id'] != $params['configoptions']['template_id'])
        {
            $vm->rebuild(
                    [
                        'virtual_machine' =>
                        [
                            'template_id'         => $params['configoptions']['template_id'],
                            'licensing_type'      => $product->getConfig('licensing_type'),
                            'licensing_key'       => $product->getConfig('licensing_key'),
                            'licensing_server_id' => $product->getConfig('licensing_server_id'),
                            'type_of_format'      => $product->getConfig('type_of_format'),
                        ],
                    ]
            );
            if (!$vm->isSuccess())
            {
                return $vm->error();
            }
        }

        $cn_ip = (isset($params['configoptions']['ip_addresses']) ? $params['configoptions']['ip_addresses'] : $product->getConfig('ip_addresses'));
        if (count($details['virtual_machine']['ip_addresses']) != $cn_ip)
        {
            $vm->assignIP($cn_ip, $details['virtual_machine']['ip_addresses'][0]['ip_address']['network_id'], $details['virtual_machine']['hypervisor_id']);
        }

        $swap    = $product->getValueWithUnit(isset($params['configoptions']['swap_disk_size']) ? $params['configoptions']['swap_disk_size'] : $product->getConfig('swap_disk_size'), $product->getConfig('swap_unit'), 'GB');
        $primary = $product->getValueWithUnit(isset($params['configoptions']['primary_disk_size']) ? $params['configoptions']['primary_disk_size'] : $product->getConfig('primary_disk_size'), $product->getConfig('primary_unit'), 'GB');

        $diskChange = true;
        if (!$isAdminArea && $isClientChangePackage)
        {
            $primaryOptionId = $upgradeRepository->getOptionId('primary_disk_size|', $params['pid']);
            $swapOptionId    = $upgradeRepository->getOptionId('swap_disk_size|', $params['pid']);
            if (!$upgradeRepository->hasNewValue($primaryOptionId, $params['serviceid']) && !$upgradeRepository->hasNewValue($swapOptionId, $params['serviceid']))
                $diskChange      = false;
        }
        if ($diskChange === true && $details['virtual_machine']['total_disk_size'] != ($swap + $primary))
        {
            //resize disk
            $disk      = new NewOnApp_Disk();
            $disk->setconnection($params);
            $disk_list = $disk->getList($params['customfields']['vmid']);
            if ($disk->isSuccess())
            {
                $isSwap = 0;
                foreach ($disk_list as $key => $val)
                {
                    if ($val['disk']['is_swap'])
                    {
                        $isSwap = 1;
                        break;
                    }
                }
                foreach ($disk_list as $key => $val)
                {
                    $disk->setID($val['disk']['id']);
                    if ($val['disk']['is_swap'])
                    {
                        $data = [
                            'disk' => [
                                'disk_size'          => $swap,
                                "add_to_linux_fstab" => $isSwap
                            ],
                        ];
                    }
                    else
                    {
                        $data = [
                            'disk' => [
                                'disk_size'          => $primary,
                                "add_to_linux_fstab" => $isSwap
                            ],
                        ];
                    }
                    $disk->edit($data);
                    if (!$disk->isSuccess())
                    {
                        return $disk->error();
                    }
                }
            }
        }

        //change rate limit
        $interface = new NewOnApp_NetworkInterface($params['customfields']['vmid']);
        $interface->setconnection($params);
        $list      = $interface->getList();
        if ($interface->isSuccess())
        {
            foreach ($list as $value)
            {

                $rate_limit = (isset($params['configoptions']['rate_limit']) ? $params['configoptions']['rate_limit'] : ($product->getConfig('rate_limit') != "" ? $product->getConfig('rate_limit') : 0));
                if ($rate_limit == $value['network_interface']['rate_limit'])
                    continue;
                if (!$isAdminArea && $isClientChangePackage)
                {
                    $optionId = $upgradeRepository->getOptionId('rate_limit|', $params['pid']);
                    if (!$upgradeRepository->hasNewValue($optionId, $params['serviceid']))
                        continue;
                }
                $interface->save($value['network_interface']['id'], [
                    'network_interface' => [
                        'label'      => $value['network_interface']['label'],
                        'rate_limit' => $rate_limit,
                    ],
                ]);
                if (!$interface->isSuccess())
                {
                    return $interface->error();
                }
            }
        }

        return 'success';
    }
}


/**
 * FUNCTION onappVPS_ChangePassword
 * Change root password for VM
 *
 * @params array
 * @return string
 */
if (!defined('CLIENTAREA'))
{

    function onappVPS_ChangePassword($params)
    {
        if (empty($params['customfields']['vmid']))
        {
            return 'VM not found!';
        }

        $vm = new NewOnApp_VM($params['customfields']['vmid']);
        $vm->setconnection($params);

        $data = [
            'virtual_machine' => [
                'initial_root_password' => $params['password'],
            ],
        ];

        $vm->changePassword($data);
        if ($vm->isSuccess())
        {
            $user = new NewOnApp_User($params['customfields']['userid']);
            $user->setconnection($params);
            $user->edit([
                'user' => [
                    'password' => $params['password'],
                ],
            ]);
            if ($user->isSuccess())
            {
                PdoWrapper::query("UPDATE tblhosting SET `password`=?  WHERE `username`=?  AND `userid`=? ", array(encrypt($params['password']), $params['username'], (int) $params['clientsdetails']['id']));

                return 'success';
            }
            else
            {
                return $user->error();
            }
        }
        else
        {
            return $vm->error();
        }
    }
}

/**
 * FUNCTION onappVPS_Start
 * Start VM
 *
 * @params array
 * @return string
 */
if (!function_exists('onappVPS_Unlock'))
{

    function onappVPS_Unlock($params)
    {
        $vm = new NewOnApp_VM($params['customfields']['vmid']);
        $vm->setconnection($params);
        $vm->unlock();

        if ($vm->isSuccess())
        {
            @setcookie("console_vps[" . $params['customfields']['vmid'] . "]", $console['remote_access_session']['remote_key'], time() - 360);

            return 'success';
        }
        else
        {
            return $vm->error();
        }
    }
}


/**
 * FUNCTION onappVPS_Start
 * Start VM
 *
 * @params array
 * @return string
 */
if (!function_exists('onappVPS_Start'))
{

    function onappVPS_Start($params)
    {
        $vm = new NewOnApp_VM($params['customfields']['vmid']);
        $vm->setconnection($params);
        $vm->start();

        if ($vm->isSuccess())
        {
            @setcookie("console_vps[" . $params['customfields']['vmid'] . "]", $console['remote_access_session']['remote_key'], time() - 360);

            return 'success';
        }
        else
        {
            return $vm->error();
        }
    }
}


/**
 * FUNCTION onapVPS_Stop
 * Stop VM
 *
 * @params array
 * @return string
 */
if (!function_exists('onappVPS_Stop'))
{

    function onappVPS_Stop($params)
    {
        $vm = new NewOnApp_VM($params['customfields']['vmid']);
        $vm->setconnection($params);
        $vm->stop();

        if ($vm->isSuccess())
        {
            @setcookie("console_vps[" . $params['customfields']['vmid'] . "]", $console['remote_access_session']['remote_key'], time() - 360);

            return 'success';
        }
        else
        {
            return $vm->error();
        }
    }
}

/**
 * FUNCTION onapVPS_Shutdown
 * Shutdown VM
 *
 * @params array
 * @return string
 */
if (!function_exists('onappVPS_Shutdown'))
{

    function onappVPS_Shutdown($params)
    {
        $vm = new NewOnApp_VM($params['customfields']['vmid']);
        $vm->setconnection($params);
        $vm->shutdown();

        if ($vm->isSuccess())
        {
            @setcookie("console_vps[" . $params['customfields']['vmid'] . "]", $console['remote_access_session']['remote_key'], time() - 360);

            return 'success';
        }
        else
        {
            return $vm->error();
        }
    }
}

/**
 * FUNCTION onapVPS_Recovery
 * Start VM with recovery mode
 *
 * @params array
 * @return string
 */
if (!function_exists('onappVPS_Recovery'))
{

    function onappVPS_Recovery($params)
    {
        $product = new onappVPS_Product($params['pid']);
        if ($product->getConfig('vmware') == 'Yes')
        {
            return 'Method not allowed for VMware.';
        }
        $vm = new NewOnApp_VM($params['customfields']['vmid']);
        $vm->setconnection($params);
        $vm->recovery();

        if ($vm->isSuccess())
        {
            @setcookie("console_vps[" . $params['customfields']['vmid'] . "]", $console['remote_access_session']['remote_key'], time() - 360);

            return 'success';
        }
        else
        {
            return $vm->error();
        }
    }
}

/**
 * FUNCTION onapVPS_Rebuild
 * Rebuild VM disk
 *
 * @params array
 * @return string
 */
if (!function_exists('onappVPS_Rebuild'))
{

    function onappVPS_Rebuild($params)
    {
        $product = new onappVPS_Product($params['pid']);
        $vm      = new NewOnApp_VM($params['customfields']['vmid']);
        $vm->setconnection($params);
        $vm->rebuild([
            'virtual_machine' =>
            [
                'template_id'         => (isset($params['configoptions']['template_id']) ? $params['configoptions']['template_id'] : $product->getConfig('template_id')),
                'licensing_type'      => $product->getConfig('licensing_type'),
                'licensing_key'       => $product->getConfig('licensing_key'),
                'licensing_server_id' => $product->getConfig('licensing_server_id'),
                'type_of_format'      => $product->getConfig('type_of_format'),
            ],
        ]);

        if ($vm->isSuccess())
        {
            @setcookie("console_vps[" . $params['customfields']['vmid'] . "]", $console['remote_access_session']['remote_key'], time() - 360);

            return 'success';
        }
        else
        {
            return $vm->error();
        }
    }
}

/**
 * FUNCTION onapVPS_Reboot
 * Reboot VM
 *
 * @params array
 * @return string
 */
if (!function_exists('onappVPS_Reboot'))
{

    function onappVPS_Reboot($params)
    {
        $vm = new NewOnApp_VM($params['customfields']['vmid']);
        $vm->setconnection($params);
        $vm->reboot();

        if ($vm->isSuccess())
        {
            @setcookie("console_vps[" . $params['customfields']['vmid'] . "]", $console['remote_access_session']['remote_key'], time() - 360);

            return 'success';
        }
        else
        {
            return $vm->error();
        }
    }
}

function onappVPS_SyncUser($params)
{
    try
    {
        onappVPS_Load();
        \NewOnApp_WrapperAPI::throwException();
        $actionController = new \OnAppVps\Controllers\Admin\ActionController($params);
        $product          = new onappVPS_Product($params['pid']);
        $actionController->setProduct($product);
        $actionController->preCreateValidation()
                ->userBuild();
        onapp_addCustomFieldValue('userid', $params['pid'], $params['serviceid'], $actionController->getOnAppUserId());
        return 'success';
    }
    catch (Exception $ex)
    {
        return $ex->getMessage();
    }
}
/**
 * FUNCTION onappVPS_AdminCustoButtonArray
 * Display actions buttons
 *
 * @params array
 * @return array
 */
if (!function_exists('onappVPS_AdminCustomButtonArray'))
{

    function onappVPS_AdminCustomButtonArray()
    {

        $buttonarray = [
            'Start VM'    => "start",
            'Stop VM'     => "stop",
            'Shutdown VM' => "shutdown",
            'Reboot VM'   => "reboot",
            'Rebuild VM'  => "rebuild",
            'Recovery VM' => "recovery",
            'Unlock VM'   => "unlock",
            'Sync User'   => "SyncUser",
        ];

        return $buttonarray;
    }
}


/**
 * FUNCTION onapVPS_AdminServiceTabFields
 * Display VM details and console button
 *
 * @params array
 * @return string
 */
if (!function_exists('onappVPS_AdminServicesTabFields'))
{

    function onappVPS_AdminServicesTabFields($params)
    {

        if (empty($params['customfields']['vmid']))
        {
            return [];
        }

        $moduledir = substr(dirname(__FILE__), strlen(ROOTDIR) + 1);
        $vm        = new NewOnApp_VM($params['customfields']['vmid']);
        $vm->setconnection($params);
        if (!$_COOKIE['console_vps'][$params['customfields']['vmid']])
        {
            $console = $vm->getConsoleKey();
            @setcookie("console_vps[" . $params['customfields']['vmid'] . "]", $console['remote_access_session']['remote_key'], time() + 900);
        }
        else
        {
            $console['remote_access_session']['remote_key'] = $_COOKIE['console_vps'][$params['customfields']['vmid']];
        }
        $fields                            = [];
        $results                           = $vm->getDetails();
        $vpsdata                           = $results['virtual_machine'];
        $vpsdata['monthly_bandwidth_used'] = $vpsdata['monthly_bandwidth_used'];

        if ($console['remote_access_session']['remote_key'] != '')
        {//&& $vpsdata['booted']==true
            $uri               = urlencode('clientarea.php?action=productdetails&id=' . $params['serviceid'] . '&modop=custom&a=management&page=console');
            $fields['Console'] = '<button class="btn btn-default" id="consolebtn" onclick="window.open(\'../dologin.php?username=' . urlencode($params['clientsdetails']['email']) . '&goto=' . $uri . '\',\'\',\'width=820,height=700\'); return false;">Open</button>';
        }
        $lang = onapVPS_getLang($params);
        $lang = $lang['mainsite'];
        $ip   = null;
        if (isset($vpsdata['network_address']))
        {
            $ip = $vpsdata['network_address'];
        }
        elseif ($vpsdata['ip_addresses'])
        {
            foreach ($vpsdata['ip_addresses'] as $key => $val)
            {
                $ip .= $val['ip_address']['address'] . '<br />';
            }
        }
        if (isset($_POST['ajax']) && $_POST['ajax'] == 1 && isset($_POST['doAction']) && $_POST['doAction'] == 'details')
        {
            ob_clean();
            if ($vpsdata['id'] > 0)
            {
                die(json_encode($vpsdata));
            }
            else
            {
                die($vm->error());
            }
        }
        $fields['VM Details'] = ' 
            <div id="serverstats">
                <table class="table" style="width:400px;">
                    <tr><td>' . $lang['server_status'] . '</td><td><span id="serverstatus"></span> <a href="#" onclick="doAction(\'details\');return false;"><img src="../' . $moduledir . '/img/refresh.png" alt="" /></a></td></tr>
                    <tr><td>' . $lang['label'] . '</td><td class="vps_label">' . $vpsdata['label'] . '</td></tr>
                    <tr><td>' . $lang['booted'] . '</td><td class="vps_booted">' . ($vpsdata['booted'] == true ? '<span class="green">' . $lang['yes'] . '</span>' : '<span class="red">' . $lang['no'] . '</span>') . '</td></tr>
                    <tr><td>' . $lang['built'] . '</td><td class="vps_built">' . ($vpsdata['built'] == true ? '<span class="green">' . $lang['yes'] . '</span>' : '<span class="red">' . $lang['no'] . '</span>') . '</td></tr>
                    <tr><td>' . $lang['recovery_mode'] . '</td><td class="vps_recovery">' . ($vpsdata['recovery_mode'] == true ? '<span class="green">' . $lang['yes'] . '</span>' : '<span class="red">' . $lang['no'] . '</span>') . '</td></tr>
                    <tr><td>' . $lang['cpus'] . '</td><td>' . $vpsdata['cpus'] . '</td></tr>
                    <tr><td>' . $lang['shares'] . '</td><td>' . $vpsdata['cpu_shares'] . '%</td></tr>
                    <tr><td>' . $lang['memory_size'] . '</td><td>' . $vpsdata['memory'] . ' ' . $lang['MB'] . '</td></tr>
                    <tr><td>' . $lang['disk_size'] . '</td><td>' . $vpsdata['total_disk_size'] . ' ' . $lang['GB'] . '</td></tr>
                    <tr><td>' . $lang['monthly_bandwidth_used'] . '</td><td><span class="vps_bandwidth">' . $vpsdata['monthly_bandwidth_used'] . '</span> ' . $lang['GB'] . '</td></tr>
                    <tr><td>' . $lang['ip'] . '</td><td>' . $ip . '</td></tr>
                    <tr><td>' . $lang['template_image'] . '</td><td class="vps_template">' . $vpsdata['template_label'] . '</td></tr> 
                    <tr><td>' . $lang['created_at'] . '</td><td class="vps_created">' . $vpsdata['created_at'] . '</td></tr>
                    <tr><td>' . $lang['updated_at'] . '</td><td class="vps_updated">' . $vpsdata['updated_at'] . '</td></tr>
                    <tr><td>' . $lang['Domain'] . '</td><td>' . $vpsdata['domain'] . '</td></tr>
                </table>
            </div>
         <script type="text/javascript">
         function doAction(action){
         jQuery.post(window.location.href,{doAction: action,ajax:1},function(data){     
                if(action=="details"){
                    var obj = jQuery.parseJSON(data);
                    if(typeof obj ==\'object\'){
                        jQuery(".vps_label").text(obj.label);           
                        if(obj.booted==true)
                           jQuery(".vps_booted").html(\'<span class="green">' . $lang['yes'] . '</span>\');
                        else
                           jQuery(".vps_booted").html(\'<span class="red">' . $lang['no'] . '</span>\');
                       
                        if(obj.built==true)
                           jQuery(".vps_built").html(\'<span class="green">' . $lang['yes'] . '</span>\');
                        else
                           jQuery(".vps_built").html(\'<span class="red">' . $lang['no'] . '</span>\');
                       
                        if(obj.recovery_mode==true)
                           jQuery(".vps_recovery").html(\'<span class="green">' . $lang['yes'] . '</span>\');
                        else
                           jQuery(".vps_recovery").html(\'<span class="red">' . $lang['no'] . '</span>\');   
                        if(obj.locked==1){
                            jQuery("#modcmdbtns").find(".btn").attr("disabled",true);
                            jQuery("#consolebtn").attr("disabled",true);
                            jQuery("#modcmdbtns input[value=\'Unlock VM\']").attr(\'disabled\',false);
                        } else 
                        {
                            jQuery("#modcmdbtns").find(".btn").removeAttr(\'disabled\');
                            jQuery("#consolebtn").removeAttr("disabled");
                            jQuery("#modcmdbtns input[value=\'Unlock VM\']").attr(\'disabled\',true);
                        }
                       
                        jQuery(".vps_bandwidth").text(obj.monthly_bandwidth_used);
                        jQuery(".vps_created").text(obj.created_at);
                        jQuery(".vps_updated").text(obj.updated_at);  
                        jQuery("#vm_alerts").html(\'\');
                    } else jQuery("#serverstatus").text(data);
                }
                });
                }
           function dateFormat(date){
                var created = new Date(date);
                var day     = created.getDate();
                var month   = created.getMonth()+1; 
                var year    = created.getFullYear();
                var hours   = created.getHours();
                var min     = created.getMinutes();
                var sec     = created.getSeconds()

                return (day <= 9 ? \'0\' + day : day)+\'-\'+(month<=9 ? \'0\' + month : month)+\'-\'+year+\' \'+(hours <= 9 ? \'0\' + hours : hours)+\':\'+(min <= 9 ? \'0\' + min : min)+\':\'+(sec <= 9 ? \'0\' + sec : sec);
            }     
          
            function sendform(){
               var url = jQuery("#consolebtn").attr("rel");
               jQuery("#consoleform").attr("action",url);
               console.log(url);
               jQuery("#consoleform").submit();
            }
            jQuery(document).ready(function() {
                ' . (
                $vpsdata['locked'] == 1 ? '
                    jQuery("#modcmdbtns").find(".btn").attr("disabled",true);
                    jQuery("#consolebtn").attr("disabled",true);
                    jQuery("#modcmdbtns input[value=\'Unlock VM\']").attr(\'disabled\',false);' : ''
                )
                . '
                setInterval("doAction(\'details\')",20000);
                jQuery(document).ajaxStart(function() {
                    jQuery("#serverstatus").show();
                    jQuery("#serverstatus").html( "<img src=\"../modules/servers/onappVPS/img/loadingsml.gif\" />" );
                }).ajaxStop(function() {jQuery("#serverstatus").hide(); });
                
              
        });      
           </script>
           <style type="text/css">
            .green {font-weight:bold;color:green;}
            .red {font-weight:bold;color:red}
            .table td {border-bottom:1px solid #fff;}
           </style>
            ';
        if (empty($vpsdata['id']))
        {
            $fields['VM Details'] = 'VM not found';
        }

        return $fields;
    }
}

/**
 * FUNCTION onapVPS_getLang
 * Get user language
 *
 * @params array
 * @return string
 */
if (!function_exists('onapVPS_getLang'))
{

    function onapVPS_getLang($params)
    {
        global $CONFIG;
        if (!empty($_SESSION['Language']))
        {
            $language = strtolower($_SESSION['Language']);
        }
        else
        {
            if (strtolower($params['clientsdetails']['language']) != '')
            {
                $language = strtolower($params['clientsdetails']['language']);
            }
            else
            {
                $language = $CONFIG['Language'];
            }
        }

        $langfilename = dirname(__FILE__) . DS . 'lang' . DS . $language . '.php';
        if (file_exists($langfilename))
        {
            require_once($langfilename);
        }
        else
        {
            require_once(dirname(__FILE__) . DS . 'lang' . DS . 'english.php');
        }

        if (isset($lang))
        {
            return $lang;
        }
    }
}

/**
 * FUNCTION onapVPS_ClientAreaCustomButtonArray
 * Display buttons in clientArea
 *
 * @return array
 */
if (!function_exists('onappVPS_ClientAreaCustomButtonArray'))
{

    function onappVPS_ClientAreaCustomButtonArray()
    {
        $buttonarray = [
            "Management" => "management",
        ];

        return $buttonarray;
    }
}


/**
 * FUNCTION onapVPS_AdminLink
 * Login to admin panel
 *
 * @params int
 * @return array
 */
if (!function_exists('onappVPS_AdminLink'))
{

    function onappVPS_AdminLink($params)
    {
        return '<form target="_blank" action="http' . ($params['serversecure'] == 'on' ? 's' : '') . '://' . (empty($params['serverhostname']) ? $params['serverip'] : $params['serverhostname']) . '/users/sign_in" method="post">
                  <input type="hidden" name="user[login]" value="' . $params['serverusername'] . '" />
                  <input type="hidden" name="user[password]" value="' . $params['serverpassword'] . '" />
                  <input type="hidden" name="commit" value="Sign In" />
                  <input type="submit" value="Login to Control Panel" />
               </form>';
    }
}

/**
 * FUNCTION onapVPS_ClientArea
 * Display clientarea template
 *
 * @params array
 */
if (!function_exists('onappVPS_ClientArea'))
{

    function onappVPS_ClientArea($params)
    {
        global $smarty, $CONFIG;

        $moduledir = substr(dirname(__FILE__), strlen(ROOTDIR) + 1);
        $lang      = onapVPS_getLang($params);
        $product   = new onappVPS_Product($params['pid']);
        $vm        = new NewOnApp_VM($params['customfields']['vmid']);
        $vm->setconnection($params);
        $smarty->assign('lang', $lang['mainsite']);
        $smarty->assign('dir', $moduledir);
        $smarty->assign('params', $params);

        if (empty($params['customfields']['vmid']))
        {
            $smarty->assign('result', 1);
            $smarty->assign('resultmsg', $lang['module']['error7']);

            return;
        }

        /* AJAX START */
        if (isset($_POST['ajax']) && $_POST['ajax'] == 1 && isset($_POST['doAction']))
        {

            $allowed = [
                'reboot',
                'rebuild',
                'recovery',
                'stop',
                'start',
                'shutdown',
                'recovery',
                'details',
                'logs',
                'unlock',
                'showPass',
                'changePass',
                'changePassForm',
                'acceleratorEnable',
                'acceleratorDisable'
            ];
            if (!in_array($_POST['doAction'], $allowed))
            {
                die('Action not supported!');
            }
            $getParams = [];
            $method    = 'POST';
            $postData  = [];
            switch ($_POST['doAction'])
            {
                case 'recovery':
                    if ($product->getConfig('vmware') == 'Yes')
                    {
                        die('Method not allowed for VMware.');
                    }
                    $res          = $vm->recovery();
                    break;
                case 'reboot':
                    $res          = $vm->reboot();
                    break;
                case 'rebuild':
                    $postData     = [
                        'virtual_machine' => [
                            'template_id'         => $product->getConfig('template_id'),
                            'licensing_type'      => $product->getConfig('licensing_type'),
                            'licensing_key'       => $product->getConfig('licensing_key'),
                            'licensing_server_id' => $product->getConfig('licensing_server_id'),
                            'type_of_format'      => $product->getConfig('type_of_format'),
                        ],
                    ];
                    $res          = $vm->rebuild($postData);
                    break;
                case 'stop':
                    $res          = $vm->stop();
                    break;
                case 'start':
                    $res          = $vm->start();
                    break;
                case 'shutdown':
                    $res          = $vm->shutdown();
                    break;
                case 'unlock':
                    $res          = $vm->unlock();
                    break;
                case 'logs':
                    $transactions = $vm->getLogs();
                    if ($vm->isSuccess())
                    {
                        echo json_encode($transactions);
                        die();
                    }
                    break;
                case 'showPass':
                case 'changePassForm':
                case 'details':
                    $res  = $vm->getDetails();
                    break;
                case 'changePass':
                    $data = [
                        'virtual_machine' => [
                            'initial_root_password' => $_REQUEST['onapp_root_password'],
                        ],
                    ];

                    if (
                            strpos($_REQUEST['onapp_root_password'], '%') !== false || strpos($_REQUEST['onapp_root_password'], '"') !== false || strpos($_REQUEST['onapp_root_password'], '<') !== false ||
                            strpos($_REQUEST['onapp_root_password'], '>') !== false || strpos($_REQUEST['onapp_root_password'], '|') !== false || strpos($_REQUEST['onapp_root_password'], '^') !== false ||
                            strpos($_REQUEST['onapp_root_password'], '&') !== false || strpos($_REQUEST['onapp_root_password'], '(') !== false || strpos($_REQUEST['onapp_root_password'], ')') !== false
                    )
                    {
                        echo json_encode(['error' => $lang['mainsite']['change_pass_error']]);
                        die();
                    }

                    $vm->changePassword($data);
                    if ($vm->isSuccess())
                    {
                        sleep(2);
                        PdoWrapper::query("UPDATE tblhosting SET  `password`=? WHERE `id`=?", [encrypt($_REQUEST['onapp_root_password']), $params['serviceid']]);
                        echo json_encode(["result" => 1, "msg" => $lang['mainsite']['change_pass_success']]);
                        die();
                    }
                    break;
                case 'acceleratorEnable':
                    $vm->acceleratorEnable();
                    break;
                case 'acceleratorDisable':
                    $vm->acceleratorDisable();
                    break;
            }

            if ($vm->isSuccess())
            {
                $res['virtual_machine']['monthly_bandwidth_used'] = $res['virtual_machine']['monthly_bandwidth_used'];
                die(json_encode($res['virtual_machine']));
            }
            else
            {
                die(json_encode(['error' => $vm->error()]));
            }
        }
        /* AJAX END */
        $html5 = $product->getConfig('console');
        if ($html5 != 1)
        {
            $console = $vm->getConsoleKey();
            $smarty->assign('console', [
                'url' => 'http' . ($params['serversecure'] == 'on' ? 's' : '') . '://' . (empty($params['serverhostname']) ? $params['serverip'] : $params['serverhostname']),
                'key' => $console['remote_access_session']['remote_key'],
            ]);
        }
        else
        {
            $smarty->assign('console', ['html5' => 1]);
        }
        $results                                              = $vm->getDetails();
        $results['virtual_machine']['monthly_bandwidth_used'] = $results['virtual_machine']['monthly_bandwidth_used'];
        if ($vm->isSuccess())
        {
            $smarty->assign('vpsdata', $results['virtual_machine']);
            $transactions = $vm->getLogs();
            if ($vm->isSuccess())
            {
                $smarty->assign('curr_log_page', (isset($_GET['lp']) && $_GET['lp'] > 0 ? (int) $_GET['lp'] : 0));
                $smarty->assign('logs', $transactions);
            }
        }
        else
        {
            $smarty->assign('result', 1);
            $smarty->assign('resultmsg', $vm->error());
        }

        if (isset($_SESSION['msg_status']))
        {
            $smarty->assign('result', 'success');
            $smarty->assign('resultmsg', $_SESSION['msg_status']);
            unset($_SESSION['msg_status']);
        }

        //OnApp Billing Integration Code
        if (onAppBillingIsActived())
        {
            require_once ROOTDIR . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'addons' . DIRECTORY_SEPARATOR . 'OnAppBilling' . DIRECTORY_SEPARATOR . 'core.php';
            $row = PdoWrapper::fetchAssoc(PdoWrapper::query("SELECT product_id FROM OnAppBilling_settings WHERE product_id = ? AND enable=1", [$params['packageid']]));
            if ($row['product_id'])
            {
                $row           = PdoWrapper::fetchAssoc(PdoWrapper::query("SELECT currency FROM tblclients WHERE id = ?", [$_SESSION['uid']]));
                $user_currency = $row['currency'];

                getCurrency($_SESSION['uid']);

                $account = new OnAppBillingAccount($params['serviceid']);
                //Get Summay usage
                $summary = $account->getSummaryLines($params['packageid']);

                $out[] = [];
                foreach ($summary['lines'] as $sum_key => $sum)
                {
                    $out[$sum_key]['total']        = onAppFormatCurrency(convertCurrency($sum['amount'], 1, $user_currency));
                    $out[$sum_key]['usage']        = number_format($sum['usage'], 2);
                    $out[$sum_key]['FriendlyName'] = $sum['name'];
                    $out[$sum_key]['name']         = isset($sum['partName']) ? $sum['partName'] : '';
                    $out[$sum_key]['unit']         = $sum['unit'];
                }

                if ($out)
                {
                    $smarty->assign('mg_lang', MG_Language::getLang());
                    $smarty->assign('billing_resources', $out);
                    $smarty->assign('records_range', [
                        'start_date' => $summary['startDate'],
                        'end_date'   => $summary['endDate'],
                    ]);
                }
            }
        }
        //End Of OnApp Billing Intergration Code
        $accelerator = $product->getConfig('accelerator');
        $smarty->assign('disallow_action', [
            'firewall'     => $product->getConfig('manage_firewall'),
            'ip'           => $product->getConfig('manage_ip'),
            'network'      => $product->getConfig('manage_network'),
            'stats'        => $product->getConfig('manage_stats'),
            'disk'         => $product->getConfig('manage_disk'),
            'backups'      => $product->getConfig('manage_backups'),
            'autoscalling' => $product->getConfig('manage_autoscalling'),
            'api_info'     => $product->getConfig('show_api_info'),
            'accelerator'  => $accelerator,
        ]);
        $smarty->assign('hide_cpu', $product->getConfig('show_cpu_share'));

        $details       = $vm->getDetails();
        $hypervisor_id = $details['virtual_machine']['hypervisor_id'];
        $hypervisor    = new NewOnApp_Hypervisor($hypervisor_id);
        $hypervisor->setconnection($params);
        $res           = $hypervisor->details();
        $zone          = new NewOnApp_HypervisorZone($res['hypervisor']['hypervisor_group_id']);
        $zone->setconnection($params);
        $zoneDetail    = $zone->getZone();
        $isFederation  = $zoneDetail['hypervisor_group']['federation_enabled'] || !empty($zoneDetail['hypervisor_group']['federation_id']);
        $smarty->assign('isFederation', $isFederation);
    }
}

/**
 * FUNCTION onapVPS_management
 * Display extended pages in clientarea
 *
 * @params array
 * @return array
 */
function onappVPS_management($params)
{

    try
    {
        onappVPS_Load();
        global $CONFIG;

        $lang             = onapVPS_getLang($params);
        $moduledir        = substr(dirname(__FILE__), strlen(ROOTDIR) + 1);
        $vm               = new NewOnApp_VM($params['customfields']['vmid']);
        $vm->setconnection($params);
        $product          = new onappVPS_Product($params['pid']);
        $actionController = new \OnAppVps\Controllers\Admin\ActionController($params);
        $actionController->setProduct($product);

        $user         = new NewOnApp_User();
        $user_details = [];
        try
        {
            if (!$actionController->userValidation())
            {
                $actionController->userBuild();
                $user_id = $actionController->getOnAppUserId();
                onapp_addCustomFieldValue('userid', $params['pid'], $params['serviceid'], $user_id);
                $vm->changeOwner(["user_id" => $user_id]);
            }
            $dbUser       = $actionController->getUser();
            $user_details = ['username' => $dbUser->getEmailAttribute(), 'password' => $dbUser->getApiKeyAttribute()];
            $user->setconnection(array_merge($params, $user_details), true);
            $user->setUserID($params['customfields']['userid']);
            $user->getDetails();
            if (!$user->isSuccess() && $params["username"] && $params['password'])
            { // fix for old version of the module
                $user_details = ["username" => $params["username"], "password" => $params['password']];
            }
        }
        catch (Exception $ex)
        {
            $vars['msg_error'] = $ex->getMessage();
        }

        $params = array_merge($params, $user_details);

        $page              = (isset($_GET['page']) ? preg_replace('/[^A-Za-z0-9]/', '', $_GET['page']) : 'mainsite');
        $vars['main_lang'] = $lang['mainsite'];
        $vars['lang']      = $lang[(empty($page) ? 'mainsite' : $page)];
        $vars['dir']       = $moduledir;
        $vars['hostname']  = (!empty($params['serverhostname']) ? $params['serverhostname'] : $params['serverip']);
        $vars['params']    = $params;
        $vars['main_dir']  = dirname(__FILE__);

        if (empty($page) || !file_exists(dirname(__FILE__) . DS . $page . '.php') || !file_exists(dirname(__FILE__) . DS . 'templates' . DS . $page . '.tpl'))
        {
            $vars['lang'] = $lang['mainsite'];
            if (empty($params['customfields']['vmid']))
            {
                $vars['resultmsg'] = $lang['module']['error7'];
            }
            else
            {
                $vars['resultmsg'] = $lang['module']['error5'];
            }

            return ['vars' => $vars];
        }

        if($page == 'console')
        {
            $accelerator = false;
        }
        else {
            $accelerator = isset($params['configoptions']['accelerator']) ? $params['configoptions']['accelerator'] == "1" && $vm->acceleratorPresence() :
                $product->getConfig('accelerator') != 1 && $vm->acceleratorPresence();
        }

        $vars['disallow_action'] = [
            'firewall'                 => $product->getConfig('manage_firewall'),
            'ip'                       => $product->getConfig('manage_ip'),
            'network'                  => $product->getConfig('manage_network'),
            'stats'                    => $product->getConfig('manage_stats'),
            'disk'                     => $product->getConfig('manage_disk'),
            'backups'                  => $product->getConfig('manage_backups'),
            'autoscalling'             => $product->getConfig('manage_autoscalling'),
            'manage_autoscalling_up'   => $product->getConfig('manage_autoscalling_up'),
            'manage_autoscalling_down' => $product->getConfig('manage_autoscalling_down'),
            'api_info'                 => $product->getConfig('show_api_info'),
            'accelerator'              => $accelerator,
        ];
        require_once(dirname(__FILE__) . DS . $page . '.php');

        if (isset($_SESSION['msg_status']))
        {
            $vars['result']    = 'success';
            $vars['resultmsg'] = $_SESSION['msg_status'];
            unset($_SESSION['msg_status']);
        }

        //OnApp Billing Integration Code
        if (onAppBillingIsActived())
        {
            require_once ROOTDIR . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'addons' . DIRECTORY_SEPARATOR . 'OnAppBilling' . DIRECTORY_SEPARATOR . 'core.php';
            $row = PdoWrapper::fetchAssoc(PdoWrapper::query("SELECT product_id FROM OnAppBilling_settings WHERE product_id = ? AND enable=1", [$params['packageid']]));
            if ($row['product_id'])
            {
                $row           = PdoWrapper::fetchAssoc(PdoWrapper::query("SELECT currency FROM tblclients WHERE id = ?", [$_SESSION['uid']]));
                $user_currency = $row['currency'];

                getCurrency($_SESSION['uid']);

                $account = new OnAppBillingAccount($params['serviceid']);
                //Get Summay usage
                $summary = $account->getSummaryLines($params['packageid']);

                $out[] = [];
                foreach ($summary['lines'] as $sum_key => $sum)
                {
                    $out[$sum_key]['total']        = onAppFormatCurrency(convertCurrency($sum['amount'], 1, $user_currency));
                    $out[$sum_key]['usage']        = number_format($sum['usage'], 2);
                    $out[$sum_key]['FriendlyName'] = $sum['name'];
                    $out[$sum_key]['name']         = isset($sum['partName']) ? $sum['partName'] : '';
                    $out[$sum_key]['unit']         = $sum['unit'];
                }

                if ($out)
                {
                    $vars['mg_lang']           = $out;
                    $vars['billing_resources'] = $out;
                    $vars['records_range']     = [
                        'start_date' => $summary['startDate'],
                        'end_date'   => $summary['endDate'],
                    ];
                }
            }
        }
        //End Of OnApp Billing Intergration Code
        $vars['hide_cpu'] = $product->getConfig('show_cpu_share');
        if ($page == 'console' && $vars['console']['html5'])
        {
            try
            { //whmcs 6.0
                global $templates_compiledir;
                $sm                = new Smarty();
                $sm->compile_dir   = $templates_compiledir;
                $sm->template_dir  = dirname(__FILE__) . DS . 'templates' . DS;
                $sm->force_compile = 1;
                foreach ($vars as $k => $v)
                {
                    $sm->assign($k, $v);
                }

                echo $sm->fetch('console.tpl');
                die();
            }
            catch (Exception $ex)
            {
                echo $ex->getMessage();
                die();
            }
        }
    }
    catch (Exception $ex)
    {
        $vars['msg_error'] = $ex->getMessage();
    }

    return [
        'templatefile' => 'templates/' . $page,
        'breadcrumb'   => ' > <a href="#">Server Details</a>',
        'vars'         => $vars,
    ];
}

/**
 * Test Connection
 *
 * @param array $params
 * @return array
 * @throws Exception
 */
function onappVPS_TestConnection($params)
{
    try
    {

        $user = new NewOnApp_User(null);
        $user->setconnection($params);
        $user->getVersion();
        if (!$user->isSuccess())
        {
            throw new Exception((string) $user->error());
        }

        return [
            'success' => true,
        ];
    }
    catch (Exception $e)
    {
        return [
            'error' => $e->getMessage(),
        ];
    }
}
