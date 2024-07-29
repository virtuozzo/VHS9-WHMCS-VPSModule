<?php

namespace OnAppVps\Database;

class FederatedTemplates extends Database
{

    protected $table    = 'onappVPS_FederatedTemplates';
    protected $builder;
    public    $fillable = ['id', 'label', 'onapp_id', 'group', 'location_id','hypervisor_group_id'];

    public function saveTemplates(array $templates, $product, $templatesvcenter)
    {
        $alreadySaved = $this->getAllSavedByOnappIds();
        try {
            foreach ($templates as $group => $values) {
                foreach ($values as $value) {
                    if (in_array($value['id'], array_keys($alreadySaved))) {
                        $toUpdate = $alreadySaved[$value['id']];
                        $toUpdate->label = $value['label'];
                        $toUpdate->group = $group;
                        $toUpdate->location_id = $value['location_id'];
                        $toUpdate->hypervisor_group_id = $value['hypervisor_group_id'];
                        $toUpdate->save();
                    } else {
                        $newTemplate = new FederatedTemplates();
                        $newTemplate->fill([
                            'label'    => $value['label'],
                            'onapp_id' => $value['id'],
                            'group'    => $group,
                            'location_id' => $value['location_id'],
                            'hypervisor_group_id' => $value['hypervisor_group_id'],
                        ]);
                        $newTemplate->save();
                    }
                }
            }
            
            if($product->getConfig('showvCentertemplates')) {
                foreach ($templatesvcenter as $template)
                {
                    if (in_array($template['vcenter_image_template']['id'], array_keys($alreadySaved))) {
                        $toUpdate = $alreadySaved[$template['vcenter_image_template']['id']];
                        $toUpdate->label = $template['vcenter_image_template']['label'];
                        $toUpdate->group = 'vCenter';
                        $toUpdate->location_id = '';
                        $toUpdate->hypervisor_group_id = '';
                        $toUpdate->save();
                    } else {
                        $newTemplate = new FederatedTemplates();
                        $newTemplate->fill([
                            'label'    => $template['vcenter_image_template']['label'],
                            'onapp_id' => $template['vcenter_image_template']['id'],
                            'group'    => 'vCenter',
                            'location_id' => '',
                            'hypervisor_group_id' => ''
                        ]);
                        $newTemplate->save();
                    }
                }
            }
            
        } catch (\Exception $e) {
            die($e->getMessage());
        }

        return true;
    }

    public function getAllSavedByOnappIds()
    {
        $all = [];
        foreach ($this->all() as $item) {
            $all[$item->onapp_id] = $item;
        }

        return $all;
    }

    public function getDefaults()
    {
        $defaults = [];
        foreach ($this->getAllSavedByOnappIds() as $onappId => $template) {
            $defaults[$template['id']] = $template;
        }

        return $defaults;
    }

    public function getAllSavedByCity()
    {
        $all = [];
        foreach ($this->all() as $item) {
            $all[$item->group][] = $item;
        }

        return $all;
    }

}