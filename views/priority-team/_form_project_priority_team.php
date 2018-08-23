<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

\app\assets\CustomInputAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\models\EvidenceTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Project Priority Team';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php $form = ActiveForm::begin(['id' => $model->formName(),'action' => Yii::$app->urlManager->createUrl('/priority-team/project-priority-team'),'enableAjaxValidation' => false,'enableClientValidation' => true,'options' => ['enctype'=>'multipart/form-data']]); ?>
<fieldset class="one-cols-fieldset">
    <div class="create-form">
		<!-- isNewRecord -->
		<?php if($model->isNewRecord!=1) { ?>
			<input type="hidden" id="priority_id" name="priority_id" value="<?= $model->id; ?>" />
		<?php } ?>
		<?= $form->field($model, 'tasks_priority_name',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(); ?>        
		<?= $form->field($model, 'priority_desc',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textarea(); ?>        
	</div>
</fieldset>
<?php ActiveForm::end(); ?>
	
		
