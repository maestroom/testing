<div id='reportform_div'>
    <?= $this->render('_form-lookup', [
    'field_list' => $field_list,
	'table_name' => $table_name,
	'current_table'=>$othertableList,
	'model' => $model
    ]) ?>
</div>
