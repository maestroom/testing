<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var getTableSchemas */

$this->title = 'Get Table List';
$this->params['breadcrumbs'][] = $this->title;
?>
<script>
$(function() {
	$('input').customInput();
        $('.my_secondary_header2 a').on('click',function(){
	if($(this).parent().hasClass('myheader-selected-tab')){
		$(this).parent().premoveClass('myheader-selected-tab');
	} else {
		$(this).parent().addClass('myheader-selected-tab');
	}
        $header = $(this).parent();
        $content = $header.next();
        $content.slideToggle(500, function () {                
        });
});
$('#availabl-second-tables .mycontainer input.primary_table').on('click', function(){ 
    var tbl_name = $(this).val();    
    if($(this).is(":checked")) {
        $('.tbl_secondary_name').val(tbl_name);
        $('#availabl-second-tables .mycontainer input.primary_table').each(function(){
            var filed_tbl = $(this).val();
            $('#availabl-second-tables .mycontainer .chk_'+filed_tbl).each(function(){
                $(this).prop('checked',false); 
                $(this).next().removeClass('checked');
            });
            $(this).prop('checked',false); 
            $(this).next().removeClass('checked');
        });       
        $(this).next().addClass('checked');        
        $(this).prop('checked',true); 
        $('#availabl-second-tables .mycontainer .chk_'+tbl_name).each(function(){             
            $(this).prop('checked',true); 
            $(this).next().addClass('checked');
        });
    }else{ 
        $('.tbl_secondary_name').val('');
        $('#availabl-second-tables .mycontainer .chk_'+tbl_name).each(function(){
            $(this).prop('checked',false); 
            $(this).next().removeClass('checked');
        });
        var uniq_single = $(this).data('uniquetbl_id')
        $('#availabl-second-tables .mycontainer  .radio-'+uniq_single).prop('checked',false);
        $('#availabl-second-tables .mycontainer  .radio-'+uniq_single).next().removeClass('checked');
        $(this).prop('checked',false); 
        $(this).next().removeClass('checked');
    }       
});
$('#availabl-second-tables .mycontainer input.primary_table_relation').on('click', function(){ 
    //alert($(this).data('unique_id'));
    var unique_id = $(this).data('unique_id');
    $('#availabl-second-tables .mycontainer .all_join_select').addClass('hide');        
    $('#availabl-second-tables .mycontainer .tbl_join_type_'+unique_id).removeClass('hide'); 
    $('#availabl-second-tables .mycontainer .'+unique_id).prop('checked',true); 
    $('#availabl-second-tables .mycontainer .'+unique_id).next().addClass('checked');
});
$('#availabl-second-tables .mycontainer input.primary_table_checkbox').on('click', function(){ 
    var tbl_name = $(this).data('tbl_name');        
    if($(this).is(":checked")) {
        $('#availabl-second-tables .mycontainer .tbl_secondary_name').val(tbl_name);
        $('#availabl-second-tables .mycontainer input.primary_table').each(function(){
            var filed_tbl = $(this).val();
            if(filed_tbl != tbl_name){                
                $('#availabl-second-tables .mycontainer .chk_'+filed_tbl).each(function(){
                    $(this).prop('checked',false); 
                    $(this).next().removeClass('checked');
                });
                $(this).prop('checked',false); 
                $(this).next().removeClass('checked');
            }
        });       
        $(this).prop('checked',true); 
        $(this).next().addClass('checked');
    }else {
        var unique_single_id = $(this).data('unique_single_id');       
        $('#table_primary_relation_'+unique_single_id).prop('checked',false);
        $('#table_primary_relation_'+unique_single_id).next().removeClass('checked');
        $('#availabl-second-tables .mycontainer .primary_table_relation').each(function(){
           $(this).prop('checked',false);
           $(this).next().removeClass('checked');
        });
         var unique_checkbox_id = $(this).data('unique_chk_id');
        $('.radio-'+unique_checkbox_id).prop('checked',false);
        $('.radio-'+unique_checkbox_id).next().removeClass('checked');
    }       
});
    });
</script>
<noscript></noscript>
<?php

