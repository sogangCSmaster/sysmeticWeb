<?
	header ("Cache-Control : no-cache");
	header ("Cache-Control : post-check=0 pre-check=0");
	header ("Pragma:no-cache");

	$enc_retInfo =  $_REQUEST["retInfo"];
	$param = "?retInfo=$enc_retInfo";
?>

<html>
<head>
<script language="JavaScript">
function end() 
{	
	window.opener.location.href = 'http://.../pcc_V3_result_seed.php' + '<?=$param?>';
	self.close();
}
</script>

</head>
<body onload="javascript:end()">
</body>
</html>