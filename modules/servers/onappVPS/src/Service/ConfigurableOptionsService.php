<?php

namespace OnAppVps\Service;

use OnAppVps\Database\Currencies;
use OnAppVps\Database\Pricing;
use OnAppVps\Database\ProductConfigGroups;
use OnAppVps\Database\ProductConfigLinks;
use OnAppVps\Database\ProductConfigOptions;
use OnAppVps\Database\ProductConfigOptionsSub;

class ConfigurableOptionsService
{

    /** @var \MG_Product */
    protected $product;
    /** @var  ProductConfigGroups */
    protected $group;
    protected $savedOptions = [];
    protected $currencies   = [];
    protected $server;

    /**
     * ConfigurableOptionsService constructor.
     *
     * @param \onappVPS_Product $product
     */
    public function __construct(\onappVPS_Product $product, $group = null)
    {
        $this->group = $group;
        $this->product    = $product;
        $currenciesModel  = new Currencies();
        $this->currencies = $currenciesModel->all();
        $this->server     = $this->product->getParams();
    }

    public function addConfigurableOptions()
    {
        if($this->group == null) {
            $this->saveConfigGroup();
        }
        


        $lib = new \NewOnApp_Location(null);
        $lib->setconnection($this->server);
        $groups = $lib->getLocationGroups();

        $this->addCountries($groups, 0);
        $this->addCities($groups, 1);
        $this->addHypervisorZones(2);
        $this->addNewtworkGroup(3);
        $this->addDataZones(4);
        $this->addTemplates(5);
        $this->addMemory(6);
        $this->addCpu(7);
        $this->addDisk(8);
        $this->addSwapUnit(9);
        $this->addIpAddresses(10);
        $this->addPortSpeed(11);
        $this->addCpuPriority(12);
        $this->addAccelerator(13);
    }

    private function addCountries($groups, $order)
    {
        $this->addNewOption('country|Country', 1, $order);

        $sub = new ProductConfigOptionsSub();
        $alreadySavedSubs = $sub->where('configid', $this->savedOptions['country']->id)->get()->toArray();
        $alreadySavedNames = array_map(function($sub) {
            return $sub['optionname'];
        }, $alreadySavedSubs);

        foreach($groups as $item) {
            $group = $item['location_group'];
            if(in_array($group['country'], $alreadySavedNames)) {
                continue;
            }
            $newSub = $this->addNewSub($this->savedOptions['country']->id, $group['country']);
            $this->addForAllCurrencies($newSub);
            $alreadySavedNames[] = $group['country'];
        }
    }

    private function addCities($groups, $order)
    {
        $this->addNewOption('location_id|City', 1, $order);

        $sub = new ProductConfigOptionsSub();
        $all = $sub->where('configid', $this->savedOptions['location_id']->id)->get();
        $alreadySavedIds = [];
        foreach($all as $item) {
            $ex = explode("|", $item->optionname);
            $alreadySavedIds[] = $ex[0];
        }
        

        foreach($groups as $item) {
            $group = $item['location_group'];
            if(in_array($group['id'], $alreadySavedIds))
                    continue;
            $newSub = new ProductConfigOptionsSub();
            $newSub->fill([
                'configid' => $this->savedOptions['location_id']->id,
                'optionname' => $group['id'].'|'.$group['city'],
                'sortorder' => 0,
                'hidden' => 0
            ]);
            $newSub->save();
            $this->addForAllCurrencies($newSub);
            $alreadySavedIds[] = $group['id'];
        }
    }

    private function addHypervisorZones($order)
    {
        if($this->checkIfOptionExists('hypervisor_zone')) {
            return;
        }
        $hpvZone = new \NewOnApp_HypervisorZone(null);
        $hpvZone->setconnection($this->server);
        $hypervisor_zones = $hpvZone->getZones();
        if (!$hpvZone->error()) {

            $this->addNewOption('hypervisor_zone|Hypervisor Zone', 1, $order);
            $this->addNoneOption($this->savedOptions['hypervisor_zone']->id);

            foreach ($hypervisor_zones as $key => $value) {
                $name = $value['hypervisor_group']['id'] . '|' . $value['hypervisor_group']['label'];
                $newSub = $this->addNewSub($this->savedOptions['hypervisor_zone']->id, $name);
                $this->addForAllCurrencies($newSub);
            }
        }
    }

    private function addNewtworkGroup($order)
    {
        if($this->checkIfOptionExists('network_group')) {
            return;
        }
        $networkZone   = new \NewOnApp_NetworkZone(null);
        $networkZone->setconnection($this->server);
        $networks = $networkZone->getList();
        if ($networkZone->isSuccess()) {

            $this->addNewOption('network_group|Network Group', 1, $order);
            $this->addNoneOption($this->savedOptions['network_group']->id);

            foreach ($networks as $network) {
                $name = $network['network_group']['id'] . '|' . $network['network_group']['label'];
                $newSub = $this->addNewSub($this->savedOptions['network_group']->id, $name);
                $this->addForAllCurrencies($newSub);
            }
        }
    }

