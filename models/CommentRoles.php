<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%comment_roles}}".
 *
 * @property integer $id
 * @property integer $comment_id
 * @property integer $role_id
 */
class CommentRoles extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%comment_roles}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['comment_id', 'role_id'], 'required'],
            [['comment_id', 'role_id'], 'integer'],
            [['comment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Comments::className(), 'targetAttribute' => ['comment_id' => 'Id']],
            [['role_id'], 'exist', 'skipOnError' => true, 'targetClass' => Role::className(), 'targetAttribute' => ['role_id' => 'id']],
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
            'role_id' => 'Role ID',
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
    	return $this->hasOne(Role::className(), ['id' => 'role_id']);
    }
    
}
