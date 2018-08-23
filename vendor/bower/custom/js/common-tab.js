jQuery(function($) {
    $( "#admin_right").find("#wf-tabs" ).tabs({
      beforeActivate: function (event, ui) {
    	    if(ui.newPanel.attr('id') == 'tabs-servicetask'){
    	    	$.ajax({
  			       url: baseUrl +'/workflow/servicetask',
  			       data:{team_id: $('#team_id').val()},
  			       type: 'post',
  			       beforeSend:function (data) {showLoader();},
  			       success: function (data) {
  			       	 hideLoader();
  			       	 $('#tabs-servicetask').html(data);
  			       }
  			  });
        	}
    	    if(ui.newPanel.attr('id') == 'tabs-assigneduser'){
    	    	$.ajax({
  			       url: baseUrl +'/workflow/teamusers',
  			       data:{team_id: $('#team_id').val()},
  			       type: 'post',
  			       beforeSend:function (data) {showLoader();},
  			       success: function (data) {
  			       	 hideLoader();
  			       	 $('#tabs-assigneduser').html(data);
  			       }
  			  	});
        	}
      },
      beforeLoad: function( event, ui ) {
        ui.jqXHR.error(function() {
          ui.panel.html(
            "Error loading current tab." );
        });
      }
    });
});