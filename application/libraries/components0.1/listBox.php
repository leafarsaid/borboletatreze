<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once('Component.php');
//require_once 'helper/Helpful.php';

class listBox extends Component {
	
	public function __construct($label=null, $id=null, $init=null, $style=null, $dataset=null){
		$this->label = $label;
		$this->id = $id;
		$this->init = $init;
		$this->dataset = $dataset;
		switch ($style){
			case 'i':
				$this->style = 'campo menor';
				break;
			case 'ii':
				$this->style = 'campo medio';
				break;
			case 'iii':
				$this->style = 'campo maior';
				break;
			default:
				$this->style = 'campo medio';
				break;
		}
	}
	
	public function loadParent($parentId){
		if (!is_array($this->dataset) && strlen($this->dataset) > 5){				
			
			$query = json_encode($this->dataset);
			
			//$query = "SELECT cidoid AS id,ciddescricao AS desc FROM cidade WHERE ciduf ilike 'PR' ORDER BY ciddescricao";
			
			$phpSelfAux = explode('/', $_SERVER['SCRIPT_NAME']);						
			$phpSelf = $phpSelfAux[count($phpSelfAux)-1];
			
			$thisId = $this->id;
			
$this->script = <<<SCRIPT
<script language="javascript">
	jQuery('#$parentId').change(function(){
	
		var valueParent = jQuery('#$parentId').val();
				
		if ( console && console.log ) {
			console.log( "Valor do campo parent ($parentId):" + valueParent);
		}
		
		if (valueParent == null) {
			valueParent = 'NO';
		}
	
		jQuery.ajax({
				async: false,
	            url: '$phpSelf',
	            type: 'post',
	            data: { builderAjax:"1", action:"loadParent", attrib1: valueParent, attrib2: $query },
	            beforeSend: function(){
	                //jQuery('#loading').html('<img src="images/loading.gif" alt="" />');               
	            },
	            success: function(data){
	                try {
	
	                    var result = jQuery.parseJSON(data);
	 
						var select = $('#$thisId');
						if(select.prop) {
						  var options = select.prop('options');
						}
						else {
						  var options = select.attr('options');
						}
						$('option', select).remove();
						
						options[options.length] = new Option('Escolha', '');
						 
						$.each(result, function(val, text) {
						    options[options.length] = new Option(text, val);
						});
	
	                    /*if ( console && console.log ) {
							console.log( "teste:" );
	            			console.log( data );
						}*/
	
	                } catch (e) {
	                    alert(e.message);
	                }
	            }
	        });   
		});
</script>
SCRIPT;

		}
	}
	
	public function toString(){
		$path = '';
		$script = '';
		if (!is_array($this->dataset) && strlen($this->dataset) > 5 && strlen($this->script) > 1){
			$script = $this->script;
		}
		
		$options = '';		 
		if (is_array($this->dataset) && count($this->dataset)>0){
			foreach($this->dataset AS $value => $text) {
				$selected = '';
				if ($value == $this->init) {
					$selected = 'selected';
				}
				$options .= "<option value=\"$value\" $selected>$text</option>\n";
			}
		}
		 
		/**
		 HTML do componente
		 */
		$label = utf8_decode($this->label);
		
		$html = <<<CMP
$script
<div class="$this->style">
	<label for="$this->id">$label</label>
	<select id="$this->id" name="$this->id">
		<option value="">Escolha</option>
		$options
	</select>
</div>
CMP;
		
		return $html;
	}
}