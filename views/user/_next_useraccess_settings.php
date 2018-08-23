<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\Options;
use app\models\User;
use app\components\IsataskFormFlag;
use kartik\widgets\Select2;
use yii\web\JsExpression;
?>
<?php $form = ActiveForm::begin(['action' => Url::to(['user/updateuserright']), 'id' => 'update-user-right', 'enableAjaxValidation' => false, 'enableClientValidation' => true]); ?>
<?= IsataskFormFlag::widget(); // change flag   ?>
<input type="hidden" name="active_form_name" id="active-form-name" class="active-form-name" value="" />
<div class="tab-inner-fix">
    <div class="test">
        Role Type: <?php
        foreach ($role_details as $id => $name) {
            if ($model->role_id == $id) {
                echo $name;
            }
        }
        ?> 
    </div>
<?= $form->field($model, 'id')->hiddenInput()->label(false); ?>
<?= $form->field($model, 'role_id')->hiddenInput()->label(false); ?>
    <div class="mycontainer" id="user_access_third">
        <div class="roleall_chk">
            <input type="checkbox" class="user_security_all" name="user_security_all" id="user_security_all" title="Select All/None" value="all" /> Select All <label for="user_security_all" class="user_security_select_all" title="Select All/None"></label>
        </div>
        <?php $i = 1; 
        if (!empty($security_features)) {
            foreach ($security_features as $key => $childs) {
               foreach ($childs as $key1 => $child) {
                    $outer_checked = '';
                    if (!empty($role_security)) {
                        foreach ($role_security as $role_security_vals) {
                            if ($role_security_vals['security_feature_id'] == $key1) {
                                $outer_checked = "checked='checked'";
                            }
                        }
                    }
                    ?>
                    <div class="myheader" tabindex="0">
                        <span><?= $key ?></span>
                        <div class="pull-right"> 
                            <!-- outer security -->
                            <input type="checkbox" class="outer_security_setting outerchk_setting_<?php echo $i; ?>" <?= $outer_checked; ?> name="security_feature[]" id="chk_setting_<?php echo $i; ?>" value="<?= $key1 ?>" onClick="chkall_setting('<?php echo $i; ?>');" />
                            <label for="chk_setting_<?php echo $i; ?>" class="outer_security_setting_all"><span class="sr-only"><?= $key ?></span></label> 
                        </div>
                    </div>
                    <div class="content">
                        <ul>
                            <?php
                            $j = 0;
                            foreach ($child as $val) {
                                $j++;
                                if (is_array($val)) {
                                    ?>
                                    <?php
                                    $inner_checked = '';
                                    $innerforce_checked = '';
                                    if (!empty($role_security)) {
                                        foreach ($role_security as $role_security_val) {
                                            if ($role_security_val['security_feature_id'] == $val['id']) {
                                                $inner_checked = "checked='checked'";
                                            }
                                            if ($role_security_val['security_force'] == 1 && $role_security_val['security_feature_id'] == $val['id']) {
                                                $innerforce_checked = "checked='checked'";
                                            }
                                        }
                                    }
                                    ?>
                                    <li><div class="col-sm-10" title="<?php echo $val['description']; ?>"><?= $val['security_feature'] ?></div>
                                        <div class="col-sm-2">
                    <?php if ($key == 'Options - Subscribe to Email Alerts') { ?>
                                                <span class="lbl_title pull-left">Force</span>
                                                <span class="pull-left">
                                                    <input type="checkbox" class="inner_security force_inner innerforcechk_<?php echo $i; ?>" <?= $innerforce_checked; ?> name="security_force[<?= $val['id'] ?>]" id="innerforcechk_<?php echo $i; ?>_<?php echo $j; ?>" value="<?= $val['id'] ?>" onclick="if (this.checked) {
                                                                $('#innerchk_setting_<?php echo $i; ?>_<?php echo $j; ?>').prop('checked', true);
                                                                $('#innerchk_setting_<?php echo $i; ?>_<?php echo $j; ?>').next('label').addClass('checked');
                                                            }"/>
                                                    <label for="innerforcechk_<?php echo $i; ?>_<?php echo $j; ?>" id="inner_securityforce_<?php echo $j; ?>" class="inner_security_label force_inner_label_<?php echo $j; ?>"><span class="sr-only"><?= $key ?>,<?= $val['security_feature'] ?></span></label>
                                                </span>
                    <?php } ?>
                                            <div class="pull-right"> 
                                                <input type="checkbox" class="inner_security_setting innerchk_setting_<?php echo $i; ?>" <?= $inner_checked; ?> name="security_feature[]" id="innerchk_setting_<?php echo $i; ?>_<?php echo $j; ?>" value="<?= $val['id'] ?>" onClick="chkall_inner_setting('<?php echo $i; ?>');" />
                                                <label for="innerchk_setting_<?php echo $i; ?>_<?php echo $j; ?>" class="inner_security_setting_all"><span class="sr-only"><?= $key ?>,<?= $val['security_feature'] ?></span></label>
                                            </div>
                                        </div>
                                    </li>
                <?php }
            }
            ?>
                        </ul>
                    </div>
                    <?php
                    $i++;
                }
            }
        }
        ?>
    </div>	
