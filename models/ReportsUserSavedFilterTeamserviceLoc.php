<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%reports_user_saved_filter_teamservice_loc}}".
 *
 * @property integer $id
 * @property integer $reports_user_saved_id
 * @property integer $teamservice_id
 * @property integer $teamservice_loc
 *
 * @property ReportsUserSaved $reportsUserSaved
 * @property Teamservice $teamservice
 * @property TeamserviceLocs $teamserviceLoc
 */
class ReportsUserSavedFilterTeamserviceLoc extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%reports_user_saved_filter_teamservice_loc}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['reports_user_saved_id', 'teamservice_id', 'teamservice_loc'], 'required'],
            [['reports_user_saved_id', 'teamservice_id', 'teamservice_loc'], 'integer'],
            [['reports_user_saved_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReportsUserSaved::className(), 'targetAttribute' => ['reports_user_saved_id' => 'id']],
            [['teamservice_id'], 'exist', 'skipOnError' => true, 'targetClass' => Teamservice::className(), 'targetAttribute' => ['teamservice_id' => 'id']],
            [['teamservice_loc'], 'exist', 'skipOnError' => true, 'targetClass' => TeamserviceLocs::className(), 'targetAttribute' => ['teamservice_loc' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'reports_user_saved_id' => 'Reports User Saved ID',
            'teamservice_id' => 'Teamservice ID',
            'teamservice_loc' => 'Teamservice Loc',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportsUserSaved()
    {
        return $this->hasOne(ReportsUserSaved::className(), ['id' => 'reports_user_saved_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeamservice()
    {
        return $this->hasOne(Teamservice::className(), ['id' => 'teamservice_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeamserviceLoc()
    {
        return $this->hasOne(TeamserviceLocs::className(), ['id' => 'teamservice_loc']);
    }
}
