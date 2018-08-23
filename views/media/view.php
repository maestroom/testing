<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Evidence */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Evidences', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="evidence-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'checkedin_by',
            'dup_evid',
            'org_link',
            'other_evid_num',
            'received_date',
            'received_time',
            'received_from',
            'evd_Internal_no',
            'evid_type',
            'cat_id',
            'serial',
            'model',
            'hash',
            'quantity',
            'cont',
            'evid_desc',
            'evid_label_desc',
            'contents_total_size',
            'contents_total_size_comp',
            'unit',
            'comp_unit',
            'contents_copied_to',
            'mpw',
            'bbates',
            'ebates',
            'm_vol',
            'ftpun',
            'ftppw',
            'enctype',
            'encpw',
            'evid_stored_location',
            'evid_notes',
            'status',
            'has_contents',
            'barcode',
            'created',
            'created_by',
            'modified',
            'modified_by',
        ],
    ]) ?>

</div>
