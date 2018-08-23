<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%reports_tables}}".
 *
 * @property integer $id
 * @property string $table_name
 * @property string $table_display_name
 */
class ReportsTables extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%reports_tables}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['table_name', 'table_display_name'], 'required'],
            [['table_name', 'table_display_name'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'table_name' => 'Table Name',
            'table_display_name' => 'Table Display Name',
        ];
    }
     /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportsFields()
    {
        return $this->hasMany(ReportsFields::className(), ['report_table_id' => 'id']);
    }
}
