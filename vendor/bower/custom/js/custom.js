jQuery(document).ready(function(){
	jQuery(".sysModules").click(function(){
		var module=jQuery(this).data('module');
		if(module == 'managedd'){
			CaseType();
		}
    });
}); 
/*Functions*/
function CaseType(){
	jQuery.ajax({
	       url: baseUrl +'/site/casetype',
	       type: 'get',
	       success: function (data) {
	    	   jQuery('#admin_main_container').html(data);
	       }
	  });
}
function AddCaseType(){
	jQuery.ajax({
	       url: baseUrl +'/site/addcasetype',
	       type: 'get',
	       success: function (data) {
	    	   jQuery('#admin_right').html(data);
	       }
	  });
}
function AddProcessCaseType(){
	jQuery.ajax({
	       url: jQuery('#frm-casetype').attr('action'),
	       data:jQuery('#frm-casetype').serialize(),
	       type: 'post',
	       success: function (data) {
	    	   if(data == 'OK')
	    		   CaseType();
	    	   else
	    		   jQuery('#casetype-case_type_name').blur();
	    		  //alert('Error');
	       }
	  });
}
function UpdateCaseType(id){
	jQuery.ajax({
	       url: baseUrl +'/site/updatecasetype?id='+id,
	       type: 'get',
	       success: function (data) {
	    	   jQuery('#admin_right').html(data);
	       }
	  });
}
function CancelCaseType(){
	CaseType()
}
function UpdateProcessCaseType(){
	jQuery.ajax({
	       url: jQuery('#frm-casetype').attr('action'),
	       data:jQuery('#frm-casetype').serialize(),
	       type: 'post',
	       success: function (data) {
	    	   if(data == 'OK')
	    		   CaseType();
	    	   else
	    		  alert('Error');
	       }
	  });
}
function DeleteCaseType(id){
	jQuery.ajax({
	       url: baseUrl +'/site/deletecasetype?id='+id,
	       type: 'get',
	       success: function (data) {
	    	   if(data == 'OK')
	    		   CaseType();
	    	   else
	    		  alert('Error');
	       }
	  });
}
function RemoveCaseType(){
	var keys = $('.grid-view').yiiGridView('getSelectedRows');
	console.log(keys.length);
	if(!keys.length){
		alert('Please select at least single record.');
	}
	else{
		if(confirm("Are you sure you want to delete selected records.?")){
			jQuery.ajax({
			       url: baseUrl +'/site/deleteselectedcasetype',
			       data:{keylist: keys},
			       type: 'post',
			       success: function (data) {
			    	   if(data == 'OK')
			    		   CaseType();
			    	   else
			    		  alert('Error');
			       }
			  });
		}
	}
}
