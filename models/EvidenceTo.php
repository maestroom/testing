<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%evidence_to}}".
 *
 * @property integer $id
 * @property string $to_name
 * @property integer $remove
 *
 * @property EvidenceTransactions[] $evidenceTransactions
 */
class EvidenceTo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%evidence_to}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        	[['to_name'], 'required'],
        	[['to_name'], 'string'],
            [['remove'], 'integer']
        ];
    }
    
    
    public function beforeSave($insert)
    {
    	if (parent::beforeSave($insert)) {
    		
    		if(!isset($this->remove) || $this->remove == ''){
    			$this->remove = 0;
    		}
    		return true;
    	} else {
    		return false;
    	}
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'to_name' => Yii::t('app', 'Media To'),
            'remove' => Yii::t('app', 'Remove'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvidenceTransactions()
    {
        return $this->hasMany(EvidenceTransactions::className(), ['Trans_to' => 'id']);
    }
}
