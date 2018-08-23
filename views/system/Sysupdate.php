<?php
use yii\helpers\Html;
use kartik\grid\GridView;
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/bootstrap-filestyle.js');
$js = <<<JS
// get the form id and set the event
$(function() {
 	$('#release_file').filestyle({
 		icon : false,
	});
});
		
$('.apply-tooltip').each(function(){
    $(this).siblings("div").attr("data-toggle", "tooltip");
    $(this).siblings("div").attr("title", "Choose File");
}); 

JS;
$this->registerJs($js);
?>
<?php $data = $dataProvider->getModels();  ?>
<div class="right-main-container">	
			<div class="sub-heading"><a href="javascript:void(0);" title="System Updates" class="tag-header-black">System Updates</a></div>		
			<fieldset class="one-cols-fieldset">
			 <div class="system-update-version-table">
				<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-striped">
                    <tbody id="system_update_table">
                        <tr>
                            <th scope="col" id="system_update_version" class="text-center"><a href="javascript:void(0);" title="Version" class="tag-header-black">Version</a></th>
                            <th scope="col" id="system_update_date" class="text-center"><a href="javascript:void(0);" title="Date" class="tag-header-black">Date</a></th>
                            <th scope="col" id="system_update_installed" class="text-center"><a href="javascript:void(0);" title="Installed" class="tag-header-black">Installed</a></th>
                        </tr>  
                        <?php foreach($data as $d => $value){ ?>
							<tr>
								<td headers="system_update_version" class="text-center"><?php echo $value->version; ?></td>
								<td headers="system_update_date" class="text-center"><?php echo date('m/d/Y',strtotime($value->date)); ?></td>
								<td headers="system_update_installed" class="text-center"><?php if($value->is_updated){ ?><a href="javascript:void(0);" class="tag-header-black" title="Installed" aria-label="Installed"><span title="Installed" class="fa fa-check text-danger"></span></a> <?php }?></td>
							</tr>
						<?php } ?>	
                    </tbody> 
                </table>
			
			</div>	
			
			 <!--<div class="col-md-12 system-updates-table">
			 <div class="table-responsive">
			 <?= 
			 	GridView::widget([
			 		'id'=>'sysupdate-grid',
					'dataProvider'=> $dataProvider,
					'layout' => '{items}<div class="pagination-main">{summary}{pager}</div>',
					'columns' =>[
							['attribute'=>'version', 'headerOptions' => ['title' => 'Version'], 'header' => 'Version', 'hAlign'=>GridView::ALIGN_CENTER],
					  		['attribute'=>'date','headerOptions' => ['title' =>'Date'], 'hAlign'=>GridView::ALIGN_CENTER],
					  		['attribute'=>'is_updated', 'headerOptions' => ['title' =>'Installed'], 'hAlign'=>GridView::ALIGN_CENTER,'format'=>'raw','value'=>function($model){ if($model->is_updated) {return '<span class="fa fa-check text-danger" title="Installed"></span>';}}],
					],
					'pjax'=>true,
					'pjaxSettings'=>[
						'options'=>['id'=>'sysupdate-pajax','enablePushState' => false],
						'neverTimeout'=>true,
						'beforeGrid'=>'',
			        	'afterGrid'=>'',
			    	],
			    	'export'=>false,
					'responsive'=>true,
					'hover'=>true,
					'pager' => [
						'options'=>['class'=>'pagination'],   // set clas name used in ui list of pagination
						'prevPageLabel' => 'Previous',   // Set the label for the "previous" page button
						'nextPageLabel' => 'Next',   // Set the label for the "next" page button
						'firstPageLabel'=>'First',   // Set the label for the "first" page button
						'lastPageLabel'=>'Last',    // Set the label for the "last" page button
						'nextPageCssClass'=>'next',    // Set CSS class for the "next" page button
						'prevPageCssClass'=>'prev',    // Set CSS class for the "previous" page button
						'firstPageCssClass'=>'first',    // Set CSS class for the "first" page button
						'lastPageCssClass'=>'last',    // Set CSS class for the "last" page button
						'maxButtonCount'=>5,    // Set maximum number of page buttons that can be displayed
					]
			]);?>
			  </div>  
			 </div>-->
			
			
			
			</fieldset>
			
			<div class="button-set text-right">
			<button class="btn btn-primary pull-left" title="Default">Default</button>
			 <div class="col-sm-4 file-handle">
				<input type="file" class="filestyle form-control apply-tooltip" data-input="false" data-buttontext="" data-iconname="glyphicon-paperclip" name="release_file" data-toggle="tooltip" title="Choose File" id="release_file" onchange="validatefileext();">
			 </div>
			 <button class="btn btn-primary" title="Update">Update</button>
			</div>
			
			<div class="tooltip top" role="tooltip">
			  <div class="tooltip-arrow"></div>
				  <div class="tooltip-inner">
				    Choose File
				  </div>
			</div>

		   </div>
		  
