<?php

use OnAppVps\Database\FederatedConfiguration;
use OnAppVps\Database\LocationGroups;
use OnAppVps\Database\FederatedTemplates;
use OnAppVps\Service\ConfigurableOptionsService;
use onappWrapper\PdoWrapper;

/**
 * @author Grzegorz Draganik <grzegorz@modulesgarden.com>
 * @author Maciej Husak <maciej@modulesgarden.com>
 */
class onappVPS_Product extends MG_WHMCS_Onapp_Product
{
    public $id;
    protected $_tableName     = 'onappVPS_prodConfig';
    public static $_tableAuth = 'onappVPS_auth';
    public $defaultConfig     = [];
    protected $configQuery    = 'INSERT INTO tblproductconfigoptions(gid,optionname,optiontype,qtyminimum,qtymaximum,`order`,hidden) VALUES(?,?,4,?,?,0,0)';

    public function __construct($id, $serviceid = null)
    {
        if ($serviceid)
        {
            $q        = PdoWrapper::query('SELECT packageid FROM tblhosting WHERE id = ' . (int) $serviceid);
            $row      = PdoWrapper::fetchAssoc($q);
            $this->id = $row['packageid'];
        }
        else
        {
            $this->id = $id;
        }

        $this->defaultConfig = ($this->getFirstPartConfig() + $this->generateFederatedGroup() + $this->getSecondPartConfig());
    }

    public function setupDefaultConfigurableOptions()
    {
        $group                     = $this->getConfigurableGroup();
        $configurableOptionService = new ConfigurableOptionsService($this, $group);
        $configurableOptionService->addConfigurableOptions();

        return true;
    }

