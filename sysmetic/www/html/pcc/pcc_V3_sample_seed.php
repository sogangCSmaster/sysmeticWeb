<?
	$id = $_REQUEST['id'];
	$srvNo = $_REQUEST['srvNo'];
	$reqNum = $_REQUEST['reqNum'];
	$certDate = $_REQUEST['certDate'];
	$certGb = $_REQUEST['certGb'];
	$addVar = $_REQUEST['addVar'];
	$retUrl = $_REQUEST['retUrl'];

	/************************************************************************************/
	/* reqNum ���� ���� ����� ��ȣȭ�� ���� SecuKey�� Ȱ�� �ǹǷ� �߿��մϴ�.			*/
	/* reqNum �� ���� Ȯ�� ��û�� �׻� ���ο� ������ �ߺ� ���� �ʰ� ���� �ؾ� �մϴ�.	*/
	/* ��Ű �Ǵ� Session�� ��Ÿ ����� ����ؼ� reqNum ����								*/
	/* vname_result_seed.php���� ���� �� �� �ֵ��� �ؾ� ��.								*/
	/* ������ ���ؼ� ��Ű�� ����� ���̹Ƿ� ���� �Ͻñ� �ٶ��ϴ�.						*/
 	/************************************************************************************/
	//01. reqNum ��Ű ����
	setcookie("REQNUM", $reqNum, time()+600);

	$exVar       = "0000000000000000";        // Ȯ���ӽ� �ʵ��Դϴ�. �������� ������..

	//02. ��ȣȭ �Ķ���� ����
	$reqInfo = $id . "^" . $srvNo . "^" . $reqNum . "^" . $certDate . "^" . $certGb . "^" . $addVar . "^" . $exVar;
	
	//03. ����Ȯ�� ��û���� 1����ȣȭ
	$iv = "";
	//2014.02.07 KISA �ǰ����
	//�� ���� ��, �ҹ� �õ� ������ ���Ͽ� �Ʒ� ���Ͽ� �ش��ϴ� ���ڿ��� ���	
	if(preg_match('~[^0-9a-zA-Z+/=^]~', $reqInfo, $matches)){
		echo "�Է� �� Ȯ���� �ʿ��մϴ�.(req)"; exit;
	}
	$enc_reqInfo = exec("/home/sysmetic/www/app/lib/Auth/SciSecuX SEED 1 1 $reqInfo ");//��ȣȭ��� ��ġ�� ������ SciSecuX ������ �ִ� ������ ��θ� �������ּ���.

	//04. ��û���� ������������ ����
	$hmac_str = exec("/home/sysmetic/www/app/lib/Auth/SciSecuX HMAC 1 1 $enc_reqInfo ");

	//05. ��û���� 2����ȣȭ
	//������ ���� ��Ģ : "��û���� 1�� ��ȣȭ^������������^�Ϻ�ȭ Ȯ�� ����"
	$enc_reqInfo = $enc_reqInfo. "^" .$hmac_str. "^" ."0000000000000000";
	$enc_reqInfo = exec("/home/sysmetic/www/app/lib/Auth/SciSecuX SEED 1 1 $enc_reqInfo ");

?>
<html>
<head>
<title>����Ȯ�μ��� ���� Sample ȭ��</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<style>
<!--
   body,p,ol,ul,td
   {
	 font-family: ����;
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
			 alert(" �� ������ XP SP2 �Ǵ� ���ͳ� �ͽ��÷η� 7 ������� ��쿡�� \n    ȭ�� ��ܿ� �ִ� �˾� ���� �˸����� Ŭ���Ͽ� �˾��� ����� �ֽñ� �ٶ��ϴ�. \n\n�� MSN,����,���� �˾� ���� ���ٰ� ��ġ�� ��� �˾������ ���ֽñ� �ٶ��ϴ�.");
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
<span class="style1">����Ȯ�μ��� ��ûȭ�� Sample�Դϴ�.</span><br>
<br><br>
<table cellpadding=1 cellspacing=1>    
    <tr>
        <td align=center>ȸ������̵�</td>
        <td align=left><? echo "$id" ?></td>
    </tr>
    <tr>
        <td align=center>���񽺹�ȣ</td>
        <td align=left><? echo "$srvNo" ?></td>
    </tr>
    <tr>
        <td align=center>��û��ȣ</td>
        <td align=left><? echo "$reqNum" ?></td>
    </tr>
	<tr>
        <td align=center>��������</td>
        <td align=left><? echo "$certGb" ?></td>
    </tr>
	<tr>
        <td align=center>��û�ð�</td>
        <td align=left><? echo "$certDate" ?></td>
    </tr>
	<tr>
        <td align=center>�߰��Ķ����</td>
        <td align=left><? echo "$addVar" ?></td>
    </tr>    
    <tr>
        <td align=center>&nbsp</td>
        <td align=left>&nbsp</td>
    </tr>
    <tr width=100>
        <td align=center>��û����(��ȣȭ)</td>
        <td align=left>
            <?
			  $enc_reqInfo1 = substr($enc_reqInfo,0,50);	
              echo $enc_reqInfo1; 
			?>...
        </td>
    </tr>
    <tr>
        <td align=center>�������URL</td>
        <td align=left><? echo "$retUrl" ?></td>
    </tr>
</table>

<!-- ����Ȯ�μ��� ��û form --------------------------->
<form name="reqCBAForm" method="post" action = "" onsubmit="return openPCCWindow()">
    <input type="hidden" name="reqInfo"     value = "<? echo "$enc_reqInfo" ?>">
    <input type="hidden" name="retUrl"      value = "<? echo "$retUrl" ?>">
    <input type="submit" value="����Ȯ�μ��� ��û" >	
</form>
<BR>
<BR>
<!--End ����Ȯ�μ��� ��û form ----------------------->

<br>
<br>
	<table width="450" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="130"><a href=http://www.siren24.com/v2alimi/comm/jsp/v2alimiAuth.jsp?id=SIR005&svc_seq=01 target=blank><img src="/name/images/logo01.gif" width="122" height="41" border=0></a></td>
        <td width="320"><span class="style2">�� ����Ʈ�� SCI������(��)�� <span class="style3">���ǵ����������</span> �������Ʈ �Դϴ�. Ÿ���� ���Ǹ� �����Ͻ� ��� ���ù��ɿ� ���� ó�� ������ �� �ֽ��ϴ�.</span></td>
      </tr>
    </table>
      <br>
      <br>
    <br>
  �� Sampleȭ���� ����Ȯ�μ��� ��ûȭ�� ���߽� ���� �ǵ��� �����ϰ� �ִ� ȭ���Դϴ�.<br>
  <br>
</center>
</BODY>
</HTML>