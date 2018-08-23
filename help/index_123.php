<?php
$yii=dirname(__FILE__).'/../framework/yii.php';
$config=dirname(__FILE__).'/../protected/config/main.php';
require_once($yii);
Yii::createWebApplication($config);
$url=str_replace("help","",Yii::app()->request->getBaseUrl(true));
if(isset(Yii::app()->user)){
 if(!Yii::app()->user->getId())
	header("LOCATION:$url"); 
}
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
<p>IS-A-TASK User Help Manual</p>
<!-- ADD BY IAT -->
<div style="float:right;"><a href="<?php echo $url;?>">Back To IS-A-TASK</a></div>
<!-- ADD BY IAT -->
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
