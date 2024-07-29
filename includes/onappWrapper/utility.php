<?php

use onappWrapper\PdoWrapper;


/**
 * @author Grzegorz Draganik <grzegorz@modulesgarden.com>
 */


if (!function_exists('onApp_getHostname')) {

    function onApp_getHostname($params, $checkHttp = true) {
        $host = $params['serverhostname'] ? $params['serverhostname'] : $params['serverip'];
        // setup port
        $matches = array();
        preg_match('/:([0-9]+)/', $host, $matches);
        $host .= isset($matches[1]) ? '' : ''; // second - default port
        // setup http
        $http = isset($params['serversecure']) && $params['serversecure'] ? 'https://' : 'http://';

        return ($checkHttp ? $http : '') . $host;
    }

}


if (!function_exists('onapp_customFieldExists')) {

    function onapp_customFieldExists($relid, $fieldname) {
        $q = PdoWrapper::query('SELECT id FROM tblcustomfields WHERE relid = ? AND type = "product" AND `fieldname` LIKE ?', array($relid, (strpos($fieldname, '|') ? $fieldname . '|%' : $fieldname . '%')));
        return (bool) PdoWrapper::numRows($q);
    }

}

if (!function_exists('onapp_addCustomFieldValue')) {

    function onapp_addCustomFieldValue($fieldname, $relid, $serviceid, $value) {
        if(onapp_customFieldExists($relid,$fieldname)){
            $field = PdoWrapper::fetchAssoc(PdoWrapper::query("SELECT `id` FROM tblcustomfields WHERE `type`='product' AND `relid`=? AND `fieldname` LIKE ?", array($relid, (strpos($fieldname, '|') ? $fieldname . '|%' : $fieldname . '%'))));
            PdoWrapper::query('DELETE FROM tblcustomfieldsvalues WHERE fieldid = ? AND relid = ?', array($field['id'], $serviceid));
            return PdoWrapper::query('INSERT INTO tblcustomfieldsvalues(fieldid,relid,value) VALUES(?,?,?)', array($field['id'], $serviceid, $value));
        }
    }

}

if (!function_exists('onapp_formatBytes')) {
    function onapp_formatBytes($bytes, $precision = 2) { 
      $units = array('B', 'KB', 'MB', 'GB', 'TB'); 

      $bytes = max($bytes, 0); 
      $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
      $pow = min($pow, count($units) - 1);

      $bytes /= (1 << (10 * $pow)); 

      return round($bytes, $precision) . ' ' . $units[$pow]; 
     }
}

if (!function_exists('onapp_pass_generator')){
    function onapp_pass_generator($length = 12, $special = 2){
        return onapp_generatePassword($length, 2, 2, $special);
        /*
        $out = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%^&*()-+=";
        $xc = '';
        for ($i = 0; $i < $length; $i++) {
                $xc .= $out[rand(0,strlen($out))];
        }
        return $xc;
         * 
         */
    }
}