if(!empty($tables_list)){    
foreach($tables_list as $tbl_fields) {
    //echo '<pre>',print_r($tbl_fields);die;
    $tbl_show = 1;
    if($post_data['primary_table_name'] == $tbl_fields->fullName){
        $tbl_show = 0;
    }
    if (strpos($post_data['secondary_tables_name'], $tbl_fields->fullName) === false) {                
    }else {
        $tbl_show = 0;
    }
     if($tbl_show == 1){    
    $table_name = ucwords(str_replace("_",' ',str_replace('tbl_','',$tbl_fields->fullName)));
    $tbl_name   =   $tbl_fields->fullName;
    $unique_id=uniqid();
    ?> 
    <div class="myheader my_secondary_header2">
    	<a href="javascript:void(0);"><?php echo $table_name; ?></a>
    	<div class="pull-right header-checkbox">    	 
    	<input type="checkbox" id="table_primary_<?=$tbl_name."_".$unique_id ?>" name="table_primary[]" value="<?php echo $tbl_name; ?>" class="parent_<?php echo $tbl_name; ?> primary_table" data-uniquetbl_id="<?=$unique_id?>" />         
		<label for="table_primary_<?=$tbl_name."_".$unique_id ?>">&nbsp;</label> 
    	</div>
    </div>
    <div class="content">        
        <ul>
        <?php foreach ($tbl_fields->columns as $key => $field_detail){            
            $dbfield = strtoupper(explode('(',$field_detail->dbType)[0]);                
            if(isset($fieldtypeselectList[$dbfield]) && $fieldtypeselectList[$dbfield] != ''){
                $field_type = $fieldtypeselectList[$dbfield];
            }                       
            $display_name = ucwords(str_replace("_",' ',$field_detail->name)); 
            $unique_radio=uniqid();
            $primary_raltion = $tbl_name.'.'.$field_detail->name;
            ?>
            <li>
                <span><?php echo $display_name; ?></span> 
               
                <div class="pull-right "> 
                    <div class="custom-inline-block-width">
                        <input onclick="i=0;$('.chk_<?php echo $tbl_name; ?>:checked').each(function(){ i++; if(i==$('.chk_<?php echo $tbl_name; ?>').length){ $('#table_primary_<?=$tbl_name."_".$unique_id ?>').next().addClass('checked');} else {$('#table_primary_<?=$tbl_name."_".$unique_id ?>').next().removeClass('checked'); }});" data-unique_cnt_id="<?=$unique_radio?>" data-unique_chk_id="<?=$unique_id?>"  id="chk_<?=$tbl_name."_".$field_detail->name.'_'.$unique_id ?>" type="checkbox" class="primary_table_checkbox chk_<?=$tbl_name; ?> <?=$unique_radio?>"  name="tbl_fields[<?= $field_detail->name?>]" value="<?=$field_detail->name?>" data-tbl_name="<?=$tbl_name; ?>" data-tbl_field_type="<?=$field_type?>" data-tbl_display_name="<?=$display_name?>" data-table_full_name="<?=$table_name?>">                        
                        <label for="chk_<?=$tbl_name."_".$field_detail->name.'_'.$unique_id ?>">&nbsp;</label>
                    </div>
                </div>
                 <div class="pull-right">                         
                     <div class="custom-inline-block-width">
                         <input id="table_primary_relation_<?=$unique_radio ?>" name="tl_relation[relation]" type="radio" value="<?php echo $field_detail->name; ?>" class="primary_table_relation radio-<?=$unique_id?>" data-unique_id="<?=$unique_radio?>" data-primary_relation="<?=$primary_raltion?>" data-checkbox_id="<?=$unique_id?>" title="Select Relation"> 
                        <label for="table_primary_relation_<?=$unique_radio ?>">&nbsp;</label>
                    </div>
                </div>
                <div class="pull-right" style="margin-right:5px;"> 
                     <select name="join_type" class="tbl_join_type_<?=$unique_radio?> hide all_join_select form-control input-sm">
                        <option value="">Select Join Type</option>
                        <option value="1">LEFT JOIN</option>
                        <option value="2">INNER JOIN</option>
                        <option value="3">RIGHT JOIN</option>
                    </select>
                </div>
            </li>
        <?php } ?>    
        </ul>        
    </div>  
<?php   } } }?>    