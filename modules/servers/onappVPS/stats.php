<?php


/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */
if($vars['disallow_action']['stats']==1){
    ob_clean();
    header ("Location: clientarea.php?action=productdetails&id=".$params['serviceid']);
    die();
}

$charts = $vm->getUsageCPUChart();
if($vm->isSuccess()){
 $vars['chart']      = $charts;
}
else {
    $vars['chart']      = null;
    $vars['msg_error']  = $vm->error();

}


