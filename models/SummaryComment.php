<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use app\models\EmailCron;
/**
 * This is the model class for table "{{%summary_comments}}".
 *
 * @property integer $Id
 * @property integer $parent_id
 * @property integer $case_id
 * @property integer $team_id
 * @property integer $team_loc
 * @property string $comment
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 */
class SummaryComment extends \yii\db\ActiveRecord
{
    public $comment_filter,$attachment;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%summary_comments}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'case_id', 'team_id', 'team_loc', 'created_by', 'modified_by'], 'integer'],
            [['comment'], 'required'],
            [['comment'], 'string'],
            [['created', 'modified'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'parent_id' => 'Parent ID',
            'case_id' => 'Case ID',
            'team_id' => 'Team ID',
            'team_loc' => 'Team Loc',
            'comment' => 'Comment',
            'created' => 'Created',
            'created_by' => 'Created By',
            'modified' => 'Modified',
            'modified_by' => 'Modified By',
        ];
    }
      /**
     * @return \yii\db\ActiveQuery
     */
    public function getChilds(){
    	return $this->hasMany(self::className(), ['parent_id'=>'Id']);
    }
      /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent(){
    	return $this->hasOne(self::className(), ['Id'=>'parent_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedUser(){
    	return $this->hasOne(User::className(), ['id' => 'created_by']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientCase()
    {
        return $this->hasOne(ClientCase::className(), ['id' => 'case_id']);
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
    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
    	if (parent::beforeSave($insert)) {
    
    		if($this->isNewRecord){
    			$this->created = date('Y-m-d H:i:s');
    			$this->created_by = Yii::$app->user->identity->id;
    			$this->modified = date('Y-m-d H:i:s');
    			$this->modified_by = Yii::$app->user->identity->id;
    			
    		}else{
    			$this->modified = date('Y-m-d H:i:s');
    			$this->modified_by = Yii::$app->user->identity->id;
    		}
    		//Place your custom code here
    		return true;
    	} else {
    		return false;
    	}
    }
    /**
    * Get Shared team Location
    */
    public function sharedTeamLoc($comment_id,$org){
        $commaneteamshared="";
        $comment=$this->findOne($comment_id);
        if($org == 'team' && $comment->case_id!=0){
            $ccdata=ClientCase::findOne($comment->case_id);
            return $commaneteamshared = ucwords($ccdata->client->client_name." - ".$ccdata->case_name);
        }else if($org == 'team' && $comment->team_id!=0){
              $commaneteamshared=$comment->team->team_name." - ".$comment->teamLocation->team_location_name;
        }else{
            $comment_shared=SummaryCommentsSharedWith::find()->where(['comment_id'=>$comment_id])->all();
            if(!empty($comment_shared)){
                foreach($comment_shared as $commentshared){
                    if($commaneteamshared==""){
                        $commaneteamshared=$commentshared->team->team_name." - ".$commentshared->teamLocation->team_location_name;
                    }else{
                        $commaneteamshared.=", ".$commentshared->team->team_name." - ".$commentshared->teamLocation->team_location_name;
                    }
                }
            }
            if($org=='case' && $commaneteamshared==""){
                $ccdata=ClientCase::findOne($comment->case_id);
                $commaneteamshared = ucwords($ccdata->client->client_name." - ".$ccdata->case_name);
            }
        }
        return $commaneteamshared;
    }

    /**
     * Replay Comments
     * */
    public function replyComment($post_data,$id,$case_id=0,$team_id=0,$team_loc=0)
    {
		$model = new SummaryComment();
		$parent_comment=SummaryComment::find()->where(['Id' => $id])->one(); 
		$comment = HtmlPurifier::process($post_data['SummaryComment']['comment']);
    	$model->comment   			 = $comment;
    	$model->parent_id 			 = $id;
        $model->case_id 			 = $parent_comment->case_id;
        $model->team_id 			 = $parent_comment->team_id;
        $model->team_loc 			 = $parent_comment->team_loc;
    	if($model->save()){
    		$comment_id = Yii::$app->db->getLastInsertId();
    		/** Code for evidence attachment start **/
    		if(!empty($_FILES['SummaryComment']['name']['attachment'][0])){
    			$docmodel = new Mydocument();
    			$doc_arr['p_id']=0;
    			$doc_arr['reference_id']=$comment_id;
    			$doc_arr['team_loc']=0;
    			$doc_arr['origination']="Summary Comment";
    			$doc_arr['is_private']=0;
    			$doc_arr['type']='F';
    			$docmodel->origination = "Summary Comment";
    			$file_arr=$docmodel->Savemydocs('SummaryComment','attachment',$doc_arr);
    		}
            /*NEW COMMENT USER LEVEL ONLY*/
			//(new SettingsEmail)->sendSummaryCommentMail($data=array('case_id'=>$case_id,'comment_id'=>$comment_id));
            EmailCron::saveBackgroundEmail(24,'opt_posted_summary_comment',$data=array('case_id'=>$case_id,'team_id'=>$team_id,'team_loc'=>$team_loc,'comment_id'=>$comment_id));
			/*NEW COMMENT USER LEVEL ONLY*/
            if($case_id!=0){
                $activity_name=$comment_id."|case_id#:".$case_id;
                (new ActivityLog)->generateLog('Summary Comment', 'Posted', $comment_id, $activity_name);
            }else{
                $activity_name=$comment_id."|team_id#:".$team_id."team_loc:".$team_loc;
                (new ActivityLog)->generateLog('Summary Comment', 'Posted', $comment_id, $activity_name);
            }
    	}
    	return;
    } 

    /**
     * Edit Comments
     * */
    public function editComment($post_data,$comment_id,$case_id,$team_id,$team_loc){
    	$model = SummaryComment::findOne($comment_id);
    	$comment = HtmlPurifier::process($post_data['SummaryComment']['comment']);
    	$model->comment   			 = $comment;
    	if(isset($post_data['remove_name_'.$comment_id]) && $post_data['remove_name_'.$comment_id]!=""){
    		(new Mydocument)->removeAttachments($post_data['remove_name_'.$comment_id]);
    	}
        if(!empty($_FILES['SummaryComment']['name']['attachment'][0])){
    		$docmodel = new Mydocument();
    		$doc_arr['p_id']=0;
    		$doc_arr['reference_id']=$comment_id;
    		$doc_arr['team_loc']=0;
    		$doc_arr['origination']="Summary Comment";
    		$doc_arr['is_private']=0;
    		$doc_arr['type']='F';
    		$docmodel->origination = "Summary Comment";
    		$file_arr=$docmodel->Savemydocs('SummaryComment','attachment',$doc_arr,$post_data['remove_name_'.$comment_id]);
    	}
    	$model->save();
        
        if($case_id!=0){
            $activity_name=$comment_id."|case_id#:".$case_id;
    	    (new ActivityLog)->generateLog('Summary Comment', 'Updated', $comment_id, $activity_name);
        }else{
            $activity_name=$comment_id."|team_id#:".$team_id."team_loc:".$team_loc;
    	    (new ActivityLog)->generateLog('Summary Comment', 'Updated', $comment_id, $activity_name);
        }
    	
    }

    /**
     * Post Comments
     * */
    public function postComment($post_data,$case_id=0,$team_id=0,$team_loc=0){
        $model = new SummaryComment();
    	$comment = HtmlPurifier::process($post_data['SummaryComment']['comment']);
    	$model->comment   			 = $comment;
    	$model->parent_id 			 = 0;
        $model->case_id 			 = $case_id;
        $model->team_id 			 = $team_id;
        $model->team_loc 			 = $team_loc;

        if($model->save()){

                $comment_id = Yii::$app->db->getLastInsertId();
                if(isset($post_data['shared_team'])){
                    foreach(explode(',',$post_data['shared_team']) as $team){
                        $model_summarycommentssharedwith = new SummaryCommentsSharedWith();
                        $team_location=explode("_",$team);
                        $model_summarycommentssharedwith->comment_id=$comment_id;
                        $model_summarycommentssharedwith->team_id=$team_location[0];
                        $model_summarycommentssharedwith->team_loc=$team_location[1];
                        $model_summarycommentssharedwith->save();
                    }
                }
                /*Store Comment Read By User*/
                $model_commentread = new  SummaryCommentsRead();
                $model_commentread->comment_id = $comment_id;
                $model_commentread->user_id    = Yii::$app->user->identity->id;
                $model_commentread->save();

                /* Code for comment attachment start */
                if(!empty($_FILES['SummaryComment']['name']['attachment'][0])){
                    $docmodel = new Mydocument();
                    $doc_arr['p_id']=0;
                    $doc_arr['reference_id']=$comment_id;
                    $doc_arr['team_loc']=0;
                    $doc_arr['origination']="Summary Comment";
                    $doc_arr['is_private']=0;
                    $doc_arr['type']='F';
                    $docmodel->origination = "Summary Comment";
                    $file_arr=$docmodel->Savemydocs('SummaryComment','attachment',$doc_arr);
                }
                /*NEW COMMENT USER LEVEL ONLY*/
                //(new SettingsEmail)->sendSummaryCommentMail
                EmailCron::saveBackgroundEmail(24,'opt_posted_summary_comment',$data=array('case_id'=>$case_id,'team_id'=>$team_id,'team_loc'=>$team_loc,'comment_id'=>$comment_id));
                /*NEW COMMENT USER LEVEL ONLY*/

                if($case_id!=0){
                    $activity_name=$comment_id."|case_id#:".$case_id;
                }else{
                    $activity_name=$comment_id."|team_id#:".$team_id."team_loc:".$team_loc;
                }
                (new ActivityLog)->generateLog('Summary Comment', 'Posted', $comment_id, $activity_name);
        }else{
            echo "<pre>",print_r($model->getErrors()),"</pre>";die;
        }
    	return true;
    }
     /**
     * delete Comments
     * */
    public function deleteComment($id,$msg=""){
    	if($msg=='parent'){
    		$chkChild=SummaryComment::find()->where(['parent_id'=>$id])->count();
    		if($chkChild){
    			return 'NA';
    		}
    		SummaryCommentsRead::deleteAll('comment_id='.$id);
    		SummaryCommentsSharedWith::deleteAll('comment_id='.$id);
    	}
    	if(isset($id) && is_numeric($id)){
    		$comment=SummaryComment::findOne($id);
    		if (!empty($comment->attachments)) {
    			foreach ($comment->attachments as $filename) {
    				(new Mydocument)->removeAttachments($filename->id);
    			}
    		}
    		$comment->delete();
    	}	
    	return true;
    }    

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttachments()
    {
    	return $this->hasMany(Mydocument::className(), ['reference_id' => 'Id'])->andOnCondition(['origination' => 'Summary Comment']);
    }

    /* get count for unread comments */
    public function getUnreadComments($caseId=0,$team_id=0,$team_loc=0, $output = '') {
        $task_ids = '';
        $taskdata = array();
        $user_id = Yii::$app->user->identity->id;
        $html = "";
        $sql="";
        if($caseId!=0){
            $sql="SELECT comment_id FROM tbl_summary_comments_read inner join tbl_summary_comments on tbl_summary_comments.Id=comment_id AND tbl_summary_comments.case_id=$caseId where parent_id = 0 AND user_id=".$user_id; 
            $unreadComment = SummaryComment::find()->where("id NOT IN ($sql) and case_id=$caseId AND parent_id = 0")->count();
        }else{
            $sql="SELECT comment_id FROM tbl_summary_comments_read inner join tbl_summary_comments on tbl_summary_comments.Id=comment_id AND tbl_summary_comments.team_id=$team_id AND tbl_summary_comments.team_loc=$team_loc where parent_id = 0 AND user_id=".$user_id; 
            $unreadComment = SummaryComment::find()->where("parent_id = 0 AND id NOT IN ($sql) and team_id=$team_id AND team_loc=$team_loc")->count();
        }
        $term = 'comment_search'; $ismagnified = 'unread_cnt';
        if($caseId!=0){
            $has_access_408=(new User)->checkAccess(4.0803);
            if ($has_access_408 && (new User)->checkAccess(4.01)){
                $html .= Html::a($unreadComment, "index.php?r=summary-comment/index&case_id=" . $caseId . "&comment=comment", ["data-pjax" => 0, "title" => $unreadComment." Unread Comments"]);
            }else{
                $html .=$unreadComment;
            }
            if ($has_access_408 && (new User)->checkAccess(4.0803)) {
                $html .= Html::a('<em class="fa fa-search" style="color:grey" title="Search Comments"></em><span class="screenreader">Search Comments</span>', "javascript:void(0);", ["onclick" => "showsearchSummarycomment('" . $caseId . "', this)", "title" => "Search Comments", "id" => "searchsummarycomment_" . $caseId]);
            }
        }else{
            $has_access_408=(new User)->checkAccess(5.073);
            if ($has_access_408 && (new User)->checkAccess(5.01)){
                $html .= Html::a($unreadComment, "index.php?r=summary-comment/index&team_id=" . $team_id . "&team_loc=".$team_loc."&comment=comment", ["data-pjax" => 0, "title" => $unreadComment." Unread Comments"]);
            }else{
                $html .=$unreadComment;
            }
            if ($has_access_408 && (new User)->checkAccess(5.073)) {
                $html .= Html::a('<em class="fa fa-search" style="color:grey" title="Search Comments"></em><span class="screenreader">Search Comments</span>', "javascript:void(0);", ["onclick" => "showsearchSummarycommentteam('" . $team_id . "','" . $team_loc . "', this)", "title" => "Search Comments", "id" => "searchsummarycomment_" . $team_id."_".$team_loc]);
            }
        }
        $html .= "</div></div>";
        return $html;
        
    }
}
