<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\Pjax;
?>
<?php Pjax::begin([
'enablePushState' => false,
'enableReplaceState' => false
]);?>
<?= 
ListView::widget([
    'dataProvider' => $dataProvider,
    'options' => [
        'tag' => 'ul',
        'class' => 'sub-links checkbox-with-sub-link',
        'id' => 'list-wrapper',
    ],
    'itemView' => function($model, $key, $index, $widget) use($from){
	    return $this->render('_list_item_user',['model' => $model,'i'=>$key,'from'=>$from]);
	},
    'layout' => "{items}\n{pager}",
    'pager'=>['maxButtonCount'=>3],
]); 
Pjax::end();
?>
<script>
$(function() {
  $('.userschkall').customInput();
});
</script>
<noscript></noscript>
