<?php

// Role Form
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

// End
$js = <<<JS
function SaveRole(form_id,btn){
	var form = $('form#'+form_id);
	$.ajax({
        url    : form.attr('action'),
        cache: false,
        type   : 'post',
        data   : form.serialize(),
        beforeSend : function()    {
        	$(btn).attr('disabled','disabled');
        },
        success: function (response){
		 	if(response == 'OK'){
 				commonAjax(baseUrl +'/user/manage-role','admin_main_container');
 			}else{
         		$(btn).removeAttr("disabled");
         	}
        },
        error  : function (){
            console.log('internal server error');
        }
    });
}
JS;
$this->registerJs($js);
?>
<style>
    <!--
    .mycontainer .content {
        display: none;
        padding : 5px;
    }
    .mycontainer .myheader a {
        cursor: pointer;
    }
    -->
</style>
<?php $form = ActiveForm::begin(['action' => Url::to(['user/addrole']), 'class' => 'Role-Security', 'id' => $model->formName(), 'enableAjaxValidation' => false, 'enableClientValidation' => true]); ?>
<div class="tab-inner-fix">    
<div class="mycontainer manage-role-security">
        <div class="roleall_chk">
            <label for="select_all" id="select_alls" name="roleAll" title="Select All/None"><span class="screenreader">Select All or None</span></label>
            <input type="checkbox" class="select_all" name="roleAll" title="Select All/None" id="select_all" value="all" /> Select All
            
        </div>
    <fieldset>
        <legend><span class="sr-only">Role Security</span></legend>
        <?php $i = 1; ?>
        <?php
        if (!empty($security_features)) {
            foreach ($security_features as $key => $childs) {
                $uniqid = uniqid();
                ?>
                <?php foreach ($childs as $key1 => $child) {
                    ?>
                    <?php
                    $outer_checked = '';
                    if (!empty($role_security)) {
                        foreach ($role_security as $role_security_vals) {
                            if ($role_security_vals['security_feature_id'] == $key1) {
                                $outer_checked = "checked='checked'";
                            }
                        }
                    }

                    ?>
                    <div class="myheader">
                        <a id="header_<?= $i ?>" href="javascript:void(0);"><?= $key ?></a>
                        <div class="pull-right header-checkbox"> 
                            <label  for="chk_<?php echo $i; ?>" class="outer_security_label">&nbsp;</label>
                            <input type="checkbox" aria-labelledby="header_<?= $i ?>" class="outer_security outerchk_<?php echo $i; ?>" <?= $outer_checked; ?> name="security_feature[]" id="chk_<?php echo $i; ?>" value="<?= $key1 ?>" onClick="chkall('<?php echo $i; ?>');" />
                        </div>
                    </div>
                    <div class="content">
                        <fieldset>
                        <legend><span class="sr-only">Role Security <?= $key ?></span></legend>
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
                            <li><div class="col-sm-10" title="<?php echo $val['description']; ?>" id="<?php echo $secFec = uniqid();?>"><?= $val['security_feature'] ?></div>
                                        <div class="col-sm-2">
                    <?php if ($key == 'Options - Subscribe to Email Alerts') { ?>
                                                <span class="lbl_title pull-left">Force</span>
                                                <span class="pull-left">
                                                    <label for="innerforcechk_<?php echo $i; ?>_<?php echo $j; ?>" id="inner_securityforce_<?php echo $j; ?>" class="inner_security_label force_inner_label"><span class="screenreader"><?php echo $val['security_feature']; ?></label>
                                                    <input type="checkbox"  aria-label="Force, <?php echo $val['security_feature']; ?>" class="inner_security force_inner innerforcechk_<?php echo $i; ?>" <?= $innerforce_checked; ?> name="security_force[<?= $val['id'] ?>]" id="innerforcechk_<?php echo $i; ?>_<?php echo $j; ?>" value="<?= $val['id'] ?>" onclick="if (this.checked) {
                                                        $('#innerchk_<?php echo $i; ?>_<?php echo $j; ?>').prop('checked', true);
                                                        $('#innerchk_<?php echo $i; ?>_<?php echo $j; ?>').next('label').addClass('checked');
                                                    }" />
                                                    
                                                </span>
                    <?php } ?>
                                            <div class="pull-right"> 
                                                <label for="innerchk_<?php echo $i; ?>_<?php echo $j; ?>" id="inner_security_<?php echo $j; ?>" class="inner_security_label"><span class="sr-only"><?php echo $val['security_feature']; ?></span></label>
                                                <input type="checkbox" class="inner_security innerchk_<?php echo $i; ?>" <?= $inner_checked; ?> name="security_feature[]" id="innerchk_<?php echo $i; ?>_<?php echo $j; ?>" value="<?= $val['id'] ?>" onClick="chkall_inner('<?php echo $i; ?>');" aria-labelledby="<?=$secFec?>" />
                                            </div>
                                        </div>

                                    </li>
                <?php }
            } ?>
                        </ul>
                        </fieldset>
                    </div>
            <?php $i++;
        }
    }
} ?>	
        </fieldset>
    </div>
    </div>
