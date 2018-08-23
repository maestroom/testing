<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%case_xteam}}".
 *
 * @property integer $id
 * @property integer $client_case_id
 * @property integer $team_id
 * @property integer $team_loc
 * @property integer $teamservice_id
 */
class ClientXteam extends \yii\db\ActiveRecord
{

	public $isserviceexcluded = 0;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%client_xteam}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id', 'team_id', 'team_loc', 'teamservice_id'], 'required'],
            [['client_id', 'team_id', 'team_loc', 'teamservice_id','isserviceexcluded'], 'integer'],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClientCase::className(), 'targetAttribute' => ['client_case_id' => 'id']],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => Team::className(), 'targetAttribute' => ['id' => 'id']],
            [['team_loc'], 'exist', 'skipOnError' => true, 'targetClass' => TeamlocationMaster::className(), 'targetAttribute' => ['team_loc' => 'id']],
            [['teamservice_id'], 'exist', 'skipOnError' => true, 'targetClass' => Teamservice::className(), 'targetAttribute' => ['teamservice_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_id' => 'Client ID',
            'team_id' => 'Team ID',
            'team_loc' => 'Team Loc',
            'teamservice_id' => 'Teamservice ID',
        	'isserviceexcluded' => 'Is Service Excluded'
        ];
    }
    
	/**
     * @return mixed
     */
    public function checkServiceExcluded()
    {
        return intval($this->isserviceexcluded);
    }
    
    public function getTeamservice()
    {
        return $this->hasOne(Teamservice::className(), ['id' => 'teamservice_id']);
    }
}
