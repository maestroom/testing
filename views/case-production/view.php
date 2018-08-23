<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\EvidenceProduction */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Evidence Productions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="evidence-production-view">

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
            'client_id',
            'client_case_id',
            'staff_assigned',
            'prod_date',
            'prod_rec_date',
            'prod_party',
            'production_desc',
            'production_type',
            'cover_let_link',
            'prod_orig',
            'prod_return',
            'attorney_notes:ntext',
            'prod_disclose',
            'prod_agencies',
            'prod_access_req',
            'has_media',
            'has_hold',
            'has_projects',
            'prod_misc1',
            'prod_misc2',
            'created',
            'created_by',
            'modified',
            'modified_by',
        ],
    ]) ?>

</div>
