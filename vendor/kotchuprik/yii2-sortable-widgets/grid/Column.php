<?php

namespace kotchuprik\sortable\grid;

use kotchuprik\sortable\assets\SortableAsset;
use yii\helpers\Html;
use yii\web\View;

class Column extends \yii\grid\Column
{
    public $headerOptions = ['style' => 'width: 30px;'];

    public function init()
    {
        SortableAsset::register($this->grid->view);
        $this->grid->view->registerJs('initSortableWidgets();', View::POS_READY, 'sortable');
    }

    protected function renderDataCellContent($model, $key, $index)
    {
        return Html::tag('div', '', [
            'class' => 'sortable-widget-handler fa fa-arrows text-primary',
            'data-id' => $model->id,
        ]);
    }
}
