<?php

namespace app\models;

use Yii; 
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tbl_saved_reports".
 *
 * @property integer $id
 * @property string  $custom_report_name
 * @property string  $custom_report_description
 * @property integer $report_save_to
 * @property integer $share_report_by
 * @property integer $report_type_id
 * @property integer $report_format_id
 * @property integer $chart_format_id
 * @property string  $created
 * @property integer $created_by
 * @property string  $modified
 * @property integer $modified_by
 * @property string  $x_data
 * @property string  $y_data
 * @property string  $item_fn
 * @property string  $x_fn
 * @property string  $y_fn
 * @property string  $series
 * @property string  $item_fn_display
 * @property string  $x_fn_display
 * @property string  $y_fn_display
 * @property string  $x_data_display
 * @property string  $y_data_display
 * @property string  $series1_display
 * @property string  $series2_display
 * @property string  $title
 * @property string  $title_location
 * @property string  $legend_location
 * @property string  $datatable_location
 * @property string  $dimension 
 * @property string  $grid_line
 * @property string  $shape 
 * @property string  $fill
 * @property string  $data_label_location
 * @property string  $x_axis_location
 * @property string  $y_axis_location
 * @property string  $slice_position
 * @property string  $markers
 * @property ReportsReportType $reportType
 * @property ReportsReportFormat $reportFormat
 * @property ReportsChartFormat $chartFormat
 * @property ReportsReportTypeFields $dateTypeField
 * @property SavedReportsFields[] $savedReportsFields
 * @property SavedReportsSharedWith[] $savedReportsSharedWiths
 */
