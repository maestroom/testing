<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\widgets\Select2;
use app\components\IsataskFormFlag;
use yii\helpers\ArrayHelper;
use app\models\FormBuilderSystem;
//use kartik\widgets\FileInput;
//use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Evidence */
/* @var $form yii\widgets\ActiveForm */
//print_r($UserName);die;
$evid_type_form = ArrayHelper::map(FormBuilderSystem::find()->select(['sys_field_name','sort_order'])->where(['sys_form'=>'media_check_in_out_form','grid_only'=>0])->orderBy('sort_order')->all(),'sys_field_name','sort_order');
?>

<?php $form = ActiveForm::begin(['id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true,'options' => ['enctype'=>'multipart/form-data']]); ?>
<?= IsataskFormFlag::widget(); // change flag ?>
<fieldset class="one-cols-fieldset">
    <div class="email-confrigration-table sla-bus-hours" id="list">
	    <?= $form->field($model, 'trans_type',['template' => "<div class='row input-field'><div class='col-md-3'>Transaction Type<span class='require-asterisk' style='display:inline-block'>*</span></div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
	    'data' => $transType,
	    'options' => ['prompt' => 'Select Transaction Type', 'id' => 'evidencetransaction-trans_type', 'title' => 'Select Transaction Type'],
	   /* 'pluginOptions' => [
	        'allowClear' => true
	    ],*/ ]);?>
    <?php if(isset($tran_show_field['trans_requested_by'])) { ?>
		<div class="listing-item" data-order=<?=$evid_type_form['trans_requested_by']?>>    
		<?= $form->field($model, 'trans_requested_by',['template' => "<div class='row input-field' id='move_to'><label class='col-md-3 form_label'>Transaction Requested By</label><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
		    	'data' => $UserName,
		    	'options' => ['prompt' => 'Select User', 'id' => 'evidencetransaction-trans_requested_by','aria-required'=> $reqFormStatus['trans_requested_by']],
	    	]); 
		?>
     </div>
     <?php } ?>
     <?php if(isset($tran_show_field['Trans_to'])) {?> 
		 <div class="listing-item" data-order=<?=$evid_type_form['Trans_to']?>>    
			<?= $form->field($model, 'Trans_to',['template' => "<div class='row input-field' id='Trans_to'><div class=''>&nbsp;</div><label class='col-md-3 form_label'>Transaction To</label><div class='col-md-7'>{input}\n<label for='evidencetransaction-Trans_to'>&nbsp;</label>{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
			'data' => $listEvidenceTo,
			'options' => ['prompt' => 'Select Transaction To', 'id' => 'evidencetransaction-Trans_to','aria-required'=> $reqFormStatus['Trans_to']],
			]); ?>
		</div>
    <?php }?>
    <?php if(isset($tran_show_field['trans_reason'])) {?>  
		<div class="listing-item" data-order=<?=$evid_type_form['trans_reason']?>>   
			<?= $form->field($model, 'trans_reason',['template' => "<div class='row input-field' ><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textarea(['maxlength'=>$model_field_length['trans_reason'],'aria-required'=> $reqFormStatus['trans_reason']]); ?> 
		</div>       
    <?php }?>
    <?php if(isset($tran_show_field['moved_to'])) { ?>
		<div class="listing-item" data-order=<?=$evid_type_form['moved_to']?>>    
     <?= $form->field($model, 'moved_to',['template' => "<div class='row input-field' id='move_to'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
	    'data' => $listEvidenceLoc,
	    'options' => ['prompt' => 'Select Move To Stored Location', 'id' => 'evidencetransaction-moved_to','aria-required'=> $reqFormStatus['moved_to']],
     ]); ?>
     </div>
     <?php } ?>
    <?php if(isset($tran_show_field['is_duplicate'])) { ?>
		<div class="listing-item" data-order=<?=$evid_type_form['is_duplicate']?>>    
			<?= 
				$form->field($model, 'is_duplicate',['template' => "<div class='row input-field' id='spn_Apply_Transaction_to_both'><div class='col-md-3' id='evidencetransactionIsDuplicate'>Apply Transaction to both Original and associated Duplicate Evidence</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->checkbox(array('label'=>null,'aria-labelledby'=>'evidencetransactionIsDuplicate')); 
			?>
		</div>
    <?php } ?>        
        <script type="text/javascript">
            $(document).ready(function(){
                <?php if(!empty($reqFormStatus)){
                    foreach($reqFormStatus as $key => $single){
                        if($single == 'true') {?>
                            $('.field-evidencetransaction-<?= strtolower($key)?>').addClass('required');
                    <?php } }
                }
                ?>
               //alert('field-evidencetransaction-moved_to'); 
            });
        </script>
</fieldset>
<div class="button-set text-right">
		<input type="hidden" value="<?php echo $evidNum;?>" name="evid_nums" />
        <input type="hidden" value="<?php echo Yii::$app->user->identity->id;?>" name="uid" /> 
        <?= Html::button('Cancel', ['class' =>  'btn btn-primary','onclick'=>'location.href="index.php?r=media/index";','title'=>'Cancel']) ?>
        <?= Html::Button('Apply', ['class' =>'btn btn-primary','title'=>'Apply','onclick'=>'validateForms();']) ?>
</div>
<?php ActiveForm::end(); ?>
<script type="text/javascript">
	/*rearrange divs based on system from Start*/
var $people = $('#list'),
	$peopleli = $people.children('.listing-item');

$peopleli.sort(function(a,b){
	var an = parseInt(a.getAttribute('data-order')),
		bn = parseInt(b.getAttribute('data-order'));
	//console.log('nelson');
	if(an > bn) {
		return 1;
	}
	if(an < bn) {
		return -1;
	}
	return 0;
});

$peopleli.detach().appendTo($people);

	$('#evidencetransaction-trans_type').change(function(){
		var str_val=$(this).val();
		transTypeHandler(str_val);
			
	});
	function transTypeHandler(trans_type){		
		
		$.ajax({
			url    : baseUrl+'media/get-evidence-type-fields',
			type   : 'POST',
			data   : {trans_type:trans_type,id:"<?=$evidNum?>"},
			beforeSend : function()    {
				//$('.btn').attr('disabled','disabled');
			},
			success: function (response){
				//console.log(response);
				//alert(trans_type);
				$("#form_div").html(response);
				/*if(response == 'OK'){
					commonAjax(baseUrl +'/system/form&sysform='+form_id,'admin_main_container');
				}else{
					$('.btn').removeAttr("disabled");
				}*/
			},
			error  : function (){
				console.log('internal server error');
			}
		});
	}
	
</script>
<noscript></noscript>	
 
