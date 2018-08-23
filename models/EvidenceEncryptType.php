<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%evidence_encrypt_type}}".
 *
 * @property integer $id
 * @property string $encrypt
 * @property integer $remove
 *
 * @property Evidence[] $evidences
 * @property Evidence[] $evidences0
 */
class EvidenceEncryptType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%evidence_encrypt_type}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        	[['encrypt'], 'required'],
            [['encrypt'], 'string'],
            [['remove'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'encrypt' => Yii::t('app', 'Encrypt'),
            'remove' => Yii::t('app', 'Remove'),
        ];
    }

    public function beforeSave($insert)
    {
    	if (parent::beforeSave($insert)) {
    		
    		if($this->isNewRecord){
                       
    			$this->remove = 0;
                        
    		}
            
    		return true;
            
    	} else {
    		return false;
    	}
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvidences()
    {
        return $this->hasMany(Evidence::className(), ['evid_type' => 'id']);
    }
}
