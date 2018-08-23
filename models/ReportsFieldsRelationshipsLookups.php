<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%reports_fields_relationships_lookups}}".
 *
 * @property integer $id
 * @property integer $reports_fields_relationships_id
 * @property string $lookup_value
 * @property string $field_value
 *
 * @property ReportsFieldsRelationships $reportsFieldsRelationships
 */
class ReportsFieldsRelationshipsLookups extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%reports_fields_relationships_lookups}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['reports_fields_relationships_id', 'lookup_value', 'field_value'], 'required'],
            [['reports_fields_relationships_id'], 'integer'],
            [['field_value'], 'string'],
            [['lookup_value'], 'string'],
            [['reports_fields_relationships_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReportsFieldsRelationships::className(), 'targetAttribute' => ['reports_fields_relationships_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'reports_fields_relationships_id' => 'Reports Fields Relationships ID',
            'lookup_value' => 'Lookup Value',
            'field_value' => 'Field Value',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportsFieldsRelationships()
    {
        return $this->hasOne(ReportsFieldsRelationships::className(), ['id' => 'reports_fields_relationships_id']);
    }
}
