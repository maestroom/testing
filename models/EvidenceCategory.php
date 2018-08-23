<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%evidence_category}}".
 *
 * @property integer $id
 * @property string $category
 * @property integer $remove
 *
 * @property Evidence[] $evidences
 */
class EvidenceCategory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%evidence_category}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category',], 'required'],
            [['remove'], 'integer'],
            [['category'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'category' => Yii::t('app', 'Category'),
            'remove' => Yii::t('app', 'Remove'),
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvidences()
    {
        return $this->hasMany(Evidence::className(), ['cat_id' => 'id']);
    }
}
