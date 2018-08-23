<?php 
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\assets\ManageUserAccessAsset;
ManageUserAccessAsset::register($this);
if(!empty($rtList)){
$form = ActiveForm::begin(['id'=>$model->formName(), 'enableAjaxValidation' => false, 'enableClientValidation' => true]);
?>
<div id="project_request_type_container" class="mycontainer">	
	<div class="myheader">
        <a href="javascript:void(0);" class="">Select Which Request Types Should Display This Template</a>            
    </div>
	
    <div id="request-types-tree" class="tree-class"></div>
    <textarea name="role_ids" id="role_ids" style="visibility: hidden; height: 0px; margin: 0px; padding: 0px;"></textarea>
	<?php 		
	/*if(!empty($request_typeids)) { ?>
            <fieldset><legend class='sr-only'>Add Project Request Types</legend>
		<ul>
                    <li>	
                        <div class="pull-right header-checkbox">
                            <input type="checkbox" id="chkselectall" aria-label="Select All/None" class="pull-right select-chk" name="chkselectall" onclick="all_checkall_detail(this.checked);"/> 
                            <label for="chkselectall" class="select-chk"><span class='sr-only'>Select All/None</span></label> 
                        </div>			
                        <span class="pull-right"><b>Select All/None</b></span>				
                    </li>
                    <?php foreach($request_typeids as $key => $single){ ?>
                        <li>
                            <!--<span><?php //echo $single; ?></span>-->
                            <label for="role_detail_<?php echo $key; ?>" class="chkbox-global-design report_details_<?php echo $key; ?>"><?php echo $single; ?></label> 
                            <div class="pull-right "> 
                                <input type="checkbox" aria-label="<?php echo $single; ?>" id="role_detail_<?php echo $key; ?>"  value="<?php echo $key; ?>" class="chk primary_table_checkbox" name="role_ids[]" data-tbl_field_type="<?php echo $single; ?>" <?php if(isset($request_type_roles[$key]) && $request_type_roles[$key] == $project_request_type_id) echo 'checked="checked"';?> onChange="inner_check_change('<?= $key ?>');"/>                                     
                                <!--<label for="role_detail_<?php //echo $key; ?>" class="report_details_<?php echo $key; ?>">&nbsp;</label> -->
                            </div>
                        </li>
                    <?php } ?>
		</ul>
            </fieldset>
		<?php } */?>
</div>
<?php ActiveForm::end();?>
<style>
    #availabl-request-types .mycontainer .content{display:block;}
</style>
<script>
var treeData = <?= json_encode($rtList); ?>;
$(function(){
	$("#request-types-tree").dynatree({
		checkbox: true,
		selectMode: 3,
		children: treeData,
		onSelect: function(select, node) {
			var clientcaseAr = [];
			var selKeys = $.map(node.tree.getSelectedNodes(), function(node){
				if(node.childList===null)
					return node.data.key.toString();
			});
			mystring = JSON.stringify(selKeys);
            //newTemp = mystring.replace(/"/g, "'");
			$('#role_ids').val(mystring);
		},
		onDblClick: function(node, event) {
			node.toggleSelect();
		},
		onKeydown: function(node, event) {
			if( event.which == 32 ) {
				node.toggleSelect();
				return false;
			}
		},
	});
    $("#btnToggleSelect").click(function(){
        $("#request-types-tree").dynatree("getRoot").visit(function(node){
            node.toggleSelect();
        });
        return false;
    });

    $("#btnDeselectAll").click(function(){
        $("#request-types-tree").dynatree("getRoot").visit(function(node){
            node.select(false);
        });
        return false;
    });

    $("#btnSelectAll").click(function(){
        $("#request-types-tree").dynatree("getRoot").visit(function(node){
            node.select(true);
        });
        return false;
    });       
});
/*
	$(document).ready(function(){
		$('#availabl-request-types input').customInput();
		all_checkall_detail(true);
		});
function all_checkall_detail(stat){
        if(stat){
            $('.chk').prop('checked',true);
            $('.select-chk').prop('checked',true);
            $('.select-chk').addClass('checked');
            $('.chkbox-global-design').addClass('checked');
	} else {
            $('.chk').prop('checked',false);
            $('.select-chk').prop('checked',false);
            $('.select-chk').removeClass('checked');
            $('.chkbox-global-design').removeClass('checked');
	}
}
function inner_check_change(loop)
{
    if($('#role_detail_'+loop).is(':checked')){
        $('.select-chk').prop('checked',true);
        $('.select-chk').addClass('checked');
    } else {
        $('.select-chk').prop('checked',false);
        $('.select-chk').removeClass('checked');
    }
}
/*$(document).ready(function(){
	if($('.chk:checked').length == $('.chk').length){
            $("#chkselectall").prop('checked',true);
            $("#chkselectall").attr('checked',true);			
            $('.chkbox-global-design').addClass('checked');
        }
	$('.chk').change(function(){
            if($('.chk:checked').length == $('.chk').length){
                $("#chkselectall").prop('checked',true);
                $("#chkselectall").attr('checked',true);			
                $('.chkbox-global-design').addClass('checked');
            }else{
                $("#chkselectall").prop('checked',false);
                $("#chkselectall").attr('checked',false);			
                $('.chkbox-global-design').removeClass('checked');
            }
	});	
});*/
</script>
<?php } else {?>
No Record Found...
<?php }?>