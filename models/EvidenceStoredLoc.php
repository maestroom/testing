<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%evidence_stored_loc}}".
 *
 * @property integer $id
 * @property string $stored_loc
 * @property integer $remove
 *
 * @property Evidence[] $evidences
 */
class EvidenceStoredLoc extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%evidence_stored_loc}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['stored_loc'], 'required'],
            [['stored_loc'], 'string'],
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
            'stored_loc' => Yii::t('app', 'Stored Loc'),
            'remove' => Yii::t('app', 'Remove'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvidences()
    {
        return $this->hasMany(Evidence::className(), ['evid_stored_location' => 'id']);
    }
}
