<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%reports_calculation_sp_params}}".
 *
 * @property integer $id
 * @property integer $sp_id
 * @property string $params
 * @property string $type
 *
 * @property ReportsCalculationSp $sp
 */
class ReportsCalculationSpParams extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%reports_calculation_sp_params}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sp_id', 'params', 'type'], 'required'],
            [['sp_id'], 'integer'],
            [['params', 'type'], 'string'],
            [['sp_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReportsCalculationSp::className(), 'targetAttribute' => ['sp_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sp_id' => 'Sp ID',
            'params' => 'Params',
            'type' => 'Type',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSp()
    {
        return $this->hasOne(ReportsCalculationSp::className(), ['id' => 'sp_id']);
    }
}
