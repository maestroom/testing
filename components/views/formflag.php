<?php use yii\helpers\Html; ?>
<!-- Form Flag Change (0 or 1) -->
<input type="hidden" id="is_change_form" name="is_change_form" value="<?= (isset($_REQUEST['is_change_form']) && $_REQUEST['is_change_form']=='0')?0:$_REQUEST['is_change_form']; ?>" class="is_change_form" />
<input type="hidden" id="is_change_form_main" name="is_change_form_main" value="<?= (isset($_REQUEST['is_change_form_main']) && $_REQUEST['is_change_form_main']=='0')?0:$_REQUEST['is_change_form_main']; ?>" class="is_change_form_main" />
<input type="hidden" id="active_form_name" name="active_form_name" value="<?= (isset($_REQUEST['active_form_name']) && $_REQUEST['active_form_name']=='0')?"":$_REQUEST['is_change_form_main']; ?>" class="active_form_name" />
