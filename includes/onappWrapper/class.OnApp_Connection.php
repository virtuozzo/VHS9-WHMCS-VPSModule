<?php


/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */
class NewOnApp_Connection
{
    protected $api;
    /**
     *
     * @var NewOnApp_WrapperAPI
     */
    protected $_api;
    
    private $apiVersion;

    public function setconnection($params, $user = false)
    {

        $params = (array)$params;
        if (!$user)
            $this->_api = new NewOnApp_WrapperAPI(NewOnApp_WrapperAPI::getHostname($params), $params['serverusername'], $params['serverpassword']);
        else {
     
            $this->_api = new NewOnApp_WrapperAPI(NewOnApp_WrapperAPI::getHostname($params), trim($params['username']), trim(htmlspecialchars_decode($params['password'])));
         
            }
    }
    
    public function setapi(NewOnApp_WrapperAPI $api)
    {
       
        $this->_api = $api;
    }
    
    /**
     * Get API
     *
     * @return NewOnApp_WrapperAPI
     */
    public function getApi()
    {
        return $this->_api;
    }
    
    public function setDetails($auth)
    {
        $this->_api = new NewOnApp_WrapperAPI($this->_api->getHost(), $auth['email'], $auth['key']);
    }

    public function isSuccess()
    {
        $response = $this->_api->getResponse();
        if ($response['info']['http_code'] >= 200 && $response['info']['http_code'] <= 210)
            return true;
        else
            return false;
    }
    
    public function error()
    {
        if ($this->_api->getError())
            return $this->_api->getError();
        else
            return false;
    }
    
    public function lb_error()
    {
        $response = $this->_api->getResponse();

        return preg_replace('/[^A-Za-z0-9\.\,\[\] ]/', '', implode(', ', json_decode($response['response_body'], true)));
    }


    public function getResponse()
    {
        return $this->_api->getResponse();
    }
    
    public function getHostname($params)
    {
        return NewOnApp_WrapperAPI::getHostname($params);
    }
    
    public function setTimeOut($timeout)
    {
        if (!$timeout)
                return false;
          $this->_api->setTimeout((int)$timeout);
    }
    
    public function getVersion()
    {
        if(!is_null($this->apiVersion)){
            return $this->apiVersion;
        }
        $res = $this->_api->sendGET('/version');
        if(preg_match('/\-/', $res['version'])){
            $res['version'] = preg_replace('/\-(.*)/', '', $res['version']);
        }
        return $this->apiVersion = $res['version'];
    }
}

