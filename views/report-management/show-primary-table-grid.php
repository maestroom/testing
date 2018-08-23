<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\grid\GridView;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\models\ReportsFieldCalculations */
/* @var $form yii\widgets\ActiveForm */
?>
<?php

if(isset($dataProvider)){           
	echo   GridView::widget([                        
		  'id'=>'get-report-type-grid',
		  'dataProvider' => $dataProvider,
		  'layout' => '{items}',
		  'columns' => [
				['class' => '\kartik\grid\ExpandRowColumn', 'format'=>'raw',  'detail' => function($dataProvider, $key, $index, $column)use($field_relationships,$tables){return $this->render('get-primary-table-fields', ['dataprovider'=>$dataProvider, 'keyValue'=>$key, 'index'=>$index, 'field_relationships'=>$field_relationships,'tables'=>$tables]);},'headerOptions'=>['title'=>'Expand/Collapse All','class'=>'first-th'],'contentOptions'=>['title'=>'Expand/Collapse Row','class' => 'first-td text-center'], 'expandIcon' => '<a href="javascript:void(0);" aria-label="Expand Raw"><span class="glyphicon glyphicon-plus"></span></a>', 'collapseIcon' => '<a href="javascript:void(0);" aria-label="Collapse Raw"><span class="glyphicon glyphicon-minus"></span></a>', 'expandTitle' => 'Expand', 'collapseTitle' => 'Collapse', 'expandAllTitle' => 'Expand All', 'collapseAllTitle' => 'Collapse All', 'mergeHeader'=>true, 'value' => function ($model) { return 1;}],
			    ['attribute' => 'table_full_name', 
					'label' => '', 
					'header'=>'Table Display Name' ,
					'headerOptions'=>['title'=>'Table Display Name','class'=>'table_display_name_th'], 
					'contentOptions' => ['class' => 'table_display_name_td'], 
					'format' => 'raw', 
					'value' => function($dataProvider, $key, $index, $column) use($tables) {
						if(!isset($tables[$key]['id'])){
							return "<span class='tableList".$key."'>Calculation Fields</span>";
						}else{
							return "<input type='hidden' name='table_name[]' class='tablesList' value='".$tables[$key]['id']."'/><span class='tableList".$key."'>".$tables[$key]['table_display_name']."</span>";
						}
					}
			    ],
			    ['attribute' => 'table_name', 'label' => '', 'header'=>'Table Source' ,
					'headerOptions'=>['title'=>'','class'=>'table_name_th'], 'contentOptions' => ['class' => 'table_name_td'], 'format' => 'html', 'value' => function($dataProvider,$key,$index, $column){if($key!='calcutions'){ return $key;}}],
				['class' => 'kartik\grid\ActionColumn',
				'header' => '<span title="Add Table or Calculated Fields" class="fa fa-plus text-primary" style="cursor: pointer;padding:5px;display:none;" onClick="javascript:add_primary_table(0,\'\');"></span>',
				'headerOptions' => ['class'=>'third-th table_name_action_th','title'=>'Actions'],
				'contentOptions' => ['class' => 'third-td table_name_action_td'],
				'mergeHeader'=>false,
				'template'=>'{add}{relationship}{delete}',
				'buttons'=>[
						'delete'=>function($dataProvider, $key, $index) use($tables){
							if($tables[$index]['id'] > 0){
							return Html::a('<em class="fa fa-close text-primary "></em>', 'javascript:void(0)', [
								'title' => Yii::t('yii', 'Remove'),
                                                                'aria-label' => Yii::t ( 'yii', 'Remove' ),
								'class' => 'icon-set',
								'onClick' => 'remove_grid_report_relationship_table(this,"'.$tables[$index]['table_display_name'].'","'.$index.'");',
							]);
							}
						},
						'relationship'=>function($dataProvider, $key, $index) use($tables){
							if($tables[$index]['id'] > 0){
								return Html::a('<em class="fa fa-sitemap text-danger "></em>', 'javascript:void(0)', [
									'title' => Yii::t('yii', 'Add Related Table'),
									'class' => 'icon-set',
									'onClick' => 'add_primary_table("'.$tables[$index]['id'].'","relationship");',
								]);
							}
						},
						'add'=>function($dataProvider, $key, $index) use($tables){
							if($tables[$index]['id'] > 0){
								return Html::a('<em class="fa fa-plus text-primary "></em>', 'javascript:void(0)', [
									'title' => Yii::t('yii', 'Add Additional Fields'),
									'class' => 'icon-set',
									'onClick' => 'add_primary_table("'.$tables[$index]['id'].'","addfields");',
								]);
							}else{
								return Html::a('<em class="fa fa-plus text-primary "></em>', 'javascript:void(0)', [
									'title' => Yii::t('yii', 'Add Caclulation Fields'),
									'class' => 'icon-set',
									'style'	=> ' margin-left: 61px',
									'onClick' => 'add_calculation("'.$tables[$index]['id'].'","addfields");',
								]);
							}
						}
					],
				],
			 ],
			  'export'=>false,
			  'floatHeader'=>false,
			  'pjax'=>true,
			  'responsive'=>false,
			  'floatHeaderOptions' => ['top' => 'auto'],
			  'persistResize'=>false,
			  'resizableColumns'=>false,
			  'pjaxSettings'=>[
			  'options'=>['id'=>'get-report-type-grid-pajax','enablePushState' => false],
			  'neverTimeout'=>true,
			  'beforeGrid'=>'',
			  'afterGrid'=>'',
		],
		'rowOptions'=>['class'=>'sort'],
  ]);
} 
?>
<script>
$(function(){
	$('input').customInput();
});
</script>
<noscript></noscript>
