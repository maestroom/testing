<div class="select-location-popup">
	<div class="row without-border">
		<input id="services" value="<?=$services?>" type="hidden">
        <input id="taskunits" value="<?=$taskunit_id?>" type="hidden">
        <input id="multi_status" value="" type="hidden">
        <!--<div class="col-md-12"><a href="#" class="task_status" data-status="0" onclick='$(".task_status").removeClass("active");$(this).addClass("active");$("#multi_status").val(0);' title="Not Started"><em title="Not Started" class="fa fa-clock-o text-primary"></em> Not Started</a></div>-->
        <div class="col-md-12"><a href="#" class="task_status" data-status="1" onclick='$(".task_status").removeClass("active");$(this).addClass("active");$("#multi_status").val(1);' title="Start"><em title="Start" class="fa fa-clock-o text-success"></em> Start</a></div>
        <div class="col-md-12"><a href="#" class="task_status" data-status="2" onclick='$(".task_status").removeClass("active");$(this).addClass("active");$("#multi_status").val(2);' title="Pause"><em title="Pause" class="fa fa-clock-o text-info"></em> Pause</a></div>
        <div class="col-md-12"><a href="#" class="task_status" data-status="3" onclick='$(".task_status").removeClass("active");$(this).addClass("active");$("#multi_status").val(3);' title="On Hold"><em title="On Hold" class="fa fa-clock-o text-gray"></em> On Hold</a></div>
        <div class="col-md-12"><a href="#" class="task_status" data-status="4" onclick='$(".task_status").removeClass("active");$(this).addClass("active");$("#multi_status").val(4);' title="Complete"><em title="Complete" class="fa fa-clock-o text-dark"></em> Complete</a></div>
    </div>
</div>
