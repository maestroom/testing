<?php
use yii\helpers\Html;
?>
<div id='reportform_div'>    
    <div id="exp_builder">
		<div class="col-sm-12">
			<div class="col-sm-4">
				<label>Tables & Fields, Functions , Sp</label>
				<div>
					<ul>
						<li id="tables"><a href="javascript:void(0);" onclick="$('#table_list').toggle();">Tables</a>
							<?php if(!empty($tables)){?>
								<ul style="display:none;" id="table_list">
									<?php foreach($tables as $table){?>
									<li><a href="javascript:void(0);" onclick="$('#function_list').hide(); $('.fileds').hide(); $('#field_list_<?=$table->id?>').toggle();" data-name="<?=$table->table_name?>" data-id="<?=$table->id?>"><?=$table->table_display_name?></a></li>
									<?php }?>
								</ul>
							<?php }?>
						</li>
						<li><a  href="javascript:void(0);" onclick="$('.fileds').hide();$('#function_list').toggle();" >Function</a>
							
						</li>
						<li><a>Store Procedure</a></li>
					</ul>
				</div>
			</div>
			<div class="col-sm-4">
				<label>Fields, Functions , Sp</label>
				<div>
					<?php if(!empty($functions)){?>
							<ul style="display:none;" id="function_list">
								<?php foreach($functions as $function){?>
									<li><a data-name="<?=$function->function_name?>" data-id="<?=$function->id?>" data-desc="<?=html_entity_decode($function->function_desc)?>" onclick="$('#exp-fun-sp-desc').text($(this).data('desc')); getFnParams('<?=$function->id?>',this);"><?=$function->function_name?></a></li>
								<?php }?>	
							</ul>	
						<?php }?>
					<?php if(!empty($tables)){?>
								
									<?php foreach($tables as $table){
										if(!empty($table->reportsFields)){
										?>
										<ul style="display:none;" id="field_list_<?=$table->id?>" class="fileds">
											<?php foreach($table->reportsFields as $fields){ ?>
											<li><a data-name="<?=$fields->field_name?>" data-id="<?=$fields->id?>" onclick="addField('<?=$table->table_name.".".$fields->field_name?>')"><?=$table->table_name.".".$fields->field_name?></a></li>
											<?php }?>
										</ul>
									<?php }
									
									}?>
								
							<?php }?>
				</div>
			</div>
			<div class="col-sm-4">
				<label>Expression, Functions , Sp Description</label>
				<textarea id="exp-fun-sp-desc" class="form-control" name="exp_fun_sp_desc"  rows="6" aria-required="true" placeholder="Expression Function Store Procedure Info" readonly></textarea>
			</div>		
		</div>
		<div class="col-sm-12">
			<label>Operators</label>
			<div>
			<?php foreach(Yii::$app->params['exp'] as $exp){ if($exp==""){ continue; }?>
				<button value="<?=$exp?>" onclick="addOp(this);"><?=$exp?></button>
			<?php }?>
			</div>
		</div>
		<div class="col-sm-12">
			<label>Formula</label>
			<textarea id="formula" class="form-control" name="formula"  rows="6" aria-required="true" placeholder="Build Formula"></textarea>
		</div>
    </div>
</div>
<script>
function getFnParams(fn_id,obj){
	$.ajax({
			url:baseUrl+'report-management/get-fnparams',
			beforeSend:function (data) {showLoader();},
			type:'post',
			data:{fn_id:fn_id},
			success:function(response){
				hideLoader();						
				if($('body').find('#get-fnparams').length == 0){
					$('body').append('<div class="dialog" id="get-fnparams" title="Provide Function Params"></div>');
				}		
				$('#get-fnparams').html('').html(response);	
				$('#get-fnparams').dialog({ 
					modal: true,
			        width:'60em',
			        height: 500,
			        title: $(obj).data('name') + ' Parameters',
			        close: function(){
						$(this).dialog('destroy').remove();
					},
			        create: function(event, ui) { 						  
						 $('.ui-dialog-titlebar-close').html('<span class="ui-button-icon-primary ui-icon"></span>');
					},
			        buttons: [
								{ 
			                	  text: "Cancel", 
			                	  "class": 'btn btn-primary',
								  "title": 'Cancel',
			                	  click: function () { 
			                		  $(this).dialog('destroy').remove();
		 	                	  } 
			                  },
			                   { 
			                	  text: "Add", 
			                	  "class": 'btn btn-primary',
									"title": 'Add',
				                	  click: function () {
											var fnname=$('#fn_name').val();
											var params="";
											$('.fnparams_options').each(function(){
												if(this.value=='custom'){
													var id = $(this).attr('id');
													if(params == ""){ params= $('#'+id+'__val_other').val();} else{ params = params+','+ $('#'+id+'__val_other').val();}
												}else{
													if(params == ""){ params= this.value;} else{ params = params+','+this.value;}
												}
											});
											
											var formula = $('#formula').val() + fnname+'('+params+')';
											$('#formula').val(formula);
											
											$(this).dialog('destroy').remove();
									  }
			                  }
			        ]
			    });
			}
		});
}
function addOp(obj){
	var formula = $('#formula').val() + ' ' + $(obj).val();
	$('#formula').val(formula);
}
function addField(filed){
	var formula = $('#formula').val() +  filed;
	$('#formula').val(formula);
}
</script>
<noscript></noscript>
