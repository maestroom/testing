<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;

$this->title = 'Add Team';
?>
<div class="right-main-container slide-open" id="maincontainer">
	<fieldset class="two-cols-fieldset workflow-management">
		<div class="administration-main-cols">
			<div class="administration-lt-cols pull-left">
                            <button id="controlbtn" aria-label="Expand or Collapse" class="slide-control-btn" title="Expand/Collapse" onclick="WorkflowToggle();">
                                <span>&nbsp;</span>
                            </button>
                            <ul>
                                <li><a class="admin-main-title" href="javascript:operationalteamheader();" title="Teams"><em  title="Teams" class="fa fa-folder-open text-danger"></em>Teams</a>
                                        <div class="select-items-dropdown">
                                        <?php 
                                            if(!empty($teamList)) {  
                                                foreach ($teamList as $team) {
                                                    $team_dropdown[$team->id] = Html::encode($team->team_name);
                                                }
                                            }
                                            echo Select2::widget([
                                                'name' => 'select_box',
                                                'attribute' => 'select_box',
                                                'data' => $team_dropdown,
                                                'options' => ['prompt' => 'Select Team','class' => 'form-control','onchange'=>'javascript:updateTeam(this.value);','id'=>'nolabel-2'],
                                                /*'pluginOptions' => [
                                                    'allowClear' => true
                                                ]*/
                                            ]);
                                        ?>	
                                        </div>
                                        <div class="left-dropdown-list">
                                            <div class="admin-left-module-list">
                                                <ul class="sub-links" id="team_list">
                                                    <?php if(!empty($teamList)){ foreach ($teamList as $team){ ?>
                                                        <li class="teamlist" data-id="<?=$team->id?>">
                                                            <a href="javascript:updateTeamSelect(<?=$team->id?>);" title="<?=Html::encode($team->team_name); ?>"><em title="<?=Html::encode($team->team_name); ?>" class="fa fa-sitemap <?php if($team->team_type==1) {?>text-gray<?php } else {?>text-danger<?php }?>"></em> <?=Html::encode($team->team_name); ?></a>
                                                        </li>
                                                    <?php } 
                                                    } ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
				</ul>
			</div>
			<div class="administration-rt-cols pull-right" id="admin_right">
                <div class="sub-heading"><a href="javascript:void(0);" class="tag-header-black" title="<?= Html::encode($this->title) ?>"><?= Html::encode($this->title) ?></a></div>
                <div id="form_div">
                <?= $this->render('_teamform', [
                    'model' => $model,
                    'teamLocation'=>$teamLocation,
                    'model_field_length' => $model_field_length
                ]) ?>
                </div>
			</div>
		</div>
	</fieldset>
</div>
<script type="text/javascript">
 /* Teams Header */	
 function operationalteamheader() {
	var chk_status = checkformstatus("event"); // change flag to 1
	if(chk_status == true) // check true status
		commonAjax(baseUrl +'/workflow/operationalteam&team_id=0','admin_main_container');
 }	
 
 var fixHelper = function(e, ui) {
		ui.children().each(function() {
		$(this).width($(this).width());
		});
		return ui;
		};
  $("#team_list").sortable({
		helper: fixHelper,
		update: function(e,ui) { 
			var sorder="";
			var sort_arr = new Array();
			$("#team_list > li ").each(function(i){ //new code for sorting
					sort_arr[i]=$(this).data('id');
					if(sorder == "")
						sorder = $(this).data('id');
					else
						sorder = sorder + ','  + $(this).data('id');
			});
			jQuery.ajax({
			       url: baseUrl +'/workflow/sortteam',
			       data:{sort_ids: sorder},
			       type: 'post',
			       success: function (data) {
			    	  /* if(data != 'OK')
			    		  alert('Error');*/
			       }
			  });
		}
	}).disableSelection(); 
</script>
<noscript></noscript>
