<?php

namespace app\models;

use Yii;
use app\models\ReportsFieldTypeTheme;

/**
 * This is the model class for table "{{%reports_field_type}}".
 *
 * @property integer $id
 * @property string $field_type
 */
class ReportsFieldType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%reports_field_type}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
             [['field_type','field_type_theme_id'], 'required'],
            [['field_type'], 'string'],
            [['field_type_theme_id'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'field_type' => 'Field Type',
        ];
    }
    
	/**
     * @inheritdoc
     */
    public function getReportsFieldTypeTheme()
    {
    	return $this->hasOne(ReportsFieldTypeTheme::className(), ['id' => 'field_type_theme_id']);	
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportsFieldTypeOperatorLogic()
    {
        return $this->hasMany(ReportsFieldTypeOperatorLogic::className(), ['fieldtype_id'=>'id']);
    }
}
