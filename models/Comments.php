<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\HtmlPurifier;
use app\models\SettingsEmail;
use app\models\CommentTeamsUsers;
use app\models\CommentRolesUsers;
use app\models\Tasks;
use app\models\EmailCron;

/**
 * This is the model class for table "{{%comments}}".
 *
 * @property integer $Id
 * @property integer $parent_id
 * @property integer $comment_origination
 * @property integer $task_id
 * @property string $comment
 * @property string $emailsend_userids
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 */
class Comments extends \yii\db\ActiveRecord
{
	private static $menuTree = array();
	public $attachment;
	public $readcount;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%comments}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'comment_origination', 'task_id', 'created_by', 'modified_by'], 'integer'],
            [['task_id', 'comment'], 'required'],
            [['comment','emailsend_userids'], 'string'],
            [['created', 'modified','emailsend_userids'], 'safe'],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tasks::className(), 'targetAttribute' => ['task_id' => 'id']],
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
            'comment_origination' => 'Comment Origination',
            'task_id' => 'Task ID',
            'comment' => 'Comment',
            'created' => 'Created',
            'created_by' => 'Created By',
            'modified' => 'Modified',
            'modified_by' => 'Modified By',
			'emailsend_userids'=>'Email Send User'
        ];
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
    public function checkAccess($model){
		//echo "<pre>"; print_r($model); 
    	$has_access=false;
    	$user_id=Yii::$app->user->identity->id;
    	$role_id=Yii::$app->user->identity->role_id;
    	if($model->created_by == $user_id){
    		$has_access=true;
    		return $has_access;
    	}else{
    		//$securityteam_ids = (new ProjectSecurity)->getUserTeamsArr($user_id);
    		if(isset($model->commentTeams) ){
    			$sql  = "SELECT user_id FROM tbl_project_security Inner Join tbl_comment_teams on  tbl_comment_teams.team_id=tbl_project_security.team_id WHERE tbl_comment_teams.comment_id=$model->Id AND tbl_project_security.user_id =".$user_id;
    			$security_data=ProjectSecurity::findBySql($sql)->count();
    			if($security_data){
    				$has_access=true;
    			}
    		}
    
    		if(isset($model->commentRoles) && $model->commentRoles!=""){
    			$sql="SELECT * FROM tbl_comment_roles WHERE role_id IN  (".$role_id.") AND comment_id=$model->Id";
    			$roles= CommentRoles::findBySql($sql)->count();
    			if($roles){
    				$has_access=true;
    			}
    			/* Role::model()->findAll(array('condition'=>"id IN (".$da->case_role.") AND id > 0",'select'=>array('id')));
    			foreach ($roles as $rol){
    				if(in_array($rol->id, $role_types))
    					$has_access=true;
    			} */
    		}
    	}
    	//echo 'has-access'.$has_access;
    	return $has_access;
    }
    public function getRecipients($model){
    	$recipients=array();
    	if(isset($model->commentRoles) && $model->commentRoles!=""){
    		foreach ($model->commentRoles as $commentRoles){
    			$recipients[$commentRoles->role->role_name]=$commentRoles->role->role_name;
    		}
    	}
    	if(isset($model->commentTeams) && $model->commentTeams!=""){
    		foreach ($model->commentTeams as $commentTeams){
    			$recipients[$commentTeams->team->team_name]=$commentTeams->team->team_name;
    		}
    	}
    	return implode(", ",$recipients);
    }
    public static function getMenuTree($task_id) {
    	if (empty(self::$menuTree)) {
    		$rows = Comments::find()->where('parent_id = 0 and task_id='.$task_id)->orderBy('created DESC')->all();
    		foreach ($rows as $item) {
    			self::$menuTree[] = self::getMenuItems($item,$task_id);
    		}
    	}
    	return self::$menuTree;
    }
    private static function getMenuItems($modelRow,$task_id) {
    
    	if (!$modelRow)
    		return;
    	
    	if (isset($modelRow->childs)) {
    		$chump = self::getMenuItems($modelRow->childs,$task_id);
    		if ($chump != null)
    			$res = array('items' => $chump, array('id' => $modelRow->Id,'data'=>$modelRow));
    		else
    			$res = array('id' => $modelRow->Id,'data'=>$modelRow);
    		return $res;
    	} else {
    		if (is_array($modelRow)) {
    			$arr = array();
    			foreach ($modelRow as $leaves) {
    				$arr[] = self::getMenuItems($leaves,$task_id);
    			}
    			return $arr;
    		} else {
    			return array('id' => $modelRow->Id,'data'=>$modelRow);
    		}
    	}
    }
    
    
    public function removeCommentsAttachmentsByProject($taskId){
    	if(isset($taskId) && $taskId!=""){
    		$sql="select tbl_mydocument.id  FROM tbl_mydocument inner join tbl_comments on tbl_mydocument.reference_id=tbl_comments.Id and tbl_mydocument.origination='Comment' and tbl_comments.task_id=".$taskId;
			//$ids=ArrayHelper::map(Mydocument::find()->select('id')->where('id IN ('.$sql.')')->all(),'id','id');
    		//if(!empty($ids))
			{
	    		/*Remove Attachments*/
	    		MydocumentsBlob::deleteAll('id IN (select doc_id  FROM tbl_mydocument where id IN ('.$sql.'))');
				$deletesql="DELETE tbl_mydocument FROM tbl_mydocument inner join tbl_comments on tbl_mydocument.reference_id=tbl_comments.Id and tbl_mydocument.origination='Comment' and tbl_comments.task_id=".$taskId;
	    		Yii::$app->db->createCommand($deletesql)->execute();
				//Mydocument::deleteAll('id IN ('.implode(",",$ids).')');
	    		/*Remove Attachments*/
    		}
    	}
    }
    /**
     * Replay Comments
     * */
    public function replyComment($post_data,$id,$task_id)
    {
		//die('here');die;
		$model = new Comments();
		$model->isNewRecord=true;
		$parent_comment=CommentRoles::find()->where(['comment_id' => $id])->one(); // comment roles
		$comment = HtmlPurifier::process($post_data['Comments']['comment']);
    	$model->comment   			 = $comment;
    	$model->parent_id 			 = $id;
    	$model->task_id   			 = $task_id;
    	// $model->comment_origination  = 2;
    	if(isset($post_data['team_ids']) && $post_data['team_ids']!="") 
    		$model->comment_origination=4;
    	else 
    		$model->comment_origination=1;
    	
    	if($model->save()){
    		$comment_id = Yii::$app->db->getLastInsertId();
    		/** case role add **/
    		$commentrole = new CommentRoles();
    		$commentrole->comment_id = $comment_id;
    		$commentrole->role_id = $parent_comment->role_id;
    		$commentrole->save();
    		/** End role **/
    		
    		/** Code for evidence attachment start **/
    		if(!empty($_FILES['Comments']['name']['attachment'][0])){
    			$docmodel = new Mydocument();
    			$doc_arr['p_id']=0;
    			$doc_arr['reference_id']=$comment_id;
    			$doc_arr['team_loc']=0;
    			$doc_arr['origination']="Comment";
    			$doc_arr['is_private']=0;
    			$doc_arr['type']='F';
    			$docmodel->origination = "Comment";
    			$file_arr=$docmodel->Savemydocs('Comments','attachment',$doc_arr);
    		}
    		$activity_name=$comment_id."|project#:".$task_id;
    		
    		/* Code for evidence attachment end */
    		(new ActivityLog)->generateLog('Project Comment', 'Posted', $comment_id, $activity_name);
    		
    		// get client case id
    		$task_info=Tasks::findOne($task_id);
    		
    		/*NEW COMMENT USER LEVEL ONLY*/
			//(new SettingsEmail)->sendCommentMail(
			$commentEmailUsers=	Comments::findOne($id)->emailsend_userids;
			EmailCron::saveBackgroundEmail(12,'opt_posted_comment',$data=array('case_id'=>$task_info->client_case_id,'project_id'=>$task_id,'comment_id'=>$comment_id, 'parent_id' => $id,'comment_email_users'=>$commentEmailUsers));
			/*NEW COMMENT USER LEVEL ONLY*/
    	}
    	return;
    } 
    /**
     * Edit Comments
     * */
    public function editComment($post_data,$id,$task_id){
    	$model = Comments::findOne($id);
    	$comment = HtmlPurifier::process($post_data['Comments']['comment']);
    	$model->comment   			 = $comment;
    	if(!empty($_FILES['Comments']['name']['attachment'][0])){
    		$docmodel = new Mydocument();
    		$doc_arr['p_id']=0;
    		$doc_arr['reference_id']=$id;
    		$doc_arr['team_loc']=0;
    		$doc_arr['origination']="Comment";
    		$doc_arr['is_private']=0;
    		$doc_arr['type']='F';
    		$docmodel->origination = "Comment";
    		$file_arr=$docmodel->Savemydocs('Comments','attachment',$doc_arr,$post_data['remove_name_'.$id]);
    	}else{
    		if(isset($post_data['remove_name_'.$id]) && $post_data['remove_name_'.$id]!=""){
    			(new Mydocument)->removeAttachments($post_data['remove_name_'.$id]);
    		}
    	}
    	$model->save();
    	$activity_name=$comment_id."|project#:".$task_id;
    	(new ActivityLog)->generateLog('Project Comment', 'Updated', $id, $activity_name);
    }
    
    /**
     * Post Comments
     * */
    public function postComment($post_data,$task_id,$case_id=0,$team_id=0,$team_loc=0){
    	$model = new Comments();
    	$team_ids=explode(", ",$post_data['team_ids']);
    	$case_ids=explode(", ",$post_data['case_ids']);
    	$comment_teams = array();
    	$comment_cases = array();
		$commentEmailUsers=null;
		if(isset($post_data['email_send_user_ids']) && $post_data['email_send_user_ids']!="") {
			$commentEmailUsers=implode(",",array_unique(explode(",",$post_data['email_send_user_ids'])));
		}
    	foreach($case_ids as $key => $sval){
    		$cases = explode(" ",$sval);
    		$comment_cases[$cases[0]][] = $cases[1];
    	}
    	foreach($team_ids as $key => $stval){
    		$teams = explode(" ",$stval);
    		$comment_teams[$teams[0]][] = $teams[1];
    	}
    	
    	$comment = HtmlPurifier::process($post_data['Comments']['comment']);
    	$model->comment   			 = $comment;
		$model->emailsend_userids    = $commentEmailUsers;
    	$model->parent_id 			 = 0;
    	$model->task_id   			 = $task_id;
    //	$model->comment_origination  = 2;
    	
    	if(isset($post_data['team_loc']) && $post_data['team_loc']!="") 
    		$model->comment_origination=4;
    	else 
    		$model->comment_origination=1;
    	
    	if($model->save()){
    		$comment_id = Yii::$app->db->getLastInsertId();
    		/*Store Comment Receipent Teams*/
    		/*if(!empty($team_ids)){
    			foreach ($team_ids as $comment_teamid) {
    				$model_commenteam = new  CommentTeams();
    				$model_commenteam->comment_id = $comment_id;
    				$model_commenteam->team_id = $comment_teamid;
    				$model_commenteam->save();
    			}
    		}*/
    		$tbl_comment_team_id = '';
    		if(!empty($comment_teams)){
    			foreach ($comment_teams as $team_key => $comment_teamid) {
    				$model_commenteam = new  CommentTeams();
    				$model_commenteam->comment_id = $comment_id;
    				$model_commenteam->team_id = $team_key;
    				$model_commenteam->save();
    				$tbl_comment_team_id = Yii::$app->db->getLastInsertId();
    				foreach($comment_teamid as $val){
    					$model_commenteamuser = new  CommentTeamsUsers();
    					$model_commenteamuser->tbl_comment_team_id = $tbl_comment_team_id;
    					$model_commenteamuser->user_id = $val;
    					$model_commenteamuser->save();
    				}
    			}
    		}
    		/*Store Comment Receipent Roles*/
    		$tbl_comment_role_id = '';
    		if(!empty($comment_cases)){
    			foreach ($comment_cases as $case_key => $comment_caseid) {
    				$model_commenteam = new  CommentRoles();
    				$model_commenteam->comment_id = $comment_id;
    				$model_commenteam->role_id = $case_key;
    				$model_commenteam->save();
    				$tbl_comment_role_id = Yii::$app->db->getLastInsertId();
    				foreach($comment_caseid as $vals){
    					$model_commencaseuser = new  CommentRolesUsers();
    					$model_commencaseuser->tbl_comment_role_id = $tbl_comment_role_id;
    					$model_commencaseuser->user_id = $vals;
    					$model_commencaseuser->save();
    				}
    			}
    			/*foreach ($case_ids as $comment_caseid){
    				$model_commentrole = new  CommentRoles();
    				$model_commentrole->comment_id = $comment_id;
    				$model_commentrole->role_id    = $comment_caseid;
    				$model_commentrole->save();
    			}*/
    		}
    		/*Store Comment Read By User*/
    		$model_commentread = new  CommentsRead();
    		$model_commentread->comment_id = $comment_id;
    		$model_commentread->user_id    = Yii::$app->user->identity->id;
    		$model_commentread->save();
    		/* Code for evidence attachment start */
    		if(!empty($_FILES['Comments']['name']['attachment'][0])){
    			$docmodel = new Mydocument();
    			$doc_arr['p_id']=0;
    			$doc_arr['reference_id']=$comment_id;
    			$doc_arr['team_loc']=0;
    			$doc_arr['origination']="Comment";
    			$doc_arr['is_private']=0;
    			$doc_arr['type']='F';
    			$docmodel->origination = "Comment";
    			$file_arr=$docmodel->Savemydocs('Comments','attachment',$doc_arr);
    		}
    		$activity_name=$comment_id."|project#:".$task_id;
    		/* Code for evidence attachment end */
    		(new ActivityLog)->generateLog('Project Comment', 'Posted', $comment_id, $activity_name);
    		/*NEW COMMENT USER LEVEL ONLY*/
    		if($case_id==0){
    			$task_info=Tasks::findOne($task_id);
    			$case_id=$task_info->client_case_id;
    		}
    		//(new SettingsEmail)->sendCommentMail(
			
			EmailCron::saveBackgroundEmail(12,'opt_posted_comment',$data=array('case_id'=>$case_id,'project_id'=>$task_id,'comment_id'=>$comment_id,'comment_email_users'=>$commentEmailUsers));
    		/*NEW COMMENT USER LEVEL ONLY*/
    		return true;
    	}
    	return false;
    }
    /**
     * delete Comments
     * */
    public function deleteComment($id,$msg=""){
    	if($msg=='parent'){
    		$chkChild=Comments::find()->where(['parent_id'=>$id])->count();
    		if($chkChild){
    			return 'NA';
    		}
    		$comment_teams_sql="SELECT id FROM tbl_comment_teams Where comment_id IN ($id)";
			CommentTeamsUsers::deleteAll('tbl_comment_team_id IN ('.$comment_teams_sql.')');
			CommentTeams::deleteAll('comment_id='.$id);
			$comment_roles_sql="SELECT id FROM tbl_comment_roles Where comment_id IN ($id)";
			CommentRolesUsers::deleteAll('tbl_comment_role_id IN ('.$comment_roles_sql.')');
    		CommentRoles::deleteAll('comment_id='.$id);
    		CommentsRead::deleteAll('comment_id='.$id);
    	}
    	if(isset($id) && is_numeric($id)){
    		$comment=Comments::findOne($id);
    		if (!empty($comment->attachments)) {
    			foreach ($comment->attachments as $filename) {
    				(new Mydocument)->removeAttachments($filename->id);
    			}
    		}
			$comment_teams_sql="SELECT id FROM tbl_comment_teams Where comment_id IN ($id)";
			CommentTeamsUsers::deleteAll('tbl_comment_team_id IN ('.$comment_teams_sql.')');
			CommentTeams::deleteAll('comment_id='.$id);
			$comment_roles_sql="SELECT id FROM tbl_comment_roles Where comment_id IN ($id)";
			CommentRolesUsers::deleteAll('tbl_comment_role_id IN ('.$comment_roles_sql.')');
    		CommentRoles::deleteAll('comment_id='.$id);
    		CommentsRead::deleteAll('comment_id='.$id);
			
    		$comment->delete();
    	}	
    	return true;
    }    
    public function getChilds(){
    	return $this->hasMany(self::className(), ['parent_id'=>'Id']);
    }
    public function getParent(){
    	return $this->hasOne(self::className(), ['Id'=>'parent_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommentsRead()
    {
        return $this->hasMany(CommentsRead::className(), ['comment_id' => 'Id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommentRoles()
    {
        return $this->hasMany(CommentRoles::className(), ['comment_id' => 'Id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommentTeams()
    {
        return $this->hasMany(CommentTeams::className(), ['comment_id' => 'Id']);
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
    public function getAttachments()
    {
    	return $this->hasMany(Mydocument::className(), ['reference_id' => 'Id'])->andOnCondition(['origination' => 'Comment']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasks(){
    	return $this->hasOne(Tasks::className(), ['id' => 'task_id']);
    }
}
