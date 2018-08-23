<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_comment_teams_users".
 *
 * @property integer $id
 * @property integer $tbl_comment_team_id
 * @property integer $user_id
 */
class CommentTeamsUsers extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_comment_teams_users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tbl_comment_team_id', 'user_id'], 'required'],
            [['tbl_comment_team_id', 'user_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tbl_comment_team_id' => 'Tbl Comment Team ID',
            'user_id' => 'User ID',
        ];
    }
}
