<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_system_maintenance_logs".
 *
 * @property integer $id
 * @property string $action
 * @property string $created
 * @property integer $created_by
 */
class SystemMaintenanceLogs extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_system_maintenance_logs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['action'], 'required'],
            [['created'], 'safe'],
            [['created_by'], 'integer'],
            [['action'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'action' => 'Action',
            'created' => 'Created',
            'created_by' => 'Created By',
        ];
    }

    public function addLogs($act){
        $model=new SystemMaintenanceLogs();
        $model->action=$act;
        $model->save();
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
    	if (parent::beforeSave($insert)) {
    		$this->created    = date('Y-m-d H:i:s');
    		$this->created_by = Yii::$app->user->identity->id;
    		return true;
    	} else {
    		return false;
    	}
    }
    public static function UtcToEst($date){
        $date = new \DateTime($date, new \DateTimeZone('UTC'));
        $date->setTimezone(new \DateTimeZone('EST'));
        return $date->format('m/d/Y h:i A T'); 
    }

   public function rrmdir($dir) { 
        if (is_dir($dir)) { 
            $objects = scandir($dir); 
            foreach ($objects as $object) { 
            if ($object != "." && $object != "..") { 
                if (is_dir($dir."/".$object))
                $this->rrmdir($dir."/".$object);
                else
                @unlink($dir."/".$object); 
            } 
            }
            @rmdir($dir); 
        } 
   }
   public function folderSize($dir)
   {
        $size = 0;
        foreach (glob(rtrim($dir, '/').'/*', GLOB_NOSORT) as $each) {
            $size += is_file($each) ? filesize($each) : $this->folderSize($each);
        }
        return $size;
   }
   public function format_size($size) {
        $mod = 1024;
        $units = explode(' ','B KB MB GB TB PB');
        for ($i = 0; $size > $mod; $i++) {
            $size /= $mod;
        }
        return round($size) . ' ' . $units[$i];
   }
}
