<?php


/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */
class NewOnApp_Template extends NewOnApp_Connection {
    
    protected $_id      = null;
    protected $_api     = null;

    public function __construct($id = null) {
        $this->_id = $id;
    }

    public function getAll(){
        return $this->_api->sendGET('/templates/all');
    }
    
    public function getSystemTemplatesVcenter(){
        return $this->_api->sendGET('/vcenter/templates');
    }
    
    public function getResourcePoolsVcenter(){
        return $this->_api->sendGET('/vcenter/resource_pools');
    }
    
    public function getSystemTemplates(){
        return $this->_api->sendGET('/templates');
    }
    
    public function getOwnTemplates(){
        return $this->_api->sendGET('/templates/own');
    }
    
    public function getUserTemplates(){
        return $this->_api->sendGET('/templates/user');
    }
    
    public function getParticularUserTemplates($user_id)
    {
        return $this->_api->sendGET('/templates/user/' . $user_id);
    }
    
    public function getInactiveTemplates(){
        return $this->_api->sendGET('/templates/inactive');
    }
    
    public function getTemplateDetails(){
        return $this->_api->sendGET('/templates/'.$this->_id);
    }
    
    public function makePublicTemplate(){
        return $this->_api->sendPOST('/templates/'.$this->_id.'/make_public');
    }
    
    public function edit(){
        return $this->_api->sendPUT('/templates/'.$this->_id.'/make_public');
    }
    
    public function delete(){
        return $this->_api->sendDELETE('/templates/'.$this->_id);
    }
     
    public function getTemplateStores(){
        return $this->getTemplatePricing();
    }
    
    public function getTemplatePricing($hypervisorGroupId=null){
        $result =  $this->_api->sendGET('/template_store');
        $result = self::templatesFormat($result);
        if(!$hypervisorGroupId)
            return $result;
        foreach($result as $k => $t){
            if($hypervisorGroupId == $t['hypervisor_group_id'])
                 return $t;
        }
        
    }
    private static function templatesFormat($templates){
    $reusult = array();
    foreach($templates as $template){
        $temp = $template;
        unset($temp['children'] );
        $reusult[] =$temp ;
        if(!empty($template['children'])){
            $temp = self::templatesFormat($template['children']);
            $reusult = array_merge($reusult, $temp);
        }
    }
    return $reusult;
}
    
    
    public function getTemplatesFromStore($id){
        return $this->_api->sendGET('/settings/image_template_groups/'.$id.'/relation_group_templates');
    }
}