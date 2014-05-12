<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Helpful {
	
	public $conn;
	
	public function __construct($conn){
		$this->conn = $conn;
	}
	
	public function init(){
		$json = '';
		if (isset($_POST['builderAjax'])){
			
			switch ($_POST['action']) {
				case 'loadParent':
					$query = str_replace('!!parent!!', $_POST['attrib1'], $_POST['attrib2']);
					$json = $this->simpleQuery($query);
					
					if (strlen($json) > 5) {
						echo $json;
					} else{
						echo json_encode(array("0"=>"0"));
					}
									
					break;
				default:
					break;
			}
			
			exit();
		}
	}
	
	private function simpleQuery($query){
		
		$result = pg_query($this->conn,$query);
		
		$arrAux = pg_fetch_all($result);
		
		$ArrJson = array();
		
		foreach ($arrAux AS $item){
			$ArrJson[$item['id']] = $item['desc'];
		}
		
		return json_encode($ArrJson);
	}	
	
	/**
	 * 
	 * Converte um xml em um Objeto DOM
	 *
	 * @author Rafael Dias <rafael.dias@sascar.com.br>
	 * @version 08/05/2014
	 * @param unknown_type $xml
	 * @return unknown
	 */
	public function convert($xml,$objReturn=null) {
		
		$nodeName = $xml->getName();	
		
		$componentes_aceitos = array('box','listBox','hiddenBox','inputText','optionBox','periodPicker');		
		
		if ($nodeName == 'dom') {
			$DOM = Dom::singleton();
			$DOM->main = $objInstance = new mainBox();			
			$this->setAttr($DOM, $xml);
		}		
		elseif ($nodeName == 'mainBox') {
			$objInstance = $objReturn;
		}		
		elseif ($nodeName == 'listBox') {
			$objInstance = new listBox('','','',$xml->attributes()->style);
			//if ($xml->children()->count() == 0) {
				$objInstance->dataset = $this->setOptions($xml);
			//}
		}
		elseif ($nodeName == 'inputText') {
			$objInstance = new inputText('','','',$xml->attributes()->style);
		}
		elseif ($nodeName == 'option'){
			//do nothing
			$objInstance = $objReturn;
		}
		elseif ($nodeName == 'button' || $nodeName == 'radioButton' || $nodeName == 'checkBox'){
			//do nothing
			$objInstance = $objReturn;
		}
		else{
			$objInstance = new $nodeName();
		}
		
		$i = 0;		
		foreach($xml->children() as $child) {
				
			$childName = $child->getName();
						
			$objChild = $this->convert($child,$objInstance);	
			
			if (is_object($objChild)) {
				if (in_array($childName, $componentes_aceitos)){
					$objInstance->setChild($objChild);
				}
			}

			$this->setAttr($objChild, $child);
			
			$i++;
		}
		
		if (is_object($DOM)){
			return $DOM;
		} elseif (is_object($objInstance)){
			return $objInstance;
		} else{
			return false;
		}
	}
		
	public function setAttr($obj, $node){
		foreach($node->attributes() as $attr){
			$attrName = $attr->getName();
			if ($attrName != 'style' && $attrName != 'dataset'){
				$obj->$attrName = $attr->__toString();
			}
		}
	}
	
	public function setAttrToArray($node){
		$arr = array();
		
		foreach($node->attributes() as $attr){
			$arr[$attr->getName()] = $attr->__toString();
		}
		
		return $arr;
	}
	
	public function setOptions($node){
		$arr = array();
		
		foreach($node->children() as $opt){
			//if ($opt->getName == 'option'){
				$arr[$opt->attributes()] = $opt;
			//}
		}
		
		return $arr;
	}
}
/* End of file Helpful.php */