<?php
    use yii\helpers\Html;
?>
<div class="mycontainer">
<form id="available-field-type" autocomplete="off">
<?php 
    if(isset($themeList) && !empty($themeList)){
            foreach($themeList as $key => $fieldtype){
                ?>
                    <div class="myheader">
                        <a href="javascript:void(0);"><?php echo $fieldtype['field_type_theme']; ?></a>
                        <div class="pull-right header-checkbox">
                                <input type="checkbox" id="field_type_<?php echo $fieldtype['id']; ?>" data-fieldtype="<?php echo $fieldtype['field_type_theme']; ?>" value="<?php echo $fieldtype['id']; ?>" class="fieldtype-report primary_header" name="fieldtype_<?php echo $fieldtype['id']; ?>" />                                     
                                <label for="field_type_<?php echo $fieldtype['id']; ?>" class="field_type_<?php echo $fieldtype['id']; ?>"><span class="sr-only">Select All Field Type <?php echo $fieldtype['field_type_theme']; ?></span></label> 
                        </div>
                    </div>
                    <div class="content">
                       <fieldset>
                       <legend class="sr-only">Field Type, <?php echo $fieldtype['field_type_theme']; ?></legend>
                       <ul>
                       <?php
                       foreach ($fieldstypeList as $single){ 
                           $unique_id=uniqid();
                           if($single['field_type_theme_id'] == $fieldtype['id']){
                           ?>
                           <li> 
                                <!--<span><?php echo $single['field_type']; ?></span>-->
                                <label for="chk_<?=$single['id'] ?>" class="chkbox-global-design"><?php echo $single['field_type']; ?></label>
                                <div class="pull-right "> 
                                    <input onclick="i=0;$('.chk_<?php echo $single['field_type_theme_id']; ?>:checked').each(function(){ i++; if(i==$('.chk_<?php echo $single['field_type_theme_id']; ?>').length){ $('#field_type_<?=$single['field_type_theme_id'] ?>').next().addClass('checked');} else {$('#field_type_<?=$single['field_type_theme_id'] ?>').next().removeClass('checked'); }});"  id="chk_<?=$single['id'] ?>" type="checkbox" class="primary_table_checkbox chk_<?=$single['field_type_theme_id']; ?>" value="<?=$single['id']?>" data-tbl_name="<?=$single['field_type_theme_id']; ?>" data-tbl_field_type="<?=$single['field_type']?>">
                                    <!--<label for="chk_<?=$single['id'] ?>"><span class="sr-only"><?php echo $single['field_type']; ?></span></label>-->
                                </div>
                            </li>
                       <?php } } ?>    
                       </ul>
                       </fieldset>
                   </div>  
    <?php }
}	?>
</form>
</div>
<script>
/* Checkbox Event */	
$(function() {
	$('input').customInput();
        $(".myheader a").click(function () {
    $header = $(this).parent();
    $content = $header.next();
    $content.slideToggle(500, function () {                
    });
});
$('.myheader').on('click',function(){
	if($(this).hasClass('myheader-selected-tab')){
		$(this).removeClass('myheader-selected-tab');
	} else {
		$(this).addClass('myheader-selected-tab');
	}	
});
$('#availabl-field-types .mycontainer input.primary_header').on('click', function(){ 
    var tbl_name = $(this).val();    
    if($(this).is(":checked")) {                       
        $(this).next().addClass('checked');        
        $(this).prop('checked',true); 
        $('.chk_'+tbl_name).each(function(){             
            $(this).prop('checked',true); 
            $(this).next().addClass('checked');
        });
    }else{                  
        $('.chk_'+tbl_name).each(function(){
            $(this).prop('checked',false); 
            $(this).next().removeClass('checked');
        });
        $(this).prop('checked',false); 
        $(this).next().removeClass('checked');
    }       
});
$('#availabl-field-types .mycontainer input.primary_table_checkbox').on('click', function(){ 
    var tbl_name = $(this).data('tbl_name');        
    if($(this).is(":checked")) {                       
        $(this).prop('checked',true); 
        $(this).next().addClass('checked');
    }else {
        $(this).prop('checked',false); 
        $(this).next().removeClass('checked');
    }       
});
});
</script>
<noscript></noscript>
