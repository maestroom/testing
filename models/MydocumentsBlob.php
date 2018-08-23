<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%mydocuments_blob}}".
 *
 * @property integer $id
 * @property integer $doc_id
 * @property string $doc
 *
 * @property Mydocument $doc0
 */
class MydocumentsBlob extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%mydocuments_blob}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['doc_id'], 'integer'],
            [['doc'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'doc_id' => 'Doc ID',
            'doc' => 'Doc',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoc0()
    {
        return $this->hasOne(Mydocument::className(), ['id' => 'doc_id']);
    }
}