if (!function_exists('onapp_loadClass')) {

    function onapp_loadCLass() {
       
        include_once ROOTDIR . DS . 'includes' . DS . 'onappWrapper' . DS . 'class.OnApp_Connection.php';
        include_once ROOTDIR . DS . 'includes' . DS . 'onappWrapper' . DS . 'class.OnApp_Billing.php';
        include_once ROOTDIR . DS . 'includes' . DS . 'onappWrapper' . DS . 'class.OnApp_Disk.php';
        include_once ROOTDIR . DS . 'includes' . DS . 'onappWrapper' . DS . 'class.OnApp_VMBackup.php';
        include_once ROOTDIR . DS . 'includes' . DS . 'onappWrapper' . DS . 'class.OnApp_Client.php';
        include_once ROOTDIR . DS . 'includes' . DS . 'onappWrapper' . DS . 'class.OnApp_Hypervisor.php';
        include_once ROOTDIR . DS . 'includes' . DS . 'onappWrapper' . DS . 'class.OnApp_HypervisorZone.php';
        include_once ROOTDIR . DS . 'includes' . DS . 'onappWrapper' . DS . 'class.OnApp_FirewallRule.php';
        include_once ROOTDIR . DS . 'includes' . DS . 'onappWrapper' . DS . 'class.OnApp_IPAddress.php';
        include_once ROOTDIR . DS . 'includes' . DS . 'onappWrapper' . DS . 'class.OnApp_IPAddressJoin.php';
        include_once ROOTDIR . DS . 'includes' . DS . 'onappWrapper' . DS . 'class.OnApp_Network.php';
        include_once ROOTDIR . DS . 'includes' . DS . 'onappWrapper' . DS . 'class.OnApp_NetworkInterface.php';
        include_once ROOTDIR . DS . 'includes' . DS . 'onappWrapper' . DS . 'class.OnApp_NetworkZone.php';
        include_once ROOTDIR . DS . 'includes' . DS . 'onappWrapper' . DS . 'class.OnApp_DataStoreZone.php';
        include_once ROOTDIR . DS . 'includes' . DS . 'onappWrapper' . DS . 'class.OnApp_DataStore.php';
        include_once ROOTDIR . DS . 'includes' . DS . 'onappWrapper' . DS . 'class.OnApp_Template.php';
        include_once ROOTDIR . DS . 'includes' . DS . 'onappWrapper' . DS . 'class.OnApp_User.php';
        include_once ROOTDIR . DS . 'includes' . DS . 'onappWrapper' . DS . 'class.OnApp_UserGroup.php';
        include_once ROOTDIR . DS . 'includes' . DS . 'onappWrapper' . DS . 'class.OnApp_UserRole.php';
        include_once ROOTDIR . DS . 'includes' . DS . 'onappWrapper' . DS . 'class.OnApp_WrapperAPI.php';
        include_once ROOTDIR . DS . 'includes' . DS . 'onappWrapper' . DS . 'class.OnApp_VM.php';
        include_once ROOTDIR . DS . 'includes' . DS . 'onappWrapper' . DS . 'class.OnApp_CDNEdgeGroup.php';
        include_once ROOTDIR . DS . 'includes' . DS . 'onappWrapper' . DS . 'class.OnApp_CDNResource.php';
        include_once ROOTDIR . DS . 'includes' . DS . 'onappWrapper' . DS . 'class.OnApp_LoadBalancer.php';
        include_once ROOTDIR . DS . 'includes' . DS . 'onappWrapper' . DS . 'class.OnApp_DNSZone.php';
        include_once ROOTDIR . DS . 'includes' . DS . 'onappWrapper' . DS . 'class.OnApp_Location.php';
        include_once ROOTDIR . DS . 'includes' . DS . 'onappWrapper' . DS . 'class.WHMCSProduct.php';
        include_once ROOTDIR . DS . 'includes' . DS . 'onappWrapper' . DS . 'class.OnApp_StorageServer.php';
        include_once ROOTDIR . DS . 'includes' . DS . 'onappWrapper' . DS . 'class.OnApp_Configuration.php';
        include_once ROOTDIR . DS . 'includes' . DS . 'onappWrapper' . DS . 'OnApp_Buckets.php';
        if(!class_exists('onappWrapper\PdoWrapper'))
            include_once ROOTDIR . DS . 'includes' . DS . 'onappWrapper' . DS . 'PdoWrapper.php';
        
    }

}

if (!function_exists('onapp_generatePassword')) {
    function onapp_generatePassword($l = 8, $c = 2, $n = 2, $s = 2) {
         // get count of all required minimum special chars
         $count = $c + $n + $s;

         // sanitize inputs; should be self-explanatory
         if(!is_int($l) || !is_int($c) || !is_int($n) || !is_int($s)) {
              trigger_error('Argument(s) not an integer', E_USER_WARNING);
              return false;
         }
         elseif($l < 0 || $l > 20 || $c < 0 || $n < 0 || $s < 0) {
              trigger_error('Argument(s) out of range', E_USER_WARNING);
              return false;
         }
         elseif($c > $l) {
              trigger_error('Number of password capitals required exceeds password length', E_USER_WARNING);
              return false;
         }
         elseif($n > $l) {
              trigger_error('Number of password numerals exceeds password length', E_USER_WARNING);
              return false;
         }
         elseif($s > $l) {
              trigger_error('Number of password capitals exceeds password length', E_USER_WARNING);
              return false;
         }
         elseif($count > $l) {
              trigger_error('Number of password special characters exceeds specified password length', E_USER_WARNING);
              return false;
         }

         // all inputs clean, proceed to build password

         // change these strings if you want to include or exclude possible password characters
         $chars = "abcdefghijklmnopqrstuvwxyz";
         $caps = strtoupper($chars);
         $nums = "0123456789";
         $syms = "!@#$%^&*()-+?";

         // build the base password of all lower-case letters
         for($i = 0; $i < $l; $i++) {
              $out .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
         }

         // create arrays if special character(s) required
         if($count) {
              // split base password to array; create special chars array
              $tmp1 = str_split($out);
              $tmp2 = array();

              // add required special character(s) to second array
              for($i = 0; $i < $c; $i++) {
                   array_push($tmp2, substr($caps, mt_rand(0, strlen($caps) - 1), 1));
              }
              for($i = 0; $i < $n; $i++) {
                   array_push($tmp2, substr($nums, mt_rand(0, strlen($nums) - 1), 1));
              }
              for($i = 0; $i < $s; $i++) {
                   array_push($tmp2, substr($syms, mt_rand(0, strlen($syms) - 1), 1));
              }

              // hack off a chunk of the base password array that's as big as the special chars array
              $tmp1 = array_slice($tmp1, 0, $l - $count);
              // merge special character(s) array with base password array
              $tmp1 = array_merge($tmp1, $tmp2);
              // mix the characters up
              shuffle($tmp1);
              // convert to string for output
              $out = implode('', $tmp1);
         }

         return $out;
    }
}

