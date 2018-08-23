<?php

namespace kotchuprik\sortable\actions;

use yii\base\Action;
use yii\db\ActiveQuery;
use yii\web\BadRequestHttpException;

class Sorting extends Action
{
    /** @var ActiveQuery */
    public $query;

    /** @var string */
    public $orderAttribute = 'order';

    public function run()
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            foreach (\Yii::$app->request->post('sorting') as $order => $id) {
                $query = clone $this->query;
                $model = $query->andWhere(['id' => $id])->one();
                if ($model === null) {
                    throw new BadRequestHttpException();
                }
                $model->{$this->orderAttribute} = $order + 1;
                $model->update(false, [$this->orderAttribute]);
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
        }
    }
    public function getOrderAttribute()
    {
    	return $this->orderAttribute;
    }
    
    public function setOrderAttribute($value)
    {
    	$this->orderAttribute = trim($value);
    }
}
