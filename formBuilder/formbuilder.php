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
    case 'element_copy':
	$fb->element_copy($_POST);
        break;
    case 'element_bulk':
        $fb->element_bulk($_POST);
        break;
    case 'element_bulk_project':
        $fb->element_bulk_project($_POST);
        break;
    case 'element_display':
        $fb->element_display($_GET);
        break;
    case 'element_display_bulk':
        $fb->element_display_bulk($_POST);
        break;
    case 'element_display_bulk_instruction':
        $fb->element_display_bulk_instruction($_POST);
        break;
    case 'element_display_bulk_billing':
        $fb->element_display_bulk_billing($_POST);
        break;
    case 'element_display_bulk_data_fields':
        $fb->element_display_bulk_data_fields($_POST);
        break;

    default:
        break;
}

/**
 * Filemanager
 */
class Formbuilder {
    /*
      Build is used to output the forms data as a html form
      $data is an array generated on post from the builder.
     */

    function build($data) {
		//echo "<pre>",print_r($data),"</pre>";die;
        if (!isset($data['properties']))
            return false;
        $elements = $data['properties'];


        foreach ($elements as $k => $val) {
            if (!isset($data[$k]))
                $data[$k] = NULL;
            if (!isset($val['values']))
                $val['values'] = NULL;

            $elements[$k]['content'] = $data[$k];

            $name = $k;

            //Added Date:19-1-15, selected option value
            $opt_chk = '';
            $field_types = '';
            $default_answer = '';
            if (isset($val['optionchk']) && $val['optionchk'] != '') {
                $val['optionchk'] = rawurldecode($val['optionchk']);
                $opt_chk = $val['optionchk'];
            }
            /*if (isset($val['field_type']) && $val['field_type'] != '') {
                $val['field_type'] = rawurldecode($val['field_type']);
                $field_types = $val['field_type'];
            }*/

            if (isset($val['default_answer']) && $val['default_answer'] != '') {
                $val['default_answer'] = rawurldecode($val['default_answer']);
                $default_answer = $val['default_answer'];
            }

            //End
            switch ($val['type']) {
                case 'text':
                    $contents = html_entity_decode(rawurldecode($data[$k]['text_val']));
                    //#$contents = str_replace(","," ",$contents);
                    $elements[$k]['html'] = $contents;
                    break;
                case 'textarea':
                    $elements[$k]['html'] = form_textarea(array(
                        'name' => $name,
                        'rows' => 5,
                        'cols' => 40,
                        //'value' => $data[$k],
                        'maxlength'=>'10000',
                        'value' => $default_answer,
                        'class' => 'instruction_multi10000 form-control '. ((isset($val['required']) && $val['required'] == 0) ? 'required' . ((isset($val['required_vars'])) ? '{' . $val['required_vars'] . '}' : null) : null)
                    ));
                    break;
                case 'textbox':
                    $elements[$k]['html'] = form_input(array(
                                'name' => $name,
                                //'value' => $data[$k],
                                'maxlength'=>'255',
                                'value' => $default_answer,
                                'class' => 'instruction_single255 form-control '. ((isset($val['required']) && $val['required'] == 0) ? 'required' . (((isset($val['required_vars']) && $val['required_vars'] != "")) ? '{' . $val['required_vars'] . '}' : null) : null)
                            )) . '<span id=' . $name . ' style="color:red;"></span>';
                    break;
                case 'number':
                    $elements[$k]['html'] = form_input(array(
                                'name' => $name,
                                //'value' => $data[$k],
                                'maxlength'=>'255',
                                'value' => $default_answer,
                                'class' => ' instruction_single255 form-control user_input numeric-field-qu negative-key'. ((isset($val['required']) && $val['required'] == 0) ? 'required' . (((isset($val['required_vars']) && $val['required_vars'] != "")) ? '{' . $val['required_vars'] . '}' : null) : null)
                            )) . '<span id=' . $name . ' style="color:red;"></span>';
                    break;
                case 'dropdown':
                    $cls = 'class="form-control"';
                    if (isset($val['required']) && $val['required'] == 0) {
                        $cls = 'class="required form-control"';
                    }

                    if (!$val['values']) {
                        $options = array("" => 'No Content'); /* unset($elements[$k]); break; */
                    }
                    $options = explode(';', html_entity_decode($val['values']));
                    //$options = array_merge(array('0' => 'Please Select'), $options);
                    //Added Date:19-1-15, set selected dropdown value
                    $element_select = array();
                    foreach ($options as $va) {
                        $element_select[] = str_replace(',', '&#44;', htmlentities($va));
                    }
                    $options = array_merge(array('0' => 'Please Select'), $element_select);
                    $selectedkey = array_search($opt_chk, $options);
                    //End
                    if (empty($options)) {
                        unset($elements[$k]);
                        break;
                    }

                    $elements[$k]['html'] = form_dropdown($name, $options, $selectedkey, $cls);
                    break;
                case 'checkbox':
                    $input = null;
                    $cls = 'class="form-control"';
                    if (isset($val['required']) && $val['required'] == 0) {
                        $cls = 'class="required form-control"';
                    }
                    if (!$val['values']) {
                        $options = array('' => ''); /* unset($elements[$k]); break; */
                    }
                    $options = explode(';', html_entity_decode($val['values']));
                    //echo "<prE>",print_r($options),"</pre>";die;
                    if (empty($options)) {
                        unset($elements[$k]);
                        break;
                    }

                    foreach ($options as $option) {
                        $option= htmlentities($option);
                        $opt_chks = explode(",", $opt_chk);
                        $chkChecked = "";
                        if (in_array($option, $opt_chks)) {
                            $chkChecked = "checked='checked'";
                        }
                        $input .= form_checkbox($name . '[]', $option, $chkChecked) . ' ' . $option . '<br/>';
                    }
                    $elements[$k]['html'] = $input;
                    break;
                case 'radio':
                    $input = null;
                    $cls = 'class="form-control"';
                    if (isset($val['required']) && $val['required'] == 0) {
                        $cls = 'class="required form-control"';
                    }
                    if (!$val['values']) {
                        $options = array('' => ''); /* unset($elements[$k]); break; */
                    }
                    $options = explode(';', html_entity_decode($val['values']));
                    if (empty($options)) {
                        unset($elements[$k]);
                        break;
                    }

                    foreach ($options as $option) {
                        $option= htmlentities($option);
                        $radioChecked = "";
                        if ($option == $opt_chk) {
                            $radioChecked = 'checked="checked"';
                        }

                        $input .= form_radio($name . '[]', $option, $radioChecked) . ' ' . $option . '<br/>';
                    }
                    $elements[$k]['html'] = $input;
                    break;
                case 'datetime':
                	$elements[$k]['html'] = '<div class="input-group calender-group">'.form_input(array('id'=>'lbl-'.$k.'_preview','name' => $k, 'placeholder' => 'Choose a date', 'class' => 'datepickers form-control ','readonly'=>'readonly')).'</div>';
                    /*$elements[$k]['html'] = form_input(array(
                        'name' => $name,
                        'placeholder' => 'Choose a date',
                        //'value' => $data[$k],
                        'class' => 'datepicker ' . ((isset($val['required']) && $val['required'] == 0) ? 'required' . ((isset($val['required_vars'])) ? '{' . $val['required_vars'] . '}' : null) : null)
                    ));*/
                    break;
                case 'fileupload':
                    $elements[$k]['html'] = form_upload(array(
                        'name' => $name,
                        'class' => ((isset($val['required']) && $val['required'] == 0) ? 'required' . ((isset($val['required_vars'])) ? '{' . $val['required_vars'] . '}' : null) : null)
                    ));
                    break;
                case 'button':
                    $elements[$k]['html'] = form_input(array(
                        'name' => $name,
                        'value' => ((isset($val['value'])) ? $val['value'] : 'Button'),
                        'type' => 'button',
                        'class' => 'btn btn-primary'
                    ));
                    break;
            }
        }

        return $elements;
    }

    /*
     */

    function element($attr) {
       // echo "<pre>",print_r($attr),"</pre>"; die();
        $lable_class = '';
        $valuessssss = '';
        $required = '1';
        $qareportuse = '0';
        $no_load_more = '1';
        $required_vars = '';
        $description = '';
        $element_view = '0';

        if (isset($attr['id']) && $attr['id'] != '')
            $id = $attr['id'];
        else
            $id = 'element_' . uniqid();

        $dropdown_val = array('' => 'No Content');
        $btn_value = 'No Content';
        //if(isset($attr['required']) && $attr['required']!='')
        if (isset($attr['no_load_prev']) && $attr['no_load_prev'] != '') {
            $no_load_more = $attr['no_load_prev'];
        }
        if (isset($attr['required']) && $attr['required'] != '') {
            $required = $attr['required'];
        }
        if (isset($attr['element_view']) && $attr['element_view'] != '') {
            $element_view= $attr['element_view'];
        }
        if (isset($attr['qareportuse']) && $attr['qareportuse'] != '') {
            $attr['qareportuse'] = ($attr['qareportuse']);
            $qareportuse = $attr['qareportuse'];
        }
        if (isset($attr['required_vars']) && $attr['required_vars'] != '') {
            $required_vars = $attr['required_vars'];
        }
        if (isset($attr['description']) && $attr['description'] != '') {
            $description = $attr['description'];
        }
        $opt_chk = "";
        if (isset($attr['optionchk']) && $attr['optionchk'] != '') {
            $attr['optionchk'] = rawurldecode($attr['optionchk']);
            $opt_chk = $attr['optionchk'];
        }

        if (isset($attr['values']) && $attr['values'] != '') {
            $attr['values'] = str_replace('&amp;', '&', $attr['values']);
            $dropdown_val1 = explode(';', $attr['values']);
            /*
              $dropdown_val=array_merge(array('0'=>'Please Select'),$dropdown_val1); */
            if ($attr['type'] == "dropdown") {
                $element_select = array();
                foreach ($dropdown_val1 as $va) {
                    $element_select[] = str_replace(',', '&#44;', $va);
                }
                $dropdown_val = array_merge(array('0' => 'Please Select'), $element_select);
                $selectedkey = array_search($opt_chk, $dropdown_val);
            } else {
                $dropdown_val = $dropdown_val1;
            }
            $pro = true;
            $valuessssss = "<input class='values' type='hidden' name='properties[" . $id . "][values]' id='" . $id . "_values' value='" . str_replace(',', '&#44;', $attr['values']) . "' />";
        }

        if (isset($attr['value']) && $attr['value'] != '') {
            $btn_value = $attr['value'];
            $pro = true;
            $valuessssss = "<input class='value' type='hidden' name='properties[" . $id . "][value]' id='" . $id . "_value' value='" . $attr['value'] . "' />";
        }
        $lbl_id = 'lbl_chkbx_'.uniqid();

        /* 508 Label Changing position
            Code Modified By Nelson
         * Code Starts */
        //give the text box a differnt label
        if (isset($attr['label']) && $attr['label'] != "") {
            if ($attr['type'] == 'text')
                $label = $contents;
            else
                $label = $attr['label'];
            $pro = true;
        }
        else {
            $label = ($attr['type'] == 'text') ? 'No Text Heading' : 'No Label';
        }

        if (isset($attr['edit']) && $attr['edit'] != '') {
        if (isset($contents) && $contents != '') {
                $contents = html_entity_decode(rawurldecode($contents));
                $label = str_replace(",", "", $contents);
            }
        $remove[] = "'";
            $remove[] = '"';
            //$remove[] = "-";
            $label = str_replace($remove, "", $label);
        }
        $element_ck = '<input type="checkbox" name="temp[values][]" id="'.$lbl_id.'">';
        /* Code Ends*/
        if ($attr['type'] == 'checkbox') {
            if (isset($attr['values']) && $attr['values'] != '') {
                $element_chk = '';
                foreach ($dropdown_val as $va) {
                    $lbl_id = 'lbl_chkbx_'.uniqid();
                    $lblTxt = str_replace(',', '&#44;', $va);
                    if ($element_chk == '')
                        $element_chk = '<input type="checkbox" name="temp[values][]" id="'.$lbl_id.'" aria-label="'.$label.','.$lblTxt.'">' . $lblTxt . '<br>';
                    else
                        $element_chk.='<input type="checkbox" name="temp[values][]" id="'.$lbl_id.'" aria-label="'.$label.','.$lblTxt.'">' . $lblTxt . '<br>';
                }
                if ($element_chk != '')
                    $element_ck = $element_chk;
            }
        }
        $element_ra = '<input type="radio">';
        if ($attr['type'] == 'radio') {
            if (isset($attr['values']) && $attr['values'] != '') {
                $element_rad = '';
                foreach ($dropdown_val as $va) {
                    $lbl_id = 'lbl_chkbx_'.uniqid();
                    $lblTxt = str_replace(',', '&#44;', $va);
                    if ($element_rad == '')
                        $element_rad = '<input type="radio" name="temp[values][]" aria-label="'.$label.','.$lblTxt.'">' . $lblTxt . '<br>';
                    else
                        $element_rad.='<input type="radio" name="temp[values][]" aria-label="'.$label.','.$lblTxt.'">' . $lblTxt . '<br>';
                }
                if ($element_rad != '')
                    $element_ra = $element_rad;
            }
        }
        $contents = '';
        if (isset($attr['edit']) && $attr['edit'] != '') {

            if (isset($attr['text_val']) && $attr['text_val'] != "")
                $contents = html_entity_decode(str_replace("\n", "<br>", $attr['text_val'])); //$attr['text_val'];


            switch ($attr['type']) {
                case 'text':
                    /* $element = form_textarea(array(
                      'class' => 'wysiwyg',
                      'id' => $id,
                      'name' => $id,
                      'rows' => 5,
                      'cols' => 40,


                      ),$contents); */
                    $lable_class = 'text_lable_class';
                    break;
                case 'textarea':
                    $element = form_textarea(array(
                        'name' => $id,
                        'rows' => 5,
                        'cols' => 40,
                        'id'=>'lbl-'.$id,
                        'class' => 'wysiwygNoEditor instruction_multi10000 form-control ',
                        'maxlength'=>'10000',
                    ));
                    break;
                case 'textbox': $element = form_input(array('name' => $id,'id'=>'lbl-'.$id, 'maxlength'=>'255','class' => 'jf_text form_role_input instruction_single255 form-control '));
                    break;
                case 'number': $element = form_input(array('name' => $id,'id'=>'lbl-'.$id, 'maxlength'=>'255','class' => 'jf_text form_role_input instruction_single255 form-control user_input numeric-field-qu negative-key'));
                    break;
                case 'dropdown': $element = form_dropdown($id, $dropdown_val, $selectedkey);
                    break;
                case 'checkbox': $element = '<span class="values ' . $id . '" style="display:inline-block;">' . $element_ck . '</span>';
                    break;
                case 'radio': $element = '<span class="values ' . $id . '" style="display:inline-block;">' . $element_ra . '</span>';
                    break;
                case 'datetime': $element = '<div class="input-group calender-group">'.form_input(array('id'=>'lbl-'.$id,'name' => $id, 'placeholder' => 'Choose a date', 'class' => 'datepickers form-control ','readonly'=>'readonly')).'</div>';
                    break;
                case 'fileupload': $element = form_upload($id);
                    break;
                case 'button': $element = form_input(array('name' => $id, 'value' => $btn_value, 'type' => 'button'));
                    break;
                default: $element = null;
                    break;
            }
        } else {
            switch ($attr['type']) {
                case 'text':
                    /* $element = form_textarea(array(
                      'class' => 'wysiwyg',
                      'id' => $id,
                      'name' => $id,
                      'rows' => 5,
                      'cols' => 40,
                      'style'=>'display:none;'

                      )); */
                    $lable_class = 'text_lable_class';
                    break;
                case 'textarea':
                    $element = form_textarea(array(
                        'name' => $id,
                        'rows' => 5,
                        'cols' => 40,
                        'id'=>'lbl-'.$id,
                        'class' => 'wysiwygNoEditor instruction_multi10000 form-control ',
                        'maxlength'=>'10000',
                    ));
                    break;
                case 'textbox': $element = form_input(array('name' => $id, 'maxlength'=>'255','id'=>'lbl-'.$id, 'class' => 'jf_text instruction_single255 form-control '));
                    break;
                case 'number': $element = form_input(array('name' => $id, 'maxlength'=>'255','id'=>'lbl-'.$id, 'class' => 'jf_text form_role_input instruction_single255 form-control user_input numeric-field-qu negative-key'));
                    break;
                case 'dropdown': $element = form_dropdown($id, $dropdown_val);
                    break;
                case 'checkbox': $element = '<span class="values ' . $id . '" style="display:inline-block;">' . $element_ck . '</span>';
                    break;
                case 'radio': $element = '<span class="values ' . $id . '" style="display:inline-block;">' . $element_ra . '</span>';
                    break;
                case 'datetime': $element = '<div class="input-group calender-group">'.form_input(array('id'=>'lbl-'.$id,'name' => $id, 'placeholder' => 'Choose a date', 'class' => 'datepickers form-control ','readonly'=>'readonly')).'</div>';
                    break;
                case 'fileupload': $element = form_upload($id);
                    break;
                case 'button': $element = form_input(array('name' => $id, 'value' => $btn_value, 'type' => 'button'));
                    break;
                default: $element = null;
                    break;
            }
        }

        $email_view = '';
        if($attr['form_type']!='custodianform'){
            $email_view="<a title='Will Not Appear in Email Alerts' href='javascript:void(0);' id='view_".$id."' tabindex='-1' class='toolbox icon-set' onClick='show_email_field(\"$id\", 0);'><em class='fa fa-eye-slash text-primary' title='Show Email/Hide Email'></em><span class='sr-only'>Show Email/Hide Email</span></a>";
        }

        $fieldsetstart = '';
        $fieldsetend = '';
        if ($attr['type'] == 'checkbox' || $attr['type'] == 'radio') {
            $fieldsetstart = '<fieldset><legend class="sr-only" id=sr_only_'.$id.'>'.$label.'</legend>';
            $fieldsetend = '</fieldset>';
        }

        $text_val = "";
        $order = microtime(true);
        $order = str_replace(".", "", $order);
        $labelID=($attr['type'] == 'datetime')?'lbl-':'lbl-';
        if (isset($attr['edit']) && $attr['edit'] != '') {
            if (isset($attr['order']) && $attr['order'] != '')
                $order = $attr['order'];
            $chk = '';
            if ($required == 0)
                $chk = 'checked=checked';

            $chknoloadpre = '';
            if ($no_load_more == 1) {
                $chknoloadpre = 'checked=checked';
            }
            $colsm=($attr['type'] == 'text')?9:3;
            $colsmnext=($attr['type'] == 'text')?1:7;
            $output = "
                <li data-id='" . $id . "'>
                        <div class='row border-saprater'>
                            <div class='col-md-$colsm'><label for='".$labelID . $id . "' class='" . $lable_class . " form_label' ><span id='" . $id . "' title='".$label."'>" . $label . "</span></label></div>
                            <div class='block col-md-$colsmnext'>
                                $fieldsetstart" . $element . "$fieldsetend
                                <span class='note " . $id . "'>$description</span>
                            </div>
                            <div class='col-md-2 text-right handle'>".$email_view."
                              <a title='Move' href='javascript:void(0);' tabindex='-1' class='toolbox icon-set'><em class='fa fa-arrows text-primary' title='Move'></em><span class='screenreader'>Move</span></a>
                              <a title='Edit' href='javascript:void(0);'  class='properties toolbox icon-set' rel='" . $attr['type'] . "'><em class='fa fa-pencil text-primary' for='" . $id . "' title='Edit'></em><span class='screenreader'>" . $label . "</span></a>
                              <a class='icon-set' title='Remove' href='javascript:void(0);' onclick='deleteformbuilderfield(this,\"$id\");'><em class='fa fa-close text-primary' title='Remove'></em><span class='screenreader'>Close</span></a>
                            </div>
                        </div>
                        <div class='clear'></div>
                        <div class='attrs clear " . $id . "'>
			<input type='hidden' name='properties[" . $id . "][type]' value='" . $attr['type'] . "'/>
			<input type='hidden' name='properties[" . $id . "][order]' value='" . $order . "'/>";
		            if (isset($contents) && $contents != '') {
		                $output .= "<input type='hidden' name='properties[" . $id . "][label]' class='label' value='' />" . $valuessssss;
		            } else {
		                $output .="<input type='hidden' name='properties[" . $id . "][label]' class='label' value='" . $label . "' />" . $valuessssss;
		            }

                     $output .="<input type='hidden' name='properties[" . $id . "][required]' class='required' value='" . $required . "' id='required_" . $id . "' " . $chk . "  / >
                                <input type='hidden' name='properties[" . $id . "][qareportuse]'   value='" . $qareportuse . "' id='qareportuse_" . $id . "' />
                                <input type='hidden' name='properties[" . $id . "][no_load_prev]' value='" . $no_load_more . "' id='no_load_prev_" . $id . "' " . $chknoloadpre . "  / >
                                <input type='hidden' name='properties[" . $id . "][required_vars]'  class='required_vars' value='" . $required_vars . "' />
                                <input type='hidden' name='properties[" . $id . "][description]' id='description_" . $id . "' class='description' value='" . $description . "'>
                                <input type='hidden' name='properties[" . $id . "][text_val]' id='" . $id . "_text_val' class='text_val' value='" . rawurlencode(htmlentities($contents)) . "'>
                                </div>
                            </li>
                        ";
        } else {
			$colsm=($attr['type'] == 'text')?9:3;
            $colsmnext=($attr['type'] == 'text')?1:7;
            $output = "
				<li data-id='" . $id . "'>
					<div class='row border-saprater'>
					 <div class='col-md-$colsm'><label for='".$labelID . $id . "' class='" . $lable_class . " form_label'><span id='" . $id . "' title='".$label."'>" . $label . "</a></label></div>
					<div class='block col-md-$colsmnext'>
						$fieldsetstart" . $element . "$fieldsetend
						<span class='note " . $id . "'></span>
					</div>
					<div class='col-md-2 text-right handle'>
                                          ".$email_view."
					  <a title='Move' href='javascript:void(0);' tabindex='-1' class='toolbox icon-set'><em class='fa fa-arrows text-primary' title='Move'></em><span class='screenreader'>Move</span></a>
					  <a title='Edit' href='javascript:void(0);' onclick=$('" . $id . "').trigger('click'); class='properties toolbox icon-set' rel='" . $attr['type'] . "'><em class='fa fa-pencil text-primary' for='" . $id . "' title='Edit'></em><span class='screenreader'>" . $label . "</span></a>
					  <a class='icon-set' title='Remove' href='javascript:void(0);' onclick='deleteformbuilderfield(this,\"$id\");'><em class='fa fa-close text-primary'  title='Remove'></em><span class='screenreader'>Close</span></a>
					</div>
					</div>
					<div class='clear'></div>
					<div class='attrs clear " . $id . "'>
                                            <input type='hidden' name='properties[" . $id . "][type]' value='" . $attr['type'] . "'/>
                                            <input type='hidden' name='properties[" . $id . "][order]' value='" . $order . "'/>
                                            <input type='hidden' id='hide_".$id."' name='properties[" . $id . "][element_view]' value='".$element_view."'>
                                            <input type='hidden' name='properties[" . $id . "][text_val]' id='" . $id . "_text_val' class='text_val' value='" . $contents . "'>
                                            <input type='hidden' id='required_" . $id . "' name='properties[" . $id . "][required]' class='required' value='1'>
                                            <input type='hidden' name='properties[" . $id . "][no_load_prev]' value='0' id='no_load_prev_" . $id . "'  / >
                                            <input type='hidden' name='properties[" . $id . "][optionchk]' value='' id='optionchk_" . $id . "'  / >
                                            <input type='hidden' name='properties[" . $id . "][qareportuse]'   value='" . $qareportuse . "' id='qareportuse_" . $id . "' />
					</div>
				</li>
			";
        }
        //if ($element) {
        //set output to AJAX
        usleep(1000000);
        if (isset($attr['edit']) && $attr['edit'] != '')
            echo $output . '^' . $id;
        else
            echo $output;

        //}
    }

