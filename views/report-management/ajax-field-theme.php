<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<div class="admin-left-module-list">
    <ul class="sub-links">
        <?php    
        if(!empty($fieldstypeList)){ foreach ($fieldstypeList as $fieldtype){?>
            <li id="report_field-type_<?php echo $fieldtype->id; ?>"><a href="javascript:updateReportfieldtype(<?php echo $fieldtype->id; ?>,'Select Field Type','field-type','<?=$fieldtype->field_type_theme_id?>');"><em class="fa fa-building text-danger"></em> <?=Html::encode($fieldtype->field_type); ?></a></li>
        <?php }}?>
    </ul>
</div>
                    