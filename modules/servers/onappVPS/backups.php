<?php


/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */
if($vars['disallow_action']['backups']==1){
    ob_clean();
    header ("Location: clientarea.php?action=productdetails&id=".$params['serviceid']);
    die();
}

$backup           = new NewOnApp_VMBackup();
$backup           -> setconnection($params);

$configuration = new NewOnApp_Configuration();
$configuration->setconnection($params);
$configuration_details = $configuration->getDetails();

$vars['allow_incremental_backups'] = isset($configuration_details['settings']['allow_incremental_backups']) ? $configuration_details['settings']['allow_incremental_backups'] : '';

if(isset($_POST['do'])){
    switch($_POST['do']){
        case 'removeBackup':
            if($_POST['backup_id']>0){
                $backup->delete($_POST['backup_id']);       
                if($backup->isSuccess())
                    $vars['msg_success'] = $vars['lang']['backup_deleted'];
                else
                    $vars['msg_error']   =  $backup->error();
            }
        break;    
        case 'restoreBackup':
            if($_POST['backup_id']>0){
                $backup->restore($_POST['backup_id']);
                if($backup->isSuccess())
                    $vars['msg_success'] = $vars['lang']['backup_restored'];
                else
                    $vars['msg_error']   = $backup->error();
            }
        break;
        case 'createBackupIncremental':
            $backup->addIncrementralBackup($_POST['vm_id']);
            if($backup->isSuccess())
                $vars['msg_success'] = $vars['lang']['backup_incremental'];
            else
                $vars['msg_error']   = $backup->error();
        break;    
        case 'create_templateBackup':
           /* if($_POST['backup_id']>0){]
                $data = array('backup'=>array('label'=>$product->getConfig('backup_label'),'min_disk_size'=>$product->getConfig('backup_min_disk_size'))))
                $backup->convert($_POST['backup_id'],$data);
                if($backup->isSuccess())
                    $vars['msg_success'] = $vars['lang']['rebuilded'];
                else
                    $vars['msg_error']   = $backup->error();
            }*/
        break;
    }
}

$backup_servers   = $backup -> getServerList();
$backups          = $backup -> getList($params['customfields']['vmid']);
$servers          = array();
$vars['backups']  = array();

foreach($backup_servers  as $key =>$value)
    $servers[$value['backup_server']['id']] = $value['backup_server']['label'];

foreach($backups as $key=>$value){
    $value['backup']['server_label'] = $servers[$value['backup']['backup_server_id']];
    $vars['backups'][] = $value;
}