</div>
<div class="button-set text-right"> 
<?= Html::button('Cancel', ['title' => 'Cancel', 'class' => 'btn btn-primary', 'id' => 'previous_user_settings']) ?>  
<?= Html::button('Update', ['title' => 'Update', 'class' => 'btn btn-primary', 'onclick' => 'SaveUserAccess("update-user-right",this);']) ?>
</div>
<?php ActiveForm::end(); ?>	
<script>
    $(function () {
        $('#user-tabs #second input').customInput();
        /**
         * myheader span show contant of each header
         */
        $("#user-tabs .myheader span").click(function () {
            $header = $(this).parent();
            $content = $header.next('.content');
            $content.slideToggle(500, function () {
                $header.text(function () {
                    //  change text based on condition
                    //return $content.is(":visible") ? "Collapse" : "Expand";
                });
            });
        });
        $("#user-tabs .myheader").keyup(function (event) {
            if (event.keyCode == 13) {
                $(this).children("span").click();
            }
        });

        /**
         * user settings cancel button
         */
        $('form#User #previous_user_settings_last').click(function (event) {
            var chk_status = checkformstatus(event); // check form edit status 
            if (chk_status == true)
                commonAjax(baseUrl + '/user/manage-user-access', 'admin_main_container');
        });

        $('form#User #previous_user_settings').click(function (event) {
            var chk_status = checkformstatus(event); // check form edit status 
            if (chk_status == true)
                commonAjax(baseUrl + '/user/manage-user-access', 'admin_main_container');
        });
    });
    
$(document).ready(function(){   
	var total_chk_box = $('form#update-user-right .outer_security_setting').size();
	var cnt = $('form#update-user-right .outer_security_setting:checked').size();
	$('form#update-user-right .user_security_all').prop('checked',false);
	$('form#update-user-right .user_security_select_all').removeClass('checked');
	if(total_chk_box == cnt){
		$('form#update-user-right .user_security_all').prop('checked',true);
		$('form#update-user-right .user_security_select_all').addClass('checked');
	}
	$("form#update-user-right #user_security_all").change(function () {
            if ($('form#update-user-right #user_security_all').is(':checked')){
                    $("form#update-user-right .outer_security_setting_all").addClass('checked');
            $("form#update-user-right .inner_security_setting_all").addClass('checked');
                    $("form#update-user-right .outer_security_setting").prop('checked',true);
            $("form#update-user-right .inner_security_setting").prop('checked',true);
            }else{
                    $("form#update-user-right .outer_security_setting_all").removeClass('checked');
            $("form#update-user-right .inner_security_setting_all").removeClass('checked');
                    $("form#update-user-right .outer_security_setting").prop('checked',false);
            $("form#update-user-right .inner_security_setting").prop('checked',false);
            }	
	});        
});    
</script>