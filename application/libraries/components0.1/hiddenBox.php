<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once('Component.php');

class hiddenBox extends Component {
	
	public function __construct($label=null){
		$this->label = $label;
	}
	
	public function toString(){
		
		$none = ($this->label == '') ? ' style="display:none"' : '';
		
		/**
		 HTML do componente
		 */
		$html1 = <<<CMP1
<div class="blocoOculto" style="float:left">
	<div class="blocoOculto_titulo"$none>$this->label</div>
	<div class="blocoOculto_conteudo">
CMP1;
    
    	$html2 = <<<CMP2
	</div>
    <br />    
</div>
CMP2;
		
		$return = $html1;
		
		foreach($this->childs AS $item){
			$return .= $item->toString();
		}
		
		$return .= $html2;
		
		return $return;
	}
}