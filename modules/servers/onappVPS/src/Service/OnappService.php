<?php

namespace OnAppVps\Service;


use OnAppVps\Database\LocationGroups;

class OnappService
{

    protected $params = [];

    /**
     * OnappService constructor.
     *
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    public function filterVMArrayByFederated(array $virtualMachine)
    {
        $location = $this->obtainLocationFromParams();
        if($location === false) {
            return $virtualMachine;
        }
        
        $virtualMachine['virtual_machine']['location_id'] = $location->id;
        
        if($location->federated == 0) {
            return $virtualMachine;
        }

        unset($virtualMachine['virtual_machine']['hypervisor_group_id']);
        unset($virtualMachine['virtual_machine']['data_store_group_primary_id']);
        unset($virtualMachine['virtual_machine']['data_store_group_swap_id']);
        unset($virtualMachine['virtual_machine']['primary_network_group_id']);

        return $virtualMachine;
    }

    /**
     * @return bool|LocationGroups
     */
    private function obtainLocationFromParams()
    {
        $locationId = $this->params['configoptions']['location_id'];
        $locationModel = new LocationGroups();
        $query = $locationModel->where('location_id', $locationId);
        if($query->count() == 0) {
            return false;
        }

        return $query->first();
    }

}