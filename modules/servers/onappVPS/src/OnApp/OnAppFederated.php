<?php

namespace OnAppVps\OnApp;

use OnAppVps\Database\FederatedTemplates; 
use OnAppVps\Database\LocationGroups;

class OnAppFederated
{

    protected $id;
    protected $params;
    

    /**
     * onAppFederated constructor.
     *
     * @param array             $params
     */
    public function __construct(array $params, $productId)
    {
        onapp_loadCLass();
        $this->params = $params;
        $this->id = $productId;
    }

    /**
     * @return array|mixed
     */
    public function getTemplatesIdsByCities()
    {
        $templates = new FederatedConfiguration();
        return $templates->getAllWithDefaultsForProductId($this->id);
    }

    /**
     * @return string
     */
    public function synchronizeFederatedUrl()
    {
        return sprintf('%s?action=%s&id=%d&tab=%s&generateFederated=true', $_SERVER['SCRIPT_NAME'], $_GET['action'], $_GET['id'], $_GET['tab']);
    }

    /**
     * Checks if cache should be synchronized
     * @deprecated since version 1.7.0
     */
    private function checkIfShouldSynchronize()
    {
        $generateParam = isset($_GET['generateFederated'])? $_GET['generateFederated']:false;

        if($generateParam) {
            $this->synchronize();
        }
    }

    /**
     * Performs  call to onapp api and saves result to cache
     *
     * @return $this
     * @throws Exception
     */
    public function synchronize()
    {
        $product = new \onappVPS_Product($this->id);
        
        $templatevcenter = $this->getTemplate()->getSystemTemplatesVcenter();
        
        $templates = new FederatedTemplates();
        $templates->saveTemplates($this->getTemplatesListByCities(), $product, $templatevcenter);

        $this->syncLocationGroups();
        
        if(!$product ->hasConfigurableOptions()){
            $product->setupDefaultConfigurableOptions();
        }
        return $this;
    }

    /**
     * Get templates from API and list by cities
     *
     * @return array
     */
    private function getTemplatesListByCities()
    {
        $templates = $this->getTemplate()->getTemplatePricing();
        $hpGroup   = $this->getHypervisorZone();
       
        $toCache = [];
        foreach($templates as $template) {
            
            foreach($template['relations'] as $k => $va){
                if( $va['image_template']['operating_system_distro'] && $va['image_template']['operating_system_distro']=="lbva"){
                    unset($template['relations'][$k]);
                }else if( isset($va['image_template']['cdn']) && $va['image_template']['cdn']===1){
                    unset($template['relations'][$k]);
                }
            }
            if(is_numeric($template['hypervisor_group_id'])){
                $hpGroup->setId($template['hypervisor_group_id']);
                $detail = $hpGroup->getZone();
                $template['location_id'] = $detail['hypervisor_group']['location_group_id'];
            }
           
            $toCache[$template['label']] = array_map(function ($v) use($template) {
                    return [
                        'id' => $v['template_id'],
                        'label' => $v['image_template']['label'],
                        'location_id' => $template['location_id'],
                        'hypervisor_group_id' => $template['hypervisor_group_id'],
                    ];
                }, $template['relations']);
            sort($toCache[$template['label']]);
        }
        unset($templates, $hpGroup,  $detail );
        return $toCache;
    }
    
    
    public function syncLocationGroups()
    {
        $groups = $this->getLocation()->getLocationGroups();

        $locationGroups = new LocationGroups();
        $locationGroups->saveLocationsFromOnApp($groups);
    }

    /**
     * @return \NewOnApp_HypervisorZone
     */
    public function getHypervisorZone()
    {
        $lib = new \NewOnApp_HypervisorZone($this->id);
        $lib->setconnection($this->params);

        return $lib;
    }

    /**
     * @return NewOnApp_Hypervisor
     */
    public function getHypervisor()
    {
        $lib = new \NewOnApp_Hypervisor($this->id);
        $lib->setconnection($this->params);

        return $lib;
    }

    /**
     * @return \NewOnApp_Template
     */
    public function getTemplate()
    {
        $lib = new \NewOnApp_Template($this->id);
        $lib->setconnection($this->params);

        return $lib;
    }

    /**
     * @return \NewOnApp_Location
     */
    public function getLocation()
    {
        $lib = new \NewOnApp_Location($this->id);
        $lib->setconnection($this->params);

        return $lib;
    }

}