class ReportsUserSaved extends \yii\db\ActiveRecord
{
	public $flag='',$client_case,$team,$team_location;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_reports_user_saved';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['report_type_id'], 'required'],
            [['custom_report_name', 'report_save_to'],'required','when'=>function($model){ return $model->flag == 'saved';},'whenClient' => "function (attribute, value) {
				return $('input[name=\"ReportsUserSaved[flag]\"]').val() == 'saved';
		    }"],
		    [['share_report_by'],'required','when'=>function($model){ return $model->report_save_to == 2; }, 'whenClient' => 
		    "function (attribute, value) {
		    	return $('select[name=\"ReportsUserSaved[report_save_to]\"]').val() == 2;
		    }"],
            [['chart_format_id'],'required','when'=>function($model){ return $model->report_format_id == 2;},'whenClient' => "function (attribute, value) {
				return $('input[name=\"ReportsUserSaved[report_format_id]\"]').val() == 2;
		    }"],
		    [['custom_report_description'], 'string'],
            [['report_save_to', 'share_report_by', 'report_type_id', 'report_format_id', 'chart_format_id', 'created_by', 'modified_by'], 'integer'],
            [['created', 'modified'], 'safe'],
            [['custom_report_name'], 'string'],
            [['x_data','y_data','item_fn','x_fn','y_fn','series','item_fn_display','x_fn_display','y_fn_display','x_data_display','y_data_display','series1_display','series2_display','title','title_location','legend_location','datatable_location','dimension','grid_line','shape','fill','data_label_location','x_axis_location','y_axis_location','slice_position','markers'], 'string'],
            [['report_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReportsReportType::className(), 'targetAttribute' => ['report_type_id' => 'id']],
            [['report_format_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReportsReportFormat::className(), 'targetAttribute' => ['report_format_id' => 'id']],
            //[['chart_format_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReportsChartFormat::className(), 'targetAttribute' => ['chart_format_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'custom_report_name' => 'Report Name',
            'custom_report_description' => 'Report Description',
            'report_save_to' => 'Save To',
            'share_report_by' => 'Share Report',
            'report_type_id' => 'Report Type',
            'report_format_id' => 'Report Format',
            'chart_format_id' => 'Chart Format',
            'created' => 'Created',
            'created_by' => 'Created By',
            'modified' => 'Modified',
            'modified_by' => 'Modified By',
            'x_data'=>'X Data',
            'y_data'=>'Y Data',
            'item_fn'=>'Item Function',
            'x_fn'=>'X Function',
            'y_fn'=>'Y Function',
            'series'=>'Series',
            'item_fn_display'=>'Item Function Display',
            'x_fn_display'=>'X Function Display',
            'y_fn_display'=>'Y Function Display',
            'x_data_display'=>'X Data Display',
            'y_data_display'=>'Y Data Display',
            'series1_display'=>'Series1 Display',
            'series2_display'=>'Series2 Display',
            'title'=>'Title',
            'title_location'=>'Title Location',
            'legend_location'=>'Legend Location',
            'datatable_location'=>'Datatable Location',
            'dimension'=>'Dimension',
            'grid_line'=>'Grid Line',
            'shape'=>'Shape',
            'fill'=>'Fill',
            'data_label_location'=>'Data Label Location',
            'x_axis_location'=>'X Axis Location',
            'y_axis_location'=>'Y Axis Location',
            'slice_position'=>'Slice Position',
            'markers'=>'Markers'
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
		if (parent::beforeSave($insert)) {
    		if ($this->isNewRecord){
				$this->chart_format_id = (!isset($this->chart_format_id) || (isset($this->chart_format_id) && $this->chart_format_id==''))?0:$this->chart_format_id;
				$this->share_report_by = (!isset($this->share_report_by) || (isset($this->share_report_by) && $this->share_report_by==''))?0:$this->share_report_by;
				$this->created = date('Y-m-d H:i:s');
    			$this->created_by = Yii::$app->user->identity->id;
    			$this->modified = date('Y-m-d H:i:s');
    			$this->modified_by = Yii::$app->user->identity->id;
    		} else {
				$this->chart_format_id = (!isset($this->chart_format_id) || (isset($this->chart_format_id) && $this->chart_format_id==''))?0:$this->chart_format_id;
				$this->modified = date('Y-m-d H:i:s');
    			$this->modified_by = Yii::$app->user->identity->id;
    		}
    		return true;
    	} else {
    		return false;
    	}
    }  
    	
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportType()
    {
        return $this->hasOne(ReportsReportType::className(), ['id' => 'report_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportFormat()
    {
        return $this->hasOne(ReportsReportFormat::className(), ['id' => 'report_format_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChartFormat()
    {
        return $this->hasOne(ReportsChartFormat::className(), ['id' => 'chart_format_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportsUserSavedFields()
    {
        return $this->hasMany(ReportsUserSavedFields::className(), ['saved_report_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedUser()
    {
    	return $this->hasOne(User::className(), ['id' => 'created_by']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModifiedUser()
    {
    	return $this->hasOne(User::className(), ['id' => 'modified_by'])->alias('modified_by_user');;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportsUserSavedSharedWith()
    {
        return $this->hasMany(ReportsUserSavedSharedWith::className(), ['saved_report_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportsUserSavedFilterClientCase()
    {
        return $this->hasMany(ReportsUserSavedFilterClientCase::className(), ['reports_user_saved_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportsUserSavedFilterTeamserviceLoc()
    {
        return $this->hasMany(ReportsUserSavedFilterTeamserviceLoc::className(), ['reports_user_saved_id' => 'id']);
    }
    
    /**
     * get user full name
     */
     public function get_user_fullname($created_by)
     {
		 $query = ArrayHelper::map(self::find()->select(['tbl_reports_user_saved.created_by'])->with(['createdUser' => function(\yii\db\ActiveQuery $query) use ($created_by){
			 $query->select(['tbl_user.id','tbl_user.usr_first_name','tbl_user.usr_lastname']);
			 $query->where(['tbl_user.id' => $created_by]);
		 }])->all(),'created_by',function($model, $defaultValue) {
			return $model['createdUser']['usr_first_name'].' '.$model['createdUser']['usr_lastname'];
		 });
		 return $query;
	 }
	 
	 /**
	  * get the icon of format chart for Display Reports
	  * @return
	  */
	  public function getFormatTypeIcon($report_format_id, $chart_format_id)
	  {
          $images=['Bar Basic'=>'BarChart.png',
		'Bar Clustered'=>'BarClustered.png',
		'Bar Stacked'=>'BarStacked.png',
		'Column Basic'=>'ColumnBasic.png',
		'Column Clustered'=>'ColumnClustered.png',
		'Column Stacked'=>'ColumnStacked.png',
		'Line Basic'=>'LineBasic.png',
		'Line Clustered'=>'LineClustered.png',
		'Circle Pie'=>'CirclePie.png',
		'Circle Donut'=>'CircleDonut.png'];
           $modeReportsChartFormat = ArrayHelper::map(ReportsChartFormat::find()->orderBy('format_order')->all(),'id','chart_format');
		   $modeReportsChartFormat[$chart_format_id];
           $html = '';
		   if($report_format_id == 1){
			  $html = '<span tabindex="0" class="fa fa-table" title="Tabular"></span>';
		   }else{
               $imgUrl=Url::base(true).'/images/'.$images[$modeReportsChartFormat[$chart_format_id]];
               $format= $modeReportsChartFormat[$chart_format_id];
               $html='<span tabindex="0"><img src="'.$imgUrl.'" alt="'.$format.'" width="17" height="17" title="'.$format.'"></span>';

           } /*if($report_format_id == 2){
			  if(strpos(strtolower($modeReportsChartFormat[$chart_format_id]),'bar')!==false)
				  $html = '<em class="fa fa-bar-chart fa-rotate-90" title="'.$modeReportsChartFormat[$chart_format_id].'"></em>';
			  else if(strpos(strtolower($modeReportsChartFormat[$chart_format_id]),'column')!==false)
				  $html = '<em class="fa fa-bar-chart" title="'.$modeReportsChartFormat[$chart_format_id].'"></em>';
			  else if(strpos(strtolower($modeReportsChartFormat[$chart_format_id]),'line')!==false)
				  $html = '<em class="fa fa-line-chart" title="'.$modeReportsChartFormat[$chart_format_id].'"></em>';
			  else if(strpos(strtolower($modeReportsChartFormat[$chart_format_id]),'pie')!==false)
				  $html = '<em class="fa fa-pie-chart" title="'.$modeReportsChartFormat[$chart_format_id].'"></em>';
		   }*/
           
		return $html;
	  }
	  
	  /**
	   * get Share Report By Comment
	   * @return
	   */
	   public function getShareReportByComment($id){
		    // share report by comment
		    $res = self::find()->select(['report_save_to','share_report_by'])->where(['id' => $id])->one();
		    if($res->report_save_to==1) {
				$access = 'Private';
			} else if($res->report_save_to==2) {
				if($res->share_report_by==1)
					$access = 'By Role';
				else if($res->share_report_by==2)
					$access = 'By Client/Case';
				else if($res->share_report_by==3)
					$access = 'By Team/Location';
			}
			return $access;
	   }

	   /**
	    * Report Type Id
	    * @return
	    */
	    public function getReportTypeId($report_type_id)
	    {
			// echo $report_type_id;die;
			 $query = ArrayHelper::map(self::find()->select(['tbl_reports_user_saved.report_type_id'])->with(['reportType' => function(\yii\db\ActiveQuery $query) use ($report_type_id){
				 $query->select(['tbl_reports_report_type.id','tbl_reports_report_type.report_type','tbl_reports_report_type.report_type_description']);
				 $query->where(['tbl_reports_report_type.id' => $report_type_id]);
			 }])->all(),'report_type_id',function($model, $defaultValue) {
				return $model['reportType']['report_type'];
			 });
			 return $query;
	    }

        /**
	    * Report Format Id
	    * @return
	    */
	    public function getReportFormatId($report_format_id)
	    {
			 $query = ArrayHelper::map(ReportsReportFormat::find()->where(['id'=>$report_format_id])->all(),'id','report_format');
			 return $query;
	    }
}
