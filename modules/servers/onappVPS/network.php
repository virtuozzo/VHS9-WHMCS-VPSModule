<?php

/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */

if ($vars['disallow_action']['network'] == 1)
{
    ob_clean();
    header("Location: clientarea.php?action=productdetails&id=" . $params['serviceid']);
    die();
}

$interface            = new NewOnApp_NetworkInterface($params['customfields']['vmid']);
$interface->setconnection($params, true);

$_REQUEST['doAction'] = isset($_REQUEST['doAction']) ? $_REQUEST['doAction'] : '';
switch ($_REQUEST['doAction'])
{
    case 'add_interface':
       
        if (isset($_POST['network_interface']))
        {
          
            $data                = array(
                'network_interface' => array(
                    'label'           => $_POST['network_interface']['label'],
                    'rate_limit'      => (int) $_POST['network_interface']['rate_limit'],
                    'network_join_id' => $_POST['network_interface']['network'],
                    'primary'         => $_POST['network_interface']['primary'],
                )
            );
            $interface->addNetwork($data);
            if ($interface->isSuccess()){
                $vars['msg_success'] = $vars['lang']['interface_added'];
            }
            else{
                $vars['msg_error']   = $interface->error();
            }
      
        }
      
        $vars['form'] = 1;
        $network      = new NewOnApp_Network(null);
        $network->setconnection($params, true);

        $networks = $network->getList();
  
       
        $details = $vm->getDetails();

        $hypervisor_id = $details['virtual_machine']['hypervisor_id'];
        $hypervisor    = new NewOnApp_Hypervisor($hypervisor_id);

        $hypervisor->setconnection($params);
        $res     = $hypervisor->details();
        $zoneId  = $res['hypervisor']['hypervisor_group_id'];
        $hypZone = new NewOnApp_HypervisorZone($zoneId);
        $hypZone->setconnection($params);
        $nJoins  = $hypervisor->networkJoins();
        if (empty($nJoins))
        {
            $res    = $hypervisor->details();
            $zone   = new NewOnApp_HypervisorZone($res['hypervisor']['hypervisor_group_id']);
            $zone->setconnection($params);
            $nJoins = $zone->networkJoins();
        }
        $networkJoins = array(); //key is network_id value is network_join_id
        foreach ($nJoins as $networkJoin)
        {
             
            $networkJoins[$networkJoin['network_join']['network_id']] = $networkJoin['network_join']['id'];
        }

        $network_list = $interface->getList();
        $usedNetwork  = array();
        foreach ($network_list as $n)
        {
            $usedNetwork[] = $n['network_interface']['network_join_id'];
        }

  
        if ($network->isSuccess())
        {
            foreach ($networks as $key => $value)
            {
          
                if (!$value['network']['network_group_id'])
                    continue;
                if (!isset($networkJoins[$value['network']['id']]))
                    continue;
                if (in_array($value['network']['id'], $usedNetwork))
                    continue;
                
           
                $value['network']['network_join_id'] = $networkJoins[$value['network']['id']];
                
                
                $vars['networks_select'][$key]              = $value;
            }
         
   
        }
        else
        {
            $vars['msg_error'] = $network->error();
     }
        break;
    case 'edit':
        
        
        if (isset($_POST['network_interface']))
        {
            $data = array(
                'network_interface' => array(
                    'label'      => $_POST['network_interface']['label'],
                    'rate_limit' => $_POST['network_interface']['rate_limit'],
                    'primary'    => isset($_POST['network_interface']['primary']) ? 'true' : 'false',
                )
            );
            
          

            $interface->save($_GET['interface'], $data);

            if ($interface->isSuccess()){
                $vars['msg_success'] = $vars['lang']['interface_saved'];
            }
            else{
               
                $vars['msg_error']   = $interface->error();
            }
   
        }
        else
        {
            $vars['form'] = 1;
            $vars['edit'] = 1;

            $details = $interface->getDetails($_GET['interface']);
      
            if ($interface->isSuccess())
            {
                $vars['interface']                  = $details['network_interface'];
                $network                            = new NewOnApp_Network($vars['interface']['network_join_id']);
                $network->setconnection($params);
                $details                            = $network->getDetails();
                if ($network->isSuccess())
                    $vars['interface']['network_label'] = $details['network']['label'];
            }
            else
            {    
                $vars['msg_error'] = $interface->error();
            }
              
        }
          break;
    

    case 'removeInterface':
      
        
        $interface->delete($_POST['interface']);
        if ($interface->isSuccess())
        {
            die('success');
        }
        else
            die($interface->error());
        break;
    case 'showgraph':

        $interface->setconnection($params, false);
        $vars['graph'] = 1;
        $chart         = $interface->getUsageChart($_GET['network_id']);
     
        if ($interface->isSuccess())
        {
            $vars['chart'] = $chart;
        }
        else
            $vars['msg_error'] = ($interface->error() != "" ? $interface->error() : $chart );
        break;
    }
    
  
    
        $vm_details        = $vm->getDetails();
        if ($vm->isSuccess())
        {
            $hypervisor_id      = $vm_details['virtual_machine']['hypervisor_id'];
            $hypervisor         = new NewOnApp_Hypervisor($hypervisor_id);
            $hypervisor->setconnection($params);
            $hypervisor_details = $hypervisor->details();
            if ($hypervisor->isSuccess())
            {
                $hypervisor_zone = $hypervisor_details['hypervisor']['hypervisor_group_id'];
                $hp_zone         = new NewOnApp_HypervisorZone($hypervisor_zone);
                $hp_zone->setconnection($params);
                $zone_details    = $hp_zone->getZone();
                $network_joins   = $hp_zone->networkJoins();
            }
        }
        
      
        $network      = new NewOnApp_Network();
        $network->setconnection($params, false);
     
        $network_list = $interface->getList();

        if ($interface->isSuccess())
        {
            $vars['networks'] = $network_list;
            foreach ($network_list as $key => $value)
            {

                foreach ($network_joins as $n)
                {
                    if ($n['network_join']['id'] == $value['network_interface']['network_join_id'])
                    {
                        $network->setId($n['network_join']['network_id']);
                        $details                                     = $network->getDetails();
                        if ($network->isSuccess())
                            $value['network_interface']['network_label'] = $details['network']['label'] . ($zone_details['hypervisor_group']['label'] != "" ? ' (' . $zone_details['hypervisor_group']['label'] . ')' : '');
                        $vars['networks'][$key]                      = $value;
                    }
                }

                if (isset($value['network_interface']) && empty($value['network_interface']['network_label']))
                {
                    $network->setId($value['network_interface']['network_join_id']);
                    $det                                         = $network->getDetails();
                    $value['network_interface']['network_label'] = $det['network']['label'] . " ({$hypervisor_details['hypervisor']['label']}) ";
                    $vars['networks'][$key]                      = $value;
                    continue;
                }
            }
        }
        
        else
        {

            $vars['msg_error']    = $interface->error();
        $details              = $vm->getDetails();
        $hypervisor_id        = $details['virtual_machine']['hypervisor_id'];
        $hypervisor           = new NewOnApp_Hypervisor($hypervisor_id);
        $hypervisor->setconnection($params);
        $res                  = $hypervisor->details();
        $zone                 = new NewOnApp_HypervisorZone($res['hypervisor']['hypervisor_group_id']);
        $zone->setconnection($params);
        $zoneDetail           = $zone->getZone();
        $isFederation         = $zoneDetail['hypervisor_group']['federation_enabled'] || !empty($zoneDetail['hypervisor_group']['federation_id']);
        $vars['isFederation'] = $isFederation;
        }

