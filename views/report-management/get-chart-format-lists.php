<?php
    use yii\helpers\Html;
?>

<div class="mycontainer">
	<div class="myheader hide">
        <a href="javascript:void(0);" class="active" >Chart Format</a>
            <div class="pull-right header-checkbox">
                <input type="hidden" id="chart_format" name="Chart format"/>                                     
                <label for="chart_format">&nbsp;</label> 
            </div>
    </div>    
<?php 
    if(isset($chartformatList) && !empty($chartformatList)){ ?>
    <div class="content border-none">
        <fieldset>
            <legend class="sr-only">Available Chart Formats</legend>
            <ul>
                <?php foreach($chartformatList as $key => $fieldtype){ ?>
                    <li>
                        <!--<span><?php //echo $fieldtype['chart_format']; ?></span>-->
                        <label for="chart_format_<?php echo $fieldtype['id']; ?>" class="chkbox-global-design chart_format_<?php echo $fieldtype['id']; ?>"><?=$fieldtype['chart_format']?></label>
                        <div class="pull-right "> 
                            <input type="checkbox" id="chart_format_<?php echo $fieldtype['id']; ?>" data-chart_format="<?php echo $fieldtype['chart_format']; ?>" value="<?php echo $fieldtype['id']; ?>" class="report_chart_format" name="chart_format_<?php echo $fieldtype['id']; ?>" />                                     
                        </div>
                    </li>
                <?php } ?>
            </ul>
        </fieldset>
    </div>  
<?php }	?>
</div>
<style>
    #availabl-chart-format .mycontainer .content{display:block;}
</style>
<script>
$(function() {
	$('input').customInput();   
});
</script>
<noscript></noscript>
