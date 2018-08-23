<?php
use yii\helpers\Html;	
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;
use yii\web\Session;
use app\models\Role;
use app\models\User;
$tUrl = Yii::$app->getUrlManager()->getBaseUrl();
//$this->registerJsFile(Yii::$app->request->baseUrl.'/js/jspdf.js',['depends' => [\yii\web\JqueryAsset::className()]]);
//$this->registerJsFile(Yii::$app->request->baseUrl.'/js/jspdf.plugin.addimage.js',['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/html2canvas.min.js',['depends' => [\yii\web\JqueryAsset::className()]]);
$this->title = 'My Events';
$this->params['breadcrumbs'][] = $this->title;
if($assigned == ''){
	$assigned = 'notassigned';
}

?>
<div class="row">
	<div class="col-md-12">
		<h1 id="page-title" role="heading" class="page-header">
			<em title="My Events" class="fa fa-calendar"></em> <a href="javascript:void(0);" class="tag-header-black" title="My Events">My Events</a>
			<div class="col-sm-3 pull-right text-right">
			<span id="header-right">
				<div class="form-group field-clientcase-client_id">
			 <?php $current_url = Url::current(); 
			 $allprojects_url = Url::toRoute(['user/events', 'current_tabs' => $current_tabs]); 
			 if($current_url!=$allprojects_url){ ?>
				<?= Html::button('All Projects',['title'=>"All Projects",'class' => 'btn btn-primary all_filter', 'onclick' => 'location.href="'.$allprojects_url.'"'])?>
				<input type="button" value="My Assigned" name="my_assigned_button" onclick="myassigned('<?php echo $current_tabs; ?>');" id="myassign_button" style="display:none" class="btn btn-primary pull-right" title="My Assigned">
			<?php } else{ ?>
				<input type="button" value="My Assigned" name="my_assigned_button" onclick="myassigned('<?php echo $current_tabs; ?>');" id="myassign_button" style="" class="btn btn-primary" title="My Assigned">
				<?php } ?>
				<input type="button" value="PDF" name="yt2" onclick="" id="calendarpdf" style="" title="PDF Export" class="btn btn-primary">
				</div>
			</span>	
		</div>
		</h1>
	</div>
</div>
<div class="my-event-main" id="mydata">
	<div class="fc-content">
		<?= yii2fullcalendar\yii2fullcalendar::widget(array(
			'options'=>['id'=>'calender'],
			'clientOptions'=>[
			//'height' => 590,
			//'defaultView' =>'agendaWeek',
			'timeFormat'=> 'h:mm A',
			'titleFormat'=>['week'=>'MMMM D YYYY'],
			'eventClick' => new JsExpression("function(event) {
					var id = event.id;
					 $.ajax({
						url: 'index.php?r=user/getcalenderdetails&id='+id, 
						success: function(result){
							$('#calenderdetails').html(result);
							//$('#calender').fullCalendar('option', 'height', calender_height - 50);
							return false;
						}
						});
						
					}"),
                        'eventFocus' => new JsExpression("function(event) {
                                    var id = event.id;
                                     $.ajax({
                                            url: 'index.php?r=user/getcalenderdetails&id='+id, 
                                            success: function(result){
                                                    $('#calenderdetails').html(result);
                                                    //$('#calender').fullCalendar('option', 'height', calender_height - 50);
                                                    return false;
                                            }
                                            });

                                    }"),
			],
			
			'header' => ['left'=>'prev,next today','center'=> 'title','right'=> 'agendaDay,agendaWeek,month'],
		    'ajaxEvents' => Url::toRoute(['user/jsoncalendar','current_tabs'=>$current_tabs,'assigned'=>$assigned]),
		    'eventRender'=> new JsExpression("function(event, element) {
			     if(event.icon=='exclamation'){
		 			element.find('.fc-title').append('<span tabindex=\'0\' title=\'Past Due Task\' class=\'fa fa-exclamation fa-lg text-danger\'></span>');
				 }
				 element.bind('dblclick',function(){
				 var id = event.id;
					$.ajax({
						url: 'index.php?r=user/getdoubleclickurl&id='+id, 
						success: function(result){
						    if(result!='')
							location.href = baseUrl+result;
						}
						});
				 });
                                 element.bind('keypress',function(e){
                                 if(e.which == 13) {
                                        var id = event.id;
                                       $.ajax({
                                               url: 'index.php?r=user/getdoubleclickurl&id='+id, 
                                               success: function(result){
                                                   if(result!='')
                                                       location.href = baseUrl+result;
                                            }
                                    });
                                 }			
				 });
				
			  }"),
			  "eventAfterAllRender"=>new JsExpression("function(event, element) {
				var screen_height = screen.height;
				var header = $('header').height();
				var site_index = $('.site-index').height();
				var footer = $('.footer').height();
				var total_height = header+site_index+footer;
				var calender_height = screen_height - ($('header').height()+$('.site-index').height()+$('.footer').height());
				$('#calender').fullCalendar('option', 'height', calender_height);
			  }")
		    )
		   
		    );
		?>
	</div>
		<!--<div class="calender-action">
			<h2>Calendar Actions</h2>
			<div class="">
			</div>
		</div>-->
		<div class="calender-details">
			<h2 title="Project Overview"><a href="javascript:void(0);" title="Project Overview" class="tag-header-black">Project Overview</a></h2>
			<div id="calenderdetails">
				Select an event in the calendar to view project information.
			</div>
		</div>
