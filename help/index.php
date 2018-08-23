<?php
use Yii;
use yii\web\Session;

require(__DIR__ . '/../yii_init.php');
$url=str_replace("help/","",Yii::$app->homeUrl);
//echo "<pre>",print_r(Yii::$app->user->identity),"</pre>";die;
if (!isset(Yii::$app->user->identity)) {
    header('Location:'.$url);
}
		$session = new Session;
		$session->open();
		$myaccess = $session['myaccess'];
		if(!array_search(9,$session['myaccess']['feature_sort']) !== false){ echo 'User permissions do not allow entry into this module.';?>
			<a href="<?php echo $url;?>" style=" font-size: 16px;margin: 0 10px;text-decoration: none;">Back To IS-A-TASK</a>
			<?php die();
		}
		
//

?>
<!DOCTYPE html>
<html>
<head>
  <title>IS-A-TASK User Help Manual</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <script type="text/javascript" src="jquery.js"></script>
  <script type="text/javascript" src="helpman_settings.js"></script>
  <script type="text/javascript" src="helpman_navigation.js"></script>
  <script type="text/javascript" src="application_helpfile_contextids.js"></script>

  <link type="text/css" href="default.css" rel="stylesheet" />

  <style type="text/css">
    html,body { margin:0;padding:0;height:100%;overflow:hidden;
    			       background:#C1E0FF;
    }

    #hmheadbox {
      position: absolute;
      left: 5px;
      right: 5px;
      top: 5px;
      height: 60px;
      background: #FFFFFF;
      border: 1px solid #C0C0C0;
      }

    #hmheadbox p {
      font-size: 160%;
      font-weight: bold;
      margin-top: 16px;
      margin-left: 8px;
     }

    #hmnavbox {
      position: absolute;
      left: 5px;
      width: 250px;
      min-width: 50px;
      top: 70px;
      bottom: 5px;
      border: 1px solid #C0C0C0;
      }

    #hmcontentbox {
      position: absolute;
      top: 70px;
      right: 5px;
      bottom: 5px;
      left: 260px;
      border: 1px solid #C0C0C0;
      }

    iframe {
      position: absolute;left:0;top:0;width:100%;height:100%;border:none;
      }
  </style>

  <script type="text/javascript">
    $(document).ready(function(){
      if (!hmSupportsAbspos()) {
      	$(window).bind("resize", function() {
      	  hmNoAbsposResize($("#hmheadbox"), true, false);
      	  hmNoAbsposResize($("#hmnavbox"), false, true);
      	  hmNoAbsposResize($("#hmcontentbox"), true, true);
	    });
	    $(window).trigger("resize");
	  }
      hmCreateVSplitter($("#hmnavbox"),$("#hmcontentbox"));
    });
  </script>

</head>
<body>
<div id="hmheadbox">
<p>IS-A-TASK User Help Manual
<!-- ADD BY IAT -->
<div style="float:right;"><a href="<?php echo $url;?>" style=" font-size: 16px;margin: 0 10px;text-decoration: none;">Back To IS-A-TASK</a></div>
<!-- ADD BY IAT -->
</p>
</div>
<div id="hmnavbox">
  <iframe name="hmnavigation" id="hmnavigation" src="application_helpfile_content.htm" seamless="seamless" title="Navigation Pane" frameborder="0"></iframe>
</div>

<div id="hmcontentbox" >
  <script type="text/javascript">
    var defaulttopic="about_isatask.htm";
    var query = window.location.search.substring(1), hash = window.location.hash, xssTest = /javascript|:|&#58;|&#x3a;|%3a|3a;|58;|\//i;
    if (query !== "") query = (xssTest.test(query)) ? "" : query;
    if (hash !== "") hash = (xssTest.test(hash)) ? "" : hash;
    if (query != "") {
      if (typeof(hmGetContextId) != "undefined") {
        var tmpCntx = hmGetContextId(query);
        if (tmpCntx != "") {
          defaulttopic = tmpCntx;
        } else {
          defaulttopic = query + hash;
        }
      }
    }
    document.write('<iframe name="hmcontent" id="hmcontent" src="'+defaulttopic+'" seamless="seamless" title="Content Page" frameborder="0"></iframe>');
  </script>
  <noscript>
    <iframe name="hmcontent" id="hmcontent" src="about_isatask.htm" seamless="seamless" title="Content Page" frameborder="0"></iframe>
  </noscript>
</div>
</body>
</html>
