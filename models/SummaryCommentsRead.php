<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%summary_comments_read}}".
 *
 * @property integer $id
 * @property integer $comment_id
 * @property integer $user_id
 *
 * @property SummaryComments $comment
 * @property User $user
 */
class SummaryCommentsRead extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%summary_comments_read}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['comment_id', 'user_id'], 'required'],
            [['comment_id', 'user_id'], 'integer'],
            [['comment_id'], 'exist', 'skipOnError' => true, 'targetClass' => SummaryComment::className(), 'targetAttribute' => ['comment_id' => 'Id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'comment_id' => 'Comment ID',
            'user_id' => 'User ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComment()
    {
        return $this->hasOne(SummaryComments::className(), ['Id' => 'comment_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
