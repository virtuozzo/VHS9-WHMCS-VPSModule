<?php


/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */

if (!class_exists('NewOnApp_WrapperAPI')) {

    class NewOnApp_WrapperAPI {

        protected $_ch       = null;
        protected $_timeout  = 30;
        protected $_error    = null;
        protected $_response = null;
        protected $_hostname = null;
        protected $_username = null;
        protected $_password = null;
        protected $_debug    = true;
        protected $_isjson   = true;
        protected $_module   = null;
        public static $moduleLogs = false;
        private static $throwException = false;

        public function __construct($hostname, $username, $password) {
            global $CONFIG;
        
            $this->_hostname = $hostname;
            $this->_username = $username;
            $this->_password = $password;
            $this->_module   = (isset($GLOBALS['modulename']) ? $GLOBALS['modulename'] : 'onapp');
            $this->_debug    = $CONFIG['ModuleDebugMode']=='on' ? true : false;
        }

        public function setDebugMode($mode = true) {
     
            $this->_debug = $mode;
        }
        
        public function getHost()
        {
            return $this->_hostname;
        }

        public function getError() {
            return $this->_error;
        }

        public function getResponse() {
            return $this->_response;
        }
        public function setResponse($response) {
            $this->_response = $response;
        }
        
        public function setTimeout($timeout) {
            $this->_timeout = $timeout;
        }

        public function sendGET($resource, array $getdata = array()) {
            $this->_setJson();
            return $this->simpleCall('GET', $resource, array(), $getdata);
        }
        
        public function sendGETWithoutJSON($resource, array $getdata = array()) {
            return $this->simpleCall('GET', $resource, array(), $getdata);
        }
        
        public function sendPOST($resource, array $postdata = array(), array $getdata = array()) {
            return $this->simpleCall('POST', $resource, $postdata, $getdata);
        }

        public function sendPUT($resource, array $postdata = array(), array $getdata = array()) {
            return $this->simpleCall('PUT', $resource, $postdata, $getdata);
        }

        public function sendDELETE($resource, array $postdata = array(), array $getdata = array()) {
            return $this->simpleCall('DELETE', $resource, $postdata, $getdata);
        }

        public function _unsetJson() {
                return $this->_isjson = false;
        }
        
        public function _setJson() {
                return $this->_isjson = true;
        }
        
        public function testConnection() {
            $call = $this->sendGET('/version');
            return isset($call['version']) || $call['errors'];
        }

        /**
         * 
         * @param string $method POST|GET|PUT|DELETE
         * @param string $resource e.g. /virtual_machines
         * @param array $postdata
         * @param array $getdata
         * @return array|bool json_decode
         */
        public function simpleCall($method, $resource, array $postdata = array(), array $getdata = array()) {
  
   
            $this->_error = null;
            $url = $this->_hostname . $resource . ($this->_isjson ? '.json' : '');
            if (!empty($getdata))
            {
                $url .= '?' . http_build_query($getdata);
            }
            $alowed_methods = array('GET', 'POST', 'PUT', 'DELETE');
            if (!in_array($method, $alowed_methods)) {
                $this->_error = 'Wrong request method.';
                return false;
            }

            $this->_ch = curl_init();
            curl_setopt($this->_ch, CURLOPT_URL, $url);
            curl_setopt($this->_ch, CURLOPT_TIMEOUT, $this->_timeout);
            curl_setopt($this->_ch, CURLOPT_FRESH_CONNECT, 1);
            curl_setopt($this->_ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($this->_ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($this->_ch, CURLOPT_USERPWD, $this->_username . ':' . htmlspecialchars_decode($this->_password));
            curl_setopt($this->_ch, CURLOPT_FOLLOWLOCATION, true);
           
            if(!$this->_isjson)
            {
                $http_header = array(
                    'Content-Type: text/html',
                    'Accept: text/html'
                );
            }
            else
            {
                $http_header = array(
                    'Content-Type: application/json',
                    'Accept: application/json'
                );
            }

            if (!empty($postdata)) {
                $data = json_encode($postdata);
                curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $data);
                $http_header[] = 'Content-Length: ' . strlen($data);
            }
            
            curl_setopt($this->_ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->_ch, CURLOPT_HEADER, true);
            curl_setopt($this->_ch, CURLOPT_HTTPHEADER, $http_header);
            curl_setopt($this->_ch, CURLINFO_HEADER_OUT, 1);

            $this->_response = array();
            $this->_response['response_body'] = curl_exec($this->_ch);
            $this->_response['info'] = curl_getinfo($this->_ch);
            
            logModuleCall("OnApp", $resource, $this->_response['info']['request_header'].$data, $this->_response['response_body']);
            
            $curlHeaderSize = $this->_response['info']['header_size'];
            $this->_response['headers'] = substr($this->_response['response_body'], 0, $curlHeaderSize);
            $this->_response['response_body'] = substr($this->_response['response_body'], $curlHeaderSize);
  
            if($this->_isjson)
                $data = $this->_response['response_body'] ? json_decode($this->_response['response_body'], true) : array();
            else
                return $this->_response['response_body'];
            // adds log
            if (!self::$moduleLogs && $this->_debug && !self::$moduleLogs && $this->_response['headers']  &&                    ($this->_response['info']['http_code']>210 || $this->_response['info']['http_code'] < 200)) {
                $headers_arr = explode("\n", $this->_response['headers']);
                $res_log = (isset($headers_arr[0]) ? $headers_arr[0] : 'HTTP ' . $this->_response['info']['http_code']) . "\n" . print_r($data, true);
                $this->logModuleCall($method, $resource, array_merge($postdata, $getdata), $res_log);
            }
            /**
             * debug
              */ 
            if (self::$moduleLogs){
                $headers_arr = explode("\n", $this->_response['headers']);
                $res_log = (isset($headers_arr[0]) ? $headers_arr[0] : 'HTTP ' . $this->_response['info']['http_code']) . "\n" . print_r($data, true);
                $this->logModuleCall($method, $resource, array_merge($postdata, $getdata), $res_log); 
            }
             
            if (!$this->_response['response_body'] && isset($this->_response['info']['http_code']) && !in_array((int)$this->_response['info']['http_code'],[202,204]) ) {
                $curlErr = curl_error($this->_ch);
                $this->_error = $curlErr ? $curlErr : 'Response body is empty for method: ' . $method;
                if($this->_debug && !self::$moduleLogs){
                      $res_log = (isset($headers_arr[0]) ? $headers_arr[0] : 'HTTP ' . $this->_response['info']['http_code']) . "\n" . print_r($data, true);
                      $this->logModuleCall($method, $resource, array_merge($postdata, $getdata), $res_log);
                }
//                if(self::$throwException){
//                    throw new \Exception($this->_error);
//                }
                return false;
            } else {
                if (!empty($data['errors']) && is_array($data['errors'])) {
                    
  
               foreach ($data['errors'] as $k => $errorsArr) {
       
                        if (is_array($errorsArr)){
                            $errorsArr =  array_unique($errorsArr);
                            $this->_error .= '(' . $k . ') ' . implode('. ', $errorsArr) . '. ';
                        }else
                            $this->_error .= $errorsArr.". ";
                    }
//                    if(self::$throwException){
//                        throw new \Exception($this->_error);
//                    }
                    
                    return false;
                }
                // onapp API sometimes returns "error" on "errors"
                if (!empty($data['error']) && is_array($data['error'])) {
   
                    foreach ($data['error'] as $k => $errorsArr) {
                        if (is_array($errorsArr))
                            $this->_error .= '(' . $k . ') ' . implode('. ', $errorsArr) . '. ';
                        else
                            $this->_error = $errorsArr;
                    }
                    if(self::$throwException){
                        throw new \Exception($this->_error);
                     }
                    return false;
                }
                
                if(!empty($data['base']) && is_array($data['base'])){
   
                     foreach ($data['base'] as $k => $errorsArr) {
                         $this->_error .= $errorsArr."<br />";
                     }
                     if(self::$throwException){
                        throw new \Exception($this->_error);
                     }
                     return false;
                }
                
                if(!empty($data['error']) && !is_array($data['error'])){

                    $this->_error = $data['error'];
                    if(self::$throwException){
                        throw new \Exception($this->_error);
                     }
                    return false;
                }
                
                if(in_array($this->_response['info']['http_code'], array('401'))){
                    

                    $this->_error = $this->_response['response_body'];
               
                    if(self::$throwException){
                        throw new \Exception($this->_error);
                     }
                        
                    return false;
                }
                if(in_array($this->_response['info']['http_code'], array('422'))){

                    $this->_error = "HTTP/1.1 422 Unprocessable Entity";
                    if(self::$throwException){
                        throw new \Exception($this->_error);
                     }
                   
                  }
                
                 $this->logModuleCall($method, $resource, array_merge($postdata, $getdata),$this->_response['response_body']);
                return $data;
            }
        }

        public static function getHostname(array $params, $checkHttp = true) {
  
            if (!isset($params['serverhostname']) || !isset($params['serverip']))
                return false;
   
            $host = $params['serverhostname'] ? $params['serverhostname'] : $params['serverip'];
            // setup http
            $http = isset($params['serversecure']) && $params['serversecure'] ? 'https://' : 'http://';
            
            return ($checkHttp ? $http : '') . $host;
        }

        /**
         * WHMCS Module Log call
         * @param string $method
         * @param string $resource
         * @param array $params
         * @param string $response
         * @return type
         */
        private function logModuleCall($method, $resource, array $params, $response) {
            if (function_exists('logModuleCall')) {

                // hide some values
                foreach (array('initial_root_password_encryption_key', 'initial_root_password') as $key) {
                    if (isset($params[$key]))
                        $params[$key] = substr($params[$key], 0, 3) . '...';
                }
                return logModuleCall(
                        ($this->_module ? $this->_module : "OnApp"), $resource, $method . ' ' . $this->_hostname . $resource . '.json' . "\n" . print_r($params, true), '', $response
                );
            }
        }
        
        public static function moduleLogsOn(){
            self::$moduleLogs = true;
        }
        
        public static function throwException(){
            self::$throwException = true;
        }


    }

}
