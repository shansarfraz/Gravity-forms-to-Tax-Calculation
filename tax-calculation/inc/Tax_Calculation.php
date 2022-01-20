<?php 

//if(!class_exists('Tax_Calculation')){
class Tax_Calculation   {
	
	private $_fields_A = array('work_in_abroad','foreign_countries','limited_tax_liability','childrens','wife_husband','mortgage',
	'employes','elect_post','elect_auth','form_type');
	
	private $_fields_B = array('companies','incomes','form_type');
	
	private $field_values =  array();
	
	private $base_price_out =  34.9;
	
	private $base_price_local =  14.9;
	
	private $base_price_B =  26.9;
	
	private $per_country =  29.9;
	
	private $local_prices = array('childrens'=>5,'wife_husband' => 5,'mortgage'=>5,'elect_post'=>19.9);
	
	private $employes =  5;
	
	private $childrens =  5;
	
	private $wife_husband =  5;

	private $mortgage =  5;
	
	private $elect_post =  19.9;
	
	private $elect_auth =  14.9;
	
	
	private function set_fields($fields,$type){
		
		
		if($type=="A"):
		
			$keys = $this->_fields_A;
			
		else:
		
			$keys = array_merge($this->_fields_A,$this->_fields_B);
	
		endif;
		
		$counter = 0;
		foreach($keys as $key):
			//foreach($fields as $field):
				if(!empty($_POST['item_meta'][$fields[$key]])):
					$this->field_values[$keys[$counter]] = $_POST['item_meta'][$fields[$key]];
				endif;
			//endforeach;
			$counter++;
		endforeach;
		
	   return $this->field_values;
	
	}
	
	public function get_fields($fields,$type){
	
	  	return $this->set_fields($fields,$type);
 
	}
	
	private function set_price_abroad_1($auth,$send_doc){
		
		if( strtolower($auth)  == 'no'):
			$auth = 1;
		else:
			$auth = '';
		endif;
		
		if(   empty($auth) && empty($send_doc) ):
	
			return $this->base_price_out;
			
		elseif(empty($auth) && !empty($send_doc)):
			
			return ($this->base_price_out + $this->elect_post);	
		
		elseif(!empty($auth) && empty($send_doc)):
		
			return ($this->base_price_out + $this->elect_auth);
			
		elseif(!empty($auth) && !empty($send_doc)):	
		
			return ($this->base_price_out + $this->elect_auth + $this->elect_post);	
		
		endif;
	
	}
	
	private function set_price_abroad_2($countries,$auth,$send_doc){
		
		if( strtolower($auth)  == 'no'):
			$auth = 1;
		else:
			$auth = '';
		endif;
		
		if( !empty($countries) && empty($auth) && empty($send_doc) ):
	
			return ($countries * $this->per_country);
			
		elseif(!empty($countries) && empty($auth) && !empty($send_doc)):
			
			return (($countries * $this->per_country) + $this->elect_post);	
		
		elseif(!empty($countries) && !empty($auth) && empty($send_doc)):
		
			return (($countries * $this->per_country) + $this->elect_auth);
			
		elseif(!empty($countries) && !empty($auth) && !empty($send_doc)):	
		
			return (($countries * $this->per_country) + $this->elect_auth + $this->elect_post);	
		
		endif;
		
	
	}
	
	private function set_price_limited($auth,$send_doc){
		
		if( strtolower($auth)  == 'no'):
			$auth = 1;
		else:
			$auth = '';
		endif;
		
		if(   empty($auth) && empty($send_doc) ):
	
			return $this->base_price_out;
			
		elseif(empty($auth) && !empty($send_doc)):
			
			return ($this->base_price_out + $this->elect_post);	
		
		elseif(!empty($auth) && empty($send_doc)):
		
			return ($this->base_price_out + $this->elect_auth);
			
		elseif(!empty($auth) && !empty($send_doc)):	
		
			return ($this->base_price_out + $this->elect_auth + $this->elect_post);	
		
		endif;
	
	}
	
	private function single_calculate_pattern_auth($base,$field,$auth){
		
			return ( $base + $field + $auth);
		
		}
		
	private function single_calculate_pattern_doc($base,$field,$send_doc){
		
			return ( $base + $field + $send_doc);
		
		}
		
