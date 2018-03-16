<?
	/************************************************************************************/
	/* - 결과값 복호화를 위해 IV 값을 Random하게 생성함.(반드시 필요함!!)			    */
	/* - input박스 reqNum의 value값을  echo $CurTime.$RandNo  형태로 지정				*/
 	/************************************************************************************/
    $CurTime = date(YmdHis);  //현재 시각 구하기

	//6자리 랜덤값 생성
	$RandNo = rand(100000, 999999);
	
?>
<html>
<head>
<title>SCI평가정보 본인확인서비스  테스트</title>
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
</head>
<body onload="document.reqPCCForm.id.focus();" bgcolor="#FFFFFF" topmargin=0 leftmargin=0 marginheight=0 marginwidth=0>
        <center>
            <br><br><br>
            <span class="style1">SCI평가정보 본인확인서비스 테스트</span><br>

            <form name="reqPCCForm" method="post" action="http://sysmetic.mypro.co.kr/pcc/pcc_V3_sample_seed.php">
                <table cellpadding=1 cellspacing=1>
                    <tr>
                        <td align=center>회원사아이디</td>
                        <td align=left><input type="text" name="id" size='41' maxlength ='8' value = ""></td>
                    </tr>
                    <tr>
                        <td align=center>서비스번호</td>
                        <td align=left><input type="text" name="srvNo" size='41' maxlength ='6' value=""></td>
                    </tr>
                    <tr>
                        <td align=center>요청번호</td>
                        <td align=left><input type="text" name="reqNum" size='41' maxlength ='40' value='<? echo $CurTime.$RandNo ?>'></td>
                    </tr>
					<tr>
                        <td align=center>인증구분</td>
                        <td align=left>
                            <select name="certGb" style="width:300">                                
                                <option value="H">휴대폰</option>
                            </select>
                        </td>
                    </tr>
					<tr>
                        <td align=center>요청시간</td>
                        <td align=left><input type="text" name="certDate" size='41' maxlength ='40' value='<? echo $CurTime ?>'></td>
                    </tr>
					<tr>
                        <td align=center>추가파라미터</td>
                        <td align=left><input type="text" name="addVar"  size="41" maxlength ='320' value=""></td>
                    </tr>
                    <tr>
                        <td align=center>결과수신URL</td>
                        <td align=left><input type="text" name="retUrl" size="41" value="32http://sysmetic.mypro.co.kr/pcc/pcc_V3_popup_seed.php"></td>
                    </tr>
                </table>
                <br><br>
                <input type="submit" value="본인확인서비스 테스트">
            </form>
            <br>
            <br>
            이 Sample화면은 서울신용평가 본인확인서비스 테스트를 위해 제공하고 있는 화면입니다.<br>
            <br>
        </center>
    </body>
</html>