<?php

namespace app\models;
//use kotchuprik\sortable\behaviors;

use Yii;

/**
 * This is the model class for table "{{%priority_team}}".
 *
 * @property integer $id
 * @property string $tasks_priority_name
 * @property string $priority_desc
 * @property integer $priority_order
 * @property integer $remove
 */
class PriorityTeam extends \yii\db\ActiveRecord
{
	public $assoc_team;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%priority_team}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tasks_priority_name'], 'required'],
            [['tasks_priority_name', 'priority_desc'], 'string'],
         //   [['priority_order', 'remove'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tasks_priority_name' => 'Priority',
            'priority_desc' => 'Priority Description',
            'priority_order' => 'Priority Order',
            'assoc_team'=>'Associate Team Locations',
            'remove' => 'Remove',
        ];
    }
    public function getTeamLoc($id){
		$tlocs="";
		$team_loc=PriorityTeamLoc::find()->where(['priority_team_id'=>$id])->all();
		if(!empty($team_loc)){
			foreach($team_loc as $teamloc){
				if($tlocs=="")
					$tlocs=$teamloc->team->team_name." - ".$teamloc->teamLoc->team_location_name;
				else
					$tlocs.="<br> ".$teamloc->team->team_name." - ".$teamloc->teamLoc->team_location_name;		
			}
		}
		return $tlocs;
	}
    /**
     * @sortable behaviors
     */
    /*public function behaviors()
    {
    	return [
			'sortable' => [
				//'class' => \kotchuprik\sortable\behaviors\Sortable::className(),
				//'query' => self::find()->where(['remove'=>0]),
				//'orderAttribute'=>'priority_order',
			],
    	];
    }*/
    
    public function getPriorityTeamLoc(){
        return $this->hasMany(PriorityTeamLoc::className(), ['priority_team_id' => 'id']);
    }
    
}
