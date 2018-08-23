<?php
echo "<pre>";
print_r($_POST);
echo "</pre>";

$jsonData=CJSON::encode($_POST);

echo $jsonData;
exit;
?>
<script type="text/javascript">
window.close();
window.opener.ParentWindowFunction();
</script>
<noscript></noscript>