    function element_bulk($attributes)
    {
        $elements_output = array();
        //echo "<pre>",print_r($attributes); die();
        foreach ($attributes as $attr) {
            $lable_class = '';
            $valuessssss = '';
            $required = '1';
            $qareportuse = '0';
            $no_load_more = '0';
            $required_vars = '';
            $description = '';
            $sync_prods = '';
            $element_view = '';
            //$field_types = '';
            $opt_chk = '';
            $default_answer = '';
            $default_unit = 0;
            if (isset($attr['id']) && $attr['id'] != '')
                $id = $attr['id'];
            else
                $id = 'element_' . uniqid();

            $dropdown_val = array('' => 'No Content');
            $selectedkey = array('');
            $btn_value = 'No Content';
            $required_text = 'false';
            //if(isset($attr['required']) && $attr['required']!='')
            if (isset($attr['no_load_prev']) && $attr['no_load_prev'] != '') {
                $attr['no_load_prev'] = rawurldecode($attr['no_load_prev']);
                $no_load_more = $attr['no_load_prev'];
            }
            if (isset($attr['required']) && $attr['required'] != '') {
                $attr['required'] = rawurldecode($attr['required']);
                $required = $attr['required'];
            }

            if (isset($attr['qareportuse']) && $attr['qareportuse'] != '') {
                $attr['qareportuse'] = ($attr['qareportuse']);
                $qareportuse = $attr['qareportuse'];
            }
            if (isset($attr['element_view']) && $attr['element_view'] != '') {
                $attr['element_view'] = ($attr['element_view']);
                $element_view = $attr['element_view'];
            }
            if (isset($attr['required_vars']) && $attr['required_vars'] != '') {
                $attr['required_vars'] = rawurldecode($attr['required_vars']);
                $required_vars = $attr['required_vars'];
            }
            if (isset($attr['description']) && $attr['description'] != '') {
                $attr['description'] = rawurldecode($attr['description']);
                $description = $attr['description'];
            }
            if (isset($attr['sync_prod']) && $attr['sync_prod'] != '') {
                $attr['sync_prod'] = rawurldecode($attr['sync_prod']);
                $sync_prods = $attr['sync_prod'];
            }
            $default_unit = 0;
            /*if (isset($attr['field_type']) && $attr['field_type'] != '') {
                $attr['field_type'] = rawurldecode($attr['field_type']);
                $field_types = $attr['field_type'];
                if($attr['field_type'] == 'number' || $attr['field_type'] == 1){
                    $field_types = 1;
                    if (isset($attr['default_unit']) && $attr['default_unit'] != '') {
						$default_unit = $attr['default_unit'];
					}
                }
            }*/
            if (isset($attr['optionchk']) && $attr['optionchk'] != '') {
                $attr['optionchk'] = rawurldecode($attr['optionchk']);
                $opt_chk = $attr['optionchk'];
            }

			if (isset($attr['default_answer']) && $attr['default_answer'] != '') {
                //if($id=='element_5afe7e0e384f1')
                {
                $attr['default_answer'] = rawurldecode($attr['default_answer']);
                $default_answer = $attr['default_answer'];
                //echo $default_answer;die;
                }
            }

            if (isset($attr['default_unit']) && $attr['default_unit'] != '') {
                $attr['default_unit'] = rawurldecode($attr['default_unit']);
                $default_unit = $attr['default_unit'];
            }

            if (isset($attr['values']) && $attr['values'] != '') {
                $attr['values'] = rawurldecode($attr['values']);
                $attr['values'] = str_replace('&amp;', '&', $attr['values']);
                $dropdown_val1 = explode(';', html_entity_decode($attr['values']));
                /* $dropdown_val=array_merge(array('0'=>'Please Select'),$dropdown_val1); */
                if ($attr['type'] == "dropdown") {
                    $element_select = array();
                    foreach ($dropdown_val1 as $va) {
                        $element_select[] = str_replace(',', '&#44;', htmlentities($va));
                    }
                    $dropdown_val = array_merge(array('0' => 'Please Select'), $element_select);
                    //$selectedkey = array_search($opt_chk, $dropdown_val);
                    foreach($dropdown_val as $k=>$drv) {
                        if(html_entity_decode($drv) == html_entity_decode($opt_chk)) {
                            $selectedkey = $k;
                            break;
                        }
                    }
                } else {
                    $element_select = array();
                    foreach ($dropdown_val1 as $va) {
                        $element_select[] = str_replace(',', '&#44;', htmlentities($va));
                    }
                    $dropdown_val = $element_select;//$dropdown_val1;
                }
                $pro = true;
                $valuessssss = "<input class='values' type='hidden' name='properties[" . $id . "][values]' id='" . $id . "_values' value='" . str_replace(',', '&#44;', $attr['values']) . "' />";
            }

            //give the text box a differnt label
            /*  508 Moving before Check box to add label in aria
                Code Starts
             * */
            if (isset($attr['label']) && $attr['label'] != "") {
                /*if ($attr['type'] == 'text') {
                    $contents = rawurldecode($contents);
                    $label = $contents;
                } else {*/
                    $attr['label'] = rawurldecode($attr['label']);
                    $label = htmlentities($attr['label']);
                /*}*/
                $pro = true;
            } else {
                $label = ($attr['type'] == 'text') ? 'No Text Heading' : 'No Label';
            }
            if (isset($attr['edit']) && $attr['edit'] != '') {
                /*if (isset($contents) && $contents != '') {
                    $contents = html_entity_decode(rawurldecode($contents));
                    $label = str_replace(",", "", $contents);
                }*/
                $remove[] = "'";
                $remove[] = '"';
                //$remove[] = "-";
                $label = str_replace($remove, "", $label);
            }
            /* Code Ends*/

            if (isset($attr['value']) && $attr['value'] != '') {
                $attr['value'] = rawurldecode($attr['value']);
                $btn_value = $attr['value'];
                $pro = true;
                $valuessssss = "<input class='value' type='hidden' name='properties[" . $id . "][value]' id='" . $id . "_value' value='" . $attr['value'] . "' />";
            }
            $lbl_id = 'lbl_chkbx_'.uniqid();
            $element_ck = '<input type="checkbox" name="temp[values][]" id="'.$lbl_id.'"><label for="'.$lbl_id.'" aria-labelledby="'.$id.' "></label>';
//            $element_ck = '<input type="checkbox" name="temp[values][]" id="chk_sp"><label for="chk_sp"></label>';
            $requiredattr = '';
            $requiredtext = '';

            if (isset($attr['required']) && $attr['required'] == 0 && ($attr['type'] == 'checkbox' || $attr['type'] == 'radio')) {
                if ($attr['type'] != 'text') {
                    $requiredtext = 'title="this field is required"';
                    $requiredattr = 'required="required"';
                }
            }

            if ($attr['type'] == 'checkbox') {
                if (isset($attr['values']) && $attr['values'] != '') {
                    $element_chk = '';
                    foreach ($dropdown_val as $va) {
                        $chkboxVal = str_replace(',', '&#44;', $va);
                        $opt_chks = explode(",", $opt_chk);
                        $chkChecked = "";
                        //if (in_array($chkboxVal, $opt_chks)) {
                          //  $chkChecked = "checked='checked'";
                        //}
                        foreach($opt_chks as $optchk){
                            if(html_entity_decode($optchk) == html_entity_decode($chkboxVal)){
                                $chkChecked = "checked='checked'";
                                break;
                            }
                        }
                         $lbl_id = 'lbl_chkbx_'.uniqid();
                         $opText = str_replace(',', '&#44;', $va);
                        if ($element_chk == '')
                            $element_chk = '<input type="checkbox" '.$requiredtext.' '.$requiredattr.' value="' . $chkboxVal . '" class="cls_' . $id . '" name="temp[values][' . $id . '][]" ' . $chkChecked . ' id="'.$lbl_id.'" ><label class="form_label fbg-label" for="'.$lbl_id.'"><span class="cls_value_'. $id.'">' . $opText . '</span></label><br>';
                        else
                            $element_chk.='<input type="checkbox" '.$requiredtext.' '.$requiredattr.' value="' . $chkboxVal . '" class="cls_' . $id . '" name="temp[values][' . $id . '][]" ' . $chkChecked . ' id="'.$lbl_id.'" ><label class="form_label fbg-label" for="'.$lbl_id.'"><span class="cls_value_'. $id.'">' . $opText . '</span></label><br>';
                    }
                    if ($element_chk != '')
                        $element_ck = $element_chk;
                }
            }
            $element_ra = '<input type="radio" id="rdo_sp"><label for="rdo_sp"></label>';
            if ($attr['type'] == 'radio') {
                if (isset($attr['values']) && $attr['values'] != '') {
                    $element_rad = '';
                    foreach ($dropdown_val as $va) {
                        $radioVal = str_replace(',', '&#44;', $va);

                        $radioChecked = "";
                        if (html_entity_decode($radioVal) == html_entity_decode($opt_chk)) {
                            $radioChecked = 'checked="checked"';
                        }
                        $radioId = 'lbl_chkbx_'.uniqid();
                        $optText= str_replace(',', '&#44;', $va);
                        if ($element_rad == '')
                            $element_rad = '<input type="radio" '.$requiredtext.$requiredattr.' value="' . $radioVal . '" class="cls_' . $id . '" name="temp[values][' . $id . ']" ' . $radioChecked . ' id="'.$radioId.'"/><label class="form_label fbg-label" for="'.$radioId.'"><span class="cls_value_'. $id.'">' . $optText . '</span></label><br/>';
                        else
                            $element_rad.='<input type="radio" '.$requiredtext.$requiredattr.' value="' . $radioVal . '" class="cls_' . $id . '" name="temp[values][' . $id . ']" ' . $radioChecked . ' id="'.$radioId.'"/><label class="form_label fbg-label" for="'.$radioId.'"><span class="cls_value_'. $id.'">' . $optText . '</span></label><br/>';
                    }
                    if ($element_rad != '')
                        $element_ra = $element_rad;
                }
            }

            /* test */
            $textbox_array = array('name' => $id, 'id'=>'lbl-'.$id,'maxlength'=>'255', 'class' => 'jf_text form_role_input instruction_single255 form-control ', 'value' => $default_answer);
            $textbox_numeric_array = array('name' => $id, 'id'=>'lbl-'.$id,'maxlength'=>'255', 'class' => 'jf_text form_role_input instruction_single255 form-control user_input numeric-field-qu negative-key', 'value' => $default_answer);
            if(isset($attr['required']) && $attr['required'] == 0 && ($attr['type'] == 'textbox' || $attr['type'] == 'number')){
                $textbox_array = array_merge($textbox_array,array('required' => 'true'));
                $textbox_numeric_array = array_merge($textbox_numeric_array, array('required' => 'true'));
            }

            $contents = '';
            if (isset($attr['edit']) && $attr['edit'] != '') {
                if (isset($attr['text_val']) && $attr['text_val'] != ""){
                    $contents = html_entity_decode(str_replace("\n", "<br>", htmlspecialchars($attr['text_val']))); //$attr['text_val'];
                    if($attr['type'] == 'text'){
                        $label = $contents;
                    }
                }

                //echo $attr['type']; die;
                switch ($attr['type']) {
                    case 'text':
                        $lable_class = 'text_lable_class';
                        $element = null;
                        break;
                    case 'textarea':
                        $element = form_textarea(array(
                            'name' => $id,
                            'rows' => 5,
                            'cols' => 40,
                            'class' => 'wysiwygNoEditor instruction_multi10000 form-control ',
                            'maxlength'=>'10000',
                            'value' => $default_answer
                        ));
                        break;
                    case 'textbox':
                                               // $element = form_input(array('name' => $id, 'id'=>'lbl-'.$id,'maxlength'=>'255', 'class' => 'jf_text form_role_input instruction_single255 form-control ', 'value' => $default_answer));
                                               $element = form_input($textbox_array);
                        break;
                    case 'number':
						//$element = form_input(array('name' => $id,'id'=>'lbl-'.$id,'maxlength'=>'255', 'class' => 'jf_text form_role_input instruction_single255 form-control user_input numeric-field-qu negative-key', 'value' => $default_answer));
                                                 $element = form_input($textbox_numeric_array);
                        break;
                    case 'dropdown': $element = form_dropdown($id, $dropdown_val, $selectedkey);
                        break;
                    case 'checkbox': $element = '<span class="values ' . $id . '" style="display:inline-block;">' . $element_ck . '</span>';
                        break;
                    case 'radio': $element = '<span class="values ' . $id . '" style="display:inline-block;">' . $element_ra . '</span>';
                        break;
                    case 'datetime': $element = '<div class="input-group calender-group">'.form_input(array('id'=>'lbl-'.$id,'name' => $id, 'placeholder' => 'Choose a date', 'class' => 'datepickers form-control ','readonly'=>'readonly')).'</div>';
                        break;
                    case 'fileupload': $element = form_upload($id);
                        break;
                    case 'button': $element = form_input(array('name' => $id, 'value' => $btn_value, 'type' => 'button'));
                        break;
                    default: $element = null;
                        break;
                }
            } else {

                switch ($attr['type']) {
                    case 'text':
                        $lable_class = 'text_lable_class';
                        $element = null;
                        break;
                    case 'textarea':
                        $element = form_textarea(array(
                            'name' => $id,
                            'rows' => 5,
                            'cols' => 40,
                            'id'=>'lbl-'.$id,
                            'class' => 'wysiwygNoEditor instruction_multi10000 form-control ',
                            'maxlength'=>'10000',
                            'value' => $default_answer,
                        ));
                        break;
                    case 'textbox': $element = form_input(array('name' => $id,'id'=>'lbl-'.$id, 'maxlength'=>'255','class' => 'jf_text instruction_single255 form-control ', 'value' => $default_answer));
                        break;
                    case 'number': $element = form_input(array('name' => $id,'id'=>'lbl-'.$id, 'maxlength'=>'255','class' => 'jf_text instruction_single255 form-control user_input numeric-field-qu number-key', 'value' => $default_answer));
                        break;
                    case 'dropdown': $element = form_dropdown($id, $dropdown_val);
                        break;
                    case 'checkbox': $element = '<span class="values ' . $id . '" style="display:inline-block;">' . $element_ck . '</span>';
                        break;
                    case 'radio': $element = '<span class="values ' . $id . '" style="display:inline-block;">' . $element_ra . '</span>';
                        break;
                    case 'datetime': $element = '<div class="input-group calender-group">'.form_input(array('id'=>'lbl-'.$id,'name' => $id, 'placeholder' => 'Choose a date', 'class' => 'datepickers form-control ','readonly'=>'readonly')).'</div>';
                        break;
                    case 'fileupload': $element = form_upload($id);
                        break;
                    case 'button': $element = form_input(array('name' => $id, 'value' => $btn_value, 'type' => 'button'));
                        break;
                    default: $element = null;
                        break;
                }
            }

            $text_val = "";
            $order = microtime(true);
            $order = str_replace(".", "", $order);

            /** START : IRT 446 **/
            $email_view = '';
            if($attr['form_type']==1 || $attr['form_type']==2){
                $element_class = 'fa fa-eye-slash text-primary';
                $title='Will Not Appear in Email Alerts';
                if($attr['element_view']==1){ $element_class = 'fa fa-eye text-primary'; $title='Will Appear in Applicable Email Alerts'; }
                $email_view = "<a title='".$title."' id='view_".$id."' href='javascript:void(0);' class='toolbox icon-set' onClick='show_email_field(\"$id\", \"$element_view\");'><em class='".$element_class."' title='Show Email/Hide Email'></em><span class='sr-only'>Show Email/Hide Email</span></a>";
            }
            /** END : IRT 446 **/

            $label_req_span="";
            $required_star = '';
            $required_screen = '';
            $required_star_span = '';
            if (isset($attr['required']) && $attr['required'] == 0) {
                if ($attr['type'] != 'text') {
                    $label_req_span="";
                    $required_star = 'required';
                    $required_star_span = '<span class="required" aria-label="Required">*</span>';
                    $required_screen = 'aria-label="Required"';
                }
            }

            $fieldsetstart = '';
            $fieldsetend = '';
            if ($attr['type'] == 'radio' || $attr['type'] == 'checkbox') {
                $fieldsetstart = "<fieldset role='".$attr['type']."group' $required_screen><legend class='sr-only'>$label</legend>";
                $fieldsetend = '</fieldset>';
            }
            if (isset($attr['edit']) && $attr['edit'] != '') {
                if (isset($attr['order']) && $attr['order'] != '')
                    $order = $attr['order'];


                $chk = '';
                if ($required == 0)
                    $chk = 'checked=checked';

                $chknoloadpre = '';
                if ($no_load_more == 1) {
                    $chknoloadpre = 'checked=checked';
                }

                $colsm=($attr['type'] == 'text')?9:3;
                $colsmnext=($attr['type'] == 'text')?1:7;
                /* By HNL <a href='javascript:void(0);' id='" . $id . "' onclick=updateall(); title='".strip_tags($label)."' style='cursor: default !important;'>" . $label . " ".$label_req_span."</a> */


                $output = "<li data-id='" . $id . "'>
							<div class='row border-saprater'>
                                                            <div class='col-md-$colsm'>"
                    . "<label class='" . $lable_class . " form_label' for='lbl-" . $id . "'>
                                                            <span id='" . $id . "' title='".strip_tags($label)."'>" . $label . " ".$label_req_span."</span>
                                                            $required_star_span
                                                            </label></div>
                                                            <div class='block col-md-$colsmnext'>
                                                                    $fieldsetstart" . $element . "$fieldsetend
                                                                    <span class='note " . $id . "'>$description</span>
                                                            </div>
                                                            <div class='col-md-2 text-right handle'>".$email_view."
                                                                <a title='Move' href='javascript:void(0);'  class='toolbox icon-set'><em class='fa fa-arrows text-primary' title='Move'></em><span class='screenreader'>Move</span></a>
                                                                <a title='Edit' href='javascript:void(0);' onclick=$('" . $id . "').trigger('click'); class='properties toolbox icon-set' rel='" . $attr['type'] . "'><em class='fa fa-pencil text-primary' for='" . $id . "'  title='Edit'>&nbsp;</em><span class='screenreader'>Edit</span></a>
                                                                <a class='icon-set' title='Remove' href='javascript:void(0);' onclick='deleteformbuilderfield(this,\"$id\");'><em class='fa fa-close text-primary' title='Remove'></em><span class='screenreader'>Close</span></a>
                                                            </div>
							</div>
							<div class='clear'></div>
							<div class='attrs clear " . $id . "'>
							<input type='hidden' name='properties[" . $id . "][type]' value='" . $attr['type'] . "'/>
							<input type='hidden' name='properties[" . $id . "][order]' value='" . $order . "' />";
                if (isset($contents) && $contents != '') {
                    $output .= "<input type='hidden' name='properties[" . $id . "][label]' class='label' value='' />" . $valuessssss;
                } else {
                    $output .="<input type='hidden' name='properties[" . $id . "][label]' class='label' value='" . $label . "' />" . $valuessssss;
                }

                $output .="<input type='hidden' name='properties[" . $id . "][required]' class='required' value='" . $required . "' id='required_" . $id . "' " . $chk . "  / >
                            <input type='hidden' name='properties[" . $id . "][no_load_prev]' value='" . $no_load_more . "' id='no_load_prev_" . $id . "' " . $chknoloadpre . "  / >
                            <input type='hidden' name='properties[" . $id . "][required_vars]' class='required_vars' value='" . $required_vars . "' />
                            <input type='hidden' name='properties[" . $id . "][element_view]'  value='" . $element_view . "' id='hide_" . $id . "' />
                            <input type='hidden' name='properties[" . $id . "][qareportuse]'  value='" . $qareportuse . "' id='qareportuse_" . $id . "' />
                            <input type='hidden' name='properties[" . $id . "][description]' id='description_" . $id . "' class='description' value='" . $description . "'>
                            <input type='hidden' name='properties[" . $id . "][sync_prod]' id='sync_prod_" . $id . "' class='sync_prod' value='" . $sync_prods . "'>
                            <input type='hidden' name='properties[" . $id . "][optionchk]' id='optionchk_" . $id . "' class='optionchk' value='" . $opt_chk . "'>
                            <input type='hidden' name='properties[" . $id . "][text_val]' id='" . $id . "_text_val' class='text_val' value='" . rawurlencode(htmlentities($contents)) . "'>
                            <input type='hidden' name='properties[" . $id . "][default_answer]' id='default_answer_" . $id . "' class='default_answer' value='" . $default_answer . "'>
                            <input type='hidden' name='properties[" . $id . "][default_unit]' id='default_unit_" . $id . "' class='default_unit' value='" . $default_unit . "'>
                    </div>
            </li>
    ";
            } else {


                $output = "
						<li data-id='" . $id . "'>
						<div class='row border-saprater'>
							<div class='col-md-$colsm'><label for='" . $id . "' class='" . $lable_class . " form_label'>
							<span id='" . $id . "' title='".strip_tags($label)."'>" . $label . "</span></label></div>
							<div class='block col-md-$colsmnext'>
								$fieldsetstart" . $element . "$fieldsetend
								<span class='note " . $id . "'></span>
							</div>
							<div class='col-md-2 text-right handle'>
                                                                        ".$email_view."<a title='Move' href='javascript:void(0);'  class='toolbox icon-set'><em class='fa fa-arrows text-primary' title='Move'></em><span class='screenreader'>Move</span></a>
					  				<a title='Edit' href='javascript:void(0);' onclick=$('" . $id . "').trigger('click'); class='icon-set properties toolbox' rel='" . $attr['type'] . "'><em class='fa fa-pencil text-primary' for='" . $id . "' title='Edit'>&nbsp;</em><span class='screenreader'>Edit</span></a>
					  				<a class='icon-set' title='Remove' href='javascript:void(0);' onclick='deleteformbuilderfield(this,\"$id\");'><em class='fa fa-close text-primary' title='Remove'></em><span class='screenreader'>Close</span></a>
					  		</div>
						</div>
							<div class='clear'></div>
							<div class='attrs clear " . $id . "'>
								<input type='hidden' name='properties[" . $id . "][type]' value='" . $attr['type'] . "'/>
								<input type='hidden' name='properties[" . $id . "][order]' value='" . $order . "'/>
								<input type='hidden' name='properties[" . $id . "][text_val]' id='" . $id . "_text_val' class='text_val' value='" . $contents . "'>
								<input type='hidden' id='required_" . $id . "' name='properties[" . $id . "][required]' class='required' value='1'>
								<input type='hidden' name='properties[" . $id . "][qareportuse]'   value='" . $qareportuse . "' id='qareportuse_" . $id . "' />
								<input type='hidden' name='properties[" . $id . "][no_load_prev]' value='0' id='no_load_prev_" . $id . "'  / >
								<input type='hidden' name='properties[" . $id . "][default_answer]' id='default_answer_" . $id . "' class='default_answer' value='" . $default_answer . "'>
								<input type='hidden' name='properties[" . $id . "][default_unit]' id='default_unit_" . $id . "' class='default_unit' value='" . $default_unit . "'>
							</div>

						</li>
					";
            }
            $elements_output[$id] = rawurldecode(($output));
        }
        //echo "<pre>";print_r($elements_output);echo "</pre>";
        echo json_encode($elements_output);
        die;
    }

