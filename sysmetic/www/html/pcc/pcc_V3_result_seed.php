<?
	/************************************************************************************/
	/* - sample 페이지에서 요청 시 쿠키에 저장한 Reqnum값을 가져와서 IV값에 셋팅   	    */
	/* - 쿠키 만료시간 경과 후 결과처리 못함										    */
 	/************************************************************************************/
	//01. 쿠키값 확인
	$iv = "";
	if (isset($_COOKIE["REQNUM"])) {
		$iv = $_COOKIE["REQNUM"]; 
		//쿠키 삭제
		setcookie("REQNUM", "", time()-600);
	} else {
?>
		<script language=javascript>
			alert("세션이 만료되었습니다.!!");			
		</script>
<?
		return;
	}

	// 파라메터로 받은 요청결과
	$enc_retInfo = $_REQUEST["retInfo"];

	//02. 요청결과 복호화
	//2014.02.07 KISA 권고사항
	//위 변조 및, 불법 시도 차단을 위하여 아래 패턴에 해당하는 문자열만 허용
	if(preg_match('~[^0-9a-zA-Z+/=^]~', $iv, $matches)||preg_match('~[^0-9a-zA-Z+/=^]~', $enc_retInfo, $matches)){
		echo "입력 값 확인이 필요합니다.(res-1)"; exit;
	}
	$dec_retInfo = exec("/home/sysmetic/www/app/lib/Auth/SciSecuX SEED 2 0 $iv $enc_retInfo ");//암호화모듈 설치시 생성된 SciSecuX 파일이 있는 리눅스 경로를 설정해주세요.

?>

    [본인확인 결과 수신 Sample-PHP] <br> <br>

	[복호화 하기전 수신값] <br><br>
	retInfo : <? echo $enc_retInfo ?> <br>


<?
	//데이터 조합 : "본인확인1차암호화값/위변조검증값/암복화확장변수"
	$totInfo = split("\\^", $dec_retInfo);

	$encPara  = $totInfo[0];		//본인확인1차암호화값
	$encMsg   = $totInfo[1];		//암호화된 통합 파라미터의 위변조검증값

	//03. 위변조검증값 생성
	//2014.02.07 KISA 권고사항
	//위 변조 및, 불법 시도 차단을 위하여 아래 패턴에 해당하는 문자열만 허용
	if(preg_match('~[^0-9a-zA-Z+/=^]~', $encPara, $matches)){
		echo "입력 값 확인이 필요합니다.(res-2)"; exit;
	}
	$hmac_str = exec("/home/sysmetic/www/app/lib/Auth/SciSecuX HMAC 1 0 $encPara ");

	if($hmac_str != $encMsg){
?>
		<script language=javascript>
			alert("비정상적인 접근입니다.!!");
		</script>
		<a href="http://.../pcc_V3_input_seed.php">[Back]</a>
<?
		exit;
	}

	//04. 본인확인1차암호화값 복호화
	//2014.02.07 KISA 권고사항
	//위 변조 및, 불법 시도 차단을 위하여 아래 패턴에 해당하는 문자열만 허용
	if(preg_match('~[^0-9a-zA-Z+/=^]~', $iv, $matches)||preg_match('~[^0-9a-zA-Z+/=^]~', $encPara, $matches)){
		echo "입력 값 확인이 필요합니다.(res-3)"; exit;
	}
	$decPara = exec("/home/sysmetic/www/app/lib/Auth/SciSecuX SEED 2 0 $iv $encPara ");

	//05. 파라미터 분리
	$split_dec_retInfo = split("\\^", $decPara);

	$name		= $split_dec_retInfo[0];		//성명
	$birYMD		= $split_dec_retInfo[1];		//생년월일
	$sex		= $split_dec_retInfo[2];		//성별
	$fgnGbn		= $split_dec_retInfo[3];		//내외국인 구분값
	$di			= $split_dec_retInfo[4];		//DI
	$ci1		= $split_dec_retInfo[5];		//CI1
	$ci2		= $split_dec_retInfo[6];		//CI2	
	$civersion	= $split_dec_retInfo[7];		//CI Version
	$reqNum		= $split_dec_retInfo[8];		//요청번호
	$result		= $split_dec_retInfo[9];		//본인확인 결과 (Y/N)
	$certGb		= $split_dec_retInfo[10];		//인증수단
	$cellNo		= $split_dec_retInfo[11];		//핸드폰 번호
	$cellCorp	= $split_dec_retInfo[12];		//이동통신사
	$certDate	= $split_dec_retInfo[13];		//검증시간
	$addVar		= $split_dec_retInfo[14];		//추가 파라메터

	//예약 필드
	$ext1		= $split_dec_retInfo[15];
	$ext2		= $split_dec_retInfo[16];
	$ext3		= $split_dec_retInfo[17];
	$ext4		= $split_dec_retInfo[18];
	$ext5		= $split_dec_retInfo[19];


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
	<body>
            [복호화 후 수신값] <br>
            <br>
            <table cellpadding="1" cellspacing="1" border="1">
				<tr>
					<td align="center" colspan="2">14세이상 및 신원보증인 결과</td>
				</tr>
				<tr>
                    <td align="left">성명</td>
                    <td align="left"><? echo $name ?></td>
                </tr>
				<tr>
                    <td align="left">성별</td>
                    <td align="left"><? echo $sex ?></td>
                </tr>
				<tr>
                    <td align="left">생년월일</td>
                    <td align="left"><? echo $birYMD ?></td>
                </tr>
				<tr>
                    <td align="left">내외국인 구분값(1:내국인, 2:외국인)</td>
                    <td align="left"><? echo $fgnGbn ?></td>
                </tr>				
				<tr>
                    <td align="left">중복가입자정보</td>
                    <td align="left"><? echo $di ?></td>
                </tr>
				<tr>
                    <td align="left">연계정보1</td>
                    <td align="left"><? echo $ci1 ?></td>
                </tr>
				<tr>
                    <td align="left">연계정보2</td>
                    <td align="left"><? echo $ci2 ?></td>
                </tr>
				<tr>
                    <td align="left">연계정보버전</td>
                    <td align="left"><? echo $civersion ?></td>
                </tr>
                <tr>
                    <td align="left">요청번호</td>
                    <td align="left"><? echo $reqNum ?></td>
                </tr>
				<tr>
                    <td align="left">인증성공여부</td>
                    <td align="left"><? echo $result ?></td>
                </tr>
				<tr>
                    <td align="left">인증수단</td>
                    <td align="left"><? echo $certGb ?></td>
                </tr>
				<tr>
                    <td align="left">핸드폰번호</td>
                    <td align="left"><? echo $cellNo ?></td>
                </tr>
				<tr>
                    <td align="left">이동통신사</td>
                    <td align="left"><? echo $cellCorp ?></td>
                </tr>
                <tr>
                    <td align="left">요청시간</td>
                    <td align="left"><? echo $certDate ?></td>
                </tr>				
				<tr>
                    <td align="left">추가파라미터</td>
                    <td align="left"><? echo $addVar ?>&nbsp;</td>
                </tr>
				
            </table>              
            <br>
            <br>
            <a href="http://.../pcc_V3_input_seed.php">[Back]</a>
</body>
</html>


---------------------------
웹 페이지 메시지
---------------------------
정상적인 서비스 요청이 아닙니다.

[에러번호] PJ100811
---------------------------
확인   
---------------------------
