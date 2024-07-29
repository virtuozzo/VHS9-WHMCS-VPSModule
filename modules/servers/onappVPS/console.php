<?php
error_reporting(0);
ini_set('display_errors',0);

/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */
if(ob_get_contents())
    ob_clean();

$details                    = $vm->getDetails();

if($details['virtual_machine']['hypervisor_type'] == 'vcenter')
{
    $vars['console_popup']      = $vm->getConsolePopUp();
}

$vars['vm']                 = $details['virtual_machine'];
$vars['console']            = $vm ->getConsoleKey();
$vars['params']             = $params;
$vars['console']['url']     = 'http'.($params['serversecure']=='on' ? 's' :'').'://'.(empty($params['serverhostname']) ? $params['serverip'] : $params['serverhostname']);