    function element_copy($attributes)
    {
        $elements_output = array();
        $elemoutput = '';

        foreach ($attributes as $attr) {
            //echo "<pre>",print_r($attr),"</pre>";die;
			if(!is_array($attr))
				continue;
            $lable_class = '';
            $valuessssss = '';
            $required = '1';
            $qareportuse = '0';
            $no_load_more = '0';
            $required_vars = '';
            $element_view = '0';
            $description = '';
            $sync_prods = '';
            //$field_types = '';
            $opt_chk = '';
            $default_answer = '';
            $default_unit = 0;

            if (isset($attr['id']) && $attr['id'] != '')
                $id = $attr['id'];
            else
                $id = 'element_' . uniqid();

            $dropdown_val = array('' => 'No Content');
            $selectedkey = array('');
            $btn_value = 'No Content';
            //if(isset($attr['required']) && $attr['required']!='')
            if (isset($attr['no_load_prev']) && $attr['no_load_prev'] != '') {
                $attr['no_load_prev'] = rawurldecode($attr['no_load_prev']);
                $no_load_more = $attr['no_load_prev'];
            }
            if (isset($attr['required']) && $attr['required'] != '') {
                $attr['required'] = rawurldecode($attr['required']);
                $required = $attr['required'];
            }
            if (isset($attr['qareportuse']) && $attr['qareportuse'] != '') {
                $attr['qareportuse'] = ($attr['qareportuse']);
                $qareportuse = $attr['qareportuse'];
            }
            if (isset($attr['required_vars']) && $attr['required_vars'] != '') {
                $attr['required_vars'] = rawurldecode($attr['required_vars']);
                $required_vars = $attr['required_vars'];
            }
            if (isset($attr['element_view']) && $attr['element_view'] != '') {
                $attr['element_view'] = rawurldecode($attr['element_view']);
                $element_view = $attr['element_view'];
            }
            if (isset($attr['description']) && $attr['description'] != '') {
                $attr['description'] = rawurldecode($attr['description']);
                $description = $attr['description'];
            }
            if (isset($attr['sync_prod']) && $attr['sync_prod'] != '') {
                $attr['sync_prod'] = rawurldecode($attr['sync_prod']);
                $sync_prods = $attr['sync_prod'];
            }
            $default_unit = 0;
            /*if (isset($attr['field_type']) && $attr['field_type'] != '') {
                $attr['field_type'] = rawurldecode($attr['field_type']);
                $field_types = $attr['field_type'];
                if($attr['field_type'] == 'number' || $attr['field_type'] == 1){
                    $field_types = 1;
                    if (isset($attr['default_unit']) && $attr['default_unit'] != '') {
						$default_unit = $attr['default_unit'];
					}
                }
            }*/
            if (isset($attr['optionchk']) && $attr['optionchk'] != '') {
                $attr['optionchk'] = rawurldecode($attr['optionchk']);
                $opt_chk = $attr['optionchk'];
            }

			if (isset($attr['default_answer']) && $attr['default_answer'] != '') {
                $attr['default_answer'] = rawurldecode($attr['default_answer']);
                $default_answer = $attr['default_answer'];
            }

            if (isset($attr['default_unit']) && $attr['default_unit'] != '') {
                $attr['default_unit'] = rawurldecode($attr['default_unit']);
                $default_unit = $attr['default_unit'];
            }

            if (isset($attr['values']) && $attr['values'] != '') {
                $attr['values'] = rawurldecode($attr['values']);
                $attr['values'] = str_replace('&amp;', '&', $attr['values']);
                $dropdown_val1 = explode(';', html_entity_decode($attr['values']));
                /* $dropdown_val=array_merge(array('0'=>'Please Select'),$dropdown_val1); */
                if ($attr['type'] == "dropdown") {
                    $element_select = array();
                    foreach ($dropdown_val1 as $va) {
                        $element_select[] = str_replace(',', '&#44;', htmlentities($va));
                    }
                    $dropdown_val = array_merge(array('0' => 'Please Select'), $element_select);
                    $selectedkey = array_search($opt_chk, $dropdown_val);
                } else {
                    $element_select = array();
                    foreach ($dropdown_val1 as $va) {
                        $element_select[] = str_replace(',', '&#44;', htmlentities($va));
                    }
                    $dropdown_val = $element_select;
                }
                $pro = true;
                $valuessssss = "<input class='values' type='hidden' name='properties[" . $id . "][values]' id='" . $id . "_values' value='" . str_replace(',', '&#44;', $attr['values']) . "' />";
            }

            if (isset($attr['value']) && $attr['value'] != '') {
                $attr['value'] = rawurldecode($attr['value']);
                $btn_value = $attr['value'];
                $pro = true;
                $valuessssss = "<input class='value' type='hidden' name='properties[" . $id . "][value]' id='" . $id . "_value' value='" . $attr['value'] . "' />";
            }

            $element_ck = '<input type="checkbox" name="temp[values][]" id="chk_sp"><label for="chk_sp"></label>';

            if ($attr['type'] == 'checkbox') {
                if (isset($attr['values']) && $attr['values'] != '') {
                    $element_chk = '';
                    foreach ($dropdown_val as $va) {
                        $chkboxVal = str_replace(',', '&#44;', $va);
                        $opt_chks = explode(",", $opt_chk);
                        $chkChecked = "";
                        /*if (in_array($chkboxVal, $opt_chks)) {
                            $chkChecked = "checked='checked'";
                        }*/
                        foreach($opt_chks as $optchk){
                            if(html_entity_decode($optchk) == html_entity_decode($chkboxVal)){
                                $chkChecked = "checked='checked'";
                                break;
                            }
                        }
                        $checkBoxId = 'lbl_chkbx_'.uniqid();
                        if ($element_chk == '')
                            $element_chk = '<input type="checkbox" value="' . $chkboxVal . '" class="cls_' . $id . '" name="temp[values][' . $id . '][]" ' . $chkChecked . ' id="'.$checkBoxId.'"><label for="'.$checkBoxId.'" class="form_label fbg-label"><span class="cls_value_'. $id.'">' . str_replace(',', '&#44;', $va) . '</span></label><br>';
                        else
                            $element_chk.='<input type="checkbox" value="' . $chkboxVal . '" class="cls_' . $id . '" name="temp[values][' . $id . '][]" ' . $chkChecked . ' id="'.$checkBoxId.'"><label for="'.$checkBoxId.'" class="form_label fbg-label"><span class="cls_value_'. $id.'">' . str_replace(',', '&#44;', $va) . '</span></label><br>';
                    }
                    if ($element_chk != '')
                        $element_ck = $element_chk;
                }
            }
            $element_ra = '<input type="radio" id="rdo_sp"><label for="rdo_sp"></label>';
            if ($attr['type'] == 'radio') {
                if (isset($attr['values']) && $attr['values'] != '') {
                    $element_rad = '';
                    foreach ($dropdown_val as $va) {
                        $radioVal = str_replace(',', '&#44;', $va);

                        $radioChecked = "";
                        //if ($radioVal == $opt_chk) {
                        if (html_entity_decode($radioVal) == html_entity_decode($opt_chk)) {
                            $radioChecked = 'checked="checked"';
                        }
                        $radioId = 'lbl_chkbx_'.uniqid();
                        $optText= str_replace(',', '&#44;', $va);
                        if ($element_rad == '')
                            $element_rad = '<input type="radio" value="' . $radioVal . '" class="cls_' . $id . '" name="temp[values][' . $id . ']" ' . $radioChecked . ' id="'.$radioId.'"><label for="'.$radioId.'" class="form_label fbg-label"><span class="cls_value_'. $id.'">' . str_replace(',', '&#44;', $va) . '</span></label><br/>';
                        else
                            $element_rad.='<input type="radio" value="' . $radioVal . '" class="cls_' . $id . '" name="temp[values][' . $id . ']" ' . $radioChecked . ' id="'.$radioId.'"><label for="'.$radioId.'" class="form_label fbg-label"><span class="cls_value_'. $id.'">' . str_replace(',', '&#44;', $va) . '</span></label><br/>';
                    }
                    if ($element_rad != '')
                        $element_ra = $element_rad;
                }
            }
            $contents = '';
            if (isset($attr['edit']) && $attr['edit'] != '') {
                if (isset($attr['text_val']) && $attr['text_val'] != "")
                    $contents = html_entity_decode(str_replace("\n", "<br>", htmlspecialchars($attr['text_val']))); //$attr['text_val'];


                switch ($attr['type']) {
                    case 'text':
                        $lable_class = 'text_lable_class';
                        $element = null;
                        break;
                    case 'textarea':
                        $element = form_textarea(array(
                            'name' => $id,
                            'rows' => 5,
                            'cols' => 40,
                            'id'=>'lbl-'.$id,
                            'class' => 'wysiwygNoEditor instruction_multi10000 form-control ',
                            'maxlength'=>'10000',
                            'value' => $default_answer
                        ));
                        break;
                    case 'textbox':
						$element = form_input(array('name' => $id,'id'=>'lbl-'.$id,'maxlength'=>'255', 'class' => 'jf_text form_role_input instruction_single255 form-control ', 'value' => $default_answer));
                        break;
                    case 'number':
						$element = form_input(array('name' => $id,'id'=>'lbl-'.$id,'maxlength'=>'255', 'class' => 'jf_text form_role_input instruction_single255 form-control user_input numeric-field-qu negative-key', 'value' => $default_answer));
                        break;
                    case 'dropdown': $element = form_dropdown($id, $dropdown_val, $selectedkey);
                        break;
                    case 'checkbox': $element = '<span class="values ' . $id . '" style="display:inline-block;">' . $element_ck . '</span>';
                        break;
                    case 'radio': $element = '<span class="values ' . $id . '" style="display:inline-block;">' . $element_ra . '</span>';
                        break;
                    case 'datetime': $element = '<div class="input-group calender-group">'.form_input(array('id'=>'lbl-'.$id,'name' => $id, 'placeholder' => 'Choose a date', 'class' => 'datepickers form-control ','readonly'=>'readonly')).'</div>';
                        break;
                    case 'fileupload': $element = form_upload($id);
                        break;
                    case 'button': $element = form_input(array('name' => $id, 'value' => $btn_value, 'type' => 'button'));
                        break;
                    default: $element = null;
                        break;
                }
            } else {
                switch ($attr['type']) {
                    case 'text':
                        $lable_class = 'text_lable_class';
                        $element = null;
                        break;
                    case 'textarea':
                        $element = form_textarea(array(
                            'name' => $id,
                            'rows' => 5,
                            'cols' => 40,
                            'id'=>'lbl-'.$id,
                            'class' => 'wysiwygNoEditor instruction_multi10000 form-control ',
                            'maxlength'=>'10000',
                            'value' => $default_answer,
                        ));
                        break;
                    case 'textbox': $element = form_input(array('name' => $id,'id'=>'lbl-'.$id, 'maxlength'=>'255','class' => 'jf_text instruction_single255 form-control ', 'value' => $default_answer));
                        break;
                    case 'number': $element = form_input(array('name' => $id,'id'=>'lbl-'.$id, 'maxlength'=>'255','class' => 'jf_text instruction_single255 form-control user_input numeric-field-qu number-key', 'value' => $default_answer));
                        break;
                    case 'dropdown': $element = form_dropdown($id, $dropdown_val);
                        break;
                    case 'checkbox': $element = '<span class="values ' . $id . '" style="display:inline-block;">' . $element_ck . '</span>';
                        break;
                    case 'radio': $element = '<span class="values ' . $id . '" style="display:inline-block;">' . $element_ra . '</span>';
                        break;
                    case 'datetime': $element = '<div class="input-group calender-group">'.form_input(array('id'=>'lbl-'.$id,'name' => $id, 'placeholder' => 'Choose a date', 'class' => 'datepickers form-control ','readonly'=>'readonly')).'</div>';
                        break;
                    case 'fileupload': $element = form_upload($id);
                        break;
                    case 'button': $element = form_input(array('name' => $id, 'value' => $btn_value, 'type' => 'button'));
                        break;
                    default: $element = null;
                        break;
                }
            }

            $email_view = '';
        if($attr['form_type']!=3){
            $email_view="<a title='Will Not Appear in Email Alerts' href='javascript:void(0);' id='view_".$id."' tabindex='-1' class='toolbox icon-set' onClick='show_email_field(\"$id\", 0);'><em class='fa fa-eye-slash text-primary' title='Show Email/Hide Email'></em><span class='sr-only'>Show Email/Hide Email</span></a>";
        }
            //give the text box a differnt label
            if (isset($attr['label']) && $attr['label'] != "") {
                if ($attr['type'] == 'text') {
                    $contents = rawurldecode($contents);
                    $label = $contents;
                } else {
                    $attr['label'] = rawurldecode($attr['label']);
                    $label = ($attr['label']);
                }
                $pro = true;
            } else {
                $label = ($attr['type'] == 'text') ? 'No Text Heading' : 'No Label';
            }

            $text_val = "";
            $order = microtime(true);
            $order = str_replace(".", "", $order);
            if (isset($attr['edit']) && $attr['edit'] != '') {
                if (isset($attr['order']) && $attr['order'] != '')
                    $order = $attr['order'];

                if (isset($contents) && $contents != '') {
                    $contents = html_entity_decode(rawurldecode($contents));
                    $label = str_replace(",", "", $contents);
                }
                $chk = '';
                if ($required == 0)
                    $chk = 'checked=checked';

                $chknoloadpre = '';
                if ($no_load_more == 1) {
                    $chknoloadpre = 'checked=checked';
                }
                $remove[] = "'";
                $remove[] = '"';
                //$remove[] = "-";
                $label = str_replace($remove, "", $label);
                $label_req_span="";
                $required_star = '';
                if (isset($attr['required']) && $attr['required'] == 0) {
					if ($attr['type'] != 'text') {
						$label_req_span="";
						$required_star = 'required';
					}
				}
				$colsm=($attr['type'] == 'text')?9:3;
				$colsmnext=($attr['type'] == 'text')?1:7;
				/* By HNL <a href='javascript:void(0);' id='" . $id . "' onclick=updateall(); title='".strip_tags($label)."' style='cursor: default !important;'>" . $label . " ".$label_req_span."</a> */
                $output = "
						<li data-id='" . $id . "'>
							<div class='row border-saprater'>
								<div class='col-md-$colsm ".$required_star."'><label for='lbl-" . $id . "' class='" . $lable_class . " form_label'>
									<span id='" . $id . "' title='".strip_tags($label)."'>" . $label . " ".$label_req_span."</span>
									</label></div>
								<div class='block col-md-$colsmnext'>
									" . $element . "
									<span class='note " . $id . "'>$description</span>
								</div>
                                <div class='col-md-2 text-right handle'>".
                                $email_view."
									<a title='Move' href='javascript:void(0);'  class='toolbox icon-set'><em class='fa fa-arrows text-primary' title='Move'></em><span class='screenreader'>Move</span></a>
					  				<a title='Edit' href='javascript:void(0);' onclick=$('" . $id . "').trigger('click'); class='properties toolbox icon-set' rel='" . $attr['type'] . "'><em class='fa fa-pencil text-primary' for='" . $id . "' title='Edit'>&nbsp;</em><span class='screenreader'>Edit</span></a>
					  				<a class='icon-set' title='Remove' href='javascript:void(0);' onclick='deleteformbuilderfield(this,\"$id\");'><em class='fa fa-close text-primary' title='Remove'></em><span class='screenreader'>Close</span></a>
					  			</div>
							</div>
							<div class='clear'></div>
							<div class='attrs clear " . $id . "'>
								<input type='hidden' name='properties[" . $id . "][type]' value='" . $attr['type'] . "'/>
								<input type='hidden' name='properties[" . $id . "][order]' value='" . $order . "'/>";
                if (isset($contents) && $contents != '') {
                    $output .= "<input type='hidden' name='properties[" . $id . "][label]' class='label' value='' />" . $valuessssss;
                } else {
                    $output .="<input type='hidden' name='properties[" . $id . "][label]' class='label' value='" . $label . "' />" . $valuessssss;
                }

                $output .="<input type='hidden' name='properties[" . $id . "][required]' class='required' value='" . $required . "' id='required_" . $id . "' " . $chk . "  / >
								<input type='hidden' name='properties[" . $id . "][no_load_prev]' value='" . $no_load_more . "' id='no_load_prev_" . $id . "' " . $chknoloadpre . "  / >
                                <input type='hidden' name='properties[" . $id . "][required_vars]'  class='required_vars' value='" . $required_vars . "' />
                                <input type='hidden' name='properties[" . $id . "][element_view]'  class='element_view' value='" . $element_view . "' />
								<input type='hidden' name='properties[" . $id . "][qareportuse]'   value='" . $qareportuse . "' id='qareportuse_" . $id . "' />
								<input type='hidden' name='properties[" . $id . "][description]' id='description_" . $id . "' class='description' value='" . $description . "'>
								<input type='hidden' name='properties[" . $id . "][sync_prod]' id='sync_prod_" . $id . "' class='sync_prod' value='" . $sync_prods . "'>
								<input type='hidden' name='properties[" . $id . "][optionchk]' id='optionchk_" . $id . "' class='optionchk' value='" . $opt_chk . "'>
								<input type='hidden' name='properties[" . $id . "][text_val]' id='" . $id . "_text_val' class='text_val' value='" . rawurlencode(htmlentities($contents)) . "'>
								<input type='hidden' name='properties[" . $id . "][default_answer]' id='default_answer_" . $id . "' class='default_answer' value='" . $default_answer . "'>
								<input type='hidden' name='properties[" . $id . "][default_unit]' id='default_unit_" . $id . "' class='default_unit' value='" . $default_unit . "'>
							</div>
						</li>
					";
            } else {

                $output = "
						<li data-id='" . $id . "'>
						<div class='row border-saprater'>
							<div class='col-md-$colsm'><label for='" . $id . "' class='" . $lable_class . " form_label'>
							<span id='" . $id . "' title='".strip_tags($label)."'>" . $label . "</span></label></div>
							<div class='block col-md-$colsmnext'>
								" . $element . "
								<span class='note " . $id . "'></span>
							</div>
							<div class='col-md-2 text-right handle'>".
                            $email_view."
									<a title='Move' href='javascript:void(0);'  class='toolbox icon-set'><em class='fa fa-arrows text-primary' title='Move'></em><span class='screenreader'>Move</span></a>
					  				<a title='Edit' href='javascript:void(0);' onclick=$('" . $id . "').trigger('click'); class='icon-set properties toolbox' rel='" . $attr['type'] . "'><em class='fa fa-pencil text-primary' for='" . $id . "' title='Edit'>&nbsp;</em><span class='screenreader'>Edit</span></a>
					  				<a class='icon-set' title='Remove' href='javascript:void(0);' onclick='deleteformbuilderfield(this,\"$id\");'><em class='fa fa-close text-primary' title='Remove'></em><span class='screenreader'>Close</span></a>
					  		</div>
						</div>
							<div class='clear'></div>
							<div class='attrs clear " . $id . "'>
								<input type='hidden' name='properties[" . $id . "][type]' value='" . $attr['type'] . "'/>
								<input type='hidden' name='properties[" . $id . "][order]' value='" . $order . "'/>
								<input type='hidden' name='properties[" . $id . "][text_val]' id='" . $id . "_text_val' class='text_val' value='" . $contents . "'>
                                <input type='hidden' id='required_" . $id . "' name='properties[" . $id . "][required]' class='required' value='1'>
                                <input type='hidden' name='properties[" . $id . "][element_view]'  class='element_view' value='" . $element_view . "' />
								<input type='hidden' name='properties[" . $id . "][qareportuse]'   value='" . $qareportuse . "' id='qareportuse_" . $id . "' />
								<input type='hidden' name='properties[" . $id . "][no_load_prev]' value='0' id='no_load_prev_" . $id . "'  / >
								<input type='hidden' name='properties[" . $id . "][default_answer]' id='default_answer_" . $id . "' class='default_answer' value='" . $default_answer . "'>
								<input type='hidden' name='properties[" . $id . "][default_unit]' id='default_unit_" . $id . "' class='default_unit' value='" . $default_unit . "'>
							</div>

						</li>
					";
            }
            $elements_output[$id] .= rawurldecode(($output));
            //$elemoutput .= rawurldecode(($output));
        }
        //echo "<pre>";print_r($elements_output);echo "</pre>";
        //echo $elemoutput;
        echo json_encode($elements_output);
        die;
    }

    /* Display instruction form fields in project add / edit - enter form details step */
    function element_bulk_project($attributes) {
        $elements_output = array();
        //echo "<pre>",print_r($attributes),"</pre>";die;
        foreach ($attributes as $attr) {
            $lable_class = '';
            $div_class = 'col-md-3';
            $valuessssss = '';
            $required = '1';
            $qareportuse = '0';
            $no_load_more = '0';
            $required_vars = '';
            $description = '';
            $sync_prods = '';
            //$field_types = '';
            $opt_chk = '';
            if (isset($attr['id']) && $attr['id'] != '')
                $id = $attr['id'];
            else
                $id = 'element_' . uniqid();

            $dropdown_val = array('' => 'No Content');
            $selectedkey = array('');
            $btn_value = 'No Content';
            //if(isset($attr['required']) && $attr['required']!='')
            if (isset($attr['no_load_prevss']) && $attr['no_load_prevss'] != '') {
                $attr['no_load_prevss'] = rawurldecode($attr['no_load_prevss']);
                $no_load_more = $attr['no_load_prevss'];
            }
            if (isset($attr['required']) && $attr['required'] != '') {
                $attr['required'] = rawurldecode($attr['required']);
                $required = $attr['required'];
            }
            if (isset($attr['qareportuse']) && $attr['qareportuse'] != '') {
                $attr['qareportuse'] = ($attr['qareportuse']);
                $qareportuse = $attr['qareportuse'];
            }
            if (isset($attr['required_vars']) && $attr['required_vars'] != '') {
                $attr['required_vars'] = rawurldecode($attr['required_vars']);
                $required_vars = $attr['required_vars'];
            }
            if (isset($attr['description']) && $attr['description'] != '') {
                $attr['description'] = rawurldecode($attr['description']);
                $description = $attr['description'];
            }
            if (isset($attr['sync_prod']) && $attr['sync_prod'] != '') {
                $attr['sync_prod'] = rawurldecode($attr['sync_prod']);
                $sync_prods = $attr['sync_prod'];
            }
            /*if (isset($attr['field_type']) && $attr['field_type'] != '') {
                $attr['field_type'] = rawurldecode($attr['field_type']);
                $field_types = $attr['field_type'];
            }*/
            if (isset($attr['optionchk']) && $attr['optionchk'] != '') {
                $attr['optionchk'] = rawurldecode($attr['optionchk']);
                $opt_chk = $attr['optionchk'];
            }


            if (isset($attr['values']) && $attr['values'] != '') {
                $attr['values'] = rawurldecode($attr['values']);
                $attr['values'] = str_replace('&amp;', '&', $attr['values']);
                $dropdown_val1 = explode(';', $attr['values']);
                /* $dropdown_val=array_merge(array('0'=>'Please Select'),$dropdown_val1); */
                if ($attr['type'] == "dropdown") {
                    $element_select = array();
                    foreach ($dropdown_val1 as $va) {
                        $element_select[] = str_replace(',', '&#44;', $va);
                    }
                    $dropdown_val = array_merge(array('0' => 'Please Select'), $element_select);
                    $selectedkey = array_search($opt_chk, $dropdown_val);
                } else {
                    $dropdown_val = $dropdown_val1;
                }
                $pro = true;
                $valuessssss = "<input class='values' type='hidden' name='properties[" . $id . "][values]' id='" . $id . "_values' value='" . str_replace(',', '&#44;', $attr['values']) . "' />";
            }

            if (isset($attr['value']) && $attr['value'] != '') {
                $attr['value'] = rawurldecode($attr['value']);
                $btn_value = $attr['value'];
                $pro = true;
                $valuessssss = "<input class='value' type='hidden' name='properties[" . $id . "][value]' id='" . $id . "_value' value='" . $attr['value'] . "' />";
            }

            $element_ck = '<input type="checkbox" name="temp[values][]" id="chk_sp"><label for="chk_sp"></label>';

            if ($attr['type'] == 'checkbox') {
                if (isset($attr['values']) && $attr['values'] != '') {
                    $element_chk = '';
                    foreach ($dropdown_val as $va) {
                        $chkboxVal = str_replace(',', '&#44;', $va);
                        $opt_chks = explode(",", $opt_chk);
                        $chkChecked = "";
                        if (in_array($chkboxVal, $opt_chks)) {
                            $chkChecked = "checked='checked'";
                        }

                        if ($element_chk == '')
                            $element_chk = '<input type="checkbox" value="' . $chkboxVal . '" class="cls_' . $id . '" name="temp[values][' . $id . '][]" ' . $chkChecked . ' id="chk_sp_temp"><span class="cls_value_'. $id.'">' . str_replace(',', '&#44;', $va) . '</span><label for="chk_sp_temp"></label><br>';
                        else
                            $element_chk.='<input type="checkbox" value="' . $chkboxVal . '" class="cls_' . $id . '" name="temp[values][' . $id . '][]" ' . $chkChecked . ' id="chk_sp_temp"><span class="cls_value_'. $id.'">' . str_replace(',', '&#44;', $va) . '</span><label for="chk_sp_temp"></label><br>';
                    }
                    if ($element_chk != '')
                        $element_ck = $element_chk;
                }
            }
            $element_ra = '<input type="radio" id="rdo_sp"><label for="rdo_sp"></label>';
            if ($attr['type'] == 'radio') {
                if (isset($attr['values']) && $attr['values'] != '') {
                    $element_rad = '';
                    foreach ($dropdown_val as $va) {
                        $radioVal = str_replace(',', '&#44;', $va);

                        $radioChecked = "";
                        if ($radioVal == $opt_chk) {
                            $radioChecked = 'checked="checked"';
                        }

                        if ($element_rad == '')
                            $element_rad = '<input type="radio" value="' . $radioVal . '" class="cls_' . $id . '" name="temp[values][' . $id . ']" ' . $radioChecked . ' id="rdo_sp_temp"><span class="cls_value_'. $id.'">' . str_replace(',', '&#44;', $va) . '</span><label for="rdo_sp_temp"></label><br/>';
                        else
                            $element_rad.='<input type="radio" value="' . $radioVal . '" class="cls_' . $id . '" name="temp[values][' . $id . ']" ' . $radioChecked . ' id="rdo_sp_temp"><span class="cls_value_'. $id.'">' . str_replace(',', '&#44;', $va) . '</span><label for="rdo_sp_temp"></label><br/>';
                    }
                    if ($element_rad != '')
                        $element_ra = $element_rad;
                }
            }
            $contents = '';
            if (isset($attr['edit']) && $attr['edit'] != '') {
                if (isset($attr['text_val']) && $attr['text_val'] != "")
                    $contents = html_entity_decode(str_replace("\n", "<br>", $attr['text_val'])); //$attr['text_val'];


                switch ($attr['type']) {
                    case 'text':
                        $lable_class = 'text_lable_class';
                        $div_class = 'col-md-12';
                        $element = null;
                        break;
                    case 'textarea':
                        $element = form_textarea(array(
                            'name' => $id,
                            'rows' => 5,
                            'cols' => 40,
                            'id'=>'lbl-'.$id,
                            'class' => 'wysiwygNoEditor instruction_multi10000 form-control ',
                            'maxlength'=>'10000',
                        ));
                        break;
                    case 'textbox': $element = form_input(array('name' => $id,'id'=>'lbl-'.$id,'maxlength'=>'255', 'class' => 'jf_text form_role_input instruction_single255 form-control '));
                        break;
                    case 'number': $element = form_input(array('name' => $id,'id'=>'lbl-'.$id,'maxlength'=>'255', 'class' => 'jf_text form_role_input instruction_single255 form-control user_input numeric-field-qu negative-key'));
                        break;
                    case 'dropdown': $element = form_dropdown($id, $dropdown_val, $selectedkey);
                        break;
                    case 'checkbox': $element = '<span class="values ' . $id . '" style="display:inline-block;">' . $element_ck . '</span>';
                        break;
                    case 'radio': $element = '<span class="values ' . $id . '" style="display:inline-block;">' . $element_ra . '</span>';
                        break;
                    case 'datetime': $element = '<div class="input-group calender-group">'.form_input(array('id'=>'lbl-'.$id,'name' => $id, 'placeholder' => 'Choose a date', 'class' => 'datepickers form-control ','readonly'=>'readonly')).'</div>';
                        break;
                    case 'fileupload': $element = form_upload($id);
                        break;
                    case 'button': $element = form_input(array('name' => $id, 'value' => $btn_value, 'type' => 'button'));
                        break;
                    default: $element = null;
                        break;
                }
            } else {
                switch ($attr['type']) {
                    case 'text':
                        $lable_class = 'text_lable_class';
                        $div_class = 'col-md-12';
                        $element = null;
                        break;
                    case 'textarea':
                        $element = form_textarea(array(
                            'name' => $id,
                            'rows' => 5,
                            'cols' => 40,
                            'id'=>'lbl-'.$id,
                            'class' => 'wysiwygNoEditor instruction_multi10000 form-control ',
                            'maxlength'=>'10000',
                        ));
                        break;
                    case 'textbox': $element = form_input(array('name' => $id,'id'=>'lbl-'.$id, 'maxlength'=>'255','class' => 'jf_text instruction_single255 form-control '));
                        break;
                    case 'number': $element = form_input(array('name' => $id, 'id'=>'lbl-'.$id,'maxlength'=>'255','class' => 'jf_text instruction_single255 form-control user_input numeric-field-qu negative-key'));
                        break;
                    case 'dropdown': $element = form_dropdown($id, $dropdown_val);
                        break;
                    case 'checkbox': $element = '<span class="values ' . $id . '" style="display:inline-block;">' . $element_ck . '</span>';
                        break;
                    case 'radio': $element = '<span class="values ' . $id . '" style="display:inline-block;">' . $element_ra . '</span>';
                        break;
                    case 'datetime': $element = '<div class="input-group calender-group">'.form_input(array('id'=>'lbl-'.$id,'name' => $id, 'placeholder' => 'Choose a date', 'class' => 'datepickers form-control ','readonly'=>'readonly')).'</div>';
                        break;
                    case 'fileupload': $element = form_upload($id);
                        break;
                    case 'button': $element = form_input(array('name' => $id, 'value' => $btn_value, 'type' => 'button'));
                        break;
                    default: $element = null;
                        break;
                }
            }
            //give the text box a differnt label
            if (isset($attr['label']) && $attr['label'] != "") {
                if ($attr['type'] == 'text') {
                    $contents = rawurldecode($contents);
                    $label = $contents;
                } else {
                    $attr['label'] = rawurldecode($attr['label']);
                    $label = $attr['label'];
                }
                $pro = true;
            } else {
                $label = ($attr['type'] == 'text') ? 'No Text Heading' : 'No Label';
            }

            $text_val = "";
            $order = microtime(true);
            $order = str_replace(".", "", $order);
            if (isset($attr['edit']) && $attr['edit'] != '') {
                if (isset($attr['order']) && $attr['order'] != '')
                    $order = $attr['order'];

                if (isset($contents) && $contents != '') {
                    $contents = html_entity_decode(rawurldecode($contents));
                    $label = str_replace(",", "", $contents);
                }
                $chk = '';
                if ($required == 0)
                    $chk = 'checked=checked';

                $chknoloadpre = '';
                if ($no_load_more == 1) {
                    $chknoloadpre = 'checked=checked';
                }
                $remove[] = "'";
                $remove[] = '"';
                //$remove[] = "-";
                $label = str_replace($remove, "", $label);
                $colsm=($attr['type'] == 'text')?9:3;
				$colsmnext=($attr['type'] == 'text')?1:7;
                $output = "
						<li data-id='" . $id . "'>
							<div class='row border-saprater'>
								<div class='col-sm-$colsm'><label for='" . $id . "' class='" . $lable_class . " form_label'><span  id='" . $id . "' title='".strip_tags($label)."'>" . $label . "</span></label></div>
								<div class='block col-md-$colsmnext'>
									" . $element . "
									<span class='note " . $id . "'>$description</span>
								</div>
							</div>
							<div class='clear'></div>
							<div class='attrs clear " . $id . "'>
								<input type='hidden' name='properties[" . $id . "][type]' value='" . $attr['type'] . "'/>
								<input type='hidden' name='properties[" . $id . "][order]' value='" . $order . "'/>";
                if (isset($contents) && $contents != '') {
                    $output .= "<input type='hidden' name='properties[" . $id . "][label]' class='label' value='' />" . $valuessssss;
                } else {
                    $output .="<input type='hidden' name='properties[" . $id . "][label]' class='label' value='" . $label . "' />" . $valuessssss;
                }

                $output .="<input type='hidden' name='properties[" . $id . "][required]' class='required' value='" . $required . "' id='required_" . $id . "' " . $chk . "  / >
								<input type='hidden' name='properties[" . $id . "][no_load_prev]' value='" . $no_load_more . "' id='no_load_prev_" . $id . "' " . $chknoloadpre . "  / >
								<input type='hidden' name='properties[" . $id . "][required_vars]'  class='required_vars' value='" . $required_vars . "' />
								<input type='hidden' name='properties[" . $id . "][qareportuse]'   value='" . $qareportuse . "' id='qareportuse_" . $id . "' />
								<input type='hidden' name='properties[" . $id . "][description]' id='description_" . $id . "' class='description' value='" . $description . "'>
                                                                <input type='hidden' name='properties[" . $id . "][sync_prod]' id='sync_prod_" . $id . "' class='sync_prod' value='" . $sync_prods . "'>
                                                                <input type='hidden' name='properties[" . $id . "][optionchk]' id='optionchk_" . $id . "' class='optionchk' value='" . $opt_chk . "'>
								<input type='hidden' name='properties[" . $id . "][text_val]' id='" . $id . "_text_val' class='text_val' value='" . rawurlencode(htmlentities($contents)) . "'>
							</div>
						</li>
					";
            } else {

                $output = "
						<li data-id='" . $id . "'>
						<div class='row border-saprater'>
							<div class='col-sm-$colsm'><label for='" . $id . "' class='" . $lable_class . " form_label'><span id='" . $id . "' title='".strip_tags($label)."'>" . $label . "</span></label></div>
							<div class='block col-md-$colsmnext'>
								" . $element . "
								<span class='note " . $id . "'></span>
							</div>
						</div>
							<div class='clear'></div>
							<div class='attrs clear " . $id . "'>
								<input type='hidden' name='properties[" . $id . "][type]' value='" . $attr['type'] . "'/>
								<input type='hidden' name='properties[" . $id . "][order]' value='" . $order . "'/>
								<input type='hidden' name='properties[" . $id . "][text_val]' id='" . $id . "_text_val' class='text_val' value='" . $contents . "'>
								<input type='hidden' id='required_" . $id . "' name='properties[" . $id . "][required]' class='required' value='1'>
								<input type='hidden' name='properties[" . $id . "][qareportuse]'   value='" . $qareportuse . "' id='qareportuse_" . $id . "' />
								<input type='hidden' name='properties[" . $id . "][no_load_prev]' value='0' id='no_load_prev_" . $id . "'  / >
							</div>

						</li>
					";
            }
            $elements_output[$id] = rawurldecode(($output));
        }
        //echo "<pre>";print_r($elements_output);echo "</pre>";
        echo json_encode($elements_output);
        die;
    }
    /*
      Builds a list of properties for the builder to display.
     */

