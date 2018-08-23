<?php
/**
 * View: Index
 * @abstract This view is define for Evidence Index Page
 * @package views
 * @subpackage evidence
 * @author Jayant (mali1545@gmail.com)
 * @copyright � 2011-2012 Inovitech, LLC, All Rights Reserved.
 * @todo Ensure all comment block and methods are complete
 * @version 1.0.0 Initial release
 *
 */
// --------------------------------------------------------------------------------------
// PRODUCTION CODE STARTS HERE
// --------------------------------------------------------------------------------------
/*$bUrl = Yii::app()->baseUrl; //base URL
$tUrl = Yii::app()->theme->baseUrl;
$actionId = Yii::app()->controller->action->id; // get action Id
$controllerId = Yii::app()->controller->id; //get controller Id
$roleId = Yii::app()->user->role_id;*/
//echo "<pre>"; print_r($data);
?>
<div id="addevidcust" title="Add New Custodian" style="display:none;overflow-x: hidden;overflow-y: auto;">
	<?= $this->render('_formaddevidcustodian', [
            'model_cust' => $model_cust,
            //'listcustdata' => $listcustdata,
            //'listDataType' => $listDataType,
            //'listUnit' => $listUnit,
        ])
    ?>
</div>
<div id="add-todo-item" title="Add Media Contents">
  <div class="create-form">
       <?= $this->renderAjax('_formaddevidcontent', [
			'model' => $model,
			'listcustdata' => $listcustdata,
			'listDataType' => $listDataType,
			'listUnit'=>$listUnit,
			'temp_evid_id'=>$temp_evid_id,
			'data'=> $data
        ]); ?>
	</div>		
</div>
<?php
die;
/* BY HNL 05-07 $form = $this->beginWidget('CActiveForm', array(
    'id' => 'add-evidenceCustodian-form',
    'action' => Yii::app()->createAbsoluteUrl('/media/append-evidence-content'), //<- your form action here
    'enableAjaxValidation' => false, 'stateful' => true, 'htmlOptions' => array('enctype' => 'multipart/form-data'),
        ));*/
?>			
<!-- Evidence Add Form Second Step -->
<!--<div class="SectionMiddle sectionmid_cl">
    <div class="FormLeftSpace add_sub_cl">
        <div id="EvidenceList">
            <div class="text_field_bx">
                <label>Select Custodian:</label>
                <strong class="required requiredstar">*</strong>
                <span> 
                    <div class="left"> 
                        <p class="evdSelect evdSelect_contentsextra" id="custodian_details" >
                            <?php
                            $model->cust_id = $data['cust_id'];
                            echo $form->dropDownList($model, 'cust_id', $listcustdata, array('class' => 'SelectDropDown', 'prompt' => 'Select Custodian','aria-label'=>'Select Custodian'));
                            ?> 
                        </p>
                    </div>
                    <?php if (empty($data['temp_evid_id'])) {
						echo "fgdfhdfh";die;
						if ((new User)->checkAccess(4.002)) { ?>
                        <div class="left height_35">
                            <?php //echo CHtml::button('Add New', array('id' => 'addEvidenceCustodian', 'class' => 'button_small dialog', 'onclick' => 'openaddcust();','aria-label'=>'Add New Custodian')); ?>
                        </div>
                    <?php } }?>
                </span>
            </div>
            <div class="text_field_bx no_margintop">
                <label>Data Type:</label>
                <span>
                    <p class="evdSelect">
                        <?php
                        $model->data_type = $data['data_type'];
                        echo $form->dropDownList($model, 'data_type', $listDataType, array('class' => 'SelectDropDown', 'prompt' => 'Select Data Type','aria-label'=>'Select Data Type'));
                        ?> 
                    </p>
                </span>
            </div>
            <div class="text_field_bx no_margintop">
                <label>Data Size:</label>
                <span>
                    <?php
                    $model->data_size = $data['data_size'];
                    echo $form->textField($model, 'data_size', array('class' => 'role_input','aria-label'=>'Data Size'));
                    ?>
                </span>
            </div>
            <div class="text_field_bx no_margintop">
                <label>Unit:</label>
                <span>
                    <p class="evdSelect">
                        <?php
                        $model->unit = $data['unit'];
                        echo $form->dropDownList($model, 'unit', $listUnit, array('class' => 'SelectDropDown', 'prompt' => 'Select Unit','aria-label'=>'Select Unit'));
                        ?> 
                    </p>
                </span>
            </div>
            <div class="text_field_bx no_margintop">
                <label>Data Copied To:</label>
                <span>
                    <?php
                    $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                        'model' => $model,
                        'value' => $data['data_copied_to'],
                        'name' => 'EvidenceContent[data_copied_to]',
                        'source' => $this->createUrl('/evidence/dataCopied'),
                        'htmlOptions' => array(
                            'class' => 'role_input',
                        	'aria-label'=>'Data Copied To'
                        ),
                    ));
                    ?>
                </span>
            </div>
            <input type="hidden" name="evid_id" value="<?php echo $evid_id; ?>" />
            <input  type="hidden" name="YII_CSRF_TOKEN" id="token" value="<?php echo CHtml::encode(Yii::app()->request->csrfToken) ?>"/>
            <input type="hidden" name="temp_evid_id" value="<?php echo $temp_evid_id; ?>" id="temp_evid_id"/>
            <input type="hidden" name="type" value="<?php echo $type; ?>" id="type"/>
            <input type="hidden" name="editEvidContentId" value="<?php echo $temp_evid_id; ?>" id="editEvidContentId"/>
        </div>
        <!-- End of Main Div For Ajax Response -->
    </div>
