/**
 * This work is licensed under the Creative Commons Attribution-Share Alike 3.0
 * United States License. To view a copy of this license,
 * visit http://creativecommons.org/licenses/by-sa/3.0/us/ or send a letter
 * to Creative Commons, 171 Second Street, Suite 300, San Francisco, California, 94105, USA.
 *
 * Modified by: Jill Elaine
 * Email: jillelaine01@gmail.com
 *
 * Configurable idle (no activity) timer and logout redirect for jQuery.
 * Works across multiple windows and tabs from the same domain.
 *
 * Dependencies: JQuery v1.7+, JQuery UI, store.js from https://github.com/marcuswestin/store.js - v1.3.4+
 *
 * version 1.0.10
 **/

/*global jQuery: false, document: false, store: false, clearInterval: false, setInterval: false, setTimeout: false, clearTimeout: false, window: false, alert: false*/
/*jslint indent: 2, sloppy: true, plusplus: true*/

(function ($) {
  
  $.fn.idleTimeout = function (userRuntimeConfig) {
	  
    //##############################
    //## Public Configuration Variables
    //##############################
    var defaultConfig = {
      redirectUrl: '/logout',      // redirect to this url on logout. Set to "redirectUrl: false" to disable redirect

      // idle settings
      idleTimeLimit: 259200,           // 'No activity' time limit in seconds. 1200 = 20 Minutes
      idleCheckHeartbeat: 1,       // Frequency to check for idle timeouts in seconds

      // optional custom callback to perform before logout
      customCallback: false,       // set to false for no customCallback
      // customCallback:    function () {    // define optional custom js function
          // perform custom action before logout
      // },

      // configure which activity events to detect
      // http://www.quirksmode.org/dom/events/
      // https://developer.mozilla.org/en-US/docs/Web/Reference/Events
      activityEvents: 'click keypress scroll wheel mousewheel mousemove', // separate each event with a space

      // warning dialog box configuration
      enableDialog: true,           // set to false for logout without warning dialog
      dialogDisplayLimit: 30,       // Time to display the warning dialog before logout (and optional callback) in seconds. 180 = 3 Minutes
      dialogTitle: 'Your Session is about to expire!', // also displays on browser title bar
      dialogText: 'Because you have been inactive, your session is about to expire.',
      dialogTimeRemaining: 'You will be logged out in ',
      dialogStayLoggedInButton: 'Keep Alive',
      dialogLogOutNowButton: 'Sign Out',

      // error message if https://github.com/marcuswestin/store.js not enabled
      errorAlertMessage: 'Please disable "Private Mode", or upgrade to a modern browser. Or perhaps a dependent file missing. Please see: https://github.com/marcuswestin/store.js',

      // server-side session keep-alive timer
      sessionKeepAliveTimer: 60,   // ping the server at this interval in seconds. 600 = 10 Minutes. Set to false to disable pings
      sessionKeepAliveUrl: window.location.href // set URL to ping - does not apply if sessionKeepAliveTimer: false
    },

    //##############################
    //## Private Variables
    //##############################
      currentConfig = $.extend(defaultConfig, userRuntimeConfig), // merge default and user runtime configuration
      origTitle = document.title, // save original browser title
      activityDetector,
      startKeepSessionAlive, stopKeepSessionAlive, keepSession, keepAlivePing, // session keep alive
      idleTimer, remainingTimer, checkIdleTimeout, checkIdleTimeoutLoop, startIdleTimer, stopIdleTimer, // idle timer
      openWarningDialog, dialogTimer, checkDialogTimeout, startDialogTimer, stopDialogTimer, isDialogOpen, destroyWarningDialog, countdownDisplay, // warning dialog
      logoutUser,
      isLogoutTriggered = false;
    
    //console.log(currentConfig);
    //##############################
    //## Public Functions
    //##############################
    // trigger a manual user logout
    // use this code snippet on your site's Logout button: $.fn.idleTimeout().logout();
    this.logout = function () {
      //console.log($(this));
      $(window).unbind(defaultConfig.activityEvents);
      //console.log($(window));
      //alert($(window));
      //return false;
    	$('#loader').css('z-index','9999');
    	$('#loader').show();
    	setCookie('idleTimerLoggedOut', 'true');
    	//store.set('idleTimerLoggedOut', true);
    };

    //##############################
    //## Private Functions
    //##############################

    //----------- KEEP SESSION ALIVE FUNCTIONS --------------//
    startKeepSessionAlive = function () {

      keepSession = function () {
        $.get(currentConfig.sessionKeepAliveUrl);
        startKeepSessionAlive();
      };

      keepAlivePing = setTimeout(keepSession, (currentConfig.sessionKeepAliveTimer * 1000));
    };

    stopKeepSessionAlive = function () {
      clearTimeout(keepAlivePing);
    };

    //----------- ACTIVITY DETECTION FUNCTION --------------//
    activityDetector = function () {
      $(window).on(currentConfig.activityEvents, function () {
    	  //if(store.get('idleTimerLoggedOut') === true){
    	  if(getCookie('idleTimerLoggedOut') === 'true') {
    		  $('#loader').css('z-index','9999');
    		  $('#loader').show();
    		  $(window).unbind(defaultConfig.activityEvents);
          //alert('in activityDetector : '+isLogoutTriggered);
          if(isLogoutTriggered === false){
          	logoutUser();
          }
    	  } else {
	    	  if (!currentConfig.enableDialog || (currentConfig.enableDialog && isDialogOpen() !== true)) {
	    		  startIdleTimer();
	    	  }
    	  }
      });
    };

    //----------- IDLE TIMER FUNCTIONS --------------//
    var t = 0;
    checkIdleTimeout = function () {
      //var timeIdleTimeout = (store.get('idleTimerLastActivity') + (currentConfig.idleTimeLimit * 1000));
    	var idleTimeLimit = getCookie('idleTimeLimit');
    	var timeIdleTimeout = (parseInt(getCookie('idleTimerLastActivity')) + parseInt(idleTimeLimit * 1000));
    	//console.log(t + " = " + currentConfig.idleTimeLimit+ " idle ");
      if ($.now() > timeIdleTimeout) {
        if (!currentConfig.enableDialog) { // warning dialog is disabled
        	$('#loader').css('z-index','9999');
        	$('#loader').show();
        	$(window).unbind(defaultConfig.activityEvents);
          //alert('in checkIdleTimeout 1 : '+isLogoutTriggered);
          if(isLogoutTriggered === false){
            logoutUser(); // immediately log out user when user is idle for idleTimeLimit
          }
        } else if (currentConfig.enableDialog && isDialogOpen() !== true) {
          openWarningDialog();
          startDialogTimer(); // start timing the warning dialog
        }
      } else if (getCookie('idleTimerLoggedOut') === 'true') {
    	  //else if (store.get('idleTimerLoggedOut') === true) { //a 'manual' user logout?
    	  $('#loader').css('z-index','9999');
    	  $('#loader').show();
        $(window).unbind(defaultConfig.activityEvents);
        //alert('in checkIdleTimeout 2 : '+isLogoutTriggered);
        if(isLogoutTriggered === false){
    	   logoutUser();
        }
      } else {
        if (currentConfig.enableDialog && isDialogOpen() === true) {
          destroyWarningDialog();
          stopDialogTimer();
        }
      }
      t++;
    };

    startIdleTimer = function () {
      stopIdleTimer();
      setCookie('idleTimerLastActivity', $.now()); 
      checkIdleTimeoutLoop();
    };

    checkIdleTimeoutLoop = function () {
		  checkIdleTimeout();
      idleTimer = setTimeout(checkIdleTimeoutLoop, (currentConfig.idleCheckHeartbeat * 1000));
    };

    stopIdleTimer = function () {
      clearTimeout(idleTimer);
    };

    //----------- WARNING DIALOG FUNCTIONS --------------//
    openWarningDialog = function () {

      //var dialogContent = "<div id='idletimer_warning_dialog'><p>" + currentConfig.dialogText + "</p><p style='display:inline'>" + currentConfig.dialogTimeRemaining + ": <div style='display:inline' id='countdownDisplay'></div></p></div>";
    	var dialogContent = '<div id="timeout-dialog" style="display:none;">' +
                '<p id="timeout-message">' + currentConfig.dialogTimeRemaining + '</p>' + 
                '<p id="timeout-question">' + currentConfig.dialogText + '</p>' +
              '</div>';
		
	  changeformstatustimer('0');  // change form flag to 0
	  	
	  $(dialogContent).dialog({
    	dialogClass: 'timeout-dialog',
    	zIndex: 10000,
    	width: 450,
        minHeight: 'auto',
    	draggable: false,
        resizable: false,
        buttons: [{
	          text: currentConfig.dialogStayLoggedInButton,
	          id: "timeout-keep-signin-btn",
	          click: function () {
				destroyWarningDialog();
	            stopDialogTimer();
	            startIdleTimer();
	            $.get(currentConfig.sessionKeepAliveUrl);
	          }
        	},
            {
            text: currentConfig.dialogLogOutNowButton,
            id: "timeout-sign-out-button",
            title: "Logout",
            click: function () {
				      $('#loader').css('z-index','9999');
        		  $('#loader').show();
        		  $(window).unbind(defaultConfig.activityEvents);
              if(isLogoutTriggered === false){
                logoutUser();
              }
            }
          }
          ],
        closeOnEscape: false,
        modal: true,
        title: currentConfig.dialogTitle,
        open: function () {
          //$(this).closest('.ui-dialog').find('.ui-dialog-titlebar-close').hide();
        }
      });
      countdownDisplay();
      document.title = currentConfig.dialogTitle;

      if (currentConfig.sessionKeepAliveTimer) {
        stopKeepSessionAlive();
      }
    };

    checkDialogTimeout = function () {
	  //var timeDialogTimeout = (store.get('idleTimerLastActivity') + (currentConfig.idleTimeLimit * 1000) + (currentConfig.dialogDisplayLimit * 1000));
    	var idleTimeLimit = getCookie('idleTimeLimit');
    	var timeDialogTimeout = (getCookie('idleTimerLastActivity') + (idleTimeLimit * 1000) + (currentConfig.dialogDisplayLimit * 1000));
    	//console.log($.now() + " > " + timeDialogTimeout);
      //if (($.now() > timeDialogTimeout) || (store.get('idleTimerLoggedOut') === true)) {
    	if (($.now() > timeDialogTimeout) || (getCookie('idleTimerLoggedOut') === 'true')) {
    	  $('#loader').css('z-index','9999');
    	  $('#loader').show();
    	  $(window).unbind(defaultConfig.activityEvents);
        if(isLogoutTriggered === false){
         logoutUser();
        }
      }
    };

    startDialogTimer = function () {
      dialogTimer = setInterval(checkDialogTimeout, (currentConfig.idleCheckHeartbeat * 1000));
    };

    stopDialogTimer = function () {
      clearInterval(dialogTimer);
      clearInterval(remainingTimer);
    };

    isDialogOpen = function () {
      var dialogOpen = $("#timeout-dialog").is(":visible");
      if (dialogOpen === true) {
        return true;
      }
      return false;
    };

    destroyWarningDialog = function () {
      $("#timeout-dialog").dialog('destroy').remove();
      document.title = origTitle;

      if (currentConfig.sessionKeepAliveTimer) {
        startKeepSessionAlive();
      }
    };

    countdownDisplay = function () {
      var dialogDisplaySeconds = currentConfig.dialogDisplayLimit, mins, secs;

      remainingTimer = setInterval(function () {
        //mins = Math.floor(dialogDisplaySeconds / 60); // minutes
        //if (mins < 10) { mins = '0' + mins; }
    	dialogDisplaySeconds--;
        secs = dialogDisplaySeconds; // seconds
        if (secs < 10) { secs = '0' + secs; }
        //$('#countdownDisplay').html(mins + ':' + secs);
        
        $('#countdownDisplay').html(secs);
        if(secs == 0) {
          //alert('in countdownDisplay : '+isLogoutTriggered);
          if(isLogoutTriggered === false){
            logoutUser(); 
          }
          return false;
        }
        //dialogDisplaySeconds -= 1;
      }, 1000);
    };

    //----------- LOGOUT USER FUNCTION --------------//
    logoutUser = function () {
      isLogoutTriggered = true;
    	$('#loader').css('z-index','9999');
    	$('#loader').show();
    	//$(window).unbind(defaultConfig.activityEvents);
    	setCookie('idleTimerLoggedOut', true);
      //store.set('idleTimerLoggedOut', true);

      if (currentConfig.sessionKeepAliveTimer) {
        stopKeepSessionAlive();
      }

      if (currentConfig.customCallback) {
        currentConfig.customCallback();
      }
      //console.log(currentConfig.redirectUrl);
      if (currentConfig.redirectUrl) {
    	  
        window.location.href = currentConfig.redirectUrl;
      }
      /*$.fn.idleTimeout().logout();*/
    };
    
    this.setCookie = function(cname, cvalue, exdays) {
    	setCookie(cname, cvalue, exdays);
    };
    
    setCookie = function(cname, cvalue, exdays) {
    	var d = new Date();
    	d.setTime(d.getTime() + (exdays*24*60*60*1000));
    	var expires = "expires="+ d.toUTCString();
      if (location.protocol == 'https:')
      {
        var security_attrs = "secure";
    	  document.cookie = cname + "=" + cvalue + "; " + security_attrs + "; " + expires;
      }
      else
      {
        document.cookie = cname + "=" + cvalue + "; " + expires;
      }
    };
    	
    getCookie = function(cname) {
    	var name = cname + "=";
    	var ca = document.cookie.split(';');
    	for(var i = 0; i <ca.length; i++) {
    		var c = ca[i];
    		while (c.charAt(0)==' ') {
    			c = c.substring(1);
    		}
    		if (c.indexOf(name) == 0) {
    			return c.substring(name.length,c.length);
    		}
    	}
    	return "";
    };

	//###############################
    // Build & Return the instance of the item as a plugin
    // This is your construct.
    //###############################
    return this.each(function () {

        //if (store.enabled) {
        //console.log("COOKIE1 "+getCookie('idleTimerLastActivity'));
        //console.log("COOKIE2 "+getCookie('idleTimerLoggedOut'));
        setCookie('idleTimerLastActivity', $.now()); 
        setCookie('idleTimerLoggedOut', false);
        setCookie('idleTimeLimit', currentConfig.idleTimeLimit);
        //console.log(store.get('idleTimerLastActivity'));
        //store.set('idleTimerLastActivity', $.now());
        //store.set('idleTimerLoggedOut', false);

        activityDetector();

        if (currentConfig.sessionKeepAliveTimer) {
          startKeepSessionAlive();
        }

        startIdleTimer();

      //} else {
      //alert(currentConfig.errorAlertMessage);
      //}

    });
  };
}(jQuery));
