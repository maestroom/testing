<?php use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\grid\GridView;
use kartik\grid\datetimepicker;
use yii\web\JsExpression;
use app\models\User;
use app\models\Role;
use app\models\Options;
use app\models\EvidenceCustodians;
use app\models\Servicetask;
use app\models\ProjectSecurity;
use app\models\Team;
use app\models\TasksUnitsTodoTransactionLog;
$checkAccess = (new ProjectSecurity)->checkTeamAccess($teamId,$team_loc);
$roleId = Yii::$app->user->identity->role_id;
?>
<?php if(isset($processTrackData['tododata']) && !empty($processTrackData['tododata'])){ ?>
			<tr>
				<th class="text-left track-exp-th-todoitems" id="todo_items" scope="col" style="width:30%;"><a href="#" title="ToDo Items" style="">ToDo Items</a></th>
				<th class="text-left track-exp-th-category" id="todo_follow_up_category" scope="col" colspan="3" style="width:40%;"><a href="#" title="Follow-up Category">Follow-up Category</a></th>
				<!--<th class="text-left track-exp-th-3" id="todo_first" scope="col">&nbsp;</th>-->
				<th class="text-left track-exp-th-5" id="todo_second" scope="col"><a href="#" title="Assigned To">Assigned To</a></th>
				<!-- <th class="text-left track-exp-th-created" id="todo_third" scope="col">Created By</th> 
				<th class="text-left track-exp-th-6" id="todo_fourth" scope="col">&nbsp;</th>
				<th class="text-left track-exp-th-7" id="todo_fifth" scope="col">&nbsp;</th>
				<th class="text-left track-exp-th-8" id="todo_sixth" scope="col">&nbsp;</th>-->
				<th class="text-left track-exp-th-9" id="todo_seven" scope="col">&nbsp;</th>
				<th class="text-left track-exp-th-9" id="todo_seven" scope="col">&nbsp;</th>
				<th class="text-right track-exp-th-10" id="todo_eight" scope="col" colspan="3">Action</th>
			</tr>
	
			<?php foreach ($processTrackData['tododata'] as $todo) { ?>
			<tr>
				<td class="v-align-top text-left track-exp-td-todoitems word-break" headers="todo_items" ><?=Html::decode($todo->todo);?>
				<?php 
                	$attachment="";
                	if (!empty($todo->todoattachments)) {
		                foreach ($todo->todoattachments as  $at) {
		                    if($attachment=="")
		                        $attachment ='<a href="javascript:void(0);" onclick="downloadattachment(' . $at->id . ')" class="icon-fa" title="Attachment"><em title="Attachment" class="fa fa-paperclip"></em><span class="screenreader">Download Attachment</span></a>';
		                    else
		                        $attachment .='&nbsp;&nbsp;<a href="javascript:void(0)" onclick="downloadattachment(' . $at->id . ')" class="icon-fa" title="Attachment"><em title="Attachment" class="fa fa-paperclip"></em><span class="screenreader">Download Attachment</span></a>';
		                }
	               }echo "&nbsp;".$attachment ?>
				</td>
				<td class="v-align-top text-left track-exp-td-category word-break" headers="todo_follow_up_category" colspan="3"><?=Html::decode($processTrackData['todo_cat_list'][$todo->todo_cat_id]);?></td>
				<!--<td class="text-left track-exp-td-3" headers="todo_first">&nbsp;</td>-->
				<td class="v-align-top text-left track-exp-td-5 word-break" headers="todo_second"><span title="Assigned to">
				<?php if($todo->assigned > 0 ) {
					//echo $servietask_id; print_r($belongtocurr_team_serarr); 
					$assignTitle="Assign Transit Todo";
					$assignonclick = "javascript:AssignTransitTodo('$servietask_id','$task_id','$team_loc','$taskunit_id','$todo->id','$teamId','$case_id','$todo->assigned');";
					$team_name = Team::find()->select('team_name')->where('id = '.$teamId)->one()->team_name;
					if(!$checkAccess){
						if (((new User)->checkAccess(4.0611) && $case_id != 0) || ((new User)->checkAccess(5.0611) && $team_id != 0)) { 
							$assignonclick = "javascript:AssignTransitTodo('$servietask_id','$task_id','$team_loc','$taskunit_id','$todo->id','$teamId','$case_id','$todo->assigned');";
							$assignTitle="Assign Transit Todo";
						}else{
						$assignonclick = "javascript:alert('This action is available only to $team_name Team Members.');";
						}
					}
					
					if (((new User)->checkAccess(4.06) && $case_id != 0) || ((new User)->checkAccess(5.05) && $team_id != 0)) { /* 83 */
							echo Html::a('<em title="'.$assignTitle.'" class="fa fa-thumb-tack text-danger"></em>',null,['href'=>$assignonclick,'class'=>"text-primary track-icon",'title'=>$assignTitle]); 
					}
					
					$tododatetime=(new Options)->ConvertOneTzToAnotherTz($todo->modified, 'UTC', $_SESSION['usrTZ']);
					$todolog = TasksUnitsTodoTransactionLog::find()->where('todo_id='.$todo->id.' AND transaction_type IN (7,8)'); //->orderBy('id desc')
					$todohover="";
					if($todolog->count() > 0 ){
						$todolog_data = $todolog->one();
						if($todolog_data->transaction_type == 7)
						$todohover=" Assign From ".$todolog_data->transactionUser->usr_first_name." ".$todolog_data->transactionUser->usr_lastname;
						else
						$todohover=" Transition From ".$todolog_data->transactionUser->usr_first_name." ".$todolog_data->transactionUser->usr_lastname;
					}
					if (in_array($servietask_id, $belongtocurr_team_serarr) && $isteamlocaccess == 'yes') {	
						echo "<span title='{$tododatetime}{$todohover}'>".$todo->assigned_user."</span>";
					}
					else if($roleId == 0){
						echo "<span title='{$tododatetime}{$todohover}'>".$todo->assigned_user."</span>";
					}
					else if($teamId == 1){
						$roleInfo=Role::findOne($roleId);
						$User_Role=explode(',',$roleInfo->role_type);
						if(in_array(1,$User_Role)){
							echo "<span title='{$tododatetime}{$todohover}'>".$todo->assigned_user."</span>";
						}else{
								if (((new User)->checkAccess(4.0611) && $case_id != 0) || ((new User)->checkAccess(5.0611) && $team_id != 0)) { 
									echo "<span title='{$tododatetime}{$todohover}'>".$todo->assigned_user."</span>";
								}else{
									echo "User ";	
								}
							}
						} else {
							if (((new User)->checkAccess(4.0611) && $case_id != 0) || ((new User)->checkAccess(5.0611) && $team_id != 0)) { 
								echo "<span title='{$tododatetime}{$todohover}'>".$todo->assigned_user."</span>";
							}else{
								echo "User ";				
							}
						
					}?></span> 
				<?php }?>	
				</td>
				<!--<td class="v-align-top text-left track-exp-td-created word-break" headers="todo_fourth"><?php echo $todo->createdUser->usr_first_name." ".$todo->createdUser->usr_lastname;  ?></td>-->
				<td class="v-align-top text-left track-exp-td-6 word-break" headers="todo_third"><?php /*if($todo->complete == 1) {?><em class="fa fa-check" title="Complete"></em> <?php }*/?></td>
				<td headers="todo_fifth" class="v-align-top text-right word-break td-no-pad track-exp-td-7" width="15" title="<?php if($todo->assigned > 0 ) {?>Transition<?php }else{?>Assign<?php }?> ToDo">
				<?php 
					if($todo->assigned == 0) {
					$assignonclick = "javascript:AssignTransitTodo('$servietask_id','$task_id','$team_loc','$taskunit_id','$todo->id','$teamId','$case_id','$todo->assigned');";
					$team_name = Team::find()->select('team_name')->where('id = '.$teamId)->one()->team_name;
					if(!$checkAccess){
						if (((new User)->checkAccess(4.0611) && $case_id != 0) || ((new User)->checkAccess(5.0611) && $team_id != 0)) { 
							$assignonclick = "javascript:AssignTransitTodo('$servietask_id','$task_id','$team_loc','$taskunit_id','$todo->id','$teamId','$case_id','$todo->assigned');";
						}else{
						$assignonclick = "javascript:alert('This action is available only to $team_name Team Members.');";
						}
					}
					
					if (((new User)->checkAccess(4.06) && $case_id != 0) || ((new User)->checkAccess(5.05) && $team_id != 0)) { /* 83 */
							echo Html::a('<em class="fa fa-thumb-tack" title="Assign Transit Todo"></em>',null,['href'=>$assignonclick,'class'=>"text-primary track-icon",'title'=>"Assign Transit Todo"]); 
					}
					}
				?>
				</td>
				
				<?php /*if($todo->complete == 1) { */?>
				<td headers="todo_seven" class="v-align-top text-right  td-no-pad track-exp-td-9" width="15" title="Incomplete ToDo">
				<?php 
					$todolog = TasksUnitsTodoTransactionLog::find()->where('todo_id='.$todo->id)->orderBy('id desc')->one();
					if ($todolog->transaction_type == 7) {
						$task_status_a = Html::a("<em title='ToDo Assigned' class='fa fa-clock-o text-primary'></em>",null, ['href'=>'javascript:void(0);',"title" => "ToDo Assigned",'class'=>"track-icon",'data-toggle'=>"dropdown"]);
					} else if ($todolog->transaction_type == 8) {
						$task_status_a = Html::a("<em title='ToDo Transitioned' class='fa fa-clock-o text-info'></em>",null, ['href'=>'javascript:void(0);',"title" => "ToDo Transitioned",'class'=>"track-icon",'data-toggle'=>"dropdown"]);
					} else if ($todolog->transaction_type == 9) {
						$task_status_a = Html::a("<em title='ToDo Complete' class='fa fa-clock-o text-dark'></em>",null, ['href'=>'javascript:void(0);',"title" => "ToDo Complete",'class'=>"track-icon",'data-toggle'=>"dropdown"]);
					} else if ($todolog->transaction_type == 11) {
						$task_status_a = Html::a("<em title='ToDo Transferred' class='fa fa-clock-o text-info'></em>",null, ['href'=>'javascript:void(0);',"title" => "ToDo Transferred",'class'=>"track-icon",'data-toggle'=>"dropdown"]);
					} else if ($todolog->transaction_type == 13) {
						$task_status_a = Html::a("<em title='ToDo Started' class='fa fa-clock-o text-success'></em>",null, ['href'=>'javascript:void(0);',"title" => "ToDo Started",'class'=>"track-icon",'data-toggle'=>"dropdown"]);
					} else if ($todolog->transaction_type == 14) {
						$task_status_a = Html::a("<em title='ToDo Not Started' class='fa fa-clock-o text-primary'></em>",null, ['href'=>'javascript:void(0);',"title" => "ToDo Not Started",'class'=>"track-icon",'data-toggle'=>"dropdown"]);	
					}
					
					if($todolog->transaction_type == 13){
						$return_data ='<div class="dropdown">'.$task_status_a.'<ul class="dropdown-menu dropdown-menu-right">';
						$onclick = "javascript:void(0)";
						if (((new User)->checkAccess(4.06) && $case_id != 0) || ((new User)->checkAccess(5.05) && $team_id != 0)) { /* 83 */
						$onclick ='javascript:CompleteTodo('.$servietask_id.','.$task_id.','.$team_loc.','.$taskunit_id.','.$todo->id.','.$teamId.','.$case_id.');';
						}
						$return_data .='<li>'.Html::a("<em title='ToDo Complete' class='fa fa-clock-o text-dark'></em> Complete",null, ['href'=>$onclick,"title" =>'ToDo Complete','class'=>'track-icon']).'</li>';
						$return_data .='</ul></div>';
					}else if(in_array($todolog->transaction_type,array(14,9,8,7))){
						$return_data ='<div class="dropdown">'.$task_status_a.'<ul class="dropdown-menu dropdown-menu-right">';
						$onclick = "javascript:void(0)";
						if (((new User)->checkAccess(4.06) && $case_id != 0) || ((new User)->checkAccess(5.05) && $team_id != 0)) { /* 83 */
						$onclick ='javascript:ReOpenTodo('.$servietask_id.','.$task_id.','.$team_loc.','.$taskunit_id.','.$todo->id.','.$teamId.','.$case_id.');'; 
						}	
						$return_data .='<li>'.Html::a("<em title='ToDo Started' class='fa fa-clock-o text-success'></em>Start",null, ['href'=>$onclick,"title" =>'ToDo Started','class'=>'track-icon']).'</li>';
						$return_data .='</ul></div>';
					}else{
						$return_data =$task_status_a;
					}
					
					echo $return_data;
					if (((new User)->checkAccess(4.06) && $case_id != 0) || ((new User)->checkAccess(5.05) && $team_id != 0)) { /* 83 */
						//echo Html::a('<em class="fa fa-undo"></em>',null,['href'=>'javascript:ReOpenTodo('.$servietask_id.','.$task_id.','.$team_loc.','.$taskunit_id.','.$todo->id.','.$teamId.','.$case_id.');','class'=>"text-primary track-icon"]); 
					}
				?>
				</td>
				<?php /*} else { ?>
				<td headers="todo_seven" class="v-align-top text-right  td-no-pad track-exp-td-9" width="15" title="Complete ToDo">
				<?php 
					if (((new User)->checkAccess(4.06) && $case_id != 0) || ((new User)->checkAccess(5.05) && $team_id != 0)) { /* 83 */
						/*echo Html::a('<em class="fa fa-check"></em>',null,['href'=>'javascript:CompleteTodo('.$servietask_id.','.$task_id.','.$team_loc.','.$taskunit_id.','.$todo->id.','.$teamId.','.$case_id.');','class'=>"text-primary track-icon"]);
					} ?>
				</td>
				<?php } */?>
				<td headers="todo_sixth" class="v-align-top text-right td-no-pad  track-exp-td-8" width="15" title="Edit ToDo">
				<?php 
					if (((new User)->checkAccess(4.06) && $case_id != 0) || ((new User)->checkAccess(5.05) && $team_id != 0)) { /* 83 */
						echo Html::a('<em title="Edit ToDo" class="fa fa-pencil"></em>',null,['href'=>'javascript:EditTodo('.$servietask_id.','.$task_id.','.$team_loc.','.$taskunit_id.','.$todo->id.');','class'=>"text-primary track-icon",'title'=>"Edit ToDo"]);
					} 
				?>
				</td>
				<td headers="todo_eight" class="v-align-top text-right  td-no-pad track-exp-td-10" width="15" title="Delete ToDo">
				<?php 
					if (((new User)->checkAccess(4.06) && $case_id != 0) || ((new User)->checkAccess(5.05) && $team_id != 0)) { /* 83 */
						$onclick="javascript:alert('You do not have access to delete this item. Please contact your administrator if your access needs to be updated.');";
						if (((new User)->checkAccess(4.06111) && $case_id != 0) || ((new User)->checkAccess(5.0511) && $team_id != 0)) { /* 83 */
						$onclick='javascript:DeleteTodo('.$todo->id.','.$teamId.','.$case_id.','.$team_loc.','.$task_id.')';
						}	
						echo Html::a('<em title="Delete ToDo" class="fa fa-close"></em>',null,['id'=>'todo_'.$todo->id,'data-name'=>Html::decode($todo->todo),'href'=>$onclick,'class'=>"text-primary track-icon",'title'=>"Delete ToDo"]);
				}?>
				</td>
			</tr>
			<?php }?>	
<?php } else {?>
			<tr class="hide-tr">
				<th class="text-left track-exp-th-todoitems hide-td" id="todo_items" scope="col" style="width:30%;"><a href="#" title="ToDo Items" style="">ToDo Items</a></th>
				<th class="text-left track-exp-th-category hide-td" id="todo_follow_up_category" scope="col" colspan="3" style="width:40%;"><a href="#" title="Follow-up Category">Follow-up Category</a></th>
				<th class="text-left track-exp-th-5 hide-td" id="todo_second" scope="col"><a href="#" title="Assigned To">Assigned To</a></th>
				<th class="text-left track-exp-th-9 hide-td" id="todo_seven" scope="col">&nbsp;</th>
				<th class="text-left track-exp-th-9 hide-td" id="todo_seven" scope="col">&nbsp;</th>
				<th class="text-right track-exp-th-10 hide-td" id="todo_eight" scope="col" colspan="3">Action</th>
			</tr>
			<tr class="hide-tr">
				<td class="v-align-top text-left track-exp-td-todoitems word-break hide-td" headers="todo_items" >&nbsp;</td>
				<td class="v-align-top text-left track-exp-td-category word-break hide-td" headers="todo_follow_up_category" colspan="3">&nbsp;</td>
				<td class="v-align-top text-left track-exp-td-5 word-break hide-td" headers="todo_second">&nbsp;</td>
				<td class="v-align-top text-left track-exp-td-6 word-break hide-td" headers="todo_third">&nbsp;</td>
				<td headers="todo_fifth" class="v-align-top text-right word-break td-no-pad track-exp-td-7 hide-td" width="15">&nbsp;</td>
				<td headers="todo_seven" class="v-align-top text-right  td-no-pad track-exp-td-9 hide-td" width="15" title="Incomplete ToDo">&nbsp;</td>
				<td headers="todo_sixth" class="v-align-top text-right td-no-pad track-exp-td-8 hide-td" width="15" title="Edit ToDo">&nbsp;</td>
				<td headers="todo_eight" class="v-align-top text-right  td-no-pad track-exp-td-10 hide-td" width="15" title="Delete ToDo">&nbsp;</td>
			</tr>
<?php }?>
