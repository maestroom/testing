<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%evidence_type}}".
 *
 * @property integer $id
 * @property string $evidence_name
 * @property integer $est_size
 * @property integer $media_unit_id
 * @property integer $remove
 */
class EvidenceType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%evidence_type}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        	[['evidence_name'], 'required'],
        	[['evidence_name'], 'string'],
            [['est_size', 'media_unit_id', 'remove'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'evidence_name' => Yii::t('app', 'Evidence Name'),
            'est_size' => Yii::t('app', 'Est Size'),
            'media_unit_id' => Yii::t('app', 'Default Unit'),
            'remove' => Yii::t('app', 'Remove'),
        	'unit'   => Yii::t('app', 'Default Unit'),
        ];
    }
   	
	/**
 	* @return \yii\db\ActiveQuery
 	*/
	public function getUnit()
	{
	    return $this->hasOne(Unit::className(), ['id' => 'media_unit_id']);
	}
}
