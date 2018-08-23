<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_summary_comments_shared_with".
 *
 * @property integer $id
 * @property integer $comment_id
 * @property integer $team_id
 * @property integer $team_loc
 */
class SummaryCommentsSharedWith extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_summary_comments_shared_with';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['comment_id', 'team_id', 'team_loc'], 'required'],
            [['comment_id', 'team_id', 'team_loc'], 'integer'],
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
            'team_id' => 'Team ID',
            'team_loc' => 'Team Loc',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeam()
    {
        return $this->hasOne(Team::className(), ['id' => 'team_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeamLocation()
    {
        return $this->hasOne(TeamlocationMaster::className(), ['id' => 'team_loc']);
    }
}