	private function single_calculate_pattern_all($base,$field='',$auth='',$send_doc=''){
		
			return ( $base + $field + $auth + $send_doc);
			
		}
		
	
	private function set_price_local($field,$type)	{
		
		if($type=='A'):
			$base = $this->base_price_local;
			$emp = $field['employes'];
		else:
		   
			$base = $this->base_price_B;
			$emp = count($field['incomes']) + $field['companies'];
		endif;
		
		
		if($emp > 2):
		
			$employes = $this->employes;
		
		else:
		
			$employes = '';
		
		endif;
		
		//if(preg_match('/\bno\b/',strtolower($field['elect_auth']))):

		if(strtolower($field['elect_auth']) =='no'):	
			$auth = $this->elect_auth;
		else:
			$auth = '';
		endif;
		
		if(!empty($field['elect_post'])):
			$send_doc = $this->elect_post;
		else:
			$send_doc = '';
		endif;
		
		
		
		foreach($field as $key=>$temp):
			
				if(!empty($this->local_prices[$key])):
				
					$values[] = $this->local_prices[$key];
				
				endif;
			
		endforeach;
		    
			if(!empty($values)):
				$total = array_sum($values);
			else:
				$total = '';
			endif;
			
			return ($base + $auth + $employes + $total);


	}

	public function get_prices($field,$types){
		
			if( !empty($field['work_in_abroad']) && !empty($field['foreign_countries']) && $field['foreign_countries'] == 1):
			
				return $this->set_price_abroad_1($field['elect_auth'],$field['elect_post']);
				
			elseif( !empty($field['work_in_abroad']) && !empty($field['foreign_countries']) && $field['foreign_countries'] > 1):
				
				return $this->set_price_abroad_2($field['foreign_countries'],
												$field['elect_auth'],
												$field['elect_post']
												,'foreign_countries');
				
			elseif(!empty($field['limited_tax_liability'])):
			
				return $this->set_price_limited($field['elect_auth'],$field['elect_post']);

			else:
			
				return $this->set_price_local($field,$types);
				
			endif;	
				
		}
		
	
	function price_table_field($lang){
		
		if($lang == 'eng'):
			return $field = array(
				'employe_1' => 'Employees and part time job',
				'employe_2' => 'Employees worked in 3 and more companies',
				'childrens' => 'Asking for tax bonus on child',
				'wife_husband' => 'Tax benefit for wife',
				'mortgage'   => 'Asking for tax bonus on interest from mortgage',
				'income_1' => 'Type B - slovak incomes',
				'income_2' => 'More kind of different incomes in Slovakia (3 and more)',
				'limited' => 'Forigners in Slovakia',
				'declaration' => 'Electronic sending of declaration',
				'authorization' => 'Creating of authorization',
				'countries_1' => 'working in abroad',
				'countries_2' => 'working in abroad - more countries(2 and more)-price for one country'
			);
		else:
			return $field = array(
				'employe_1' => 'Príjem zo zamestnania a dohody(brigády)',
				'employe_2' => 'Zamestnanci pracujúci v 3 a viac spoločnostiach',
				'childrens' => 'Uplatnenie daňového bonusu na deti',
				'wife_husband' => 'Uplatnenie zníženia daňového základu na manželku/a',
				'mortgage'=> 'Uplatnenie daňového bonusu na úroky z hypotéky',
				'income_1' => 'Slovenské príjmy zo zamestnania, živnosti, nájmu bytu a iné',
				'income_2' => 'Viac druhov rozličných príjmov zo Slovenska (3 a viac)',
				'limited' => 'Daňový nerezidenti (občania iného štátu)',
				'declaration' => 'Elektronické poslanie daňového priznania+ročný monitoring pošty',
				'authorization' => 'Vytvorenie autorizácie na elektronickú komunikáciu - jednorazový poplatok',
				'countries_1' => 'Príjem z práce zo zahraničia',
				'countries_2' => 'Príjem z práce zo zahraničia (z viacerých krajín - 2 a viac) - cena za každú krajinu'
			);
		endif;
		
	}
	