/**
 * ValidateVariable()
 *
 * method retrieve and sanatize variable requests
 *
 * @author					Neil Young neil.young@neilyoungcv.com
 *
 * EXAMPLE USAGE:
 *
 * $customerId = ValidateVariable('my_variable', 'int', 'request');
 *
 * @param string				$index
 * @param int					$type
 * @param string				$gpcs
 */

function ValidateVariable($index, $type = 'int' , $gpcs = 'request')
{
	//convert the type identifier to uppercase
	$type = strtoupper($type);
	//get the default value
	$value = ($type === 'INT' || $type === 'BOOLEAN') ? 0 : '';	//	default if nothing set
	//GPCS - (Get, Post, Cookie, Server)
	//check if we are looking for a files object otherwise uppercase gpcs
	$gpcs = ($type == 'FILES') ? "FILES" : strtoupper($gpcs);
	//determine which gpcs we are running
	switch($gpcs)
	{
		//GET
		case 'GET':
			//determine if the GCPS is set with the appropriate index
			if(isset($_GET[$index]))
			{
				//determine the value type
				switch($type)
				{
					//integer
					case 'INT':
						//get the value of the integer, making sure we escape to avoid
						//any XSS (Cross Site Scripting) attacks
						$value = (int) htmlentities($_GET[$index], ENT_QUOTES);
						break;
					case 'BOOLEAN':
						//get the value of the boolean, making sure we escape to avoid
						//any XSS (Cross Site Scripting) attacks
						$value = (htmlentities($_GET[$index], ENT_QUOTES)) ? 1 : 0;
						break;
					case 'STRING':
						//get the value of the string, making sure we escape to avoid
						//any XSS (Cross Site Scripting) attacks
						$value = (string) htmlentities($_GET[$index], ENT_QUOTES);
						//trim the string
						$value = trim($value);
						break;
				}

			}

			break;
		//POST
		case 'POST':
			//determine if the GCPS is set with the appropriate index
			if(isset($_POST[$index]))
			{
				//determine the value type
				switch($type)
				{
					//integer
					case 'INT':
						//get the value of the integer, making sure we escape to avoid
						//any XSS (Cross Site Scripting) attacks
						$value = (int) htmlentities($_POST[$index], ENT_QUOTES);
						break;
					case 'BOOLEAN':
						//get the value of the boolean, making sure we escape to avoid
						//any XSS (Cross Site Scripting) attacks
						$value = (htmlentities($_POST[$index], ENT_QUOTES)) ? 1 : 0;
						break;
					case 'STRING':
						//get the value of the string, making sure we escape to avoid
						//any XSS (Cross Site Scripting) attacks
						$value = (string) htmlentities($_POST[$index], ENT_QUOTES);
						//trim the string
						$value = trim($value);
						break;
				}

			}
			break;
		//REQUEST
		case 'REQUEST':
			//determine if the GCPS is set with the appropriate index
			if(isset($_REQUEST[$index]))
			{
				//determine the value type
				switch($type)
				{
					//integer
					case 'INT':
						//get the value of the integer, making sure we escape to avoid
						//any XSS (Cross Site Scripting) attacks
						$value = (int) htmlentities($_REQUEST[$index], ENT_QUOTES);
						break;
					case 'BOOLEAN':
						//get the value of the boolean, making sure we escape to avoid
						//any XSS (Cross Site Scripting) attacks
						$value = (htmlentities($_REQUEST[$index], ENT_QUOTES)) ? 1 : 0;
						break;
					case 'STRING':
						//get the value of the string, making sure we escape to avoid
						//any XSS (Cross Site Scripting) attacks
						$value = (string) htmlentities($_REQUEST[$index], ENT_QUOTES);
						//trim the string
						$value = trim($value);
						break;
				}

			}
			break;
		//COOKIE
		case 'COOKIE':
			//determine if the GCPS is set with the appropriate index
			if(isset($_COOKIE[$index]))
			{
				//determine the value type
				switch($type)
				{
					//integer
					case 'INT':
						//get the value of the integer, making sure we escape to avoid
						//any XSS (Cross Site Scripting) attacks
						$value = (int) htmlentities($_COOKIE[$index], ENT_QUOTES);
						break;
					case 'BOOLEAN':
						//get the value of the boolean, making sure we escape to avoid
						//any XSS (Cross Site Scripting) attacks
						$value = (htmlentities($_COOKIE[$index], ENT_QUOTES)) ? 1 : 0;
						break;
					case 'STRING':
						//get the value of the string, making sure we escape to avoid
						//any XSS (Cross Site Scripting) attacks
						$value = (string) htmlentities($_COOKIE[$index], ENT_QUOTES);
						//trim the string
						$value = trim($value);
						break;
				}

			}
			break;
		//SERVER
		case 'SERVER':
			//determine if the GCPS is set with the appropriate index
			if(isset($_SERVER[$index]))
			{
				//get the value of the server index
				$value = $_SERVER[$index];
			}
			break;
		//SESSION
		case 'SESSION':
			//determine if the GCPS is set with the appropriate index
			if(isset($_SESSION[$index]))
			{
				//determine the value type
				switch($type)
				{
					//integer
					case 'INT':
						//get the value of the integer, making sure we escape to avoid
						//any XSS (Cross Site Scripting) attacks
						$value = (int) htmlentities($_SESSION[$index], ENT_QUOTES);
						break;
					case 'BOOLEAN':
						//get the value of the boolean, making sure we escape to avoid
						//any XSS (Cross Site Scripting) attacks
						$value = (htmlentities($_SESSION[$index], ENT_QUOTES)) ? 1 : 0;
						break;
					case 'STRING':
						//get the value of the string, making sure we escape to avoid
						//any XSS (Cross Site Scripting) attacks
						$value = (string) htmlentities($_SESSION[$index], ENT_QUOTES);
						//trim the string
						$value = trim($value);
						break;
				}

			}
			break;
		//FILES
		case 'FILES':
			//check to see if the $_FILES array is set.
			if(isset($_FILES[$index]))
			{
				//check that the user has provided a maximum file size
				if (!isset($_REQUEST['MAX_FILE_SIZE']))
				{
					//return false as we cannot check the size of the file
					throw new Exception("Please specify a MAX_FILE_SIZE for your upload form");	

				}
				//check that the user has provided a maximum file size
				if (!isset($_REQUEST['ALLOWED_FILE_TYPES']))
				{
					//return false as we cannot check the size of the file
					throw new Exception("Please specify an ALLOWED_FILE_TYPES for your upload form");		

				}
				//get the maximum file size but sanitise the value
				$maxFileSize = htmlentities($_REQUEST['MAX_FILE_SIZE'], ENT_QUOTES);
				$allowedFileTypes = htmlentities($_REQUEST['ALLOWED_FILE_TYPES'], ENT_QUOTES);
				//get the allowed file types
				$validFileTypes = explode(',', $allowedFileTypes);
				//we count how many files have been uploaded
				$fileCount = count($_FILES[$index]['tmp_name']);
				//if we only have one file
				if ($fileCount == 1)
				{
					//first we determine if the file has been uploaded via http post
					if (!is_uploaded_file($_FILES[$index]['tmp_name']))
					{
						//return false as file was not posted
						throw new Exception("A valid file upload was not found");	

					}
					//next we check that the size is what we are expecting
					else if ($_FILES[$index]['size'] >= $maxFileSize)
					{
						//return false as there is a file that is greater than the maximum size
						throw new Exception("The size of your file exceeds the maximum file size of " . round($_FILES[$index]['size'] / $maxFileSize, 2) . " MB");

					}
					//file does not exist
					else if (!file_exists($_FILES[$index]['tmp_name']))
					{
						//the file does not exist so cannot be referenced
						throw new Exception("A file was not found on upload");

					}
					else if (!in_array($_FILES[$index]['type'], $validFileTypes))
					{
						//the file does not exist so cannot be referenced
						throw new Exception("An invalid file type was found, valid file types are " . implode(", ", $validFileTypes));

					}
					else if($_FILES[$index]['error'] != UPLOAD_ERR_OK)
					{
						//there was a problem uploading the image
						throw new Exception("There was an error whilst uploading your file");	

					}

				}
				else
				{
					//initialise counter
					//loop through each file
					for($i = 0; $i <= ($fileCount-1); $i++)
					{

						//first we determine if the file has been uploaded via http post
						if (!is_uploaded_file($_FILES[$index]['tmp_name'][$i]))
						{
							//return false as file was not posted
							throw new Exception("A valid file upload was not found");	

						}

						//next we check that the size is what we are expecting
						if ((int)$_FILES[$index]['size'][$i] > (int)$maxFileSize)
						{
							//return false as there is a file that is greater than the maximum size
							throw new Exception("The size of your file exceeds the maximum file size of " . round($_FILES[$index]['size'] / $maxFileSize, 2) . " MB");

						}
						else if (!file_exists($_FILES[$index]['tmp_name'][$i]))
						{
							//the file does not exist so cannot be referenced
							throw new Exception("A file was not found on upload");

						}
						else if (!in_array($_FILES[$index]['type'][$i], $validFileTypes))
						{
							//the file does not exist so cannot be referenced
							throw new Exception("An invalid file type was found, valid file types are " . implode(", ", $validFileTypes));

						}
						else if($_FILES[$index]['error'][$i] != UPLOAD_ERR_OK)
						{
							//there was a problem uploading the image
							throw new Exception("There was an error whilst uploading your file");	

						}

					}

				}

				//files have been posted and are not larger than the maximum file size
				return $_FILES;	

			}
			break;
		}
		//return the sanitised value
		return $value;
}