    private function addDataZones($order)
    {
        if($this->checkIfOptionExists('data_store') && $this->checkIfOptionExists('swap_store')) {
            return;
        }
        $dataStoreZone = new \NewOnApp_DataStoreZone(null);
        $dataStoreZone->setconnection($this->server);
        $data_zones = $dataStoreZone->getList();

        if ($dataStoreZone->isSuccess()) {
            if(!$this->checkIfOptionExists('data_store')) {
                $this->addNewOption('data_store|Primary Data Store', 1, $order);
                $this->addNoneOption($this->savedOptions['data_store']->id);
            }
            if(!$this->checkIfOptionExists('swap_store')) {
                $this->addNewOption('swap_store|Swap Data Store', 1, ++$order);
                $this->addNoneOption($this->savedOptions['swap_store']->id);
            }

            foreach ($data_zones as $zone) {
                $name = $zone['data_store_group']['id'] . '|' . $zone['data_store_group']['label'];

                $newSub = $this->addNewSub($this->savedOptions['data_store']->id, $name);
                $this->addForAllCurrencies($newSub);
                
                $newSub = $this->addNewSub($this->savedOptions['swap_store']->id, $name);
                $this->addForAllCurrencies($newSub);

            }
        }
    }
    
    function array_sort_new($array, $on, $order=SORT_ASC)
    {
        $new_array = array();
        $sortable_array = array();

        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }

            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                break;
                case SORT_DESC:
                    arsort($sortable_array);
                break;
            }

            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $array[$k];
            }
        }

        return $new_array;
    }

    private function addTemplates($order)
    {
        if($this->checkIfOptionExists('template_id')) {
            return;
        }
        $template = new \NewOnApp_Template(null);
        $template->setconnection($this->server);
        // Default Template
        $templates = $template->getSystemTemplates();
        $this->addNewOption('template_id|OS Template', 1, $order);

        if($this->product->getConfig('showvCentertemplates'))
        {
            $vcentertemplates = $template->getSystemTemplatesVcenter();
        }
        
        $templatestoadd = [];
 
        foreach ($templates as $template) {
            $templatestoadd[] = [
                'id' => $template['image_template']['id'],
                'name' => $template['image_template']['label']
            ];
        }
        
        if($this->product->getConfig('showvCentertemplates')) {
            foreach ($vcentertemplates as $template)
            {
                $templatestoadd[] = [
                    'id' => $template['vcenter_image_template']['id'],
                    'name' => $template['vcenter_image_template']['label']
                ];
            }
        }
        
        $templatestoadd = $this->array_sort_new($templatestoadd, 'name', SORT_ASC);
        
        foreach($templatestoadd as $template)
        {
            $name = $template['id'] . '|' . $template['name'];
            $newSub = $this->addNewSub($this->savedOptions['template_id']->id, $name);
            $this->addForAllCurrencies($newSub);
        }
    }

    private function addIpAddresses($order)
    {
        if($this->checkIfOptionExists('ip_addresses')) {
            return;
        }
        $this->addNewOption('ip_addresses|Total IP Address', 4, $order, 0, 1, 50);
        $this->addNewSub($this->savedOptions['ip_addresses']->id, 'Unit');
    }

    private function addPortSpeed($order)
    {
        if($this->checkIfOptionExists('rate_limit')) {
            return;
        }
        $this->addNewOption('rate_limit|Port Speed', 4, $order, 0, 1, 100);
        $this->addNewSub($this->savedOptions['rate_limit']->id, 'Mbps');
    }

    private function addCpuPriority($order)
    {
        if($this->checkIfOptionExists('cpu_shares')) {
            return;
        }
        $this->addNewOption('cpu_shares|CPU Priority', 4, $order, 0, 1, 100);
        $this->addNewSub($this->savedOptions['cpu_shares']->id, 'Unit');
    }

    private function addAccelerator($order)
    {
        if($this->checkIfOptionExists('accelerator')) {
            return;
        }
        $this->addNewOption('accelerator|Accelerator', 3, $order, 0, 1, 100);
        $this->addNewSub($this->savedOptions['accelerator']->id, '');
    }
    
    private function addSwapUnit($order)
    {
        if($this->checkIfOptionExists('swap_disk_size')) {
            return;
        }
        $someCondition = $this->isSetToGB('swap_unit');
        $this->addNewOption('swap_disk_size|Swap Disk Size', 4, $order, 0, $someCondition ? 1 : 1024,
            $someCondition ? 100 : 51200);

        $name = $this->product->getConfig('swap_unit') == "" ? 'GB' : $this->product->getConfig('swap_unit');
        $this->addNewSub($this->savedOptions['swap_disk_size']->id, $name);
    }

    private function addDisk($order)
    {
        if($this->checkIfOptionExists('primary_disk_size')) {
            return;
        }
        $someCondition = $this->isSetToGB('primary_unit');
        $this->addNewOption('primary_disk_size|Primary Disk Size', 4, $order, 0, $someCondition ? 5 : 5120,
            $someCondition ? 100 : 51200);

        $name = $this->product->getConfig('primary_unit') == "" ? 'GB' : $this->product->getConfig('primary_unit');
        $this->addNewSub($this->savedOptions['primary_disk_size']->id, $name);
    }

    private function addCpu($order)
    {
        if($this->checkIfOptionExists('cpus')) {
            return;
        }
        $this->addNewOption('cpus|CPU(s)', 4, $order, 0, 1, 100);

        $this->addNewSub($this->savedOptions['cpus']->id, 'Unit');
    }

    private function addMemory($order)
    {
        if($this->checkIfOptionExists('memory')) {
            return;
        }
        $someCondition = $this->isSetToMB('memory_unit');
        $this->addNewOption('memory|Memory', 4, $order, 0, $someCondition ? 128 : 1, $someCondition ? 6144 : 6);
        $name = $this->product->getConfig('memory_unit') == "" ? 'MB' : $this->product->getConfig('memory_unit');
        $this->addNewSub($this->savedOptions['memory']->id, $name);
    }

    private function addNewSub($configId, $name, $order = 0, $hidden = 0)
    {
        $newSub = new ProductConfigOptionsSub();
        $newSub->fill([
            'configid'   => $configId,
            'optionname' => $name,
            'sortorder'  => $order,
            'hidden'     => $hidden,
        ]);
        $newSub->save();

        return $newSub;
    }

    private function addNoneOption($groupId)
    {
        $newSub = $this->addNewSub($groupId, '0|None', 0, 1);
        $this->addForAllCurrencies($newSub);
    }

    private function addForAllCurrencies(ProductConfigOptionsSub $newSub)
    {
        foreach ($this->currencies as $currency) {
            $newPrice = new Pricing();
            $newPrice->fill([
                'type'      => Pricing::PRICING_TYPE_CONFIGOPTIONS,
                'currency'  => $currency->id,
                'relid'     => $newSub->id,
                'msetupfee' => '0.00',
            ]);
            $newPrice->save();
        }
    }

    public function checkIfOptionExists($name)
    {
        $optionModel = new ProductConfigOptions();
        $optionQuery = $optionModel->where('optionname', 'like', $name.'%' )
                                   ->where('gid', $this->group->id);
        return $optionQuery->count() > 0;
    }

    /**
     * @param      $name
     * @param int  $type
     * @param int  $order
     * @param int  $hidden
     * @param null $min
     * @param null $max
     */
    private function addNewOption($name, $type = 4, $order = 0, $hidden = 0, $min = 0, $max = 0)
    {
        $newOption = new ProductConfigOptions();
        $newOption->fill([
            'gid'        => $this->group->id,
            'optionname' => $name,
            'optiontype' => $type,
            'order'      => $order,
            'hidden'     => $hidden,
            'qtyminimum' => $min,
            'qtymaximum' => $max,
        ]);
        $newOption->save();

        $this->addToSavedOptions($newOption);
    }

    private function isSetToMB($slug)
    {
        return ($this->product->getConfig($slug) == "" || $this->product->getConfig($slug) == "MB");
    }
    
    private function isSetToGB($slug)
    {
        return ($this->product->getConfig($slug) == "" || $this->product->getConfig($slug) == "GB");
    }

    /**
     * @param ProductConfigOptions $option
     * @return bool
     */
    private function addToSavedOptions(ProductConfigOptions $option)
    {
        $nameArr                         = explode('|', $option->optionname);
        $this->savedOptions[$nameArr[0]] = $option;

        return true;
    }

    /**
     * @return mixed
     */
    private function saveConfigGroup()
    {
        $newGroup = new ProductConfigGroups();
        $newGroup->fill([
            'name'        => 'Configurable options for onAppVPS',
            'description' => 'Auto generated by module',
        ]);
        $newGroup->save();

        $link = new ProductConfigLinks();
        $link->fill([
            'gid' => $newGroup->id,
            'pid' => $this->product->id,
        ]);
        $link->save();

        $this->group = $newGroup;

        return $newGroup->id;
    }

}