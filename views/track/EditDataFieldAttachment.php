<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\models\Options;
use app\models\TasksUnitsData;
use app\components\IsataskFormFlag;
$this->title = 'Edit Data Field';
$this->params['breadcrumbs'][] = ['label' => 'Data Field', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$required_class="";

/* Register Js File */
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/jquery.form.min.js');
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/jquery.MultiFile.js');

$js = <<<JS
$('document').ready(function(){
	$('#T7').MultiFile({
	  list: '#T7-list',
	  STRING: {
		  remove:'<em class="fa fa-close text-danger" title="Remove"></em>',
	  },
	  maxsize:102400
	 });
});
		
function remove_image(id,obj){
	removed = $("#remove_attachments").val();
	if(removed == ""){
	  removed = id;
	}else{
	  removed = removed + ','+ id;
	}	
	$("#remove_attachments").val(removed);
	// $(obj).parent().remove();
	$(obj).closest('tr').remove();
}
JS;
$this->registerJs($js);
?>
<div id="form_div">
	<form name="tasksunitsdataattachment" method="post" id="TasksUnitsDataAttachment" enctype="multipart/form-data" autocomplete="off"> <!-- action="<?php // Url::to(['track/editdatafieldattachment']) ?>"  -->
		<input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken() ?>" />
		<input type="hidden" name="remove_attachments" id="remove_attachments" value="">
			<?= IsataskFormFlag::widget(); // change flag ?>
		    <?php
           		if (!empty($attachment)) {?>
           		<table summary="Added Pricing Rates" class="table table-striped"> <!-- class="display dataTable no-footer table table-striped" id="pricing_rate_table" -->
	           		<thead><tr>
		           		<th><a href="javascript:void(0);" title="Document Name">Document Name</a></th>
		           		<th><a href="javascript:void(0);" title="Uploaded By">Uploaded By</a></th>
						<th><a href="javascript:void(0);" title="Updated Date">Updated Date</a></th>
		           		<th><a href="javascript:void(0);" title="Action">Action</a></th>
	           		</tr></thead>
	           		<tbody class="tbodyClass">
		               	<?php foreach ($attachment as $filename) { ?>
			               	<tr>
			               		<td><?php echo $filename['fname']; ?></td>
			               		<td><?php echo $filename['user']['usr_first_name'].' '.$filename['user']['usr_lastname']; ?></td>
			               		<td><?php echo $bill_date = (new Options)->ConvertOneTzToAnotherTz($filename['modified'], 'UTC', $_SESSION['usrTZ'], "MDYHIS"); ?></td>
			               		<td><a href="javascript:void(0);" title="Delete" class="text-primary MultiFile-remove" onclick="remove_image('<?php echo $filename['id']; ?>', this);"><em title="Remove" class="fa fa-close"></em></a></td>
			               	</tr>
		               <?php } ?>
		              </tbody>
          		</table>
           	<?php } ?>
            <br>   	  
	        <div class="rows">
				<div class="form-group field-evidence-cont">
					<div class="row input-field">
						<div class="col-sm-3"><label>Attachment: </label></div>	
						<div class="col-sm-7"><span>Tip: File size cannot exceed 100 MB.</span>
							<input type="file" name="TasksUnitsBilling[attachment][]" class="T7" id="T7" title="Choose File" multiple="multiple" />
							<div id='T7-list' class="T7-list"></div>
						</div>
					</div>
				</div> 
		    </div> 
		</div>
	</form>
</div>
<noscript></noscript>
