<?php 
if (! function_exists('array_column')) {
    function array_column(array $input, $columnKey, $indexKey = null) {
        $array = array();
        foreach ($input as $value) {
            if ( !array_key_exists($columnKey, $value)) {
                trigger_error("Key \"$columnKey\" does not exist in array");
                return false;
            }
            if (is_null($indexKey)) {
                $array[] = $value[$columnKey];
            }
            else {
                if ( !array_key_exists($indexKey, $value)) {
                    trigger_error("Key \"$indexKey\" does not exist in array");
                    return false;
                }
                if ( ! is_scalar($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not contain scalar value");
                    return false;
                }
                $array[$value[$indexKey]] = $value[$columnKey];
            }
        }
        return $array;
    }
}
?>
<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use app\components\IsataskFormFlag;
use app\models\SystemMaintenanceLogs;
\app\assets\CustomInputAsset::register($this);
$js = <<<JS
// get the form id and set the event
$('form#{$model->formName()}').on('beforeSubmit', function(e) {
		var form = $(this);
		$.ajax({
            url    : form.attr('action'),
            type   : 'post',
            data   : form.serialize(),
            beforeSend : function()    {
            	$('#submitUpdate').attr('disabled','disabled');
            },
            success: function (response){
            	if($.trim(response) == 'OK') {
					commonAjax(baseUrl +'/system/system-maintenance','admin_main_container');
				}else if($.trim(response) == 'OKS') {
					$('#message_db_op').html('<br><span  class="alert alert-success">Successful</span>');
             		setTimeout(function(){
						 commonAjax(baseUrl +'/system/system-maintenance','admin_main_container');
					},1500);
             	}else if($.trim(response) == 'OKF'){
					$('#message_db_op').html('<br><span  class="alert alert-danger">Failed</span>');
					setTimeout(function(){
             			commonAjax(baseUrl +'/system/system-maintenance','admin_main_container');
					 },1500);
             	}else{
                	$('#submitUpdate').removeAttr("disabled");
            	}
            },
            error  : function (){
                console.log('internal server error');
            }
        });
   	
	return false;
	// do whatever here, see the parameter \$form? is a jQuery Element to your form
}).on('submit', function(e){
    e.preventDefault();
});
JS;
$this->registerJs($js);
?>
<div class="right-main-container">			
	<div class="sub-heading"><a href="javascript:void(0);" title="System Maintenance" class="tag-header-black">System Maintenance</a></div>
	<?php $form = ActiveForm::begin(['id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
	<?= IsataskFormFlag::widget(); // change flag ?>
	
	<fieldset class="one-cols-fieldset">
	 <div class="create-form system-maintain">

	 	<div class="row">
			<div class="col-sm-1 sysmaininputchk"><input id="clear-assets-directory" type="checkbox" name="clear-assets-directory">
			<label class="form_label required" for="clear-assets-directory"></label>
			</div>
			<div class="col-sm-11">
				<b>Clear Assets Directory </b><br>
				Recommended after upgrading application code files or when migrating application code files to new server.
				<?php 
				if(array_search('clear-assets-directory', array_column($system_log, 'action')) !== false) {
				$key = array_search('clear-assets-directory', array_column($system_log, 'action'));?>
				<br><em class="text-gray">Last Updated: <?php echo SystemMaintenanceLogs::UtcToEst($system_log[$key]['created'])?> by <?php echo $system_log[$key]['usr_first_name']." ".$system_log[$key]['usr_lastname'];?></em>
				<?php } ?>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-1 sysmaininputchk"><input id="clear-db-cache" type="checkbox" name="clear-db-cache">
			<label class="form_label required" for="clear-db-cache"></label>
			</div>
			<div class="col-sm-11">
				<b>Clear Schema, APC, Wincache Cache</b><br>
				Recommended after upgrading application database schema.
				<?php 
				if(array_search('clear-db-cache', array_column($system_log, 'action')) !== false) {
				$key = array_search('clear-db-cache', array_column($system_log, 'action'));?>
				<br><em class="text-gray">Last Updated: <?php echo SystemMaintenanceLogs::UtcToEst($system_log[$key]['created'])?> by <?php echo $system_log[$key]['usr_first_name']." ".$system_log[$key]['usr_lastname'];?></em>
				<?php } ?>
				<div id="message_db_op"></div>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-1 sysmaininputchk"><input id="clear-garbage-sessions" type="checkbox" name="clear-garbage-sessions">
			<label class="form_label required" for="clear-garbage-sessions"></label>
			</div>
			<div class="col-sm-11">
				<b>Clear "Garbage" User Sessions <span style="color:blue"><em>(Stored on Disk (<?=$session_sizeformated?>))</em></span></b><br>
				Recommended per administrative preference on storage size.  All session created earlier than today will be deleted from the system.
				<?php 
				if(array_search('clear-garbage-sessions', array_column($system_log, 'action')) !== false) {
				$key = array_search('clear-garbage-sessions', array_column($system_log, 'action'));?>
				<br><em class="text-gray">Last Updated: <?php echo SystemMaintenanceLogs::UtcToEst($system_log[$key]['created'])?> by <?php echo $system_log[$key]['usr_first_name']." ".$system_log[$key]['usr_lastname'];?></em>
				<?php } ?>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-1 sysmaininputchk"><input id="clear-all-sessions" type="checkbox" name="clear-all-sessions">
			<label class="form_label required" for="clear-all-sessions"></label>
			</div>
			<div class="col-sm-11">
				<b>Clear "All" User Sessions </b> <span tabindex="0" class="fa fa-info-circle text-primary" aria-hidden="true" title="Only current login user's session will remain. All other users will be forced to login again."></span><br>
				Recommended after upgrading application code files or when migrating application code files to new server.
				<?php 
				if(array_search('clear-all-sessions', array_column($system_log, 'action')) !== false) {
				$key = array_search('clear-all-sessions', array_column($system_log, 'action'));?>
				<br><em class="text-gray">Last Updated: <?php echo SystemMaintenanceLogs::UtcToEst($system_log[$key]['created'])?> by <?php echo $system_log[$key]['usr_first_name']." ".$system_log[$key]['usr_lastname'];?></em>
				<?php } ?>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-1 sysmaininputchk"><input id="clear-app-logs" type="checkbox" name="clear-app-logs">
			<label class="form_label required" for="clear-app-logs"></label>
			</div>
			<div class="col-sm-11">
				<b>Clear Application Logs <span style="color:blue"><em>(Stored on Disk (<?=$log_sizeformated?>))</em></span></b> <?php if (extension_loaded('zip')) {?><a class="btn btn-primary" style="margin: -10px 0px 0px 5px !important;" href="javascript:backUpLog();">Backup</a><?php } else {?>php zip extension needed for backup functionality.<?php }?><br>
				Recommended per administrative preference on storage size or when migrating application code files to new server.
				<?php 
				if(array_search('clear-app-logs', array_column($system_log, 'action')) !== false) {
				$key = array_search('clear-app-logs', array_column($system_log, 'action'));?>
				<br><em class="text-gray">Last Updated: <?php echo SystemMaintenanceLogs::UtcToEst($system_log[$key]['created'])?> by <?php echo $system_log[$key]['usr_first_name']." ".$system_log[$key]['usr_lastname'];?></em>
				<?php } ?>
				<?php 
				if(array_search('backup-logs', array_column($system_log, 'action')) !== false) {
				$key = array_search('backup-logs', array_column($system_log, 'action'));?>
				<br><em class="text-gray">Last Backup: <?php echo SystemMaintenanceLogs::UtcToEst($system_log[$key]['created'])?> by <?php echo $system_log[$key]['usr_first_name']." ".$system_log[$key]['usr_lastname'];?></em>
				<?php } ?>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-2 sysmainselect">
			 <?php
			 	$enbdeb=0;
				if(isset($model->fieldvalue)){
					$enbdeb=$model->fieldvalue;
				}
				echo Select2::widget([
						'name' => 'enable-debug',
	                    'attribute' => 'enable-debug',
	                    'value'=>$enbdeb,
	                    'data' => array(1=>'Yes',0=>'No'),
	                    'options' => ['prompt' => 'Select Session Timeout','class' => 'form-control','id'=>'times','nolabel'=>'true','aria-label'=>'Select Session Timeout'],
	                    'pluginOptions' => [
	                      'allowClear' => false
	                    ],
						'pluginEvents' => [
								'change' => "function() {
									$('#enable-text').removeClass('text-danger');
									if(this.value == 1){
										$('#enable-text').addClass('text-danger');
									}
									
								}",
								]
	                    ]);
	            	echo '<div class="help-block"></div>';
			?>
		</div>
			<div class="col-sm-10" style="padding-left:5px;">
				<b id="enable-text" class="<?php if($enbdeb==1){?>text-danger<?php }?>">Enable Yii Logging - (Default is set to No) </b><span tabindex="0" class="fa fa-info-circle text-primary" aria-hidden="true" title='Set default to “Yes” in Development environment  &#013;Set default to “No” in Stage and Production environments'></span></a><br>
				<span style="">
				Recommended to turn on when troubleshooting feature bugs.
				</span>
				<?php 
				if(array_search('enable-debug', array_column($system_log, 'action')) !== false) {
				$key = array_search('enable-debug', array_column($system_log, 'action'));?>
				<span style="">
				<br><em class="text-gray">Last Updated: <?php echo SystemMaintenanceLogs::UtcToEst($system_log[$key]['created'])?> by <?php echo $system_log[$key]['usr_first_name']." ".$system_log[$key]['usr_lastname'];?></em>
				</span>
				<?php } ?>
			<div>	
		</div>


	 </div>
	</fieldset>
	<div class="button-set text-right">
		<?= Html::submitButton('Update', ['title'=>"Update",'class' => 'btn btn-primary','id'=>'submitUpdate']) ?>
	</div>
	<?php ActiveForm::end(); ?>
</div>
<script>
/* customInput */	
$(function() {
  $('input').customInput();
});
function backUpLog(){
	location.href=baseUrl +'/system/backup-logs';
}
</script>
<noscript></noscript>