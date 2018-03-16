<?
session_start();
//if(!$_SESSION['user']['email']){
//	header("Location:/signin");
//	exit;
//}
?>
<!-- <frameset rows="0,100%" frameborder=0>
	<frame src="" name="">
	<frame src="" name="mainFram">
</frameset> -->
<body style="padding:0; margin:0;">
<iframe name="mainFram" width=100% height=100% frameborder=0  ></iframe>

<form name="goLink" style="padding:0; margin:0;" target="mainFram">
  <input type="hidden" name="UserID" value="<?=$_SESSION['user']['email']?>" />
  <input type="hidden" name="Key" value="<?=md5(date("Ymd")."^^".$_SESSION['user']['email'])?>" />
</form>

<script type="text/javascript">
<!--
window.onload=function(){
	var f=document.goLink;  //폼 name
	f.action="http://52.78.243.147:8080/main";  //이동할 페이지
	f.method="post";  //POST방식
	f.submit();
}
//-->
</script>
</body>