<?php
use yii\helpers\Html;	
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;
use yii\web\Session;

?>
<div class="row">
	<div class="col-md-12">
		<div id="page-title" role="heading" class="page-header default-header">
			<em class="fa fa-calendar" title="My Events"></em> <span>My Events</span>
		</div>
	</div>
</div>
<div class="row">
	
	<div class="col-xs-9 col-sm-9 col-md-9 fc-content">
		<?php echo yii2fullcalendar\yii2fullcalendar::widget(array(
			'options'=>['id'=>'calender'],
			'clientOptions'=>['eventClick' => new JsExpression("function(event) {
					var id = event.id;
					 $.ajax({
						url: 'index.php?r=user/getcalenderdetails&id='+id, 
						success: function(result){
							$('#calenderdetails').html(result);
						return false;
						}
						});
						
					}"),
			'defaultDate'=>'2014-02-01',
			'defaultView'=>'agendaDay'],
			'header' => ['left'=>'prev,next today','center'=> 'title','right'=> 'month,agendaWeek,agendaDay'],
		    'ajaxEvents' => Url::toRoute(['user/jsoncalendar','current_tabs'=>$current_tabs]),
		    ));
		?>
	</div>
</div>