</div>

<script type='text/javascript'>
$('#calender').find('.fc-button').each(function(){
 	console.log($(this));
 });
 $("#calendarpdf").click(function () {
		//showLoader();
		var fcscrollerHeight = $('.fc-scroller').height();
		var wrapHeight = $('.wrap').height();
    	$('.fc-scroller').css({'overflow':'visible','height':'auto'}); //might need to do this to grandparent nodes as well, possibly.
		$('.wrap').css({'overflow':'visible','height':'auto'});
		var center_datevalue = $('.fc-center h2').html();
    	html2canvas( [ document.getElementsByClassName('fc-view-container')[0] ], {
			width:  $('.fc-scroller')[0].scrollWidth + document.getElementsByClassName('fc-view-container')[0].clientWidth,
			height: $('.fc-scroller')[0].scrollHeight + document.getElementsByClassName('fc-view-container')[0].clientHeight,
        	onrendered: function(canvas) {
		    	$('.fc-scroller').css({'overflow':'auto','height':fcscrollerHeight}); //might need to do this to grandparent nodes as well, possibly.
				$('.wrap').css({'overflow':'auto','height':wrapHeight});
		    	var	data = canvas.toDataURL('image/png');
		    	var form = document.createElement("form");
				form.id="pdfcalendar";
			    var element1 = document.createElement("input"); 
			    form.method = "POST";
			    form.action = baseUrl+"pdf/event";   
			    $(form).append('<input type="hidden" name="image_data" value="'+data+'" />');
			    $(form).append('<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />');
			    $(form).append('<input type="hidden" name="center_datevalue" value="'+center_datevalue+'" />');
			    document.body.appendChild(form);
			    hideLoader();
			    form.submit();
			    $("#pdfcalendar").remove();
        }
     });
	});
	function myassigned(current_tabs) {
        location.href = baseUrl +'user/events&current_tabs='+current_tabs+'&assigned=assigned';
    }
$(window).resize(function(){
	var screen_height = screen.height;
	var header = $('header').height();
	var site_index = $('.site-index').height();
	var footer = $('.footer').height();
	var total_height = header+site_index+footer;
	var calender_height = screen_height - ($('header').height()+$('.site-index').height()+$('.footer').height());
	$('#calender').fullCalendar('option', 'height', calender_height);
});
</script>
<noscript></noscript>	