    public function setupDefaultCustomFields()
    {
        $q = PdoWrapper::query('
			SELECT id, relid, fieldname
			FROM tblcustomfields
			WHERE
				relid = ' . (int) $this->id . '
				AND type = "product"
				AND fieldname IN("vmid|Virtual Machine ID","userid|Virtual Machine User ID","hostname|Hostname","label|Label")
		');

        $fieldnames = [];
        while ($row        = PdoWrapper::fetchAssoc($q))
        {
            $fieldnames[] = $row['fieldname'];
        }

        $query_raw = 'INSERT INTO tblcustomfields(type,relid,fieldname,fieldtype,description,fieldoptions,regexpr,adminonly,required,showorder,showinvoice,sortorder)
			VALUES("product", ' . $this->id . ', ?, "text", "", "", "", "on", "", "", "", 0)';

        if (!in_array('vmid|Virtual Machine ID', $fieldnames))
        {
            PdoWrapper::query($query_raw, ['vmid|Virtual Machine ID']);
        }
        if (!in_array('userid|Virtual Machine User ID', $fieldnames))
        {
            PdoWrapper::query($query_raw, ['userid|Virtual Machine User ID']);
        }

        $query_raw = 'INSERT INTO tblcustomfields(type,relid,fieldname,fieldtype,description,fieldoptions,regexpr,adminonly,required,showorder,showinvoice,sortorder)
			VALUES("product", ' . $this->id . ', ?, "text", "", "", "", "", "on", "on", "", 0)';
        if (!in_array('hostname|Hostname', $fieldnames))
        {
            PdoWrapper::query($query_raw, ['hostname|Hostname']);
        }
        if (!in_array('label|Label', $fieldnames))
        {
            PdoWrapper::query($query_raw, ['label|Label']);
        }

        if (!in_array('domain|Domain', $fieldnames))
        {
            $query_raw = 'INSERT INTO tblcustomfields(type,relid,fieldname,fieldtype,description,fieldoptions,regexpr,adminonly,required,showorder,showinvoice,sortorder)
			VALUES("product", ' . $this->id . ', ?, "text", "", "", "", "", "", "on", "", 0)';
            PdoWrapper::query($query_raw, ['domain|Domain']);
        }

        return true;
    }

    public function getConfigurableGroup()
    {
        $links = new \OnAppVps\Database\ProductConfigLinks();
        foreach ($links->where('pid', $this->id)->get() as $link)
        {
            $groupObj = new \OnAppVps\Database\ProductConfigGroups();
            $group    = $groupObj->where('id', $link->gid)->first();
            if ($group->name == 'Configurable options for onAppVPS')
            {
                return $group;
            }
        }

        return null;
    }

    public function hasConfigurableOptions()
    {
        $q = PdoWrapper::query('SELECT * FROM tblproductconfiglinks WHERE pid = ?', [$this->id]);

        return (bool) PdoWrapper::numRows($q);
    }

    public function customFieldExists($name)
    {
        $q = PdoWrapper::query('
			SELECT id, relid, fieldname
			FROM tblcustomfields
			WHERE relid = ? AND type = "product" AND fieldname = ?
		', [$this->id, $name]);

        return (bool) PdoWrapper::numRows($q);
    }

    public function getValueWithUnit($val, $new = 'MB', $unit = 'MB')
    {
        if ($new == $unit)
        {
            return $val;
        }
        else
        {
            if ($new == 'GB' && $unit == 'MB')
            {
                return $val * 1024;
            }
            else
            {
                if ($new == 'MB' && $unit == 'GB')
                {
                    return ceil($val / 1024);
                }
            }
        }
    }

    public function synchronize($replace = false)
    {

        $server    = $this->getParams();
        $template  = new NewOnApp_Template(null);
        $template->setconnection($server);
        //$billing_id = $this->getConfig('user_billing_plan');
        $bucket_id = $this->getConfig('user_billing_plan');
//        if (empty($billing_id)) {
//            return 'Please specify user billing plan.';
//        }

        if (empty($bucket_id))
        {
            return 'Please specify user billing plan.';
        }
    
     
//        $billing = new NewOnApp_Billing($billing_id);
//        $billing->setconnection($server);
//        $plan    = $billing->getDetails();
 
        $bucket = new OnApp_Buckets($bucket_id);
        $bucket->setconnection($server);
        $plan   = $bucket->getDetails();

//        if ($billing->isSuccess())
//        {
//            $curr = PdoWrapper::fetchAssoc(PdoWrapper::query("SELECT `id` FROM `tblcurrencies` WHERE `code`=?", [$plan['user_plan']['currency_code']]));
//            if (empty($curr['id']))
//            {
//                return 'Currencies: <b>' . $plan['billing_plan']['currency_code'] . '</b> is not specifed in WHMCS';
//            }
//        }
//        else
//        {
//            return $billing->error();
//        }
        
        
          if ($bucket->isSuccess())
        {
         $curr = PdoWrapper::fetchAssoc(PdoWrapper::query("SELECT `id` FROM `tblcurrencies` WHERE `code`=?", [$plan['bucket']['currency_code']]));
            if (empty($curr['id']))
            {
              return 'Currencies: <b>' . $plan['bucket']['currency_code'] . '</b> is not specifed in WHMCS';
            }
        }
        else
        {
            return $bucket->error();
        }
        
        
        // Default Template
        $templates = $template->getSystemTemplates();
        $new       = [];
        if ($template->isSuccess())
        {
            foreach ($templates as $key => $value)
            {
                $new[$value['image_template']['id']] = ['label' => $value['image_template']['label'], 'pricing' => 0];
            }
        }
        else
        {
            return $template->error();
        }
        
        if($this->getConfig('showvCentertemplates')) {
            $vcentertemplates = $template->getSystemTemplatesVcenter();
            foreach ($vcentertemplates as $value)
            {
                $new[$value['vcenter_image_template']['id']] = ['label' => $value['vcenter_image_template']['label'], 'pricing' => 0];   
            }
        }

        $stores = $template->getTemplatePricing();
        
        if ($template->isSuccess())
        {
            foreach ($stores as $key => $value)
            {
                foreach ($value['relations'] as $temp => $val)
                {
                    if (array_key_exists($val['template_id'], $new))
                    {
                        $new[$val['template_id']]['pricing'] = $val['price'];
                    }
                }
            }
        }
        else
        {
            $template->error();
        }
        $query_old = PdoWrapper::query("SELECT ps.id,ps.optionname FROM  tblproductconfiggroups pg LEFT JOIN tblproductconfiglinks pl ON (pg.id = pl.gid) LEFT JOIN tblproductconfigoptions po ON (po.gid=pg.id) LEFT JOIN tblproductconfigoptionssub ps ON (ps.configid = po.id) WHERE po.optionname LIKE 'template_id|%' AND pl.pid =?", [$this->id]);
        $records   = [];
        
        $alldata = [];
        while ($r         = PdoWrapper::fetchAssoc($query_old))
        {
            $temp = explode('|', $r['optionname']);
            $alldata['exists'][$temp[0]] = $temp[1];
            
            
            if (array_key_exists($temp[0], $new))
            {
                $records['exists'][$temp[0]] = $temp[1];
            }
            else
            {
                if ($replace)
                {
                    PdoWrapper::query("DELETE FROM tblproductconfigoptionssub WHERE `id`=?", [$r['id']]);
                    PdoWrapper::query("DELETE FROM tblhostingconfigoptions WHERE `optionid`=?", [$r['id']]);
                }
                $records['remove'][$temp[0]] = $temp[1];
            }
        }

        foreach ($new as $key => $val)
        {
            if (!key_exists($key, $records['exists']))
            {
                $records['new'][$key] = $val['label'];
                if ($replace)
                {
                    $query_old = PdoWrapper::query("SELECT po.id FROM  tblproductconfiggroups pg LEFT JOIN tblproductconfiglinks pl ON (pg.id = pl.gid) LEFT JOIN tblproductconfigoptions po ON (po.gid=pg.id) LEFT JOIN tblproductconfigoptionssub ps ON (ps.configid = po.id) WHERE po.optionname LIKE 'template_id|%' AND pl.pid =? GROUP BY po.id", [$this->id]);
                    while ($r         = PdoWrapper::fetchAssoc($query_old))
                    {
                        PdoWrapper::query("INSERT INTO tblproductconfigoptionssub (`configid`,`optionname`) VALUES(?,?)", [$r['id'], $key . '|' . $val['label']]);
                    }
                }
            }
        }

        if ($replace)
        {
            die("success");
        }

        $left  = null;
        $right = null;
        foreach ($records as $key => $value)
        {
            foreach ($value as $id => $label)
            {
                if ($key == 'new')
                {
                    $left .= '<div style="color:green;">' . $label . '</div>';
                }
                else
                {
                    if ($key == 'remove')
                    {
                        $right .= '<div style="color:red;">' . $label . '</div>';
                    }
                }
                //else
                // $string .='<div style="width:140px;float:left;display:block;">'.$label.'</div>';
            }
        }
        $content = '<table width="100%"><tr><td width="50%">New</td><td>To remove</td></tr>
                    <tr><td>' . $left . '</td><td>' . $right . '</td></tr></table>';

        return $content;
    }

    public function installDb()
    {

        $query = PdoWrapper::query("SHOW TABLES LIKE '{$this->_tableName}'");
        if (!PdoWrapper::numRows($query))
        {
            $result = PdoWrapper::query('CREATE TABLE IF NOT EXISTS `' . $this->_tableName . '` (
                                    `setting` varchar(100) NOT NULL,
                                    `product_id` int(10) unsigned NOT NULL,
                                    `value` text NOT NULL,
                                    PRIMARY KEY (`setting`,`product_id`)
                                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
        }
        $result = PdoWrapper::query('ALTER TABLE  `' . $this->_tableName . '` CHANGE  `value`  `value` TEXT NOT NULL');

        return true;
    }

    /**
     * Remove Group Config
     *
     * @param string Eg. 'gr7','gr8'
     * @return boolean
     */
    public function removeGroupConfig()
    {
        $groups = func_get_args();
        if (empty($groups))
        {
            return false;
        }
        $remove = false;
        foreach ($this->defaultConfig as $key => $value)
        {
            if ($remove && (is_array($value) || strpos($key, 'subgroup') !== false))
            {
                unset($this->defaultConfig[$key]);
                continue;
            }
            else
            {
                if ($remove && (!is_array($value) || strpos($value, 'subgroup') === false))
                {
                    $k      = array_search($remove, $groups);
                    unset($groups[$k]);
                    $remove = false;
                }
            }
            //start
            if (!$remove && !is_array($value) && in_array($key, $groups))
            {
                $remove = $key;
                unset($this->defaultConfig[$key]);
                continue;
            }
        }

        return true;
    }

    public function generateFederatedGroup()
    {
        $this->loadConfig();
        $config = ['gr2' => 'OS Templates for Federated'];
        if (!class_exists('OnAppVps\Database\FederatedTemplates'))
        {
            return [];
        }
        try
        {
            $templates           = new FederatedTemplates();
            $locations           = new LocationGroups();
            $templatesRepository = new \OnAppVps\Reposiotry\FederatedTemplateRepository();
            $i                   = 1;

            foreach ($locations->all() as $location)
            {
                $groupName = $location->country . ' - ' . $location->full_city;

                $temp = $templatesRepository->findByLocation($location);
                if (count($temp) <= 0)
                {
                    continue;
                }
                $config['subgroup' . $i] = sprintf('<span class="c_uc-subject">%s</span>', $groupName);
                foreach ($temp as $c)
                {
                    $key = 'federated_' . $c->onapp_id; // the id is not unique
                    if ($this->getConfig($key) === null)
                    {
                        $this->saveConfig($key, 1);
                        $this->_config[$key] = 1;
                    }
                    $config[$key] = [
                        'title'       => $c->label,
                        'description' => '',
                        'type'        => 'checkbox_with_hidden',
                        'additional'  => [
                            'data-city' => $location->country . ' - ' . $location->full_city
                        ]
                    ];
                }
                $i++;
            }
            
            
            $groupName = 'vCenter';

            $temp = $templatesRepository->findByLocationVCenter();
        
            $config['subgroup' . $i] = sprintf('<span class="c_uc-subject">%s</span>', $groupName);
            foreach ($temp as $c)
            {
                $key = 'federated_' . $c->onapp_id; // the id is not unique
                if ($this->getConfig($key) === null)
                {
                    $this->saveConfig($key, 1);
                    $this->_config[$key] = 1;
                }
                $config[$key] = [
                    'title'       => $c->label,
                    'description' => '',
                    'type'        => 'checkbox_with_hidden',
                    'additional'  => [
                        'data-city' => $groupName
                    ]
                ];
            }
            
        }
        catch (\Exception $e)
        {
            return [];
        }

        return $config;
    }

    public function getFirstPartConfig()
    {
        return [
            'vmware'            => [
                'title'       => 'VMware',
                'type'        => 'select',
                'options'     => ['No', 'Yes'],
                'description' => '<img src="../modules/servers/onappVPS/img/info.png"  title="The only difference is that publishing rules are used instead of firewall rules and backups are replaced by snapshots. Also, as the VMware cluster is displayed as a pool of resources rather than per hypervisor,  certain VS operations are unavailable in OnApp with VMware:

                    Reboot in recovery
                    Segregate 
                    VIP status
                    Autoscaling
                    Migrate VS. " />',
            ],
            'userAccountPerVPS' => [
                'title'       => 'User Account Per VPS',
                'description' => '',
                'type'        => 'checkbox',
            ],
            'showvCentertemplates' => [
                'title'       => 'Show vCenter templates',
                'description' => '',
                'type'        => 'checkbox',
            ],
            'gr1'               => 'Resources',
            'memory'            => [
                'title'       => 'Default Memory Limit',
                'type'        => 'text',
                'default'     => '256',
                'description' => '<select name="customconfigoption[memory_unit]"><option value="MB">MB</option><option value="GB">GB</option></select>',
            ],
            'cpus'              => [
                'title'   => 'Default CPU Limit',
                'type'    => 'text',
                'default' => '1',
            ],
            'cpu_shares'        => [
                'title'       => 'Default CPU Shares [%]',
                'type'        => 'text',
                'default'     => '20',
                'description' => '<img src="../modules/servers/onappVPS/img/info.png"  title="For KVM hypervisor the CPU priority value is always 100. For XEN, set a custom value. The default value for XEN is 1" />',
            ],
            'primary_disk_size' => [
                'title'       => 'Default Disk Size',
                'type'        => 'text',
                'default'     => '20',
                'description' => '<select name="customconfigoption[primary_unit]"><option value="GB">GB</option><option value="MB">MB</option></select>',
            ],
            'swap_disk_size'    => [
                'title'       => 'SWAP Space',
                'type'        => 'text',
                'default'     => '10',
                'description' => '<select name="customconfigoption[swap_unit]"><option value="GB">GB</option><option value="MB">MB</option></select> <img src="../modules/servers/onappVPS/img/info.png"  title="There is no swap disk for Windows-based VMs" />',
            ],
        ];
    }

    public function getSecondPartConfig()
    {
        $server    = $this->getParams();
        $template  = new NewOnApp_Template(null);
        $template->setconnection($server);
        
        
        $resources = [];
        if($this->getConfig('showvCentertemplates'))
        {
            $resources = $template->getResourcePoolsVcenter();
        }
        
        
        $resourcesoptions = [];
        
        foreach($resources as $resource)
        {
            $resourcesoptions[$resource['vcenter_resource_pool']['id']] = $resource['vcenter_resource_pool']['label']; 
        }
        
        return [
            'gr3'                            => 'OS Templates & Storage/Backups',
            'template_group'                 => [
                'title'          => 'Template Group',
                'type'           => 'select',
                'options'        => ['all' => 'All Templates'], // loaded later
                'description'    => '<img src="../modules/servers/onappVPS/img/info.png"  title="If no hypervisor is specified, the VM will be built on the hypervisor with the least available RAM (but sufficient RAM for the VM)" />',
                'useOptionsKeys' => true,
            ],
            'template_id'                    => [
                'title'          => 'OS Template',
                'type'           => 'select',
                'options'        => ['-- not specified --' => '-- not specified --'],
                'description'    => '<img src="../modules/servers/onappVPS/img/info.png"  title="The templates available on your system. A template is a pre-configured OS image that you can build a virtual server" />',
                'useOptionsKeys' => true,
            ],
            'data_store_group_primary_id'    => [
                'title'          => 'Data Store Zone (non Federated)',
                'type'           => 'select',
                'options'        => ['-- not specified --' => '-- not specified --'],
                'description'    => '<img src="../modules/servers/onappVPS/img/info.png"  title="Select label of the data store zone to which this primary disk is allocated" />',
                'useOptionsKeys' => true,
            ],
            'data_store_group_swap_id'       => [
                'title'          => 'SWAP: Data Store Zone (non Federated)',
                'type'           => 'select',
                'options'        => ['-- not specified --' => '-- not specified --'],
                'description'    => '<img src="../modules/servers/onappVPS/img/info.png"  title="Select label of the data store zone to which this swap disk is allocated" />',
                'useOptionsKeys' => true,
            ],
            'primary_disk_min_iops'          => [
                'title'       => 'Primary Disk Min Iops',
                'type'        => 'text',
                'default'     => '',
                'description' => '<img src="../modules/servers/onappVPS/img/info.png"  title="Minimum number of  IO operations per second for primary disk" />',
            ],
            'swap_disk_min_iops'             => [
                'title'       => 'Swap Disk Min Iops',
                'type'        => 'text',
                'default'     => '',
                'description' => '<img src="../modules/servers/onappVPS/img/info.png"  title="Minimum number of  IO operations per second for swap disk" />',
            ],
            'additional_disk_size'           => [
                'title'       => 'MAX Disk Size',
                'type'        => 'text',
                'default'     => '',
                'description' => '[GB] <img src="../modules/servers/onappVPS/img/info.png"  title="Max disk size for all disk" />',
            ],
            'gr4'                            => 'Networks',
            'hypervisor_zone'                => [
                'title'          => 'Hypervisor Zone (non Federated)',
                'type'           => 'select',
                'options'        => ['-- not specified --' => '-- not specified --'], // loaded later
                'description'    => '<img src="../modules/servers/onappVPS/img/info.png"  title="If no hypervisor is specified, the VM will be built on the hypervisor with the least available RAM (but sufficient RAM for the VM)" />',
                'useOptionsKeys' => true,
            ],
            'hypervisor_id'                  => [
                'title'          => 'Hypervisor (non Federated)',
                'type'           => 'select',
                'options'        => ['0' => 'Auto'], // loaded later
                'description'    => '<img src="../modules/servers/onappVPS/img/info.png"  title="If no hypervisor is specified, the VM will be built on the hypervisor with the least available RAM (but sufficient RAM for the VM)" />',
                'useOptionsKeys' => true,
            ],
            'ip_addresses'                   => [
                'title'   => 'IP Addresses count',
                'type'    => 'text',
                'default' => '1',
            ],
            'rate_limit'                     => [
                'title'       => 'Max Port Speed',
                'type'        => 'text',
                'description' => '<img src="../modules/servers/onappVPS/img/info.png"  title="[Rate] If none set, the system sets port speed to unlimited" />',
                'default'     => '10',
            ],
            'primary_network_id'             => [
                'title'          => 'Network Zone (non Federated)',
                'type'           => 'select',
                'options'        => ['-- not specified --' => '-- not specified --'], // loaded later
                'useOptionsKeys' => true,
            ],
            'gr5'                            => 'Additionals',
            'label'                          => [
                'title'   => 'User-friendly VM description',
                'type'    => 'text',
                'default' => 'Virtual Machine created with onappVPS for WHMCS',
            ],
            'type_of_format'                 => [
                'title'   => 'Type Of Filesystem',
                'type'    => 'text',
                'default' => 'ext3',
            ],
            'licensing_server_id'            => [
                'title'       => 'Licensing Server Id',
                'description' => '<img src="../modules/servers/onappVPS/img/info.png"  title="The ID of a licensing server/template group – this parameter is for Windows XP/7 virtual machines only" />',
                'type'        => 'text',
            ],
            'licensing_type'                 => [
                'title'       => 'Licensing Type',
                'description' => '<img src="../modules/servers/onappVPS/img/info.png"  title="The type of a license: mak, kms or user own license. This parameter is for Windows XP/7 virtual machines only" />',
                'type'        => 'text',
                'default'     => 'mak',
            ],
            'licensing_key'                  => [
                'title'       => 'Licensing Key',
                'description' => '<img src="../modules/servers/onappVPS/img/info.png"  title="The key of a license. This parameter is for Windows XP/7 virtual machines only" />',
                'type'        => 'text',
            ],
            'required_automatic_backup'      => [
                'title'          => 'Automatic Backup',
                'description'    => '<img src="../modules/servers/onappVPS/img/info.png"  title="Select \'Yes\' if you need automatic backups" />',
                'type'           => 'select',
                'options'        => [0 => 'No', 1 => 'Yes'],
                'useOptionsKeys' => true,
            ],
            'required_virtual_machine_build' => [
                'title'          => 'Virtual Machine Build',
                'description'    => '<img src="../modules/servers/onappVPS/img/info.png"  title="Select \'Yes\' if you want build VM automatically" />',
                'type'           => 'select',
                'options'        => ['Yes', 'No'],
                'useOptionsKeys' => true,
            ],
            'vcenter_resource_pool_id' => [
                'title'   => 'vCenter resource pool',
                'type'    => 'select',
                'options' => $resourcesoptions,
            ],
            'gr6'                            => 'User Configuration',
            'user_role'                      => [
                'title'          => 'User Role',
                'description'    => '<img src="../modules/servers/onappVPS/img/info.png"  title="Assigns a role to a user" />',
                'type'           => 'multiselect',
                'options'        => ['-- not specified --' => '-- not specified --'], // loaded later
                'useOptionsKeys' => true,
            ],
            'user_billing_plan'              => [
                'title'          => 'User Bucket Plan',
                'description'    => '<img src="../modules/servers/onappVPS/img/info.png"  title="Set by default, if not selected" />',
                'type'           => 'select',
                'options'        => ['-- not specified --' => '-- not specified --'], // loaded later
                'useOptionsKeys' => true,
            ],
            'user_group'                     => [
                'title'          => 'User Group',
                'description'    => '<img src="../modules/servers/onappVPS/img/info.png"  title="ID of the group, to which the user is attached" />',
                'type'           => 'select',
                'options'        => ['-- not specified --' => '-- not specified --'], // loaded later
                'useOptionsKeys' => true,
            ],
            'user_prefix'                    => [
                'title' => 'Username Prefix',
                'type'  => 'text',
            ],
            /* 'user_counter'                  => array(
              'title'                 => 'Username Counter',
              'type'                  => 'text',
              ), */
            'gr7'                            => 'UP  AutoScalling',
            'subgroup11'                     => 'RAM',
            'up_memory_for_minutes'          => [
                'title'          => 'Time',
                'description'    => '<img src="../modules/servers/onappVPS/img/info.png"  title="The time threshold before scaling will be triggered" />',
                'type'           => 'select',
                'options'        => [
                    5  => '5 minutes',
                    10 => '10 minutes',
                    15 => '15 minutes',
                    20 => '20 minutes',
                    25 => '25 minutes',
                    30 => '30 minutes',
                ],
                'useOptionsKeys' => true,
            ],
            'up_memory_limit_trigger'        => [
                'title'       => 'If usage is above',
                'description' => '[%] <img src="../modules/servers/onappVPS/img/info.png"  title="The amount of the resource usage (%). If this value is reached by the VM for the period specified by the for_minutes parameter, the system will add the amount of units set by the add_units parameters" />',
                'type'        => 'text',
            ],
            'up_memory_adjust_units'         => [
                'title'       => 'Add',
                'description' => '[MB] <img src="../modules/servers/onappVPS/img/info.png" title="The amount of resource units which the system should add if the rule is met" />',
                'type'        => 'text',
            ],
            'up_memory_up_to'                => [
                'title'       => '24hr limit',
                'description' => '[MB] <img src="../modules/servers/onappVPS/img/info.png" title="The amount of resource which cannot be exceeded within 24 hours period" />',
                'type'        => 'text',
            ],
            'subgroup22'                     => 'CPU Usage',
            'up_cpu_for_minutes'             => [
                'title'          => 'Time',
                'description'    => '<img src="../modules/servers/onappVPS/img/info.png"  title="The time threshold before scaling will be triggered" />',
                'type'           => 'select',
                'options'        => [
                    5  => '5 minutes',
                    10 => '10 minutes',
                    15 => '15 minutes',
                    20 => '20 minutes',
                    25 => '25 minutes',
                    30 => '30 minutes',
                ],
                'useOptionsKeys' => true,
            ],
            'up_cpu_limit_trigger'           => [
                'title'       => 'If usage is above',
                'description' => '[%] <img src="../modules/servers/onappVPS/img/info.png"  title="The amount of the resource usage (%). If this value is reached by the VM for the period specified by the for_minutes parameter, the system will add the amount of units set by the add_units parameters" />',
                'type'        => 'text',
            ],
            'up_cpu_adjust_units'            => [
                'title'       => 'Add',
                'description' => '[%] <img src="../modules/servers/onappVPS/img/info.png" title="The amount of resource units which the system should add if the rule is met" />',
                'type'        => 'text',
            ],
            'up_cpu_up_to'                   => [
                'title'       => '24hr limit',
                'description' => '[%] <img src="../modules/servers/onappVPS/img/info.png" title="The amount of resource which cannot be exceeded within 24 hours period" />',
                'type'        => 'text',
            ],
            'subgroup33'                     => 'Disk Usage',
            'up_disk_for_minutes'            => [
                'title'          => 'Time',
                'description'    => '<img src="../modules/servers/onappVPS/img/info.png"  title="The time threshold before scaling will be triggered" />',
                'type'           => 'select',
                'options'        => [
                    5  => '5 minutes',
                    10 => '10 minutes',
                    15 => '15 minutes',
                    20 => '20 minutes',
                    25 => '25 minutes',
                    30 => '30 minutes',
                ],
                'useOptionsKeys' => true,
            ],
            'up_disk_limit_trigger'          => [
                'title'       => 'If usage is above',
                'description' => '[%] <img src="../modules/servers/onappVPS/img/info.png"  title="The amount of the resource usage (%). If this value is reached by the VM for the period specified by the for_minutes parameter, the system will add the amount of units set by the add_units parameters" />',
                'type'        => 'text',
            ],
            'up_disk_adjust_units'           => [
                'title'       => 'Add',
                'description' => '[GB] <img src="../modules/servers/onappVPS/img/info.png" title="The amount of resource units which the system should add if the rule is met" />',
                'type'        => 'text',
            ],
            'up_disk_up_to'                  => [
                'title'       => '24hr limit',
                'description' => '[GB] <img src="../modules/servers/onappVPS/img/info.png" title="The amount of resource which cannot be exceeded within 24 hours period" />',
                'type'        => 'text',
            ],
            'gr8'                            => 'Down  AutoScalling',
            'subgroup44'                     => 'RAM',
            'down_memory_for_minutes'        => [
                'title'          => 'Time',
                'description'    => '<img src="../modules/servers/onappVPS/img/info.png"  title="The time threshold before scaling will be triggered" />',
                'type'           => 'select',
                'options'        => [
                    5  => '5 minutes',
                    10 => '10 minutes',
                    15 => '15 minutes',
                    20 => '20 minutes',
                    25 => '25 minutes',
                    30 => '30 minutes',
                ],
                'useOptionsKeys' => true,
            ],
            'down_memory_limit_trigger'      => [
                'title'       => 'If usage is below',
                'description' => '[%] <img src="../modules/servers/onappVPS/img/info.png"  title="The amount of the resource usage (%). If this value is reached by the VM for the period specified by the for_minutes parameter, the system will add the amount of units set by the add_units parameters" />',
                'type'        => 'text',
            ],
            'down_memory_adjust_units'       => [
                'title'       => 'Remove',
                'description' => '[MB] <img src="../modules/servers/onappVPS/img/info.png" title="The amount of resource units which the system should add if the rule is met" />',
                'type'        => 'text',
            ],
            /* 'down_memory_up_to'             => array(
              'title'                 => '24hr limit',
              'description'           => '[MB] <img src="../modules/servers/onappVPS/img/info.png" title="The amount of resource which cannot be exceeded within 24 hours period" />',
              'type'                  => 'text',
              ), */
            'subgroup55'                     => 'CPU Usage',
            'down_cpu_for_minutes'           => [
                'title'          => 'Time',
                'description'    => '<img src="../modules/servers/onappVPS/img/info.png"  title="The time threshold before scaling will be triggered" />',
                'type'           => 'select',
                'options'        => [
                    5  => '5 minutes',
                    10 => '10 minutes',
                    15 => '15 minutes',
                    20 => '20 minutes',
                    25 => '25 minutes',
                    30 => '30 minutes',
                ],
                'useOptionsKeys' => true,
            ],
            'down_cpu_limit_trigger'         => [
                'title'       => 'If usage is below',
                'description' => '[%] <img src="../modules/servers/onappVPS/img/info.png"  title="The amount of the resource usage (%). If this value is reached by the VM for the period specified by the for_minutes parameter, the system will add the amount of units set by the add_units parameters" />',
                'type'        => 'text',
            ],
            'down_cpu_adjust_units'          => [
                'title'       => 'Remove',
                'description' => '[%] <img src="../modules/servers/onappVPS/img/info.png" title="The amount of resource units which the system should add if the rule is met" />',
                'type'        => 'text',
            ],
            /* 'down_cpu_up_to'                => array(
              'title'                 => '24hr limit',
              'description'           => '[%] <img src="../modules/servers/onappVPS/img/info.png" title="The amount of resource which cannot be exceeded within 24 hours period" />',
              'type'                  => 'text',
              ), */
            'subgroup6'                      => 'Disk Usage',
            'down_disk_for_minutes'          => [
                'title'          => 'Time',
                'description'    => '<img src="../modules/servers/onappVPS/img/info.png"  title="The time threshold before scaling will be triggered" />',
                'type'           => 'select',
                'options'        => [
                    5  => '5 minutes',
                    10 => '10 minutes',
                    15 => '15 minutes',
                    20 => '20 minutes',
                    25 => '25 minutes',
                    30 => '30 minutes',
                ],
                'useOptionsKeys' => true,
            ],
            'down_disk_limit_trigger'        => [
                'title'       => 'If usage is below',
                'description' => '[%] <img src="../modules/servers/onappVPS/img/info.png"  title="The amount of the resource usage (%). If this value is reached by the VM for the period specified by the for_minutes parameter, the system will add the amount of units set by the add_units parameters" />',
                'type'        => 'text',
            ],
            'down_disk_adjust_units'         => [
                'title'       => 'Remove',
                'description' => '[GB] <img src="../modules/servers/onappVPS/img/info.png" title="The amount of resource units which the system should add if the rule is met" />',
                'type'        => 'text',
            ],
            /* 'down_disk_up_to'               => array(
              'title'                 => '24hr limit',
              'description'           => '[MB] <img src="../modules/servers/onappVPS/img/info.png" title="The amount of resource which cannot be exceeded within 24 hours period" />',
              'type'                  => 'text',
              ), */
            'gr9'                            => 'Disallow certain actions in clientarea',
            'manage_firewall'                => [
                'title'       => 'Firewall Management',
                'description' => '',
                'type'        => 'checkbox',
            ],
            'manage_ip'                      => [
                'title'       => 'IP Management',
                'description' => '',
                'type'        => 'checkbox',
            ],
            'manage_network'                 => [
                'title'       => 'Network Management',
                'description' => '',
                'type'        => 'checkbox',
            ],
            'manage_stats'                   => [
                'title'       => 'CPU Usage Graphs',
                'description' => '',
                'type'        => 'checkbox',
            ],
            'manage_disk'                    => [
                'title'       => 'Disk Management',
                'description' => '',
                'type'        => 'checkbox',
            ],
            'manage_backups'                 => [
                'title'       => 'Backups',
                'description' => '',
                'type'        => 'checkbox',
            ],
            'manage_autoscalling'            => [
                'title'       => 'Autoscalling',
                'description' => '',
                'type'        => 'checkbox',
            ],
            'show_cpu_share'                 => [
                'title'       => 'Display CPU Priority',
                'description' => '',
                'type'        => 'checkbox',
            ],
            'manage_autoscalling_up'         => [
                'title'       => 'Autoscalling UP',
                'description' => '',
                'type'        => 'checkbox',
            ],
            'show_user_details'              => [
                'title'       => 'Display User Details',
                'description' => '',
                'type'        => 'checkbox',
            ],
            'manage_autoscalling_down'       => [
                'title'       => 'Autoscalling Down',
                'description' => '',
                'type'        => 'checkbox',
            ],
            'show_api_info'                  => [
                'title'       => 'Display API Info',
                'description' => '',
                'type'        => 'checkbox',
            ],
            'accelerator'                    => [
                'title'       => 'Accelerator',
                'description' => '',
                'type'        => 'checkbox',
            ],
        ];
    }

    public function getFederatedConfigIds()
    {
        $this->loadConfig();
        $this->load();
        $group = Illuminate\Database\Capsule\Manager::table('tblproductconfiglinks')
                ->where('pid', $this->id)
                ->first();

        $optModel    = new \OnAppVps\Database\ProductConfigOptions();
        $optionGroup = $optModel->where('optionname', 'like', 'template_id|%')->where('gid', $group->gid)->first();

        $optsubModel = new \OnAppVps\Database\ProductConfigOptionsSub();
        $options     = $optsubModel->where('configid', $optionGroup->id)->get();

        $originalTemplates = [];
        foreach ($options as $option)
        {
            $originalTemplates[$option->optionname] = $option->id;
        }

        $templates = new \OnAppVps\Database\FederatedTemplates();
        foreach ($templates->getDefaults() as $template)
        {

            $key = 'federated_' . $template['onapp_id'];

            if (!$this->getConfig($key))
            {
                continue;
            }
            $key = $template['onapp_id'] . '|' . $template['label'];
            if (!isset($originalTemplates[$key]))
            {
                continue;
            }
            $config[$template['group']][] = [
                'location_id' => $template['location_id'],
                'id'          => $template['onapp_id'],
                'label'       => $template['label'],
                'whmcs_id'    => $originalTemplates[$key],
            ];
        }
        return $config;
    }
}
