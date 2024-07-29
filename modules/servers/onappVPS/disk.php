<?php

/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */
if($vars['disallow_action']['disk']==1){
    ob_clean();
    header ("Location: clientarea.php?action=productdetails&id=".$params['serviceid']);
    die();
}
$disk           = new NewOnApp_Disk();
$disk           -> setconnection($params,true);
$backup         = new NewOnApp_VMBackup();
$backup         ->setconnection($params,true);
$stores         = array();
$details = $vm ->getDetails();
$hypervisor_id = $details['virtual_machine']['hypervisor_id'];
$hypervisor    = new NewOnApp_Hypervisor($hypervisor_id);
$hypervisor->setconnection($params);
$res = $hypervisor->details();
$hypervisorZone = new NewOnApp_HypervisorZone($res['hypervisor']['hypervisor_group_id']);
$hypervisorZone->setconnection($params);
$data_stores = $hypervisorZone->getDataStores();

$configuration = new NewOnApp_Configuration();
$configuration->setconnection($params);
$configuration_details = $configuration->getDetails();

$vars['allow_incremental_backups'] = isset($configuration_details['settings']['allow_incremental_backups']) ? $configuration_details['settings']['allow_incremental_backups'] : '';

if($hypervisorZone->isSuccess() && empty($data_stores)){
    $data_stores = $hypervisor ->joinesDataStore();
    $ds = new NewOnApp_DataStore();
    $ds->setconnection($params);
    foreach($data_stores  as $key =>$value){
        if(!$value['data_store']['data_store_type']!='vmware')
            continue;
        $dsd = $ds->getDetails($value['data_store_join']['data_store_id']);
        $stores[ $dsd['data_store']['id']] =  $dsd['data_store']['label'];
    }
}
else if($hypervisorZone->isSuccess()){
    foreach($data_stores  as $key =>$value){
        if($value['data_store']['data_store_type']!='vmware')
            $stores[$value['data_store']['id']] = $value['data_store']['label'];
    }
} else $vars['msg_error'] = $hypervisorZone->error();

if(isset($_REQUEST['do'])){
    switch($_REQUEST['do']){
        case 'createBackup':
            if($_POST['disk_id']>0){
                $backup->create($_POST['disk_id'], array('backup' => array("force_windows_backup"=> 0)));    
                if($backup->isSuccess())
                    $vars['msg_success'] = $vars['lang']['backup_created'];
                else
                    $vars['msg_error']   =  $backup->error();
            }
            break;    
        case 'addDisk':
            $vars['step']   = 'addDisk';
            $vars['stores'] = $stores;
            break;
        case 'deleteDisk':
            if($_POST['disk_id']>0){
                
                $postData=[];
                if($_POST['delete']['force_reboot']){
                    $postData['force']=1;
                    $postData['shutdown_type'] = $_POST['delete']['shutdown_type'];
                    $postData['required_startup'] = (int) $_POST['delete']['required_startup'];
                }
                $disk->deleteDisk($params['customfields']['vmid'],$_POST['disk_id'], $postData);
                if($disk->isSuccess()){
                    $vars['msg_success'] = $vars['lang']['disk_removed'];
                } else $vars['msg_error'] = $disk->error();
            }
            break;
        case 'saveDisk':
            if(isset($_POST['add'])){
                $disks   = $disk ->getList($params['customfields']['vmid']);
                $sum     = 0;
                foreach($disks as $value){
                    $sum += $value['disk']['disk_size'];
                }
                
                $allow  = (int)$product->getConfig('additional_disk_size')-$sum;
                
                if(($_POST['add']['size']>$product->getConfig('additional_disk_size') || $_POST['add']['size']>$allow) && $product->getConfig('additional_disk_size') != ""){
                    $vars['msg_error'] = $vars['lang']['disk_too_high'];
                    break;
                }
                
                $newDisk = array (
                        'data_store_id'         => $_POST['add']['data_store'],
                        'disk_size'             => $_POST['add']['size'],
                        'label'                 => $_POST['add']['label'],
                        'is_swap'               => $_POST['add']['is_swap'] == 1 ? 1 : 0,
                        'mount_point'           => $_POST['add']['mount_point'],
                        'require_format_disk'   => $_POST['add']['require_format_disk'] == 1 ? 1 : 0,
                        'add_to_linux_fstab'    => $_POST['add']['add_to_linux_fstab'] == 1 ? 1 : 0,
                        'min_iops'              => 600,     
                    );
                
                if(!$newDisk['is_swap'] && $newDisk['mount_point'])
                    $newDisk['mounted'] =0;
                else if(!$newDisk['is_swap'] && !$newDisk['mount_point'])
                    $newDisk['mounted'] =0;
                if(!$newDisk['is_swap'])
                    $newDisk['file_system'] =  $_POST['add']['file_system'] ;
                
                
                $disk->addDisk($params['customfields']['vmid'],array(
                    'disk' => $newDisk
                ));
                

                if($disk->isSuccess()){
                    $vars['msg_success']  = $vars['lang']['disk_added']; 
                }
                else $vars['msg_error'] = $disk->error();
            }
            break;
    }
}

$disk           -> setconnection($params,false);

$disks   = $disk ->getList($params['customfields']['vmid']);

$value   = array();

foreach($disks as $key=>$value)
    {
   
    $disk_backups = $backup->getDiskBackup($params['customfields']['vmid'], $value['disk']['id']);
   
    $value['disk']['data_store_label'] = $stores[$value['disk']['data_store_id']];
    $value['disk']['count_backups']     = is_array($disk_backups) ? count($disk_backups) : 0;

 
    $vars['disks'][] = $value;
}

$details = $vm ->getDetails();
$hypervisor_id = $details['virtual_machine']['hypervisor_id'];
$hypervisor    = new NewOnApp_Hypervisor($hypervisor_id);
$hypervisor->setconnection($params);
$res = $hypervisor->details();
$zone = new NewOnApp_HypervisorZone($res['hypervisor']['hypervisor_group_id']);
$zone->setconnection($params);
$zoneDetail = $zone->getZone();
$isFederation = $zoneDetail['hypervisor_group']['federation_enabled'] || !empty($zoneDetail['hypervisor_group']['federation_id']);
$vars['isFederation']=  $isFederation;