<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_comment_roles_users".
 *
 * @property integer $id
 * @property integer $tbl_comment_role_id
 * @property integer $user_id
 */
class CommentRolesUsers extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_comment_roles_users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tbl_comment_role_id', 'user_id'], 'required'],
            [['tbl_comment_role_id', 'user_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tbl_comment_role_id' => 'Tbl Comment Role ID',
            'user_id' => 'User ID',
        ];
    }
}
