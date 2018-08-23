<?php
    use yii\helpers\Html;
?>
<div class="mycontainer">
    <div class="myheader hide">
        <a href="javascript:void(0);" class="active">Field Calculation</a>
            <div class="pull-right header-checkbox">
                <input type="hidden" id="field_calculation_header" name="Chart format"/>                                     
                    <label for="field_calculation_header">&nbsp;</label> 
            </div>
    </div>    
<?php 
    if(isset($fieldcalculationList) && !empty($fieldcalculationList)){ ?>
    <div class="content">
        <ul>
        <?php
            foreach($fieldcalculationList as $key => $fieldtype){  ?>
            <li><span><?php echo $fieldtype['calculation_name']; ?></span>
                <div class="pull-right "> 
                        <input type="checkbox" id="field_calculation_<?php echo $fieldtype['id']; ?>" data-calculation_name="<?php echo $fieldtype['calculation_name']; ?>" value="<?php echo $fieldtype['id']; ?>" class="report_field_calculation" name="field_calculation_<?php echo $fieldtype['id']; ?>" />                                     
                        <label for="field_calculation_<?php echo $fieldtype['id']; ?>">&nbsp;</label> 
                </div>
            </li>
            <?php } ?>
            </ul>
    </div>  
<?php }	?>
</div>
<style>
    #availabl-field-calculation .mycontainer .content{display:block;}
</style>
<script>
$(function() {
	$('input').customInput();   
});
</script>
<noscript></noscript>