<?php
	//define('BASEPATH','WHATEVER'); //used so i dont have to edit CI libraries.
	//include("lib/form_helper.php");
 	error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
 	include '../formBuilder/lib/form_helper.php';
	$fb = new Formbuilder;
	
	$action = (isset($_GET['action'])) ? $_GET['action'] : null;
	
	switch ($action) {
		case 'properties':
			$fb->properties($_GET);
			break;
		case 'element':
			$fb->element($_GET);
			break;
		case 'element_display':
			$fb->element_display($_GET);
			break;	
		default:
			break;
	}
	
	/**
	* Filemanager
	*/
	class Formbuilder
	{
		
		/*
		Build is used to output the forms data as a html form
		$data is an array generated on post from the builder.
		*/
		function build($data)
		{
			if (!isset($data['properties'])) return false;
			$elements = $data['properties'];
			
			foreach ($elements as $k => $val)
			{
				if (!isset($data[$k])) $data[$k] = NULL;
				if (!isset($val['values'])) $val['values'] = NULL;
				
				$elements[$k]['content'] = $data[$k];
				
				$name = $k;
				
				switch ($val['type'])
				{
					case 'text': 
						 $contents=html_entity_decode(urldecode($data[$k]['text_val']));
						 //#$contents = str_replace(","," ",$contents);
						 $elements[$k]['html'] =$contents;
						 break;
					case 'textarea':
						$elements[$k]['html'] = form_textarea(array(
							'name' => $name,
							'rows' => 5,
							'cols' => 50,
							'value' => $data[$k],
							'class' => ((isset($val['required']) && $val['required']==0)?'required'.((isset($val['required_vars']))?'{'.$val['required_vars'].'}':null):null)
						)); 
						break;
					case 'textbox': 
						$elements[$k]['html'] = form_input(array(
							'name'=>$name,
							'value'=>$data[$k],
							'class' => ((isset($val['required']) && $val['required']==0)?'required'.(((isset($val['required_vars']) && $val['required_vars']!=""))?'{'.$val['required_vars'].'}':null):null)
						)).'<span id='.$name.' style="color:red;"></span>';
						break;
					case 'dropdown': 
							$cls='';
							if(isset($val['required']) && $val['required']==0)
							{
							$cls='class="required"';
							}
					
						if (!$val['values']) { $options = array(""=>'No Content');/*unset($elements[$k]); break;*/ }
						$options = explode(';',$val['values']);
						$options=array_merge(array('0'=>'Please Select'),$options);
						if (empty($options)) { unset($elements[$k]); break; }
						
						$elements[$k]['html'] = form_dropdown($name,$options,'',$cls); 
						break;
					case 'checkbox':
						$input = null;
							$cls='';
							if(isset($val['required']) && $val['required']==0)
							{
							$cls='class="required"';
							}
						if (!$val['values']) { $options = array(''=>'');/*unset($elements[$k]); break;*/ }
						$options = explode(';',$val['values']);
						if (empty($options)) { unset($elements[$k]); break; }
						
						foreach ($options as $option) {
							$input .= form_checkbox($name.'[]', $option).' '.$option.'<br/>';
						}
						$elements[$k]['html'] = $input; 
						break;
					case 'radio':
						$input = null;
						$cls='';
							if(isset($val['required']) && $val['required']==0)
							{
							$cls='class="required"';
							}
						if (!$val['values']) { $options = array(''=>'');/*unset($elements[$k]); break;*/ }
						$options = explode(';',$val['values']);
						if (empty($options)) { unset($elements[$k]); break; }
						
						foreach ($options as $option) {
							$input .= form_radio($name.'[]', $option).' '.$option.'<br/>';
						}
						$elements[$k]['html'] = $input; 
						break;
					case 'datetime':
						$elements[$k]['html'] = form_input(array(
							'name'=>$name,
							'value'=>$data[$k],
							'class' => 'datepicker '.((isset($val['required']) && $val['required']==0)?'required'.((isset($val['required_vars']))?'{'.$val['required_vars'].'}':null):null)
						));
						break;
					case 'fileupload':
						$elements[$k]['html'] = form_upload(array(
							'name'=>$name,
							'class' => ((isset($val['required']) && $val['required']==0)?'required'.((isset($val['required_vars']))?'{'.$val['required_vars'].'}':null):null)
						));
						break;
					case 'button':
						$elements[$k]['html'] = form_input(array(
							'name'=>$name,
							'value'=>((isset($val['value']))?$val['value']:'Button'),
							'type'=>'button',
							'class'=>'button'
						));
						break;
				}
			}
			
			return $elements;
		}
		
		/*
		*/
		function element($attr)
		{
			
			$lable_class='';
			$valuessssss='';
			$required='1';
			$no_load_more='1';
	    	$required_vars='';
	    	$description='';
			if(isset($attr['id']) && $attr['id']!='')
				$id=$attr['id'];
			else
				$id = 'element_'.uniqid();
				
			$dropdown_val=array(''=>'No Content');
			$btn_value='No Content';
			//if(isset($attr['required']) && $attr['required']!='')
			if(isset($attr['no_load_prev']) && $attr['no_load_prev']!='')
			{
				$no_load_more=$attr['no_load_prev'];
			}
			if(isset($attr['required']) && $attr['required']!='')
			{
				$required=$attr['required'];
			}
			if(isset($attr['required_vars']) && $attr['required_vars']!='')
			{
				$required_vars=$attr['required_vars'];
			}
			if(isset($attr['description']) && $attr['description']!='')
			{
				$description=$attr['description'];
			}
			
			if(isset($attr['values']) && $attr['values']!='')
			{	
				$attr['values']=str_replace('&amp;', '&', $attr['values']);
				$dropdown_val1=explode(';',$attr['values']);
				/*
				$dropdown_val=array_merge(array('0'=>'Please Select'),$dropdown_val1);*/
				if($attr['type'] == "dropdown"){
					$element_select = array();
					foreach ($dropdown_val1 as $va){
						$element_select[]=str_replace(',', '&#44;', $va);
					}
					$dropdown_val=array_merge(array('0'=>'Please Select'),$element_select);
				}	
				else{	
					$dropdown_val=$dropdown_val1;
				}	
				$pro=true;
				$valuessssss="<input class='values' type='hidden' name='properties[".$id."][values]' id='".$id."_values' value='".str_replace(',', '&#44;',$attr['values'])."' />";
			}
			
			if(isset($attr['value']) && $attr['value']!='')
			{
				$btn_value=$attr['value'];
				$pro=true;
				$valuessssss="<input class='value' type='hidden' name='properties[".$id."][value]' id='".$id."_value' value='".$attr['value']."' />";
			}
			
			$element_ck='<input type="checkbox" name="temp[values][]" >';
			if($attr['type']=='checkbox')
			{
				if(isset($attr['values']) && $attr['values']!='')
				{
					$element_chk='';
					foreach ($dropdown_val as $va)
					{
						if($element_chk=='')
					 		$element_chk='<input type="checkbox" name="temp[values][]" >'.str_replace(',', '&#44;', $va).'<br>';
						else 
							$element_chk.='<input type="checkbox" name="temp[values][]" >'.str_replace(',', '&#44;', $va).'<br>';
					}
				if($element_chk!='')
				$element_ck=$element_chk;
				}	
			}
		$element_ra='<input type="radio">';
			if($attr['type']=='radio')
			{
				if(isset($attr['values']) && $attr['values']!='')
				{
					$element_rad='';
					foreach ($dropdown_val as $va)
					{
						if($element_rad=='')
					 		$element_rad='<input type="radio" name="temp[values][]">'.str_replace(',', '&#44;', $va).'<br/>';
						else 
							$element_rad.='<input type="radio" name="temp[values][]">'.str_replace(',', '&#44;', $va).'<br/>';
					}
				if($element_rad!='')
				$element_ra=$element_rad;
				}	
			}
			$contents='';
			if(isset($attr['edit']) && $attr['edit']!='')
			{
				
				if(isset($attr['text_val']) && $attr['text_val']!="")
					$contents=html_entity_decode(str_replace("\n","<br>",$attr['text_val']));//$attr['text_val'];
				
				
				switch($attr['type'])
				{
					case 'text': 
						/*$element = form_textarea(array(
							'class' => 'wysiwyg',
							'id' => $id,
							'name' => $id,
							'rows' => 5,
							'cols' => 40,
							
							
						),$contents);*/
						$lable_class='text_lable_class';
						break;
					case 'textarea':
						$element = form_textarea(array(
							'name' => $id,
							'rows' => 5,
							'cols' => 40,
							'class'=>'wysiwygNoEditor'
							
						)); 
						break;
					case 'textbox': $element = form_input(array('name'=>$id,'class'=>'jf_text form_role_input')); break;
					case 'dropdown': $element = form_dropdown($id,$dropdown_val); break;
					case 'checkbox': $element = '<span class="values '.$id.'" style="display:inline-block;">'.$element_ck.'</span>'; break;
					case 'radio': $element = '<span class="values '.$id.'" style="display:inline-block;">'.$element_ra.'</span>'; break;
					case 'datetime': $element = form_input(array('name'=>$id,'class'=>'datepicker jf_text form_role_input')); break;
					case 'fileupload': $element = form_upload($id); break;
					case 'button': $element = form_input(array('name'=>$id,'value'=>$btn_value,'type'=>'button')); break;
					default: $element = null; break;
				}
			}
			else
			{
			switch($attr['type'])
				{
					case 'text':
						/*$element = form_textarea(array(
							'class' => 'wysiwyg',
							'id' => $id,
							'name' => $id,
							'rows' => 5,
							'cols' => 40,
							'style'=>'display:none;'
							
						));*/ 
						$lable_class='text_lable_class';
						break;
					case 'textarea':
						$element = form_textarea(array(
							'name' => $id,
							'rows' => 5,
							'cols' => 40,
							'class'=>'wysiwygNoEditor'
							
						)); 
						break;
					case 'textbox': $element = form_input(array('name'=>$id,'class'=>'jf_text')); break;
					case 'dropdown': $element = form_dropdown($id,$dropdown_val); break;
					case 'checkbox': $element = '<span class="values '.$id.'" style="display:inline-block;">'.$element_ck.'</span>'; break;
					case 'radio': $element = '<span class="values '.$id.'" style="display:inline-block;">'.$element_ra.'</span>'; break;
					case 'datetime': $element = form_input(array('name'=>$id,'class'=>'datepicker jf_text')); break;
					case 'fileupload': $element = form_upload($id); break;
					case 'button': $element = form_input(array('name'=>$id,'value'=>$btn_value,'type'=>'button')); break;
					default: $element = null; break;
				}
			}
			//give the text box a differnt label
			if(isset($attr['label']) && $attr['label']!="")
			{
				if($attr['type'] == 'text')
				$label=$contents;
				else
				$label=$attr['label'];
				$pro=true;
			}
			else
			{
				
					$label = ($attr['type'] == 'text') ? 'No Text Heading' : 'No Label';
			}
			if($pro)
			{
			//$this->properties($attr);
			}
			//basic output list element.
			
					$text_val="";
					$order=microtime(true);
					$order=str_replace(".","",$order);
			if(isset($attr['edit']) && $attr['edit']!='')
			{
				if(isset($attr['order']) && $attr['order']!='')
					$order=$attr['order'];
					
				if(isset($contents) && $contents!='')
				{
						$contents=html_entity_decode(urldecode($contents));
						$label = str_replace(",","",$contents);
				}
				$chk='';
				if($required==0)
					$chk='checked=checked';
					
				$chknoloadpre='';
				if($no_load_more==1){
					$chknoloadpre='checked=checked';	
				}
				$remove[] = "'";
				$remove[] = '"';
				$remove[] = "-"; 
				$label = str_replace( $remove, "", $label );
				$output = "
					<li data-id='".$id."'>
					<label for='".$id."' class='".$lable_class."'><a href='javascript:void(0);' id='".$id."' onclick=updateall(); title='Edit' style='cursor: default !important;'>".$label."</a></label>
						<div class='block'>
							<div class='handle'>
										<a href='javascript:void(0);' title='Move' class='toolbox'><span class='icon move'>&nbsp;</span></a> 
										<a href='javascript:void(0);' class='properties toolbox' title='Edit' rel='".$attr['type']."'>
												<span class='icon edit' for='".$id."'>&nbsp;<span style='display:none'>".$label."</span></span>
										</a>
							</div>
							".$element."
							<span class='note ".$id."'>$description</span>
						</div>
						<div class='clear'></div>
						<div class='attrs clear ".$id."'>
							<input type='hidden' name='properties[".$id."][type]' value='".$attr['type']."'/>
							<input type='hidden' name='properties[".$id."][order]' value='".$order."'/>";
							if(isset($contents) && $contents!='')
							{
							$output .= "<input type='hidden' name='properties[".$id."][label]' class='label' value='' />".$valuessssss;
							}else
							{
							$output .="<input type='hidden' name='properties[".$id."][label]' class='label' value='".$label."' />".$valuessssss;
							}
							
							$output .="<input type='hidden' name='properties[".$id."][required]' class='required' value='".$required."' id='required_".$id."' ".$chk."  / >
							<input type='hidden' name='properties[".$id."][no_load_prev]' value='".$no_load_more."' id='no_load_prev_".$id."' ".$chknoloadpre."  / >
							<input type='hidden' name='properties[".$id."][required_vars]'  class='required_vars' value='".$required_vars."' />
							<input type='hidden' name='properties[".$id."][description]' id='description_".$id."' class='description' value='".$description."'>
							<input type='hidden' name='properties[".$id."][text_val]' id='".$id."_text_val' class='text_val' value='".urlencode(htmlentities($contents))."'>							
						</div>
					</li>
				";
			}
			else
			{
			$output = "
				<li data-id='".$id."'>
				<label for='".$id."' class='".$lable_class."'><a href='javascript:void(0);' id='".$id."' title='Edit'>".$label."</a></label>
					<div class='block'>
						<div class='handle'>
							<a href='javascript:void(0);' title='Move' class='toolbox'><span class='icon move'>&nbsp;</span></a>
							<a href='javascript:void(0);' onclick=$('".$id."').trigger('click'); class='properties toolbox' title='Edit' rel='".$attr['type']."'><span class='icon edit' for='".$id."'>&nbsp;<span style='display:none'>".$label."</span></span>
							</a>
						</div>
						".$element."
						<span class='note ".$id."'></span>
					</div>
					<div class='clear'></div>
					<div class='attrs clear ".$id."'>
						<input type='hidden' name='properties[".$id."][type]' value='".$attr['type']."'/>
						<input type='hidden' name='properties[".$id."][order]' value='".$order."'/>
						<input type='hidden' name='properties[".$id."][text_val]' id='".$id."_text_val' class='text_val' value='".$contents."'>
						<input type='hidden' id='required_".$id."' name='properties[".$id."][required]' class='required' value='1'>
						<input type='hidden' name='properties[".$id."][no_load_prev]' value='0' id='no_load_prev_".$id."'  / >						
					</div>
				</li>
			";
			}
			//if ($element) {
				//set output to AJAX
				usleep(1000000);
				if(isset($attr['edit']) && $attr['edit']!='')
				echo $output.'^'.$id;
				else
				echo $output;
				
			//}
			
		}
		
		/*
		Builds a list of properties for the builder to display.
		*/
		function properties($attr)
		{
			$output = null;
			/*ECHO "<PRE>";
			PRINT_R($attr);
			DIE;*/
			$type = $attr['type'];
			$id = $attr['id'];
			$label='';
			if(isset($attr['label']) && $attr['label']!="No Label")
			{
				$label=str_replace("<br>","\n",$attr['label']);
				
			}
			//$dropdown_val=array(''=>'No Content');
			$btn_value=' ';
			if(isset($attr['values']) && $attr['values']!='')
			{
				if(is_array($attr['values']))
				{
					$dropdown_val1=explode(';',$attr['values']);
					if($attr['type'] == "dropdown")
						$dropdown_val=array_merge(array('0'=>'Please Select'),$dropdown_val1);
					else	
						$dropdown_val=$dropdown_val1;
				}	
				else 
				$dropdown_val=$attr['values'];
			}
			if(isset($attr['value']) && $attr['value']!='')
			{
				$btn_value=$attr['value'];
			}				
			
			//print_r($dropdown_val);//die;
			//print_r($attr);//die;
			//basic options
			$chk='';$chk1='';
			if($attr['req']==0)
				$chk='checked=checked';
			if($attr['req'] == 'undefined')
				$chk='';
			
			if($attr['no_load_prev']==1)
				$chk1='checked=checked';
			if($attr['no_load_prev'] == 'undefined')
				$chk1='';
				
				if($attr['type']=='textbox')
				{	
			$options = array(
				'Label' => form_input(array('rel'=>'label[for='.$id.'] a','name'=>'label'),$label),
				'Required' => array(
					'Yes' => '<input type="checkbox" value="1" name="required" onclick="if(this.checked) this.value=0; else this.value=1;" '.$chk.' >',//form_checkbox('required','1'),
					'Type' => form_dropdown('required_vars',array(''=>'Text','email'=>'Email','number'=>'Number'))
				),
				'No Load Previous' => array(
					'' => '<input type="checkbox" value="0" name="no_load_prev" onclick="if(this.checked) this.value=1; else this.value=0;" '.$chk1.' title="Do not copy field contents during Load Previous process when submitting new projects." >',//form_checkbox('required','1'),
				),
				'Description' => form_input(array('name'=>'description','rel'=>'.note[class~='.$id.']')),
			);
				
				}
				if($attr['type']=='text')
				{
						$element = form_textarea(array(
									'class' => 'wysiwyg',
									'id' => $id.'_text',
									'name' => $id,
									'rows' => 5,
									'cols' => 40,
								),$attr['label']);
						$options=array(
							'Add Text'=>$element			
						);
				}
				else
				{
				$options = array(
				'Label' => form_input(array('rel'=>'label[for='.$id.'] a','name'=>'label'),$label),
				'Required' => array(
					'' => '<input type="checkbox" value="1" name="required" onclick="if(this.checked) this.value=0; else this.value=1;" '.$chk.' >',//form_checkbox('required','1'),
					//'Type' => form_dropdown('required_vars',array(''=>'Text','email'=>'Email','number'=>'Number'))
				),
				'No Load Previous' => array(
					'' => '<input type="checkbox" value="0" name="no_load_prev" onclick="if(this.checked) this.value=1; else this.value=0;" '.$chk1.' title="Do not copy field contents during Load Previous process when submitting new projects." >',//form_checkbox('required','1'),
				),
				'Description' => form_input(array('name'=>'description','id'=>'description','rel'=>'.note[class~='.$id.']')),
				);
				
				}
				
			$seperate_help = '<span class="icon toolbox" style="cursor: pointer; float: right; color: #9CBDDF; margin-top: -10px;width: 40px;" onclick=showFromHelp();>Help</span>';
			
			//specific options
			//echo "<pre>";
			//print_r($options);
			//die;
			$desc='';
			switch($type)
			{
				case 'dropdown':
					$options['Options'] = form_textarea(array('name'=>'values','class'=>'dropdown','rel'=>'select[name='.$id.']','style'=>'height: 100px;width:400px;'));
									
					break;
				case 'radio':
					$options['Options'] = form_textarea(array('name'=>'values','class'=>'radio','rel'=>'span.values[class~='.$id.']','style'=>'height: 100px;width:400px;'));
					break;
				case 'checkbox':
					$options['Options'] = form_textarea(array('name'=>'values','class'=>'checkbox','rel'=>'span.values[class~='.$id.']','style'=>'height: 100px;width:400px;'));
					break;
				case 'button':
					$options['Value'] = form_input(array('name'=>'value','class'=>'button','rel'=>'input[name='.$id.']'));
					unset($options['Required']); //useless
					break;
				case 'text':
					unset($options['Label']); //useless
					unset($options['Description']); //useless
					break;
				default: break;
			}
			
			//echo $label;
			
			//throw a delete on the bottom for good measure!
			//$options['Delete'] = form_input(array('rel'=>$id,'name'=>'remove','value'=>'Delete Element','type'=>'button','class'=>'button','onclick'=>'Admin.formbuilder.remove(this);'));
			$options[''] = form_input(array('rel'=>$id, 'id'=>'remove','name'=>'remove','value'=>'Delete Element','type'=>'button','class'=>'button','onclick'=>'Admin.formbuilder.remove(this);','style'=>'margin-top: 20px !important;top:-6px!important;'));
			$output=$seperate_help;
			//spit out the options for ajax
			foreach ($options as $k => $option) {
				//echo $k."<br>";
				//print_r($option);
			$wid='';	
				if($k!='')
				$wid="width:75px;";
				
				$output .= '<li class="'.$id.'"  data-id="'.$id.'">';
				$output .= '<strong style="padding-right:10px;padding-top:6px;'.$wid.'">'.$k.'</strong> ';
				$output .= '<ul>';
						if (is_array($option)) {
							foreach ($option as $sk => $sub) {
								//echo $sk."<br>".$sub."<br>";
								$output .= '<li class="sub"><strong>'.$sk.'</strong> '.$sub.'</li>';
							}
						} else {
							// echo $option."<br>";
							$output .= '<li class="sub">'.$option.'</li>';
						}
				$output .= '</ul>';
				$output .= '</li>';
			}
			echo $output;
		}
		function element_display($attr)
		{			
			//echo "<pre>",print_r($attr),"</pre>";
			//if no load more then sep attribute
			$load_prev_chked=false;
			if(isset($attr['load_prev']) && $attr['load_prev']==1)
			{
				if(isset($attr['no_load_more']) && $attr['no_load_more']==1)
				{
					$load_prev_chked=true;
				}
			}
			if($load_prev_chked)
			{
				$attr['text_val']="";
				$attr['value']="";
			}
			$lable_class='';
			$pro=false;
			$valuessssss='';
			//$required='1';
	    	$required_vars='';
	    	$description='';
	    	$contents='';
	    	$required_class='';
	    	//$required_span='';
			if(isset($attr['id']) && $attr['id']!='')
				$id=$attr['id'];
			else
				$id = 'element_'.uniqid();
			$dropdown_val=array(''=>'No Content');
			$btn_value='No Content';

			// Required field.
			//if(isset($attr['required']) && $attr['required']!=1)
			if(isset($attr['required']) && $attr['required']==0)
			{
				if($attr['type']!='text')
				{
					$required=$attr['required'];
					$required_class='required-entry';
					$required_span='<span class="data-required">* </span>';
					//$required_span='';
				}else{
					$required_span='<span class="data-required">* </span>';
				}
			}else{
				$required_span='';
			}
			if(isset($attr['required_vars']) && $attr['required_vars']!='')
			{
				$required_vars=$attr['required_vars'];
			}
			if(isset($attr['description']) && $attr['description']!='')
			{
			$description=$attr['description'];
			}
			if(isset($attr['text_val']) && $attr['text_val']!="")
			{
				$attr['text_val']=str_replace("<br>","\n",$attr['text_val']);
				$contents=html_entity_decode($attr['text_val']);
			}
			if(isset($attr['values']) && $attr['values']!='')
			{
				$attr['values']=str_replace('&amp;', '&', $attr['values']);
				$dropdown_val1=explode(';',$attr['values']);
				if($attr['type'] == "dropdown")
					$dropdown_val=array_merge(array('0'=>'Please Select'),$dropdown_val1);
				else	
					$dropdown_val=$dropdown_val1;
				$pro=true;
				$valuessssss="<input class='values' type='hidden' name='properties[".$id."][values]' value='".$attr['values']."' />
							
							";
			}
			$ivalue="";
			$svalue=array();
			$chsel_arr=array();
			
			if(isset($attr['value']) && $attr['value']!='')
			{
				$btn_value=$attr['value'];
				$pro=true;
				$valuessssss="<input class='value' type='hidden' name='properties[".$id."][value]' value='".$attr['value']."' />";
				if($attr['type']=='textarea')
				{
					$ivalue=strip_tags($attr['value']);
					$ivalue=str_replace("<br>","\n",$ivalue);
					$contents=html_entity_decode(urldecode(str_replace("<br>","\n",$ivalue)));
					$ivalue = $contents;
				}
				else 
					$ivalue=$attr['value'];
					
				array_push($svalue,$attr['value']);
				if($attr['type']=='checkbox')
				{
					$chsel_arr=explode(",",$attr['value']);
				}				
			}
			$checked='';
			if(isset($chsel_arr[0]) && $chsel_arr[0]=='on')
				$checked='checked="checked"';			
			$element_ck='<input type="checkbox"  name="'.$id.'[]"  class="'.$required_class.'" '.$checked.'>';
			if($attr['type']=='checkbox')
			{
				if(isset($attr['values']) && $attr['values']!='')
				{
					$element_chk='';
					$count_chk=0;
					
					foreach ($dropdown_val as $va)
					{
						$checked='';
						if(in_array($count_chk,$chsel_arr))
							$checked='checked="checked"';						
						if($element_chk=='')
					 		$element_chk='<input type="checkbox" name="'.$id.'[]" value="'.$count_chk.'" class="'.$required_class.'" '.$checked.'>'.$va.'<br>';
						else 
							$element_chk.='<input type="checkbox" name="'.$id.'[]" value="'.$count_chk.'" class="'.$required_class.'" '.$checked.'>'.$va.'<br>';
						
						$count_chk++;
					}
				if($element_chk!='')
				$element_ck=$element_chk;
				}	
			}
			$element_ra='<input type="radio" name="'.$id.'" class="'.$required_class.'">';
			if($attr['type']=='radio')
			{
				if(isset($attr['values']) && $attr['values']!='')
				{
					
					$element_rad='';
					$count_rd=0;
					foreach ($dropdown_val as $va)
					{
						$checked='';
						if($ivalue!='')
						{
						if($count_rd==$ivalue)
							$checked='checked="checked"';
						}	
						if($element_rad=='')
					 		$element_rad='<input type="radio" name="'.$id.'" value="'.$count_rd.'"  class="'.$required_class.'" '.$checked.'>'.$va.'<br>';
						else 
							$element_rad.='<input type="radio" name="'.$id.'" value="'.$count_rd.'"  class="'.$required_class.'" '.$checked.'>'.$va.'<br>';
					$count_rd++;		
					}
				if($element_rad!='')
				$element_ra=$element_rad;
				}	
			}
			$ivalue=str_replace("\'","'",$ivalue);
			switch($attr['type'])
			{
				case 'text': 
					$element = form_textarea(array(
						'class' => 'wysiwyg '.$required_class,
						'id' => $id,
						'name' => $id,
						'rows' => 5,
						'cols' => 50,
						'style'=>'display:none;'
						
					)); 
					$lable_class='text_lable_class';
					break;
				case 'textarea':
					$element = form_textarea(array(
						'name' => $id,
						'rows' => 5,
						'cols' => 50,
						'value' => $ivalue,
						'class' =>$required_class,						
					)); 
					break;
				case 'textbox': $element = form_input(array('name'=>$id,'class'=>'jf_text form_role_input '.$required_class),$ivalue); break;
				case 'dropdown': $element = form_dropdown($id,$dropdown_val,$svalue,'class='.$required_class); break;
				case 'checkbox': $element = '<span class="values '.$id.'">'.$element_ck.'</span>'; break;
				case 'radio': $element = '<span class="values '.$id.'">'.$element_ra.'</span>'; break;
				case 'datetime': $element = form_input(array('name'=>$id,'class'=>'datepicker jf_text form_role_input '.$required_class),$ivalue); break;
				case 'fileupload': $element = form_upload($id); break;
				case 'button': $element = form_input(array('name'=>$id,'value'=>$btn_value,'type'=>'button')); break;
				default: $element = null; break;
				
				//echo $element;
			}
			//give the text box a differnt label
			if(isset($attr['label']) && $attr['label']!="")
			{
				$label=$attr['label'];
				$pro=true;
			}
			else
			$label = ($attr['type'] == 'text') ? ' ' : 'No Label';
			
			if($pro)
			{
			//$this->properties($attr);
			}
			
			//basic output list element.
			if(isset($attr['edit']) && $attr['edit']!='')
			{
				if(isset($contents) && $contents!='')
				{
					if(isset($attr['text_val']) && $attr['text_val']!="")
					{
						$contents=html_entity_decode(urldecode($contents));
						$label = str_replace(",","",$contents);
						$label = str_replace("+"," ",$contents);
						$label = str_replace("\n","<br>",$contents);
					}
				}
				$colon=($attr['type'] == 'text') ? '' : ':';
				$output = "
					<li>
					<label for='".$id."' class='".$lable_class."'>".$label.$required_span.' '.$colon."</label>
							<div class='block'>".$element."
							<span class='note ".$id."'>$description</span></div>
						<div class='clear'></div>
						<div class='attrs clear ".$id."'>
							<input type='hidden' name='properties[".$id."][name]' value='".$id."'/>
							<input type='hidden' name='properties[".$id."][value]' class='label' value='".$label."' />".$valuessssss."
							<input type='hidden' name='properties[".$id."][required]' class='required' value='".$required."' / >
							<input type='hidden' name='properties[".$id."][required_vars]' class='required_vars' value='".$required_vars."' />
							<input type='hidden' name='properties[".$id."][description]' class='description' value='".$description."'>							
						</div>
					</li>
				";
			}
			
			if ($element) {
				//set output to AJAX
				if(isset($attr['edit']) && $attr['edit']!='')
					echo html_entity_decode($output.'^'.$id);
				else
					echo html_entity_decode($output);
			}
			sleep(1);
		}
		function get_count()
		{
		    static $count = 0; // "inner" count = 0 only the first run
		    return $count++; // "inner" count + 1
		}
		
		
	}
	
?>
<script type="text/javascript">
function allowOnly(e)
{
	var unicode=e.charCode? e.charCode : e.keyCode
	if(unicode==39 || unicode==34)
		return false;
		
	return true;
}
function textToLable(obj){
			id=$(obj).attr('id');
			if (id !== undefined)
			{
			id_val=$(obj).val();
			$('#form_builder_panel ol li a').each(function(i,e) {
				if (e.id==id.replace("_text","")) 
					$(e).html(id_val);
			});
			}
}	
</script>
<noscript></noscript>
