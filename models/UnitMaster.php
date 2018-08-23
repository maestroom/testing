<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_unit_master".
 *
 * @property integer $id
 * @property integer $unit_id
 * @property string $unit_size
 * @property integer $unit_type
 * @property integer $unit_convert_report
 * @property integer $default_unit
 *
 * @property Unit $unit
 */
class UnitMaster extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_unit_master';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['unit_id', 'unit_size', 'unit_type'], 'required'],
            [['unit_id', 'unit_size', 'unit_type', 'unit_convert_report', 'default_unit'], 'integer'],
            [['unit_id'], 'exist', 'skipOnError' => true, 'targetClass' => Unit::className(), 'targetAttribute' => ['unit_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'unit_id' => 'Unit ID',
            'unit_size' => 'Unit Size',
            'unit_type' => 'Unit Type',
            'unit_convert_report' => 'Unit Convert Report',
            'default_unit' => 'Default Unit',
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert){
    	if (parent::beforeSave($insert)) {
    		if($this->isNewRecord) {
    			$this->default_unit = 0;	
			}
    		return true;
    	} else {
    		return false;
    	}
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUnit()
    {
        return $this->hasOne(Unit::className(), ['id' => 'unit_id']);
    }
}
