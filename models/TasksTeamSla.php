<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%tasks_team_sla}}".
 *
 * @property integer $id
 * @property integer $tasks_teams_id
 * @property integer $teamservice_sla_id
 */
class TasksTeamSla extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tasks_team_sla}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tasks_teams_id', 'teamservice_sla_id'], 'required'],
            [['tasks_teams_id', 'teamservice_sla_id'], 'integer'],
            [['tasks_teams_id'], 'exist', 'skipOnError' => true, 'targetClass' => TasksTeams::className(), 'targetAttribute' => ['tasks_teams_id' => 'id']],
            [['teamservice_sla_id'], 'exist', 'skipOnError' => true, 'targetClass' => TeamserviceSla::className(), 'targetAttribute' => ['teamservice_sla_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tasks_teams_id' => 'Tasks Teams ID',
            'teamservice_sla_id' => 'Teamservice Sla ID',
        ];
    }
}
