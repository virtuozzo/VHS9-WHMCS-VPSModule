<?php
/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */
if ($vars['disallow_action']['autoscalling'] == 1) {
    ob_clean();
    header("Location: clientarea.php?action=productdetails&id=".$params['serviceid']);
    die();
}

  $vm = new NewOnApp_VM($vars['params']['customfields']['vmid']);
  $vm->setconnection($params);

if (isset($_REQUEST['doAction'])) {
    switch ($_REQUEST['doAction']) {
        case 'save':
            $vars['msg_error'] = null;
            
            
            
            $vm->deleteAutoScalling();
            foreach ($_POST['auto_scaling_configurations'] as $key => $value) {
                foreach ($value as $k => $val) {
                    if ($val['limit_trigger'] && $val['for_minutes'] && $val['adjust_units']) {
                        if (($val['limit_trigger'] > $product->getConfig($key.'_'.$k.'_limit_trigger')) && $product->getConfig($key.'_'.$k.'_limit_trigger') != "") {
                            $vars['msg_error'] .= $key.':'.$k.':'.$vars['lang']['is_usage_above'].' - '.$vars['lang']['value_too_high'].'<br />';
                            continue;
                        }

                        if (($val['adjust_units'] > $product->getConfig($key.'_'.$k.'_adjust_units')) && $product->getConfig($key.'_'.$k.'_adjust_units') != "") {
                            $vars['msg_error'] .= $key.':'.$k.':'.$vars['lang']['add'].'  - '.$vars['lang']['value_too_high'].'<br />';
                            continue;
                        }

                        if (($val['up_to'] > $product->getConfig($key.'_'.$k.'_up_to')) && $product->getConfig($key.'_'.$k.'_up_to') != "") {
                            $vars['msg_error'] .= $key.':'.$k.':'.$vars['lang']['24hr'].'  - '.$vars['lang']['value_too_high'].'<br />';
                            continue;
                        }

//                        $data = array(
//                            'auto_scaling_configuration' => array (
//                                        'limit_trigger'     => $val['limit_trigger'],
//                                        'for_minutes'       => $val['for_minutes'],//$product->getConfig('up_ram_time'),
//                                        'up_to'             => $val['up_to'],//$product->getConfig('up_ram_limit'),
//                                        'resource'          => $k,
//                                        'scale_type'        => $key ,
//                                        'adjust_units'      => $val['adjust_units'],//$product->getConfig('up_ram_add'),
//                                        'enabled'           => 1,
//                                        'allow_cold_resize' => 1
//                             )
//                        );

                        $data['auto_scaling_configurations'][$key][$k]['enabled']       = 1;
                        $data['auto_scaling_configurations'][$key][$k]['for_minutes']   = $val['for_minutes'];
                        $data['auto_scaling_configurations'][$key][$k]['limit_trigger'] = $val['limit_trigger'];
                        $data['auto_scaling_configurations'][$key][$k]['adjust_units']  = $val['adjust_units'];
                        if ($val['up_to']) {
                            $data['auto_scaling_configurations'][$key][$k]['up_to'] = $val['up_to'];
                        }
                        $vm->setAutoScalling($data);
                        if (!$vm->isSuccess()) $vars['msg_error'] .= $vm->error();
                    }
                }
            }
            if ($vars['msg_error'] == null) $vars['msg_success'] = $vars['lang']['applied'];

            break;
    }
}


$chart         = $vm->getUsageChart();
if ($vm->isSuccess()) $vars['chart'] = $chart;

$rules = $vm->getAutoScalling();
if ($vm->isSuccess()) {
    $group_rules = array();
    foreach ($rules as $key => $value) {
        $group_rules[$value['auto_scaling_configuration']['resource']][$value['auto_scaling_configuration']['scale_type']] = $value['auto_scaling_configuration'];
    }
    $vars['rules'] = $group_rules;
} else $vars['msg_error'] .= $vm->error();