    function properties($attr)
    {
        $output = null;
        $type = $attr['type'];
        $id = $attr['id'];
        $label = '';
        $formtype = $attr['formtype'];
        //echo "<pre>"; print_r($attr); exit;

        if (isset($attr['label']) && $attr['label'] != "No Label") {
            $label = str_replace("<br>", "\n", $attr['label']);
          //  echo "<pre>",trim($label); die;
        }

        //$dropdown_val=array(''=>'No Content');
        $btn_value = ' ';
        if (isset($attr['values']) && $attr['values'] != '') {
            if (is_array($attr['values'])) {
                $dropdown_val1 = explode(';', html_entity_decode($attr['values']));
                if ($attr['type'] == "dropdown"){
                    $element_select = array();
                    foreach ($dropdown_val1 as $va) {
                        $element_select[] = str_replace(',', '&#44;', htmlentities($va));
                    }
                    $dropdown_val = array_merge(array('0' => 'Please Select'), $element_select);
                    //$dropdown_val = array_merge(array('0' => 'Please Select'), $dropdown_val1);
                }else{
                    $element_select = array();
                    foreach ($dropdown_val1 as $va) {
                        $element_select[] = str_replace(',', '&#44;', htmlentities($va));
                    }
                    $dropdown_val = $element_select;
                    //$dropdown_val1;
                }
            } else
                $dropdown_val = $attr['values'];
        }

        if (isset($attr['value']) && $attr['value'] != '') {
            $btn_value = $attr['value'];
        }
        $optionVal = "";
        if (isset($attr['vals']) && $attr['vals'] != '') {
            $optionVal = html_entity_decode($attr['vals']);
        }

        $option_values = "";
        if (isset($attr['optionValue']) && $attr['optionValue'] != '') {
            $option_values = html_entity_decode($attr['optionValue']);
        }

        $chk = '';
        $chk1 = '';
        $chk2 = '';
        $chkqau = '';
        if ($attr['req'] == 0)
            $chk = 'checked=checked';
        if ($attr['qau'] == 1)
            $chkqau = 'checked=checked';
        if ($attr['req'] == 'undefined')
            $chk = '';

        if ($attr['no_load_prev'] == 1)
            $chk1 = 'checked=checked';
        if ($attr['no_load_prev'] == 'undefined')
            $chk1 = '';

        $optVals = "";
        $syncprod = "";
        if (isset($attr['optionchk']) && $attr['optionchk'] != '') {
            $optVals = $attr['optionchk'];
        }
		//echo $optVals; exit;
        if (isset($attr['sync_prod']) && $attr['sync_prod'] != "") {
            $syncprod = $attr['sync_prod'];
        }

        /*if ($attr['type'] == 'textbox') {
            $options = array(
                'Label' => form_input(array('rel' => 'label[for=' . $id . '] span', 'name' => 'label','maxlength'=>'1000','class'=>'form-control '), $label),
                'Required' => array(
                    'Yes' => '<input class=" " type="checkbox" value="1" name="required" onclick="if(this.checked) this.value=0; else this.value=1;" ' . $chk . ' >', //form_checkbox('required','1'),
                    'Type' => form_dropdown('required_vars', array('' => 'Text', 'email' => 'Email', 'number' => 'Number','class'=>'form-control'))
                ),
                'No Load Previous' => array(
                    '' => '<input class=" " type="checkbox" value="0" name="no_load_prev" onclick="if(this.checked) this.value=1; else this.value=0;" ' . $chk1 . ' title="Do not copy field contents during Load Previous process when submitting new projects." >', //form_checkbox('required','1'),
                ),
                'Description' => form_input(array('name' => 'description','maxlength'=>'1000', 'rel' => '.note[class~=' . $id . ']','class'=>'form-control')),
            );

        }*/
        if ($attr['type'] == 'text') {
			// $element = $label;
			// echo "<pre>",print_r($attr['label']);
            $element = form_textarea(array(
                'class' => 'wysiwyg',
                'id' => $id . '_text',
                'name' => $id,
                'rows' => 5,
                'maxlength'=>'1000',
                'cols' => 40,
            ), $attr['label']);

            $options = array(
                '' => $element,
            );

            //Added Date:8-1-15
        } else if ($attr['type'] == 'textarea') {
            $options['Label'] = form_input(array('rel' => 'label[for=' . $id . '] span', 'aria-labelledby'=>'Labelby_'.$id, 'name' => 'label','maxlength'=>'1000','class'=>'form-control '), $label);
            $options['Required'] = array('' => '<input class=" " aria-labelledby="Requiredby_'.$id.'" type="checkbox" value="1" name="required" onclick="if(this.checked) this.value=0; else this.value=1;" ' . $chk . ' id="chk-required-'.$id.'" /><label class="sr-only" for="chk-required-'.$id.'">Required</label>');
            if($formtype == 'instructionform'){
				$options['No Load Previous'] = array('' => '<input class=" " aria-labelledby="No_Load_Previousby_'.$id.'" type="checkbox" value="0" name="no_load_prev" onclick="if(this.checked) this.value=1; else this.value=0;" ' . $chk1 . ' title="Do not copy field contents during Load Previous process when submitting new projects."  id="chk-no-load-prev-'.$id.'" /><label class="sr-only" for="chk-no-load-prev-'.$id.'">No Load Previous</label>', //form_checkbox('required','1'),
                );
			}
            $options['Description'] = form_input(array('name' => 'description', 'aria-labelledby'=>'Descriptionby_'.$id, 'maxlength'=>'1000','id' => 'description', 'rel' => '.note[class~=' . $id . ']','class'=>'form-control '));
			$options['Default Answer'] = array(''=>'<label for="Default_Answerby_'.$id.'" class="sr-only">Default Answer</label><textarea name="default_answer" aria-labelledby="Default_Answerby_'.$id.'" maxlength="10000" id="default_answer" rel=".default_answer[class~=' . $id . ']" data-element-id="'.$id.'" class="form-control" rows="5"></textarea>');

        } else if ($attr['type'] == 'datetime') {
            $options = array(
                'Label' => form_input(array('rel' => 'label[for=' . $id . '] span', 'aria-labelledby'=>'Labelby_'.$id, 'name' => 'label','maxlength'=>'1000','class'=>'form-control '), $label),
                'Required' => array('' => '<input class=" " type="checkbox" aria-labelledby="Requiredby_'.$id.'" value="1" name="required" onclick="if(this.checked) this.value=0; else this.value=1;" ' . $chk . ' id="chk-required-'.$id.'" /><label class="sr-only" for="chk-required-'.$id.'">Required</label>'),
                'No Load Previous' => array('' => '<input class=" " type="checkbox" aria-labelledby="No_Load_Previousby_'.$id.'" value="0" name="no_load_prev" onclick="if(this.checked) this.value=1; else this.value=0;" ' . $chk1 . ' title="Do not copy field contents during Load Previous process when submitting new projects." id="chk-no-load-prev-'.$id.'" /><label class="sr-only" for="chk-no-load-prev-'.$id.'">No Load Previous</label>', //form_checkbox('required','1'),
                ),
                'Description' => form_input(array('name' => 'description', 'aria-labelledby'=>'Descriptionby_'.$id,'maxlength'=>'1000', 'id' => 'description', 'rel' => '.note[class~=' . $id . ']','class'=>'form-control ')),
            );
            //Added Date:8-1-15
        } else if ($attr['type'] == 'textbox') {
            $options['Label'] = form_input(array('rel' => 'label[for=' . $id . '] span', 'aria-labelledby'=>'Labelby_'.$id, 'name' => 'label','maxlength'=>'1000','class'=>'form-control '), $label);
			$options['Required'] = array('' => '<input class=" " aria-labelledby="Requiredby_'.$id.'" type="checkbox" value="1" name="required" onclick="if(this.checked) this.value=0; else this.value=1;" ' . $chk . ' id="chk-required-'.$id.'" /><label class="sr-only" for="chk-required-'.$id.'">Required</label>');
			if($attr['formtype'] == 'instructionform'){
				$options['No Load Previous'] = array('' => '<input class=" " aria-labelledby="No_Load_Previousby_'.$id.'" type="checkbox" value="0" name="no_load_prev" onclick="if(this.checked) this.value=1; else this.value=0;" ' . $chk1 . ' title="Do not copy field contents during Load Previous process when submitting new projects." id="chk-no-load-prev-'.$id.'" /><label class="sr-only" for="chk-no-load-prev-'.$id.'">No Load Previous</label>', //form_checkbox('required','1'),
				);
			}
			if($attr['formtype'] == 'dataform'){
				//$options['Field Type'] = form_dropdown('field_type', array('' => '', 1 => 'Number'), $attr['field_type'], 'class="fieldtypeClass form-control " data-element-id="'.$id.'"');
				$options['Sync with Production Fields'] = form_dropdown('sync_prod', array('' => '', 'aria-labelledby'=>'Sync_with_Production_Fieldsby_'.$id, 'prod_begbates' => 'Prod BegBates', 'prod_endbates' => 'Prod EndBates', 'prod_vol' => 'Prod Volume'), $syncprod, 'class="fieldtypeClass form-control " data-element-id="'.$id.'"');
			}

			$options['Description'] = form_input(array('name' => 'description', 'aria-labelledby'=>'Descriptionby_'.$id, 'maxlength'=>'1000','id' => 'description', 'rel' => '.note[class~=' . $id . ']','class'=>'form-control '));

			$options['Default Answer'] = form_input(array('name' => 'default_answer', 'aria-labelledby'=>'Default_Answerby_'.$id, 'data-element-id' => $id, 'maxlength'=>'255','id' => 'default_answer', 'rel' => '.default_answer[class~=' . $id . ']','class'=>'form-control '));
			/*if($attr['formtype'] == 'dataform'){
				require(__DIR__ . '/../yii_init.php');
				$list = Yii::$app->db->createCommand('select * from tbl_unit WHERE remove=0')->queryAll();
			    $list_units = array(0 => '');
                foreach ($list as $unititem) {
                    $list_units[$unititem['id']] = $unititem['unit_name'];
                }

                $options['Default Unit'] = form_dropdown('default_unit', $list_units, $attr['default_unit'], 'class="default_unitClass form-control" '.$field_unit.' data-element-id="'.$id.'"');
			}*/
        } else if ($attr['type'] == 'number') {
            $options['Label'] = form_input(array('rel' => 'label[for=' . $id . '] span', 'aria-labelledby'=>'Labelby_'.$id, 'name' => 'label','maxlength'=>'1000','class'=>'form-control '), $label);
			$options['Required'] = array('' => '<input class=" " type="checkbox" value="1" aria-labelledby="Requiredby_'.$id.'" name="required" onclick="if(this.checked) this.value=0; else this.value=1;" ' . $chk . ' id="chk-required-'.$id.'" /><label class="sr-only" for="chk-required-'.$id.'">Required</label>');
			if($attr['formtype'] == 'instructionform'){
				$options['No Load Previous'] = array('' => '<input class=" " aria-labelledby="No_Load_Previousby_'.$id.'" type="checkbox" value="0" name="no_load_prev" onclick="if(this.checked) this.value=1; else this.value=0;" ' . $chk1 . ' title="Do not copy field contents during Load Previous process when submitting new projects."  id="chk-no-load-prev-'.$id.'" /><label class="sr-only" for="chk-no-load-prev-'.$id.'">No Load Previous</label>', //form_checkbox('required','1'),
				);
			}
			if($attr['formtype'] == 'dataform'){
				//$options['Field Type'] = form_dropdown('field_type', array('' => '', 1 => 'Number'), $attr['field_type'], 'class="fieldtypeClass form-control " data-element-id="'.$id.'"');
				$options['Sync with Production Fields'] = form_dropdown('sync_prod', array('' => '', 'prod_begbates' => 'Prod BegBates', 'prod_endbates' => 'Prod EndBates', 'prod_vol' => 'Prod Volume'), $syncprod, 'class="fieldtypeClass form-control " data-element-id="'.$id.'" aria-labelledby="Sync_with_Production_Fieldsby_'.$id.'"');
			}

			$options['Description'] = form_input(array('name' => 'description', 'aria-labelledby'=>'Descriptionby_'.$id, 'maxlength'=>'1000','id' => 'description', 'rel' => '.note[class~=' . $id . ']','class'=>'form-control '));

			$options['Default Answer'] = form_input(array('name' => 'default_answer', 'aria-labelledby'=>'Default_Answerby_'.$id, 'data-element-id' => $id, 'maxlength'=>'255','id' => 'default_answer', 'rel' => '.default_answer[class~=' . $id . ']','class'=>'form-control user_input numeric-field-qu negative-key'));
			//if($attr['formtype'] == 'dataform'){
				require(__DIR__ . '/../yii_init.php');
				$list = Yii::$app->db->createCommand('select * from tbl_unit WHERE remove=0')->queryAll();
			    $list_units = array(0 => '');
                foreach ($list as $unititem) {
                    $list_units[$unititem['id']] = $unititem['unit_name'];
                }

                $options['Default Unit'] = form_dropdown('default_unit', $list_units, $attr['default_unit'], 'class="default_unitClass form-control" '.$field_unit.' data-element-id="'.$id.'" aria-labelledby="Default_Unitby_'.$id.'"');

			//}
        } else if ($attr['type'] == "dropdown") {
            if ($optionVal != "undefined") {
                $optionValueArr = explode(";", html_entity_decode($optionVal));
                $optionTr = array();
                $c=1;
                foreach ($optionValueArr as $optValue) {
                    $optValue=htmlentities($optValue);
                    $pdropChecked = "";
                    if (html_entity_decode($optValue) == html_entity_decode($optVals)) {
                        $pdropChecked = "checked='checked'";
                    }
                    $classoddeven='odd';
                    $uniqid = uniqid();
                    if($c%2==0)$classoddeven='even';
                    $optionTr[] = '<tr class="'.$classoddeven.' newTr">
						<td width="85%">'.$optValue.'</td>
						<td><input type="checkbox" class="dropdown_btn" aria-label="'.$optValue.'" aria-labelledby="option_chk_label'.$uniqid.'" name="optionchk_name" value="' . $optValue . '" id="' . $id . '" '.$pdropChecked.' ><label for="' . $id . '" id="option_chk_label_'.$uniqid.'" class=""><span class="sr-only">'.$optValue.'</span></label></td>
						<td width="5%"><a href="javascript:void(0);" title="Remove Option" class="removeOptionText" id="' . $id . '"><em class="fa fa-close text-primary" title="Remove"></em><span class="screenreader">Close</span></a></td>
					</tr>';

                    /*$optionTr[] = '<tr class="'.$classoddeven.' newTr">
                                <td width="5%"><a style="cursor: pointer;" title="Remove Option" class="removeOptionText" id="' . $id . '"><em class="fa fa-times" ></em></a></td>
                                <td width="85%">' . $optValue . '</td>
                                <td><input type="checkbox" class="dropdown_btn" value="' . $optValue . '" id="' . $id . '" name="optionchk_name" ' . $pdropChecked . '></td>
                                </tr>';*/
                   	$c++;
                }
            } else if ($option_values != "undefined") {
                $optionValueArr = explode(";", html_entity_decode($option_values));
                $optionTr = array();
                $c=1;
                foreach ($optionValueArr as $optValue) {
                    $optValue=htmlentities($optValue);
                    $pdropChecked = "";
                    if (html_entity_decode($optValue) == html_entity_decode($optVals)) {
                        $pdropChecked = "checked='checked'";
                    }
                    $classoddeven='odd';
                    $uniqid = uniqid();
                    if($c%2==0)$classoddeven='even';
                    $optionTr[] = '<tr class="'.$classoddeven.' newTr">
						<td width="85%">'.$optValue.'</td>
						<td><input type="checkbox" class="dropdown_btn" aria-label="'.$optValue.'" aria-labelledby="option_chk_label'.$uniqid.'" name="optionchk_name" value="' . $optValue . '" id="' . $id . '" '.$pdropChecked.' ><label for="' . $id . '" id="option_chk_label_'.$uniqid.'" class=""><span class="sr-only">'.$optValue.'</span></label></td>
						<td width="5%"><a href="javascript:void(0);" title="Remove Option" class="removeOptionText" id="' . $id . '" ><em class="fa fa-close text-primary" title="Remove"></em><span class="screenreader">Close</span></a></td>
					</tr>';

                    /*$optionTr[] = '<tr class="newTr">
                                <td style="min-width:20px !important;"><a style="cursor: pointer;" class="removeOptionText" id="' . $id . '"><em class="fa fa-times" ></em></a></td>
                                <td style="min-width: 284px ! important; text-align: left; word-wrap: break-word; display: block; max-width: 284px ! important; width: 284px ! important;">' . $optValue . '</td>
                                <td style="min-width:40px !important;text-align:center"><input type="checkbox" class="dropdown_btn" value="' . $optValue . '" id="' . $id . '" name="optionchk_name" ' . $pdropChecked . '></td>
                                </tr>';*/
                    $c++;
                }
            } else {
            	$classoddeven = 'odd';
				/*$optionTr = '<tr class="'.$classoddeven.' newTr blank-hide-tr">
					<td width="85%"></td>
					<td></td>
					<td width="5%"></td>
				</tr>';*/

                /*$optionTr = '<tr class="removeTr">
                                <td style="min-width:20px !important;"></td>
                                <td style="min-width: 284px ! important; text-align: left; word-wrap: break-word; display: block; max-width: 284px ! important; width: 284px ! important;"></td>
                                <td style="min-width:40px !important;">&nbsp;</td>
                            </tr>';*/
            }
            if (is_array($optionTr)) {
                $optionTr = implode("", $optionTr);
            }
            $typeClass = "dropdown";
            if ($attr['qareport'] == 1) {
            	$options = array(
                    'Label' => form_input(array('rel' => 'label[for=' . $id . '] span', 'aria-labelledby'=>'Labelby_'.$id, 'name' => 'label','maxlength'=>'1000','class'=>'form-control '), $label),
                    'Required' => array('' => '<input class=" " type="checkbox" aria-labelledby="Requiredby_'.$id.'" value="1" name="required" onclick="if(this.checked) this.value=0; else this.value=1;" ' . $chk . '  id="chk-required-'.$id.'" /><label class="sr-only" for="chk-required-'.$id.'">Required</label>', //form_checkbox('required','1'),
                    ),
                    'QA Report Use' => array('' => '<input class=" " type="checkbox" aria-labelledby="QA_Report_Useby_'.$id.'" value="0" name="qareportuse" onclick="if(this.checked) this.value=1; else this.value=0;" ' . $chkqau . '  id="chk-qa-report-use-'.$id.'" /><label class="sr-only" for="chk-qa-report-use-'.$id.'">QA Report Use</label>', //form_checkbox('required','1'),
                    ),
                    'No Load Previous' => array('' => '<input class=" " type="checkbox" aria-labelledby="No_Load_Previousby_'.$id.'" value="0" name="no_load_prev" onclick="if(this.checked) this.value=1; else this.value=0;" ' . $chk1 . ' title="Do not copy field contents during Load Previous process when submitting new projects."  id="chk-no-load-prev-'.$id.'" /><label class="sr-only" for="chk-no-load-prev-'.$id.'">No Load Previous</label>', //form_checkbox('required','1'),
                    ),
                    //Added Date:8-1-15
                    //'Sync with Production Fields' => form_dropdown('sync_prod', array('' => '', 'prod_begbates' => 'Prod BegBates', 'prod_endbates' => 'Prod EndBates', 'prod_vol' => 'Prod Volume'), $syncprod, 'class="fieldtypeClass hide"'),
                    'Description' => form_input(array('name' => 'description', 'aria-labelledby'=>'Descriptionby_'.$id,'maxlength'=>'1000', 'id' => 'description', 'rel' => '.note[class~=' . $id . ']','class'=>'form-control ')),
                    'Options' => array(
                        form_input(array('name' => 'optionTextboxdropdown', 'aria-labelledby'=>'Optionsby_'.$id, 'class' => 'form-control pull-left','id' => 'optionTextboxAddVal','maxlength'=>'1000', 'style' => 'width:315px;', 'value' => '')) =>
                        '<div class="optionbtnClass pull-left">

                     	<button type="button" class="btn btn-primary" onclick="addOption(\'' . $id . '\',\'' . $typeClass . '\',\'' . $chk2 . '\');" role="button" aria-disabled="false"><span class="ui-button-text" style="margin-top:7px">Add</span></button></div>
                     	<br/><br/>
                     <div class="optionvalueHtml">
                     	<div class="row border-saprater">
							<div class="col-md-12">
								<div class="table-responsive table-tbody-scroll">
					  				<div id="example_wrapper" class="dataTables_wrapper no-footer">
						  				<table summary="include a summary of the content of the table here" class="display display-check-radio dataTable no-footer" id="example">
											<thead>
												<tr>
													<th width="85%" scope="col" class="sorting_disabled" role="columnheader" rowspan="1" colspan="1"><a href="javascript:void(0);" title="Option" class="tag-header-black">Option</a></th>
													<th scope="col" class="sorting_disabled" role="columnheader" rowspan="1" colspan="1"><a href="javascript:void(0);" title="Default" class="tag-header-black">Default</a></th>
                    								<th width="5%" scope="col" class="sorting_disabled" role="columnheader" rowspan="1" colspan="1"></th>
												</tr>
											</thead>
											<tbody class="tbodyClass_' . $id . '">'.$optionTr.'</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
                    </div>
                    <div style="display:none">' . form_textarea(array("name" => "values",'tab-index'=>-1,'maxlength'=>'1000', "class" => "dropdown", "rel" => 'select[name=' . $id . ']', "style" => "height: 100px;width:400px;")) . "<div>"
                    ),
                );
            } else {

                $options = array(
                    'Label' => form_input(array('rel' => 'label[for=' . $id . '] span', 'aria-labelledby'=>'Labelby_'.$id, 'name' => 'label','maxlength'=>'1000','class'=>'form-control '), $label),
                    'Required' => array('' => '<input class=" " type="checkbox" aria-labelledby="Requiredby_'.$id.'" value="1" name="required" onclick="if(this.checked) this.value=0; else this.value=1;" ' . $chk . '  id="chk-required-'.$id.'" /><label class="sr-only" for="chk-required-'.$id.'">Required</label>', //form_checkbox('required','1'),
                    ),
                    'No Load Previous' => array('' => '<input class=" " type="checkbox" aria-labelledby="No_Load_Previousby_'.$id.'" value="0" name="no_load_prev" onclick="if(this.checked) this.value=1; else this.value=0;" ' . $chk1 . ' title="Do not copy field contents during Load Previous process when submitting new projects."  id="chk-no-load-prev-'.$id.'" /><label class="sr-only" for="chk-no-load-prev-'.$id.'">No Load Previous</label>', //form_checkbox('required','1'),
                    ),
                    //Added Date:8-1-15
                    //'Sync with Production Fields' => form_dropdown('sync_prod', array('' => '', 'prod_begbates' => 'Prod BegBates', 'prod_endbates' => 'Prod EndBates', 'prod_vol' => 'Prod Volume'), $syncprod, 'class="fieldtypeClass hide"'),
                    'Description' => form_input(array('name' => 'description', 'aria-labelledby'=>'Descriptionby_'.$id, 'maxlength'=>'1000','id' => 'description', 'rel' => '.note[class~=' . $id . ']','class'=>'form-control ')),
                    'Options' => array(
                        form_input(array('name' => 'optionTextboxdropdown', 'aria-labelledby'=>'Optionsby_'.$id, 'class' => 'form-control pull-left', 'id' => 'optionTextboxAddVal', 'maxlength'=>'1000','style' => 'width:315px;', 'value' => '')) =>
                        '<div class="optionbtnClass pull-left">
                     	<button type="button" class="btn btn-primary" onclick="addOption(\'' . $id . '\',\'' . $typeClass . '\',\'' . $chk2 . '\');" role="button" aria-disabled="false"><span class="ui-button-text" style="margin-top:7px">Add</span></button></div>
                     	<br/><br/>
                     	<div class="optionvalueHtml">
                     	<div class="row border-saprater">
							<div class="col-md-12">
								<div class="table-responsive table-tbody-scroll">
					  				<div id="example_wrapper" class="dataTables_wrapper no-footer">
						  				<table summary="include a summary of the content of the table here" class="display display-check-radio dataTable no-footer" id="example">
											<thead>
                                                                                            <tr>
                                                                                                <th width="85%" scope="col" class="sorting_disabled" role="columnheader" rowspan="1" colspan="1"><a href="javascript:void(0);" title="Option" class="tag-header-black">Option</a></th>
                                                                                                <th scope="col" class="sorting_disabled" role="columnheader" rowspan="1" colspan="1"><a href="javascript:void(0);" title="Default" class="tag-header-black">Default</a></th>
                    								<th width="5%" scope="col" class="sorting_disabled" role="columnheader" rowspan="1" colspan="1"><a href="javascript:void(0);" title="Actions" class="tag-header-black">Actions</a></th>
												</tr>
											</thead>
											<tbody class="tbodyClass_' . $id . '">'.$optionTr.'</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
						</div>
                    <div style="display:none">' . form_textarea(array("name" => "values", "class" => "dropdown",'maxlength'=>'1000', "rel" => 'select[name=' . $id . ']', "style" => "height: 100px;width:400px;",)) . "<div>"
                    ),
                );
            }
        } else if ($attr['type'] == "checkbox") {
            if ($optionVal != "undefined") {
                $optionValueArr = explode(";", html_entity_decode($optionVal));
                $optionTr = array();
                $c=1;
                foreach ($optionValueArr as $optValue) {
                    $optValue=htmlentities($optValue);
                    $opt_chks = explode(",", $optVals);
                    $pchkChecked = "";

                    //if (in_array($optValue, ($opt_chks))
					foreach($opt_chks as $opt_chkesssss)
					{
						if(html_entity_decode($opt_chkesssss) == html_entity_decode($optValue)){
							$pchkChecked = "checked='checked'";
							break;
						}
                    }
					$classoddeven='odd';
                    $uniqid = uniqid();
                    if($c%2==0)$classoddeven='even';
                    //<td><input type="checkbox" id="option_chk_label'.$uniqid.'" name="optionchk_name" class="checkbox_btn" value="' . $optValue . '"  '.$pchkChecked.' ><label for="option_chk_label_'.$uniqid.'" class=""><span class="sr-only">'.$optValue.'</span></label></td>
                    $optionTr[] = '<tr class="'.$classoddeven.' newTr">
						<td width="85%">'.$optValue.'</td>
						<td><input type="checkbox" id="'.$id.'" name="optionchk_name" aria-label="'.$optValue.'" class="checkbox_btn" value="' . $optValue . '"  '.$pchkChecked.' ><label for="'.$id.'" class=""><span class="sr-only">'.$optValue.'</span></label></td>
						<td width="5%"><a href="javascript:void(0);" title="Remove Option" class="removeOptionText" id="' . $id . '" ><em class="fa fa-close text-primary" title="Remove"></em><span class="screenreader">Close</span></a></td>
					</tr>';

                    /*$optionTr[] = '<tr class="newTr">
                                <td style="min-width:20px !important;"><a style="cursor: pointer;" class="removeOptionText" id="' . $id . '"><em class="fa fa-times" ></em></a></td>
                                <td style="min-width: 284px ! important; text-align: left; word-wrap: break-word; display: block; max-width: 284px ! important; width: 284px ! important;">' . $optValue . '</td>
                                <td style="min-width:40px !important;text-align:center"><input type="checkbox" class="checkbox_btn" value="' . $optValue . '" id="' . $id . '" name="optionchk_name" ' . $pchkChecked . '></td>
                                </tr>';*/
                  $c++;
                }
            } else if ($option_values != "undefined") {
                $optionValueArr = explode(";", html_entity_decode($option_values));
                $optionTr = array();
                $c=1;
                foreach ($optionValueArr as $optValue) {
                    $optValue=htmlentities($optValue);
                    $opt_chks = explode(",", $optVals);
                    $pchkChecked = "";

                    //if (in_array($optValue, $opt_chks)) {
					foreach($opt_chks as $opt_chkesssss)
					{
						if(html_entity_decode($opt_chkesssss) == html_entity_decode($optValue)){
							$pchkChecked = "checked='checked'";
							break;
						}
                    }

                    $classoddeven='odd';
                    $uniqid = uniqid();
                    if($c%2==0)$classoddeven='even';
                    $optionTr[] = '<tr class="'.$classoddeven.' newTr">
						<td width="85%">'.$optValue.'</td>
						<td><input type="checkbox" aria-label="'.$optValue.'" aria-labelledby="option_chk_label'.$uniqid.'" name="optionchk_name" class="checkbox_btn" id="' . $id . '" value="' . $optValue . '" id="' . $id . '" '.$pchkChecked.' ><label for="' . $id . '" id="option_chk_label_'.$uniqid.'" class=""><span class="sr-only">'.$optValue.'</span></label></td>
						<td width="5%"><a href="javascript:void(0);" title="Remove Option" class="removeOptionText" id="' . $id . '" ><em class="fa fa-close text-primary" title="Remove"></em><span class="screenreader">Close</span></a></td>
					</tr>';

                    /*$optionTr[] = '<tr class="newTr">
                                <td style="min-width:20px !important;"><a style="cursor: pointer;" class="removeOptionText" id="' . $id . '"><em class="fa fa-times" ></em></a></td>
                                <td style="min-width: 284px ! important; text-align: left; word-wrap: break-word; display: block; max-width: 284px ! important; width: 284px ! important;">' . $optValue . '</td>
                                <td style="min-width:40px !important;text-align:center"><input type="checkbox" class="checkbox_btn" value="' . $optValue . '" id="' . $id . '" name="optionchk_name" ' . $pchkChecked . '></td>
                                </tr>';*/
                    $c++;
                }
            } else {
            	$classoddeven = 'odd';
				/*$optionTr = '<tr class="'.$classoddeven.' newTr blank-hide-tr">
					<td width="85%"></td>
					<td></td>
					<td width="5%"></td>
				</tr>';            */
                /*$optionTr = '<tr class="removeTr">
                                <td style="min-width:20px !important;"></td>
                                <td style="min-width: 284px ! important; text-align: left; word-wrap: break-word; display: block; max-width: 284px ! important; width: 284px ! important;"></td>
                                <td style="min-width:40px !important;">&nbsp;</td>
                            </tr>';*/
            }
            if (is_array($optionTr)) {
                $optionTr = implode("", $optionTr);
            }
            $typeClass = "checkbox";

            $options = array(
                'Label' => form_input(array('rel' => 'label[for=' . $id . '] span', 'aria-labelledby'=>'Labelby_'.$id, 'name' => 'label','maxlength'=>'1000','class'=>'form-control '), $label),
                'Required' => array('' => '<input class=" " type="checkbox" aria-labelledby="Requiredby_'.$id.'" value="1" name="required" onclick="if(this.checked) this.value=0; else this.value=1;" ' . $chk . '  id="chk-required-'.$id.'" /><label class="sr-only" for="chk-required-'.$id.'">Required</label>', //form_checkbox('required','1'),
                ),
                'No Load Previous' => array('' => '<input class=" " aria-labelledby="No_Load_Previousby_'.$id.'" type="checkbox" value="0" name="no_load_prev" onclick="if(this.checked) this.value=1; else this.value=0;" ' . $chk1 . ' title="Do not copy field contents during Load Previous process when submitting new projects." id="chk-no-load-prev-'.$id.'" /><label class="sr-only" for="chk-no-load-prev-'.$id.'">No Load Previous</label>', //form_checkbox('required','1'),
                ),
                //Added Date:8-1-15
                //'Sync with Production Fields' => form_dropdown('sync_prod', array('' => '', 'prod_begbates' => 'Prod BegBates', 'prod_endbates' => 'Prod EndBates', 'prod_vol' => 'Prod Volume'), $syncprod, 'class="fieldtypeClass hide"'),
                'Description' => form_input(array('name' => 'description', 'aria-labelledby'=>'Descriptionby_'.$id, 'maxlength'=>'1000','id' => 'description', 'rel' => '.note[class~=' . $id . ']','class'=>'form-control ')),
                'Options' => array(
                    form_input(array('name' => 'optionTextboxcheckbox', 'aria-labelledby'=>'Optionsby_'.$id, 'class' => 'form-control pull-left', 'id' => 'optionTextboxAddVal','maxlength'=>'1000', 'style' => 'width:315px;', 'value' => '')) =>
                    '<div class="optionbtnClass pull-left">

                     <button type="button" class="btn btn-primary" onclick="addOption(\'' . $id . '\',\'' . $typeClass . '\',\'' . $chk2 . '\');" role="button" aria-disabled="false"><span class="ui-button-text" style="margin-top:7px">Add</span></button></div>
                     <br/><br/>
                    <div class="optionvalueHtml">
                     	<div class="row border-saprater">
							<div class="col-md-12">
								<div class="table-responsive table-tbody-scroll">
					  				<div id="example_wrapper" class="dataTables_wrapper no-footer">
						  				<table summary="include a summary of the content of the table here" class="display display-check-radio dataTable no-footer" id="example">
											<thead>
												<tr>
													<th width="85%" scope="col" class="sorting_disabled" role="columnheader" rowspan="1" colspan="1"><a href="javascript:void(0);" title="Option" class="tag-header-black">Option</a></th>
													<th scope="col" class="sorting_disabled" role="columnheader" rowspan="1" colspan="1"><a href="javascript:void(0);" title="Default" class="tag-header-black">Default</a></th>
                									<th width="5%" scope="col" class="sorting_disabled" role="columnheader" rowspan="1" colspan="1"><a href="javascript:void(0);" title="Actions" class="tag-header-black">Actions</a></th>
												</tr>
											</thead>
											<tbody class="tbodyClass_' . $id . '">'.$optionTr.'</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
                    <div style="display:none;">' . form_textarea(array("name" => "values", 'tab-index'=>-1, 'maxlength'=>'1000',"class" => "checkbox", "rel" => 'span.values[class~=' . $id . ']', "style" => "height: 100px;width:400px;")) . "<div>"
                ),
            );
        } else if ($attr['type'] == 'radio') {
            if ($optionVal != "undefined") {
                $optionValueArr = explode(";", html_entity_decode($optionVal));
                $optionTr = array();
                $c=1;
                foreach ($optionValueArr as $optValue) {
                    $optValue=htmlentities($optValue);
                    $pradioChecked = "";
                    if (html_entity_decode($optValue) == html_entity_decode($optVals)) {
                        $pradioChecked = "checked='checked'";
                    }
                    $classoddeven='odd';
                    $uniqid = uniqid();
                    if($c%2==0)$classoddeven='even';
                    $optionTr[] = '<tr class="'.$classoddeven.' newTr">
						<td width="85%">'.$optValue.'</td>
						<td><input type="checkbox" aria-label="'.$optValue.'" aria-labelledby="option_chk_label'.$uniqid.'" name="optionchk_name" class="radio_btn" id="' . $id . '" value="' . $optValue . '" id="' . $id . '" '.$pradioChecked.' ><label for="' . $id . '" id="option_chk_label_'.$uniqid.'" class=""><span class="sr-only">'.$optValue.'</span></label></td>
						<td width="5%"><a href="javascript:void(0);" title="Remove Option" class="removeOptionText" id="' . $id . '" ><em class="fa fa-close text-primary" title="Remove"></em><span class="screenreader">Close</span></a></td>
					</tr>';
                    /*$optionTr[] = '<tr class="newTr">
                                <td style="min-width:20px !important;"><a style="cursor: pointer;" class="removeOptionText" id="' . $id . '"><em class="fa fa-times" ></em></a></td>
                                <td style="min-width: 284px ! important; text-align: left; word-wrap: break-word; display: block; max-width: 284px ! important; width: 284px ! important;">' . $optValue . '</td>
                                <td style="min-width:40px !important;text-align:center"><input type="checkbox" class="radio_btn" value="' . $optValue . '" id="' . $id . '" name="optionchk_name" ' . $pradioChecked . '></td>
                                </tr>';*/
                    $c++;
                }
            } else if ($option_values != "undefined") {
                $optionValueArr = explode(";", html_entity_decode($option_values));
                $optionTr = array();
                $c=1;
                foreach ($optionValueArr as $optValue) {
                    $optValue=htmlentities($optValue);
                    $pradioChecked = "";
                    if (html_entity_decode($optValue) == html_entity_decode($optVals)) {
                        $pradioChecked = "checked='checked'";
                    }
                    $classoddeven='odd';
                    $uniqid = uniqid();
                    if($c%2==0)$classoddeven='even';
                    $optionTr[] = '<tr class="'.$classoddeven.' newTr">
						<td width="85%">'.$optValue.'</td>
						<td><input type="checkbox" aria-label="'.$optValue.'" aria-labelledby="option_chk_label'.$uniqid.'" name="optionchk_name" class="radio_btn" id="' . $id . '" value="' . $optValue . '" id="' . $id . '" '.$pradioChecked.' ><label for="' . $id . '" id="option_chk_label_'.$uniqid.'" class=""><span class="sr-only">'.$optValue.'</span></label></td>
						<td width="5%"><a href="javascript:void(0);" title="Remove Option" class="removeOptionText" id="' . $id . '" ><em class="fa fa-close text-primary" title="Remove"></em><span class="screenreader">Close</span></a></td>
					</tr>';
                   /* $optionTr[] = '<tr class="newTr">
					<td style="min-width:20px !important;"><a style="cursor: pointer;" class="removeOptionText" id="' . $id . '"><em class="fa fa-times" ></em></a></td>
					<td style="min-width: 284px ! important; text-align: left; word-wrap: break-word; display: block; max-width: 284px ! important; width: 284px ! important;">' . $optValue . '</td>
					<td style="min-width:40px !important;text-align:center"><input type="checkbox" class="radio_btn" value="' . $optValue . '" id="' . $id . '" name="optionchk_name" ' . $pradioChecked . '></td>
					</tr>';*/
                    $c++;
                }
            } else {
            	$classoddeven = 'odd';
				/*$optionTr = '<tr class="'.$classoddeven.' newTr blank-hide-tr">
					<td width="85%"></td>
					<td></td>
					<td width="5%"></td>
				</tr>';  */
                /*$optionTr = '<tr class="removeTr">
                                <td style="min-width:20px !important;"></td>
                                <td style="min-width: 284px ! important; text-align: left; word-wrap: break-word; display: block; max-width: 284px ! important; width: 284px ! important;"></td>
                                <td style="min-width:40px !important;">&nbsp;</td>
                            </tr>';*/

            }
            if (is_array($optionTr)) {
                $optionTr = implode("", $optionTr);
            }

            $typeClass = "radio";

            if ($attr['qareport'] == 1) {
                $options = array(
                    'Label' => form_input(array('rel' => 'label[for=' . $id . '] span', 'aria-labelledby'=>'Labelby_'.$id, 'name' => 'label','maxlength'=>'1000','class'=>'form-control '), $label),
                    'Required' => array('' => '<input class="" type="checkbox" value="1" aria-labelledby="Requiredby_'.$id.'" name="required" onclick="if(this.checked) this.value=0; else this.value=1;" ' . $chk . '  id="chk-required-'.$id.'" /><label class="sr-only" for="chk-required-'.$id.'">Required</label>', //form_checkbox('required','1'),
                    ),
                    'QA Report Use' => array('' => '<input class=" " type="checkbox" aria-labelledby="QA_Report_Useby_'.$id.'" value="0" name="qareportuse" onclick="if(this.checked) this.value=1; else this.value=0;" ' . $chkqau . '  id="chk-qa-report-use-'.$id.'" /><label class="sr-only" for="chk-qa-report-use-'.$id.'">QA Report Use</label>', //form_checkbox('required','1'),
                    ),
                    'No Load Previous' => array('' => '<input class=" " type="checkbox" aria-labelledby="No_Load_Previousby_'.$id.'" value="0" name="no_load_prev" onclick="if(this.checked) this.value=1; else this.value=0;" ' . $chk1 . ' title="Do not copy field contents during Load Previous process when submitting new projects."  id="chk-no-load-prev-'.$id.'" /><label class="sr-only" for="chk-no-load-prev-'.$id.'">No Load Previous</label>', //form_checkbox('required','1'),
                    ),
                    //Added Date:8-1-15
                    //'Sync with Production Fields' => form_dropdown('sync_prod', array('' => '', 'prod_begbates' => 'Prod BegBates', 'prod_endbates' => 'Prod EndBates', 'prod_vol' => 'Prod Volume'), $syncprod, 'class="fieldtypeClass hide"'),
                    'Description' => form_input(array('name' => 'description', 'aria-labelledby'=>'Descriptionby_'.$id, 'maxlength'=>'1000', 'id' => 'description', 'rel' => '.note[class~=' . $id . ']','class'=>'form-control ')),
                    'Options' => array(
                        form_input(array('name' => 'optionTextboxradio', 'aria-labelledby'=>'Optionsby_'.$id, 'class' => 'form-control pull-left', 'id' => 'optionTextboxAddVal','maxlength'=>'1000', 'style' => 'width:315px;', 'value' => '')) =>
                        '<div class="optionbtnClass pull-left">

                     <button type="button" class="btn btn-primary" onclick="addOption(\'' . $id . '\',\'' . $typeClass . '\',\'' . $chk2 . '\');" role="button" aria-disabled="false"><span class="ui-button-text" style="margin-top:7px">Add</span></button></div>
                     <br/><br/>
                    <div class="optionvalueHtml">
                     	<div class="row border-saprater">
							<div class="col-md-12">
								<div class="table-responsive table-tbody-scroll">
					  				<div id="example_wrapper" class="dataTables_wrapper no-footer">
						  				<table summary="include a summary of the content of the table here" class="display display-check-radio dataTable no-footer" id="example">
											<thead>
												<tr>
													<th width="85%" scope="col" class="sorting_disabled" role="columnheader" rowspan="1" colspan="1"><a href="javascript:void(0);" title="Option" class="tag-header-black">Option</a></th>
													<th scope="col" class="sorting_disabled" role="columnheader" rowspan="1" colspan="1"><a href="javascript:void(0);" title="Default" class="tag-header-black">Default</a></th>
                    								<th width="5%" scope="col" class="sorting_disabled" role="columnheader" rowspan="1" colspan="1"><a href="javascript:void(0);" title="Actions" class="tag-header-black">Actions</a></th>
												</tr>
											</thead>
											<tbody class="tbodyClass_' . $id . '">'.$optionTr.'</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
                    <div style="display:none">' . form_textarea(array("name" => "values", 'tab-index'=>-1, 'maxlength'=>'1000',"class" => "radio", "rel" => 'span.values[class~=' . $id . ']', "style" => "height: 100px;width:400px;")) . "<div>"
                    ),
                );
            } else {

                $options = array(
                    'Label' => form_input(array('rel' => 'label[for=' . $id . '] span', 'aria-labelledby'=>'Labelby_'.$id, 'name' => 'label','maxlength'=>'1000','class'=>'form-control '), $label),
                    'Required' => array('' => '<input class=" " type="checkbox" aria-labelledby="Requiredby_'.$id.'" value="1" name="required" onclick="if(this.checked) this.value=0; else this.value=1;" ' . $chk . '  id="chk-required-'.$id.'" /><label class="sr-only" for="chk-required-'.$id.'">Required</label>', //form_checkbox('required','1'),
                    ),
                    'No Load Previous' => array('' => '<input class=" " type="checkbox" aria-labelledby="No_Load_Previousby_'.$id.'" value="0" name="no_load_prev" onclick="if(this.checked) this.value=1; else this.value=0;" ' . $chk1 . ' title="Do not copy field contents during Load Previous process when submitting new projects."  id="chk-no-load-prev-'.$id.'" /><label class="sr-only" for="chk-no-load-prev-'.$id.'">No Load Previous</label>', //form_checkbox('required','1'),
                    ),
                    // Added Date:8-1-15
                    //'Sync with Production Fields' => form_dropdown('sync_prod', array('' => '', 'prod_begbates' => 'Prod BegBates', 'prod_endbates' => 'Prod EndBates', 'prod_vol' => 'Prod Volume'), $syncprod, 'class="fieldtypeClass hide"'),
                    'Description' => form_input(array('name' => 'description', 'aria-labelledby'=>'Descriptionby_'.$id, 'maxlength'=>'1000','id' => 'description', 'rel' => '.note[class~=' . $id . ']','class'=>'form-control ')),
                    'Options' => array(
                        form_input(array('name' => 'optionTextboxradio', 'aria-labelledby'=>'Optionsby_'.$id, 'class' => 'form-control pull-left', 'id' => 'optionTextboxAddVal','maxlength'=>'1000', 'style' => 'width:315px;', 'value' => '')) =>
                        '<div class="optionbtnClass pull-left">
                     <button type="button" class="btn btn-primary" onclick="addOption(\'' . $id . '\',\'' . $typeClass . '\',\'' . $chk2 . '\');" role="button" aria-disabled="false"><span class="ui-button-text" style="margin-top:7px">Add</span></button></div>
                     <br/><br/>
                    <div class="optionvalueHtml">
                     	<div class="row border-saprater">
							<div class="col-md-12">
								<div class="table-responsive table-tbody-scroll">
					  				<div id="example_wrapper" class="dataTables_wrapper no-footer">
						  				<table summary="include a summary of the content of the table here" class="display display-check-radio dataTable no-footer" id="example">
											<thead>
												<tr>
													<th width="85%" scope="col" class="sorting_disabled" role="columnheader" rowspan="1" colspan="1"><a href="javascript:void(0);" title="Option" class="tag-header-black">Option</a></th>
													<th scope="col" class="sorting_disabled" role="columnheader" rowspan="1" colspan="1"><a href="javascript:void(0);" title="Default" class="tag-header-black">Default</a></th>
                    								<th width="5%" scope="col" class="sorting_disabled" role="columnheader" rowspan="1" colspan="1"><a href="javascript:void(0);" title="Actions" class="tag-header-black">Actions</a></th>
												</tr>
											</thead>
											<tbody class="tbodyClass_' . $id . '">'.$optionTr.'</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
                    <div style="display:none">' . form_textarea(array("name" => "values",'maxlength'=>'1000', "class" => "radio", "rel" => 'span.values[class~=' . $id . ']', "style" => "height: 100px;width:400px;")) . "<div>"
                    ),
                );
            }
        }

        //$seperate_help = '<span class="icon toolbox" style="cursor: pointer; float: right; color: #9CBDDF; margin-top: -10px;width: 40px;" onclick=showFromHelp();>Help</span>';
        $seperate_help = '';
        //specific options
        if (isset($attr['form']) && ($attr['form'] == "data" || $attr['form'] == 'undefined')) {
            unset($options['No Load Previous']);
        }
       /* echo "<pre>";
        print_r($options);
        die;*/
        $desc = '';
        switch ($type) {
            //case 'dropdown':
            //$options['Options'] = "<div style='display:none;'>".form_textarea(array('name' => 'values', 'class' => 'dropdown', 'rel' => 'select[name=' . $id . ']', 'style' => 'height: 100px;width:400px;'))."</div>";
            //break;
//            case 'radio':
//                $options['Options'] = form_textarea(array('name' => 'values', 'class' => 'radio', 'rel' => 'span.values[class~=' . $id . ']', 'style' => 'height: 100px;width:400px;'));
//                break;
//            case 'checkbox':
//                $options['Options'] = form_textarea(array('name' => 'values', 'class' => 'checkbox', 'rel' => 'span.values[class~=' . $id . ']', 'style' => 'height: 100px;width:400px;'));
//                break;
            case 'button':
                $options['Value'] = form_input(array('name' => 'value', 'class' => 'btn btn-primary', 'rel' => 'input[name=' . $id . ']'));
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
        /* remove Button */
        //$options[''] = form_input(array('rel'=>$id, 'id'=>'remove','name'=>'remove','value'=>'Delete Element','type'=>'button','class'=>'button','onclick'=>'Admin.formbuilder.remove(this);','style'=>'margin-top: 20px !important;top:-6px!important;'));
        //$options[''] = form_input(array('rel' => $id, 'id' => '', 'name' => '', 'value' => '', 'type' => 'button', 'class' => 'btn btn-primary', 'style' => 'margin-top: 20px !important;top:-6px!important;background: none repeat scroll 0 0 #fff !important;border-bottom: 0px none #fff;'));
        /* remove Button */
        $output = $seperate_help;
        //spit out the options for ajax
        foreach ($options as $k => $option) {
            /*  echo $k."<br>";
              print_r($option); */
            $wid = '';
            if ($k != '')
                $wid = "width:75px;";

            $formeditor_class='';
           	if($attr['type'] == 'text'){
           		$formeditor_class='form-builder-editor';
			}
			$output .= '<li class="' . $id . ' ' . $formeditor_class . '"  data-id="' . $id . '">';


           // $output .= '<strong style="padding-right:10px;padding-top:6px;' . $wid . '">' . $k . '</strong> ';
            $output .= '<strong id="'.str_replace(' ','_',$k).'by_'.$id.'">' . $k . '</strong> ';
            $output .= '<ul>';
            if (is_array($option)) {
                foreach ($option as $sk => $sub) {
                    //echo $sk."<br>".$sub."<br>";
                   // $output .= '<li class="sub"><strong>' . $sk . '</strong> ' . $sub . '</li>';
                	$output .= '<li class="sub">' . $sk . ' ' . $sub . '</li>';
                }
            } else {
                //echo $option."<br>";
                $output .= '<li class="sub">' . $option . '</li>';
            }
            $output .= '</ul>';
            $output .= '</li>';
        }
        $output .= '<li class="addborderbottom">';

        echo ($output);
    }

    function element_display($attr) {

        //echo "<pre>",print_r($attr),"</pre>";
        //if no load more then sep attribute
        $load_prev_chked = false;
        if (isset($attr['load_prev']) && $attr['load_prev'] == 1) {
            if (isset($attr['no_load_more']) && $attr['no_load_more'] == 1) {
                $load_prev_chked = true;
            }
        }
        if ($load_prev_chked) {
            $attr['text_val'] = "";
            $attr['value'] = "";
        }
        $lable_class = '';
        $pro = false;
        $valuessssss = '';
        //$required='1';
        $required_vars = '';
        $description = '';
        $contents = '';
        $required_class = '';
        //$required_span='';
        if (isset($attr['id']) && $attr['id'] != '')
            $id = $attr['id'];
        else
            $id = 'element_' . uniqid();
        $dropdown_val = array('' => 'No Content');
        $btn_value = 'No Content';

        // Required field.
        //if(isset($attr['required']) && $attr['required']!=1)
        if (isset($attr['required']) && $attr['required'] == 0) {
            if ($attr['type'] != 'text') {
                $required = $attr['required'];
                $required_class = 'required-entry';
                $required_span = '<span class="data-required" aria-label="Required">*</span>';
                //$required_span='';
            } else {
                $required_span = '<span class="data-required" aria-label="Required">*</span>';
            }
        } else {
            $required_span = '';
        }
        if (isset($attr['required_vars']) && $attr['required_vars'] != '') {
            $required_vars = $attr['required_vars'];
        }
        if (isset($attr['description']) && $attr['description'] != '') {
            $description = $attr['description'];
        }
        if (isset($attr['text_val']) && $attr['text_val'] != "") {

            $attr['text_val'] = str_replace("<br>", "\n", $attr['text_val']);
            $contents = html_entity_decode($attr['text_val']);
        }
        if (isset($attr['values']) && $attr['values'] != '') {
            $attr['values'] = str_replace('&amp;', '&', $attr['values']);
            $dropdown_val1 = explode(';', $attr['values']);
            if ($attr['type'] == "dropdown")
                $dropdown_val = array_merge(array('0' => 'Please Select'), $dropdown_val1);
            else
                $dropdown_val = $dropdown_val1;
            $pro = true;
            $valuessssss = "<input class='values' type='hidden' name='properties[" . $id . "][values]' value='" . $attr['values'] . "' />

							";
        }
        $ivalue = "";
        $svalue = array();
        $chsel_arr = array();

        if (isset($attr['value']) && $attr['value'] != '') {
            $btn_value = $attr['value'];
            $pro = true;
            $valuessssss = "<input class='value' type='hidden' name='properties[" . $id . "][value]' value='" . $attr['value'] . "' />";
            if ($attr['type'] == 'textarea') {
                $ivalue = strip_tags($attr['value']);
                $ivalue = str_replace("<br>", "\n", $ivalue);
                $contents = html_entity_decode(rawurldecode(str_replace("<br>", "\n", $ivalue)));
            } else
                $ivalue = $attr['value'];

            array_push($svalue, $attr['value']);
            if ($attr['type'] == 'checkbox') {
                $chsel_arr = explode(",", $attr['value']);
            }
        }
        $checked = '';
        if (isset($chsel_arr[0]) && $chsel_arr[0] == 'on')
            $checked = 'checked="checked"';
        $element_ck = '<input type="checkbox"  name="' . $id . '[]"  class="' . $required_class . '" ' . $checked . ' id="chk_'.$id.'"><label for="chk_'.$id.'"></label>';
        if ($attr['type'] == 'checkbox') {
            if (isset($attr['values']) && $attr['values'] != '') {
                $element_chk = '';
                $count_chk = 0;

                foreach ($dropdown_val as $va) {
                    $checked = '';
                    if (in_array($count_chk, $chsel_arr))
                        $checked = 'checked="checked"';
                    if ($element_chk == '') {
                        $element_chk = '<input type="checkbox" name="' . $id . '[]" value="' . $count_chk . '" class="' . $required_class . '" ' . $checked . ' id="chk_'.$count_chk.'_'.$id.'">' . $va . '<label for="chk_'.$count_chk.'_'.$id.'"></label><br>';
                    } else {
                        $element_chk.='<input type="checkbox" name="' . $id . '[]" value="' . $count_chk . '" class="' . $required_class . '" ' . $checked . ' id="chk_'.$count_chk.'_'.$id.'">' . $va . '<label for="chk_'.$count_chk.'_'.$id.'"></label><br>';
                    }
                    $count_chk++;
                }
                if ($element_chk != '')
                    $element_ck = $element_chk;
            }
        }
        $element_ra = '<input type="radio" name="' . $id . '" class="' . $required_class . '" id="rdo_'.$id.'" ><label for="rdo_'.$id.'"></label>';
        if ($attr['type'] == 'radio') {
            if (isset($attr['values']) && $attr['values'] != '') {

                $element_rad = '';
                $count_rd = 0;
                foreach ($dropdown_val as $va) {
                    $checked = '';
                    if ($ivalue != '') {
                        if ($count_rd == $ivalue)
                            $checked = 'checked="checked"';
                    }
                    if ($element_rad == '')
                        $element_rad = '<input type="radio" name="' . $id . '" value="' . $count_rd . '"  class="' . $required_class . '" ' . $checked . ' id="rdo_'.$count_rd.'_'.$id.'">' . $va . '<label for="rdo_'.$count_rd.'_'.$id.'"></label><br>';
                    else
                        $element_rad.='<input type="radio" name="' . $id . '" value="' . $count_rd . '"  class="' . $required_class . '" ' . $checked . ' id="rdo_'.$count_rd.'_'.$id.'">' . $va . '<label for="rdo_'.$count_rd.'_'.$id.'"></label><br>';
                    $count_rd++;
                }
                if ($element_rad != '')
                    $element_ra = $element_rad;
            }
        }
        $ivalue = str_replace("\'", "'", $ivalue);
        switch ($attr['type']) {
            case 'text':
                $element = form_textarea(array(
                    'class' => 'wysiwyg instruction_multi10000 ' . $required_class,
                    'id' => $id,
                    'name' => $id,
                    'rows' => 5,
                    'cols' => 50,
                    'style' => 'display:none;',
                    'maxlength'=>'10000',
                ));
                $lable_class = 'text_lable_class';
                break;
            case 'textarea':
                $element = form_textarea(array(
                    'name' => $id,
                    'rows' => 5,
                    'cols' => 50,
                    'id'=>'lbl-'.$id,
                    'value' => $ivalue,
                    'class' => 'instruction_multi10000 '.$required_class,
                    'maxlength'=>'10000',
                ));
                break;
            case 'textbox': $element = form_input(array('name' => $id,'id'=>'lbl-'.$id, 'maxlength'=>'255', 'class' => 'jf_text form_role_input instruction_single255 ' . $required_class), $ivalue);
                break;
            case 'number': $element = form_input(array('name' => $id,'id'=>'lbl-'.$id, 'maxlength'=>'255', 'class' => 'jf_text form_role_input instruction_single255 user_input numeric-field-qu negative-key' . $required_class), $ivalue);
                break;
            case 'dropdown': $element = form_dropdown($id, $dropdown_val, $svalue, 'class=' . $required_class);
                break;
            case 'checkbox': $element = '<span class="values ' . $id . '">' . $element_ck . '</span>';
                break;
            case 'radio': $element = '<span class="values ' . $id . '">' . $element_ra . '</span>';
                break;
            case 'datetime': $element = form_input(array('name' => $id, 'placeholder' => 'Choose a date', 'class' => 'datepicker jf_text form_role_input ' . $required_class,'readonly'=>'readonly'), $ivalue);
                break;
            case 'fileupload': $element = form_upload($id);
                break;
            case 'button': $element = form_input(array('name' => $id, 'value' => $btn_value, 'type' => 'button'));
                break;
            default: $element = null;
                break;

            //echo $element;
        }
        //give the text box a differnt label
        if (isset($attr['label']) && $attr['label'] != "") {
            $label = $attr['label'];
            $pro = true;
        } else
            $label = ($attr['type'] == 'text') ? ' ' : 'No Label';

        if ($pro) {
            //$this->properties($attr);
        }

        //basic output list element.
        if (isset($attr['edit']) && $attr['edit'] != '') {
            if (isset($contents) && $contents != '') {
                if (isset($attr['text_val']) && $attr['text_val'] != "") {
                    $contents = html_entity_decode(rawurldecode($contents));
                    $label = str_replace(",", "", $contents);
                    $label = str_replace("+", " ", $contents);
                    $label = str_replace("\n", "<br>", $contents);
                }
            }
            $colon = ($attr['type'] == 'text') ? '' : ':';
            $output = "
					<li>
					<label for='" . $id . "' class='" . $lable_class . "'>" . $label . $required_span . $colon . "</label>
							<div class='block'>" . $element . "
							<span class='note " . $id . "'>$description</span></div>
						<div class='clear'></div>
						<div class='attrs clear " . $id . "'>
							<input type='hidden' name='properties[" . $id . "][name]' value='" . $id . "'/>
							<input type='hidden' name='properties[" . $id . "][value]' class='label' value='" . $label . "' />" . $valuessssss . "
							<input type='hidden' name='properties[" . $id . "][required]' class='required' value='" . $required . "' / >
							<input type='hidden' name='properties[" . $id . "][required_vars]' class='required_vars' value='" . $required_vars . "' />
							<input type='hidden' name='properties[" . $id . "][description]' class='description' value='" . $description . "'>
						</div>
					</li>
				";
        }

        if ($element) {
            //set output to AJAX
            if (isset($attr['edit']) && $attr['edit'] != '')
                echo html_entity_decode($output . '^' . $id);
            else
                echo html_entity_decode($output);
        }
        sleep(1);
    }

    function element_display_bulk($attributes) {

        //echo "<pre>",print_r($attributes),"</pre>"; die();

    	$label_div_class=" col-md-3 ";
    	$label_itself_class=" form_label ";
    	$input_div_class=" col-md-7 ";
    	$input_itself_class=" form-control ";
        //$islegend="N";
        require(__DIR__ . '/../yii_init.php');
        //echo "<pre>",print_r($attributes),"</pre>";die;
       //if no load more then sep attribute
        $elements_output = array();
        foreach ($attributes as $attr) {
            $load_prev_chked = false;
            if (isset($attr['load_prev']) && $attr['load_prev'] == 1) {
               // if (isset($attr['no_load_more']) && $attr['no_load_more'] == 1) {
                    $load_prev_chked = true;
                //}
            }
            if ($load_prev_chked) {
                $attr['text_val'] = "";
                $attr['value'] = "";
            }
            $lable_class = '';
            $pro = false;
            $valuessssss = '';
            //$required='1';
            $required_vars = '';
            $description = '';
            $contents = '';
            $required_class = '';
            $sync_prods = '';
            $field_types = '';
            $opt_chk = '';
            $noloadpre = '';
            $default_answer = '';
            $default_unit = 0;
            $wfloadprevoius = 0;
            $is_new_st_addded = isset($attr['is_new_st_addded'])?$attr['is_new_st_addded']:0;
            $formtype = isset($attr['formtype'])?$attr['formtype']:'add';
            //$required_span='';
            if (isset($attr['id']) && $attr['id'] != '')
                $id = $attr['id'];
            else
                $id = 'element_' . uniqid();
            $dropdown_val = array('' => 'No Content');
            $btn_value = 'No Content';
			$star_required="";
            // Required field.
            //if(isset($attr['required']) && $attr['required']!=1)
            if (isset($attr['required']) && $attr['required'] == 0) {
                if ($attr['type'] != 'text') {
                    $required = $attr['required'];
                    $required_class = 'required-entry';
                    $star_required="required";
                    //$required_span = '<span class="data-required require-asterisk-again">*</span>';
                    $required_span='';
                } else {
                    //$required_span = '<span class="data-required require-asterisk-again">*</span>';
                    $required_span='';
                    $star_required="required";
                }
            } else {
                $required_span = '';
            }
            $date_val="";
            if ($attr['type'] == "datetime") {
            	if (isset($attr['value']) && $attr['value'] != '') {
            		$date_val=$attr['value'];
            	}
            }
            if (isset($attr['required_vars']) && $attr['required_vars'] != '') {
                $attr['required_vars'] = rawurldecode($attr['required_vars']);
                $required_vars = $attr['required_vars'];
            }
            if (isset($attr['description']) && $attr['description'] != '') {
                $attr['description'] = rawurldecode($attr['description']);
                $description = ucfirst($attr['description']);
            }
            if (isset($attr['text_val']) && $attr['text_val'] != "") {
                $attr['text_val'] = rawurldecode($attr['text_val']);
                $attr['text_val'] = str_replace("<br>", "\n", $attr['text_val']);
                $contents = html_entity_decode($attr['text_val']);
            }

            if (isset($attr['sync_prod']) && $attr['sync_prod'] != '') {
                $attr['sync_prod'] = rawurldecode($attr['sync_prod']);
                $sync_prods = $attr['sync_prod'];
            }
            /*if (isset($attr['field_type']) && $attr['field_type'] != '') {
                $attr['field_type'] = rawurldecode($attr['field_type']);
                $field_types = $attr['field_type'];
                if($attr['field_type'] == 'number' || strtolower($field_types) == 1){
					$field_types .= " numeric-field-qu";
                }
            }*/
            if (isset($attr['optionchk']) && $attr['optionchk'] != '') {
                $attr['optionchk'] = rawurldecode($attr['optionchk']);
                $opt_chk = $attr['optionchk'];
            }

            if (isset($attr['noloadpre']) && $attr['noloadpre'] != '') {
                $attr['noloadpre'] = rawurldecode($attr['noloadpre']);
                $noloadpre = $attr['noloadpre'];
            }

            if (isset($attr['default_answer']) && $attr['default_answer'] != '') {
                $attr['default_answer'] = rawurldecode($attr['default_answer']);
                $default_answer = $attr['default_answer'];
            }

            if (isset($attr['default_unit']) && $attr['default_unit'] != '') {
                $attr['default_unit'] = rawurldecode($attr['default_unit']);
                $default_unit = $attr['default_unit'];
            }

            //give the text box a differnt label
            if (isset($attr['label']) && $attr['label'] != "") {
                $label = ucfirst($attr['label']);
                $pro = true;
            } else
                $label = ($attr['type'] == 'text') ? ' ' : 'No Label';

            //Start: Logic for displaying unit
            $unit_dd = '';
            if ($attr['type'] == 'number') {
                $unit_dd = "<select name='properties[" . $id . "][unit_id]' class='clsunit $input_itself_class'><option value=''>Unit</option>";
                $list = Yii::$app->db->createCommand('select * from tbl_unit WHERE remove=0')->queryAll();
                foreach ($list as $unititem) {
					$unitselected = '';
					if($default_unit != 0 && $default_unit == $unititem['id'])
						$unitselected = 'selected="selected"';
                    $unit_dd.="<option value=" . $unititem['id'] . " $unitselected>" . $unititem['unit_name'] . "</option>";
                }
                $unit_dd.="</select>";
            }

            if (isset($attr['wfloadprevoius']) && $attr['wfloadprevoius'] != '') {
                $attr['wfloadprevoius'] = rawurldecode($attr['wfloadprevoius']);
                $wfloadprevoius = $attr['wfloadprevoius'];
            }

            if (isset($attr['values']) && $attr['values'] != '') {
                $attr['values'] = rawurldecode($attr['values']);
                $attr['values'] = str_replace('&amp;', '&', $attr['values']);
				//$attr['values'] = 'Please Select;'.$attr['values'];
                $dropdown_val1 = explode(';', html_entity_decode($attr['values']));


                if ($attr['type'] == "dropdown") {
					//$dropdown_val = array_merge(array('0' => 'Please Select'), $dropdown_val1);
					//echo "<pre>",print_r($opt_chk),"</pre>";
					$dropdown_val = array('0'=>'Please Select');
					if($attr['values_ids'] != ''){
						$explode_ids = explode(";",$attr['values_ids']);
						foreach($dropdown_val1 as $key=>$dropdown){
							if(isset($explode_ids[$key])){
								$dropdown_val[$explode_ids[$key]] = str_replace(',', '&#44;', htmlentities($dropdown));
                                //$dropdown;
								/*unset($dropdown_val1[$key]);*/
							}
						}
					}
                    //echo "<pre>",print_r($dropdown_val),"</pre>";
                    //die;
					$selectedkey = array_search($opt_chk, $dropdown_val);
                    if($selectedkey == 0) {
                    	$selectedkey=$opt_chk;
                    }
                } else {
					$dropdown_val = array();
					if($attr['values_ids'] != ''){
						$explode_ids = explode(";",$attr['values_ids']);
						foreach($dropdown_val1 as $key=>$dropdown){
							if(isset($explode_ids[$key])){
                                $dropdown_val[$explode_ids[$key]] = str_replace(',', '&#44;', htmlentities($dropdown));
								//$dropdown_val[$explode_ids[$key]] = $dropdown;
								/*unset($dropdown_val1[$key]);*/
							}
						}
					}
				}
                $pro = true;
                $valuessssss = "<input class='values' type='hidden' name='properties[" . $id . "][values]' value='" . $attr['values'] . "' />";
            }


            $ivalue = "";
            $svalue = array();
            $chsel_arr = array();

            if (isset($attr['value']) && $attr['value'] != '') {
                $btn_value = rawurldecode($attr['value']);
                $pro = true;
                $valuessssss = "<input class='value' type='hidden' name='properties[" . $id . "][value]' value='" . $attr['value'] . "' />";
                if ($attr['type'] == 'textarea') {

                    $ivalue = htmlentities($attr['value']);
                    //strip_tags($attr['value']);
                    //if($id=='element_569e348427091'){echo "<prE>",print_r($ivalue),"</pre>"; die;}
                    $ivalue = str_replace("<br>", "\n", $ivalue);
                    $contents = html_entity_decode(rawurldecode(str_replace("<br>", "\n", $ivalue)));
                } else
                    $ivalue = $attr['value'];

                if (isset($attributes['editinstruform'][0]) && $attributes['editinstruform'][0] == "editinstruform") {
                    array_push($svalue, $attr['value']);
                } else if($attr['type'] == 'dropdown'){
                	$svalue = array("0"=>$selectedkey);
                } else {
                    $svalue = $selectedkey;
                }
                if ($attr['type'] == 'checkbox') {
                    $chsel_arr = explode(",", rawurldecode($attr['value']));
                }
            } else if($attr['type'] == 'dropdown' && $selectedkey!="" && $selectedkey != 0){
				$svalue = array("0"=>$selectedkey);
            }
            $checked = '';
            if (isset($chsel_arr[0]) && $chsel_arr[0] == 'on')
                $checked = 'checked="checked"';

            $ariareq = '';
            $required = 0;
            if($required_class != ''){
                $ariareq = 'aria-required = "true" title="This field is required"';
                $required = 1;
            }

            $element_ck = '<input type="checkbox"  name="' . $id . '[]" '.$ariareq.' class="' . $required_class . $input_itself_class . '" ' . $checked . ' id="chk_'.$id.'"><label for="chk_'.$id.'"></label>';
            if ($attr['type'] == 'checkbox') {
                //$islegend="Y";
                if (isset($attr['values']) && $attr['values'] != '') {
                    $element_chk = '';
                    $count_chk = 1;
                    $opt_chks = explode(",", $opt_chk);
                    //echo "<pre>",print_r($dropdown_val),print_r($opt_chks); //exit;
                    foreach ($dropdown_val as $key=>$va) {
                        $chkboxVal = str_replace(',', '&#44;', $va);

                        $checked = "";
                if (isset($attributes['editinstruform'][0]) && $attributes['editinstruform'][0] == "editinstruform") {
                            if (in_array($count_chk, $chsel_arr))
                                $checked = 'checked="checked"';
                        } else {
                                //echo "<pre>",print_r($opt_chks),"</pre>";
                        	if(!empty($opt_chks)){
	                        	//foreach ($opt_chks as $optionchk){
                                    if(is_numeric($opt_chks)){
	                        			if ($key == $opt_chks) {
	                        				$checked = "checked='checked'";
	                        			}
	                        		} else if (is_array($opt_chks) && !empty($opt_chks)) {
										if (in_array($key, $opt_chks)) {
	                        				$checked = "checked='checked'";
	                        			}
									} else{
	                        			if (in_array($chkboxVal, $opt_chks)) {
	                        				$checked = "checked='checked'";
	                        			}
	                        		}
	                        	//}
                        	}

                            if (in_array($count_chk, $chsel_arr)){
                            	$checked = 'checked="checked"';
                            }
                        }

                        if ($element_chk == '')
                            $element_chk = '<input aria-label="'.$va.'" '.$ariareq.' type="checkbox" name="' . $id . '[]" value="' . $key . '" class="' . $required_class . $input_itself_class. '" ' . $checked . ' id="chk_'.$key.'_'.$id.'"><label for="chk_'.$key.'_'.$id.'">' . $va . '&nbsp;</label>';
                        else
                            $element_chk.='<input aria-label="'.$va.'" '.$ariareq.' type="checkbox" name="' . $id . '[]" value="' . $key . '" class="' . $required_class . $input_itself_class. '" ' . $checked . ' id="chk_'.$key.'_'.$id.'"><label for="chk_'.$key.'_'.$id.'">' . $va . '&nbsp;</label>';

                        $count_chk++;
                    }
                    if ($element_chk != '')
                        $element_ck = '<legend class="sr-only">'.$label.'</legend>'.$element_chk;
                }
            }
            $element_ra = '<input type="radio" name="' . $id . '" '.$ariareq.' class="' . $required_class . $input_itself_class . '" id="rdo_'.$id.'"><label for="rdo_'.$id.'"></label>';
            if ($attr['type'] == 'radio') {
                //$islegend="Y";
                if (isset($attr['values']) && $attr['values'] != '') {

                    $element_rad = '';
                    $count_rd = 0;
                    //echo "<Pre>";print_r($dropdown_val);die;
                    $totRadio = count($dropdown_val);
                    $radIndex = 1;
                    foreach ($dropdown_val as $rdokey=>$va) {
                        $radioVal = str_replace(',', '&#44;', $va);
                        $opt_chks = explode(",", $opt_chk);
                        $checked = '';
                        // echo "<Pre>";print_r($opt_chks);die;
                        if (isset($attributes['editinstruform'][0]) && $attributes['editinstruform'][0] == "editinstruform") {
                        	$checked = '';
                            if ($ivalue != '') {
                                if ($count_rd == $ivalue)
                                    $checked = 'checked="checked"';
                            }
                        } else {
                            if(is_numeric($opt_chk)){
                                if ($rdokey == $opt_chk) {
                                    $checked = "checked='checked'";
                                }
                            }else{
                                if ($radioVal == $opt_chk) {
                                    $checked = "checked='checked'";
                                }
                            }
                            if($count_rd>0) {
                                if (in_array($rdokey, $opt_chks)) {
                                    $checked = "checked='checked'";
                                }
                            }
                        }
                        $commRadioAttr = 'aria-label="'.$va.'" '.$ariareq.' aria-setsize="'.$totRadio.'" aria-posinset="'.$radIndex++.'" type="radio" name="' . $id . '" value="' . $rdokey . '" class="' . $required_class . $input_itself_class . '" id="chk_'.$rdokey.'_'.$id.'" ';
                        if ($element_rad == '')
                            $element_rad = '<input '.$commRadioAttr . $checked . '><label for="chk_'.$rdokey.'_'.$id.'">' . $va . '&nbsp;</label>';
                        else
                            $element_rad.='<input  '.$commRadioAttr. $checked . '><label for="chk_'.$rdokey.'_'.$id.'">' . $va . '&nbsp;</label>';

                        $count_rd++;
                    }
                    if ($element_rad != '')
                        $element_ra = '<legend class="sr-only">'.$label.'</legend>'.$element_rad;
                }
            }

          if($noloadpre == 1){$ivalue = '';}else if($noloadpre == 0){$ivalue = str_replace("\'", "'", $ivalue);}

          $dropClass = 'taskdropdown';
          switch ($attr['type']) {
                case 'text':
                    $element = form_textarea(array(
                        'class' => 'wysiwyg instruction_multi10000 ' . $required_class. $input_itself_class,
                        'id' => $id,
                        'name' => $id,
                        'rows' => 5,
                        'cols' => 50,
                        'style' => 'display:none;',
                        'maxlength'=>'10000',
                    ));
                    $lable_class = 'text_lable_class';
                    break;
                case 'textarea':
                    if ($formtype == 'custodianadd' || ($formtype == 'projectadd' && (($wfloadprevoius == 1 && $load_prev_chked == true) || ($wfloadprevoius == 0))) || ($formtype == 'projectedit' && $is_new_st_addded == 1)) {
                            $ivalue = $default_answer;
                    }
                    $element = form_textarea(array(
                        'name' => $id,
                        'rows' => 5,
                        'cols' => 50,
                        'value' => $ivalue,
                        'class' => ' instruction_multi10000 '.$required_class. $input_itself_class,
                    ));
                    break;
                case 'textbox':
					//if ($formtype == 'custodianadd' || ($formtype == 'projectadd')) {
					if ($formtype == 'custodianadd' || ($formtype == 'projectadd' && (($wfloadprevoius == 1 && $load_prev_chked == true) || ($wfloadprevoius == 0))) || ($formtype == 'projectedit' && $is_new_st_addded == 1)) {
						$ivalue = $default_answer;
					}
					$element = form_input(array('name' => $id,'id'=>'lbl-'.$id, 'aria-required'=>$required==1?'true':'false', 'class' => 'jf_text form_role_input instruction_single255 ' . $required_class . ' ' . $input_itself_class), $ivalue);
                    break;
                case 'number':
					//if ($formtype == 'custodianadd' || ($formtype == 'projectadd')) {
					if ($formtype == 'custodianadd' || ($formtype == 'projectadd' && (($wfloadprevoius == 1 && $load_prev_chked == true) || ($wfloadprevoius == 0))) || ($formtype == 'projectedit' && $is_new_st_addded == 1)) {
						$ivalue = $default_answer;
					}
					$element = form_input(array('name' => $id,'id'=>'lbl-'.$id, 'aria-required'=>$required==1?'true':'false', 'class' => 'jf_text form_role_input instruction_single255 ' . $required_class . ' user_input numeric-field-qu negative-key ' .$input_itself_class), $ivalue);
                    break;
                case 'dropdown': $element = "<span class='taskspandrop' style='width: 409px !important;'>" . form_dropdown($id, $dropdown_val, $svalue, 'class="' . $required_class . $input_itself_class . " " . $dropClass . '" '.$ariareq) . "</span>";
                //case 'dropdown': $element = "<span class='taskspandrop' style='width: 409px !important;'>" . form_dropdown($id, $dropdown_val, array("0"=>$selectedkey), 'class="' . $required_class . " " . $dropClass . '"') .  "</span>";
                	break;
                case 'checkbox': $element = '<span class="pull-left values custom-full-width ' . $id . '">' . $element_ck . '</span>';
                    break;
                case 'radio': $element = '<span class="pull-left values custom-full-width ' . $id . '">' . $element_ra . '</span>';
                    break;
                //case 'datetime': $element = form_input(array('name' => $id, 'id' => $id, 'placeholder' => 'Choose a date', 'class' => 'datepicker jf_text form_role_input ' . $required_class . $input_itself_class), $ivalue);
                case 'datetime': $element = '<div class="input-group calender-group">'.form_input(array('id'=>'lbl-'.$id, 'aria-required'=>$required,'name' => $id, 'placeholder' => 'Choose a date', 'class' => 'datepickers ' . $required_class . $input_itself_class,'readonly'=>'readonly','value'=>$date_val)).'</div>';
                    break;
                case 'fileupload': $element = form_upload($id);
                    break;
                case 'button': $element = form_input(array('name' => $id, 'value' => $btn_value, 'type' => 'button'));
                    break;
                default: $element = null;
                    break;

                //echo $element;
            }


            /*if($islegend=="Y"){
                $label = '<legend>'.$label.'</legend>';
            }*/

            if ($pro) {
                //$this->properties($attr);
            }

            //basic output list element.
            if (isset($attr['edit']) && $attr['edit'] != '') {
                if (isset($contents) && $contents != '') {
                    if (isset($attr['text_val']) && $attr['text_val'] != ""){
                        $contents = html_entity_decode(rawurldecode($contents));
                        $label = str_replace(",", "", $contents);
                        $label = str_replace("+", " ", $contents);
                        $label = str_replace("\n", "<br>", $contents);
                    }
                }
                $colon = ($attr['type'] == 'text') ? '' : ':';
                if (isset($attr['text_val']) && $attr['text_val'] != "") {
                	$output = "
						<li data-id='" . $id . "'>
								<div class='row border-saprater'>
								<div class='col-md-12'><label for='" . $id . "' class='" . $lable_class .$label_itself_class." col-sm-12'>" . $label . $required_span .  "</label></div>
								<div class='block'>
								<span class='note " . $id . "'>$description</span>
                									</div>
                									</div>
                									<div class='clear'></div>
                									<div class='attrs clear " . $id . "'>
								<input type='hidden' name='properties[" . $id . "][type]' value='" . $attr['type'] . "'/>
								<input type='hidden' name='properties[" . $id . "][formref_id]' value='" . $attr['formref_id'] . "'/>
								<input type='hidden' name='properties[" . $id . "][name]' value='" . $id . "'/>
								<input type='hidden' name='properties[" . $id . "][value]' class='label' value='" . $label . "' />" . $valuessssss . "
								<input type='hidden' name='properties[" . $id . "][required]' class='required' value='" . $required . "' / >
								<input type='hidden' name='properties[" . $id . "][required_vars]' class='required_vars' value='" . $required_vars . "' />
								<input type='hidden' name='properties[" . $id . "][description]' class='description' value='" . $description . "'>
	                                                        <input type='hidden' name='properties[" . $id . "][sync_prod]' id='sync_prod_" . $id . "' class='sync_prod' value='" . $sync_prods . "'>
	                                                        <input type='hidden' name='properties[" . $id . "][optionchk]' id='optionchk_" . $id . "' class='optionchk' value='" . $opt_chk . "'>
							</div>
						</li>
					";
                }else{

	                $output = "
						<li data-id='" . $id . "'>
								<div class='row border-saprater'>
                                <fieldset>
								<div class='".$label_div_class.' '.$star_required."'>
                                    <label for='" . $id . "' class='" . $lable_class .$label_itself_class." col-sm-12'>" . $label . $required_span .  "</label>
                                </div>
								<div class='block ".$input_div_class."'>";
                                    if ($attr['type'] == 'number') {
                                        $output .= " <div class='row'><div class='col-sm-9'>" . $element."</div><div class='col-sm-3'>" . $unit_dd."</div>";
                                    }else{
                                        $output .= $element;
                                    }
                                    $output .= "<span class='note " . $id . "'>$description</span>
								</div>
								</div>
							<div class='clear'></div>
							<div class='attrs clear " . $id . "'>
								<input type='hidden' name='properties[" . $id . "][type]' value='" . $attr['type'] . "'/>
								<input type='hidden' name='properties[" . $id . "][formref_id]' value='" . $attr['formref_id'] . "'/>
								<input type='hidden' name='properties[" . $id . "][name]' value='" . $id . "'/>
								<input type='hidden' name='properties[" . $id . "][value]' class='label' value='" . $label . "' />" . $valuessssss . "
								<input type='hidden' name='properties[" . $id . "][required]' class='required' value='" . $required . "' / >
								<input type='hidden' name='properties[" . $id . "][required_vars]' class='required_vars' value='" . $required_vars . "' />
								<input type='hidden' name='properties[" . $id . "][description]' class='description' value='" . $description . "'>
								<input type='hidden' name='properties[" . $id . "][sync_prod]' id='sync_prod_" . $id . "' class='sync_prod' value='" . $sync_prods . "'>
								<input type='hidden' name='properties[" . $id . "][optionchk]' id='optionchk_" . $id . "' class='optionchk' value='" . $opt_chk . "'>
							</div>
                            </fieldset>
						</li>
					";
                }


            }

            if ($element) {
                //set output to AJAX
                $elements_output[$id] = rawurldecode($output);
                //rawurldecode(html_entity_decode($output));
            }
            //sleep(1);
        }

        echo json_encode($elements_output);
        die;
    }

    function element_display_bulk_instruction($attributes) {

    	$label_div_class=" col-md-3 ";
    	$label_itself_class=" form_label ";
    	$input_div_class=" col-md-7 ";
    	$input_itself_class=" form-control ";
    	require(__DIR__ . '/../yii_init.php');

        //echo "<pre>",print_r($attributes),"</pre>";die;
       //if no load more then sep attribute
        $elements_output = array();
        foreach ($attributes as $attr) {
            $load_prev_chked = false;
            if (isset($attr['load_prev']) && $attr['load_prev'] == 1) {
               // if (isset($attr['no_load_more']) && $attr['no_load_more'] == 1) {
                    $load_prev_chked = true;
                //}
            }
            if ($load_prev_chked) {
                $attr['text_val'] = "";
                $attr['value'] = "";
            }
            $formref_id = isset($attr['formref_id'])?$attr['formref_id']:0;
            $lable_class = '';
            $pro = false;
            $valuessssss = '';
            //$required='1';
            $required_vars = '';
            $description = '';
            $contents = '';
            $required_class = '';
            $sync_prods = '';
            $field_types = '';
            $opt_chk = '';
            $noloadpre = '';
            $default_answer = '';
            $default_unit = 0;
            $wfloadprevoius = 0;
            $is_new_st_addded = isset($attr['is_new_st_addded'])?$attr['is_new_st_addded']:0;
            $formtype = isset($attr['formtype'])?$attr['formtype']:'add';
            //$required_span='';
            if (isset($attr['id']) && $attr['id'] != '')
                $id = $attr['id'];
            else
                $id = 'element_' . uniqid();
            $dropdown_val = array('' => 'No Content');
            $btn_value = 'No Content';
            $star_required="";
            //Required field.
            //if(isset($attr['required']) && $attr['required']!=1)
            if (isset($attr['required']) && $attr['required'] == 0) {
                if ($attr['type'] != 'text') {
                    $required = $attr['required'];
                    $required_class = 'required-entry';
                    $star_required="required";
                    //$required_span = '<span class="data-required require-asterisk-again">*</span>';
                    $required_span='';
                } else {
                    //$required_span = '<span class="data-required require-asterisk-again">*</span>';
                    $required_span='';
                    $star_required="required";
                }
            } else {
                $required_span = '';
            }
            $date_val="";
            if ($attr['type'] == "datetime") {
            	if (isset($attr['value']) && $attr['value'] != '') {
            		$date_val=$attr['value'];
            	}
            }
            if (isset($attr['required_vars']) && $attr['required_vars'] != '') {
                $attr['required_vars'] = rawurldecode($attr['required_vars']);
                $required_vars = $attr['required_vars'];
            }
            if (isset($attr['description']) && $attr['description'] != '') {
                $attr['description'] = rawurldecode($attr['description']);
                $description = ucfirst($attr['description']);
            }
            if (isset($attr['text_val']) && $attr['text_val'] != "") {
                $attr['text_val'] = rawurldecode($attr['text_val']);
                $attr['text_val'] = str_replace("<br>", "\n", $attr['text_val']);
                $contents = html_entity_decode(($attr['text_val']));
            }

            if (isset($attr['sync_prod']) && $attr['sync_prod'] != '') {
                $attr['sync_prod'] = rawurldecode($attr['sync_prod']);
                $sync_prods = $attr['sync_prod'];
            }
            /*if (isset($attr['field_type']) && $attr['field_type'] != '') {
                $attr['field_type'] = rawurldecode($attr['field_type']);
                $field_types = $attr['field_type'];
                if($attr['field_type'] == 'number' || strtolower($field_types) == 1){
					$field_types .= " numeric-field-qu";
                }
            }*/
            if (isset($attr['optionchk']) && $attr['optionchk'] != '') {
                $attr['optionchk'] = rawurldecode($attr['optionchk']);
                $opt_chk = $attr['optionchk'];
            }

            if (isset($attr['noloadpre']) && $attr['noloadpre'] != '') {
                $attr['noloadpre'] = rawurldecode($attr['noloadpre']);
                $noloadpre = $attr['noloadpre'];
            }

			if (isset($attr['default_answer']) && $attr['default_answer'] != '') {
                $attr['default_answer'] = rawurldecode($attr['default_answer']);
                $default_answer = $attr['default_answer'];
            }

            if (isset($attr['default_unit']) && $attr['default_unit'] != '') {
                $attr['default_unit'] = rawurldecode($attr['default_unit']);
                $default_unit = $attr['default_unit'];
            }

            $ariareq = '';
            $required = 0;
            if($required_class != ''){
                $ariareq = 'aria-required = "true" title="This field is required" ';
                $required = 1;
            }

            //give the text box a differnt label
            if (isset($attr['label']) && $attr['label'] != "") {
                $label = ucfirst(($attr['label']));
                $pro = true;
            } else
                $label = ($attr['type'] == 'text') ? ' ' : 'No Label';


            //Start: Logic for displaying unit
            $unit_dd = '';
            if ($attr['type'] == 'number') {
                $unit_dd = "<select name='properties[".$formref_id."][" . $id . "][unit_id]' class='clsunit $input_itself_class'><option value=''>Unit</option>";
                $list = Yii::$app->db->createCommand('select * from tbl_unit WHERE remove=0')->queryAll();
                foreach ($list as $unititem) {
					$unitselected = '';
					if($default_unit != 0 && $default_unit == $unititem['id'])
						$unitselected = 'selected="selected"';
                    $unit_dd.="<option value=" . $unititem['id'] . " $unitselected>" . $unititem['unit_name'] . "</option>";
                }
                $unit_dd.="</select>";
            }

            if (isset($attr['wfloadprevoius']) && $attr['wfloadprevoius'] != '') {
                $attr['wfloadprevoius'] = rawurldecode($attr['wfloadprevoius']);
                $wfloadprevoius = $attr['wfloadprevoius'];
            }

            if (isset($attr['values']) && $attr['values'] != '') {
                $attr['values'] = rawurldecode($attr['values']);
                $attr['values'] = str_replace('&amp;', '&', $attr['values']);
				//$attr['values'] = 'Please Select;'.$attr['values'];
                $dropdown_val1 = explode(';', html_entity_decode($attr['values']));

                if ($attr['type'] == "dropdown") {
					//$dropdown_val = array_merge(array('0' => 'Please Select'), $dropdown_val1);
					//echo "<pre>",print_r($opt_chk),"</pre>";
					$dropdown_val = array('0'=>'Please Select');
					if($attr['values_ids'] != ''){
						$explode_ids = explode(";",$attr['values_ids']);
						foreach($dropdown_val1 as $key=>$dropdown){
							if(isset($explode_ids[$key])){
								$dropdown_val[$explode_ids[$key]] = str_replace(',', '&#44;', htmlentities($dropdown));
                                //$dropdown;
								/*unset($dropdown_val1[$key]);*/
							}
						}
					} //echo "<pre>",print_r($dropdown_val),"</pre>";
					$selectedkey = array_search($opt_chk, $dropdown_val);
                    if($selectedkey == 0){
                    	$selectedkey=$opt_chk;
                    }
                } else {
					$dropdown_val = array();
					if($attr['values_ids'] != ''){
						$explode_ids = explode(";",$attr['values_ids']);
						foreach($dropdown_val1 as $key=>$dropdown){
							if(isset($explode_ids[$key])){
								$dropdown_val[$explode_ids[$key]] = str_replace(',', '&#44;', htmlentities($dropdown));
                                //$dropdown;
								/*unset($dropdown_val1[$key]);*/
							}
						}
					}
				}
                $pro = true;
                $valuessssss = "<input class='values' type='hidden' name='properties[".$formref_id."][" . $id . "][values]' value='" . $attr['values'] . "' />";
            }

            $ivalue = "";
            $svalue = array();
            $chsel_arr = array();

            if (isset($attr['value']) && $attr['value'] != '') {
                $btn_value = rawurldecode($attr['value']);
                $pro = true;
                $valuessssss = "<input class='value' type='hidden' name='properties[".$formref_id."][" . $id . "][value]' value='" . $attr['value'] . "' />";
                if ($attr['type'] == 'textarea') {
                    $attr['value']=str_replace("+", "plus",$attr['value']);
                    $ivalue =$attr['value'];
                	//$ivalue = htmlentities(html_entity_decode(htmlspecialchars($attr['value'])));
                    //strip_tags($attr['value']);
                    //if($id=='element_569e348427091'){echo "<prE>",print_r($ivalue),"</pre>"; die;}
                    $ivalue = str_replace("<br>", "\n", $ivalue);
                    $contents = str_replace("plus","+",html_entity_decode(rawurldecode(str_replace("<br>", "\n", $ivalue))));
                    //$ivalue = str_replace("plus","+",$ivalue);

                } else
                    $ivalue = $attr['value'];

                if (isset($attributes['editinstruform'][0]) && $attributes['editinstruform'][0] == "editinstruform") {
                    array_push($svalue, $attr['value']);
                } else if($attr['type'] == 'dropdown'){
                	$svalue = array("0"=>$selectedkey);
                } else {
                    $svalue = $selectedkey;
                }
                if ($attr['type'] == 'checkbox') {
                    $chsel_arr = explode(",", rawurldecode($attr['value']));
                }
            } else if($attr['type'] == 'dropdown' && $selectedkey!="" && $selectedkey != 0){
				$svalue = array("0"=>$selectedkey);
            }
            $checked = '';
            if (isset($chsel_arr[0]) && $chsel_arr[0] == 'on')
                $checked = 'checked="checked"';
            $element_ck = '<input type="checkbox" '.$ariareq.' name="'.$formref_id.'[' . $id . '][]"  class="' . $required_class . $input_itself_class . '" ' . $checked . ' id="chk_'.$id.'"><label for="chk_'.$id.'"></label>';
            if ($attr['type'] == 'checkbox') {
                if (isset($attr['values']) && $attr['values'] != '') {
                	$element_chk = '';
                    $count_chk = 1;
                    $opt_chks = explode(",", $opt_chk);
                    //echo "<pre>",print_r($dropdown_val),print_r($opt_chks); //exit;
                    foreach ($dropdown_val as $key=>$va) {
                        $chkboxVal = str_replace(',', '&#44;', $va);

                        $checked = "";
						if (isset($attributes['editinstruform'][0]) && $attributes['editinstruform'][0] == "editinstruform") {
                            if (in_array($count_chk, $chsel_arr))
                                $checked = 'checked="checked"';
                        } else {
							//echo "<pre>",print_r($opt_chks),"</pre>";
                        	if(!empty($opt_chks)){
	                        	//foreach ($opt_chks as $optionchk){
                                            if(is_numeric($opt_chks)) {
                                                    if ($key == $opt_chks) {
                                                        $checked = "checked='checked'";
                                                    }
                                            } else if (is_array($opt_chks) && !empty($opt_chks)) {
                                                if (in_array($key, $opt_chks)) {
                                                    $checked = "checked='checked'";
                                                }
                                            } else{
                                                if (in_array($chkboxVal, $opt_chks)) {
                                                    $checked = "checked='checked'";
                                                }
                                            }
	                        	//}
                        	}

                            if (in_array($count_chk, $chsel_arr)){
                            	$checked = 'checked="checked"';
                            }
                        }

                        if ($element_chk == '')
                            $element_chk = '<input type="checkbox" '.$ariareq.' aria-label="'.$va.'" name="'.$formref_id.'[' . $id . '][]" value="' . $key . '" class="' . $required_class . $input_itself_class. '" ' . $checked . ' id="chk_'.$key.'_'.$id.'"><label for="chk_'.$key.'_'.$id.'">' . $va . '&nbsp;</label>';
                        else
                            $element_chk.='<input type="checkbox" '.$ariareq.' aria-label="'.$va.'" name="'.$formref_id.'[' . $id . '][]" value="' . $key . '" class="' . $required_class . $input_itself_class. '" ' . $checked . ' id="chk_'.$key.'_'.$id.'"><label for="chk_'.$key.'_'.$id.'">' . $va . '&nbsp;</label>';

                        $count_chk++;
                    }
                    if ($element_chk != '')
                        $element_ck = $element_chk;
                }
            }
            $element_ra = '<input type="radio" '.$ariareq.' name="'.$formref_id.'[' . $id . ']" class="' . $required_class . $input_itself_class . '" id="rdo_'.$id.'"><label for="rdo_'.$id.'"></label>';
            if ($attr['type'] == 'radio') {
                if (isset($attr['values']) && $attr['values'] != '') {
                    $element_rad = '';
                    $count_rd = 1;
                    //echo "<Pre>";print_r($dropdown_val);die;
                    $total_cnt = count($dropdown_val);
                    foreach ($dropdown_val as $rdokey=>$va) {
                        $radioVal = str_replace(',', '&#44;', $va);
                        $opt_chks = explode(",", $opt_chk);
                        $checked = '';
                        //echo "<Pre>";print_r($opt_chks);die;
                        if (isset($attributes['editinstruform'][0]) && $attributes['editinstruform'][0] == "editinstruform") {
                                $checked = '';
                                if ($ivalue != '') {
                                    if ($count_rd == $ivalue)
                                        $checked = 'checked="checked"';
                            }
                        } else { // echo "<pre>",print_r($radioVal),print_r($opt_chk),"</pre>";
                        	if(is_numeric($opt_chk)){
                                    if ($rdokey == $opt_chk) {
                                        $checked = "checked='checked'";
                                    }
                        	}else{
                                    if ($radioVal == $opt_chk) {
                                        $checked = "checked='checked'";
                                    }
                        	}
                            if($count_rd>0) {
                                if (in_array($rdokey, $opt_chks)) {
                                    $checked = "checked='checked'";
                                }
                            }
                        }
                        if ($element_rad == '')
                            $element_rad = '<input type="radio" '.$ariareq.' aria-posinset="'.$count_rd.'" aria-setsize="'.$total_cnt.'" name="'.$formref_id.'[' . $id . ']" value="' . $rdokey . '"  class="' . $required_class . $input_itself_class . '" ' . $checked . ' aria-label="'.$va.'" id="chk_'.$rdokey.'_'.$id.'"><label for="chk_'.$rdokey.'_'.$id.'">' . $va . '&nbsp;</label>';
                        else
                            $element_rad.='<input type="radio" '.$ariareq.' aria-posinset="'.$count_rd.'" aria-setsize="'.$total_cnt.'" name="'.$formref_id.'[' . $id . ']" value="' . $rdokey . '"  class="' . $required_class . $input_itself_class . '" ' . $checked . ' aria-label="'.$va.'" id="chk_'.$rdokey.'_'.$id.'"><label for="chk_'.$rdokey.'_'.$id.'">' . $va . '&nbsp;</label>';

                        $count_rd++;
                    }
                    if ($element_rad != '')
                        $element_ra = $element_rad;
                }
            }

          if($noloadpre == 1){$ivalue = '';}else if($noloadpre == 0){$ivalue = str_replace("\'", "'", $ivalue);}

          $dropClass = 'taskdropdown';
          switch ($attr['type']) {
                case 'text':
                    $element = form_textarea(array(
                        'class' => 'wysiwyg instruction_multi10000 ' . $required_class. $input_itself_class,
                        'id' => $id,
                        'name' => $formref_id.'['.$id.']',
                        'rows' => 5,
                        'cols' => 50,
                        'style' => 'display:none;',
                        'maxlength'=>'10000',
                    ));
                    $lable_class = 'text_lable_class';
                    break;
                case 'textarea':
					if ($formtype == 'custodianadd' || ($formtype == 'projectadd' && (($wfloadprevoius == 1 && $load_prev_chked == true) || ($wfloadprevoius == 0))) || ($formtype == 'projectedit' && $is_new_st_addded == 1)) {
						$ivalue = $default_answer;
					}

                    $element = form_textarea(array(
                        'name' => $formref_id.'['.$id.']',
                        'id' => 'lbl-'.$id,
                        'rows' => 5,
                        'cols' => 50,
                        'value' => $ivalue,
                        'aria-required'=>$required==1?'true':'false',
                        'class' => ' instruction_multi10000 '.$required_class. $input_itself_class,
                    ));
                    //element_58463ddea91f6
                    //print_r($attr);
                    //echo $element;die;

                    break;
                case 'textbox':
					// if ($formtype == 'custodianadd' || ($formtype == 'projectadd')) {
					if ($formtype == 'custodianadd' || ($formtype == 'projectadd' && (($wfloadprevoius == 1 && $load_prev_chked == true) || ($wfloadprevoius == 0))) || ($formtype == 'projectedit' && $is_new_st_addded == 1)) {
						$ivalue = $default_answer;
					}
					//$ivalue = str_replace(array("'", '"'), array("&#39;", "&quot;"), $ivalue);
					$element = form_input(array('name' => $formref_id.'['.$id.']', 'aria-required'=>$required==1?'true':'false','id' => 'lbl-'.$formref_id.'['.$id.']', 'class' => 'jf_text form_role_input instruction_single255 ' . $required_class . ' ' . $input_itself_class), $ivalue);
				   break;
                case 'number':
					//if ($formtype == 'custodianadd' || ($formtype == 'projectadd')) {
					if ($formtype == 'custodianadd' || ($formtype == 'projectadd' && (($wfloadprevoius == 1 && $load_prev_chked == true) || ($wfloadprevoius == 0))) || ($formtype == 'projectedit' && $is_new_st_addded == 1)) {
						$ivalue = $default_answer;
					}
					$element = form_input(array('name' => $formref_id.'['.$id.']', 'aria-required'=>$required==1?'true':'false','id' => 'lbl-'.$formref_id.'['.$id.']', 'class' => 'jf_text form_role_input instruction_single255 ' . $required_class . ' user_input numeric-field-qu negative-key ' .$input_itself_class), $ivalue);
                    break;
                case 'dropdown': $element = "<span class='taskspandrop' style='width: 409px !important;'>" . form_dropdown($formref_id.'['.$id.']', $dropdown_val, $svalue, 'class="' . $required_class . $input_itself_class . " " . $dropClass . '" '.$ariareq) . "</span>";
                	break;
                case 'checkbox': $element = '<span class="values custom-full-width ' . $id . '">' . $element_ck . '</span>';
                    break;
                case 'radio': $element = '<span class="values custom-full-width ' . $id . '">' . $element_ra . '</span>';
                    break;
                case 'datetime':
                    if($required_class != ''){
                        $element = '<div class="input-group calender-group">'.form_input(array("aria-describedby"=>'lbl-'.$formref_id.'['.$id.']','aria-required' => "true",'id'=>'lbl-'.$id,'name' => $formref_id.'['.$id.']', 'placeholder' =>$label, 'class' => 'datepickers ' . $required_class . $input_itself_class,'readonly'=>'readonly','value'=>$date_val)).'</div>';
                    }else{
                        $element = '<div class="input-group calender-group">'.form_input(array('id'=>'lbl-'.$id,'name' => $formref_id.'['.$id.']', 'placeholder' => 'Choose a date', 'class' => 'datepickers ' . $required_class . $input_itself_class,'readonly'=>'readonly','value'=>$date_val)).'</div>';
                    }
                    break;
                case 'fileupload': $element = form_upload($formref_id.'['.$id.']');
                    break;
                case 'button': $element = form_input(array('name' => $formref_id.'['.$id.']', 'value' => $btn_value, 'type' => 'button'));
                    break;
                default: $element = null;
                    break;

                //echo $element;
            }

            if ($pro) {
                //$this->properties($attr);
            }
            $fieldsetContent = $fieldsetContentEnd = '';
            if($attr['type'] == 'checkbox' || $attr['type'] == 'radio') {
                $fieldsetContent    = '<fieldset><legend class="sr-only">'.$label.'</legend>';
                $fieldsetContentEnd = '</fieldset>';
            }
            //basic output list element.
            if (isset($attr['edit']) && $attr['edit'] != '') {
                if (isset($contents) && $contents != '') {
                    if (isset($attr['text_val']) && $attr['text_val'] != ""){
                        //echo $contents;
                        //die;
                        //$contents = html_entity_decode(rawurldecode($contents));
                        $label = str_replace(",", "", $contents);
                        $label = str_replace("+", " ", $contents);
                        $label = str_replace("\n", "<br>", $contents);
                        //echo "label",$label;die;
                    }
                }
                $colon = ($attr['type'] == 'text') ? '' : ':';
                if (isset($attr['text_val']) && $attr['text_val'] != "") {
					$output = "
                                            <li data-id='" . $id . "'>
                                                <div class='row border-saprater'>
                                                    <div class='col-md-12'><label for='lbl-".$formref_id."[" . $id . "]' class='" . $lable_class .$label_itself_class." col-sm-12'>" . $label . $required_span .  "</label></div>
                                                    <div class='block'>
                                                    <span class='note " . $id . "'>$description</span>
                                                                            </div>
                                                                            </div>
                                                                            <div class='clear'></div>
                                                                            <div class='attrs clear " . $id . "'>
                                                    <input type='hidden' name='properties[".$formref_id."][" . $id . "][type]' value='" . $attr['type'] . "' />
                                                    <input type='hidden' name='properties[".$formref_id."][" . $id . "][formref_id]' value='" . $attr['formref_id'] . "' />
                                                    <input type='hidden' name='properties[".$formref_id."][" . $id . "][name]' value='" . $id . "'/>
                                                    <input type='hidden' name='properties[".$formref_id."][" . $id . "][value]' class='label' value='" . $label . "' />" . $valuessssss . "
                                                    <input type='hidden' name='properties[".$formref_id."][" . $id . "][required]' class='required' value='" . $required . "' / >
                                                    <input type='hidden' name='properties[".$formref_id."][" . $id . "][required_vars]' class='required_vars' value='" . $required_vars . "' />
                                                    <input type='hidden' name='properties[".$formref_id."][" . $id . "][description]' class='description' value='" . $description . "'>
                                                    <input type='hidden' name='properties[".$formref_id."][" . $id . "][sync_prod]' id='sync_prod_" . $id . "' class='sync_prod' value='" . $sync_prods . "'>
                                                    <input type='hidden' name='properties[".$formref_id."][" . $id . "][optionchk]' id='optionchk_" . $id . "' class='optionchk' value='" . $opt_chk . "'>
                                                </div>
                                            </li>
					";
                }else{
					$output = "
						<li data-id='" . $id . "'>
								<div class='row border-saprater'>
								<div class='".$label_div_class.' '.$star_required."'><label for='lbl-".$formref_id."[" . $id . "]' class='" . $lable_class .$label_itself_class." col-sm-12'>" . $label . $required_span .  "</label></div>
								<div class='block ".$input_div_class."'>".$fieldsetContent;
								if ($attr['type'] == 'number') {
                                    $output .= " <div class='row'><div class='col-sm-9'>" . $element."</div><div class='col-sm-3'>" . $unit_dd."</div>";
								}else{
                                    $output .= $element;
								}
								$output .= "<span class='note " . $id . "'>$description</span>
								".$fieldsetContentEnd."</div>
								</div>
							<div class='clear'></div>
							<div class='attrs clear " . $id . "'>
                                                            <input type='hidden' name='properties[".$formref_id."][" . $id . "][type]' value='" . $attr['type'] . "'/>
                                                            <input type='hidden' name='properties[".$formref_id."][" . $id . "][formref_id]' value='" . $attr['formref_id'] . "'/>
                                                            <input type='hidden' name='properties[".$formref_id."][" . $id . "][name]' value='" . $id . "'/>
                                                            <input type='hidden' name='properties[".$formref_id."][" . $id . "][value]' class='label' value='" . $label . "' />" . $valuessssss . "
                                                            <input type='hidden' name='properties[".$formref_id."][" . $id . "][required]' class='required' value='" . $required . "' / >
                                                            <input type='hidden' name='properties[".$formref_id."][" . $id . "][required_vars]' class='required_vars' value='" . $required_vars . "' />
                                                            <input type='hidden' name='properties[".$formref_id."][" . $id . "][description]' class='description' value='" . $description . "'>
                                                            <input type='hidden' name='properties[".$formref_id."][" . $id . "][sync_prod]' id='sync_prod_" . $id . "' class='sync_prod' value='" . $sync_prods . "'>
                                                            <input type='hidden' name='properties[".$formref_id."][" . $id . "][optionchk]' id='optionchk_" . $id . "' class='optionchk' value='" . $opt_chk . "'>
							</div>
						</li>
					";

			    }
            }
			if ($element) {

                //set output to AJAX

                $elements_output[$id] = rawurldecode($output);
                //rawurldecode(html_entity_decode($output));
                //if($attr['id']=='element_58463ddea91f6'){
                if($attr['type'] == 'textarea'){
                    $elements_output[$id] = str_replace("plus","+",$elements_output[$id]);
                }

            }
            //sleep(1);
        }

        echo json_encode($elements_output);
        die;
    }

    /* This function use to display data form value
     * @since:17-1-15
     */

    function element_display_bulk_billing($attributes) {

    	$label_div_class=" col-md-3 ";
    	$label_itself_class=" form_label ";
    	$input_div_class=" col-md-7 ";
    	$input_itself_class=" form-control user_input ";
        //echo "<pre>",  print_r($_REQUEST);die;
        // Create custom command query: modified date: 06-05-2015
        require(__DIR__ . '/../yii_init.php');

        // END : custom command query

        //if no load more then sep attribute
        if (isset($attributes['mediaId'])) {
            $mediaId = $attributes['mediaId'];
        } else {
            $mediaId = array(0=>0);
        }

        $elements_output = array();
        foreach ($attributes as $key => $attr) {

            $load_prev_chked = false;
            if (isset($attr['load_prev']) && $attr['load_prev'] == 1) {
                if (isset($attr['no_load_more']) && $attr['no_load_more'] == 1) {
                    $load_prev_chked = true;
                }
            }
            if ($load_prev_chked) {
                $attr['text_val'] = "";
                $attr['value'] = "";
            }
            $lable_class = '';
            $pro = false;
            $valuessssss = '';
            //$required='1';
            $required_vars = '';
            $description = '';
            $contents = '';
            $required_class = '';
            $sync_prods = '';
            $field_types = '';
            $opt_chk = '';
            $unit_dd = "";
            $default_answer = '';
            $qau = 0;
            $default_unit = 0;
            //$required_span='';

            if (isset($attr['id']) && $attr['id'] != '')
                $id = $attr['id'];
            else
                $id = 'element_' . uniqid();

            $dropdown_val = array('' => 'No Content');
            $btn_value = 'No Content';
			$star_billing_required = "";
            // Required field.
            //if(isset($attr['required']) && $attr['required']!=1)

            if (isset($attr['required']) && $attr['required'] == 0) {
				$star_billing_required = "required";
                if ($attr['type'] != 'text') {
                    $required = $attr['required'];
                    $required_class = 'required-entry';
                    //$required_span = '<span class="data-required">*</span>';
                    $required_span='';
                } else {
                    //$required_span = '<span class="data-required">*</span>';
                    $required_span='';
                }
            } else {
                $required_span = '';
            }
            if (isset($attr['required_vars']) && $attr['required_vars'] != '') {
                $attr['required_vars'] = rawurldecode($attr['required_vars']);
                $required_vars = $attr['required_vars'];
            }
            if (isset($attr['description']) && $attr['description'] != '') {
                $attr['description'] = rawurldecode($attr['description']);
                $description = $attr['description'];
            }
            if (isset($attr['text_val']) && $attr['text_val'] != "") {
                $attr['text_val'] = rawurldecode($attr['text_val']);
                $attr['text_val'] = str_replace("<br>", "\n", $attr['text_val']);
                $contents = html_entity_decode(htmlspecialchars($attr['text_val']));
            }

            if (isset($attr['sync_prod']) && $attr['sync_prod'] != '') {
                $attr['sync_prod'] = rawurldecode($attr['sync_prod']);
                $sync_prods = $attr['sync_prod'];
            }
            /*if (isset($attr['field_type']) && $attr['field_type'] != '') {
                $attr['field_type'] = rawurldecode($attr['field_type']);
                $field_types = $attr['field_type'];
            }*/
            if (isset($attr['optionchk']) && $attr['optionchk'] != '') {
                $attr['optionchk'] = rawurldecode($attr['optionchk']);
                $opt_chk = $attr['optionchk'];
            }

            if (isset($attr['default_answer']) && $attr['default_answer'] != '') {
                $attr['default_answer'] = rawurldecode($attr['default_answer']);
                $default_answer = $attr['default_answer'];
            }

            if (isset($attr['default_unit']) && $attr['default_unit'] != '') {
                $attr['default_unit'] = rawurldecode($attr['default_unit']);
                $default_unit = $attr['default_unit'];
            }

            //give the text box a differnt label
            if (isset($attr['label']) && $attr['label'] != "") {
                $label = ucfirst(htmlentities($attr['label']));
                $pro = true;
            } else
                $label = ($attr['type'] == 'text') ? ' ' : 'No Label';


            //Start: Logic for displaying unit

            if ($attr['type'] == 'number') {
                $unit_dd = "<select aria-label='Select Unit for $label' name='properties[" . $id . "][unit_id][".$mediaId[0]."]' class='clsunit $input_itself_class'><option value=''>Unit</option>";
                $list = Yii::$app->db->createCommand('select * from tbl_unit WHERE remove=0')->queryAll();
                foreach ($list as $unititem) {
					$unitselected = '';
					if($default_unit != 0 && $default_unit == $unititem['id'])
						$unitselected = 'selected="selected"';
                    $unit_dd.="<option value=" . $unititem['id'] . " $unitselected>" . $unititem['unit_name'] . "</option>";
                }
                $unit_dd.="</select>";
            }

            // End :Logic for displaying unit

            if (isset($attr['values']) && $attr['values'] != '') {
                $attr['values'] = rawurldecode($attr['values']);
                $attr['values'] = str_replace('&amp;', '&', $attr['values']);

                $dropdown_val1 = explode(';', html_entity_decode($attr['values']));
                /*if ($attr['type'] == "dropdown") {
                    $unit_dd = "";
                    if (isset($attr['qareportuse']) && $attr['qareportuse'] == 1) {
                        $qau = 1;
                    }
                    $dropdown_val = array_merge(array('0' => 'Please Select'), $dropdown_val1);
                    $selectedkey = array_search($opt_chk, $dropdown_val);
                } else
                    $dropdown_val = $dropdown_val1;*/

                 if ($attr['type'] == "dropdown") {
					$unit_dd = "";
					if (isset($attr['qareportuse']) && $attr['qareportuse'] == 1) {
                        $qau = 1;
                    }

					$dropdown_val = array('0'=>'Please Select');
					if($attr['values_ids'] != ''){
						$explode_ids = explode(";",$attr['values_ids']);
						foreach($dropdown_val1 as $key=>$dropdown){
							if(isset($explode_ids[$key])){
								$dropdown_val[$explode_ids[$key]] = $dropdown;
							}
						}
					}
					$selectedkey = array_search($opt_chk, $dropdown_val);
                    if($selectedkey == 0){
                    	$selectedkey=$opt_chk;
                    }
                } else {
					$dropdown_val = array();
					if($attr['values_ids'] != ''){

						$explode_ids = explode(";",$attr['values_ids']);
						foreach($dropdown_val1 as $key=>$dropdown){
							if(isset($explode_ids[$key])){
								$dropdown_val[$explode_ids[$key]] = $dropdown;
								/*unset($dropdown_val1[$key]);*/
							}
						}
					}
				}
                $pro = true;
                $valuessssss = "<input class='values' type='hidden' name='properties[" . $id . "][values]' value='" . $attr['values'] . "' />";
            }
            $ivalue = "";
            $svalue = array();
            $chsel_arr = array();

            if (isset($attr['value']) && $attr['value'] != '') {
                $btn_value = rawurldecode($attr['value']);
                $pro = true;
                $valuessssss = "<input class='value' type='hidden' name='properties[" . $id . "][value]' value='" . $attr['value'] . "' />";
                if ($attr['type'] == 'textarea') {
                    $unit_dd = "";
                    $ivalue = strip_tags($attr['value']);
                    $ivalue = str_replace("<br>", "\n", $ivalue);
                    $contents = html_entity_decode(rawurldecode(str_replace("<br>", "\n", $ivalue)));
                } else
                    $ivalue = $attr['value'];

                array_push($svalue, $attr['value']);
                if ($attr['type'] == 'checkbox') {
                    $chsel_arr = explode(",", rawurldecode($attr['value']));
                }
            }
            $checked = '';
            if (isset($chsel_arr[0]) && $chsel_arr[0] == 'on')
                $checked = 'checked="checked"';
            $element_ck = '<input type="checkbox" name="' . $mediaId[0] . '[' . $id . '][value][]"  class=" user_input ' . $required_class . ' ' . $sync_prods . '" ' . $checked . ' id="chk_'.$id.'"><label for="chk_'.$id.'"></label><br>';
            if ($attr['type'] == 'checkbox') {
                $unit_dd = "";
                if (isset($attr['values']) && $attr['values'] != '') {
                    $element_chk = '';
                    $count_chk = 0;
					//echo "<pre>",print_r($dropdown_val)," - ",print_r($opt_chk),"</pre>";
                    foreach ($dropdown_val as $key => $va) {
                        $chkboxVal = str_replace(',', '&#44;', $va);
                        $opt_chks = explode(",", $opt_chk);

                        $chkChecked = "";
                        if (in_array($key, $opt_chks)) {
                            $chkChecked = "checked='checked'";
                        }

                        $checked = '';
                        if (in_array($count_chk, $chsel_arr))
                            $checked = 'checked="checked"';

                        if ($element_chk == '')
                            $element_chk = '<div class="col-xs-12"><input aria-label="'.$va.'" type="checkbox" name="' . $mediaId[0] . '[' . $id . '][value][]" value="' . $key . '" style="margin:0 5px 5px 0;" class="user_input statistics-input ' . $required_class . ' ' . $sync_prods . '" ' . $chkChecked .$input_itself_class. ' id="chk_'.$key.'_'.$id.'"><label for="chk_'.$key.'_'.$id.'">' . $va . '</label></div>';
                        else
                            $element_chk.='<div class="col-xs-12"><input aria-label="'.$va.'" type="checkbox" name="' . $mediaId[0] . '[' . $id . '][value][]" value="' . $key . '" class="statistics-input user_input ' . $required_class . ' ' . $sync_prods . '" ' . $chkChecked .$input_itself_class. ' id="chk_'.$key.'_'.$id.'"><label for="chk_'.$key.'_'.$id.'">' . $va . '</label></div>';
                        $count_chk++;
                    }

                    $element_chk.='<br/><span class="note element_'.$key.'_'.$id.'">'.htmlspecialchars($attr['description']).'</span>';
                    if ($element_chk != '')
                        $element_ck = '<legend class="sr-only">'.$label.'</legend>'.$element_chk;
                }
            }
            $element_ra = '<div class="col-xs-12"><input type="radio" name="' . $mediaId[0] . '[' . $id . '][value]" class="statistics-input user_input ' . $required_class . ' ' . $sync_prods .$input_itself_class. '" id="rdo_'.$id.'"><label for="rdo_'.$id.'"></label></div>';
            if ($attr['type'] == 'radio') {
                $unit_dd = "";
            	$qauclasss = "";
                if (isset($attr['qareportuse']) && $attr['qareportuse'] == 1) {
                    $qau = 1;
                    $qauclasss = "quclass";
                }
                if (isset($attr['values']) && $attr['values'] != '') {

                    $element_rad = '';
                    $count_rd = 0;
                    $totRadios = count($dropdown_val);
                    $radioIndex = 1;
                    foreach ($dropdown_val as $keyid => $va) {
                        $radioVal = str_replace(',', '&#44;', $va);
                        $radioChecked = "";
                        if ($keyid == $opt_chk) {
                            $radioChecked = "checked='checked'";
                        }

                        $checked = '';
                        if ($ivalue != '') {
                            if ($ivalue == $ivalue)
                                $checked = 'checked="checked"';
                        }
                        if ($element_rad == '')
                            $element_rad = '<div class="col-xs-12"><input aria-label="'.$va.'" aria-setsize="'.$totRadios.'" aria-posinset="'.$radioIndex++.'" type="radio" name="' . $mediaId[0] . '[' . $id . '][value]" value="' . $keyid . '"  class="statistics-input user_input ' . $required_class . ' ' . $sync_prods . ' ' . $qauclasss . '" ' . $radioChecked . ' id="rdo_'.$keyid.'_'.$id.'"><label for="rdo_'.$keyid.'_'.$id.'">' . $va . '</label></div>';
                        else
                            $element_rad.='<div class="col-xs-12"><input aria-label="'.$va.'" type="radio"  aria-setsize="'.$totRadios.'" aria-posinset="'.$radioIndex++.'"  name="' . $mediaId[0] . '[' . $id . '][value]" value="' . $keyid . '"  class="statistics-input user_input ' . $required_class . ' ' . $sync_prods . ' ' . $qauclasss . '" ' . $radioChecked . ' id="rdo_'.$keyid.'_'.$id.'"><label for="rdo_'.$keyid.'_'.$id.'">' . $va . '</label></div>';
                        $count_rd++;
                    }
                    if ($element_rad != '')
                        $element_ra = '<legend class="sr-only">'.$label.'</legend>'.$element_rad;
                }
            }
            $ivalue = str_replace("\'", "'", $ivalue);
            $dropClass = 'taskdropdown';

            switch ($attr['type']) {
                case 'text':
                    $element = form_textarea(array(
                        'class' => 'user_input wysiwyg instruction_multi10000 ' . $required_class. $input_itself_class.' '. $sync_prods,
                        'id' => $mediaId[0] . '[' . $id . '][value]',
                        'name' => $mediaId[0] . '[' . $id . '][value]',
                        'rows' => 5,
                        'cols' => 50,
                        'maxlength'=>'2500',
                        'style' => 'display:none;'
                    ));
                    $lable_class = 'text_lable_class';
                    break;
                case 'textarea':

					if ($ivalue == '') {
						$ivalue = $default_answer;
					}

                    $element = form_textarea(array(
                        'name' => $mediaId[0] . '[' . $id . '][value]',
                        'rows' => 5,
                        'aria-labelledby' => "frm_lbl_$mediaId[0]_$id",
                        'cols' => 50,
                        'id'=>'lbl-'.$id,
                        'value' => $ivalue,
                        'maxlength'=>'2500',
                        'class' => 'user_input instruction_multi2500 '.$required_class.$input_itself_class . ' ' . $sync_prods,
                    ));
                    break;
                case 'textbox':

					if ($ivalue == '') {
						$ivalue = $default_answer;
					}

					/*if (strtolower($field_types) == 'number' || strtolower($field_types) == 1) {
						$element = form_input(array('name' => $mediaId[0] . '[' . $id . '][value]','maxlength'=>'255', 'class' => 'user_input numeric-field-qu jf_text form_role_input instruction_single255 ' . $required_class . ' ' . $sync_prods . ' ' . $field_types.$input_itself_class), $ivalue);
					}else{*/
						$element = form_input(array('aria-labelledby' => "frm_lbl_$mediaId[0]_$id", 'name' => $mediaId[0] . '[' . $id . '][value]','maxlength'=>'255','id'=>'lbl-'.$id, 'class' => 'user_input  jf_text form_role_input instruction_single255 ' . $required_class . ' ' . $sync_prods . ' ' .$input_itself_class), $ivalue);
					//}
                    break;
                case 'number':

					if ($ivalue == '') {
						$ivalue = $default_answer;
					}

					$element = form_input(array('aria-labelledby' => "frm_lbl_$mediaId[0]_$id", 'name' => $mediaId[0] . '[' . $id . '][value]','maxlength'=>'255','id'=>'lbl-'.$id, 'class' => 'user_input numeric-field-qu negative-key jf_text form_role_input instruction_single255 ' . $required_class . ' ' . $sync_prods . ' ' . $field_types.$input_itself_class), $ivalue);

                    break;
               // case 'dropdown': $element = "<span class='taskspandrop' style='width: 409px !important;'>" . form_dropdown($mediaId[0] . '[' . $id . '][value]', $dropdown_val, $selectedkey, 'aria-labelledby="frm_lbl_'.$mediaId[0].'_'.$id.'"', 'class="user_input ' . $required_class . " " . $sync_prods . " " . $dropClass . $input_itself_class.'"') . "</span>";
               //     break;
               case 'dropdown':
               $ariareq='aria-labelledby="frm_lbl_'.$mediaId[0].'_'.$id.'"';
               $element = "<span class='taskspandrop' style='width: 409px !important;'>" . form_dropdown($mediaId[0] . '[' . $id . '][value]', $dropdown_val, $selectedkey, 'class=" user_input ' . $required_class . $input_itself_class . " " . $dropClass . '" '.$ariareq) . "</span>";
                	break;
                case 'checkbox': $element = '<span class="values row custom-full-width ' . $id . '">' . $element_ck . '</span>';
                    break;
                case 'radio': $element = '<span class="values row custom-full-width ' . $id . '">' . $element_ra . '</span>';
                    break;
                case 'datetime': $element = '<div class="input-group calender-group">'.form_input(array('aria-labelledby' => "frm_lbl_$mediaId[0]_$id",'id'=>'lbl-'.$mediaId[0] . $id,'name' => $mediaId[0] . '[' . $id . '][value]', 'placeholder' => 'Choose a date','readonly'=>'readonly', 'class' => 'user_input datepickers ' . $required_class . $input_itself_class. ' ' . $sync_prods), $ivalue).'</div>';
                    break;
                case 'fileupload': $element = form_upload($id,'','aria-labelledby="frm_lbl_'.$mediaId[0].'_'.$id.'"');
                    break;
                case 'button': $element = form_input(array('name' => $id, 'value' => $btn_value, 'type' => 'button'));
                    break;
                default: $element = null;
                    break;

                //echo $element;
            }
            if ($pro) {
                //$this->properties($attr);
            }
            //basic output list element.
            if (isset($attr['edit']) && $attr['edit'] != '') {
            	$label_div_class=' col-md-3 ';
                if (isset($contents) && $contents != '') {
                    if (isset($attr['text_val']) && $attr['text_val'] != "") {
                        $label = str_replace(",", "", $contents);
                        $label = str_replace("+", " ", $contents);
                        $label = str_replace("\n", "<br>", $contents);

                        /*$contents = html_entity_decode(rawurldecode($contents));
                        $label = str_replace(",", "", $contents);
                        $label = str_replace("+", " ", $contents);
                        $label = str_replace("\n", "<br>", $contents);*/
                        $label_div_class=' col-md-12 ';
                    }else{
                    	$label_div_class=' col-md-3 ';
                    }
                }

                $colon = ($attr['type'] == 'text') ? '' : ':';
                /*$output = "
					<li>
					<label for='" . $id . "' media-id='$mediaId[0]' class='" . $lable_class . "' title='" . $description . "'>" . $label . $required_span . ' ' . $colon . "</label>
							<div class='block'>" . $element . $unit_dd . "
							<span class='note " . $id . "'>$description</span></div>
						<div class='clear'></div>
						<div class='attrs clear " . $id . "'>
							<input type='hidden' name='properties[" . $id . "][name]' value='" . $id . "'/>
							<input type='hidden' name='properties[" . $id . "][value]' class='label' value='" . $label . "' />" . $valuessssss . "
							<input type='hidden' name='properties[" . $id . "][required]' class='required' value='" . $required . "' / >
							<input type='hidden' name='properties[" . $id . "][qareportuse]' value='" . $qau . "' / >
							<input type='hidden' name='properties[" . $id . "][required_vars]' class='required_vars' value='" . $required_vars . "' />
							<input type='hidden' name='properties[" . $id . "][description]' class='description' value='" . $description . "'>
							<input type='hidden' name='properties[" . $id . "][sync_prod]' id='sync_prod_" . $id . "' class='sync_prod' value='" . $sync_prods . "'>
                            <input type='hidden' name='properties[" . $id . "][field_type]' id='field_type_" . $id . "' class='field_type' value='" . $field_types . "'>
                            <input type='hidden' name='properties[" . $id . "][optionchk]' id='optionchk_" . $id . "' class='optionchk' value='" . $opt_chk . "'>
                            <input type='hidden' name='$mediaId[0][" . $id . "][label]' value='" . $label . "' />
                            <input type='hidden' name='$mediaId[0][" . $id . "][number]' value='" . $field_types . "' />
                            <input type='hidden' name='$mediaId[0][" . $id . "][qareportuse]' value='" . $qau . "' / >
						</div>
					</li>
				";*/
                 if (isset($attr['text_val']) && $attr['text_val'] != "") {
                 	$output = "<li data-id='" . $id . "'>
			<div class='row border-saprater'>
                        	<div class='col-md-12'><label for='lbl-" . $id . "' data-for-name='".$mediaId[0]."[".$id."][value]' media-id='$mediaId[0]' class='" . $lable_class .$label_itself_class."' title='" . $description . "'>" . $label . $required_span . $colon . "</label></div>

                 	</div>
                        <div class='clear'></div>
                        <div class='attrs clear " . $id . "'>
                            <input type='hidden' name='properties[" . $id . "][name]' value='" . $id . "'/>
                            <input type='hidden' name='properties[" . $id . "][value]' class='label' value='" . $label . "' />" . $valuessssss . "
                            <input type='hidden' name='properties[" . $id . "][required]' class='required' value='" . $required . "' / >
                            <input type='hidden' name='properties[" . $id . "][required_vars]' class='required_vars' value='" . $required_vars . "' />
                            <input type='hidden' name='properties[" . $id . "][description]' class='description' value='" . $description . "'>
                            <input type='hidden' name='properties[" . $id . "][sync_prod]' id='sync_prod_" . $id . "' class='sync_prod' value='" . $sync_prods . "'>
                            <input type='hidden' name='properties[" . $id . "][optionchk]' id='optionchk_" . $id . "' class='optionchk' value='" . $opt_chk . "'>
                            <input type='hidden' name='$mediaId[0][" . $id . "][label]' value='" . $label . "' />
                            <input type='hidden' name='$mediaId[0][" . $id . "][number]' value='" . $field_types . "' />
                            <input type='hidden' name='$mediaId[0][" . $id . "][qareportuse]' value='" . $qau . "' / >
                        </li>";
                 }else{
                $output = "
					<li data-id='" . $id . "'>
							<div class='row border-saprater'>
                            <fieldset>
							<div class='".$label_div_class.' '.$star_billing_required."'>
                            <label for='lbl-" . $id . "' id='frm_lbl_$mediaId[0]_$id'  data-for-name='".$mediaId[0]."[".$id."][value]' media-id='$mediaId[0]' class='" . $lable_class .$label_itself_class."' title='" . $description . "'>" . $label . $required_span . $colon . "</label>
                            </div>
							<div class='block ".$input_div_class."'>";
                			if ($attr['type'] == 'number') {
                				$output .= " <div class='row'><div class='col-sm-9'>" . $element."</div><div class='col-sm-3'>" . $unit_dd."</div>";
                			}else{
								$output .= $element;
            				}
							$output .= "<span class='note " . $id . "'>$description</span>
                							</div>
                							</div>
                							<div class='clear'></div>
                							<div class='attrs clear " . $id . "'>
							<input type='hidden' name='properties[" . $id . "][name]' value='" . $id . "'/>
							<input type='hidden' name='properties[" . $id . "][value]' class='label' value='" . $label . "' />" . $valuessssss . "
							<input type='hidden' name='properties[" . $id . "][required]' class='required' value='" . $required . "' / >
							<input type='hidden' name='properties[" . $id . "][required_vars]' class='required_vars' value='" . $required_vars . "' />
							<input type='hidden' name='properties[" . $id . "][description]' class='description' value='" . $description . "'>
							<input type='hidden' name='properties[" . $id . "][sync_prod]' id='sync_prod_" . $id . "' class='sync_prod' value='" . $sync_prods . "'>
                            <input type='hidden' name='properties[" . $id . "][optionchk]' id='optionchk_" . $id . "' class='optionchk' value='" . $opt_chk . "'>
                            <input type='hidden' name='$mediaId[0][" . $id . "][label]' value='" . $label . "' />
                            <input type='hidden' name='$mediaId[0][" . $id . "][qareportuse]' value='" . $qau . "' / >
						</div>
                        </fieldset>
					</li>
				";
                 }
            }

            if ($element) {

                //set output to AJAX
                $elements_output[$id] = rawurldecode(html_entity_decode($output));
            }
            //sleep(1);
        }
        echo json_encode($elements_output);
        die;
    }

    /* This function use to display data form value
     * @since:17-1-15
     */

    function element_display_bulk_data_fields($attributes) {
        //if no load more then sep attribute
        $dataId = $attributes['dataId'];
        $elements_output = array();
        foreach ($attributes as $key => $attr) {
            if ($attributes['dataElementId'] == $key) {

                $load_prev_chked = false;
                if (isset($attr['load_prev']) && $attr['load_prev'] == 1) {
                    if (isset($attr['no_load_more']) && $attr['no_load_more'] == 1) {
                        $load_prev_chked = true;
                    }
                }
                if ($load_prev_chked) {
                    $attr['text_val'] = "";
                    $attr['value'] = "";
                }
                $lable_class = '';
                $pro = false;
                $valuessssss = '';
                //$required='1';
                $required_vars = '';
                $description = '';
                $contents = '';
                $required_class = '';
                $sync_prods = '';
                $field_types = '';
                $opt_chk = '';
                //$required_span='';

                if (isset($attr['id']) && $attr['id'] != '')
                    $id = $attr['id'];
                else
                    $id = 'element_' . uniqid();
                $dropdown_val = array('' => 'No Content');
                $btn_value = 'No Content';

                // Required field.
                //if(isset($attr['required']) && $attr['required']!=1)
                if (isset($attr['required']) && $attr['required'] == 0) {
                    if ($attr['type'] != 'text') {
                        $required = $attr['required'];
                        $required_class = 'required-entry';
                        $required_span = '<span class="data-required">*</span>';
                        //$required_span='';
                    } else {
                        $required_span = '<span class="data-required">*</span>';
                    }
                } else {
                    $required_span = '';
                }
                if (isset($attr['required_vars']) && $attr['required_vars'] != '') {
                    $attr['required_vars'] = rawurldecode($attr['required_vars']);
                    $required_vars = $attr['required_vars'];
                }
                if (isset($attr['description']) && $attr['description'] != '') {
                    $attr['description'] = rawurldecode($attr['description']);
                    $description = $attr['description'];
                }
                if (isset($attr['text_val']) && $attr['text_val'] != "") {
                    $attr['text_val'] = rawurldecode($attr['text_val']);
                    $attr['text_val'] = str_replace("<br>", "\n", $attr['text_val']);
                    $contents = html_entity_decode($attr['text_val']);
                }

                if (isset($attr['sync_prod']) && $attr['sync_prod'] != '') {
                    $attr['sync_prod'] = rawurldecode($attr['sync_prod']);
                    $sync_prods = $attr['sync_prod'];
                }
                /*if (isset($attr['field_type']) && $attr['field_type'] != '') {
                    $attr['field_type'] = rawurldecode($attr['field_type']);
                    $field_types = $attr['field_type'];
                    if($attr['field_type'] == 'number' || strtolower($field_types) == 1){
                    	$field_types .= " numeric-field-qu integer";
                    }
                }*/
                if (isset($attr['optionchk']) && $attr['optionchk'] != '') {
                    $attr['optionchk'] = rawurldecode($attr['optionchk']);
                    $opt_chk = $attr['optionchk'];
                }

                if (isset($attr['values']) && $attr['values'] != '') {
                    $attr['values'] = rawurldecode($attr['values']);
                    $attr['values'] = str_replace('&amp;', '&', $attr['values']);
                    $dropdown_val1 = explode(';', $attr['values']);
                    if ($attr['type'] == "dropdown") {
                        $dropdown_val = array_merge(array('0' => 'Please Select'), $dropdown_val1);
                        $selectedkey = key_exists($opt_chk, $dropdown_val)?$opt_chk:"0";
                    } else
                        $dropdown_val = $dropdown_val1;
                    $pro = true;
                    $valuessssss = "<input class='values' type='hidden' name='properties[" . $id . "][values]' value='" . $attr['values'] . "' />";
                }
                $ivalue = "";
                $svalue = array();
                $chsel_arr = array();

                if (isset($attr['value']) && $attr['value'] != '') {
                    $btn_value = rawurldecode($attr['value']);
                    $pro = true;
                    $valuessssss = "<input class='value' type='hidden' name='properties[" . $id . "][value]' value='" . $attr['value'] . "' />";
                    if ($attr['type'] == 'textarea') {
                        $ivalue = strip_tags($opt_chk);
                        $ivalue = str_replace("<br>", "\n", $opt_chk);
                        $contents = html_entity_decode(rawurldecode(str_replace("<br>", "\n", $ivalue)));
                    } else
                        $ivalue = $attr['value'];

                    array_push($svalue, $attr['value']);
                    if ($attr['type'] == 'checkbox') {
                        $chsel_arr = explode(",", rawurldecode($attr['value']));
                    }
                }
                $checked = '';
                if (isset($chsel_arr[0]) && $chsel_arr[0] == 'on')
                    $checked = 'checked="checked"';
                $element_ck = '<input type="checkbox"  name="' . $dataId . '[' . $id . '][]"  class="' . $required_class . '" ' . $checked . ' id="chk_'.$id.'"><label for="chk_'.$id.'"></label><br>';
                if ($attr['type'] == 'checkbox') {
                    if (isset($attr['values']) && $attr['values'] != '') {
                        $element_chk = '';
                        $count_chk = 0;

                        $testings = array();
                        foreach ($dropdown_val as $chkkey => $va) {
                            $chkboxVal = str_replace(',', '&#44;', $va);
                            $opt_chks = explode(",", $opt_chk);
                            $chkChecked = "";

                            if (in_array($chkkey, $opt_chks)) {
                                $chkChecked = "checked='checked'";
                            }

                            $checked = '';
                            if (in_array($count_chk, $chsel_arr))
                                $checked = 'checked="checked"';
                            if ($element_chk == '')
                                $element_chk = '<input type="checkbox" name="' . $dataId . '[' . $id . '][]" value="' . $count_chk . '" class="' . $required_class . '" ' . $chkChecked . ' id="chk_'.$count_chk.'_'.$id.'">' . $va . '<label for="chk_'.$count_chk.'_'.$id.'" ></label><br>';
                            else
                                $element_chk.='<input type="checkbox" name="' . $dataId . '[' . $id . '][]" value="' . $count_chk . '" class="' . $required_class . '" ' . $chkChecked . ' id="chk_'.$count_chk.'_'.$id.'">' . $va . '<label for="chk_'.$count_chk.'_'.$id.'" ></label><br>';

                            $count_chk++;
                        }
                        if ($element_chk != '')
                            $element_ck = $element_chk;
                    }
                }
                $element_ra = '<input type="radio" name="' . '[' . $id . ']" class="' . $required_class . '"  id="rdo_'.$id.'"><label for="rdo_'.$id.'"></label><br>';
                if ($attr['type'] == 'radio') {
                    if (isset($attr['values']) && $attr['values'] != '') {

                        $element_rad = '';
                        $count_rd = 0;
                        foreach ($dropdown_val as $rdKey => $va) {
                            $radioVal = str_replace(',', '&#44;', $va);
                            $radioChecked = "";

                            if ($rdKey == $opt_chk) {
                                $radioChecked = "checked='checked'";
                            }

                            $checked = '';
                            if ($ivalue != '') {
                                if ($count_rd == $ivalue)
                                    $checked = 'checked="checked"';
                            }
                            if ($element_rad == '')
                                $element_rad = '<input type="radio" name="' . $dataId . '[' . $id . ']" value="' . $count_rd . '"  class="' . $required_class . '" ' . $radioChecked . ' id="rdo_'.$count_rd.'_'.$id.'">' . $va . '<label for="rdo_'.$count_rd.'_'.$id.'"></label><br>';
                            else
                                $element_rad.='<input type="radio" name="' . $dataId . '[' . $id . ']" value="' . $count_rd . '"  class="' . $required_class . '" ' . $radioChecked . ' id="rdo_'.$count_rd.'_'.$id.'">' . $va . '<label for="rdo_'.$count_rd.'_'.$id.'"></label><br>';
                            $count_rd++;
                        }
                        if ($element_rad != '')
                            $element_ra = $element_rad;
                    }
                }
                $ivalue = str_replace("\'", "'", $opt_chk);
                $dropClass = 'taskdropdown';

                switch ($attr['type']) {
                    case 'text':
                        $element = form_textarea(array(
                            'class' => 'wysiwyg instruction_multi10000 ' . $required_class,
                            'id' => $dataId . '[' . $id . ']',
                            'name' => $dataId . '[' . $id . ']',
                            'rows' => 5,
                            'cols' => 50,
                            'maxlength'=>'10000',
                            'style' => 'display:none;'
                        ));
                        $lable_class = 'text_lable_class';
                        break;
                    case 'textarea':
                        $element = form_textarea(array(
                            'name' => $dataId . '[' . $id . ']',
                            'rows' => 5,
                            'cols' => 50,
                            'id'=>'lbl-'.$id,
                            'value' => $ivalue,
                            'maxlength'=>'10000',
                            'class' => 'instruction_multi10000 '.$required_class,
                        ));
                        break;
                    case 'textbox':
						$element = form_input(array('name' => $dataId . '[' . $id . ']','maxlength'=>'255', 'class' => 'jf_text form_role_input instruction_single255 ' . $required_class), $ivalue);
                        break;
                    case 'number':
						$element = form_input(array('name' => $dataId . '[' . $id . ']','id'=>'lbl-'.$id,'maxlength'=>'255', 'class' => 'jf_text form_role_input instruction_single255 ' . $required_class . ' ' . $field_types), $ivalue);
                        break;
                    case 'dropdown': $element = "<span class='taskspandrop' style='width: 409px !important;'>" .
                    	form_dropdown($dataId . '[' . $id . ']', $dropdown_val, array("0"=>$selectedkey), 'class="' . $required_class . " " . $dropClass . '"') .
                    	"</span>";
                    	//echo "selected Data : ".$dataId. '[' . $id . ']'." => ".$dropdown_val." => ".$selectedkey." => ".'class="' . $required_class . " " . $dropClass . '"<br/>';
                    	//echo "select : ".form_dropdown($dataId . '[' . $id . ']', $dropdown_val, $selectedkey, 'class="' . $required_class . " " . $dropClass . '"');
                        break;
                    case 'checkbox': $element = '<span class="values ' . $id . '">' . $element_ck . '</span>';
                        break;
                    case 'radio': $element = '<span class="values ' . $id . '">' . $element_ra . '</span>';
                        break;
                    case 'datetime': $element = form_input(array('name' => $dataId . '[' . $id . ']', 'placeholder' => 'Choose a date', 'class' => 'datepicker jf_text form_role_input ' . $required_class,'readonly'=>'readonly'), $ivalue);
                        break;
                    case 'fileupload': $element = form_upload($id);
                        break;
                    case 'button': $element = form_input(array('name' => $id, 'value' => $btn_value, 'type' => 'button'));
                        break;
                    default: $element = null;
                        break;

                    //echo $element;
                }
                //give the text box a differnt label
                if (isset($attr['label']) && $attr['label'] != "") {
                    $label = $attr['label'];
                    $pro = true;
                } else
                    $label = ($attr['type'] == 'text') ? ' ' : 'No Label';

                if ($pro) {
                    //$this->properties($attr);
                }

                //basic output list element.
                if (isset($attr['edit']) && $attr['edit'] != '') {
                    if (isset($contents) && $contents != '') {
                        if (isset($attr['text_val']) && $attr['text_val'] != "") {
                            $contents = html_entity_decode(rawurldecode($contents));
                            $label = str_replace(",", "", $contents);
                            $label = str_replace("+", " ", $contents);
                            $label = str_replace("\n", "<br>", $contents);
                        }
                    }

                    $colon = ($attr['type'] == 'text') ? '' : ':';
                    $output = "
					<li>
					<label for='" . $id . "' class='" . $lable_class . "'>" . $label . $required_span . $colon . "</label>
							<div class='block'>" . $element . "
							<span class='note " . $id . "'>$description</span></div>
						<div class='clear'></div>
						<div class='attrs clear " . $id . "'>
							<input type='hidden' name='properties[" . $id . "][name]' value='" . $id . "'/>
							<input type='hidden' name='properties[" . $id . "][value]' class='label' value='" . $label . "' />" . $valuessssss . "
							<input type='hidden' name='properties[" . $id . "][required]' class='required' value='" . $required . "' / >
							<input type='hidden' name='properties[" . $id . "][required_vars]' class='required_vars' value='" . $required_vars . "' />
							<input type='hidden' name='properties[" . $id . "][description]' class='description' value='" . $description . "'>
                                                        <input type='hidden' name='properties[" . $id . "][sync_prod]' id='sync_prod_" . $id . "' class='sync_prod' value='" . $sync_prods . "'>
                                                        <input type='hidden' name='properties[" . $id . "][optionchk]' id='optionchk_" . $id . "' class='optionchk' value='" . $opt_chk . "'>
						</div>
					</li>
				";
                }

                if ($element) {

                    //set output to AJAX
                    $elements_output[$id] = rawurldecode(html_entity_decode($output));
                }
            }
            //sleep(1);
        }
        echo json_encode($elements_output);
        die;
    }

    function get_count() {
        static $count = 0; // "inner" count = 0 only the first run
        return $count++; // "inner" count + 1
    }

}
?>
