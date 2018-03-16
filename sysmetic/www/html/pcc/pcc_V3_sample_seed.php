<?
	$id = $_REQUEST['id'];
	$srvNo = $_REQUEST['srvNo'];
	$reqNum = $_REQUEST['reqNum'];
	$certDate = $_REQUEST['certDate'];
	$certGb = $_REQUEST['certGb'];
	$addVar = $_REQUEST['addVar'];
	$retUrl = $_REQUEST['retUrl'];

	/************************************************************************************/
	/* reqNum 값은 최종 결과값 복호화를 위한 SecuKey로 활용 되므로 중요합니다.			*/
	/* reqNum 은 본인 확인 요청시 항상 새로운 값으로 중복 되지 않게 생성 해야 합니다.	*/
	/* 쿠키 또는 Session및 기타 방법을 사용해서 reqNum 값을								*/
	/* vname_result_seed.php에서 가져 올 수 있도록 해야 함.								*/
	/* 샘플을 위해서 쿠키를 사용한 것이므로 참고 하시길 바랍니다.						*/
 	/************************************************************************************/
	//01. reqNum 쿠키 생성
	setcookie("REQNUM", $reqNum, time()+600);

	$exVar       = "0000000000000000";        // 확장임시 필드입니다. 수정하지 마세요..

	//02. 암호화 파라미터 생성
	$reqInfo = $id . "^" . $srvNo . "^" . $reqNum . "^" . $certDate . "^" . $certGb . "^" . $addVar . "^" . $exVar;
	
	//03. 본인확인 요청정보 1차암호화
	$iv = "";
	//2014.02.07 KISA 권고사항
	//위 변조 및, 불법 시도 차단을 위하여 아래 패턴에 해당하는 문자열만 허용	
	if(preg_match('~[^0-9a-zA-Z+/=^]~', $reqInfo, $matches)){
		echo "입력 값 확인이 필요합니다.(req)"; exit;
	}
	$enc_reqInfo = exec("/home/sysmetic/www/app/lib/Auth/SciSecuX SEED 1 1 $reqInfo ");//암호화모듈 설치시 생성된 SciSecuX 파일이 있는 리눅스 경로를 설정해주세요.

	//04. 요청정보 위변조검증값 생성
	$hmac_str = exec("/home/sysmetic/www/app/lib/Auth/SciSecuX HMAC 1 1 $enc_reqInfo ");

	//05. 요청정보 2차암호화
	//데이터 생성 규칙 : "요청정보 1차 암호화^위변조검증값^암복화 확장 변수"
	$enc_reqInfo = $enc_reqInfo. "^" .$hmac_str. "^" ."0000000000000000";
	$enc_reqInfo = exec("/home/sysmetic/www/app/lib/Auth/SciSecuX SEED 1 1 $enc_reqInfo ");

?>
<html>
<head>
<title>본인확인서비스 서비스 Sample 화면</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<style>
<!--
   body,p,ol,ul,td
   {
	 font-family: 굴림;
	 font-size: 12px;   
   } 
   
   a:link { size:9px;color:#000000;text-decoration: none; line-height: 12px}
   a:visited { size:9px;color:#555555;text-decoration: none; line-height: 12px}
   a:hover { color:#ff9900;text-decoration: none; line-height: 12px}

   .style1 {
		color: #6b902a;
		font-weight: bold;
	}
	.style2 {
	    color: #666666
	}
	.style3 {
		color: #3b5d00;
		font-weight: bold;
	}
-->
</style>

<script language=javascript>  
<!--
    var CBA_window; 

    function openPCCWindow(){ 
        var CBA_window = window.open('', 'PCCWindow', 'width=430, height=560, resizable=1, scrollbars=no, status=0, titlebar=0, toolbar=0, left=300, top=200' );

        if(CBA_window == null){ 
			 alert(" ※ 윈도우 XP SP2 또는 인터넷 익스플로러 7 사용자일 경우에는 \n    화면 상단에 있는 팝업 차단 알림줄을 클릭하여 팝업을 허용해 주시기 바랍니다. \n\n※ MSN,야후,구글 팝업 차단 툴바가 설치된 경우 팝업허용을 해주시기 바랍니다.");
        }

        document.reqCBAForm.action = 'https://pcc.siren24.com/pcc_V3/jsp/pcc_V3_j10.jsp';
        document.reqCBAForm.target = 'PCCWindow';

		return true;
    }	

//-->
</script>

</head>

<body bgcolor="#FFFFFF" topmargin=0 leftmargin=0 marginheight=0 marginwidth=0  >

<center>
<br><br><br><br><br><br>
<span class="style1">본인확인서비스 요청화면 Sample입니다.</span><br>
<br><br>
<table cellpadding=1 cellspacing=1>    
    <tr>
        <td align=center>회원사아이디</td>
        <td align=left><? echo "$id" ?></td>
    </tr>
    <tr>
        <td align=center>서비스번호</td>
        <td align=left><? echo "$srvNo" ?></td>
    </tr>
    <tr>
        <td align=center>요청번호</td>
        <td align=left><? echo "$reqNum" ?></td>
    </tr>
	<tr>
        <td align=center>인증구분</td>
        <td align=left><? echo "$certGb" ?></td>
    </tr>
	<tr>
        <td align=center>요청시간</td>
        <td align=left><? echo "$certDate" ?></td>
    </tr>
	<tr>
        <td align=center>추가파라메터</td>
        <td align=left><? echo "$addVar" ?></td>
    </tr>    
    <tr>
        <td align=center>&nbsp</td>
        <td align=left>&nbsp</td>
    </tr>
    <tr width=100>
        <td align=center>요청정보(암호화)</td>
        <td align=left>
            <?
			  $enc_reqInfo1 = substr($enc_reqInfo,0,50);	
              echo $enc_reqInfo1; 
			?>...
        </td>
    </tr>
    <tr>
        <td align=center>결과수신URL</td>
        <td align=left><? echo "$retUrl" ?></td>
    </tr>
</table>

<!-- 본인확인서비스 요청 form --------------------------->
<form name="reqCBAForm" method="post" action = "" onsubmit="return openPCCWindow()">
    <input type="hidden" name="reqInfo"     value = "<? echo "$enc_reqInfo" ?>">
    <input type="hidden" name="retUrl"      value = "<? echo "$retUrl" ?>">
    <input type="submit" value="본인확인서비스 요청" >	
</form>
<BR>
<BR>
<!--End 본인확인서비스 요청 form ----------------------->

<br>
<br>
	<table width="450" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="130"><a href=http://www.siren24.com/v2alimi/comm/jsp/v2alimiAuth.jsp?id=SIR005&svc_seq=01 target=blank><img src="/name/images/logo01.gif" width="122" height="41" border=0></a></td>
        <td width="320"><span class="style2">본 사이트는 SCI평가정보(주)의 <span class="style3">명의도용방지서비스</span> 협약사이트 입니다. 타인의 명의를 도용하실 경우 관련법령에 따라 처벌 받으실 수 있습니다.</span></td>
      </tr>
    </table>
      <br>
      <br>
    <br>
  이 Sample화면은 본인확인서비스 요청화면 개발시 참고가 되도록 제공하고 있는 화면입니다.<br>
  <br>
</center>
</BODY>
</HTML>