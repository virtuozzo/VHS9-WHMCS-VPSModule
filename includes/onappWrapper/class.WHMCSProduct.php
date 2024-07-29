<?php

use onappWrapper\PdoWrapper;

/**
 * @author Grzegorz Draganik <grzegorz@modulesgarden.com>
 */

if (!class_exists('MG_WHMCS_Onapp_Product')){
	
	class MG_WHMCS_Onapp_Product {
		
		public $id;
		public $defaultConfig = array();
		protected $_tableName = 'mg_prodConfig';
		protected $_config    = null;
		
		public function __construct($id, array $params = array()){
			foreach ($params as $k => $v)
				$this->$k = $v;
			$this->id = (int)$id;
		}
		
		public function load(){
			$q = PdoWrapper::query('SELECT * FROM tblproducts WHERE id = ' . (int)$this->id);
			$row = PdoWrapper::fetchAssoc($q);
			if (!empty($row)){
				foreach ($row as $k => $v)
					$this->$k = $v;
			} else {
				throw new Exception('No product to load');
			}
		}
		
		public function setIdByServiceId($serviceid){
			$q = PdoWrapper::query('SELECT packageid FROM tblhosting WHERE id = ' . (int)$serviceid);
			$row = PdoWrapper::fetchAssoc($q);
			$this->id = (int)$row['packageid'];
		}
		
		public function update(array $values){
			$sets = array();
			foreach ($values as $k => $v){
				$v = is_numeric($v) ? $v : '"'.PdoWrapper::realEscapeString($v).'"';
				$sets[] = $k.'='.$v;
			}
			return PdoWrapper::query('UPDATE tblproducts SET '.implode(',',$sets).' WHERE id = ' . (int)$this->id);
		}

		public function hasConfigurableOptions(){
			$q = PdoWrapper::query('SELECT * FROM tblproductconfiglinks WHERE pid = ?', array($this->id));
			return (bool)PdoWrapper::numRows($q);
		}

		public function hasAssignedServerGroup(){
			$q = PdoWrapper::query('SELECT servergroup FROM tblproducts WHERE id = ?', array($this->id));
			$row = PdoWrapper::fetchAssoc($q);
			return isset($row['servergroup']) ? (int)$row['servergroup'] : false;
		}
		
		public function getParams(){
			$result = PdoWrapper::query("
				SELECT
					s.ipaddress AS serverip, s.hostname AS serverhostname, s.username AS serverusername, s.password AS serverpassword, s.secure AS serversecure,
					configoption1,configoption2,configoption3,configoption4,configoption5,configoption6,configoption7,configoption8,configoption9
				FROM tblservers AS s
				JOIN tblservergroupsrel AS sgr ON sgr.serverid = s.id
				JOIN tblservergroups AS sg ON sgr.groupid = sg.id
				JOIN tblproducts AS p ON p.servergroup = sg.id
				WHERE p.id = ?
				ORDER BY s.active DESC
				LIMIT 1",
				array($this->id)
			);
			$row = PdoWrapper::fetchAssoc($result);
			// old whmcs
			if (!function_exists('decrypt') && file_exists(ROOTDIR . DS . 'includes' . DS . 'functions.php'))
				include_once ROOTDIR . DS . 'includes' . DS . 'functions.php';
                                if(!empty($row['serverpassword']))
                                    $row['serverpassword'] = decrypt($row['serverpassword']);
                                
                           
			return $row;
		}
		
		// ========================================
		// ============ CUSTOM CONFIG =============
		// ========================================
		
		public function getConfig($name){
			$this->loadConfig();
			return isset($this->_config[$name]) ? $this->_config[$name] : null;
		}
		
		public function issetConfig($name){
			$this->loadConfig();
			return isset($this->_config[$name]);
		}

		public function loadConfig(){
                        $this->setupDbTable();
			if ($this->_config !== null)
				return $this->_config;

			$q = PdoWrapper::query('SELECT * FROM '.$this->_tableName.' WHERE product_id = ' . (int)$this->id);
			while ($row = PdoWrapper::fetchAssoc($q)){
                                if(json_decode($row['value'])!== NULL)
                                    $row['value'] = json_decode ($row['value']);
                                
				$this->_config[$row['setting']] = $row['value'];
			}
			return $this->_config;
		}

		public function saveConfig($name, $value){
			$this->setupDbTable();
                        if(is_array($value))
                            $value = json_encode($value);
			return PdoWrapper::query('INSERT INTO '.$this->_tableName.'(setting,product_id,value) VALUES(?,?,?) ON DUPLICATE KEY UPDATE value = ?', array(
				$name,
				(int)$this->id,
				($value=='-- not specified --' ? '' : $value),
				$value
			));
		}
		
		public function clearConfig(){
			return PdoWrapper::query('DELETE FROM '.$this->_tableName.' WHERE product_id = ' . (int)$this->id);
		}
		
		public function renderConfigOptions($scripts = ''){
			$scripts .= '
				<style type="text/css">
				td.configoption_group {background-color:silver;font-weight:bold;text-align:left;}
                                td.configoption_subgroup {background-color:#efefef;font-weight:bold;text-align:left;}
				.fieldlabel.mg, .fieldarea.mg {width:25%;}
				.fielddescription {font-size: 10px;color: gray;display: inline;}
				</style>
			';
                        
			$str = '';
			$options = array();
			$groups = array();
			$i = 0;
			foreach ($this->defaultConfig as $k => $config){
				// group html

                                if (substr($k,0,8)=='subgroup')
                                {
                                        $subgroups[$i] = $config;
                                        continue;;
                                }
				elseif (is_string($config)){
					$groups[$i] = $config;
					continue;
				}
				$options[] = '
					<td class="fieldlabel mg">'.$config['title'].'</td>
					<td class="fieldarea mg">
						'.$this->renderConfigOptionInput(
							$k,
							$config['type'],
							isset($config['default']) ? $config['default'] : '',
                            isset($config['options']) && !empty($config['options']) ? $config['options'] : array(),
							isset($config['useOptionsKeys']) && $config['useOptionsKeys']
						) . ($i == 0 ? $scripts : '') . '
						'.(isset($config['description']) ? '<div class="fielddescription">'.$config['description'].'</div>' : '').'
					</td>';
				$i++;
			}
			$countFields = 0;
			foreach ($options as $k => $option){
				if ($countFields == 0 && $k != 0)
					$str .= '<tr>';

				if (isset($groups[$k]) ){
					if ($countFields == 1)
						$str .= '<td></td><td></td>';
					$str .= '</tr><tr><td colspan="4" class="configoption_group">'.$groups[$k].'</td></tr><tr>';
					$countFields = 0;
				}
                                if (isset($subgroups[$k]) ){
                                    
					if ($countFields == 1)
						$str .= '<td></td><td></td>';
					$str .= '</tr><tr><td colspan="4" class="configoption_subgroup">'.$subgroups[$k].'</td></tr><tr>';
					$countFields = 0;
				}
				$str .= $option;
				
				$countFields++;
				if ($countFields == 2)
					$str .= '</tr>';
				if ($countFields > 1)
					$countFields = 0;
			}
			if ($countFields != 0)
				$str .= '</tr>';
			return $str;
		}
		
		public function renderConfigOptionInput($name, $type, $default, array $options = array(), $optionsValuesFromKeys = false){
			$value = $this->getConfig($name) ? $this->getConfig($name) : ($this->issetConfig($name) ? '' : $default);
			switch ($type){
				case 'multiselect':
					$str = '<select name="customconfigoption['.$name.'][]" multiple style="width:160px;">';
					foreach ($options as $k => $option){
						$str .= '<option value="'.($optionsValuesFromKeys ? $k : $option).'" '.(is_array($value) && in_array(($optionsValuesFromKeys ? $k : $option),$value) ? 'selected' : '').'>'.$option.'</option>';
					}
					$str .= '</select>';
					return $str;
					
				case 'select':
					$str = '<select name="customconfigoption['.$name.']" style="width:160px;">';
					foreach ($options as $k => $option){
						$str .= '<option value="'.($optionsValuesFromKeys ? $k : $option).'" '.($value == ($optionsValuesFromKeys ? $k : $option) ? 'selected' : '').'>'.$option.'</option>';
					}
					$str .= '</select>';
					return $str;
				
				case 'text':
					return '<input type="text" name="customconfigoption['.$name.']" style="width:150px;" value="'.$value.'" />';
					
				case 'textarea':
					return '<textarea name="customconfigoption['.$name.']" style="width:150px;">'.$value.'</textarea>';
					
				case 'radio':
					$str = '';
					foreach ($options as $option)
						$str .= '<input type="radio" name="customconfigoption['.$name.']" value="'.$option.'" /> ' . $option;
					return $str;
                                                                        
                                case 'checkbox': 
                                    return '<input type="checkbox"  name="customconfigoption['.$name.']" value="1"  '.($value ? ' checked="checked" ' : '').' /> '.$option;
                                    
				case 'checkbox_with_hidden':
					return '<input type="hidden" name="customconfigoption[' . $name . ']" value="0" />
					<input type="checkbox" '.$attributes.' name="customconfigoption[' . $name . ']" value="1"  ' . ($value ? ' checked="checked" ' : '') . ' /> ' . $option;


			}
			// NO CHECKBOX
			throw new Exception('Config Option type not supported');
		}
		
		public function setupDbTable(){
                        $res = PdoWrapper::query("SHOW TABLES LIKE '$this->_tableName' ");
                        if(PdoWrapper::numRows($res) == NULL)
                        {
                            PdoWrapper::query('CREATE TABLE IF NOT EXISTS `'.$this->_tableName.'` (
				`setting` varchar(100) NOT NULL,
				`product_id` int(10) unsigned NOT NULL,
				`value` text NOT NULL,
				PRIMARY KEY (`setting`,`product_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
                        }
                        PdoWrapper::query('ALTER TABLE  `'.$this->_tableName.'` CHANGE  `value`  `value` TEXT NOT NULL');
                        return true;
		}
		
	}
	
}

