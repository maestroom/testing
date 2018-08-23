<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var getTableSchemas */
?>
<table class="table table-striped" width="100%" cellspacing="0" cellpadding="0" border="0">
<?php if(!empty($current_table)){ ?>
	<?php 
            $i=1; 
            foreach ($current_table as $key => $tablefields) { 
                $display_name = $tablefields['field_display_name'];
                $table_name=$tablefields['table_name'];
                $field_name=$tablefields['field_name']; 
	?>
        <tr>
            <td class="field-operator-id"><?= $field_name; ?></td>
            <td class="tbl-selectall-option text-center">
                <input type="hidden" name="field_lists_relationship[<?= $tablefields['field_id'] ?>]" value="<?= $field_relationship_id ?>" />
                <input type="checkbox" name="column_field_name[]" data-tbl_name = "<?= $table_name ?>" data-tbl_og_name = "<?= $table_name ?>" data-tbl_display_name = "<?= $display_name ?>" id="column_field_name_<?= $i ?>" value="<?= $tablefields['field_id'] ?>" class="column-fields check-individual" />
                <label for="column_field_name_<?= $i ?>" class="column-fields"><span class="sr-only"><?= $field_name; ?></span></label>
            </td>
        </tr>
	<?php $i++; }
	} else { ?>
		<td colspan="2" align="center">No Fields Found</td>
	<?php } ?>

</table>
<script>
	$(function() {
		$('input').customInput();
	});
</script>
<noscript></noscript>