if(function_exists('dumpData') == false){
    /**
     * Helper function
     * 
     * @param array|object $row
     */
    function dumpData($row='#############################################'){
        echo "<pre>";
        print_r($row);
        echo "</pre>";
    }    
}
if(!function_exists('getWHMCSconfig2'))
{
    /**
     * Get WHMCSconfig
     * 
     * @param sring $k
     * @return sring|null
     */
    function getWHMCSconfig2($k) 
    {
        $q = PdoWrapper::query("SELECT value FROM tblconfiguration WHERE setting = ?", array($k));
        $ret=PdoWrapper::fetchAssoc($q);
        unset($q);
        
        if(isset($ret['value']))
        {
            return $ret['value'];
        }
    }
}
if(!function_exists('getWHMCSconfig'))
{
      /**
       * Get WHMCSconfig
       * 
       * @param string $k
       * @return type\
       */
    function getWHMCSconfig($k) 
    {
        $q = PdoWrapper::query("SELECT value FROM tblconfiguration WHERE setting = ?", array($k));
        $ret=PdoWrapper::fetchAssoc($q);
        unset($q);
        
        if(isset($ret['value']))
        {
            return $ret['value'];
        }
    }
}
if(!function_exists('saveWHMCSconfig'))
{
    /**
     * Save WHMCSconfig
     * @param string $k
     * @param string $v
     * @return boolean
     */
    function saveWHMCSconfig($k, $v) 
    {
        $q = PdoWrapper::query("SELECT `value` FROM tblconfiguration WHERE `setting` = ?",array($k));
        $ret=PdoWrapper::fetchAssoc($q);
        unset($q);
        
        if(isset($ret['value'])) 
        {
            return PdoWrapper::query("UPDATE tblconfiguration SET value = ? WHERE setting = ?",array( $v, $k));
        }
        else
        {
            return PdoWrapper::query("INSERT INTO tblconfiguration  (setting,value) VALUES (?,?)",array($k, $v));
        }
    }
}
if (!function_exists('onAppBillingIsActived')) {
    function onAppBillingIsActived() {
        static $actived;
        if(!empty($actived) && is_bool($actived))
            return $actived;
        
        $actived = file_exists(ROOTDIR . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'addons' . DIRECTORY_SEPARATOR . 'OnAppBilling' . DIRECTORY_SEPARATOR . 'core.php');
        if(!$actived)
            return $actived;
        
        if(!class_exists('onappWrapper\PdoWrapper'))
            include_once ROOTDIR . DS . 'includes' . DS . 'onappWrapper' . DS . 'PdoWrapper.php';
        
        $q = PdoWrapper::query("SELECT * FROM `tbladdonmodules` WHERE `module` = ? AND `setting`= ? AND `value` > ? ", array('OnAppBilling', 'access', '0'));
        return $actived = (boolean) PdoWrapper::numRows( $q );
    }
}
if (!function_exists('onAppFormatCurrency')) {
    function onAppFormatCurrency($amount) {
        $amount = formatCurrency($amount);
        if (is_string($amount)) {
            return $amount;
        } else if (\is_a($amount, '\WHMCS\View\Formatter\Price')) {
            return $amount->format();
        }
        throw new \Exception("Object '\WHMCS\View\Formatter\Price' does not exist!");
    }
}