<div class="button-set text-right">
    <?= Html::button('Previous', ['title' => 'Previous', 'class' => 'btn btn-primary', 'id' => 'previous-role']) ?>
    <?= Html::button('Cancel', ['title' => 'Cancel', 'class' => 'btn btn-primary', 'id' => 'roleDetailsCancel']) ?>
    <?= Html::button($model->isNewRecord ? 'Add' : 'Update', ['title' => $model->isNewRecord ? 'Add' : 'Update', 'class' => 'btn btn-primary', 'onclick' => 'SaveRole("' . $model->formName() . '",this);']) ?>
</div>
<?php ActiveForm::end(); ?>  


<script>
$(function () {
        $('#role-tabs #second input').customInput();
    });
    /* checkbox is change form */
    $(':checkbox').change(function () {
        $('#Role #is_change_form').val('1');
        $('#is_change_form_main').val('1');
    });
    $('#roleDetailsCancel').click(function () {
        var chk_status = checkformstatus("event"); // check form edit status 
        if (chk_status == true)
            commonAjax(baseUrl + '/user/manage-role', 'admin_main_container');
    });

    $('#previous-role').click(function () {
        /*jQuery('#first').show();
        jQuery('#second').hide();
        jQuery('li[aria-controls="first"]').addClass('ui-tabs-active ui-state-active');
        jQuery('li[aria-controls="second"]').removeClass('ui-tabs-active ui-state-active');*/
        $("#role-tabs").tabs({disabled: [1]});
        $("#role-tabs").tabs({enabled: [0]});
        $('#role-tabs').tabs({active:0});
    });
    /**
     * Check All Inner checkbox
     * @ return checked
     */
    function chkall_inner(loop1) {
        var total_chk_box = $('.innerchk_' + loop1).size();
        var count = $('.innerchk_' + loop1 + ':checked').size();
        if (count == 0) {
            $(".outerchk_" + loop1).prop('checked', false);
        } else {
            $(".outerchk_" + loop1).prop('checked', true);
        }
        //if(total_chk_box == count){

        //}
    }

    $(document).ready(function () {
        $('.select_all').customInput();
        $('.outer_security').customInput();
        $('.inner_security').customInput();
        /**
         * Select All checkbox checked
         */
        var total_chk_box = $('.outer_security').size();
        var cnt = $('.outer_security:checked').size();
        $('.select_all').prop('checked', false);
        if (total_chk_box == cnt) {
            $('#select_alls').prop('checked', true);
            $('#select_alls').addClass('checked');
        }
        /**
         * selected all security features
         */
        $(".select_all").change(function () {
            if ($('.select_all').is(':checked')) {
                $('.outer_security_label').addClass('checked');
                $('.inner_security_label').not('.force_inner_label').addClass('checked');
                $(".outer_security").prop('checked', true);
                $(".inner_security").not('.force_inner').prop('checked', true);
            } else {
                $('.outer_security_label').removeClass('checked');
                $('.inner_security_label').removeClass('checked');
                $(".outer_security").prop('checked', false);
                $(".inner_security").prop('checked', false);
            }
        });
    });



    /**
     * checked all selected security features main
     */
    function chkall(loop1) {
        var total_chk_box = $('.outer_security').size();
        var cnt = $('.outer_security:checked').size();
        $('.select_all').prop('checked', false); // select All checkbox unchecked 
        $('#select_alls').removeClass('checked');
        if (total_chk_box == cnt) { // select All checkbox checked
            $('#select_alls').addClass('checked');
            $('.select_all').prop('checked', true);
        }
        if ($('#chk_' + loop1).is(':checked')) {
            $(".innerchk_" + loop1).prop('checked', true);
            $('#inner_security_' + loop1).addClass('checked');
        } else {
            $(".innerchk_" + loop1).prop('checked', false);
            $('#inner_security_' + loop1).removeClass('checked');
        }
    }

    /**
     * myheader span 
     */
    $(".myheader a").click(function () {
        $header = $(this).parent();
        $content = $header.next();
        $content.slideToggle(500, function () {
            $header.text(function () {
                //  change text based on condition
                //return $content.is(":visible") ? "Collapse" : "Expand";
            });
        });
    });
    $(".manage-role-security .myheader").keyup(function (event) {
            if (event.keyCode == 13) {
                $(this).children("a").click();
            }
        });

    /**
     * Header span
     */
    $('.myheader').on('click', function () {
        if ($(this).hasClass('myheader-selected-tab')) {
            $(this).removeClass('myheader-selected-tab');
        } else {
            $(this).addClass('myheader-selected-tab');
        }
    });
</script>
<noscript></noscript>
