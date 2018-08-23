<?php
use app\models\User;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\web\JsExpression;
// form
$form = ActiveForm::begin(['id' => $model->formName(),'action' => Yii::$app->urlManager->createUrl('/media/append-evidence-content'),'enableAjaxValidation' => false,'enableClientValidation' => true,'options' => ['enctype'=>'multipart/form-data']]); ?>
<fieldset class="one-cols-fieldset">
    <?php
        $model->cust_id = $data['cust_id'];
        $model->data_type = $data['data_type']; 
        $model->data_size = $data['data_size'];
        $model->unit = $data['unit'];
        $md_width = 7;
        $template = "<div class='col-md-3'>";
              if($data['type'] != 'edit' && (new User)->checkAccess(4.002)) { 
                $template.= Html::button('Add New',['class' =>  'btn btn-primary','title'=>'Add New Custodian','onclick'=>'openaddcust();','id'=>'addEvidenceCustodian']); 
                $md_width = 5;
              } 
        $template.="</div></div>";
    ?>
    <div class="create-form">
        
     <?= $form->field($model, 'cust_id',['template' => "<div class='row input-field'><div class='col-md-3'>Select Custodian</div><div class='col-md-".$md_width."' id='case_by_client'>{input}\n{hint}\n{error}</div>".$template,'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
     'data' => $listcustdata,
     'options' => ['prompt' => 'Select Custodian'],
     /* 'pluginOptions' => [
        'allowClear' => true
     ],*/]); ?>
        
       <div style="clear:both;"></div>
    
        
    <?= $form->field($model, 'data_type',['template' => "<div class='row input-field'><div class='col-md-3'>Select {label}</div><div class='col-md-8' id='case_by_client'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
    'data' => $listDataType,
    'options' => ['prompt' => 'Select Data Type'],
    'pluginOptions' => [
       // 'allowClear' => true,
        'dropdownParent' => new JsExpression('$("#case_by_client")')
    ],]);?>
    <?= $form->field($model, 'data_size',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(); ?>  
          
      <?= $form->field($model, 'unit',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
    'data' => $listUnit,
    'options' => ['prompt' => 'Select Unit'],
    'pluginOptions' => [
      //  'allowClear' => true,
        'dropdownParent' => new JsExpression('$("#case_by_client")')
    ],]);?>    
    
    <?= $form->field($model, 'data_copied_to',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['value'=> $data['data_copied_to']]); ?>        
    <input type="hidden" name="evid_id" value="<?php echo $data['evid_id']; ?>" />
    <input type="hidden" name="form-action-type" id="form-action-type" value="Add" />
    <input type="hidden" name="temp_evid_id" value="<?php echo $data['temp_evid_id']; ?>" id="temp_evid_id"/>
    <input type="hidden" name="type" value="<?php echo $data['type']; ?>" id="type"/>
    <input type="hidden" name="editEvidContentId" value="<?php echo $data['temp_evid_id']; ?>" id="editEvidContentId"/>
</fieldset>
<?php ActiveForm::end();
	//echo "<pre>"; print_r($data);die;
?>
