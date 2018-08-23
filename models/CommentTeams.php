<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%comment_teams}}".
 *
 * @property integer $id
 * @property integer $comment_id
 * @property integer $team_id
 */
class CommentTeams extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%comment_teams}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['comment_id', 'team_id'], 'required'],
            [['comment_id', 'team_id'], 'integer'],
            [['comment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Comments::className(), 'targetAttribute' => ['comment_id' => 'Id']],
            [['team_id'], 'exist', 'skipOnError' => true, 'targetClass' => Team::className(), 'targetAttribute' => ['team_id' => 'id']],
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
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeam()
    {
    	return $this->hasOne(Team::className(), ['id' => 'team_id']);
    }
}
