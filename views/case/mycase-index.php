<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use app\models\User;
use kartik\grid\GridView;
use kartik\grid\datetimepicker;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
\app\assets\HighchartAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\models\search\EvidenceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Select Case';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
        <div class="col-md-12">
          <div id="page-title" role="heading" class="page-header"><em title="Select Case" class="fa fa-briefcase"></em> <span>Select Case</span></div>
        </div>
      </div>
      <div class="row two-cols-right">
        <div class="col-xs-12 col-sm-8 col-md-9 left-side">
          <div class="right-main-container">
            <div class="select-gridview-head">
              <div class="col-sm-4 last">
                   <?php 
                   //Html::dropDownList(ArrayHelper::map($clientDropdownData, 'id', 'client_name'), ['id'=>'client_id', 'class' => 'form-control', 'prompt' => 'Select Client']);
                   echo Select2::widget([
                    'model' => $searchModel,
                    'attribute' => 'client_name',
                    'data' => ArrayHelper::map($clientDropdownData, 'id', 'client_name'),
                    'options' => ['prompt' => 'Select Client', 'id' => 'client_id', 'class' => 'form-control'],
                    'pluginOptions' => [
                      'allowClear' => true
                    ]
                    ]); ?>
              </div>
              <div class="col-sm-4 last">
                  <?php  
                   echo DepDrop::widget([
                    'type' => 2,
                    'model' => $searchModel,
                    'name' => 'case_name',
                    'options' => ['id' => 'client_case_id', 'class' => 'form-control'],
                    'pluginOptions' => [
                      'allowClear' => true,
                      'depends'=>['client_id'],
                      'prompt' => 'Select Case',
                      'url' => Url::toRoute(['case/getcasesbyclient'])
                    ]
                    ]); ?>
              </div>
              <div class="col-sm-2 last">
               <?= Html::button('Enter Portal',['title'=>"Enter Portal",'class' => 'btn btn-primary btn-block', 'onclick'=>'showselectedcase();'])?>   
              </div>
			  <div class="col-sm-2">
                <?= ((new User)->checkAccess(4.01))?Html::button('New Project',['title'=>"New Project",'class' => 'btn btn-primary btn-block', 'onclick'=>'newProjects();']):"";?>     
              </div>
            </div>
            <fieldset class="one-cols-fieldset fieldset-top">
            <?= GridView::widget([
						'id'=>'clientcase-grid',
						'dataProvider' => $dataProvider,
						'layout' => '{items}',
						'columns' => [
							 ['class' => '\kartik\grid\ExpandRowColumn', 'detailUrl' => Url::to(['case/getcasedetailsbyclient']),'headerOptions'=>['title'=>'Expand/Collapse All'],'contentOptions'=>['title'=>'Expand/Collapse Row'], 'expandIcon' => '<a href="#" aria-label="Expand Raw"><span class="glyphicon glyphicon-plus"></span></a>', 'collapseIcon' => '<a href="#" aria-label="Collapse Raw"><span class="glyphicon glyphicon-minus"></span></a>', 'expandTitle' => 'Expand', 'collapseTitle' => 'Collapse', 'expandAllTitle' => 'Expand All', 'collapseAllTitle' => 'Collapse All', 'mergeHeader'=>false, 'value' => function ($model) { return 1;}],
							 ['attribute' => 'client_name', 'label' => 'Client', 'format' => 'html', 'value' => function($model) { $ret = "<em class='fa fa-pie-chart' title='".$model->client_name."'></em> <strong>$model->client_name</strong>"; ($model->description!="")?$ret.="- $model->description":""; return $ret;}],
						],
						'export'=>false,
						'floatHeader'=>true,    
						'pjax'=>true,
						'responsive'=>false,
						'floatHeaderOptions' => ['top' => 'auto'],
						'pjaxSettings'=>[
						'options'=>['id' => 'teamassigneduser-pajax', 'enablePushState' => false],
						'neverTimeout'=>true,
						'beforeGrid'=>'',
						'afterGrid'=>'',
                    ],
                ]); ?>
            </fieldset>
            <div class="button-set text-right">
              <div class="col-sm-4 search-item-set"><input type="text" class="form-control" size="30" name="fName" id="adultFName1" placeholder="Enter Search Term"></div>
			  <button class="btn btn-primary" title="Clear Button">Select Cases</button>
			  <button class="btn btn-primary" title="Clear Button">Search</button>
			  <button class="btn btn-primary" title="Clear Button">Clear</button>
            </div>
          </div>
        </div>
        <div class="col-xs-12 col-sm-4 col-md-3 right-side">
          <div class="projects-main">
            <div class="project-block">
              <h3 class="block-title">Project Status</h3>
              <div class="block-content">
			    <div id="container-horizontal" style="width:100%; height:100%; margin:0px auto"></div>
			  </div>
            </div>
            <div class="project-block">
              <h3 class="block-title">Project Priority</h3>
              <div class="block-content">
			    <div id="container-vertical" style="width:100%; height:100%; margin:0px auto"></div>
			  </div>
            </div>
          </div>
        </div>
      </div>