	function table_row_html($text,$quanity,$price){
		
			return '<tr>
				<td>'.$text.'</td>
				<td>'.$quanity.'</td>
				<td>'.$price.'€</td>
			</tr>';
		
		}
		
		
	function price_table_row($fields,$type,$lang){
		

		if($type=='A'):
			$base = $this->base_price_local;
			$emp = $fields['employes'];
		else:

		   
			$base = $this->base_price_B;
			$emp = count($fields['incomes']) + $fields['companies'];
		endif;
		
		 $field_text = $this->price_table_field($lang);
		 
		if(isset($fields['elect_post'])):
			$elec_post_row = $this->table_row_html($field_text['declaration'],1,$this->elect_post); 
		else:
		
			$elec_post_row = '';
								  
		endif;
		
		
		//if(isset($fields['elect_auth']) && preg_match('/\bno\b/', strtolower($fields['elect_auth']))):

		if(strtolower($fields['elect_auth']) =='no'):	
		
			$elec_auth_row = $this->table_row_html($field_text['authorization'],1,$this->elect_auth);
		else:
		
			$elec_auth_row = '';

								  
		endif;
			
		
		if(!empty($fields['foreign_countries']) && $fields['foreign_countries'] < 2):
		
			$out_row = $this->table_row_html($field_text['countries_1'],
										 $fields['foreign_countries'],
										 $this->base_price_out);
		
		elseif(!empty($fields['foreign_countries']) && $fields['foreign_countries'] > 1):
			
			$out_row = $this->table_row_html($field_text['countries_2'],
										 $fields['foreign_countries'],
										 $this->per_country * $fields['foreign_countries']);
										 
		elseif(!empty($fields['limited_tax_liability'])):
		
			$out_row = $this->table_row_html($field_text['limited'],
										 1,
										 $this->base_price_out);
										 
		else:
		
			$out_row = '';								 							 
		
		endif;
		

		if($emp < 3 && $type == 'A' && empty($out_row)):
		
			$emp_row_A = $this->table_row_html($field_text['employe_1'],
										$emp,
										 $base);
		
		elseif($emp > 2 && $type == 'A' && empty($out_row)):
			
			$emp_row_A = $this->table_row_html($field_text['employe_2'],
										 $emp,
										 $base + $this->employes);
										 
		else: 
		
			$emp_row_A = '';							 
			
		endif;
		
		
		if($emp < 3  && $type == 'B' && empty($out_row)):
		
			$emp_row_B = $this->table_row_html($field_text['income_1'],
										 $emp,
										 $base);
		
		elseif($emp > 2  && $type == 'B' && empty($out_row)):
			
			$emp_row_B = $this->table_row_html($field_text['income_2'],
										 $emp,
										 $base + $this->employes);
										 
		else:
		
			$emp_row_B = '';								 
			
		endif;
		
		if(isset($fields['childrens']) && empty($out_row)):
		
			$child_row = $this->table_row_html($field_text['childrens'],1,$this->childrens);
			
		else:
		
			$child_row = '';
			
		
		endif;
		
		if(isset($fields['wife_husband']) && empty($out_row)):
		
			$wife_row = $this->table_row_html($field_text['wife_husband'],1,$this->wife_husband);
			
		else:
		
			$wife_row = '';
			
		
		endif;

		if(isset($fields['mortgage']) && empty($out_row)):

			$mortgage_row = $this->table_row_html($field_text['mortgage'],1,$this->mortgage);

		else:
		
			$mortgage_row = '';
			
		
		endif;
	
	
	    return $out_row.$emp_row_A.$emp_row_B.$child_row.$wife_row.$mortgage_row.$elec_post_row.$elec_auth_row;

		//return $elec_auth_row;

		}	
		
			
	function price_table($fields,$type,$lang){

		

		 if ($lang == 'eng') {
			$labels = array('field' =>'Field' , 'quantity' =>'Quantity' ,'price' =>'Price','total'=>'Total');
		} else {
			$labels = array('field' =>'Položka' , 'quantity' =>'Množstvo' ,'price' =>'Cena','total'=>'Cena spolu');
		}
		
		 
	return array('footer'=>'<div id="price-terms"> 
		<table class="table table-bordered">
		 <thead>
                <tr>
                    <th>'.$labels['field'].'</th>
                    <th>'.$labels['quantity'].'</th>
                    <th>'.$labels['price'].'</th>
                </tr>
            </thead>
			<tbody>'.$this->price_table_row($fields,$type,$lang).'</tbody>
		</table>
		<table class="table table-bordered" style="width:50%; float:right;">
        	<thead>
                <tr>
                    <th>Total</th>
                    <th>'.$this->get_prices($fields,$type).'€</th>
                </tr>
            </thead>
        </table>
		</div><script type="text/javascript">		
			var htmlString  = jQuery(\'#price-terms\').html();
			var htmlEmail = jQuery(\'#email_table\').html();
			jQuery(".price-table").html(htmlString);
			jQuery(".price-email").find("textarea").attr("data-frmval",htmlEmail);
		</script>',
		'email'=>	
		'<table style="width:100%; border: 1px solid; font-weight:bold; text-align:center;">
		 <thead>
                <tr>
                    <th>Field</th>
                    <th>Quantity</th>
                    <th>Price</th>
                </tr>
            </thead>
			<tbody>'.$this->price_table_row($fields,$type,$lang).'</tbody>
		</table>
		<table style="width:50%; border: 1px solid; font-weight:bold; float:right;">
        	<thead>
                <tr>
                    <th>'.$labels['price'].'</th>
                    <th>'.$this->get_prices($fields,$type).'€</th>
                </tr>
            </thead>
        </table>'
		);
		
}
}