</div>
<div class="left padding_lefttop5 dialogrequired"><span class="required">*</span> Required</div> -->
<?php $this->endWidget(); //End of Form for Add Evidence ?>	
<script>
    $.noConflict();
    clearall();
   // alert("TEST");
    $('#add-evidenceCustodian-form').ajaxForm({
        success: SubmitSuccesfulcustodianForm,
    });
    var $custodianDialogContainer = $('#addevidcust');
    function openaddcust()
    {
        $custodianDialogContainer.dialog("open");
    }
    $(function () {
	    $('#add-case-custodian-form').ajaxForm({
            success: SubmitSuccesful,
        });
        $custodianDialogContainer.dialog({
            autoOpen: false,
            resizable: false,
            height: 400,
            width: 500,
            modal: true,
            buttons: {
                "Add":  {
					text: 'Add',
					'aria-label': "Add New Custodian Data",
					click: function () {
						validate($custodianDialogContainer);
					}
				},
                'Cancel': {
					text: 'Cancel',
					'aria-label': "Cancel New Custodian Data",
					click:  function () {
                    	$custodianDialogContainer.dialog("close");
	                    $.each($('.ui-dialog'), function (i, e) {
    	                    $custodianDialogContainer.dialog("close");
        	            });
                	}
                }
            }
        });
    });

    function SubmitSuccesful(responseText, statusText) {
        if (responseText != "no") {
            $('#custodian_details').html(responseText);
        }
        else {
            alert("Opps. Something Wrong...");
        }
    }
    function validate(obj)
    {
		var cust_ln = document.getElementById('EvidenceContent_cust_lname').value;
        var cust_fn = document.getElementById('EvidenceContent_cust_fname').value;
        if (cust_fn.replace(/\s/gi, "X") == "")
        {
            alert('Custodian First Name is Required Field.');
            $('#EvidenceContent_cust_fname').focus();
            return false;
        }
        else if (cust_ln.replace(/\s/gi, "X") == "")
        {
            alert('Custodian Last Name is Required Field.');
            $('#EvidenceContent_cust_lname').focus();
            return false;
        }
        else
        {
			<?php  if ($evid_id == 0) { ?>
			    var cust_fname = $('#EvidenceContent_cust_fname').val();
                var cust_lname = $('#EvidenceContent_cust_lname').val();
                var cust_mi = $('#EvidenceContent_cust_mi').val();
                var cust_title = $('#EvidenceContent_title').val();
                var cust_dept = $('#EvidenceContent_dept').val();
             	var dt = new Date();
			    var tmp_cust_id = dt.getHours() + "" + dt.getMinutes() + "" + dt.getSeconds();

                var custodian_values = "<div class='custodians_list' id='" + tmp_cust_id + "'>";
                custodian_values += "<input type='hidden' class='cust_fname' name='EvidenceCustodian[" + tmp_cust_id + "][cust_fname]' value='" + cust_fname + "'/>";
                custodian_values += "<input type='hidden' class='cust_lname' name='EvidenceCustodian[" + tmp_cust_id + "][cust_lname]' value='" + cust_lname + "'/>";
                custodian_values += "<input type='hidden' class='cust_mi' name='EvidenceCustodian[" + tmp_cust_id + "][cust_mi]' value='" + cust_mi + "'/>";
                custodian_values += "<input type='hidden' class='cust_title' name='EvidenceCustodian[" + tmp_cust_id + "][title]' value='" + cust_title + "'/>";
                custodian_values += "<input type='hidden' class='cust_dept' name='EvidenceCustodian[" + tmp_cust_id + "][dept]' value='" + cust_dept + "'/>";
                custodian_values += "<input type='hidden' class='cust_fullname' value='" + cust_lname + ", " + cust_fname + " " + cust_mi + "'/>";
                custodian_values += "</div>";

                if ($('body div.wrapper').find('#right_evidence #evidence_rightSection').find('form#addEvidenceform #evdFrm2 #evid_custodian_list').length) {
                    $('body div.wrapper').find('#right_evidence #evidence_rightSection').find('form#addEvidenceform #evdFrm2 #evid_custodian_list').append(custodian_values);
                }
                else { //case production code
                    $('body div.wrapper').find('#evid_custodian_list').append(custodian_values);
                }
				
			    $('#custodian_details select').append("<option value='" + tmp_cust_id + "'>" + cust_lname + ", " + cust_fname + " " + cust_mi + "</option>");
			    $('#custodian_details select').val(tmp_cust_id);
         
			<?php } else { ?>
                $("#add-case-custodian-form").submit();
			<?php } ?>
            clearall();
            $(obj).dialog("close");
        }
    }
    function clearall()
    {
        $('#EvidenceContent_cust_fname').val('');
        $('#EvidenceContent_cust_lname').val('');
        $('#EvidenceContent_cust_mi').val('');
        $('#EvidenceContent_title').val('');
        $('#EvidenceContent_dept').val('');
    }

</script>
<noscript></noscript>
