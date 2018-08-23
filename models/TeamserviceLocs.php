<?php

namespace app\models;


use Yii;
use yii\helpers\ArrayHelper;
use app\models\TeamlocationMaster;
/**
 * This is the model class for table "{{%teamservice_locs}}".
 *
 * @property integer $id
 * @property integer $teamservice_id
 * @property integer $team_loc
 *
 * @property TeamlocationMaster $teamLoc
 * @property Teamservice $teamservice
 */
class TeamserviceLocs extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%teamservice_locs}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['teamservice_id'], 'required'],
            [['teamservice_id', 'team_loc'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'teamservice_id' => 'Teamservice ID',
            'team_loc' => 'Team Loc',
        ];
    }
    /**
    * @return Team Location List Teamservice wise
    */
    public static function getTeamLocationList($teamservice_id){
    	return $teamLocation =ArrayHelper::map(TeamlocationMaster::find()->select(['id','team_location_name'])->orderBy('team_location_name')->where('id IN (SELECT team_loc FROM tbl_teamservice_locs WHERE teamservice_id='.$teamservice_id.') AND remove =0 AND  id NOT IN(0)')->all(), 'id','team_location_name');
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeamLoc()
    {
        return $this->hasOne(TeamlocationMaster::className(), ['id' => 'team_loc']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeamservice()
    {
        return $this->hasOne(Teamservice::className(), ['id' => 'teamservice_id']);
    }
}
